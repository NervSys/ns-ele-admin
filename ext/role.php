<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-5-15
 * Time: 下午 12:28
 */
namespace ext;

use core\handler\factory;

/**
 * 角色
 * Class role
 * @package ext
 */
class role extends factory
{
    /**
     * 角色名
     * @var string
     */
    protected $roleName;

    /**
     * 角色拥有的权限值
     * @var int
     */
    protected $roleValue;

    /**
     * 父角色对象
     * @var Peak_Role
     */
    protected $parentRole;

    /**
     * 构造函数
     * @param string  $roleName   角色名
     * @param Peak_Role|null $parentRole 父角色对象
     */
    function __construct($roleName,role $parentRole = null)
    {
        $this->roleName = $roleName;
        $this->authValue = 0;
        if($parentRole){
            $this->parentRole = $parentRole;
            $this->authValue = $parentRole->getAuthValue();
        }
    }

    /**
     * 获取父角色的权限
     */
    protected function fetchParentAuthValue(){
        if($this->parentRole){
            $this->authValue |= $this->parentRole->getAuthValue();
        }
    }

    /**
     * 给予某种权限
     * @param  Peak_Auth $auth $auth
     * @return 以便链式操作
     */
    public function allow(auth $auth){
        $this->fetchParentAuthValue();
        $this->authValue |= $auth->getAuthValue();
        return $this;
    }


    /**
     * 阻止某种权限
     *
     * @param Peak_Auth $auth
     * @return Peak_Role 以便链式操作
     */
    public function deny(auth $auth) {
        $this->fetchParentAuthValue();
        $this->authValue &= ~$auth->getAuthValue();
        return $this;
    }

    /**
     * 检测是否拥有某种权限
     *
     * @param Peak_Auth $auth
     * @return boolean
     */
    public function checkAuth(auth $auth) {
        return $this->authValue & $auth->getAuthValue();
    }

    /**
     * 获取角色的权限值
     *
     * @return int
     */
    public function getAuthValue() {
        return $this->authValue;
    }

    /**
     * 直接设置权限值
     * @param int $authValue
     * @return int
     */
    public function setAuthValue(int $authValue){
        $this->authValue = $authValue;
        return $this->authValue;
    }


}