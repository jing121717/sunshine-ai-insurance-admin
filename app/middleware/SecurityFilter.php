<?php
namespace app\middleware;

class SecurityFilter
{
    public function handle($request, \Closure $next)
    {
        $request->filter([
            function ($value) {
                return is_string($value) ? htmlspecialchars(strip_tags($value), ENT_QUOTES, 'UTF-8') : $value;
            },
        ]);

        return $next($request);
    }
}

