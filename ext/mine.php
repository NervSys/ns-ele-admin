<?php
namespace ext;
use app\model\logs;
use core\handler\factory;


/**
 * 项目公共函数库
 * Class mine
 * @package ext
 */
class mine extends factory
{


    /**
     * 全局返回码
     * @param $code
     * @return string
     */
    public function msgClient($code):string
    {
        $msg = [
            200  => "成功",
            203  => "暂无数据",
            404  => "系统繁忙，请稍候再试",
            40001=> "非法请求或签名验证失败",
            40002=> "token失效",
            40003=> "不存在的用户",
            40004=> "邮箱已经被使用",
            40005=> "参数为空",
            40006=> "邮箱格式错误",
            40007=> "密码不正确",
            40008=> "密码格式不正确",
            40009=> "密码匹配错误",
            40010=> "两次密码匹配错误",
            40011=> "手机号已经被使用",
            40012=> "验证码错误",
            40013=> "验证码已过期",
            40014=> "验证码超过上限",
            40015=> "请用其它方式登录与注册",
            40016=> "已获得",
            40017=> "不存在",
            40018=> "非法分值",
            40019=> "不能上传大于4M的图片",
            40020=> "后台统一下单失败",
            40021=> "图片尺寸不正确头像：180*180",
            40022=> "手机号码格式错误",
            40023=> "上传文件错误",
            40024=> "订单不存在",
            40025=> "微信未填写",
            40026=> "地址未填写",
            40027=> "公司名未填写",
            40028=> "所属行业未填写",
            40029=> "禁用用户",
            40030=> "未登录,或登录失效",
            40031=> "非法请求，超时",
            40032=> "字符过短，最少4个单位",
            40033=> "已存在单位，不允许重复操作",
            40034=> "名字不能小于两位或者大于五位",
            40035=> "该激活码不能兑换",
            40036=> "兑换失败,请联系相关客服",
            40037=> "账号只能由数字英文字符组成，不小于6个单位或大于20个单位",
        ];

        return $msg[$code];
    }

    /**
     * 错误码
     * @param $code
     * @param $msg
     * @throws \Exception
     */
    public function abortMy(int $code)
    {
        throw new \Exception(self::msgClient($code),$code);
    }


    /**
     * 获取用户密码， 16位md5
     * @param $pwd
     * @param string $key
     * @return string
     */
    public function getPayPwdKey(string $pwd,string $key = ''):string
    {
        $pwd_key = md5($pwd.$key);
        return $pwd_key;
    }


    /**
     * 将数据转换成money
     * @param $money
     * @return string
     */
    public function toMoney($money)
    {
        if(strstr($money,'.')){
            return $money;
        }
        if(intval($money) > 0){
            return number_format($money/100,2,'.',',');
        }else{
            return 'N/A';
        }

    }

    /**
     * 根据日期获取距离当今的天数
     * @param $date
     * @return int
     */
    public function getDateNumber($date)
    {
        $dateTime = strtotime($date);
        $newTime = time();

        $number = intval((($newTime - $dateTime)/3600/24));
        return $number;
    }

    /**
     * 对象转数组
     * @param unknown $obj
     * @return unknown|NULL[]
     */
    public function objToArr($obj){
        if(!is_object($obj) && !is_array($obj)) {
            return $obj;
        }
        $arr = array();
        foreach($obj as $k => $v){
            $arr[$k] = self::objToArr($v);
        }
        return $arr;
    }

    /**
     * 递归创建文件夹
     *
     * @param unknown $dir
     * @param number $mode
     * @return boolean
     */
    public function mkdirs($dir, $mode = 0755)
    {
        if (! is_dir($dir)) {
            if (! self::mkdirs(dirname($dir))) {
                return false;
            }
            if (! mkdir($dir, $mode)) {
                return false;
            }
        }
        return true;
    }



