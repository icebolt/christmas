

(function(){
	function Slide(box,nav,index){
		this.box = box;
		this.navbox = nav;
		this.navs = null;
		this.lis = null;
		this.len = null;
		this.cur = null;
		this.next = null;
		this.pre = null;
		this.curIndex = 0;
		this.nextIndex = 0;
		this.preIndex = 0;
		this.nnIndex = 0;
		this.ppIndex = 0;
		this.pageX = 0;
		this.pageY = 0;
		this.fangxiang = "";
		this.imageArr = null;
		this.imageIndex = index;
		this.init();
	}
	Slide.prototype = {
		init : function () {
			var k = this;
			k.addImage();
			k.lis = k.box.find('li');
			k.len = k.lis.length;
			k.curIndex = 0;
			k.nextIndex = 1;
			k.preIndex = k.len -1;
			k.lis.eq(k.curIndex)[0].className = 'cur';
			k.lis.eq(k.nextIndex)[0].className = 'next';
			k.lis.eq(k.preIndex)[0].className = 'pre';
			k.addNav();
			k.events();
		},
		addImage: function () {
			var k = this;
			k.imageArr = images1[k.imageIndex-1];
			var len = k.imageArr.length, html = [], src;
			for(var i = 0; i < len; i++){
				src = prefix1+k.imageArr[i];
				html.push('<li index="1"><img src="'+src+'"></li>');
			}
			k.box.find('ul').html(html.join(""));
		},
		events: function() {
			var k = this;
			if("ontouchstart" in window) {
				//window.addEventListener("resize orientationchange", k.resize.bind(k), false);
				"touchstart touchmove touchend touchcancel".split(" ").forEach(function(item) {
					k.box[0].addEventListener(item, k[item].bind(k), false);
				});
			} else if(window.PointerEvent) {
				"pointerdown pointermove pointerup pointercancel".split(" ").forEach(function(item) {
					var item1;
					if(item == 'pointerdown'){
						item1 = "touchstart";
					} else if (item == 'pointermove') {
						item1 = "touchmove";
					} else if (item == 'pointerup') {
						item1 = "touchend";
					} else if (item == 'pointercancel') {
						item1 = "touchcancel";
					} else if(item == 'mousemove') {
						item1 = "touchmove";
					}
					k.box[0].addEventListener(item, k[item1].bind(k), false);
				});
			} else if(window.MSPointerEvent) {
				 "MSPointerDown MSPointerMove MSPointerUp MPointerCancel".split(" ").forEach(function(item) {
					var item1;
					if(item == 'MSPointerDown'){
						item1 = "touchstart";
					} else if (item == 'MSPointerMove') {
						item1 = "touchmove";
					} else if (item == 'MSPointerUp') {
						item1 = "touchend";
					} else if (item == 'MPointerCancel') {
						item1 = "touchcancel";
					} 
					k.box[0].addEventListener(item, k[item1].bind(k), false);
				});
			} else {
				"mousedown mousemove mouseup".split(" ").forEach(function(item) {
					var item1;
					if(item == 'mousedown'){
						item1 = "touchstart";
					} else if (item == 'mousemove') {
						item1 = "touchmove";
					} else if (item == 'mouseup') {
						item1 = "touchend";
					} 
					k.box[0].addEventListener(item, k[item1].bind(k), false);
				});
			} 
		},
		getPoint : function (e) {
			var t = {};
			if(e.touches) {
				if(!e.touches[0]) {
					return ;
				}
				t.x = e.touches[0].clientX;
				t.y = e.touches[0].clientY;
			} else {
				t.x = e.clientX;
				t.y = e.clientY;
			}
			return t;
		},
		touchstart: function(e) {
			var t = this.getPoint(e);
			this.pageX = t.x;
			this.pageY = t.y;
			this.fangxiang = "none";
			e.preventDefault();
		},
		touchmove: function(e) {
		    e.preventDefault();
		   var t = this.getPoint(e);
		   var px = t.x;
		   var py = t.y;
		   var lenX = px - this.pageX;
		   var lenY = py - this.pageY;
		   if(Math.abs(lenY) > Math.abs(lenX)) {
			  return;
		   }
		   if(lenX > 10 ) {
			 this.fangxiang = "right";
		   } else if (lenX < -10) {
			 this.fangxiang = 'left';
		   } else {
			 this.fangxiang = "none";
		   }
		},
		touchend: function(e) {
		   e.preventDefault();
		   var k = this;
		   if(k.fangxiang == 'right') {
			  k.swiperight();
		   } else if(k.fangxiang == "left") {
			  k.swipeleft();
		   } else if(k.fangxiang == "none"){
			  k.switchToPage3(e);
		   }
		},
		touchcancel: function(e) {
		   e.preventDefault();
		   var k = this;
		   if(k.fangxiang == 'right') {
			  k.swiperight();
		   } else if(k.fangxiang == "left") {
			  k.swipeleft();
		   } else if(k.fangxiang == "none"){
			  k.switchToPage3(e);
		   }
		},
		switchToPage3: function (e) {
			var tag = e.target.tagName.toLowerCase();
			var src ;
			if(tag == 'img') {
				src =  e.target.src;
				curPage.css('display', 'none');
				curPage = page3;
				page3.find('img').attr('src',src);
				curPage.css('display', 'block');
			}
		},
		clear: function () {
			var k = this;
			k.lis.each(function() {
				this.className = "";
			});
		},
		addNav: function () {
		   var k = this, html = [];
		   for(var i =0; i < k.len; i++ ){
			if(i == k.curIndex){
		      html.push("<span class='cur'></span>");
			} else {
			   html.push("<span></span>");
			 }
		   }
		   k.navbox.html(html.join(''));
		   k.navs = k.navbox.find('span');
		},
		setNav: function () {
			var k = this;
			k.navs.removeClass('cur').eq(k.curIndex).addClass('cur');
		},
		swipeleft: function (e){
			var k = this;
			k.clear();
				k.curIndex = k.curIndex + 1;
                if (k.curIndex >= k.len) {
					k.curIndex = 0;
				}
				k.preIndex = k.curIndex > 0 ? k.curIndex - 1 : k.len-1;
                k.nextIndex = k.curIndex == k.len-1 ? 0 : k.curIndex + 1;
                k.ppIndex = k.preIndex - 1 >= 0 ? k.preIndex - 1 : k.len-1;
                k.nnIndex = k.nextIndex + 1 > k.len-1 ? 0 : k.nextIndex + 1;
				
			  k.lis.eq(k.curIndex)[0].className = 'cur';
			  k.lis.eq(k.nextIndex)[0].className = 'next';
			  k.lis.eq(k.preIndex)[0].className = 'pre';
			  k.lis.eq(k.nnIndex)[0].className = 'nnext';
			  k.lis.eq(k.ppIndex)[0].className = 'ppre';
			  k.setNav();
		},
		swiperight: function () {
			var k = this;
			k.clear();
			    k.curIndex = k.curIndex - 1;
				if (k.curIndex < 0) {
					k.curIndex = k.len-1;
				}
				k.preIndex = k.curIndex > 0 ? k.curIndex - 1 : k.len-1;
				k.nextIndex = k.curIndex == k.len-1 ? 0 : k.curIndex + 1;
				k.ppIndex = k.preIndex - 1 >= 0 ? k.preIndex - 1 : k.len-1;
				k.nnIndex = k.nextIndex + 1 > k.len-1 ? 0 : k.nextIndex + 1;
			  k.lis.eq(k.curIndex)[0].className = 'cur';
			  k.lis.eq(k.nextIndex)[0].className = 'next';
			  k.lis.eq(k.preIndex)[0].className = 'pre';
			   k.lis.eq(k.ppIndex)[0].className = 'ppre';
			   k.lis.eq(k.nnIndex)[0].className = 'nnext';
			  
			  k.setNav();
		}
	};
	window.Slide = Slide;
}());

