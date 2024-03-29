/*! perfect-scrollbar - v0.4.9
* http://noraesae.github.com/perfect-scrollbar/
* Copyright (c) 2014 Hyeonje Jun; Licensed MIT */
(function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery"],e):"object"==typeof exports?e(require("jquery")):e(jQuery)})(function(e){"use strict";var t={wheelSpeed:10,wheelPropagation:!1,minScrollbarLength:null,useBothWheelAxes:!1,useKeyboard:!0,suppressScrollX:!1,suppressScrollY:!1,scrollXMarginOffset:0,scrollYMarginOffset:0,includePadding:!1},n=function(){var e=0;return function(){var t=e;return e+=1,".perfect-scrollbar-"+t}}();e.fn.perfectScrollbar=function(o,r){return this.each(function(){var l=e.extend(!0,{},t),a=e(this);if("object"==typeof o?e.extend(!0,l,o):r=o,"update"===r)return a.data("perfect-scrollbar-update")&&a.data("perfect-scrollbar-update")(),a;if("destroy"===r)return a.data("perfect-scrollbar-destroy")&&a.data("perfect-scrollbar-destroy")(),a;if(a.data("perfect-scrollbar"))return a.data("perfect-scrollbar");a.addClass("ps-container");var s,i,c,u,d,p,f,h,v,g,b=e("<div class='ps-scrollbar-x-rail'></div>").appendTo(a),m=e("<div class='ps-scrollbar-y-rail'></div>").appendTo(a),w=e("<div class='ps-scrollbar-x'></div>").appendTo(b),T=e("<div class='ps-scrollbar-y'></div>").appendTo(m),y=parseInt(b.css("bottom"),10),L=parseInt(m.css("right"),10),S=n(),x=function(e,t){var n=e+t,o=u-v;g=0>n?0:n>o?o:n;var r=parseInt(g*(p-u)/(u-v),10);a.scrollTop(r),b.css({bottom:y-r})},M=function(e,t){var n=e+t,o=c-f;h=0>n?0:n>o?o:n;var r=parseInt(h*(d-c)/(c-f),10);a.scrollLeft(r),m.css({right:L-r})},P=function(e){return l.minScrollbarLength&&(e=Math.max(e,l.minScrollbarLength)),e},X=function(){b.css({left:a.scrollLeft(),bottom:y-a.scrollTop(),width:c,display:s?"inherit":"none"}),m.css({top:a.scrollTop(),right:L-a.scrollLeft(),height:u,display:i?"inherit":"none"}),w.css({left:h,width:f}),T.css({top:g,height:v})},D=function(){c=l.includePadding?a.innerWidth():a.width(),u=l.includePadding?a.innerHeight():a.height(),d=a.prop("scrollWidth"),p=a.prop("scrollHeight"),!l.suppressScrollX&&d>c+l.scrollXMarginOffset?(s=!0,f=P(parseInt(c*c/d,10)),h=parseInt(a.scrollLeft()*(c-f)/(d-c),10)):(s=!1,f=0,h=0,a.scrollLeft(0)),!l.suppressScrollY&&p>u+l.scrollYMarginOffset?(i=!0,v=P(parseInt(u*u/p,10)),g=parseInt(a.scrollTop()*(u-v)/(p-u),10)):(i=!1,v=0,g=0,a.scrollTop(0)),g>=u-v&&(g=u-v),h>=c-f&&(h=c-f),X()},I=function(){var t,n;w.bind("mousedown"+S,function(e){n=e.pageX,t=w.position().left,b.addClass("in-scrolling"),e.stopPropagation(),e.preventDefault()}),e(document).bind("mousemove"+S,function(e){b.hasClass("in-scrolling")&&(M(t,e.pageX-n),e.stopPropagation(),e.preventDefault())}),e(document).bind("mouseup"+S,function(){b.hasClass("in-scrolling")&&b.removeClass("in-scrolling")}),t=n=null},Y=function(){var t,n;T.bind("mousedown"+S,function(e){n=e.pageY,t=T.position().top,m.addClass("in-scrolling"),e.stopPropagation(),e.preventDefault()}),e(document).bind("mousemove"+S,function(e){m.hasClass("in-scrolling")&&(x(t,e.pageY-n),e.stopPropagation(),e.preventDefault())}),e(document).bind("mouseup"+S,function(){m.hasClass("in-scrolling")&&m.removeClass("in-scrolling")}),t=n=null},k=function(e,t){var n=a.scrollTop();if(0===e){if(!i)return!1;if(0===n&&t>0||n>=p-u&&0>t)return!l.wheelPropagation}var o=a.scrollLeft();if(0===t){if(!s)return!1;if(0===o&&0>e||o>=d-c&&e>0)return!l.wheelPropagation}return!0},C=function(){l.wheelSpeed/=10;var e=!1;a.bind("mousewheel"+S,function(t,n,o,r){var c=t.deltaX*t.deltaFactor||o,u=t.deltaY*t.deltaFactor||r;e=!1,l.useBothWheelAxes?i&&!s?(u?a.scrollTop(a.scrollTop()-u*l.wheelSpeed):a.scrollTop(a.scrollTop()+c*l.wheelSpeed),e=!0):s&&!i&&(c?a.scrollLeft(a.scrollLeft()+c*l.wheelSpeed):a.scrollLeft(a.scrollLeft()-u*l.wheelSpeed),e=!0):(a.scrollTop(a.scrollTop()-u*l.wheelSpeed),a.scrollLeft(a.scrollLeft()+c*l.wheelSpeed)),D(),e=e||k(c,u),e&&(t.stopPropagation(),t.preventDefault())}),a.bind("MozMousePixelScroll"+S,function(t){e&&t.preventDefault()})},j=function(){var t=!1;a.bind("mouseenter"+S,function(){t=!0}),a.bind("mouseleave"+S,function(){t=!1});var n=!1;e(document).bind("keydown"+S,function(o){if(t&&!e(document.activeElement).is(":input,[contenteditable]")){var r=0,l=0;switch(o.which){case 37:r=-30;break;case 38:l=30;break;case 39:r=30;break;case 40:l=-30;break;case 33:l=90;break;case 32:case 34:l=-90;break;case 35:l=-u;break;case 36:l=u;break;default:return}a.scrollTop(a.scrollTop()-l),a.scrollLeft(a.scrollLeft()+r),n=k(r,l),n&&o.preventDefault()}})},O=function(){var e=function(e){e.stopPropagation()};T.bind("click"+S,e),m.bind("click"+S,function(e){var t=parseInt(v/2,10),n=e.pageY-m.offset().top-t,o=u-v,r=n/o;0>r?r=0:r>1&&(r=1),a.scrollTop((p-u)*r)}),w.bind("click"+S,e),b.bind("click"+S,function(e){var t=parseInt(f/2,10),n=e.pageX-b.offset().left-t,o=c-f,r=n/o;0>r?r=0:r>1&&(r=1),a.scrollLeft((d-c)*r)})},E=function(){var t=function(e,t){a.scrollTop(a.scrollTop()-t),a.scrollLeft(a.scrollLeft()-e),D()},n={},o=0,r={},l=null,s=!1;e(window).bind("touchstart"+S,function(){s=!0}),e(window).bind("touchend"+S,function(){s=!1}),a.bind("touchstart"+S,function(e){var t=e.originalEvent.targetTouches[0];n.pageX=t.pageX,n.pageY=t.pageY,o=(new Date).getTime(),null!==l&&clearInterval(l),e.stopPropagation()}),a.bind("touchmove"+S,function(e){if(!s&&1===e.originalEvent.targetTouches.length){var l=e.originalEvent.targetTouches[0],a={};a.pageX=l.pageX,a.pageY=l.pageY;var i=a.pageX-n.pageX,c=a.pageY-n.pageY;t(i,c),n=a;var u=(new Date).getTime(),d=u-o;d>0&&(r.x=i/d,r.y=c/d,o=u),e.preventDefault()}}),a.bind("touchend"+S,function(){clearInterval(l),l=setInterval(function(){return.01>Math.abs(r.x)&&.01>Math.abs(r.y)?(clearInterval(l),void 0):(t(30*r.x,30*r.y),r.x*=.8,r.y*=.8,void 0)},10)})},H=function(){a.bind("scroll"+S,function(){D()})},A=function(){a.unbind(S),e(window).unbind(S),e(document).unbind(S),a.data("perfect-scrollbar",null),a.data("perfect-scrollbar-update",null),a.data("perfect-scrollbar-destroy",null),w.remove(),T.remove(),b.remove(),m.remove(),w=T=c=u=d=p=f=h=y=v=g=L=null},W=function(t){a.addClass("ie").addClass("ie"+t);var n=function(){var t=function(){e(this).addClass("hover")},n=function(){e(this).removeClass("hover")};a.bind("mouseenter"+S,t).bind("mouseleave"+S,n),b.bind("mouseenter"+S,t).bind("mouseleave"+S,n),m.bind("mouseenter"+S,t).bind("mouseleave"+S,n),w.bind("mouseenter"+S,t).bind("mouseleave"+S,n),T.bind("mouseenter"+S,t).bind("mouseleave"+S,n)},o=function(){X=function(){w.css({left:h+a.scrollLeft(),bottom:y,width:f}),T.css({top:g+a.scrollTop(),right:L,height:v}),w.hide().show(),T.hide().show()}};6===t&&(n(),o())},q="ontouchstart"in window||window.DocumentTouch&&document instanceof window.DocumentTouch,F=function(){var e=navigator.userAgent.toLowerCase().match(/(msie) ([\w.]+)/);e&&"msie"===e[1]&&W(parseInt(e[2],10)),D(),H(),I(),Y(),O(),q&&E(),a.mousewheel&&C(),l.useKeyboard&&j(),a.data("perfect-scrollbar",a),a.data("perfect-scrollbar-update",D),a.data("perfect-scrollbar-destroy",A)};return F(),a})}}),function(e){"function"==typeof define&&define.amd?define(["jquery"],e):"object"==typeof exports?module.exports=e:e(jQuery)}(function(e){function t(t){var a=t||window.event,s=i.call(arguments,1),c=0,u=0,d=0,p=0;if(t=e.event.fix(a),t.type="mousewheel","detail"in a&&(d=-1*a.detail),"wheelDelta"in a&&(d=a.wheelDelta),"wheelDeltaY"in a&&(d=a.wheelDeltaY),"wheelDeltaX"in a&&(u=-1*a.wheelDeltaX),"axis"in a&&a.axis===a.HORIZONTAL_AXIS&&(u=-1*d,d=0),c=0===d?u:d,"deltaY"in a&&(d=-1*a.deltaY,c=d),"deltaX"in a&&(u=a.deltaX,0===d&&(c=-1*u)),0!==d||0!==u){if(1===a.deltaMode){var f=e.data(this,"mousewheel-line-height");c*=f,d*=f,u*=f}else if(2===a.deltaMode){var h=e.data(this,"mousewheel-page-height");c*=h,d*=h,u*=h}return p=Math.max(Math.abs(d),Math.abs(u)),(!l||l>p)&&(l=p,o(a,p)&&(l/=40)),o(a,p)&&(c/=40,u/=40,d/=40),c=Math[c>=1?"floor":"ceil"](c/l),u=Math[u>=1?"floor":"ceil"](u/l),d=Math[d>=1?"floor":"ceil"](d/l),t.deltaX=u,t.deltaY=d,t.deltaFactor=l,t.deltaMode=0,s.unshift(t,c,u,d),r&&clearTimeout(r),r=setTimeout(n,200),(e.event.dispatch||e.event.handle).apply(this,s)}}function n(){l=null}function o(e,t){return u.settings.adjustOldDeltas&&"mousewheel"===e.type&&0===t%120}var r,l,a=["wheel","mousewheel","DOMMouseScroll","MozMousePixelScroll"],s="onwheel"in document||document.documentMode>=9?["wheel"]:["mousewheel","DomMouseScroll","MozMousePixelScroll"],i=Array.prototype.slice;if(e.event.fixHooks)for(var c=a.length;c;)e.event.fixHooks[a[--c]]=e.event.mouseHooks;var u=e.event.special.mousewheel={version:"3.1.9",setup:function(){if(this.addEventListener)for(var n=s.length;n;)this.addEventListener(s[--n],t,!1);else this.onmousewheel=t;e.data(this,"mousewheel-line-height",u.getLineHeight(this)),e.data(this,"mousewheel-page-height",u.getPageHeight(this))},teardown:function(){if(this.removeEventListener)for(var e=s.length;e;)this.removeEventListener(s[--e],t,!1);else this.onmousewheel=null},getLineHeight:function(t){return parseInt(e(t)["offsetParent"in e.fn?"offsetParent":"parent"]().css("fontSize"),10)},getPageHeight:function(t){return e(t).height()},settings:{adjustOldDeltas:!0}};e.fn.extend({mousewheel:function(e){return e?this.bind("mousewheel",e):this.trigger("mousewheel")},unmousewheel:function(e){return this.unbind("mousewheel",e)}})});

activity_offset = 0;
message_offset = 0;
activity_request = false;
message_request = false;

$(document).ready(function(){
	"use strict";
	$('#activitylist').one('click', function(e){
		e.preventDefault();
		cs_load_activity();
	});
	
	$('#messagelist').one('click', function(e){
		e.preventDefault();
		cs_load_messages();		
	});
	
	$('.mark-activity').click(function(){
		$.ajax({
			type:'GET',
			url : ajax_url,
			data: {
				action: 'mark_all_activity'
			},
			success: function (response) {				
				$('.activity-dropdown-list .append .event-content').addClass('read');
				$('#activitylist > span').remove();
			},
		});
	});	
	$('.mark-messages').click(function(){
		$.ajax({
			type:'GET',
			url : ajax_url,
			data: {
				action: 'mark_all_messages'
			},
			success: function (response) {				
				$('#message-dropdown-list .append .event-content').addClass('read');
				$('#messagelist > span').remove();
			},
		});
	});
	
	$('.activity-bar .append').on('scroll', cs_chk_activity_scroll);
	$('.message-bar .append').on('scroll', cs_chk_message_scroll);

	cs_user_activity_count();
	
	window.setInterval(function(){
	  cs_user_activity_count();
	}, 50000);

});

function cs_load_activity(){	
	activity_request = true;
	$.ajax({
		type:'GET',
		url : ajax_url,
		data: {
			action: 'activitylist',
			offset: activity_offset
		},
		dataType: 'html',
		context: this,
		success: function (response) {
			if(response){
				$(".activity-dropdown-list .append").perfectScrollbar('destroy');
				$('.activity-dropdown-list .append .ajax-list').append(response);	
				$('.activity-dropdown-list .loading').hide();
				$('.activity-dropdown-list .no-activity').hide();					
				$('.activity-dropdown-list .append').perfectScrollbar();
				activity_offset= activity_offset+1;
				activity_request = false;
			}else{
				$('.activity-dropdown-list .loading').hide();
				$('.activity-dropdown-list .no-activity').show();
				activity_request = false;
			}
		},
	});

}

function cs_load_messages(){	
	message_request = true;
	$.ajax({
		type:'GET',
		url : ajax_url,
		data: {
			action: 'messagelist',
			offset:message_offset
		},
		dataType: 'html',
		context: this,
		success: function (response) {
			if(response){
				$(".message-dropdown-list .append").perfectScrollbar('destroy');
				$('.message-dropdown-list .append .ajax-list').append(response);					
				$('.message-dropdown-list .loading').hide();
				$('.message-dropdown-list .no-activity').hide();
				$(".message-dropdown-list .append").perfectScrollbar();
				message_offset= message_offset+1;
				message_request = false;
			}else{
				$('.message-dropdown-list .loading').hide();
				$('.message-dropdown-list .no-activity').show();
				message_request = false;
			}
		},
	});

}

function cs_user_activity_count(){
	$.ajax({
		type:'GET',
		url : ajax_url,
		data: {
			action: 'activity_count'
		},
		success: function (response) {
			if(response > 0)
				$('#activitylist').html('<span>'+response+'</span>');				
		},
	});
	$.ajax({
		type:'GET',
		url : ajax_url,
		data: {
			action: 'messages_count'
		},
		success: function (response) {
			if(response > 0)
				$('#messagelist').html('<span>'+response+'</span>');				
		},
	});
}


function cs_chk_activity_scroll(e){
	if(($('.activity-bar .append .no-more-activity').length ==0) && !activity_request){
		var elem = $(e.currentTarget);		
		if (elem[0].scrollHeight - elem.scrollTop() == elem.outerHeight()){
			cs_load_activity();
		}
	}
}


function cs_chk_message_scroll(e){
	if(($('.message-bar .append .no-more-activity').length ==0) && !message_request){
		var elem = $(e.currentTarget);		
		if (elem[0].scrollHeight - elem.scrollTop() == elem.outerHeight()){
			cs_load_messages();
		}
	}
}