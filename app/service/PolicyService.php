<?php
namespace app\service;

use app\model\InsuranceCustomer;
use app\model\InsurancePolicy;
use think\facade\Db;

class PolicyService
{
    public function saveWithCustomer(array $customerData, array $policyData): InsurancePolicy
    {
        return Db::transaction(function () use ($customerData, $policyData) {
            $customer = InsuranceCustomer::updateOrCreate(
                ['id_card' => $customerData['id_card']],
                $customerData + ['customer_no' => $customerData['customer_no'] ?? 'C' . date('YmdHis') . mt_rand(100, 999)]
            );
            $policyData['customer_id'] = $customer->id;
            return InsurancePolicy::updateOrCreate(['policy_no' => $policyData['policy_no']], $policyData);
        });
    }

    public function statistics(): array
    {
        $redis = app(RedisService::class)->client();
        $key = 'policy:statistics';
        if ($cached = $redis->get($key)) {
            return json_decode($cached, true);
        }

        $data = [
            'total' => InsurancePolicy::count(),
            'pending' => InsurancePolicy::where('status', '待审核')->count(),
            'active' => InsurancePolicy::where('status', '生效')->count(),
            'surrender' => InsurancePolicy::where('status', '退保')->count(),
            'claim' => InsurancePolicy::where('status', '理赔')->count(),
        ];
        $redis->setex($key, 120, json_encode($data, JSON_UNESCAPED_UNICODE));
        return $data;
    }
}

