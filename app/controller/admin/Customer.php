<?php
namespace app\controller\admin;

use app\model\InsuranceCustomer;
use app\utils\ExcelUtil;

class Customer
{
    public function index()
    {
        $page = input('get.page/d', 1);
        $limit = input('get.limit/d', 10);
        $keyword = trim(input('get.keyword/s', ''));
        $query = InsuranceCustomer::order('id', 'desc');
        if ($keyword !== '') {
            $query->whereLike('name|mobile|customer_no', '%' . $keyword . '%');
        }
        $count = $query->count();
        $list = $query->page($page, $limit)->select()->toArray();
        $list = array_map('mask_customer_row', $list);
        return json_success($list, 'success', $count);
    }

    public function save()
    {
        $data = input('post.');
        $data['customer_no'] = $data['customer_no'] ?? 'C' . date('YmdHis') . mt_rand(100, 999);
        InsuranceCustomer::updateOrCreate(['id' => $data['id'] ?? 0], $data);
        write_operate_log('客户管理', '新增/修改客户');
        return json_success([], '客户保存成功');
    }

    public function delete()
    {
        InsuranceCustomer::destroy((int) input('post.id/d', 0));
        write_operate_log('客户管理', '删除客户');
        return json_success([], '客户删除成功');
    }

    public function import()
    {
        $file = request()->file('file');
        if (!$file) {
            return json_error('请上传Excel文件');
        }
        $rows = ExcelUtil::read($file->getPathname());
        foreach ($rows as $row) {
            InsuranceCustomer::updateOrCreate(['id_card' => $row['id_card']], $row);
        }
        write_operate_log('客户管理', 'Excel导入客户');
        return json_success(['rows' => count($rows)], '导入成功');
    }
}