    /**
     * 验证手机号是否正确
     * @author honfei
     * @param number $mobile
     */
    public function isMobile($mobile)
    {
        if (!is_numeric($mobile)) {
            return false;
        }
        return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8,9]{1}\d{8}$|^18[\d]{9}$|^19[\d]{9}$#', $mobile) ? true : false;
    }


    /**
     * 列出昨日到今天，本周，本月
     * @return array
     */
    public function get_month_weeks_today(){
        $time = time(); // 格式化时间
        $data = array(); // 格式化时间
        $data['month_star'] = ''; //本月第一天
        $data['month_end'] = ''; //本月最后一天
        $data['today'] = date('Y-m-d 00:00:00',$time); //今天
        $data['weeks_star'] = '';//本周开始
        $data['weeks_end'] = ''; //本周结束
        $data['month_number'] = ''; //本月天数


        $j = date('t');
        $start_time = strtotime(date('Y-m-01'));  //获取本月第一天时间戳

        $array = array();
        for($i=0;$i<$j;$i++){
            $array[] = date('Y-m-d',$start_time+$i*86400); //每隔一天赋值给数组
        }


        // 查看本月有多少天
        $array_number = count($array);
        $data['month_star'] = $array[0];
        $data['month_end'] = $array[$array_number-1].' '.'23:59:59';
        $data['month_number'] = $array_number;

        //本周的第一天和最后一天
        $date = new \DateTime();
        $date->modify('this week');
        $data['weeks_star'] = $date->format('Y-m-d');
        $date->modify('this week +6 days');
        $data['weeks_end'] = $date->format('Y-m-d').' '.'23:59:59';

        return $data;
    }

    /**
     *  根据指定的数字 列出至今的日期
     * @param int $number
     * @return array
     */
    public function SolsticeNowDate(int $number = 7):array
    {
        $time = time()-(3600*24*$number); // 格式化时间

        for($i=0;$i<$number;$i++) {
            $array[] = date('Y-m-d', $time + $i * 86400); //每隔一天赋值给数组
        }
        return $array;
    }


    /**
     * pwd key生成
     */
    public function pwd_key_home($strlen = 5){
        $str = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $len = strlen($str)-1;
        $randString = '';
        for($i = 0;$i < $strlen;$i ++){
            $num = mt_rand(0, $len);
            $randString .= $str[$num];
        }
        return $randString ;
    }

    /**
     * 创建密码及加密盐
     * @param string $key
     * @param string $pwd
     * @return string
     */
    function createPwdAccount(string $key,string $pwd):string
    {
        $pwd = substr(md5($key.$pwd),8,16);
        return $pwd;
    }



    /**
     * 二维数组根据key去重
     * @param $arr
     * @param $key
     * @return array
     */
    public function array_unset_tt($arr,$key){

        //建立一个目标数组
        $res = array();
        foreach ($arr as $value) {
            //查看有没有重复项
            if(isset($res[$value[$key]])){
                //有：销毁
                unset($value[$key]);
            }
            else{

                $res[$value[$key]] = $value;
            }
        }
        return $res;
    }

    /**
     * 二维数组根据字段进行排序
     * @params array $array 需要排序的数组
     * @params string $field 排序的字段
     * @params string $sort 排序顺序标志 SORT_DESC 降序；SORT_ASC 升序
     */
    public function array_sequence($array, $field, $sort = 'SORT_DESC')
    {
        $arrSort = array();
        foreach ($array as $uniqid => $row) {
            foreach ($row as $key => $value) {
                $arrSort[$key][$uniqid] = $value;
            }
        }
        array_multisort($arrSort[$field], constant($sort), $array);
        return $array;
    }



    /* 毫秒时间戳转换成日期 */
    public function msecdate($tag, $time)
    {
        $a = substr($time,0,10);
        $date = date($tag,$a);
        return $date;
    }

    /**
     * 验证邮箱格式是否正确
     * @param $email
     * @return bool
     */
    public function isEmail($email)
    {
          // $pattern = '/^\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}$/';
          //@前面的字符可以是英文字母和._- ，._-不能放在开头和结尾，且不能连续出现
         $pattern = '/^[a-z0-9]+([._-][a-z0-9]+)*@([0-9a-z]+\.[a-z]{2,14}(\.[a-z]{2})?)$/i';
         if(preg_match($pattern,$email)){
             return true;
         }else {
             return false;
         }
    }


    //时间戳转换为日期，并按照时间显示不同的内容，如刚刚，分钟前，小时前，今天，昨天等
    public function transTime($ustime)
    {

        $ytime = date("Y-m-d",$ustime);

        $rtime = date("Yn月j日 H:i",$ustime);

        $htime = date("H:i",$ustime);

        $time = time() - $ustime;

        $todaytime = strtotime("today");

        $time1 = time() - $todaytime;

        if($time < 60){
            $str = '刚刚';
        }else if($time < 60 * 60){
            $min = floor($time/60);
            $str = $min.'分钟前';
        }else if($time < $time1){
            $str = '今天'.$htime;
        }else{
            $str = $ytime;
        }
        return $str;

    }

    /**
     * 给二维数据添加 数据
     * @param array $array
     * @param array $keyArray
     * @return array
     */
    public function AddTwoArray(array $array,array $keyArray):array
    {
        foreach ($array as $key=>$value){
            $array[$key][array_keys($keyArray)[0]] = implode(',',$keyArray[array_keys($keyArray)[0]]);
        }
        return $array;
    }

    /**
     * 抓https数据
     *
     * @param unknown $url
     * @param string $data
     * @param string $timeout
     */
    public function https_request($url, $data = null, $timeout = 5000)
    {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (! empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        if ($timeout) {
            curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);

        curl_close($curl);
        return $output;
    }


    /**
     * curl
     * @param $url
     * @param $data
     * @return mixed
     */
    public function https_request2($url,$data='')
    {

        $curl = curl_init($url);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_POST=>TRUE,
            CURLOPT_HTTPHEADER => array(
                "Content-type: application/json;charset='utf-8'",
                "Accept: application/json",
                "Cache-Control: no-cache",
                "Pragma: no-cache",
            ),
            CURLOPT_SSL_VERIFYHOST=>0,
            CURLOPT_SSL_VERIFYPEER=>0

        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
           logs::new()->my_log( "cURL Error #:" . $err);
        } else {
            return $response;
        }
    }

    /**
     * 获取图片的Base64编码(不支持url)
     * @date 2017-02-20 19:41:22
     *
     * @param $img_file 传入本地图片地址
     *
     * @return string
     */
    public function imgToBase64($img_file) {
        $img_base64 = '';
        if (file_exists($img_file)) {

            $app_img_file = $img_file; // 图片路径
            $img_info = getimagesize($app_img_file); // 取得图片的大小，类型等
            $fp = fopen($app_img_file, "r"); // 图片是否可读权限
            if ($fp) {

                $filesize = filesize($app_img_file);
                $content = fread($fp, $filesize);
                $file_content = chunk_split(base64_encode($content)); // base64编码
                switch ($img_info[2]) {           //判读图片类型
                    case 1: $img_type = "gif";
                        break;
                    case 2: $img_type = "jpg";
                        break;
                    case 3: $img_type = "png";
                        break;
                }

                $img_base64 = 'data:image/' . $img_type . ';base64,' . $file_content;//合成图片的base64编码

            }
            fclose($fp);
        }

        return $img_base64; //返回图片的base64
    }

    /**
     * 验证码银行卡号
     * @param $card_number
     * @return string
     */
    public function checkBankCard($card_number)
    {
        $arr_no = str_split($card_number);
        $last_n = $arr_no[count($arr_no)-1];
        krsort($arr_no);
        $i = 1;
        $total = 0;
        foreach ($arr_no as $n){
            if($i%2==0){
                $ix = $n*2;
                if($ix>=10){
                    $nx = 1 + ($ix % 10);
                    $total += $nx;
                }else{
                    $total += $ix;
                }
            }else{
                $total += $n;
            }
            $i++;
        }
        $total -= $last_n;
        $x = 10 - ($total % 10);
        if($x == $last_n){
            return true;
        }else{
            return false;
        }
    }

    /**
     * int 性别转中文
     * @param int $sex
     * @return string
     */
    public function sexIntString(int $sex):string
    {
        $sexString = "";
        switch ($sex)
        {
            case 1:
                $sexString = "男";
                break;
            case 2:
                $sexString = "女";
                break;
            default:
                $sexString = "秘密";
        }

        return $sexString;
    }


}