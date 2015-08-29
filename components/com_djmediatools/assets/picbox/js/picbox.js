/*
	Picbox v1.3 - adapted by DJ-Extensions.com (MT 1.4.5 compat)
	(c) 2010 Ben Kay <http://bunnyfire.co.uk>

	Based on code from Slimbox v1.7 - The ultimate lightweight Lightbox clone
	(c) 2007-2009 Christophe Beyls <http://www.digitalia.be>
	MIT-style license.
*/

(function($){

var Picbox=function(e){function P(){var e=t.getScroll(),n=t.getSize();l=t.getWidth()/2;c=t.getHeight()/2;if(r){l=l+e.x;c=c+e.y;S.setStyles({left:e.x,top:e.y,width:n.x,height:n.y})}T.setStyles({top:Math.max(0,c),left:Math.max(0,l),width:1,height:1})}function H(e){if(i.hideFlash){["object","embed"].forEach(function(t){Array.forEach(document.getElementsByTagName(t),function(t){if(e)t._picbox=t.style.visibility;t.style.visibility=e?"hidden":t._picbox})})}S.style.display="";var t=e?"addEvent":"removeEvent";document[t]("keydown",B);document[t]("mousewheel",V);document[t]("mousemove",j)}function B(e){var t=e.code;return i.closeKeys.contains(t)?K():i.nextKeys.contains(t)?R():i.previousKeys.contains(t)?q():false}function j(){F([k,N,zoomBtn,C])}function F(e,t){clearTimeout(g);$$(e).fade("in");g=setTimeout(function(){$$(e).fade("out")},i.controlsFadeDelay)}function I(e){var t=1==e?"removeEvent":"addEvent";document[t]("mousemove",j);clearTimeout(g)}function q(){return U(a,true)}function R(){return U(f,true)}function U(t,n){if(t>=0){o=t;u=s[t][0];a=(o||(i.loop?s.length:0))-1;f=(o+1)%s.length||(i.loop?0:-1);J();S.className="pbLoading";T.setStyle("display","none");if(!s[o][1])e(L).fade("hide");else e(L).set("html",s[o][1]).fade("show");A.set("html",(s.length>1&&i.counterText||"").replace(/{x}/,o+1).replace(/{y}/,s.length));if(a>=0){w.src=s[a][0];N.removeClass(D)}if(f>=0){E.src=s[f][0];C.removeClass(D)}k.setStyle("display","");b=new Image;b.onload=function(){z(n)};b.src=u}return false}function z(e){X();var n=t.getWidth()-i.margins,r=t.getHeight()-i.margins,s=1;if(b.width>n||b.height>r){s=Math.min(n/b.width,r/b.height);zoomBtn.removeClass(D);y=false}else{zoomBtn.addClass(D);y=true}d=v=s;W(s,e);T.set("src",u);T.setStyle("display","");S.className="";F([k],[N,zoomBtn,C])}function W(e,t,n){var r=e/d;h=l-(l-h)*r;p=c-(c-p)*r;d=e;var i=b.width*e,s=b.height*e,o=h-i/2,u=p-s/2;var a=t?"set":"start";var f=0==e?function(){T.setStyle("display","none")}:null;M[a]({width:i,height:s,top:u,left:o}).chain(f);return false}function X(){h=l;p=c}function V(e){zoomBtn.addClass(_);var t=d+e.wheel*(d/10);return W(t)}function $(){if(d==v&&Math.abs(h-l+p-c)<2&&!y){zoomBtn.addClass(_);return W(1)}else{zoomBtn.removeClass(_);X();return W(v)}}function J(){b.onload={};b.src=w.src=E.src=u;M.cancel();$$(N,C).addClass(D);zoomBtn.removeClass(_)}function K(){if(o>=0){J();o=a=f=-1;W(0);H();k.setStyle("display","none");O.cancel().chain(function(){S.setStyle("display","none")}).start(0)}return false}var t=window,n=Browser.ie&&Browser.version<=6,r,i,s,o=-1,u,a,f,l,c,h,p,d,v,m,g,y,b={},w=new Image,E=new Image,S,x,T,N,C,k,L,A,O,M,_="pbzoomed",D="pbgreyed";t.addEvent("domready",function(){e(document.body).adopt($$(S=(new Element("div",{id:"pbOverlay",events:{click:K}})).adopt(x=new Element("div",{id:"pbCloseBtn"})),T=new Element("img",{id:"pbImage",events:{dblclick:$}}),k=(new Element("div",{id:"pbBottom",events:{mouseover:function(){I(1)},mouseout:I}})).adopt(L=new Element("div",{id:"pbCaption"}),A=new Element("div",{id:"pbNumber"}),(new Element("div",{id:"pbNav"})).adopt(N=new Element("a",{id:"pbPrevBtn",href:"#",events:{click:q}}),zoomBtn=new Element("a",{id:"pbZoomBtn",href:"#",events:{click:$}}),C=new Element("a",{id:"pbNextBtn",href:"#",events:{click:R}})))).setStyle("display","none"));r=n||S.currentStyle&&S.currentStyle.position!="fixed";if(r){$$(S,x,T,k).setStyle("position","absolute")}T.tinyDrag(function(){var t=Browser.ie&&(Browser.version==7||Browser.version==8)?S:undefined;var n=T.getPosition(t);h=n.x+T.offsetWidth/2;p=n.y+T.offsetHeight/2;e(zoomBtn).addClass(_)})});Element.implement({picbox:function(e,t){$$(this).picbox(e,t);return this}});Elements.implement({picbox:function(e,t,n){t=t||function(e){return[e.href,e.title]};n=n||function(){return true};var r=this;r.removeEvents("click").addEvent("click",function(){var i=r.filter(n,this);return Picbox.open(i.map(t),i.indexOf(this),e)});return r}});return{open:function(e,t,n){i=Object.append({loop:false,overlayOpacity:.8,overlayFadeDuration:200,resizeDuration:300,resizeEasing:Fx.Transitions.Sine.easeOut,controlsFadeDelay:3e3,counterText:false,hideFlash:true,closeKeys:[27,88,67],previousKeys:[37,80],nextKeys:[39,78],margins:0},n||{});O=new Fx.Tween(S,{property:"opacity",duration:i.overlayFadeDuration});M=new Fx.Morph(T,Object.append({duration:i.resizeDuration,link:"cancel"},i.resizeTransition?{transition:i.resizeTransition}:{}));if(typeof e=="string"){e=[[e,t]];t=0}O.set(0).start(i.overlayOpacity);P();H(1);s=e;i.loop=i.loop&&s.length>1;return U(t)}}}(document.id);(function(e){Element.implement({tinyDrag:function(e){function u(e){var s=e.page.x,u=e.page.y;if(r){i.setStyles({left:s-t.x,top:u-t.y})}else{if(o(s-n.x)>1||o(u-n.y)>1)r=true}return false}function a(){s.removeEvent("mousemove",u).removeEvent("mouseup");r&&e&&e()}var t,n,r,i=this,s=document,o=Math.abs;this.addEvent("mousedown",function(e){var i=this.getPosition();r=false;n={x:e.page.x,y:e.page.y};t={x:n.x-i.x,y:n.y-i.y};s.addEvent("mousemove",u).addEvent("mouseup",a);return false});return this}})})(document.id)

// AUTOLOAD CODE BLOCK (MAY BE CHANGED OR REMOVED)
Picbox.scanPage = function() {
	$$(document.links).filter(function(el) {
		return el.rel && el.rel.test(/^lightbox/i);
	}).picbox({/* Put custom options here */}, null, function(el) {
		return (this == el) || ((this.rel.length > 8) && (this.rel == el.rel));
	});
};
//if (!/android|iphone|ipod|series60|symbian|windows ce|blackberry/i.test(navigator.userAgent)) {
	window.addEvent("domready", Picbox.scanPage);
//}
})(document.id);