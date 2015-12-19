<?php
include_once("base.php");
//提交个人信息
if($_POST){
    $name = htmlspecialchars($_POST['nametxt']);
    $phone = htmlspecialchars($_POST['mobiletxt']);
    $weixin = htmlspecialchars($_POST['wechattxt']);
    $address = htmlspecialchars($_POST['addresstxt']);

    $showGiftID = intval($_POST['showGiftID']);
    $url = 'http://'.$host.'/public/index.php/index/active/addinfo';
    $post_data = array();
    $post_data['name']       = $name;
    $post_data['phone']      = $phone;
    $post_data['weixin'] = $weixin;
    $post_data['address']    = $address;
    $post_data['uid']    = $uid;
    $post_data['token'] = $token;
    $post_data['active_id'] = $active_id;
    //$post_data = array();
    $res = request_post($url, $post_data);
    $res = json_decode($res,1);
    if($res['code'] == 200){

    }
}
?>
<DOCTYPE HTML>
<html><head>
<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0'/> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href='style.css' rel='stylesheet'/>
<script src="js/jquery-1.8.3.min.js"></script>
<script src="js/SRTscript.js"></script>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<title>圣诞抽奖</title>
</head>
<script>
	var get_ticket = 'http://user.test.bbwc.cn/weixin/getSign',
            title = 'iWeekly2015圣诞回馈',
            desc = 'iWeekly2015圣诞回馈',
            link = 'http://mobile.iweeklyapp.com/articles/xmas2015/index.html',
            imgUrl='http://mobile.iweeklyapp.com/articles/xmas2015/images/icon.jpg';
	$(document).ready(function () {
        $.getJSON(get_ticket, {url: encodeURIComponent(location.href.split('#')[0])}, function (data) {
            wx.config({
                debug: false,
                appId: data.data.appId,
                nonceStr: data.data.nonceStr,
                timestamp: data.data.timestamp,
                signature: data.data.signature,
                jsApiList: ['onMenuShareAppMessage', 'onMenuShareTimeline', 'onMenuShareQQ', 'onMenuShareWeibo']
            });

            wx.ready(function () {

        

                wx.onMenuShareAppMessage({
                    title: title,
                    desc: desc,
                    link: link,
                    imgUrl: imgUrl
                });


                wx.onMenuShareTimeline({
                    title: title,
                    link: link,
                    imgUrl: imgUrl
                });

                wx.onMenuShareQQ({
                    title: title,
                    desc: desc,
                    link: link,
                    imgUrl: imgUrl
                });

                wx.onMenuShareWeibo({
                    title: title,
                    desc: desc,
                    link: link,
                    imgUrl: imgUrl
                });
            });
            wx.error(function (res) {
                 //alert(res.errMsg);
            });
        });
})
	
	
</script>
<body onLoad="htmlLoaded()">
<div class="container">
    <div class="scale">
        <div class="spinner">
            <div class="spinner-container container1">
                <div class="circle1"></div>
               <div class="circle2"></div>
               <div class="circle3"></div>
               <div class="circle4"></div>
           </div>
           <div class="spinner-container container2">
                <div class="circle1"></div>
                <div class="circle2"></div>
                <div class="circle3"></div>
                <div class="circle4"></div>
           </div>
           <div class="spinner-container container3">
                <div class="circle1"></div>
                <div class="circle2"></div>
                <div class="circle3"></div>
                <div class="circle4"></div>
          </div>
        </div>
        
        <div class="content">
        	<div class="giftdiv">
            <img src="images/3/2.jpg" style="">
            <div class="snowdiv">
                <img class="snow" src="images/1/2.png">
                <img class="snow" src="images/1/3.png">
                </div>
            <img src="images/3/22.png" style=" position:absolute;left:10px; top:400px;" width="188" height="188" onClick="javascript:showTag(1)">
            <img src="images/3/24.png" style=" position:absolute;left:150px; top:200px;" width="188" height="188" onClick="javascript:showTag(2)">
            <img src="images/3/21.png" style=" position:absolute;left:330px; top:420px;" width="188" height="188" onClick="javascript:showTag(3)">
            <img src="images/3/22.png" style=" position:absolute;left:540px; top:260px;" width="188" height="188" onClick="javascript:showTag(4)">
            <div class="giftfont">恭喜你获得iWeekly贺卡一张</div>
            <div class="tagPanel">
            	<img class="coverdesc" src="images/3/4.png">
                <div id="shareBtn" onClick="javascript:showShareTag()"></div>
                <div id="closeshareBtn" onClick="javascript:hideTag()"></div>
            </div>
            <div class="ShareTagPanel">
            	<img class="coverdesc" src="images/3/26.png" onClick="javascript:hideShareTag()">
            </div>
            </div>
        </div>
    </div>
</div>
</body> 
</html> 