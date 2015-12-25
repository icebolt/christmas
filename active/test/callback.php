<?php
//include_once('base.php');
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 15/12/24
 * Time: 上午11:47
 */
if($_GET['code']){

    echo $ret = login();
//    var_dump($ret);

}
function login(){
    $code = $_GET['code'];
    $state = $_GET['state'];
    $arr = explode("#",$state);
    $inviter_id = intval($arr[0]);
    $active_id = 2;
    $type =1;
    $login_url = "http://weixin.bbwc.cn/active/public/index.php/index/userrule/login";
    $post_data = array();
    $post_data['open_id'] = $code;
    $post_data['active_id'] = $active_id;
    $post_data['type'] = $type;
    $post_data['inviter_id'] = $inviter_id;
    $res = request_post($login_url, $post_data);
    return $res = json_decode($res, 1);
}
/**
 * 模拟post进行url请求
 * @param string $url
 * @param array $post_data
 */
function request_post($url = '', $post_data = array())
{
    if (empty($url) || empty($post_data)) {
        return false;
    }

    $o = "";
    foreach ($post_data as $k => $v) {
        $o .= "$k=" . urlencode($v) . "&";
    }
    $post_data = substr($o, 0, -1);

    $postUrl = $url;
    $curlPost = $post_data;
    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL, $postUrl);//抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($ch);//运行curl
    curl_close($ch);
    file_put_contents('/tmp/newactive.log',"接口：".$postUrl."==数据：".var_export($post_data,1)."返回数据：".var_export($data,1)."\r\n",FILE_APPEND);
    return $data;
}
