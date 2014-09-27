define(function(require, exports, module) {
	var PreLoad = function (resultDom, imgArr, opt) {
		if(!imgArr || !Array.isArray(imgArr) || !imgArr.length) {
			return;
		}
		this.result = resultDom;
		this.items = imgArr;
		this.prefix = opt.prefix || "";
		this.callback = opt.callback || false;
	};
	PreLoad.prototype.load = function () {
		var i = 0;
		var k = this;
		var arr = k.items;
		var len = arr.length;
		var cb = k.callback;
		arr.forEach(function (imgSrc) {
			var s = new Image;
			s.onload = s.onerror = s.onabort = function () {
				if(++i === len && typeof cb === 'function') {
					cb.call(k);
				}
				k.result.innerText = Math.floor(100*i/len) + "%";
			}
			s.src = k.prefix + imgSrc;
		});
	};
	module.exports = PreLoad;
});

