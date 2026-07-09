<?php
namespace app\controller\admin;

use app\model\AdminMenu;
use app\model\AdminRole;
use app\model\AdminRolePermission;
use think\facade\Db;

class Rbac
{
    public function roles()
    {
        $list = AdminRole::order('id', 'asc')->select()->toArray();
        return json_success($list, 'success', count($list));
    }

    public function saveRole()
    {
        $data = input('post.');
        AdminRole::updateOrCreate(['id' => $data['id'] ?? 0], $data);
        write_operate_log('RBAC权限', '新增/修改角色');
        return json_success([], '角色保存成功');
    }

    public function menus()
    {
        $list = AdminMenu::order('sort', 'asc')->select()->toArray();
        return json_success($list, 'success', count($list));
    }

    public function bindPermission()
    {
        $roleId = (int) input('post.role_id/d', 0);
        $menuIds = input('post.menu_ids/a', []);
        Db::transaction(function () use ($roleId, $menuIds) {
            AdminRolePermission::where('role_id', $roleId)->delete();
            foreach ($menuIds as $menuId) {
                AdminRolePermission::create(['role_id' => $roleId, 'menu_id' => (int) $menuId]);
            }
        });
        write_operate_log('RBAC权限', '绑定角色菜单权限');
        return json_success([], '权限绑定成功');
    }
}

