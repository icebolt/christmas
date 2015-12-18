<?php
include_once("base.php");
//提交个人信息
if($content == 1){
    //不需要填写信息直接抽奖
    $showGiftID = intval($_GET['giftid']);
    $res = win();
    if($res['code']!=200){
        header("location:mygift.php");
    }
    $name = $res['data']['name'];
}else {
    if ($_POST) {
        $res = addinfo();
        $showGiftID = intval($_POST['showGiftID']);
        if ($res['code'] == 200) {
            //执行抽奖
            $res = win();
            if($res['code']!=200){
                header("location:mygift.php");
            }
            $name = $res['data']['name'];
        }
    }
}
$showGiftID>0?$showGiftID:1;
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
            <img src="images/3/22.png" id="img1" style=" position:absolute;left:10px; top:400px;" width="188" height="188" onClick="javascript:showTag(1)">
            <img src="images/3/24.png" id="img2" style=" position:absolute;left:150px; top:200px;" width="188" height="188" onClick="javascript:showTag(2)">
            <img src="images/3/21.png" id="img3" style=" position:absolute;left:330px; top:420px;" width="188" height="188" onClick="javascript:showTag(3)">
            <img src="images/3/22.png" id="img4" style=" position:absolute;left:540px; top:260px;" width="188" height="188" onClick="javascript:showTag(4)">
            <div class="giftfont">恭喜你获得<?=$name;?></div>
            <div class="tagPanel">
            	<img class="coverdesc" id="img5" src="images/3/11.png">
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
<input type="hidden" id="giftid" value="<?=$showGiftID;?>">
</body> 
</html> 