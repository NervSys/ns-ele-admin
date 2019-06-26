<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-6-26
 * Time: 下午 15:15
 */
namespace app\model;

use app\library\model;

/**
 * 认筹模型
 * Class clients
 * @package app\model
 */
class investmentRecords extends model
{
    private static $table = "investment_records";

    /**
     * 获取平台认筹金额
     * @return int
     */
    public function getRecruitMoney():int
    {
        $data = $this->select(self::$table)
            ->where([["type","in",["Cash","RealThing"]]])
            ->fetch();
        /* 如果还没有认筹记录，返回0 */
        if (empty($data)) return 0;
        /* 对数据进行二维到一维转换，取出money */
        $moneys = array_column($data,"money");
        /* 进行金额计算并且返回 */
        return array_sum($moneys);
    }

    /**
     * 获取总共的认筹份数
     * @return int
     */
    public function getTotal():int
    {
        $data = $this->select(self::$table)
            ->where([["type","in",["Cash","RealThing"]]])
            ->fetch();
        /* 如果还没有认筹记录，返回0 */
        if (empty($data)) return 0;
        /* 对数据进行二维到一维转换，取出number */
        $moneys = array_column($data,"number");
        /* 进行number计算并且返回 */
        return array_sum($moneys);
    }


}