<?php


namespace App\Service;
use Illuminate\Support\Facades\Redis;

class MutexService
{

    const LOCK_SUCCESS = 'OK';
    const IF_NOT_EXIST = 'NX';
    const MILLISECONDS_EXPIRE_TIME = 'PX';

    const RELEASE_SUCCESS = 1;

    /**
     * 尝试获取锁
     * @param \Predis\Client $redis redis客户端
     * @param String $key 锁
     * @param String $requestId 请求id
     * @param int $expireTime 过期时间
     * @return bool                     是否获取成功
     */
    public static function tryGetLock(string $key, string $requestId, int $expireTime)
    {
        $result = Redis::set($key, $requestId, self::MILLISECONDS_EXPIRE_TIME, $expireTime, self::IF_NOT_EXIST);

        return self::LOCK_SUCCESS === (string)$result;
    }

    public static function releaseLock(string $key, string $requestId)
    {
        $lua = "if redis.call('get', KEYS[1]) == ARGV[1] then return redis.call('del', KEYS[1]) else return 0 end";

        $result = Redis::eval($lua, 1, $key, $requestId);
        return self::RELEASE_SUCCESS === $result;
    }

}