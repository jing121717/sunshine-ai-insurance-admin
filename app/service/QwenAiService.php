<?php
namespace app\service;

use app\model\AiChatLog;
use app\model\InsurancePolicy;
use think\facade\Log;

class QwenAiService
{
    public function ask(int $userId, string $question, int $customerId = 0, int $policyId = 0): array
    {
        $config = config('ai_config.dashscope');
        $words = config('ai_config.sensitive_words');
        foreach ($words as $word) {
            if (mb_stripos($question, $word) !== false) {
                $this->saveLog($userId, $customerId, $policyId, $question, '问题包含敏感内容，已拦截。', 0, '敏感词拦截');
                return ['blocked' => true, 'answer' => '您的问题包含敏感内容，请调整后再咨询。'];
            }
        }

        $cacheKey = 'ai:answer:' . md5($userId . '|' . $customerId . '|' . $policyId . '|' . $question);
        $redis = app(RedisService::class)->client();
        if ($cached = $redis->get($cacheKey)) {
            return json_decode($cached, true);
        }

        $messages = $this->buildMessages($question, $customerId, $policyId);
        $payload = [
            'model' => $config['model'],
            'messages' => $messages,
            'temperature' => 0.3,
        ];

        $lastError = '';
        for ($i = 0; $i <= (int) $config['retry']; $i++) {
            try {
                $result = $this->httpPost($config['endpoint'], $config['api_key'], $payload, (int) $config['timeout']);
                $answer = $result['choices'][0]['message']['content'] ?? '暂未生成回答，请稍后再试。';
                $usage = $result['usage'] ?? [];
                $data = [
                    'blocked' => false,
                    'answer' => $answer,
                    'usage' => [
                        'prompt_tokens' => (int) ($usage['prompt_tokens'] ?? 0),
                        'completion_tokens' => (int) ($usage['completion_tokens'] ?? 0),
                        'total_tokens' => (int) ($usage['total_tokens'] ?? 0),
                    ],
                ];
                $this->saveLog($userId, $customerId, $policyId, $question, $answer, 1, '', $data['usage'], $config['model']);
                $redis->setex($cacheKey, 300, json_encode($data, JSON_UNESCAPED_UNICODE));
                return $data;
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
                Log::error('Qwen request failed: ' . $lastError);
                usleep(200000);
            }
        }

        $this->saveLog($userId, $customerId, $policyId, $question, 'AI服务暂时不可用', 0, $lastError);
        return ['blocked' => false, 'answer' => 'AI客服繁忙，请稍后再试。'];
    }

    private function buildMessages(string $question, int $customerId, int $policyId): array
    {
        $context = '你是阳光保险后台AI客服助手，回答必须专业、简洁，并提示最终以正式保单条款为准。';
        if ($policyId > 0) {
            $policy = InsurancePolicy::with(['customer'])->find($policyId);
            if ($policy) {
                $customer = $policy->customer;
                $context .= sprintf(
                    ' 当前保单：保单号%s，客户%s，产品%s，类型%s，状态%s，保费%s，保额%s，保障期%s至%s。',
                    $policy->policy_no,
                    $customer->name ?? '',
                    $policy->product_name,
                    $policy->product_type,
                    $policy->status,
                    $policy->premium_amount,
                    $policy->insured_amount,
                    $policy->effective_date,
                    $policy->expire_date
                );
            }
        } elseif ($customerId > 0) {
            $context .= ' 当前咨询已关联客户ID：' . $customerId . '。';
        }

        return [
            ['role' => 'system', 'content' => $context],
            ...$this->historyMessages($customerId, $policyId),
            ['role' => 'user', 'content' => $question],
        ];
    }

    private function historyMessages(int $customerId, int $policyId): array
    {
        $query = AiChatLog::where('status', 1)->order('id', 'desc')->limit(5);
        if ($policyId > 0) {
            $query->where('policy_id', $policyId);
        } elseif ($customerId > 0) {
            $query->where('customer_id', $customerId);
        } else {
            return [];
        }

        $rows = array_reverse($query->select()->toArray());
        $messages = [];
        foreach ($rows as $row) {
            $messages[] = ['role' => 'user', 'content' => mb_substr($row['question'], 0, 500)];
            $messages[] = ['role' => 'assistant', 'content' => mb_substr($row['answer'], 0, 800)];
        }
        return $messages;
    }

    private function httpPost(string $url, string $apiKey, array $payload, int $timeout): array
    {
        if ($apiKey === '') {
            throw new \RuntimeException('DASHSCOPE_API_KEY未配置');
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_TIMEOUT => $timeout,
        ]);
        $body = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($errno || $status >= 400) {
            throw new \RuntimeException($errno ? $error : 'HTTP ' . $status . ': ' . $body);
        }

        return json_decode((string) $body, true, 512, JSON_THROW_ON_ERROR);
    }

    private function saveLog(int $userId, int $customerId, int $policyId, string $question, string $answer, int $status, string $error = '', array $usage = [], string $model = ''): void
    {
        AiChatLog::create([
            'admin_user_id' => $userId,
            'customer_id' => $customerId,
            'policy_id' => $policyId,
            'question' => $question,
            'answer' => $answer,
            'model_name' => $model ?: config('ai_config.dashscope.model'),
            'prompt_tokens' => (int) ($usage['prompt_tokens'] ?? 0),
            'completion_tokens' => (int) ($usage['completion_tokens'] ?? 0),
            'total_tokens' => (int) ($usage['total_tokens'] ?? 0),
            'request_ip' => request()->ip(),
            'status' => $status,
            'error_message' => mb_substr($error, 0, 500),
        ]);
    }
}
