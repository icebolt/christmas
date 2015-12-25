<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 15/12/24
 * Time: 上午11:32
 */

$appid = 'wx9a57805496ad6b31';
$appid = 'wx5b318b313920170e';
$appid = 'wx1f831aa265aa59bd'; //网页登录
$redirect_uri = urlencode('http://weixin.bbwc.cn/active/active/test/callback.php');
$state = "";
$inviter_id = 1052;
$host = "https://open.weixin.qq.com/connect/oauth2/authorize";
  //   * ?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect";
//我是网页登录
$url ="https://open.weixin.qq.com/connect/qrconnect?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_login&state=$inviter_id#wechat_redirect";
//https://open.weixin.qq.com/connect/oauth2/authorize
//?appid=wxf0e81c3bee622d60&redirect_uri=http%3A%2F%2Fnba.bluewebgame.com%2Foauth_response.php
//&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect
//https://open.weixin.qq.com/connect/oauth2/authorize?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect
//我是移动端登录
//$url = $host."?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=data#wechat_redirect";
header("location:$url;");
?>

