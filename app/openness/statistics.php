<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-6-26
 * Time: 下午 15:11
 */
namespace app\openness;

use app\model\clients;
use app\model\crowdfundings;
use app\model\investmentRecords;
use app\model\runningWaters;
use core\handler\factory;
use ext\mine;

/**
 * 数据图表统计
 * Class statistics
 * @package app\user
 */
class statistics extends factory
{
    public static $tz = [
        "getData"       =>  [
            "param"         =>      "start,end",
            "pre"           =>      "auth/base-releaseToken"
        ]
    ];

    /**
     * 获取数据统计
     */
    public function getData(string $start, string $end)
    {
        if(parent::$error){
            return parent::$error;
        }
        $res = [
            'code'=>200,
            'msg'=>'成功',
            'data'=>[]
        ];

        try{
            $startString = strtotime($start);
            $endString = strtotime($end);
            $starDate =  date('Y-m-d 00:00:00',$startString);
            $endDate =  date('Y-m-d 23:59:59',$endString);

            $solsticeDateData = mine::new()->solsticeDate($endString,$startString);
            /* 获取用户数量 */
            $getTotal = clients::new()->getTotal();

            $crowdfundingsModel = crowdfundings::new();
            /* 获取项目数量 */
            $crowdfundingsNumber = $crowdfundingsModel->getTotal();
            /* 获取认筹金额 */
            $investmentRecordsModel = investmentRecords::new();
            $winvestmentRecordsMoney = $investmentRecordsModel->getRecruitMoney();
            /* 认筹数量 */
            $winvestmentRecordsNumber = $investmentRecordsModel->getTotal();

            /* 进行图表数据统计 */
            /* 充值 */
            $runningWatersModel = runningWaters::new();
            $runningWatersEnterData = $runningWatersModel->enter($starDate,$endDate);
            $res['data']["runningWatersEnterData"] = mine::new()->chart($solsticeDateData,$runningWatersEnterData,"date","number");
            /* 提现 */
            $runningWatersOutflowData = $runningWatersModel->outflow($starDate,$endDate);
            $res['data']["runningWatersOutflowData"] = mine::new()->chart($solsticeDateData,$runningWatersOutflowData,"date","number");

            /* 获取项目种类 */
            $crowdfundingsType = $crowdfundingsModel->getDataType();

            $res['data']["crowdfundingsType"] = array_column($crowdfundingsType,"name");

            /* 获取项目的投资额度 */
            $crowdfundingIds = array_column($crowdfundingsType,"id");
            $crowdfundingIdsData = $runningWatersModel->crowdfundingIdsData($starDate,$endDate,implode(",",$crowdfundingIds));

            /* 投资额度 */
            $res['data']["crowdfundingsMoneys"] = [];
            $ratio = [];
            foreach ($crowdfundingsType as $key=>$value){
                $ratio[$key]["name"] = $value['name'];
                if (empty($crowdfundingIdsData) ){
                    $res['data']["crowdfundingsMoneys"][$key] = 0;
                    $ratio[$key]["value"] = 0;
                }else{
                    foreach ($crowdfundingIdsData as $k=>$v){

                        if($value['id'] == $v['crowdfunding_id']){
                            $res['data']["crowdfundingsMoneys"][$key] = $v['moneys']/100;
                            $ratio[$key]["value"] = $v['moneys']/100;
                            break;
                        }else{
                            $res['data']["crowdfundingsMoneys"][$key] = 0;
                            $ratio[$key]["value"] = 0;
                        }
                    }
                }

            }
            $res['data']["crowdfundingsRatio"] = $ratio;

            /* 日期 */

            $res['data']["datetime"] = $solsticeDateData;

            $res["data"]["getTotal"] = $getTotal; //获取用户数量
            $res["data"]["crowdfundingsNumber"] = $crowdfundingsNumber; //获取项目数量
            $res["data"]["winvestmentRecordsMoney"] = $winvestmentRecordsMoney/100; //获取认筹金额
            $res["data"]["winvestmentRecordsNumber"] = $winvestmentRecordsNumber; //认筹数量

        }catch (\Exception $e){
            $res['code'] = $e->getCode();
            $res['msg'] = $e->getMessage();
        }

        return $res;
    }

}