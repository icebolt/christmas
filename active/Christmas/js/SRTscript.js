///综合参数
var parameter={
	defaultWidth:750,//窗口默认宽度度
	defaultHeight:1206,//窗口默认高度
	materialCount:0,//总素材量
	materialLoaded:0,//总素材量
	materialLoadFinish:false,//素材加载是否完成
	htmlLoadFinish:false,//页面加载是否完成
	resizeError:false,//页面是否被错误加载
	startX:0,//起始位置X轴坐标
	startY:0,//起始位置Y轴坐标
	moveX:0,//X轴上移动的距离
	moveY:0,//Y轴上移动的距离
	minDistance:50,//最小距离判定
	flashid:0,
	flashid2:0,
	flashid3:0,
	timer:null,
	timer2:null,
	timer3:null,
	item:null,
	item2:null,
	item3:null,
	flag:true,
	flag2:null,
	flag3:null,
};

///DOM创建完成时
$(document).ready(function(){
   	resize();
	initialize();
});


///手机翻转时
$(window).resize(function(){
    resize();
});

///重置屏幕大小相关
var resize=function(){
	var w=document.body.clientWidth;
	var h=document.body.clientHeight;
	var rate=0,left=0,top=0;
	/*if(w>h){
		$(".resizeMessage").show();
		parameter.resizeError=true;
	}else{
		if(parameter.resizeError){
			window.location.href=window.location.href;
		}
	}*/
	if(w/h>parameter.defaultWidth/parameter.defaultHeight){
		rate=h/parameter.defaultHeight;
		left=(w-parameter.defaultWidth*rate)/(2*rate);
	}else{
		rate=w/parameter.defaultWidth;
		top=(h-parameter.defaultHeight*rate)/(2*rate);
	}
	$(".scale").css({"-webkit-transform":"scale("+rate+") translate3d("+left+"px,"+top+"px,0px)"});
}

///初始化
var initialize=function(){
	pageLoaded();
}

///所有素材加载完毕后
var pageLoaded=function(){
	$(".content").addClass("showContent");
	$("#showrule").on("click",showDesc);
	$(".descPanel").on("click",hideDesc);
	$("#showindexbtn").on("click",coverClick);
	
	var swiper = $(".snowdiv");
	parameter.item = swiper.find("img");
	flash();
	parameter.timer = setInterval(flash,800);
}

var flash=function(){
	var controller = parameter.item.eq(parameter.flashid);
	parameter.item.hide();
	controller.show();
	parameter.flashid++;
	if(parameter.flashid == $(".snowdiv img").length){
      parameter.flag = false;
      parameter.flashid = 0;
    }else if(parameter.flashid == 0){
      parameter.flag = true;
    }
}

///显示活动详情
var showDesc=function(){
	$(".descPanel").fadeIn(500).addClass("showDescPanel");
}

///隐藏活动详情
var hideDesc=function(){
	$(".descPanel").fadeOut(500).removeClass("showDescPanel");
}

///显示礼物详情
var showGiftList=function(){
	$(".giftListPanel").fadeIn(500).addClass("showgiftListPanel");
}

///隐藏礼物详情
var hideGiftList=function(){
	$(".giftListPanel").fadeOut(500).removeClass("showgiftListPanel");
}



///封面离去
var coverClick=function(){
	$(".cover").addClass("coverLeave");
	setTimeout(function(){
		$(".cover").remove();
		$(".indexdiv").animate({opacity:"1"}, 1000,"","");
		$("#showFlashImgBg").on("click",showFlash);
		
	},500);
}



var showFlash=function(){
	
	var swiper = $(".indexFlash");
	parameter.item2 = swiper.find("img");
	flash2();
	parameter.timer2 = setInterval(flash2,200);
}

var flash2=function(){
	var controller = parameter.item2.eq(parameter.flashid2);
	parameter.item2.hide();
	controller.show();
	parameter.flashid2++;
	if(parameter.flashid2 == $(".indexFlash img").length){
      parameter.flag2 = false;
      parameter.flashid2 = $(".indexFlash img").length-2;
	  $("#FlashImgHand").show();
	  clearInterval(parameter.timer2);
    }else if(parameter.flashid2 == 0){
      parameter.flag2 = true;
    }
}

var showGift=function(GiftID){
	var c =  $("#user_content").val();
	if(c == 1){
		goPage2(GiftID);
	}else{
		showInfoList(GiftID);
	}

}

///显示用户填写资料页
var showInfoList=function(GiftID){
	$(".infoPanel").fadeIn(500).addClass("showgiftListPanel");
	$("#showGiftID").val(GiftID);
}

///隐藏用户填写资料页
var hideInfoList=function(){
	$(".infoPanel").fadeOut(500).removeClass("showgiftListPanel");
}

var goPage=function(){
	
	var message="";
	if($("#nametxt").val()==""){
		message+="请输入姓名！\r\n";
	}
	if($("#mobiletxt").val()==""){
		message+="请输入手机号码！\r\n";
	}
	if($("#wechattxt").val()==""){
		message+="请输入微信ID！\r\n";
	}
	if($("#addresstxt").val()==""){
		message+="请输入联系地址！\r\n";
	}
	if(message!="")
	{
		alert(message);
	}
	else
	{
		var gid = encodeURI($("#showGiftID").val());
		$('form').attr('action',"gift.php?giftid="+gid);
		form1.submit();	
		//window.location.href="gift.php?giftid="+encodeURI($("#showGiftID").val());
	}
	
}
var goPage2=function(id){
	var uid = $("#uid").val();
	window.location.href="gift.php?giftid="+encodeURI(id)+"&uid="+uid;
}

///显示中奖信息
var showTag=function(GiftID){
	
	//var url=window.location.href;
	//if(url.indexOf("?giftid=")>0){
	//	if(getUrlParam('giftid') == GiftID)
	//	{
	//		$(".tagPanel").fadeIn(500).addClass("showTagPanel");
	//	}
	//	else
	//	{
	//		alert('请选择您获得的奖品')
	//	}
	//}
	var id = $("#giftid").val();
	if(id == GiftID){
		$(".tagPanel").fadeIn(500).addClass("showTagPanel");
	}else{
		alert('请选择您获得的奖品');
	}
}


///隐藏中奖信息
var hideTag=function(){
	$(".tagPanel").fadeOut(500).removeClass("showTagPanel");
}

///显示分享提示
var showShareTag=function(GiftID){
	$(".ShareTagPanel").fadeIn(500).addClass("showShareTagPanel");

}
///隐藏分享提示
var hideShareTag=function(){
	$(".ShareTagPanel").fadeOut(500).removeClass("showShareTagPanel");
}

function getUrlParam(name) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
	var r = window.location.search.substr(1).match(reg);  //匹配目标参数
	if (r != null) return unescape(r[2]); return null; //返回参数值
}

var showFlash3=function(){
	
	var swiper = $(".cardgifdiv");
	parameter.item3 = swiper.find("img");
	flash3();
	parameter.timer3 = setInterval(flash3,400);
}

var flash3=function(){
	var controller = parameter.item3.eq(parameter.flashid3);
	parameter.item3.hide();
	controller.show();
	parameter.flashid3++;
	if(parameter.flashid3 == $(".cardgifdiv img").length){
      parameter.flag3 = false;
      parameter.flashid3 = $(".cardgifdiv img").length-1;
	  clearInterval(parameter.timer3);
    }else if(parameter.flashid3 == 0){
      parameter.flag3 = true;
    }
}
















