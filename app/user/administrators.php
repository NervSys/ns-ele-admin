<?php
namespace app\user;

use app\model\adminRole;
use app\model\adminUsers;
use app\model\logs;
use core\handler\factory;
use ext\mine;
use ext\upload;

/**
 * 用户模块
 * Class user
 * @package word
 */
class administrators extends factory
{
    public static $tz = [
        'roles'=>[
            'param'         =>      '',
            "pre"           =>      "auth/base-releaseToken"
        ],
        'administrators'=>[
            'param'         =>      '',
            "pre"           =>      "auth/base-releaseToken"
        ],
        'delete'=>[
            'param'         =>      'id',
            "pre"           =>      "auth/base-releaseToken"
        ],
        'add'=>[
            'param'         =>      'city,company,name,pwd,email,logo,phone,province,role_id',
            "pre"           =>      "auth/base-releaseToken"
        ],
        'update'=>[
            'param'         =>      'id,pwd,role_id,logo,province,city,phone,email',
            "pre"           =>      "auth/base-releaseToken"
        ],
        'logo'=>[
            'param'         =>      '',
        ],
    ];

    /**
     * 获取角色列表
     * @return array
     */
    function administrators():array
    {
        if(parent::$error){
            return parent::$error;
        }
        $res = [
            'code'=>200,
            'msg'=>'成功',
            'data'=>[]
        ];
        try{
            /* 获取所有的管理员 */
            $administratorsData = adminUsers::new()->getDataAll();

            /* 如果没有管理员 */
            if (empty($administratorsData)) mine::new()->abortMy(203);
            $administratorsData = mine::new()->delTwoArrayKey($administratorsData,[
                "updated_at",
                "deleted_at",
                "age",
                "birthday",
                "first_role_id",
                "country",
                "area",
                "address",
                "ip",
                "code",
                "token",
                "status",
                "company"
            ]);
            /* 将用户的权限id从string 转 arra有 */
            foreach ($administratorsData as $key=>$value){
                $arr = explode(",",$value['role_id']);
                $administratorsData[$key]['role_id'] = mine::new()->stringToIntArray($arr);
                $administratorsData[$key]['created_at'] = substr($value["created_at"],0,13);
                $administratorsData[$key]['pwd'] = "******";

            }
            $res['data'] = $administratorsData;

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
    function roles():array
    {
        if(parent::$error){
            return parent::$error;
        }
        $res = [
            'code'=>200,
            'msg'=>'成功',
            'data'=>[]
        ];
        try{

            $where = ["status",1];
            $adminRoles = adminRole::new()->getData($where);

            $res['data'] = $adminRoles;

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
    public function add(string $city,string $name,string $pwd,string $email,string $logo,string $phone,string $province,array $role_id):array
    {
        if(parent::$error){
            return parent::$error;
        }
        $res = [
            'code'=>200,
            'msg'=>'成功',
            'data'=>[]
        ];
        try{

            if(empty($name) || empty($pwd) || empty($province) || empty($city) || empty($phone) || empty($role_id)) mine::new()->abortMy(40005);

            if(strlen($name) < 4 || strlen($name) > 20) mine::new()->abortMy(40032);
            if(preg_match('/[\x{4e00}-\x{9fa5}]/u', $name)>0) mine::new()->abortMy(40037);
            /*查看当前帐号是否已经存在*/
            $isNot = adminUsers::new()->isNot($name);
            if(!empty($isNot)) mine::new()->abortMy(40033);

            if(strlen($pwd) < 6 || strlen($pwd) > 20) mine::new()->abortMy(40008);
            if(mine::new()->isMobile($phone) == false) mine::new()->abortMy(40022);
            if(mine::new()->isEmail($email) === false) mine::new()->abortMy(40006);

            if(!strpos($logo,"ttp:")) $logo = "https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif";
            $data = adminUsers::new()->createUser(
                $name,
                $pwd,
                mine::new()->pwd_key_home(),
                "",
                $logo,
                $province,
                $city,
                implode(",",$role_id),
                $phone,
                $email
            );
            if($data === false){
                logs::new()->myLog("添加用户失败",'administrators');
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
    public function update(int $id,string $pwd,string $logo,string $province,string $city,string $phone,string $email,array $role_id)
    {
        if(parent::$error){
            return parent::$error;
        }
        $res = [
            'code'=>200,
            'msg'=>'成功',
            'data'=>[]
        ];
        try{
            if(empty($id)|| empty($pwd) || empty($logo) || empty($province) || empty($city) || empty($phone)) mine::new()->abortMy(40005);


            /*查看当前帐号是否已经存在*/
            $getIdFirstUser = adminUsers::new()->getIdFirstUser($id);
            if(empty($getIdFirstUser)) mine::new()->abortMy(40003);


            if(strlen($pwd) < 6 || strlen($pwd) > 20) mine::new()->abortMy(40008);
            if(!strpos($logo,"ttp:")) $logo = "https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif";
            if(mine::new()->isMobile($phone) == false) mine::new()->abortMy(40022);

            if(mine::new()->isEmail($email) === false) mine::new()->abortMy(40006);
            $data = adminUsers::new()->saveUser(
                $getIdFirstUser['id'],
                $pwd,
                $getIdFirstUser['code'],
                implode(",",$role_id),
                $logo,
                $province,
                $city,
                $phone,
                $email
            );
            if($data === false){
                logs::new()->myLog("修改失败",'administrators');
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
        if(parent::$error){
            return parent::$error;
        }
        $res = [
            'code'=>200,
            'msg'=>'成功',
            'data'=>[]
        ];
        try{
            $bool = adminUsers::new()->delUser($id);
            if ($bool === false) mine::new()->abortMy(404);

        }catch (\Exception $e){
            $res['code'] = $e->getCode();
            $res['msg'] = $e->getMessage();
        }

        return $res;
    }

    /**
     * 上传logo
     * @return array
     */
    public function logo():array
    {
        if(parent::$error){
            return parent::$error;
        }
        $res = [
            'code'=>200,
            'msg'=>'成功',
            'data'=>[],
        ];
        try {
            if(parent::$data['file']['size']> 4*1024*1024){
                mine::new()->abortMy(40019);
            }
            $data = upload::new()->fetch('file')->save();

            if($data['errno'] != 0){
                mine::new()->abortMy(40023);
            }
            $res['logo'] = self::$domain['domain'].'/upload/'.$data['name'];
            $res['files']["file"] = self::$domain['domain'].'/upload/'.$data['name'];
        }catch (\Exception $e){
            $res['code'] = $e->getCode();
            $res['msg'] = $e->getMessage();

        }
        return $res;
    }

}