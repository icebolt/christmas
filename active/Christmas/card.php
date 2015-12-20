<DOCTYPE HTML> 
<html><head>
<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0'/> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href='style.css' rel='stylesheet'/>
<script src="js/jquery-1.8.3.min.js"></script>
<script src="js/SRTscript.js"></script>
<title>一盒甄选， 分享暖冬，iWeekly想要这个冬天捂热你</title>
</head>
<script>
	var get_ticket = 'http://user.test.bbwc.cn/weixin/getSign',
            title = '一盒甄选， 分享暖冬，iWeekly想要这个冬天捂热你',
            desc = 'iWeekly圣诞感恩大礼',
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
<body onLoad="showFlash3()">
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
            	<div class="cardgifdiv">
                    <img src="images/card/cardgif1.jpg" style="display:">
                    <img src="images/card/cardgif2.jpg" style="display:">
                    <img src="images/card/cardgif3.jpg" style="display:">
                    <img src="images/card/cardgif4.jpg" style="display:">
                    <img src="images/card/cardgif5.jpg" style="display:">
                    <img src="images/card/cardgif6.jpg" style="display:">
                </div>
                <div class="snowdiv">
                    <img class="snow" src="images/card/snow1.png">
                    <img class="snow" src="images/card/snow2.png">
                    <img class="snow" src="images/card/snow3.png">
                </div>
                <img src="images/card/cardbtn.png" style=" position:absolute;left:175px; top:1040px;" onClick="javascript:showShareTag()">
                <div class="ShareTagPanel">
            		<img class="" src="images/card/tanchuang.png" onClick="javascript:hideShareTag()">
            	</div>
            </div>
        </div>
    </div>
</div>
</body> 
</html> 