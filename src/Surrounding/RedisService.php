<?php

namespace Primeskills\ApiCommon\Surrounding;

use Illuminate\Support\Facades\Redis;

class RedisService
{
    /**
     * @param string $key
     * @param string $value
     * @return void
     */
    public static function set(string $key, string $value)
    {
        Redis::set($key, $value);
    }

    /**
     * @param string $key
     * @return mixed|string
     */
    public static function get(string $key)
    {
        return Redis::get($key);
    }

    /**
     * @param string $key
     * @return mixed|string
     */
    public static function del(string $key)
    {
        return Redis::del($key);
    }

    /**
     * @return mixed
     */
    public static function flushDB()
    {
        return Redis::flushDB();
    }

    public static function setEx(string $key, string $value, int $exp)
    {
        self::set($key, $value);
        Redis::expire($key, $exp);
    }
}
