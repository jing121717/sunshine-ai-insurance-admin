<?php
namespace app\controller\admin;

use app\model\InsurancePolicy;
use app\service\PolicyService;
use app\utils\ExcelUtil;

class Policy
{
    public function index()
    {
        $page = input('get.page/d', 1);
        $limit = input('get.limit/d', 10);
        $status = input('get.status/s', '');
        $query = InsurancePolicy::with(['customer'])->order('id', 'desc');
        if ($status !== '') {
            $query->where('status', $status);
        }
        $count = $query->count();
        $list = $query->page($page, $limit)->select()->toArray();
        foreach ($list as &$item) {
            if (isset($item['customer'])) {
                $item['customer'] = mask_customer_row($item['customer']);
            }
        }
        return json_success($list, 'success', $count);
    }

    public function save(PolicyService $service)
    {
        $customer = input('post.customer/a', []);
        $policy = input('post.policy/a', []);
        if (!in_array($policy['status'] ?? '待审核', ['待审核', '生效', '退保', '理赔'], true)) {
            return json_error('保单状态不合法');
        }

        $saved = $service->saveWithCustomer($customer, $policy);
        write_operate_log('保单管理', '新增/修改保单');
        return json_success(['id' => $saved->id], '保单保存成功');
    }

    public function delete()
    {
        InsurancePolicy::destroy((int) input('post.id/d', 0));
        write_operate_log('保单管理', '删除保单');
        return json_success([], '保单删除成功');
    }

    public function export()
    {
        $list = InsurancePolicy::with(['customer'])->select()->toArray();
        write_operate_log('保单管理', 'Excel导出保单');
        return ExcelUtil::exportPolicy($list);
    }

    public function statistics(PolicyService $service)
    {
        return json_success($service->statistics(), 'success');
    }
}

