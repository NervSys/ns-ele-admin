<?php
namespace app\model;

use app\library\model;

/**
 * Class adminRole
 * @package model
 */
class adminRole extends model
{
    /*定义当前模型的数据表名称*/
    private static $table = 'admin_roles';

    /**
     * 根据角色id 获取角色信息
     * @param int $role_id
     * @return array
     */
    public function getFirst(int $role_id):array
    {
        $data = $this->select(self::$table)
            ->where(['id',$role_id])
            ->where(['status',1])
            ->fetch();

        return $data ? $data[0] : [];
    }

    /**
     * 修改权限值
     * @param int $id
     * @param int $score
     * @return bool
     */
    public function setAuthValue(int $id,int $score):bool
    {
        $data = $this->update(self::$table)
            ->where(['id',$id])
            ->value([
                "score"=>$score
            ])
            ->execute();
        return $data;
    }

    /**
     * 添加
     * @param array $data
     * @return bool
     */
    public function addData(array $data)
    {
        $bool = $this->insert(self::$table)
            ->value($data)
            ->execute();
        return $bool;
    }

    /**
     * 修改
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function saveData(int $id,array $data)
    {
        $bool = $this->update(self::$table)
            ->where(["id",$id])
            ->value($data)
            ->execute();
        return $bool;
    }

    /**
     * 当前角色标识是否存在
     * @param string $sulg
     * @return bool
     */
    public function hasName(string $sulg):bool
    {
        $data = $this->select(self::$table)
            ->where(["sulg",$sulg])
            ->fetch();
        return $data ? true : false;
    }

    /**
     * 获取权限列表
     * @param array $where
     * @param array $order
     * @return array
     */
    public function getData(array $where,array $order = ["id"=>"desc"]):array
    {
        $data = $this->select(self::$table)
            ->where($where)
            ->order($order)
            ->field("id,created_at,name,info,sulg,score")
            ->fetch();
        return $data;
    }

    /**
     * 删除
     * @param int $id
     * @return bool
     */
    public function del(int $id):bool
    {
        $data = $this->update(self::$table)
            ->where(["id",$id])
            ->value([
                "status"=>0,
                "deleted_at"=>date("Y-m-d H:i:s",time())
            ])
            ->execute();
        return $data;
    }

}
