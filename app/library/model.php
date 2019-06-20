<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-6-20
 * Time: 上午 11:37
 */
namespace app\library;

use ext\conf;
use ext\pdo_mysql;

/**
 * 初始化mysql
 * Class model
 * @package app\library
 */
class model extends pdo_mysql{

    public function __construct()
    {
        $this->instance = $this->config(conf::get("mysql"))->connect()->get_pdo();
    }
}