<?php
return [
    \think\middleware\SessionInit::class,
    \think\middleware\FormTokenCheck::class,
    \app\middleware\SecurityFilter::class,
];
