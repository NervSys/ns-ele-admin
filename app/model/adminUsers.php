<?php
namespace app\model;

use app\library\model;
use ext\mine;


/**
 * 管理系统用户模型
 * Class adminUsers
 * @package model
 */
class adminUsers extends model
{
    /*定义当前模型的数据表名称*/
    private static $table = 'admin_users';

    /**
     * 判断这个登录用户是否存在
     * @param string $name
     * @return string  返回加密用的code
     */
    public function isNot(string $name):string
    {
        // 大写转小写
        $name = strtolower(trim($name));
        $data = $this->select(self::$table)
            ->where(['name',$name])
            ->fetch();

        return $data ? strval($data[0]['code']) : '';
    }

    /**
     * 获取用户信息
     * @param string $name  用户名
     * @param string $pwd  密码
     * @param string $code  加密编码
     * @return array
     */
    public function getUser(string $name,string $pwd,string $code):array
    {
        $name = strtolower(trim($name));
        $pwd = strtolower(trim($pwd));
        // 对密码进行编码加密
        $pwd = mine::new()->createPwdAccount($code,$pwd);
        $data = $this->select(self::$table)
            ->where(['name',$name])
            ->where(['pwd',$pwd])
            ->fetch();

        return $data ? $data[0] : [];
    }

    /**
     * 获取所有的用户
     * @return array
     */
    public function getUserData():array
    {
        $data = $this->select(self::$table)
            ->fetch();
        return $data ? $data : [];
    }
    /**
     * 根据用户id获取用户
     * @param int $id
     * @return array
     */
    public function getIdFirstUser(int $id):array
    {
        $data = $this->select(self::$table)
            ->where(['id',$id])
            ->fetch();

        return $data ? $data[0] : [];
    }

    /**
     * 新增用户
     * @param string $name
     * @param string $pwd
     * @param string $code
     * @param string $company
     * @param string $logo
     * @param string $country
     * @param string $province
     * @param string $city
     * @param int $role_id
     * @return bool
     */
    public function createUser(string $name,string $pwd,string $code,string $company,string $logo,string $province,string $city,int $role_id,string $phone,string $email):bool
    {
        $data = $this->insert(self::$table)
            ->value([
                "name"=>$name,
                "pwd"=>mine::new()->createPwdAccount($code,$pwd),
                "code"=>$code,
                "company"=>$company,
                "logo"=>$logo,
                "province"=>$province,
                "city"=>$city,
                "role_id"=>$role_id,
                "ip"=>self::get_ip(),
                "created_at"=>date('Y-m-d H:i:s',time()),
                "phone"=>$phone,
                "email"=>$email
            ])->execute();

        return $data;
    }

    /**
     * 修改
     * @param string $pwd
     * @param string $code
     * @param string $company
     * @param string $logo
     * @param string $country
     * @param string $province
     * @param string $city
     * @param string $phone
     * @param string $email
     * @param int $status
     * @return bool
     */
    public function saveUser(int $id,string $pwd,string $code,string $company,string $logo,string $province,string $city,string $phone,string $email)
    {
        $data = $this->update(self::$table)
            ->where(['id',$id])
            ->value([
                "pwd"=>mine::new()->createPwdAccount($code,$pwd),
                "company"=>$company,
                "logo"=>$logo,
                "province"=>$province,
                "city"=>$city,
                "ip"=>self::get_ip(),
                "updated_at"=>date('Y-m-d H:i:s',time()),
                "phone"=>$phone,
                "email"=>$email
            ])->execute();

        return $data;
    }

    /**
     * 修改用户登陆的token
     * @param int $id
     * @param string $token
     * @return bool
     */
    public function saveToken(int $id,string $token):bool
    {
        $bool = $this->update(self::$table)
            ->where(["id",$id])
            ->value([
                "token"=>$token
            ])->execute();
        return $bool;
    }

    /**
     * 根据用户id、页数、每页条数获取数据
     * @param array $where
     * @param int $page
     * @param int $number
     * @return array
     */
    public function getData(array $where,int $page,int $number):array
    {
        $page = ($page-1) * $number;

        $data = $this->select(self::$table)
            ->where($where)
            ->order(['id'=>'desc'])
            ->limit($page,$number)
            ->fetch();

        return $data;
    }

    /**
     * 删除
     * @param int $int
     * @return array
     */
    public function delUser(int $id):bool
    {
        $data = $this->update(self::$table)
            ->where(['id',$id])
            ->value(["status"=>0])
            ->execute();
        return $data;
    }

    /**
     * 禁用
     * @param int $int
     * @return array
     */
    public function prohibited(int $id):bool
    {
        $data = $this->update(self::$table)
            ->where(['id',$id])
            ->value(["status"=>2])
            ->execute();
        return $data;
    }

    /**
     * 启用
     * @param int $int
     * @return array
     */
    public function enable(int $id):bool
    {
        $data = $this->update(self::$table)
            ->where(['id',$id])
            ->value(["status"=>1])
            ->execute();
        return $data;
    }

}
