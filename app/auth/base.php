<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-5-9
 * Time: 下午 14:54
 */
namespace app\auth;

use app\model\adminPermission;
use app\model\adminUsers;
use app\model\logs;
use app\redis\redisCache;
use core\handler\factory;
use ext\auth;
use ext\crypt;
use ext\errno;


/**
 * auth 基类函数 用户确认用户登陆
 * Class base
 * @package app\auth
 */
class base extends factory
{
    /*初始化项目token加密*/
    private $key = 'admin_onegarden_key';
    /* 登录超时 */
    private $loginTimeOut = 24*3600*3;

    public static $tz = [];

    /**
     * 用于客户端生成token
     * @param $user_name
     * @param string $currentappversion
     * @return string
     * @throws \RedisException
     */
    public function generateToken(int $id,string $tokenOid,$currentappversion = '1.1'):string
    {
        $token = crypt::new()->get_key().'==='.$id.'==='.$currentappversion.'==='.time().'==='.$this->key;
        $access_token = crypt::new()->encrypt($token,$this->key);
        /* 设置token */
        $setStringBool = redisCache::new()->setString($access_token,$id,$this->loginTimeOut);
        if($setStringBool === true){
            /* 当token设置从成功，在用户元素上保存当此用户的登陆token 。并且删除上次登陆的token 避免造成redis token过多，与拥有多个登陆token同时存在*/
            $saveTokenBool = adminUsers::new()->saveToken($id,$access_token);
            if ($saveTokenBool === true) {
                /* 删除token */
                redisCache::new()->delString($tokenOid);
            }else{
                logs::new()->myLog('设置token失败：'.$access_token,"token");
            }
        }

        return $access_token;
    }

    /**
     * 解密token并且获取用户数据
     * @param string $token
     * @return array
     */
    public function releaseToken()
    {
        $token = $_SERVER['HTTP_X_TOKEN'];
        if (empty($token)) {
            errno::set(40002,40002);
        }
        $releaseToken = crypt::new()->decrypt($token,$this->key);
        $userId = explode('===',$releaseToken);

        /*验证登陆是否超时*/
        $loginTime = $userId[3] + $this->loginTimeOut;

        if(time() > $loginTime){
            errno::set(40013,40013);
        }

        /* 加密字符是否违法*/
        if($userId[4] != $this->key){
            errno::set(40001,40001);
        }

        $redis_ceche_token = redisCache::new()->getString($token);

        if(!$redis_ceche_token){
            errno::set(40030,40030);
        }else{
            /*获取用户信息*/

            $userData = adminUsers::new()->getIdFirstUser(intval($redis_ceche_token));

            if(is_array($userData) && $userData){
                self::$newUserInfo = $userData;
                /*初始化权限*/
                if (empty(self::$Permission)) self::instantiationPermission();
            }else{
                logs::new()->myLog('授权时未发现用户'.$token);
                errno::set(40002,40002);
            }
        }
    }

    /**
     * 用户登录之后实例化权限
     */
    public static function instantiationPermission()
    {
        /*获取数据库权限记录*/
        $adminPermissionData = adminPermission::new()->getData(['status',1]);
        if(!empty($adminPermissionData)){
            foreach ($adminPermissionData as $key=>$value){
                /*将权限添载入系统*/
                self::$Permission[$value['model_name'].'_'.$value['control_real_name']] = new auth($value['model_name'].'_'.$value['control_real_name'],$value['name']);
            }

        }
    }


}