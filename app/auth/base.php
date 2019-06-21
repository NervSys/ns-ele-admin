<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-5-9
 * Time: 下午 14:54
 */
namespace app\auth;

use app\library\cache;
use app\model\adminPermission;
use app\model\adminUsers;
use app\model\logs;
use core\handler\factory;
use ext\auth;
use ext\crypt;
use ext\errno;

class base extends factory
{
    /*初始化项目token加密*/
    private $key = 'admin_onegarden_key';
    private $nonce = 'admin_onegarden';
    private $outtime = 30000;
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
    public function generateToken(int $id,$currentappversion = '1.1'):string
    {
        $token = crypt::new()->get_key().'==='.$id.'==='.$currentappversion.'==='.time().'==='.$this->key;

        $access_token = crypt::new()->encrypt($token,$this->key);

        cache::new()->set($access_token,[$id],$this->loginTimeOut);

        return $access_token;
    }

    /**
     * 解密token并且获取用户数据
     * @param string $token
     * @return array
     */
    public function releaseToken(string $token,string $nonce,string $timestamp,string $signature)
    {

        $releaseToken = crypt::new()->decrypt($token,$this->key);
        $userId = explode('===',$releaseToken);

        /*验证登陆是否超时*/
        $loginTime = $userId[3] + $this->loginTimeOut;

        if(time() > $loginTime){
            errno::set(40030,40030);
        }

        /*验证请求是否超时*/
        if((time() - $timestamp) > $this->outtime){
            errno::set(40031,40031);
        }
        /* 加密字符是否违法*/
        if($nonce != $this->nonce){
            errno::set(40001,40001);
        }

        $sign = $token.$nonce.$timestamp;

        $sign = sha1($sign);
        if($sign != $signature){
            logs::new()->myLog('签名错误'.'sgin:'.$sign.'------------------------'.'get_sgin:'.$signature);
            errno::set(40001,40001);
        }

        $redis_ceche_token = cache::new()->get($token);

        if(!$redis_ceche_token){
            errno::set(40030,40030);
        }else{
            /*获取用户信息*/

            $userData = adminUsers::new()->getIdFirstUser(intval($redis_ceche_token[0]));

            if(is_array($userData) && $userData){
                self::$newUserInfo = $userData;
                /*初始化权限*/
                self::instantiationPermission();
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
        $adminPermissionData = adminPermission::new()->getData();
        if(!empty($adminPermissionData)){
            foreach ($adminPermissionData as $key=>$value){
                /*将权限添载入系统*/
                self::$Permission[$value['model_name'].'_'.$value['control_real_name']] = new auth($value['model_name'].'_'.$value['control_real_name'],$value['control_name']);
            }
        }
    }
}