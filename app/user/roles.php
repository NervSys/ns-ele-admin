<?php
namespace app\user;

use app\model\adminPermission;
use app\model\adminRole;
use core\handler\factory;
use ext\mine;
/**
 * 用户模块
 * Class user
 * @package word
 */
class roles extends factory
{
    public static $tz = [
        'roles'=>[
            'param'         =>      '',
            "pre"           =>      "auth/base-releaseToken"
        ],
        'routes'=>[
            'param'         =>      '',
            "pre"           =>      "auth/base-releaseToken"
        ],
        'delete'=>[
            'param'         =>      'id',
            "pre"           =>      "auth/base-releaseToken"
        ],
        'add'=>[
            'param'         =>      'info,name,routes,sulg',
            "pre"           =>      "auth/base-releaseToken"
        ],
        'update'=>[
            'param'         =>      'id',
            "pre"           =>      "auth/base-releaseToken"
        ],
    ];

    /**
     * 获取角色列表
     * @return array
     */
    function roles():array
    {

        $res = [
            'code'=>200,
            'msg'=>'成功',
            'data'=>[]
        ];
        try{
            /* 设置wehre条件 */
            $where = ["status",1];    // 所有上架的角色
            /* 查询角色 */
            $adminRoleData = adminRole::new()->getData($where);

            foreach ($adminRoleData as $k=>$v){
                /* 获取当前用户的权限 */
                $roleScore = $v['score'];

                /* 如果没有角色，异常处理 */
                if (empty($adminRoleData)) mine::new()->abortMy(203);

                /* 系统权限 */
                $adminPermissionData = adminPermission::new()->getData($where);
                /* 如果没有权限，异常处理 */
                if (empty($adminPermissionData)) mine::new()->abortMy(203);

                foreach ($adminPermissionData as $key=>$value){
                    $permissionSulg = self::$Permission[$value['model_name'].'_'.$value['control_real_name']];

                    if($roleScore & $permissionSulg->authValue){
                        $adminPermissionData[$key]['have'] = true;
                    }else{
                        $adminPermissionData[$key]['have'] = false;
                        unset($adminPermissionData[$key]);
                    }
                }
                /* 将权限数据压入角色属性 */
                $adminRoleData[$k]["routes"] = array_column($adminPermissionData,"id");
                /* 删除权限值 */
                unset($v['score']);
            }

            $res['data'] = $adminRoleData;

        }catch (\Exception $e){
            $res['code'] = $e->getCode();
            $res['msg'] = $e->getMessage();
        }

        return $res;
    }

    /**
     * 获取权限列表
     * @return array
     */
    function routes():array
    {

        $res = [
            'code'=>200,
            'msg'=>'成功',
            'data'=>[]
        ];
        try{
            /* 设置wehre条件 */
            $where = ["status",1];    // 所有上架的权限
            /* 查询权限 */
            $adminPermissionData = adminPermission::new()->getData($where);

            /* 如果没有权限 */
            if (empty($adminPermissionData)) mine::new()->abortMy(203);
            foreach ($adminPermissionData as $key=>$value){
                $adminPermissionData[$key]['value'] = $value['id'];
            }
            $res['data'] = $adminPermissionData;

        }catch (\Exception $e){
            $res['code'] = $e->getCode();
            $res['msg'] = $e->getMessage();
        }

        return $res;
    }

    /**
     * 添加角色
     * @param string $info
     * @param string $name
     * @param string $sulg
     * @param array $routes
     * @return array
     */
    public function add(string $info,string $name,string $sulg,array $routes):array
    {
        $res = [
            'code'=>200,
            'msg'=>'成功',
            'data'=>[]
        ];
        try{
            if (empty($info) || empty($name) || empty($sulg) || empty($routes)) mine::new()->abortMy(40005);
            if (preg_match ("/^[a-z]+$/i", $sulg)) {

            }else{
                mine::new()->abortMy(40038);
            }
            if (strlen($sulg) < 4 || strlen($sulg) > 20 ){
                mine::new()->abortMy(40038);
            }

            if (adminRole::new()->hasName($sulg) === true){
                mine::new()->abortMy(40033);
            }

            /* 计算权限值 */
            $adminPermissionData = adminPermission::new()->getDataIds($routes);
            $score = 0;
            foreach ($adminPermissionData as $key=>$value){
                $permissionSulg = self::$Permission[$value['model_name'].'_'.$value['control_real_name']];
                $score += $permissionSulg->authValue;
            }
            $addData = [
                "created_at"=>date("Y-m-d H:i:s",time()),
                "updated_at"=>date("Y-m-d H:i:s",time()),
                "name"=>$name,
                "info"=>$info,
                "score"=>$score,
                "sulg"=>$sulg,
                "status"=>1,
            ];
            $addDataBool = adminRole::new()->addData($addData);
            if ($addDataBool === false){
                mine::new()->abortMy(404);
            }

        }catch (\Exception $e){
            $res['code'] = $e->getCode();
            $res['msg'] = $e->getMessage();
        }

        return $res;
    }

    /**
     * 修改
     * @param int $id
     * @param string $info
     * @param string $name
     * @param array $routes
     * @throws \Exception
     */
    public function update(int $id,string $info,string $name,array $routes)
    {
        $res = [
            'code'=>200,
            'msg'=>'成功',
            'data'=>[]
        ];
        try{
            if (empty($info) || empty($name) || empty($routes)) mine::new()->abortMy(40005);

            /* 计算权限值 */
            $adminPermissionData = adminPermission::new()->getDataIds($routes);
            $score = 0;
            foreach ($adminPermissionData as $key=>$value){
                $permissionSulg = self::$Permission[$value['model_name'].'_'.$value['control_real_name']];
                $score += $permissionSulg->authValue;
            }
            $saveData = [
                "updated_at"=>date("Y-m-d H:i:s",time()),
                "name"=>$name,
                "info"=>$info,
                "score"=>$score,
                "status"=>1,
            ];
            $addDataBool = adminRole::new()->saveData($id,$saveData);
            if ($addDataBool === false){
                mine::new()->abortMy(404);
            }
        }catch (\Exception $e){
            $res['code'] = $e->getCode();
            $res['msg'] = $e->getMessage();
        }

        return $res;
    }
    /**
     * 删除
     * @param int $id
     * @return array
     */
    public function delete(int $id)
    {
        $res = [
            'code'=>200,
            'msg'=>'成功',
            'data'=>[]
        ];
        try{
            $bool = adminRole::new()->del($id);
            if ($bool === false) mine::new()->abortMy(404);

        }catch (\Exception $e){
            $res['code'] = $e->getCode();
            $res['msg'] = $e->getMessage();
        }

        return $res;
    }

}