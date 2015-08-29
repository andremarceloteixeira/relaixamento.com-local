/**
 * @version $Id: jquery.djmegamenu.js 25 2014-08-21 13:09:46Z szymon $
 * @package DJ-MegaMenu
 * @copyright Copyright (C) 2013 DJ-Extensions.com, All rights reserved.
 * @license DJ-Extensions.com Proprietary Use License
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
 */
(function($){var j=this.DJMegaMenu=function(a,b){this.options={delay:500,animIn:'fadeIn',animOut:'fadeOut',animSpeed:'normal',duration:450,wrap:null,direction:'ltr',event:'mouseenter',touch:(('ontouchstart'in window)||(navigator.MaxTouchPoints>0)||(navigator.msMaxTouchPoints>0))};this.init(a,b)};j.prototype.init=function(c,d){var e=this;jQuery.extend(e.options,d);if(!c.length)return;e.options.direction=window.getComputedStyle(document.body).getPropertyValue('direction');switch(e.options.animSpeed){case'fast':e.options.duration=250;break;case'slow':e.options.duration=650;break}c.addClass(e.options.animSpeed);var f=c.find('li.dj-up');e.kids=[];if(!e.options.wrap)e.options.wrap=c;else e.options.wrap=$('#'+e.options.wrap);f.each(function(a){var b=$(this);e.kids[a]=new k(b,0,e,e.options)});if(e.options.fixed&&!e.options.touch){$(window).load(e.makeSticky.bind(e,c))}};j.prototype.makeSticky=function(a){var b=this;b.sticky=false;var c=$('<div id="'+a.attr('id')+'sticky"></div>');c.addClass('dj-megamenu');c.addClass('dj-megamenu-sticky');c.css({position:'fixed',top:b.options.offset,left:0,width:'100%'});var d=a.offset().top-b.options.offset;var e=a.clone();e.attr('id',a.attr('id')+'placeholder');e.css('opacity',0);var f=b.options.direction=='rtl'?'right':'left';$(window).scroll(b.scroll.bind(b,c,a,e,d,f,false));$(window).resize(b.scroll.bind(b,c,a,e,d,f,true))};j.prototype.scroll=function(a,b,c,d,e,f){var g=this;if($(window).scrollTop()>d){if(!g.sticky){var h=b.offset();var i=e=='left'?h.left:$(window).width()-h.left-b.outerWidth();b.css(e,i);c.insertBefore(b);b.wrap(a);g.sticky=true}else if(f){var h=c.offset();var i=e=='left'?h.left:$(window).width()-h.left-b.outerWidth();b.css(e,i)}}else if(g.sticky){b.unwrap();c.detach();b.css(e,'');g.sticky=false}};var k=function(a,b,c,d){this.options={};this.init(a,b,c,d)};k.prototype.init=function(a,b,c,d){var f=this;jQuery.extend(f.options,d);f.menu=a;f.level=b;f.parent=c;f.timer=null;f.sub=a.find('> .dj-subwrap').first();var g='mouseenter';if(f.options.touch||f.options.event=='click_all'){g='click';var h=a.find('> a').first();if(h.length){if(a.hasClass('separator'))h.css('cursor','pointer');h.on('click',function(e){if(f.sub.length&&!f.menu.hasClass('hover'))e.preventDefault()})}}else if(f.options.event=='click'&&a.hasClass('separator')){var h=a.find('> a').first();if(h.length)h.css('cursor','pointer');g='click'}f.menu.on(g,f.showSub.bind(f));f.menu.on('mouseleave',f.hideSub.bind(f));if(f.sub.length){f.kids=[];f.initKids()}};k.prototype.showSub=function(){var a=this;clearTimeout(a.timer);if(a.menu.hasClass('hover')&&!a.sub.hasClass(a.options.animOut)){return}clearTimeout(a.animTimer);a.menu.addClass('hover');if(a.sub.length&&!a.DirDone)a.checkDir();a.hideOther();if(a.sub.length){a.sub.removeClass(a.options.animOut);a.sub.addClass(a.options.animIn)}};k.prototype.hideSub=function(){var a=this;if(a.sub.length){a.timer=setTimeout(function(){a.sub.removeClass(a.options.animIn);a.sub.addClass(a.options.animOut);a.animTimer=setTimeout(function(){a.menu.removeClass('hover')},a.options.duration)},a.options.delay)}else{a.menu.removeClass('hover')}};k.prototype.checkDir=function(){var a=this;a.DirDone=true;var b=a.sub.offset();var c=a.options.wrap.offset();if(a.options.wrap.hasClass('dj-megamenu')){var d=$('#'+a.options.wrap.get('id')+'placeholder');if(d.length)c=d.offset()}if(a.options.direction=='ltr'){var e=b.left+a.sub.outerWidth()-a.options.wrap.outerWidth()-c.left;if(e>0){if(a.level){a.sub.css('right',a.menu.outerWidth());a.sub.css('left','auto')}else{a.sub.css('margin-left',-e)}}}else if(a.options.direction=='rtl'){var e=b.left-c.left;if(e<0){if(a.level){a.sub.css('left',a.menu.outerWidth());a.sub.css('right','auto')}else{a.sub.css('margin-right',e)}}}};k.prototype.initKids=function(){var c=this;var d=c.sub.find('> .dj-subwrap-in > .dj-subcol > ul.dj-submenu > li');d.each(function(a){var b=$(this);c.kids[a]=new k(b,c.level+1,c,c.options)})};k.prototype.hideOther=function(){var c=this;$.each(c.parent.kids,function(a,b){if(b.menu.hasClass('hover')&&b!=c){if(b.sub.length){b.hideOtherSub();b.sub.removeClass(b.options.animIn);b.sub.addClass(b.options.animOut);b.animTimer=setTimeout(function(){b.menu.removeClass('hover')},c.options.duration)}else{b.menu.removeClass('hover')}}})};k.prototype.hideOtherSub=function(){var c=this;$.each(c.kids,function(a,b){if(b.sub.length){b.hideOtherSub();b.sub.removeClass(b.options.animIn);b.sub.removeClass(b.options.animOut)}b.menu.removeClass('hover')})}})(jQuery);