(function(){
	function Zoom(box,btn,max){
		this.box = box;
		this.btn = btn;
		this.pageX = 0;
		this.pageY = 0;
		this.max = max;
		this.init();
		this.flag = false;
		this.bottom = 0;
		this.moving = false;
	}
	Zoom.prototype = {
		init : function () {
			var k = this;
			k.events();
		},
		events: function() {
			var k = this;
			if("ontouchstart" in window) {
				//window.addEventListener("resize orientationchange", k.resize.bind(k), false);
				"touchstart touchmove touchend touchcancel".split(" ").forEach(function(item) {
					k.btn[0].addEventListener(item, k[item].bind(k), false);
				});
			} else if(window.PointerEvent) {
				"pointerdown pointermove pointerup pointercancel".split(" ").forEach(function(item) {
					var item1;
					if(item == 'pointerdown'){
						item1 = "touchstart";
					} else if (item == 'pointermove') {
						item1 = "touchmove";
					} else if (item == 'pointerup') {
						item1 = "touchend";
					} else if (item == 'pointercancel') {
						item1 = "touchcancel";
					} else if(item == 'mousemove') {
						item1 = "touchmove";
					}
					k.btn[0].addEventListener(item, k[item1].bind(k), false);
				});
			} else if(window.MSPointerEvent) {
				 "MSPointerDown MSPointerMove MSPointerUp MPointerCancel".split(" ").forEach(function(item) {
					var item1;
					if(item == 'MSPointerDown'){
						item1 = "touchstart";
					} else if (item == 'MSPointerMove') {
						item1 = "touchmove";
					} else if (item == 'MSPointerUp') {
						item1 = "touchend";
					} else if (item == 'MPointerCancel') {
						item1 = "touchcancel";
					} 
					k.btn[0].addEventListener(item, k[item1].bind(k), false);
				});
			} else {
				"mousedown mousemove mouseup".split(" ").forEach(function(item) {
					var item1;
					if(item == 'mousedown'){
						item1 = "touchstart";
					} else if (item == 'mousemove') {
						item1 = "touchmove";
					} else if (item == 'mouseup') {
						item1 = "touchend";
					} 
					k.box[0].addEventListener(item, k[item1].bind(k), false);
				});
			} 
		},
		getPoint : function (e) {
			var t = {};
			if(e.touches) {
				if(!e.touches[0]) {
					return ;
				}
				t.x = e.touches[0].clientX;
				t.y = e.touches[0].clientY;
			} else {
				t.x = e.clientX;
				t.y = e.clientY;
			}
			return t;
		},
		getBottom: function () {
			var k = this;
			var b = parseInt(window.getComputedStyle(k.box[0], "").bottom) || 0;
			return b;
		},
		touchstart: function(e) {
			var t = this.getPoint(e);
			this.pageX = t.x;
			this.pageY = t.y;
			this.bottom = this.getBottom();
			e.preventDefault();
			if(this.box.hasClass('form1')){
				this.box.removeClass('form1').addClass('form');
			}
		},
		touchmove: function(e) {
		    e.preventDefault();
		   var t = this.getPoint(e);
		   var px = t.x;
		   var py = t.y;
		   var lenX = px - this.pageX;
		   var lenY = py - this.pageY;
		   if(Math.abs(lenY) < Math.abs(lenX)) {
			  return;
		   }
		   if(!this.flag) {
			 if(lenY < 0) {return;}
			 if(Math.abs(this.bottom-lenY) >= this.max) {
				this.box.css('bottom', -this.max+"px");
			 }else{
				this.box.css('bottom', this.bottom-lenY+"px");
			 }
		   } else {
			 if(lenY > 0) {return;}
			 if(this.bottom-lenY >=0){
				this.box.css('bottom', 0+"px");
			 } else {
				this.box.css('bottom', this.bottom-lenY+"px");
			 }
		   }
		   this.moving = true;
		   
		},
		touchend: function(e) {
		   e.preventDefault();
		   var k = this;
		   if(!k.moving) {
			  return;
		   }
		    k.moving = false;
		   if(!k.flag) {
				k.flag = true;
				k.box.css('bottom', -this.max+"px");
				k.box.removeClass('form').addClass('form1');
		   } else {
			    k.flag = false;
				k.box.css('bottom', 0+"px");
				k.box.removeClass('form1').addClass('form');
		   }
		},
		touchcancel: function(e) {
		   e.preventDefault();
		   var k = this;
		   if(!k.moving) {
			  return;
		   }
		    k.moving = false;
		   if(!k.flag) {
				k.flag = true;
				k.box.css('bottom', -this.max+"px");
				k.box.removeClass('form').addClass('form1');
		   } else {
			    k.flag = false;
				k.box.css('bottom', 0+"px");
				k.box.removeClass('form1').addClass('form');
		   }
		   
		}
	};
	window.Zoom = Zoom;
}());

