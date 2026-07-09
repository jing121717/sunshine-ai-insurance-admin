<?php
namespace app\utils;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use think\Response;

class ExcelUtil
{
    public static function read(string $path): array
    {
        $sheet = IOFactory::load($path)->getActiveSheet();
        $rows = $sheet->toArray();
        array_shift($rows);
        $data = [];
        foreach ($rows as $row) {
            if (empty($row[0])) {
                continue;
            }
            $data[] = [
                'customer_no' => $row[0] ?: 'C' . date('YmdHis') . mt_rand(100, 999),
                'name' => $row[1] ?? '',
                'gender' => (int) ($row[2] ?? 0),
                'id_card' => $row[3] ?? '',
                'mobile' => $row[4] ?? '',
                'email' => $row[5] ?? '',
                'city' => $row[6] ?? '',
                'address' => $row[7] ?? '',
                'risk_level' => $row[8] ?? 'normal',
                'remark' => $row[9] ?? '',
            ];
        }
        return $data;
    }

    public static function exportPolicy(array $list): Response
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $headers = ['保单号', '客户姓名', '手机号', '产品类型', '产品名称', '保费', '保额', '状态', '生效日', '到期日'];
        $sheet->fromArray($headers, null, 'A1');
        $rowNo = 2;
        foreach ($list as $item) {
            $customer = $item['customer'] ?? [];
            $sheet->fromArray([
                $item['policy_no'],
                $customer['name'] ?? '',
                isset($customer['mobile']) ? mask_mobile($customer['mobile']) : '',
                $item['product_type'],
                $item['product_name'],
                $item['premium_amount'],
                $item['insured_amount'],
                $item['status'],
                $item['effective_date'],
                $item['expire_date'],
            ], null, 'A' . $rowNo++);
        }

        $file = runtime_path() . 'policy_export_' . date('YmdHis') . '.xlsx';
        (new Xlsx($spreadsheet))->save($file);
        return download($file, '阳光保险保单数据.xlsx');
    }
}

