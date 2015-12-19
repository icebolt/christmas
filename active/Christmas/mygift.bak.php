<?php
include_once("base.php");

?>
<DOCTYPE HTML>
<html><head>
<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0'/> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href='style.css' rel='stylesheet'/>
<script src="js/jquery-1.8.3.min.js"></script>
<script src="js/SRTscript.js"></script>
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
            <div class="myGiftdiv">
            	<img src="images/4/1.jpg" style="display:">
                <img src="images/4/2.jpg" style="display:none">
                <div class="snowdiv">
                <img class="snow" src="images/1/2.png">
                <img class="snow" src="images/1/3.png">
                </div>
                <img src="images/4/6.jpg" style=" position:absolute;left:90px; top:559px;">
                <img src="images/4/6.jpg" style=" position:absolute;left:295px; top:559px;">
                <img src="images/4/6.jpg" style=" position:absolute;left:500px; top:559px;">
                <img src="images/4/6.jpg" style=" position:absolute;left:90px; top:780px;">
                <img src="images/4/6.jpg" style=" position:absolute;left:295px; top:780px;">
                <img src="images/4/6.jpg" style=" position:absolute;left:500px; top:780px;">
                <img src="images/4/3.png" style=" position:absolute;left:80px; top:1000px;" onClick="javascript:showShareTag()">
                <img src="images/4/4.png" style=" position:absolute;left:410px; top:1000px;" onClick="javascript:goPage2()">
                <img src="images/4/5.png" style=" position:absolute;left:410px; top:1000px;display:none;">
                <div class="ShareTagPanel">
            		<img class="" src="images/3/26.png" onClick="javascript:hideShareTag()">
            	</div>
            </div>
        </div>
    </div>
</div>
</body> 
</html> 