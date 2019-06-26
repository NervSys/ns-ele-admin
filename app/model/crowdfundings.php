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
 * 项目模型
 * Class clients
 * @package app\model
 */
class crowdfundings extends model
{
    private static $table = "crowdfundings";

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

    /**
     * 项目种类
     * @return array
     */
    public function getDataType():array
    {
        $data =$this->select(self::$table)
            ->where(["status",1])
            ->fetch();

        return $data;
    }
}