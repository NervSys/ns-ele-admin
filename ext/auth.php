<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-5-15
 * Time: 下午 12:14
 */

namespace ext;

use core\handler\factory;

/**
 * 通过位移来控制权限
 * Class auth
 * @package ext
 */
class auth extends factory
{
    /**
     * 权限计数器
     * 作用在于生成权限值
     * @var integer
     */
    protected static $authCount = 0;
    /**
     * 权限名称
     * @var string
     */
    protected $authName;
    /**
     * 权限详细信息
     * @var string
     */
    protected $authMessage;
    /**
     * 权限值
     * @var int 2的n次方
     */
    protected $authValue;

    /**
     * 构造函数
     * @param string $authName    权限名称
     * @param string $authMessage 权限详细信息
     */
    public function __construct($authName,$authMessage = ''){
        $this->authName = $authName;
        $this->authMessage = $authMessage;
        $this->authValue = 1 << self::$authCount;
        self::$authCount++;
    }

    /**
     * 本类不允许对象复制的操作
     *
     */
    public function __clone(){

    }

    /**
     * 设置权限的详细信息
     * @param  string $authMessage
     */
    public function setAuthMessage($authMessage){
        $this->authMessage = $authMessage;
    }

    /**
     * 获取权名称
     * @return int
     */
    public function getAuthName(){
        return $this->authName;
    }

    /**
     * 获取权限值
     * @return int
     */
    public function getAuthValue(){
        return $this->authValue;
    }

    /**
     * 获取权限的详细信息
     * @return string
     */
    public function getAuthMessage(){
        return $this->authMessage;
    }

}