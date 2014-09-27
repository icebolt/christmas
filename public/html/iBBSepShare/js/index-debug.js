define("js/index-debug", [ "./pages-debug", "./tools-debug" ], function(require, exports, module) {
    var Page = require("./pages-debug");
    var tool = require("./tools-debug");
    document.addEventListener("touchmove", function(e) {
        e.preventDefault();
    });
    var pageDOM = tool.get(".focus");
    new Page(pageDOM, "X");
});

define("js/pages-debug", [], function(require, exports, module) {
    var Pages = function(el, nav, type) {
        this.el = el;
        this.swipe = type || "X";
        this.nav = nav;
        this.flag;
        this.current = 0;
        this.w = 0;
        this.move;
        this.interval = 400;
        this.pageX;
        this.pageY;
        this.curDom;
        this.fangxiang = "";
        this.left = 0;
        this.count = 0;
        this.init();
    };
    Pages.prototype = {
        init: function(index) {
            var p = this.el.parentNode;
            var lis = this.el.getElementsByTagName("li");
            var len = lis.length;
            this.count = len;
            this.w = this.getw(p);
            this.tw = 0;
            for (var i = 0; i < len; i++) {
                lis.item(i).style.width = this.w + "px";
                this.tw += this.w;
            }
            this.el.style.width = this.tw + "px";
            this.events();
            this.automove();
        },
        getw: function(e) {
            return Number(window.getComputedStyle(e, "").width.replace("px", ""));
        },
        automove: function() {
            var k = this;
            k.flag = setInterval(function() {
                k.next.call(k);
            }, 4e3);
        },
        clear: function() {
            clearInterval(this.flag);
            this.flag = null;
        },
        events: function() {
            var k = this;
            if ("ontouchstart" in window) {
                //window.addEventListener("resize orientationchange", k.resize.bind(k), false);
                "touchstart touchmove touchend touchcancel".split(" ").forEach(function(item) {
                    k.el.addEventListener(item, k[item].bind(k), false);
                });
            } else if (window.PointerEvent) {
                "pointerdown pointermove pointerup pointercancel".split(" ").forEach(function(item) {
                    k.el.addEventListener(item, k[item].bind(k), false);
                });
            } else if (window.MSPointerEvent) {
                "MSPointerDown MSPointerMove MSPointerUp MPointerCancel".split(" ").forEach(function(item) {
                    k.el.addEventListener(item, k[item].bind(k), false);
                });
            }
        },
        touchstart: function(e) {
            var t = e.touches[0];
            this.clear();
            this.move = 0;
            this.pageX = t.pageX;
            this.pageY = t.pageY;
            if (window.PointerEvent || window.MSPointerEvent) {
                var sleft = this.el.style.msTransform || this.el.style.transform;
                this.left = this.getLeft(sleft, "x");
            } else {
                this.left = this.getLeft(this.el.style.webkitTransform, "x");
            }
            e.preventDefault();
        },
        touchmove: function(e) {
            var t = e.touches[0];
            var px = t.pageX;
            var py = t.pageY;
            var lenX = px - this.pageX;
            var lenY = py - this.pageY;
            e.preventDefault();
            if (Math.abs(lenY) > Math.abs(lenX)) {
                return;
            }
            this.move = lenX;
            if (lenX > 4) {
                this.fangxiang = "right";
            } else if (lenX < -4) {
                this.fangxiang = "left";
            }
            this.setX(this.el, this.left + lenX, 0);
        },
        touchend: function(e) {
            e.preventDefault();
            var k = this;
            if (k.fangxiang == "right") {
                if (k.current == 0) {
                    k.setX(k.el, 0, k.interval);
                } else {
                    k.prev();
                }
            } else if (k.fangxiang == "left") {
                if (k.current == k.count - 1) {
                    k.setX(k.el, -(k.w * (k.count - 1)), k.interval);
                } else {
                    k.next();
                }
            } else {
                this.setX(this.el, this.left, 0);
            }
            k.automove.call(k);
        },
        touchcancel: function(e) {
            e.preventDefault();
            var k = this;
            if (k.fangxiang == "right") {
                if (k.current == 0) {
                    k.setX(k.el, 0, k.interval);
                } else {
                    k.prev();
                }
            } else if (k.fangxiang == "left") {
                if (k.current == k.count - 1) {
                    k.setX(k.el, -(k.w * (k.count - 1)), k.interval);
                } else {
                    k.next();
                }
            } else {
                this.setX(this.el, this.left, 0);
            }
            k.automove.call(k);
        },
        setX: function(el, len, time) {
            el.style.webkitTransitionDuration = time + "ms";
            el.style.msTransitionDuration = time + "ms";
            el.style.transitionDuration = time + "ms";
            el.style.webkitTransform = "translate3d(" + len + "px,0,0)";
            el.style.msTransform = "translate3d(" + len + "px,0,0)";
            el.style.transform = "translate3d(" + len + "px,0,0)";
        },
        next: function() {
            this.go(this.current + 1);
        },
        prev: function() {
            this.go(this.current - 1);
        },
        go: function(idx) {
            var len = 0;
            if (idx === this.current) {
                return;
            }
            if (idx >= this.count) {
                idx = 0;
            }
            if (idx < 0) {
                idx = this.count - 1;
            }
            this.current = idx;
            len = -(idx * this.w);
            this.setX(this.el, len, this.interval);
        },
        getLeft: function(c, f) {
            var a = 0, d = /([0-9-]+)+(?![3d]\()/gi, e = c.toString().match(d);
            if (!e) {
                return a;
            }
            if (e.length) {
                var b = f == "x" ? 0 : f == "y" ? 1 : 2;
                a = parseFloat(e[b]);
            }
            return a;
        }
    };
    module.exports = Pages;
});

define("js/tools-debug", [], function(require, exports, module) {
    function get(e) {
        return document.querySelector(e);
    }
    function gets(e) {
        return document.querySelectorAll(e);
    }
    exports.get = get;
    exports.gets = gets;
});