var winW = function () {
	var a = 480;
	if (window.innerWidth) {
		a = window.innerWidth
	} else {
		if (document.body && document.body.clientWidth) {
			a = document.body.clientWidth
		} else {
			if (document.documentElement && document.documentElement.clientWidth) {
				a = document.documentElement.clientWidth
			}
		}
	}
	return a
},
winH = function () {
	var a = 640;
	if (window.innerHeight) {
		a = window.innerHeight
	} else {
		if (document.body && document.body.clientHeight) {
			a = document.body.clientHeight
		} else {
			if (document.documentElement && document.documentElement.clientHeight) {
				a = document.documentElement.clientHeight
			}
		}
	}
	return a
};
var click = "ontouchstart" in window ? 'touchend' : 'click';
//预加载图片
(function(){
	window.prefix1 = 'images/src/';
	window.images1 = [['friend/1.png','friend/4.png','friend/5.png','friend/6.png','friend/7.png','friend/9.png','friend/11.png','friend/13.png','friend/14.png'],
	['family/1.png','family/2.png','family/13.png','family/14.png','family/15.png'],
	['love/3.png','love/7.png','love/8.png','love/10.png','love/13.png','love/15.png'],
	['self/1.png','self/2.png','self/4.png','self/5.png','self/7.png','self/10.png','self/12.png','self/14.png','self/15.png']];
	
	window.prefix2 = 'images/package/';
	window.images2 = [['friend/1.png','friend/4.png','friend/5.png','friend/6.png','friend/7.png','friend/9.png','friend/11.png','friend/13.png','friend/14.png'],
	['family/1.png','family/2.png','family/13.png','family/14.png','family/15.png'],
	['love/3.png','love/7.png','love/8.png','love/10.png','love/13.png','love/15.png'],
	['self/1.png','self/2.png','self/4.png','self/5.png','self/7.png','self/10.png','self/12.png','self/14.png','self/15.png']];
	var PreLoad = function (imgArr,prefix) {
		this.items = imgArr;
		this.prefix = prefix;
		this.load();
	};
	PreLoad.prototype.load = function () {
		var k = this;
		var arr = k.items;
		var len = arr.length;
		var imgSrc = '';
		var s ;
		for(var i = 0; i < len; i++ ) {
			var temp = arr[i];
			for(var j = 0, l = temp.length; j < l; j++){
				imgSrc = temp[j];
				s = new Image;
				//console.log(k.prefix + imgSrc);
				s.src = k.prefix + imgSrc;
			}
		}
	};
	
	new PreLoad(images1, prefix1);
	new PreLoad(images2, prefix2);
}());


