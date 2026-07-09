<?php

use app\model\SystemOperateLog;
use think\facade\Request;
use think\Response;

if (!function_exists('json_success')) {
    function json_success(array $data = [], string $msg = '操作成功', int $count = 0): Response
    {
        return json(['code' => 0, 'msg' => $msg, 'count' => $count, 'data' => $data]);
    }
}

if (!function_exists('json_error')) {
    function json_error(string $msg = '操作失败', int $code = 1, array $data = []): Response
    {
        return json(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }
}

if (!function_exists('mask_mobile')) {
    function mask_mobile(string $mobile): string
    {
        return preg_replace('/(\d{3})\d{4}(\d{4})/', '$1****$2', $mobile) ?: $mobile;
    }
}

if (!function_exists('mask_id_card')) {
    function mask_id_card(string $idCard): string
    {
        $len = strlen($idCard);
        return $len >= 10 ? substr($idCard, 0, 4) . str_repeat('*', $len - 8) . substr($idCard, -4) : $idCard;
    }
}

if (!function_exists('mask_customer_row')) {
    function mask_customer_row(array $row): array
    {
        if (isset($row['mobile'])) {
            $row['mobile_masked'] = mask_mobile((string) $row['mobile']);
        }
        if (isset($row['id_card'])) {
            $row['id_card_masked'] = mask_id_card((string) $row['id_card']);
        }
        return $row;
    }
}

if (!function_exists('write_operate_log')) {
    function write_operate_log(string $module, string $action, int $status = 1): void
    {
        $user = session('admin_user') ?: [];
        SystemOperateLog::create([
            'admin_user_id' => $user['id'] ?? 0,
            'username' => $user['username'] ?? '',
            'module' => $module,
            'action' => $action,
            'request_method' => Request::method(),
            'request_url' => Request::url(),
            'request_param' => json_encode(Request::param(), JSON_UNESCAPED_UNICODE),
            'ip' => Request::ip(),
            'user_agent' => substr(Request::header('user-agent', ''), 0, 255),
            'result_status' => $status,
        ]);
    }
}

