<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-6-21
 * Time: 下午 15:45
 */
namespace app\model;

use app\library\model;
class logs extends model
{
    /*定义当前模型的数据表名称*/
    private static $table = 'logs';
    /**
     * 项目log函数
     * @param $text
     * @param string $type
     */
    public function myLog(string $text,$type='')
    {
        $this->connect()
            ->insert(self::$table)
            ->value([
                'text'=>$text,
                'postion'=>$type,
                'created_at'=>date('Y-m-d H:i:s',time())
            ])->execute();
    }
}