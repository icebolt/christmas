define(function(require, exports, module) {
	function get(e) {
		return document.querySelector(e);
	}
	function gets(e) {
		return document.querySelectorAll(e);
	}
	exports.get = get;
	exports.gets = gets;
});

