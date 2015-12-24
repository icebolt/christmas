<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 15/12/24
 * Time: 上午11:32
 */

$appid = 'wx1f831aa265aa59bd';
$redirect_uri = 'http://weixin.bbwc.cn/active/active/test/callback.php';
$state = "";
$url ="https://open.weixin.qq.com/connect/qrconnect?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_login&state=STATE#wechat_redirect";
header("location:$url;");
?>

