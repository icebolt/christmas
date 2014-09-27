
define( function(require, exports, module) {
	var Page = require('./pages');
	var tool = require('./tools');
	document.addEventListener("touchmove", function(e) {e.preventDefault()});
	var pageDOM = tool.get('.focus');
	new Page(pageDOM, 'X');
	
});