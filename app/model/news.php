<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-6-26
 * Time: 下午 15:15
 */
namespace app\model;

use app\library\model;
use core\parser\data;

/**
 * 新闻模型
 * Class clients
 * @package app\model
 */
class news extends model
{
    private static $table = "news";

    /**
     * 查找数据
     * @param array $where 条件
     * @param array $sort  排序
     * @param int $page 页码
     * @param int $number 数量
     * @return array
     */
    public function getData(array $where, int $page,int $number,array $sort = ["id"=>"desc"]):array
    {
        $data = $this->select(self::$table)
            ->where($where)
            ->order($sort)
            ->limit($page,$number)
            ->fetch();
        return $data;
    }

    /**
     * 获取总量
     * @return int
     */
    public function getTotal():int
    {
        $data = $this->select(self::$table)
            ->where([["id",">",0]])
            ->fetch();
        return count($data);
    }

    /**
     * 详情
     * @param int $id
     * @return array
     */
    public function detail(int $id):array
    {
        $data = $this->select(self::$table)
            ->where(["id",$id])
            ->fetch();
        return $data;
    }

    /**
     * 修改
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateData(int $id,array $data):bool
    {
        $data = $this->update(self::$table)
            ->where(["id",$id])
            ->value($data)
            ->execute();
        return $data;
    }

    /**
     * 创建
     * @param array $data
     * @return bool
     */
    public function addData(array $data):bool
    {
        $data = $this->insert(self::$table)
            ->value($data)
            ->execute();
        return $data;
    }
}