var page1 = $('#page1');
var page2 = $('#page2');
var page3 = $('#page3');
var page4 = $('#page4');
var curPage = page1;
var address = 'http://activity.bb.bbwc.cn/html/iBBSepShare/';
var newArr = [];
var sendFlag = true;
function isInArr (index) {
	var i =0, l = newArr.length;
	for(; i < l; i++) {
		if (newArr[i] == index) {
			return false;
		}
	}
	return true;
}
$(function(){
	
	var h = winH(), form = $('#form'), zoom = form.find('.zoom'), 
	con = $('.container'), conH = h*0.6924603174603175, conB = h*0.4563492063492063;
	var pageUl = null, nava = null;
	var ulIndex = 1;
	//page1
	page1.on(click, function(e){
		page1.css('display', 'none');
		curPage = page2;
		curPage.css('display', 'block');
		new Slide($('#ul1'), $('#dot1'), 1);
		pageUl = $('#ul1');
		nava = $('.nav').find('a').eq(0);
		ulIndex = 1;
		newArr.push(1);
	});
	// page2
	page2.find('.nav').on(click, 'a', function(e) {
		e.preventDefault();
		var t = $(this);
		var index = t.attr('index');
		if (ulIndex == index){return;}
		pageUl.css("display","none");
		nava[0].className = "";
		pageUl = $('#ul'+index);
		nava = t;
		pageUl.css("display","block");
		nava[0].className = 'on';
		ulIndex = index;
		if(isInArr(index)){
			new Slide(pageUl, $('#dot'+index), index);
			newArr.push(index);
		}
	});
	//page3
	page3.find('.btn1').on(click, function(e){
		e.preventDefault();
		curPage.css('display', 'none');
		curPage = page2;
		curPage.css('display', 'block');
	});
	// ajax 在此(发送按钮)
	page3.find('.btn2').on(click, function(e){
		e.preventDefault();
		var from = $("#mailFrom").val();
		var to = $("#mailTo").val();
		var toName = $("#mailSend").val();
		var content = $("#mailContent").val();
		var thumb = page3.find('img').attr('src');
		if(!from){
			return;
		}
		if(!to){
			return;
		}
		if(!toName){
			return;
		}
		if(!content){
			return;
		}
		if(sendFlag === false)
			return false;
		sendFlag = false;
		$.ajax({
		  type: 'POST',
		  url: 'http://activity.bb.bbwc.cn/mail/send',//
		  dataType: 'json',
		  timeout: 30000000,
		  data : {
			'from' : from,
			'to' : to,
			'toName' : toName,
			'content' : content,
			'thumb' : thumb
		  },
		  success: function(data){
			if(data.status == "success"){
				curPage.css('display', 'none');
				var src = page3.find('img').attr('src');
				var newsrc = src.replace('src', 'package');
				curPage = page4;
				page4.find('img').attr('src', newsrc);
				curPage.css('display', 'block');
				sendFlag = true;
			} else {
				
			}
		  },
		  error: function(xhr, type){
			return;
		  }
		});
	});
	//con.css('height', h+"px");
	form.css('height', conH+'px');
	new Zoom(form, zoom.closest('.z'), conB);
	//page4
	page4.find('.return').on(click, function(e){
		e.preventDefault();
		curPage.css('display', 'none');
		curPage = page1;
		curPage.css('display', 'block');
	});
	

  
	
});

