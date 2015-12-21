<?php
include_once("base.php");
$ret = winlist();
if($ret['code']==200){
    $info = $ret['data'];
}
$check = checkUser();
$twowin = 0;
if($check['code']==203){
 $twowin = 1;
}
?>
<DOCTYPE HTML>
<html><head>
<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0'/> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href='style.css' rel='stylesheet'/>
<script src="js/jquery-1.8.3.min.js"></script>
<script src="js/SRTscript.js"></script>
<title>一盒甄选， 分享暖冬，iWeekly想要这个冬天捂热你</title>
</head>
<body onLoad="htmlLoaded()">
<input type="hidden" id="twowin" value="<?=$twowin;?>" />
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
        	<div class="indexdiv">
            	<img id="showFlashImgBg" src="images/2/1.jpg">
                <div class="indexfont"><?=$info['nickname']?>获得<?=$info['prizeName']?></div>
                <div style="width:190px; height:130px; position:absolute;top:0px; left:550px;" onClick="javascript:showGiftList()"></div>
                <div class="indexFlash">
                <img src="images/2/2.jpg" style="display:none;">
                <img src="images/2/3.jpg" style="display:none;">
                <img src="images/2/4.jpg" style="display:none;">
                <img src="images/2/5.jpg" style="display:none;">
                <img src="images/2/6.jpg" style="display:none;">
                <img src="images/2/7.jpg" style="display:none;">
                <img src="images/2/8.jpg" style="display:none;">
                <img src="images/2/9.jpg" style="display:none;">
                </div>
                <img id="FlashImgHand" src="images/2/10.png" style="display:none;">
        		<div style="width:190px; height:150px; position:absolute;top:420px; left:10px;" onClick="javascript:showGift(1)"></div>
                <div style="width:160px; height:190px; position:absolute;top:200px; left:170px;" onClick="javascript:showGift(2)"></div>
                <div style="width:160px; height:220px; position:absolute;top:370px; left:350px;" onClick="javascript:showGift(3)"></div>
                <div style="width:190px; height:150px; position:absolute;top:280px; left:550px;" onClick="javascript:showGift(4)"></div>
                
                <div class="giftListPanel">
                    <img class="coverdesc" src="images/2/11.png" onClick="javascript:hideGiftList()">
              	</div>
                <div class="infoPanel">
                    <img class="" src="images/3/1.png">
                    <form name="form1" action="gift.php?uid=<?=$uid;?>&giftid=1" method="post">
                    <input id="nametxt" name="nametxt" type="text" />
                    <input id="mobiletxt" name="mobiletxt" type="text" />
                    <input id="wechattxt" name="wechattxt" type="text" />
                    <input id="addresstxt" name="addresstxt" type="text"/>
                    <input id="showGiftID" name="showGiftID" type="hidden" value="1"/>
                    <input id="inviter_id" name="inviter_id" type="hidden" value="<?=$inviter_id?>">
                    </form>
                    <div id="submitinfo" onClick="javascript:goPage()"></div>
                    <div id="infoClose" onClick="javascript:hideInfoList()"></div>
             	</div>
            </div>
            <div class="cover">
            	<img src="images/1/1.jpg">
                <div class="snowdiv">
                <img class="snow" src="images/1/2.png">
                <img class="snow" src="images/1/3.png">
                </div>
                <div id="showrule" style="width:150px; height:100px; position:absolute;left:580px; top:0px;;"></div>
                <div id="showindexbtn" style="width:400px; height:100px; position:absolute;left:160px; top:980px;"></div>
              	<div class="descPanel">
                    <img class="coverdesc" src="images/1/4.png">
              	</div>
            </div>
            
        </div>
    </div>
</div>
<input type="hidden" id="user_content" value="<?=$content;?>">
<input type="hidden" id="uid" value="<?=$_SESSION['uid'];?>">
<script type="text/javascript">
	var twowin = $("#twowin").val();
	if(twowin==1){
		coverClick();
	}
</script>
<script>
var _hmt = _hmt || [];
(function() {
var hm = document.createElement("script");
hm.src = "//hm.baidu.com/hm.js?8ce969f75367b959a425732378a6333f";
var s = document.getElementsByTagName("script")[0]; 
s.parentNode.insertBefore(hm, s);
})();
</script>
</body> 
</html> 
