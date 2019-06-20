<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-6-20
 * Time: 上午 11:37
 */
namespace app\library;

use ext\redis_cache;

/**
 * 缓存
 * Class model
 * @package app\library
 */
class cache extends redis_cache {

    public function __construct()
    {
        $this->instance = $this->config($this->get('redis'))->connect()->get_redis();
    }
}