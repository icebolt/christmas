<?php
include_once('base.php');
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 15/12/24
 * Time: 上午11:47
 */
if($_GET['code']){

    $ret = login();
    var_dump($ret);

}
function login(){
    $code = $_GET['code'];
    $active_id = 2;
    $type =1;
    $login_url = "http://weixin.bbwc.cn/active/public/index.php/index/userrule/login";
    $post_data = array();
    $post_data['code'] = $code;
    $post_data['token'] = $type;
    $post_data['active_id'] = $active_id;
    $res = request_post($login_url, $post_data);
    return $res = json_decode($res, 1);
}