document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
   // 发送给好友
  WeixinJSBridge.on('menu:share:appmessage', function (argv) {
      WeixinJSBridge.invoke('sendAppMessage', {
          "img_url": address+"/img/640.png",
          "img_width": "640",
          "img_height": "640",
          "link": 'http://activity.bb.bbwc.cn/html/iBBSepShare/',
          "title": "选送一张智趣卡片，享受彭博商业周刊APP免费畅读！",
          "desc": "选送一张智趣卡片，享受彭博商业周刊APP免费畅读！"
      }, function (res) {
          // 回调
      })
  });
 
  // 分享到朋友圈
  WeixinJSBridge.on('menu:share:timeline', function (argv) {
      WeixinJSBridge.invoke('shareTimeline', {
          "img_url": address+"/img/640.png",
          "img_width": "640",
          "img_height": "640",
          "link": 'http://activity.bb.bbwc.cn/html/iBBSepShare/',
          "desc": "选送一张智趣卡片，享受彭博商业周刊APP免费畅读！",
          "title": "选送一张智趣卡片，享受彭博商业周刊APP免费畅读！"
      }, function (res) {
          // 回调
      });
  });
  // 分享到微卿
  WeixinJSBridge.on('menu:share:weibo', function (argv) {
      WeixinJSBridge.invoke('shareWeibo', {
          "img_url": address+"/img/640.png",
          "content": "选送一张智趣卡片，享受彭博商业周刊APP免费畅读！",
          "url": location.href,
      }, function (res) {
          // 回调
      });
  });
			
}, false);