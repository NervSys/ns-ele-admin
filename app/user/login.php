<?php
namespace app\user;

use app\auth\base;
use app\model\adminUsers;
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
            'param' => 'username,password'
        ],
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
                        $res['data']['token'] = base::new()->generateToken($loginUser['id']);

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
}