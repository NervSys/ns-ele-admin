<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-6-24
 * Time: 上午 11:16
 */
namespace app\redis;

use app\library\cacheRedis;
use app\model\logs;

/**
 * redis 缓存处理
 * Class cache
 * @package app\cache
 */
class redisCache extends cacheRedis
{
    /**
     * 获取redis 缓存，本身父级redis返回array 这里转换为string
     * @param string $key redis设置的key
     * @return string
     */
    public  function getString(string $key): string
    {
        $string = $this->get($key);
        return $string[0] ?? "";
    }

    /**
     * 设置redis
     * @param string $key
     * @param $value redisCache 值
     */
    public function setString($key,$value, int $life):bool
    {
        $values = is_array($value) ? $value : [$value];
        $bool = $this->set($key,$values,$life);
        if ($bool === false){
            logs::new()->myLog("设置redis失败","setRedis");
        }
        return $bool;
    }

    /**
     * 删除token
     * @param string $key
     */
    public function delString(string $key)
    {
        $this->del($key);
    }


}