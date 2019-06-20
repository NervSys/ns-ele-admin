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
    private static $table = 'admin_role';

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

}
