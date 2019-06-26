<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-6-26
 * Time: 下午 15:15
 */
namespace app\model;

use app\library\model;

/**
 * 客户端
 * Class clients
 * @package app\model
 */
class clients extends model
{
    private static $table = "clients";

    /**
     * 获取用户数量
     * @return int
     */
    public function getTotal():int
    {
        $data =$this->select(self::$table)
            ->fetch();
        return count($data);
    }

}