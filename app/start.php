<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-6-20
 * Time: 下午 14:32
 */
namespace app;

use db\install;
use ext\conf;

class start
{
    /**
     * Add to system INIT section
     *
     * start constructor.
     */
    public function __construct()
    {
        conf::load('/', 'mysql');
        conf::load('/', 'redis');
        install::new();
    }
}