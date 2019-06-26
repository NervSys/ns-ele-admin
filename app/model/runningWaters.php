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
 * 流水模型
 * Class clients
 * @package app\model
 */
class runningWaters extends model
{
    private static $table = "running_waters";

    /**
     * 充值成功的
     * @param string $starDate
     * @param string $endDate
     * @return array
     */
    public function enter(string $starDate, string $endDate):array
    {
        $table = self::$table;
        $sql = "SELECT COUNT(id) as number,left(updated_at,10) AS date FROM $table WHERE type='充值' and status=1 and updated_at>'{$starDate}' and updated_at<'{$endDate}' GROUP BY left(updated_at,10)";
        $data = $this->query($sql);
        return $data;
    }

    /**
     * 提现成功的
     * @param string $starDate
     * @param string $endDate
     * @return array
     */
    public function outflow(string $starDate, string $endDate):array
    {
        $table = self::$table;
        $sql = "SELECT COUNT(id) as number,left(updated_at,10) AS date FROM $table WHERE type='提现' and status=1 and updated_at>'{$starDate}' and updated_at<'{$endDate}' GROUP BY left(updated_at,10)";
        $data = $this->query($sql);
        return $data;
    }
    /**
     * 获取投资数据
     * @param string $starDate
     * @param string $endDate
     * @param string $crowdfundingIds
     * @return array
     */
    public function crowdfundingIdsData(string $starDate, string $endDate ,string $crowdfundingIds):array
    {
        $table = self::$table;
        $sql = "SELECT SUM(money) as moneys,business_id as crowdfunding_id FROM $table WHERE business_id in($crowdfundingIds) and type='认筹' and status=1 and updated_at>'{$starDate}' and updated_at<'{$endDate}' GROUP BY business_id";
        $data = $this->query($sql);
        return $data;
    }
}