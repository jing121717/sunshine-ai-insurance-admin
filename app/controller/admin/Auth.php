<?php
namespace app\controller\admin;

use app\model\AdminUser;

class Auth
{
    public function csrf()
    {
        return json_success(['token' => token()]);
    }

    public function login()
    {
        $username = input('post.username/s', '');
        $password = input('post.password/s', '');
        $user = AdminUser::where('username', $username)->where('status', 1)->find();
        if (!$user || !password_verify($password, $user->password)) {
            write_operate_log('登录', '后台登录失败', 0);
            return json_error('账号或密码错误');
        }

        $sessionUser = [
            'id' => $user->id,
            'username' => $user->username,
            'real_name' => $user->real_name,
            'role_id' => $user->role_id,
        ];
        session('admin_user', $sessionUser);
        $user->save(['last_login_ip' => request()->ip(), 'last_login_time' => date('Y-m-d H:i:s')]);
        write_operate_log('登录', '后台登录成功');
        return json_success($sessionUser, '登录成功');
    }

    public function logout()
    {
        write_operate_log('登录', '退出登录');
        session('admin_user', null);
        return json_success([], '退出成功');
    }
}
