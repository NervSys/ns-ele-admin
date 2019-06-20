<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-6-20
 * Time: 下午 14:27
 */
namespace db;
use ext\file;
use app\library\model;
class install extends model
{
    const SQL_PATH = __DIR__ . '/pending';
    public function __construct()
    {
        parent::__construct();
        $files = file::get_list(self::SQL_PATH, '*.sql', true);
        foreach ($files as $file) {
            $ret  = $this->exec(file_get_contents($file));
            $name = basename($file);
            echo -1 !== $ret
                ? '"' . $name . '" import succeed!'
                : '"' . $name . '" import FAILED!!!';
            echo PHP_EOL;
        }
    }
}