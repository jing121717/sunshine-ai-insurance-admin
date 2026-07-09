<?php
namespace app\service;

use app\model\AdminMenu;
use app\model\AdminRolePermission;

class RbacService
{
    public function canAccess(int $roleId, string $path): bool
    {
        if ($roleId === 1) {
            return true;
        }

        $path = '/' . ltrim($path, '/');
        $menuIds = AdminRolePermission::where('role_id', $roleId)->column('menu_id');
        if (!$menuIds) {
            return false;
        }

        return AdminMenu::whereIn('id', $menuIds)
            ->where('path', $path)
            ->where('status', 1)
            ->count() > 0;
    }

    public function roleMenus(int $roleId): array
    {
        return AdminRolePermission::where('role_id', $roleId)->column('menu_id');
    }
}

