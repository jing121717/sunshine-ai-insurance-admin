<?php
namespace app\middleware;

class AuthCheck
{
    public function handle($request, \Closure $next)
    {
        $user = session('admin_user');
        if (!$user || empty($user['id'])) {
            return json_error('登录已失效，请重新登录', 401);
        }
        $request->adminUser = $user;
        return $next($request);
    }
}

