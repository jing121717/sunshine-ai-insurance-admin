<?php
namespace app;

use think\exception\Handle;
use think\Response;
use Throwable;

class ExceptionHandle extends Handle
{
    public function render($request, Throwable $e): Response
    {
        if ($request->isAjax() || str_starts_with($request->pathinfo(), 'admin/')) {
            return json_error('系统异常：' . $e->getMessage(), 500);
        }
        return parent::render($request, $e);
    }
}

