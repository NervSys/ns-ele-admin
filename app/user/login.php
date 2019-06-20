<?php
namespace app\user;

use core\handler\factory;
/**
 * 用户模块
 * Class user
 * @package word
 */
class login extends factory
{
    public static $tz = [
        'login'=>[
            'param' => 'name,pwd'
        ],
    ];

    /**
     * 登陆
     * @param string $name  用户名
     * @param string $pwd   密码
     * @return array
     */
    function login(string $name,string $pwd):array
    {

        var_dump(self::$pool);exit();
    }
}