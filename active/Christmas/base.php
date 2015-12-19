<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 15/12/17
 * Time: 下午2:33
 */
session_start();
header("Content-Type: text/html; charset=UTF-8");
//config
$appid = "";
$appsercet = "";
$host = $_SERVER['HTTP_HOST'];
if(strpos($host,"bbwc")){
    $host =$host."/active";
}
$active_id = 2;
$type = "1";

$redirect_uri = "http://" . $host . "/public/index.php/index/user/callback?type=$type&active_id=$active_id";
//判断是否微信浏览器
//验证用户是否合法
is_weixin();
/**
 * 是否微信打开
 * @return bool
 */
function is_weixin()
{
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
        return true;
    }
    echo "请使用微信浏览器打开";
    exit;
}
if(!$_SESSION['inviter_id']){
    $_SESSION['inviter_id'] = isset($_GET['uid'])?$_GET['uid']:0;
}

//是否存在session 不存在跳转到微信授权页面
if (!$_SESSION['uid']) {
    $state = rand(10000, 99999);
    $_SESSION['active']['state'] = $state;

//    $url = "https://open.weixin.qq.com/connect/qrconnect?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_login&state=$state";
    $openid = $_GET['open_id'];//"eroihghjhghjhiadffe";//.time();

    $openid = setcookie('opend_id');
    if($openid){
        $openid = time().$state;
        setcookie ("opend_id", $openid, time()+86400*7);
    }
    //设置7天
//    if(empty($openid)){
//        echo "请登录";
//        exit;
//    }
    $url = "http://".$host."/public/index.php/index/user/test?open_id=$openid&type=1&active_id=2";
    header("location: $url");
    exit;
}

$uid = $_SESSION['uid'];
$token = $_SESSION['token'];

$info = getUserInfo();
$content = 0;  //是否填写基本信息
if ($info['data']['content']) {
    $content = 1;
}
$isWin = checkUser();
$foo = strpos($_SERVER['REQUEST_URI'],'mygift.php');
if($isWin['code'] ==201 && $foo===false){
    header("location:mygift.php");
}
function getUserInfo(){
    global $uid, $token, $active_id, $host;
    $url = 'http://' . $host . '/public/index.php/index/active/getuserinfo';
    $post_data = array();
    $post_data['uid'] = $uid;
    $post_data['token'] = $token;
    $post_data['active_id'] = $active_id;
    $res = request_post($url, $post_data);
    return $res = json_decode($res, 1);
}
//获取中奖信息，显示出来
//
/**
 * 获取最后一条中奖信息
 * @return mixed
 */
function winlist(){
    global $uid, $token, $active_id, $host;
    $url = 'http://' . $host . '/public/index.php/index/active/winlist';
    $post_data = array();
    $post_data['uid'] = $uid;
    $post_data['token'] = $token;
    $post_data['active_id'] = $active_id;
    $res = request_post($url, $post_data);
    return $res = json_decode($res, 1);
}
/**
 * 检查用户是否能抽奖
 * @return mixed
 */
function checkUser()
{
    global $uid, $token, $active_id, $host;
    $url = 'http://' . $host . '/public/index.php/index/active/checkuser';
    $post_data = array();
    $post_data['uid'] = $uid;
    $post_data['token'] = $token;
    $post_data['active_id'] = $active_id;
    $res = request_post($url, $post_data);
    return $res = json_decode($res, 1);
}

//调用接口

function addinfo()
{
    global $uid, $token, $active_id, $host;
    $name = htmlspecialchars($_POST['nametxt']);
    $phone = htmlspecialchars($_POST['mobiletxt']);
    $weixin = htmlspecialchars($_POST['wechattxt']);
    $address = htmlspecialchars($_POST['addresstxt']);

    $url = 'http://' . $host . '/public/index.php/index/active/addinfo';
    $post_data = array();
    $post_data['name'] = $name;
    $post_data['phone'] = $phone;
    $post_data['weixin'] = $weixin;
    $post_data['address'] = $address;
    $post_data['uid'] = $uid;
    $post_data['token'] = $token;
    $post_data['active_id'] = $active_id;

    $res = request_post($url, $post_data);
    return $res = json_decode($res, 1);
}

/**
 * 抽奖
 */
function win()
{
    global $uid, $token, $active_id, $host;
    $url = 'http://' . $host . '/public/index.php/index/active/win';
    $post_data = array();
    $post_data['uid'] = $uid;
    $post_data['token'] = $token;
    $post_data['active_id'] = $active_id;
    $res = request_post($url, $post_data);
    return $res = json_decode($res, 1);
}
/**
 * 抽奖
 */
function friends()
{
    global $uid, $token, $active_id, $host;
    $url = 'http://' . $host . '/public/index.php/index/active/inviter';
    $post_data = array();
    $post_data['uid'] = $uid;
    $post_data['token'] = $token;
    $post_data['active_id'] = $active_id;
    $res = request_post($url, $post_data);
    return $res = json_decode($res, 1);
}

//=======================================================
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

    return $data;
}


