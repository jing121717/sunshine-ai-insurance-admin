<?php
namespace app\controller\admin;

use app\model\AiChatLog;
use app\service\QwenAiService;

class Ai
{
    public function ask(QwenAiService $service)
    {
        $user = session('admin_user');
        $question = trim(input('post.question/s', ''));
        if ($question === '') {
            return json_error('请输入咨询问题');
        }

        $data = $service->ask(
            (int) $user['id'],
            $question,
            (int) input('post.customer_id/d', 0),
            (int) input('post.policy_id/d', 0)
        );
        write_operate_log('AI客服', 'AI智能咨询');
        return json_success($data, 'AI回答成功');
    }

    public function logs()
    {
        $page = input('get.page/d', 1);
        $limit = input('get.limit/d', 10);
        $query = AiChatLog::order('id', 'desc');
        $count = $query->count();
        $list = $query->page($page, $limit)->select()->toArray();
        return json_success($list, 'success', $count);
    }
}

