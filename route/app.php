<?php
use think\facade\Route;

Route::group('admin', function () {
    Route::get('csrf', 'admin.Auth/csrf');
    Route::post('login', 'admin.Auth/login');
    Route::post('logout', 'admin.Auth/logout');

    Route::group(function () {
        Route::get('customer/list', 'admin.Customer/index');
        Route::post('customer/save', 'admin.Customer/save');
        Route::post('customer/delete', 'admin.Customer/delete');
        Route::post('customer/import', 'admin.Customer/import');

        Route::get('policy/list', 'admin.Policy/index');
        Route::post('policy/save', 'admin.Policy/save');
        Route::post('policy/delete', 'admin.Policy/delete');
        Route::get('policy/export', 'admin.Policy/export');
        Route::get('policy/statistics', 'admin.Policy/statistics');

        Route::post('ai/ask', 'admin.Ai/ask')->middleware(\app\middleware\RedisSlidingRateLimit::class);
        Route::get('ai/logs', 'admin.Ai/logs');

        Route::get('rbac/roles', 'admin.Rbac/roles');
        Route::post('rbac/saveRole', 'admin.Rbac/saveRole');
        Route::get('rbac/menus', 'admin.Rbac/menus');
        Route::post('rbac/bindPermission', 'admin.Rbac/bindPermission');
    })->middleware(\app\middleware\AuthCheck::class)
      ->middleware(\app\middleware\PermissionCheck::class);
});
