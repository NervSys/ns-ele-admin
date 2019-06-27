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
use app\model\news AS newsModel;
use ext\mine;

/**
 * 新闻类
 * Class news
 * @package app\news
 */
class news extends factory
{
    public static $tz = [
        "list"       =>  [
            "param"         =>      "page,limit,sort,title",
            "pre"           =>      "auth/base-releaseToken"
        ],
        "detail"     =>  [
            "param"         =>      "id",
            "pre"           =>      "auth/base-releaseToken"
        ],
        "user"       =>  [
            "param"         =>      "",
            "pre"           =>      "auth/base-releaseToken"
        ],
        "update"       =>  [
            "param"         =>      "desc,logo,name,status,text",
            "pre"           =>      "auth/base-releaseToken"
        ],
        "create"       =>  [
            "param"         =>      "desc,logo,name,status,text",
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
     * 详情
     * @param int $id
     * @return array
     */
    public function detail(int $id):array
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
            }else{
                /* 去除不必要的key */
                mine::new()->delTwoArrayKey($detail,["updated_at","deleted_at","new_type_id"]);
                $detail[0]["logo"] = self::$domain["domain"].$detail[0]["logo"];
                $res["data"] = $detail[0];
            }

        } catch (\Exception $e) {
            $res['code'] = $e->getCode();
            $res['msg'] = $e->getMessage();
        }

        return $res;
        
    }

    /**
     * 获取作者发布人
     * @return array
     */
    public function user():array
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
            $author = [
                ["name"=>"益亩园日报"],
                ["name"=>"小尚"],
                ["name"=>"AplusMum"],
                ["name"=>"Kimi"],
            ];
            $res["data"]["items"] = $author;

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
    public function update(string $desc,string $logo,string $name,int $status,string $text,bool $is_hot):array
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

            $id = parent::$data["id"]??0;
            $operator = parent::$data["operator"]??'';
            /* 模型 */
            $newsModel = newsModel::new();

            if (empty($operator)) $operator = "益亩园日报";
            /* 修改数据 */
            $updateData = [
                "desc"=>$desc,
                "logo"=>$logo,
                "name"=>$name,
                "operator"=>$operator,
                "status"=>$status,
                "text"=>$text,
                "is_hot"=>$is_hot?1:0,
            ];

            if($id === 0){
                $updateData["updated_at"] = date('Y-m-d H:i:s',time());
                $updateData["created_at"] = date('Y-m-d H:i:s',time());

                $updateData["logo"] = str_replace(self::$domain["domain"],"",$logo);

                $bool = $newsModel->addData($updateData);
            }else{

                $detail = $newsModel->detail($id);
                if (empty($detail)){
                    mine::new()->abortMy(404);
                }
                /* 删除不必修改的数据 */
                if($detail[0]["desc"] == $desc) unset($updateData["desc"]);
                if($detail[0]["logo"] == $logo){
                    unset($updateData["logo"]);
                }else{
                    $updateData["logo"] = str_replace(self::$domain["domain"],"",$logo);
                }
                if($detail[0]["name"] == $name) unset($updateData["name"]);
                if($detail[0]["operator"] == $operator) unset($updateData["operator"]);
                if($detail[0]["status"] == $status) unset($updateData["status"]);
                if($detail[0]["text"] == $text) unset($updateData["text"]);

                $updateData["updated_at"] = date('Y-m-d H:i:s',time());
                $bool = $newsModel->updateData($id,$updateData);

            }

            if ($bool === false){
                logs::new()->myLog("修改资讯失败: ".$id,"news");
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
    public function create(string $desc,string $logo,string $name,int $status,string $text,bool $is_hot):array
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
            $id = parent::$data["id"]??0;
            $operator = parent::$data["operator"]??"";
            /* 模型 */
            $newsModel = newsModel::new();

            if (empty($operator)) $operator = "益亩园日报";
            /* 修改数据 */
            $updateData = [
                "desc"=>$desc,
                "logo"=>$logo,
                "name"=>$name,
                "operator"=>$operator,
                "status"=>$status,
                "text"=>$text,
                "is_hot"=>$is_hot?1:0,
            ];

            if($id === 0){
                $updateData["logo"] = str_replace(self::$domain["domain"],"",$logo);
                $updateData["updated_at"] = date('Y-m-d H:i:s',time());
                $updateData["created_at"] = date('Y-m-d H:i:s',time());
                $bool = $newsModel->addData($updateData);
            }else{

                $detail = $newsModel->detail($id);
                if (empty($detail)){
                    mine::new()->abortMy(404);
                }
                /* 删除不必修改的数据 */
                if($detail[0]["desc"] == $desc) unset($updateData["desc"]);
                if($detail[0]["logo"] == $logo){
                    unset($updateData["logo"]);
                }else{
                    $updateData["logo"] = str_replace(self::$domain["domain"],"",$logo);
                }
                if($detail[0]["name"] == $name) unset($updateData["name"]);
                if($detail[0]["operator"] == $operator) unset($updateData["operator"]);
                if($detail[0]["status"] == $status) unset($updateData["status"]);
                if($detail[0]["text"] == $text) unset($updateData["text"]);

                $updateData["updated_at"] = date('Y-m-d H:i:s',time());
                $bool = $newsModel->updateData($id,$updateData);

            }

            if ($bool === false){
                logs::new()->myLog("修改资讯失败: ".$id,"news");
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