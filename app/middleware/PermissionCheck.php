<?php
namespace app\middleware;

use app\service\RbacService;

class PermissionCheck
{
    public function handle($request, \Closure $next)
    {
        $user = $request->adminUser ?? session('admin_user');
        if (!$user) {
            return json_error('登录已失效，请重新登录', 401);
        }

        if (!app(RbacService::class)->canAccess((int) $user['role_id'], $request->pathinfo())) {
            return json_error('当前账号无权限访问该接口', 403);
        }

        return $next($request);
    }
}

