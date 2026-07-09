<?php
namespace app\middleware;

use app\service\RedisService;

class RedisSlidingRateLimit
{
    public function handle($request, \Closure $next)
    {
        $config = config('ai_config.rate_limit');
        $user = session('admin_user') ?: [];
        $key = 'rate:ai:' . ($user['id'] ?? $request->ip());
        $now = microtime(true);
        $redis = app(RedisService::class)->client();

        $redis->zremrangebyscore($key, 0, $now - $config['window_seconds']);
        $count = $redis->zcard($key);
        if ($count >= $config['max_requests']) {
            return json_error('提问太频繁了，请稍后再试。为保证AI服务稳定，系统已进行限流保护。', 429);
        }

        $redis->zadd($key, [$now => (string) $now]);
        $redis->expire($key, $config['window_seconds'] + 5);
        return $next($request);
    }
}

