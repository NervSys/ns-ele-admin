<?php
namespace app\user;

use app\auth\base;
use app\model\adminRole;
use app\model\adminUsers;
use app\redis\redisCache;
use core\handler\factory;
use ext\mine;

/**
 * 用户模块
 * Class user
 * @package word
 */
class login extends factory
{
    public static $tz = [
        'login'=>[
            'param'         =>      'username,password'
        ],
        "getUserInfo"=>[
            "param"         =>      "",
            "pre"           =>      "auth/base-releaseToken"
        ],
        "logout"    =>[
            "param"         =>      "",
            "pre"           =>      "auth/base-releaseToken"
        ]
    ];

    /**
     * 登陆
     * @param string $name  用户名
     * @param string $pwd   密码
     * @return array
     */
    function login(string $username,string $password):array
    {

        $res = [
            'code'=>200,
            'msg'=>'成功',
            'data'=>[]
        ];
        try{

            if(empty($username) || empty($password)){
                mine::new()->abortMy(40005);
            }else{
                // 进行用户登陆确认
                $isNot = adminUsers::new()->isNot(strval($username));

                if(empty($isNot)){ // 用户名错误
                    mine::new()->abortMy(40003);
                }else{
                    // 进入登陆
                    $loginUser = adminUsers::new()->getUser(strval($username),strval($password),strval($isNot));

                    if(is_array($loginUser) && $loginUser){
                        if($loginUser['status'] != 1){
                            mine::new()->abortMy(40029);
                        }

                        $res['data']['token'] = base::new()->generateToken($loginUser['id'],strval($loginUser["token"]));

                    }else{
                        mine::new()->abortMy(40009);
                    }

                }
            }

        }catch (\Exception $e){
            $res['code'] = $e->getCode();
            $res['msg'] = $e->getMessage();
        }

        return $res;
    }


    /**
     * 获取用户信息
     * @param string $token
     * @param string $nonce
     * @param string $timestamp
     * @param string $signature
     */
    public function getUserInfo():array
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
            $loginUser = self::$newUserInfo;
            /*删除不能暴露的参数*/

            $loginUser['created_at'] = substr($loginUser['created_at'],0,10);
            /*获取用户的角色信息*/
            $role = adminRole::new()->getFirst($loginUser['role_id']);
            if(empty($role)){
                mine::new()->abortMy(40030);
            }

            $loginUser2['roles'] = [$role['sulg']];
            $loginUser2['avatar'] = $loginUser['logo'];
            $loginUser2['introduction'] = $loginUser['name'];
            $loginUser2['name'] = $loginUser['name'];

            unset($loginUser);
            $res['data'] = $loginUser2;

        }catch (\Exception $e){
            $res['code'] = $e->getCode();
            $res['msg'] = $e->getMessage();

        }
        return $res;
    }

    /**
     * 清除token
     * @return array
     */
    public function logout():array
    {
        if(parent::$error){
            return parent::$error;
        }
        $res = [
            'code'=>200,
            'msg'=>'成功',
            'data'=>[],
        ];
        try{

            /* 删除redis中，存储的token */
            redisCache::new()->delString(self::$newUserInfo['token']);
            /* 删除用户属性保存的token */
            adminUsers::new()->saveToken(self::$newUserInfo['id'],"");

        }catch (\Exception $e){
            $res['code'] = $e->getCode();
            $res['msg'] = $e->getMessage();
        }
        return $res;
    }

}