<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-6-27
 * Time: 下午 12:22
 */
namespace app\openness;

use app\model\logs;
use core\handler\factory;
use app\model\videos AS newsModel;
use ext\mine;

/**
 * 新闻类
 * Class news
 * @package app\news
 */
class videos extends factory
{
    public static $tz = [
        "list"       =>  [
            "param"         =>      "page,limit,sort,title",
            "pre"           =>      "auth/base-releaseToken"
        ],
        "update"       =>  [
            "param"         =>      "id,name,desc,logo,link,star,number",
            "pre"           =>      "auth/base-releaseToken"
        ],
        "create"       =>  [
            "param"         =>      "name,desc,logo,link,star,number",
            "pre"           =>      "auth/base-releaseToken"
        ],
        "status"       =>  [
            "param"         =>      "status,id",
            "pre"           =>      "auth/base-releaseToken"
        ]
    ];
//    public static $logoUrl = "http://onegarden.onemugarden.com";
//    public static $domain = "http://admin.shoppingcard.com";
    /**
     * 获取列表数据
     * @param int $page
     * @param int $number
     * @param string $sort
     * @return array
     */
    public function list(int $page,int $limit,string $sort = "desc"):array
    {
        if (parent::$error) {
            return parent::$error;
        }
        $res = [
            'code' => 200,
            'msg' => '成功',
            'data' => []
        ];
        try {
            /* 设置查询条件 */
            $where = [];
            $where[] = ["status","!=",2];
            /* 计算偏移量 */
            $page = ($page-1) * $limit;
            /* 设置排序 */
            $sort = ["id"=>$sort];
            if (parent::$data['title']){
                $title = parent::$data["title"];
                $where[] = ["name","like","%$title%"];
            }
            /* 模型 */
            $newsModel = newsModel::new();
            /* 获取列表数据 */
            $data = $newsModel->getData($where,$page,$limit,$sort);
            /* 去除不必要的key */
            mine::new()->delTwoArrayKey($data,["updated_at","deleted_at","new_type_id"]);
            foreach ($data as $key=>$value){
                $data[$key]["logo"] = self::$domain["domain"].$value["logo"];
                $data[$key]["created_at"] = substr($value["created_at"],0,16);
            }
            $res["data"]["items"] = $data;
            /* 获取总条数 */
            $total = $newsModel->getTotal();
            $res["data"]["total"] = $total;

        } catch (\Exception $e) {
            $res['code'] = $e->getCode();
            $res['msg'] = $e->getMessage();
        }
        return $res;
    }


    /**
     * 放入草稿
     * @param string $desc
     * @param string $logo
     * @param string $name
     * @param int $status
     * @param string $text
     * @param int $is_hot
     * @return array
     */
    public function create(string $name,string $desc,string $logo,string $link,int $star,int $number):array
    {
        if (parent::$error) {
            return parent::$error;
        }
        $res = [
            'code' => 200,
            'msg' => '成功',
            'data' => []
        ];
        try {

            /* 模型 */
            $newsModel = newsModel::new();

            /* 修改数据 */
            $updateData = [
                "name"=>$name,
                "desc"=>$desc,
                "link"=>$link,
                "star"=>$star,
                "number"=>$number,
            ];
            if(!strpos($link,"ttp:")) mine::new()->abortMy(40040);
            $updateData["updated_at"] = date('Y-m-d H:i:s',time());
            $updateData["created_at"] = date('Y-m-d H:i:s',time());

            $updateData["logo"] = str_replace(self::$domain["domain"],"",$logo);

            $bool = $newsModel->addData($updateData);


            if ($bool === false){
                logs::new()->myLog("添加视频失败: ","videos");
                mine::new()->abortMy(404);
            }

        } catch (\Exception $e) {
            $res['code'] = $e->getCode();
            $res['msg'] = $e->getMessage();
        }

        return $res;
        
    }

    /**
     * 发布
     * @param string $desc
     * @param string $logo
     * @param string $name
     * @param int $status
     * @param string $text
     * @param int $is_hot
     * @return array
     */
    public function update(int $id,string $name,string $desc,string $logo,string $link,int $star,int $number):array
    {
        if (parent::$error) {
            return parent::$error;
        }
        $res = [
            'code' => 200,
            'msg' => '成功',
            'data' => []
        ];
        try {
            /* 模型 */
            $newsModel = newsModel::new();
            $detail = $newsModel->detail($id);
            if (empty($detail)){
                mine::new()->abortMy(404);

            }      /* 修改数据 */
            $updateData = [
                "name"=>$name,
                "desc"=>$desc,
                "link"=>$link,
                "star"=>$star,
                "number"=>$number,
            ];

            if(!strpos($link,"ttp:")) mine::new()->abortMy(40040);
            /* 删除不必修改的数据 */
            if($detail[0]["logo"] == $logo){
                unset($updateData["logo"]);
            }else{
                $updateData["logo"] = str_replace(self::$domain["domain"],"",$logo);
            }
            if($detail[0]["name"] == $name) unset($updateData["name"]);
            if($detail[0]["desc"] == $desc) unset($updateData["desc"]);
            if($detail[0]["link"] == $link) unset($updateData["link"]);
            if($detail[0]["star"] == $star) unset($updateData["star"]);
            if($detail[0]["number"] == $number) unset($updateData["number"]);


            $updateData["updated_at"] = date('Y-m-d H:i:s',time());
            $bool = $newsModel->updateData($id,$updateData);


            if ($bool === false){
                logs::new()->myLog("修改视频失败: ".$id,"videos");
                mine::new()->abortMy(404);
            }

        } catch (\Exception $e) {
            $res['code'] = $e->getCode();
            $res['msg'] = $e->getMessage();
        }

        return $res;

    }

    public function status(int $id,int $status):array
    {
        if (parent::$error) {
            return parent::$error;
        }
        $res = [
            'code' => 200,
            'msg' => '成功',
            'data' => []
        ];
        try {
            /* 模型 */
            $newsModel = newsModel::new();
            $bool = $newsModel->updateData(
                $id,
                ["status"=>$status,"updated_at" => date('Y-m-d H:i:s',time())]
            );

            if ($bool === false){
                mine::new()->abortMy(404);
            }

        } catch (\Exception $e) {
            $res['code'] = $e->getCode();
            $res['msg'] = $e->getMessage();
        }

        return $res;
        
    }
}