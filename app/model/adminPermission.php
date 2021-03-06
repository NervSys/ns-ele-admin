<?php
namespace app\model;

use app\library\model;

/**
 * 权限表
 * Class adminUsers
 * @package model
 */
class adminPermission extends model
{
    /*定义当前模型的数据表名称*/
    private static $table = 'admin_permissions';

    /**
     * 获取权限
     * @return array
     */
    public function getData(array $where):array
    {
        $data = $this->select(self::$table)
            ->where($where)
            ->field("id,created_at,name,model_name,control_real_name")
            ->fetch();
        if(empty($data)) logs::new()->myLog("获取权限失败",'permission');
        return $data;
    }

    /**
     * 根据id 获取权限的信息
     * @param array $ids
     * @return array
     */
    public function getDataIds(array $ids ):array
    {
        $data = $this->select(self::$table)
            ->where([['id','in',$ids]])
            ->fetch();

        return $data;
    }
}
