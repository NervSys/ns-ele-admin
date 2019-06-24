<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-6-20
 * Time: 上午 11:37
 */
namespace app\library;

use ext\conf;
use ext\redis_cache;

/**
 * 缓存
 * Class model
 * @package app\library
 */
class cacheRedis extends redis_cache {

    public function __construct()
    {
        $this->instance = $this->config(conf::get("redis"))->connect()->get_redis();
    }
}