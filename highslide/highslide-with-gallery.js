/******************************************************************************
Name:    Highslide JS
Version: 4.0.7 (Oct 9 2008)
Config:  default +slideshow +positioning +transitions
Author:  Torstein H�nsi
Support: http://highslide.com/support

Licence:
Highslide JS is licensed under a Creative Commons Attribution-NonCommercial 2.5
License (http://creativecommons.org/licenses/by-nc/2.5/).

You are free:
	* to copy, distribute, display, and perform the work
	* to make derivative works

Under the following conditions:
	* Attribution. You must attribute the work in the manner  specified by  the
	  author or licensor.
	* Noncommercial. You may not use this work for commercial purposes.

* For  any  reuse  or  distribution, you  must make clear to others the license
  terms of this work.
* Any  of  these  conditions  can  be  waived  if  you  get permission from the 
  copyright holder.

Your fair use and other rights are in no way affected by the above.
******************************************************************************/

var hs = {
// Language strings
lang : {
	loadingText : 'Loading...',
	loadingTitle : 'Click to cancel',
	focusTitle : 'Click to bring to front',
	fullExpandTitle : 'Expand to actual size',
	creditsText : 'Powered by <i>Highslide JS</i>',
	creditsTitle : 'Go to the Highslide JS homepage',
	previousText : 'Previous',
	nextText : 'Next', 
	moveText : 'Move',
	closeText : 'Close', 
	closeTitle : 'Close (esc)', 
	resizeTitle : 'Resize',
	playText : 'Play',
	playTitle : 'Play slideshow (spacebar)',
	pauseText : 'Pause',
	pauseTitle : 'Pause slideshow (spacebar)',
	previousTitle : 'Previous (arrow left)',
	nextTitle : 'Next (arrow right)',
	moveTitle : 'Move',
	fullExpandText : 'Full size',
	number: 'Image %1 of %2',
	restoreTitle : 'Click to close image, click and drag to move. Use arrow keys for next and previous.'
},
// See http://highslide.com/ref for examples of settings  
graphicsDir : 'highslide/graphics/',
restoreCursor : 'zoomout.cur', // necessary for preload
expandSteps : 10, // number of steps in zoom. Each step lasts for duration/step milliseconds.
expandDuration : 250, // milliseconds
restoreSteps : 10,
restoreDuration : 250,
marginLeft : 15,
marginRight : 15,
marginTop : 15,
marginBottom : 15,
zIndexCounter : 1001, // adjust to other absolutely positioned elements
loadingOpacity : 0.75,
allowMultipleInstances: true,
numberOfImagesToPreload : 5,
outlineWhileAnimating : 2, // 0 = never, 1 = always, 2 = HTML only 
outlineStartOffset : 3, // ends at 10
fullExpandPosition : 'bottom right',
fullExpandOpacity : 1,
padToMinWidth : false, // pad the popup width to make room for wide caption
showCredits : true, // you can set this to false if you want
creditsHref : 'http://highslide.com',
enableKeyListener : true,
transitions : [],
transitionDuration: 500,
dimmingOpacity: 0, // Lightbox style dimming background
dimmingDuration: 50, // 0 for instant dimming

anchor : 'auto', // where the image expands from
align : 'auto', // position in the client (overrides anchor)
targetX: null, // the id of a target element
targetY: null,
dragByHeading: true,
minWidth: 200,
minHeight: 200,
allowSizeReduction: true, // allow the image to reduce to fit client size. If false, this overrides minWidth and minHeight
outlineType : 'drop-shadow', // set null to disable outlines
wrapperClassName : 'highslide-wrapper', // for enhanced css-control
skin : {
	controls:
		'<div class="highslide-controls"><ul>'+
			'<li class="highslide-previous">'+
				'<a href="#" title="{hs.lang.previousTitle}">'+
				'<span>{hs.lang.previousText}</span></a>'+
			'</li>'+
			'<li class="highslide-play">'+
				'<a href="#" title="{hs.lang.playTitle}">'+
				'<span>{hs.lang.playText}</span></a>'+
			'</li>'+
			'<li class="highslide-pause">'+
				'<a href="#" title="{hs.lang.pauseTitle}">'+
				'<span>{hs.lang.pauseText}</span></a>'+
			'</li>'+
			'<li class="highslide-next">'+
				'<a href="#" title="{hs.lang.nextTitle}">'+
				'<span>{hs.lang.nextText}</span></a>'+
			'</li>'+
			'<li class="highslide-move">'+
				'<a href="#" title="{hs.lang.moveTitle}">'+
				'<span>{hs.lang.moveText}</span></a>'+
			'</li>'+
			'<li class="highslide-full-expand">'+
				'<a href="#" title="{hs.lang.fullExpandTitle}">'+
				'<span>{hs.lang.fullExpandText}</span></a>'+
			'</li>'+
			'<li class="highslide-close">'+
				'<a href="#" title="{hs.lang.closeTitle}" >'+
				'<span>{hs.lang.closeText}</span></a>'+
			'</li>'+
		'</ul></div>'
},
// END OF YOUR SETTINGS


// declare internal properties
preloadTheseImages : [],
continuePreloading: true,
expanders : [],
overrides : [
	'allowSizeReduction',
	'anchor',
	'align',
	'targetX',
	'targetY',
	'outlineType',
	'outlineWhileAnimating',
	'captionId',
	'captionText',
	'captionEval',
	'captionOverlay',
	'headingId',
	'headingText',
	'headingEval',
	'headingOverlay',
	'dragByHeading',
	'autoplay',
	'numberPosition',
	'transitions',
	'dimmingOpacity',
	
	'wrapperClassName',
	'minWidth',
	'minHeight',
	'maxWidth',
	'maxHeight',
	'slideshowGroup',
	'easing',
	'easingClose',
	'fadeInOut',
	'src'
],
overlays : [],
idCounter : 0,
oPos : {
	x: ['leftpanel', 'left', 'center', 'right', 'rightpanel'],
	y: ['above', 'top', 'middle', 'bottom', 'below']
},
mouse: {},
headingOverlay: {},
captionOverlay: {},
faders : [],

slideshows : [],

pendingOutlines : {},
clones : {},
ie : (document.all && !window.opera),
safari : /Safari/.test(navigator.userAgent),
geckoMac : /Macintosh.+rv:1\.[0-8].+Gecko/.test(navigator.userAgent),

$ : function (id) {
	return document.getElementById(id);
},

push : function (arr, val) {
	arr[arr.length] = val;
},

createElement : function (tag, attribs, styles, parent, nopad) {
	var el = document.createElement(tag);
	if (attribs) hs.setAttribs(el, attribs);
	if (nopad) hs.setStyles(el, {padding: 0, border: 'none', margin: 0});
	if (styles) hs.setStyles(el, styles);
	if (parent) parent.appendChild(el);	
	return el;
},

setAttribs : function (el, attribs) {
	for (var x in attribs) el[x] = attribs[x];
},

setStyles : function (el, styles) {
	for (var x in styles) {
		if (hs.ie && x == 'opacity') {
			if (styles[x] > 0.99) el.style.removeAttribute('filter');
			else el.style.filter = 'alpha(opacity='+ (styles[x] * 100) +')';
		}
		else el.style[x] = styles[x];
	}
},

ieVersion : function () {
	var arr = navigator.appVersion.split("MSIE");
	return arr[1] ? parseFloat(arr[1]) : null;
},

getPageSize : function () {
	var d = document, w = window, iebody = d.compatMode && d.compatMode != 'BackCompat' 
		? d.documentElement : d.body;	
	
	
	var b = d.body;
	var xScroll = (w.innerWidth && w.scrollMaxX) 
			? w.innerWidth + w.scrollMaxX : Math.max(b.scrollWidth, b.offsetWidth),
		yScroll = (w.innerHeight && window.scrollMaxY) 
			? w.innerHeight + w.scrollMaxY : Math.max(b.scrollHeight, b.offsetHeight),
		pageWidth = hs.ie ? iebody.scrollWidth :
			(d.documentElement.clientWidth || self.innerWidth),
      	pageHeight = hs.ie ? Math.max(iebody.scrollHeight, iebody.clientHeight) : 
			(d.documentElement.clientHeight || self.innerHeight);
	
	var width = hs.ie ? iebody.clientWidth : 
			(d.documentElement.clientWidth || self.innerWidth),
		height = hs.ie ? iebody.clientHeight : self.innerHeight;
	
	return {
		pageWidth: Math.max(pageWidth, xScroll),
		pageHeight: Math.max(pageHeight, yScroll),
		width: width,
		height: height,		
		scrollLeft: hs.ie ? iebody.scrollLeft : pageXOffset,
		scrollTop: hs.ie ? iebody.scrollTop : pageYOffset
	}
},

getPosition : function(el)	{
	var p = { x: el.offsetLeft, y: el.offsetTop };
	while (el.offsetParent)	{
		el = el.offsetParent;
		p.x += el.offsetLeft;
		p.y += el.offsetTop;
		if (el != document.body && el != document.documentElement) {
			p.x -= el.scrollLeft;
			p.y -= el.scrollTop;
		}
	}
	return p;
},
expand : function(a, params, custom, type) {
	if (!a) a = hs.createElement('a', null, { display: 'none' }, hs.container);
	if (typeof a.getParams == 'function') return params;	
	try {	
		new hs.Expander(a, params, custom);
		return false;
	} catch (e) { return true; }
},
getElementByClass : function (el, tagName, className) {
	var els = el.getElementsByTagName(tagName);
	for (var i = 0; i < els.length; i++) {
    	if ((new RegExp(className)).test(els[i].className)) {
			return els[i];
		}
	}
	return null;
},
replaceLang : function(s) {
	s = s.replace(/\s/g, ' ');
	var re = /{hs\.lang\.([^}]+)\}/g,
		matches = s.match(re),
		lang;
	for (var i = 0; i < matches.length; i++) {
		lang = matches[i].replace(re, "$1");
		if (typeof hs.lang[lang] != 'undefined') s = s.replace(matches[i], hs.lang[lang]);
	}
	return s;
},


focusTopmost : function() {
	var topZ = 0, topmostKey = -1;
	for (var i = 0; i < hs.expanders.length; i++) {
		if (hs.expanders[i]) {
			if (hs.expanders[i].wrapper.style.zIndex && hs.expanders[i].wrapper.style.zIndex > topZ) {
				topZ = hs.expanders[i].wrapper.style.zIndex;
				
				topmostKey = i;
			}
		}
	}
	if (topmostKey == -1) hs.focusKey = -1;
	else hs.expanders[topmostKey].focus();
},

getParam : function (a, param) {
	a.getParams = a.onclick;
	var p = a.getParams ? a.getParams() : null;
	a.getParams = null;
	
	return (p && typeof p[param] != 'undefined') ? p[param] : 
		(typeof hs[param] != 'undefined' ? hs[param] : null);
},

getSrc : function (a) {
	var src = hs.getParam(a, 'src');
	if (src) return src;
	return a.href;
},

getNode : function (id) {
	var node = hs.$(id), clone = hs.clones[id], a = {};
	if (!node && !clone) return null;
	if (!clone) {
		clone = node.cloneNode(true);
		clone.id = '';
		hs.clones[id] = clone;
		return node;
	} else {
		return clone.cloneNode(true);
	}
},

discardElement : function(d) {
	hs.garbageBin.appendChild(d);
	hs.garbageBin.innerHTML = '';
},
dim : function(exp) {
	if (!hs.dimmer) {
		hs.dimmer = hs.createElement ('div', 
			{ 
				className: 'highslide-dimming',
				owner: '',
				onclick: function() {
					 
						hs.close();
				}
			}, 
			{ position: 'absolute', left: 0 }, hs.container, true);
		hs.addEventListener(window, 'resize', hs.setDimmerSize);
	}
	hs.dimmer.style.display = '';
	hs.setDimmerSize();
	hs.dimmer.owner += '|'+ exp.key;
	if (hs.geckoMac && hs.dimmingGeckoFix) 
		hs.dimmer.style.background = 'url('+ hs.graphicsDir + 'geckodimmer.png)';		
	else
		hs.fade(hs.dimmer, 0, exp.dimmingOpacity, hs.dimmingDuration); 
},
undim : function(key) {
	if (!hs.dimmer) return;
	if (typeof key != 'undefined') hs.dimmer.owner = hs.dimmer.owner.replace('|'+ key, '');
	if (
		(typeof key != 'undefined' && hs.dimmer.owner != '')
		|| (hs.upcoming && hs.getParam(hs.upcoming, 'dimmingOpacity'))
	) return;
	if (hs.geckoMac && hs.dimmingGeckoFix) 
		hs.dimmer.style.background = 'none';
	else hs.fade(hs.dimmer, hs.dimmingOpacity, 0, hs.dimmingDuration);
	setTimeout( function() {
		hs.setStyles(hs.dimmer, { display: 'none', width: 0, height: 0 });
	}, hs.dimmingDuration);
},
setDimmerSize : function(exp) {
	if (!hs.dimmer) return;
	var page = hs.getPageSize();
	var h = (hs.ie && exp && exp.wrapper) ? 
		parseInt(exp.wrapper.style.top) + parseInt(exp.wrapper.style.height)+ (exp.outline ? exp.outline.offset : 0) : 0; 
	hs.setStyles(hs.dimmer, { 
		width: page.pageWidth +'px', 
		height: Math.max(page.pageHeight, h) +'px'
	});
},

previousOrNext : function (el, op) {
	hs.updateAnchors();
	var exp = hs.last = hs.getExpander(el);
	try {
		var adj = hs.upcoming =  exp.getAdjacentAnchor(op);
		adj.onclick(); 		
	} catch (e){
		hs.last = hs.upcoming = null;
	}
	try { exp.close(); } catch (e) {}
	return false;
},

previous : function (el) {
	return hs.previousOrNext(el, -1);
},

next : function (el) {
	return hs.previousOrNext(el, 1);	
},

keyHandler : function(e) {
	if (!e) e = window.event;
	if (!e.target) e.target = e.srcElement; // ie
	if (e.target.form) return true; // form element has focus
	
	var op = null;
	switch (e.keyCode) {
		case 32: // Space
			op = 2;
			break;
		case 34: // Page Down
		case 39: // Arrow right
		case 40: // Arrow down
			op = 1;
			break;
		case 8:  // Backspace
		case 33: // Page Up
		case 37: // Arrow left
		case 38: // Arrow up
			op = -1;
			break;
		case 27: // Escape
		case 13: // Enter
			op = 0;
	}
	if (op !== null) {if (op != 2)hs.removeEventListener(document, window.opera ? 'keypress' : 'keydown', hs.keyHandler);
		if (!hs.enableKeyListener) return true;
		
		if (e.preventDefault) e.preventDefault();
    	else e.returnValue = false;
    	
    	var exp = hs.getExpander();
    	if (exp) {
			if (op == 0) {
				exp.close();
			} else if (op == 2) {
				if (exp.slideshow) exp.slideshow.hitSpace();
			} else {
				if (exp.slideshow) exp.slideshow.pause();
				hs.previousOrNext(exp.key, op);
			}
			return false;
		}
	}
	return true;
},


registerOverlay : function (overlay) {
	hs.push(hs.overlays, overlay);
},


addSlideshow : function (options) {
	var sg = options.slideshowGroup;
	if (typeof sg == 'object') {
		for (var i = 0; i < sg.length; i++) {
			var o = {};
			for (var x in options) o[x] = options[x];
			o.slideshowGroup = sg[i];
			hs.push(hs.slideshows, o);
		}
	} else {
		hs.push(hs.slideshows, options);
	}
},

getWrapperKey : function (element, expOnly) {
	var el, re = /^highslide-wrapper-([0-9]+)$/;
	// 1. look in open expanders
	el = element;
	while (el.parentNode)	{
		if (el.id && re.test(el.id)) return el.id.replace(re, "$1");
		el = el.parentNode;
	}
	// 2. look in thumbnail
	if (!expOnly) {
		el = element;
		while (el.parentNode)	{
			if (el.tagName && hs.isHsAnchor(el)) {
				for (var key = 0; key < hs.expanders.length; key++) {
					var exp = hs.expanders[key];
					if (exp && exp.a == el) return key;
				}
			}
			el = el.parentNode;
		}
	}
	return null; 
},

getExpander : function (el, expOnly) {
	if (typeof el == 'undefined') return hs.expanders[hs.focusKey] || null;
	if (typeof el == 'number') return hs.expanders[el] || null;
	if (typeof el == 'string') el = hs.$(el);
	return hs.expanders[hs.getWrapperKey(el, expOnly)] || null;
},

isHsAnchor : function (a) {
	return (a.onclick && a.onclick.toString().replace(/\s/g, ' ').match(/hs.(htmlE|e)xpand/));
},

reOrder : function () {
	for (var i = 0; i < hs.expanders.length; i++)
		if (hs.expanders[i] && hs.expanders[i].isExpanded) hs.focusTopmost();
},

mouseClickHandler : function(e) 
{	
	if (!e) e = window.event;
	if (e.button > 1) return true;
	if (!e.target) e.target = e.srcElement;
	
	var el = e.target;
	while (el.parentNode
		&& !(/highslide-(image|move|html|resize)/.test(el.className)))
	{
		el = el.parentNode;
	}
	var exp = hs.getExpander(el);
	if (exp && (exp.isClosing || !exp.isExpanded)) return true;
		
	if (exp && e.type == 'mousedown') {
		if (e.target.form) return true;
		var match = el.className.match(/highslide-(image|move|resize)/);
		if (match) {
			hs.dragArgs = { exp: exp , type: match[1], left: exp.x.min, width: exp.x.span, top: exp.y.min, 
				height: exp.y.span, clickX: e.clientX, clickY: e.clientY };
			
			
			hs.addEventListener(document, 'mousemove', hs.dragHandler);
			if (e.preventDefault) e.preventDefault(); // FF
			
			if (/highslide-(image|html)-blur/.test(exp.content.className)) {
				exp.focus();
				hs.hasFocused = true;
			}
			return false;
		}
	} else if (e.type == 'mouseup') {
		
		hs.removeEventListener(document, 'mousemove', hs.dragHandler);
		
		if (hs.dragArgs) {
			if (hs.dragArgs.type == 'image')
				hs.dragArgs.exp.content.style.cursor = hs.styleRestoreCursor;
			var hasDragged = hs.dragArgs.hasDragged;
			
			if (!hasDragged &&!hs.hasFocused && !/(move|resize)/.test(hs.dragArgs.type)) {
				exp.close();
			} 
			else if (hasDragged || (!hasDragged && hs.hasHtmlExpanders)) {
				hs.dragArgs.exp.redoShowHide();
			}
			if (hasDragged) hs.setDimmerSize(exp);
			
			hs.hasFocused = false;
			hs.dragArgs = null;
		
		} else if (/highslide-image-blur/.test(el.className)) {
			el.style.cursor = hs.styleRestoreCursor;		
		}
	}
	return false;
},

dragHandler : function(e)
{
	if (!hs.dragArgs) return true;
	if (!e) e = window.event;
	var a = hs.dragArgs, exp = a.exp;
	
	a.dX = e.clientX - a.clickX;
	a.dY = e.clientY - a.clickY;	
	
	var distance = Math.sqrt(Math.pow(a.dX, 2) + Math.pow(a.dY, 2));
	if (!a.hasDragged) a.hasDragged = (a.type != 'image' && distance > 0)
		|| (distance > (hs.dragSensitivity || 5));
	
	if (a.hasDragged && e.clientX > 5 && e.clientY > 5) {
		
		if (a.type == 'resize') exp.resize(a);
		else exp.move(a);
	}
	return false;
},

wrapperMouseHandler : function (e) {
	try {
		if (!e) e = window.event;
		var over = /mouseover/i.test(e.type); 
		if (!e.target) e.target = e.srcElement; // ie
		if (hs.ie) e.relatedTarget = 
			over ? e.fromElement : e.toElement; // ie
		var exp = hs.getExpander(e.target);
		if (!exp.isExpanded) return;
		if (!exp || !e.relatedTarget || hs.getExpander(e.relatedTarget, true) == exp 
			|| hs.dragArgs) return;
		for (var i = 0; i < exp.overlays.length; i++) {
			var o = hs.$('hsId'+ exp.overlays[i]);
			if (o && o.hideOnMouseOut) {
				var from = over ? 0 : o.opacity,
					to = over ? o.opacity : 0;			
				hs.fade(o, from, to);
			}
		}	
	} catch (e) {}
},

addEventListener : function (el, event, func) {
	try {
		el.addEventListener(event, func, false);
	} catch (e) {
		try {
			el.detachEvent('on'+ event, func);
			el.attachEvent('on'+ event, func);
		} catch (e) {
			el['on'+ event] = func;
		}
	} 
},

removeEventListener : function (el, event, func) {
	try {
		el.removeEventListener(event, func, false);
	} catch (e) {
		try {
			el.detachEvent('on'+ event, func);
		} catch (e) {
			el['on'+ event] = null;
		}
	}
},

preloadFullImage : function (i) {
	if (hs.continuePreloading && hs.preloadTheseImages[i] && hs.preloadTheseImages[i] != 'undefined') {
		var img = document.createElement('img');
		img.onload = function() { 
			img = null;
			hs.preloadFullImage(i + 1);
		};
		img.src = hs.preloadTheseImages[i];
	}
},
preloadImages : function (number) {
	if (number && typeof number != 'object') hs.numberOfImagesToPreload = number;
	
	var arr = hs.getAnchors();
	for (var i = 0; i < arr.images.length && i < hs.numberOfImagesToPreload; i++) {
		hs.push(hs.preloadTheseImages, hs.getSrc(arr.images[i]));
	}
	
	// preload outlines
	if (hs.outlineType)	new hs.Outline(hs.outlineType, function () { hs.preloadFullImage(0)} );
	else
	
	hs.preloadFullImage(0);
	
	// preload cursor
	var cur = hs.createElement('img', { src: hs.graphicsDir + hs.restoreCursor });
},


init : function () {
	if (!hs.container) {
		hs.container = hs.createElement('div', {
				className: 'highslide-container'
			}, {
				position: 'absolute', 
				left: 0, 
				top: 0, 
				width: '100%', 
				zIndex: hs.zIndexCounter,
				direction: 'ltr'
			}, 
			document.body,
			true
		);
		hs.loading = hs.createElement('a', {
				className: 'highslide-loading',
				title: hs.lang.loadingTitle,
				innerHTML: hs.lang.loadingText,
				href: 'javascript:;'
			}, {
				position: 'absolute',
				top: '-9999px',
				opacity: hs.loadingOpacity,
				zIndex: 1
			}, hs.container
		);
		hs.garbageBin = hs.createElement('div', null, { display: 'none' }, hs.container);
		
		// http://www.robertpenner.com/easing/ 
		Math.linearTween = function (t, b, c, d) {
			return c*t/d + b;
		};
		Math.easeInQuad = function (t, b, c, d) {
			return c*(t/=d)*t + b;
		};
		Math.easeInOutQuad = function (t, b, c, d) {
			if ((t/=d/2) < 1) return c/2*t*t + b;
			return -c/2 * ((--t)*(t-2) - 1) + b;
		};
		for (var x in hs.langDefaults) {
			if (typeof hs[x] != 'undefined') hs.lang[x] = hs[x];
			else if (typeof hs.lang[x] == 'undefined' && typeof hs.langDefaults[x] != 'undefined') 
				hs.lang[x] = hs.langDefaults[x];
		}
	}
},
domReady : function() {
	hs.isDomReady = true;
	if (hs.onDomReady) hs.onDomReady();
},

updateAnchors : function() {
	var els = document.getElementsByTagName('*'), all = [], images = [],groups = {}, re;
	
	for (var i = 0; i < els.length; i++) {
		re = hs.isHsAnchor(els[i]);
		if (re) {
			hs.push(all, els[i]);
			if (re[0] == 'hs.expand') hs.push(images, els[i]);
			var g = hs.getParam(els[i], 'slideshowGroup') || 'none';
			if (!groups[g]) groups[g] = [];
			hs.push(groups[g], els[i]);
		}
	}
	hs.anchors = { all: all, groups: groups, images: images };
	
	return hs.anchors;
},

getAnchors : function() {
	return hs.anchors || hs.updateAnchors();
},


fade : function (el, o, oFinal, dur, i, dir) {
	if (typeof i == 'undefined') { // new fader
		if (typeof dur != 'number') dur = 250;
		if (dur < 25) { // instant
			hs.setStyles( el, { opacity: oFinal	});
			return;
		}
		i = hs.faders.length;
		dir = oFinal > o ? 1 : -1;
		var step = (25 / (dur - dur % 25)) * Math.abs(o - oFinal);
	}
	o = parseFloat(o);
	var skip = (el.fade === 0 || el.fade === false || (el.fade == 2 && hs.ie));
	el.style.visibility = ((skip ? oFinal : o) <= 0) ? 'hidden' : 'visible';
	
	if (skip || o < 0 || (dir == 1 && o > oFinal)) return;
	
	if (el.fading && el.fading.i != i) { // reverse
		clearTimeout(hs.faders[el.fading.i]);
		o = el.fading.o;
	}
	el.fading = {i: i, o: o, step: (step || el.fading.step)};
	el.style.visibility = (o <= 0) ? 'hidden' : 'visible';
	hs.setStyles(el, { opacity: o });
	hs.faders[i] = setTimeout(function() {
		hs.fade(el, o + el.fading.step * dir, oFinal, null, i, dir);
	}, 25);
},

close : function(el) {
	var exp = hs.getExpander(el);
	if (exp) exp.close();
	return false;
}
}; // end hs object


//-----------------------------------------------------------------------------
hs.Outline =  function (outlineType, onLoad) {
	this.onLoad = onLoad;
	this.outlineType = outlineType;
	var v = hs.ieVersion(), tr;
	
	this.hasAlphaImageLoader = hs.ie && v >= 5.5 && v < 7;
	if (!outlineType) {
		if (onLoad) onLoad();
		return;
	}
	
	hs.init();
	this.table = hs.createElement(
		'table', { 
			cellSpacing: 0 
		}, {
			visibility: 'hidden',
			position: 'absolute',
			borderCollapse: 'collapse',
			width: 0
		},
		hs.container,
		true
	);
	var tbody = hs.createElement('tbody', null, null, this.table, 1);
	
	this.td = [];
	for (var i = 0; i <= 8; i++) {
		if (i % 3 == 0) tr = hs.createElement('tr', null, { height: 'auto' }, tbody, true);
		this.td[i] = hs.createElement('td', null, null, tr, true);
		var style = i != 4 ? { lineHeight: 0, fontSize: 0} : { position : 'relative' };
		hs.setStyles(this.td[i], style);
	}
	this.td[4].className = outlineType;
	
	this.preloadGraphic(); 
};

hs.Outline.prototype = {
preloadGraphic : function () {
	var src = hs.graphicsDir + (hs.outlinesDir || "outlines/")+ this.outlineType +".png";
				
	var appendTo = hs.safari ? hs.container : null;
	this.graphic = hs.createElement('img', null, { position: 'absolute', left: '-9999px', 
		top: '-9999px' }, appendTo, true); // for onload trigger
	
	var pThis = this;
	this.graphic.onload = function() { pThis.onGraphicLoad(); };
	
	this.graphic.src = src;
},

onGraphicLoad : function () {
	var o = this.offset = this.graphic.width / 4,
		pos = [[0,0],[0,-4],[-2,0],[0,-8],0,[-2,-8],[0,-2],[0,-6],[-2,-2]],
		dim = { height: (2*o) +'px', width: (2*o) +'px' };
	hs.discardElement(this.graphic);
	for (var i = 0; i <= 8; i++) {
		if (pos[i]) {
			if (this.hasAlphaImageLoader) {
				var w = (i == 1 || i == 7) ? '100%' : this.graphic.width +'px';
				var div = hs.createElement('div', null, { width: '100%', height: '100%', position: 'relative', overflow: 'hidden'}, this.td[i], true);
				hs.createElement ('div', null, { 
						filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale, src='"+ this.graphic.src + "')", 
						position: 'absolute',
						width: w, 
						height: this.graphic.height +'px',
						left: (pos[i][0]*o)+'px',
						top: (pos[i][1]*o)+'px'
					}, 
				div,
				true);
			} else {
				hs.setStyles(this.td[i], { background: 'url('+ this.graphic.src +') '+ (pos[i][0]*o)+'px '+(pos[i][1]*o)+'px'});
			}
			
			if (window.opera && (i == 3 || i ==5)) 
				hs.createElement('div', null, dim, this.td[i], true);
			
			hs.setStyles (this.td[i], dim);
		}
	}
	if (hs.pendingOutlines[this.outlineType]) hs.pendingOutlines[this.outlineType].destroy();
	hs.pendingOutlines[this.outlineType] = this;
	if (this.onLoad) this.onLoad();
},
	
setPosition : function (exp, pos, vis) {
	pos = pos || {
		x: exp.x.min,
		y: exp.y.min,
		w: exp.x.span + exp.x.p1 + exp.x.p2,
		h: exp.y.span + exp.y.p1 + exp.y.p2
	};
	if (vis) this.table.style.visibility = (pos.h >= 4 * this.offset) 
		? 'visible' : 'hidden';
	hs.setStyles(this.table, {
		left: (pos.x - this.offset) +'px',
		top: (pos.y - this.offset) +'px',
		width: (pos.w + 2 * (exp.x.cb + this.offset)) +'px'
	});
	
	pos.w += 2 * (exp.x.cb - this.offset);
	pos.h += + 2 * (exp.y.cb - this.offset);
	hs.setStyles (this.td[4], {
		width: pos.w >= 0 ? pos.w +'px' : 0,
		height: pos.h >= 0 ? pos.h +'px' : 0
	});
	if (this.hasAlphaImageLoader) this.td[3].style.height 
		= this.td[5].style.height = this.td[4].style.height;
},
	
destroy : function(hide) {
	if (hide) this.table.style.visibility = 'hidden';
	else hs.discardElement(this.table);
}
};


//-----------------------------------------------------------------------------
// The expander object
hs.Expander = function(a, params, custom, contentType) {
	if (document.readyState && hs.ie && !hs.isDomReady) {
		hs.onDomReady = function() {
			new hs.Expander(a, params, custom, contentType);
		};
		return;
	} 
	this.a = a;
	this.custom = custom;
	this.contentType = contentType || 'image';
	this.isImage = !this.isHtml;
	
	hs.continuePreloading = false;
	this.overlays = [];
	this.last = hs.last;
	hs.last = null;
	hs.init();
	var key = this.key = hs.expanders.length;
	// override inline parameters
	for (var i = 0; i < hs.overrides.length; i++) {
		var name = hs.overrides[i];
		this[name] = params && typeof params[name] != 'undefined' ?
			params[name] : hs[name];
	}
	if (!this.src) this.src = a.href;
	// get thumb
	var el = (params && params.thumbnailId) ? hs.$(params.thumbnailId) : a;
	el = this.thumb = el.getElementsByTagName('img')[0] || el;
	this.thumbsUserSetId = el.id || a.id;
	
	// check if already open
	for (var i = 0; i < hs.expanders.length; i++) {
		if (hs.expanders[i] && hs.expanders[i].a == a 
			&& !(this.last && this.transitions[1] == 'crossfade')) {
			hs.expanders[i].focus();
			return false;
		}
	}	
	// cancel other
	for (var i = 0; i < hs.expanders.length; i++) {
		if (hs.expanders[i] && hs.expanders[i].thumb != el && !hs.expanders[i].onLoadStarted) {
			hs.expanders[i].cancelLoading();
		}
	}
	hs.expanders[this.key] = this;
	if (!hs.allowMultipleInstances) {
		if (hs.expanders[key-1]) hs.expanders[key-1].close();
		if (typeof hs.focusKey != 'undefined' && hs.expanders[hs.focusKey])
			hs.expanders[hs.focusKey].close();
	}
	
	var pos = hs.getPosition(el);
	
	// initiate metrics
	var x = this.x = {};
	x.t = el.width ? parseInt(el.width) : el.offsetWidth;
	x.tpos = pos.x;
	x.tb = (el.offsetWidth - x.t) / 2;
	var y = this.y = {};
	y.t = el.height ? parseInt(el.height) : el.offsetHeight;
	y.tpos = pos.y;
	y.tb = (el.offsetHeight - y.t) / 2;
	x.p1 = x.p2 = y.p1 = y.p2 = 0;
	hs.page = hs.getPageSize();		
	if (x.t == 0 && x.tpos == 0) {
		x.tpos = (hs.page.width / 2) + hs.page.scrollLeft;
		y.tpos = (hs.page.height / 2) + hs.page.scrollTop;
	};
	
	// instanciate the wrapper
	this.wrapper = hs.createElement(
		'div', {
			id: 'highslide-wrapper-'+ this.key,
			className: this.wrapperClassName
		}, {
			visibility: 'hidden',
			position: 'absolute',
			zIndex: hs.zIndexCounter++
		}, null, true );
	
	this.wrapper.onmouseover = this.wrapper.onmouseout = hs.wrapperMouseHandler;
	if (this.contentType == 'image' && this.outlineWhileAnimating == 2)
		this.outlineWhileAnimating = 0;
	
	// get the outline
	if (!this.outlineType 
		|| (this.last && this.isImage && this.transitions[1] == 'crossfade')) {
		this[this.contentType +'Create']();
	
	} else if (hs.pendingOutlines[this.outlineType]) {
		this.connectOutline();
		this[this.contentType +'Create']();
	
	} else {
		this.showLoading();
		var exp = this;
		new hs.Outline(this.outlineType, 
			function () {
				exp.connectOutline();
				exp[exp.contentType +'Create']();
			} 
		);
	}
	return true;
};

hs.Expander.prototype = {

connectOutline : function(x, y) {
	var o = this.outline = hs.pendingOutlines[this.outlineType];
	o.table.style.zIndex = this.wrapper.style.zIndex;
	hs.pendingOutlines[this.outlineType] = null;
},

showLoading : function() {
	if (this.onLoadStarted || this.loading) return;
	
	this.loading = hs.loading;
	var exp = this;
	this.loading.onclick = function() {
		exp.cancelLoading();
	};
	var exp = this, 
		l = (this.x.tpos + this.x.tb 
			+ (this.x.t - this.loading.offsetWidth) / 2) +'px',
		t = (this.y.tpos 
			+ (this.y.t - this.loading.offsetHeight) / 2) +'px';
	if (!tgt && this.last && this.transitions[1] == 'crossfade') 
		var tgt = this.last; 
	if (tgt) {
		l = (tgt.x.min + tgt.x.cb + tgt.x.p1
			+ (tgt.x.span - this.loading.offsetWidth) / 2) +'px';
		t = (tgt.y.min + tgt.y.cb + tgt.y.p1
			+ (tgt.y.span - this.loading.offsetHeight) / 2) +'px';
		this.loading.style.zIndex = hs.zIndexCounter++;
	}
	setTimeout(function () { 
		if (exp.loading) hs.setStyles(exp.loading, { left: l, top: t, zIndex: hs.zIndexCounter++ })}
	, 100);
},

imageCreate : function() {
	var exp = this;
	
	var img = document.createElement('img');
    this.content = img;
    img.onload = function () {
    	if (hs.expanders[exp.key]) exp.contentLoaded(); 
	};
    if (hs.blockRightClick) img.oncontextmenu = function() { return false; };
    img.className = 'highslide-image';
    hs.setStyles(img, {
    	visibility: 'hidden',
    	display: 'block',
    	position: 'absolute',
		maxWidth: '9999px',
		zIndex: 3
	});
    img.title = hs.lang.restoreTitle;
    if (hs.safari) hs.container.appendChild(img);
    if (hs.ie && hs.flushImgSize) img.src = null;
	img.src = this.src;
	
	this.showLoading();
},

contentLoaded : function() {
	try {	
		if (!this.content) return;
		this.content.onload = null;
		if (this.onLoadStarted) return;
		else this.onLoadStarted = true;
		
		var x = this.x, y = this.y;
		
		if (this.loading) {
			hs.setStyles(this.loading, { top: '-9999px' });
			this.loading = null;
		}
		this.marginBottom = hs.marginBottom;	
			x.full = this.content.width;
			y.full = this.content.height;
			
			hs.setStyles(this.content, {
				width: this.x.t +'px',
				height: this.y.t +'px'
			});
		
		this.wrapper.appendChild(this.content);
		hs.setStyles (this.wrapper, {
			left: this.x.tpos +'px',
			top: this.y.tpos +'px'
		});
		hs.container.appendChild(this.wrapper);
		
		// correct for borders
		x.cb = (this.content.offsetWidth - this.x.t) / 2;
		y.cb = (this.content.offsetHeight - this.y.t) / 2;
		var modMarginRight = hs.marginRight + 2 * x.cb;
		this.marginBottom += 2 * y.cb;
		
		
		this.initSlideshow();
		this.getOverlays();
		
		var ratio = x.full / y.full;
		var minWidth = this.allowSizeReduction 
			? this.minWidth : x.full;
		var minHeight = this.allowSizeReduction 
			? this.minHeight : y.full;
		
		var justify = { x: 'auto', y: 'auto' };
		
		if (this.align == 'center') {
			justify.x = justify.y = 'center';
		} else {
			if (this.anchor.match(/^top/)) justify.y = null;
			if (this.anchor.match(/right$/)) justify.x = 'max';
			if (this.anchor.match(/^bottom/)) justify.y = 'max';
			if (this.anchor.match(/left$/)) justify.x = null;
		}
		
		// justify
		x.min = x.tpos - x.cb + x.tb;
		x.span = Math.min(x.full, this.maxWidth || x.full);
		x.minSpan = Math.min(x.full, minWidth);
		x.justify = justify.x;
		x.target = this.targetX;
		x.marginMin = hs.marginLeft; 
		x.marginMax = modMarginRight;
		x.scroll = hs.page.scrollLeft;
		x.clientSpan = hs.page.width;
		this.justify(x);
		
		y.min = y.tpos - y.cb + y.tb;
		y.span = Math.min(y.full, this.maxHeight || y.full);
		y.minSpan = Math.min(y.full, minHeight);
		y.justify = justify.y;
		y.target = this.targetY;
		y.marginMin = hs.marginTop; 
		y.marginMax = this.marginBottom; 
		y.scroll = hs.page.scrollTop;
		y.clientSpan = hs.page.height;
		this.justify(y);
		if (this.overlayBox) this.sizeOverlayBox(0, 1);
		
		
		if (this.allowSizeReduction) {
				this.correctRatio(ratio);
			var ss = this.slideshow;
			
			if (ss && this.last && ss.controls && ss.fixedControls) {
				var pos = ss.overlayOptions.position || '', p;
				for (var dim in hs.oPos) for (var i = 0; i < 5; i++) {
					p = this[dim];
					if (pos.match(hs.oPos[dim][i])) {
						p.min = this.last[dim].min 
							+ (this.last[dim].p1 - p.p1)
							+ (this.last[dim].span - p.span) * [0, 0, .5, 1, 1][i];
						if (ss.fixedControls == 'fit') {
							if (p.min + p.span + p.p1 + p.p2 > p.scroll + p.clientSpan - p.marginMax)
								p.min = p.scroll + p.clientSpan - p.span - p.marginMin - p.marginMax - p.p1 - p.p2;
							if (p.min < p.scroll + p.marginMin) p.min = p.scroll + p.marginMin; 
						} 
					}
				}
			}		
			
			if (this.isImage && this.x.full > this.x.span) {
				this.createFullExpand();
				if (this.overlays.length == 1) this.sizeOverlayBox();	
			}
		}
		this.show();
		
	} catch (e) {
		window.location.href = this.src;
	}
},

justify : function (p, moveOnly) {
	var tgtArr, tgt = p.target, dim = p == this.x ? 'x' : 'y';
	
	if (tgt && tgt.match(/ /)) {
		tgtArr = tgt.split(' ');
		tgt = tgtArr[0];
	}
	if (tgt && hs.$(tgt)) {
		p.min = hs.getPosition(hs.$(tgt))[dim];
		if (tgtArr && tgtArr[1] && tgtArr[1].match(/^[-]?[0-9]+px$/)) 
			p.min += parseInt(tgtArr[1]);
		
	} else if (p.justify == 'auto' || p.justify == 'center') {
	
		var hasMovedMin = false;
		
		var allowReduce = hs.allowSizeReduction;
		if (p.justify == 'center') p.min = Math.round(p.scroll + (p.clientSpan - p.span - p.marginMax - p.p1 - p.p2) / 2);
		else
			p.min = Math.round(p.min - ((p.span + p.p1 + p.p2 - p.t) / 2));
		if (p.min < p.scroll + p.marginMin) {
			p.min = p.scroll + p.marginMin;
			hasMovedMin = true;		
		}
		if (!moveOnly && p.span < p.minSpan) {
			p.span = p.minSpan;
			allowReduce = false;
		}
		if (p.min + p.span + p.p1 + p.p2 > p.scroll + p.clientSpan - p.marginMax) {
			if (!moveOnly && hasMovedMin && allowReduce) {
				p.span = p.clientSpan - p.marginMin - p.marginMax; // can't expand more
			} else if (p.span + p.p1 + p.p2 < p.clientSpan - p.marginMin - p.marginMax) {
				p.min = p.scroll + p.clientSpan - p.span - p.marginMax - p.p1 - p.p2;
			} else { // image larger than viewport
				p.min = p.scroll + p.marginMin;
				if (!moveOnly && allowReduce) p.span = p.clientSpan - p.marginMin - p.marginMax;
			}			
		}
		
		if (!moveOnly && p.span < p.minSpan) {
			p.span = p.minSpan;
			allowReduce = false;
		}
		
	
	} else if (p.justify == 'max') {
		p.min = Math.floor(p.min - p.span + p.t);
	}
	
		
	if (p.min < p.marginMin) {
		var tmpMin = p.min;
		p.min = p.marginMin; 
		
		if (allowReduce && !moveOnly) p.span = p.span - (p.min - tmpMin);
		
	}
},

correctRatio : function(ratio) {
		
	var x = this.x, y = this.y;
	var changed = false;
	if (x.span / y.span > ratio) { // width greater
		x.span = y.span * ratio;
		if (x.span < x.minSpan) { // below minWidth
			x.span = x.minSpan;
			y.span = x.span / ratio;
		}
		changed = true;
	
	} else if (x.span / y.span < ratio) { // height greater
		var tmpHeight = y.span;
		y.span = x.span / ratio;
		changed = true;
	}
	this.fitOverlayBox(ratio);
	
	if (changed) {
		x.min = x.tpos - x.cb + x.tb;
		x.minSpan = x.span;
		this.justify(x, true);
	
		y.min = y.tpos - y.cb + y.tb;
		y.minSpan = y.span;
		this.justify(y, true);
		if (this.overlayBox) this.sizeOverlayBox();
	}
},
fitOverlayBox : function(ratio) {
	var x = this.x, y = this.y;
	if (this.overlayBox) {
		while (y.span > this.minHeight && x.span > this.minWidth 
				&& y.marginMin + y.p1 + y.span + y.p2 + y.marginMax > y.clientSpan) {
			y.span -= 10;
			if (ratio) x.span = y.span * ratio;
			this.sizeOverlayBox(0, 1);
		}
	}
},

show : function () {
	
	// Selectbox bug
	var imgPos = {
		x: this.x.min - 20, 
		y: this.y.min - 20, 
		w: this.x.span + 40 + this.x.p1 + this.x.p2, 
		h: this.y.span + 40 + this.y.p1 + this.y.p2
	};
	hs.hideSelects = (hs.ie && hs.ieVersion() < 7);
	if (hs.hideSelects) this.showHideElements('SELECT', 'hidden', imgPos);
	// Iframes bug
	hs.hideIframes = ((window.opera && navigator.appVersion < 9) || navigator.vendor == 'KDE' 
		|| (hs.ie && hs.ieVersion() < 5.5));
	if (hs.hideIframes) this.showHideElements('IFRAME', 'hidden', imgPos);
	// Scrollbars bug
	if (hs.geckoMac) this.showHideElements('*', 'hidden', imgPos); 
	
		
	// Apply size change
	this.changeSize(
		1,
		{ 
			xmin: this.x.tpos + this.x.tb - this.x.cb,
			ymin: this.y.tpos + this.y.tb - this.y.cb,
			xspan: this.x.t,
			yspan: this.y.t,
			xp1: 0,
			xp2: 0,
			yp1: 0,
			yp2: 0,
			o: hs.outlineStartOffset
		},
		{
			xmin: this.x.min,
			ymin: this.y.min,
			xspan: this.x.span,
			yspan: this.y.span,
			xp1: this.x.p1,
			yp1: this.y.p1,
			xp2: this.x.p2,
			yp2: this.y.p2,
			o: this.outline ? this.outline.offset : 0
		},
		hs.expandDuration,
		hs.expandSteps
	);
},

changeSize : function(up, from, to, dur, steps) {
	// transition
	var trans = this.transitions,
	other = up ? (this.last ? this.last.a : null) : hs.upcoming,
	t = (trans[1] && other 
			&& hs.getParam(other, 'transitions')[1] == trans[1]) ?
		trans[1] : trans[0];
		
	
	if (this[t] && t != 'expand') {
		this[t](up, from, to); 
		return;
	}
	
	if (up) hs.setStyles(this.wrapper, { opacity: 1 });
	
	if (this.outline && !this.outlineWhileAnimating) {
		if (up) this.outline.setPosition(this);
		else this.outline.destroy();
	}
	
	
	if (!up && this.overlayBox) {
		if (this.slideshow) {
			var c = this.slideshow.controls;
			if (c && hs.getExpander(c) == this) c.parentNode.removeChild(c);
		}
		hs.discardElement(this.overlayBox);
	}
	if (this.fadeInOut) {
		from.op = up ? 0 : 1;
		to.op = up;
	}
	var t,
	exp = this,
	easing = Math[this.easing] || Math.easeInQuad;
	if (!up) easing = Math[this.easingClose] || easing;
	
	for (var i = 1; i <= steps; i++) {
		t = Math.round(i * (dur / steps));
		
		(function(){
			var pI = i, size = {};
			
			for (var x in from) {
				size[x] = easing(t, from[x], to[x] - from[x], dur);
				if (!/^op$/.test(x)) size[x] = Math.round(size[x]);
			}
			setTimeout ( function() {
				if (up && pI == 1) {
					exp.content.style.visibility = 'visible';
					exp.a.className += ' highslide-active-anchor';
				}
				exp.setSize(size);
			}, t);				
		})();
	}
	
	if (up) { 
			
		setTimeout(function() {
			if (exp.outline) exp.outline.table.style.visibility = "visible";
		}, t);
		setTimeout(function() {
			exp.afterExpand();
		}, t + 50);
	}
	else setTimeout(function() { exp.afterClose(); }, t);
},

setSize : function (to) {
	try {		
		if (to.op) hs.setStyles(this.wrapper, { opacity: to.op });
		hs.setStyles ( this.wrapper, {
			width : (to.xspan +to.xp1 + to.xp2 +
				2 * this.x.cb) +'px',
			height : (to.yspan +to.yp1 + to.yp2 +
				2 * this.y.cb) +'px',
			left: to.xmin +'px',
			top: to.ymin +'px'
		});
		hs.setStyles(this.content, {
			top: to.yp1 +'px',
			left: to.xp1 +'px',
			width: to.xspan +'px',
			height: to.yspan +'px'
		});
		
		if (this.outline && this.outlineWhileAnimating) {
			var o = this.outline.offset - to.o;
			this.outline.setPosition(this, {
				x: to.xmin + o, 
				y: to.ymin + o, 
				w: to.xspan + to.xp1 + to.xp2 + - 2 * o, 
				h: to.yspan + to.yp1 + to.yp2 + - 2 * o
			}, 1);
		}
			
		this.wrapper.style.visibility = 'visible';
		
	} catch (e) {
		window.location.href = this.src;	
	}
},

fade : function(up, from, to) {
	this.outlineWhileAnimating = false;
	var exp = this,	t = up ? 250 : 0;
	
	if (up) {
		hs.setStyles(this.wrapper, { opacity: 0 });
		this.setSize(to);
		this.content.style.visibility = 'visible';

		hs.fade (this.wrapper, 0, 1);
	}
	
	if (this.outline) {
		this.outline.table.style.zIndex = this.wrapper.style.zIndex;
		var dir = up || -1;
		for (var i = from.o; dir * i <= dir * to.o; i += dir, t += 25) {
			(function() {
				var o = up ? to.o - i : from.o - i;
				setTimeout(function() {
					exp.outline.setPosition(exp, {
						x: (exp.x.min + o), 
						y: (exp.y.min + o),
						w: (exp.x.span - 2 * o + exp.x.p1 + exp.x.p2), 
						h: (exp.y.span - 2 * o + exp.y.p1 + exp.y.p2)
					}, 1);
				}, t);
			})();
		}
	}
	
	
	if (up) setTimeout(function() { exp.afterExpand(); }, t+50);
	else {
		setTimeout( function() {
			if (exp.outline) exp.outline.destroy(exp.preserveContent);
			hs.fade (exp.wrapper, 1, 0);
			setTimeout( function() {
				exp.afterClose();
			}, 250);
		}, t);		
	}
},

crossfade : function (up, from, to) {
	if (!up) return;
	var exp = this, steps = parseInt(hs.transitionDuration / 25), last = this.last;
	hs.removeEventListener(document, 'mousemove', hs.dragHandler);
	
	hs.setStyles(this.content, { 
		width: to.xspan +'px', 
		height: to.yspan +'px'		
	});
	this.outline = this.last.outline;
	this.last.outline = null;
	this.fadeBox = hs.createElement('div',
		{	className: 'highslide-image' },
		{ 
			position: 'absolute', 
			zIndex: 4,
			overflow: 'hidden',
			display: 'none'
		}
	);
	var names = { oldImg: last, newImg: this };
	for (var x in names) { 	
		this[x] = names[x].content.cloneNode(1);
		hs.setStyles(this[x], {
			position: 'absolute',
			border: 0,
			visibility: 'visible'
		});
		this.fadeBox.appendChild(this[x]);
	}
	this.wrapper.appendChild(this.fadeBox);
	
	from = {
		xmin: last.x.min,
		xspan: last.x.span,
		xp1: last.x.p1,
		xp2: last.x.p2,
		ymin: last.y.min,
		yspan: last.y.span,
		yp1: last.y.p1,
		yp2: last.y.p2,
		o: 1 / steps
	};
	
	to.yspan = this.y.span;
	to.o = 1;
	var t, easing = Math.easeInOutQuad;
	
	this.crossfadeStep(from);
	function prep() {
		if (exp.overlayBox) {
			exp.wrapper.appendChild(exp.overlayBox);
				
			for (var i = 0; i < exp.last.overlays.length; i++) {
				var oDiv = hs.$('hsId'+ exp.last.overlays[i]);
				if (oDiv.reuse === exp.key) exp.overlayBox.appendChild(oDiv);
				else hs.fade(oDiv, oDiv.opacity, 0);
			}
		}
		exp.fadeBox.style.display = '';
		exp.last.content.style.display = 'none';
	};
	if (/rv:1\.[0-8].+Gecko/.test(navigator.userAgent)) setTimeout(prep, 0);
	else prep();
	if (hs.safari) {
		var match = navigator.userAgent.match(/Safari\/([0-9]{3})/);
		if (match && parseInt(match[1]) < 525) this.wrapper.style.visibility = 'visible';
	}  
	
	for (var i = 1; i <= steps; i++) {
		t = Math.round(i * (hs.transitionDuration / steps));
		
		(function(){
			var size = {}, pI = i;
			for (var x in from)	{
				var val = easing(t, from[x], to[x] - from[x], hs.transitionDuration);
				size[x] = (x != 'o') ? Math.round(val) : val;
			}
			
			setTimeout ( function() {
				exp.crossfadeStep(size);
			}, t);				
		})();
	}
	setTimeout ( function () {
		exp.crossfadeEnd();
	}, t + 100);

},

crossfadeStep : function (size) {
	try {
		if (this.outline) this.outline.setPosition(this, { 
			x: size.xmin, 
			y: size.ymin, 
			w: size.xspan + size.xp1 + size.xp2, 
			h: size.yspan + size.yp1 + size.yp2
		}, 1);
		this.last.wrapper.style.clip = 'rect('
			+ (size.ymin - this.last.y.min)+'px, '
			+ (size.xspan + size.xp1 + size.xp2 + 2 * this.last.x.cb + size.xmin - this.last.x.min) +'px, '
			+ (size.yspan + size.yp1 + size.yp2 + 2 * this.last.y.cb + size.ymin - this.last.y.min) +'px, '
			+ (size.xmin - this.last.x.min)+'px)';
			
		hs.setStyles(this.content, {
			top: size.yp1 +'px',
			left: size.xp1 +'px',
			marginTop: (this.y.min - size.ymin) +'px',
			marginLeft: (this.x.min - size.xmin) +'px'
		});
		
		hs.setStyles(this.wrapper, {
			top: size.ymin +'px',
			left: size.xmin +'px',
			width: (size.xp1 + size.xp2 + size.xspan + 2 * this.x.cb)+ 'px',
			height: (size.yp1 + size.yp2 + size.yspan + 2 * this.y.cb) + 'px'
		});
			
		hs.setStyles(this.fadeBox, {
			width: size.xspan + 'px',
			height: size.yspan +'px',
			left: size.xp1 +'px',
			top: size.yp1 +'px',
			visibility: 'visible'
		});
		
		hs.setStyles(this.oldImg, {
			top: (this.last.y.min - size.ymin + this.last.y.p1 - size.yp1)+'px',
			left: (this.last.x.min - size.xmin + this.last.x.p1 - size.xp1)+'px'
		});		
		
		hs.setStyles(this.newImg, {
			opacity: size.o,
			top: (this.y.min - size.ymin + this.y.p1 - size.yp1) +'px',
			left: (this.x.min - size.xmin + this.x.p1 - size.xp1) +'px'
		});
		hs.setStyles(this.overlayBox, {
			width: size.xspan + 'px',
			height: size.yspan +'px',
			left: (size.xp1 + this.x.cb)  +'px',
			top: (size.yp1 + this.y.cb) +'px'
		});
	} catch (e) {}
},
crossfadeEnd : function() {
	this.wrapper.style.background = this.wrapperBG || '';
	
	this.wrapper.style.visibility = this.content.style.visibility = 'visible';
	this.fadeBox.style.display = 'none';
	this.a.className += ' highslide-active-anchor';
	this.afterExpand();
	this.last.afterClose();
},
reuseOverlay : function(o, el) {
	if (!this.last) return false;
	for (var i = 0; i < this.last.overlays.length; i++) {
		var oDiv = hs.$('hsId'+ this.last.overlays[i]);
		if (oDiv && oDiv.hsId == o.hsId) {
			this.genOverlayBox();
			oDiv.reuse = this.key;
			hs.push(this.overlays, this.last.overlays[i]);
			return true;
		}
	}
	return false;
},


afterExpand : function() {
	this.isExpanded = true;	
	this.focus();
	
	if (this.dimmingOpacity) hs.dim(this);
	if (hs.upcoming && hs.upcoming == this.a) hs.upcoming = null;
	this.prepareNextOutline();
	
	
	var p = hs.page, mX = hs.mouse.x + p.scrollLeft, mY = hs.mouse.y + p.scrollTop;
	this.mouseIsOver = this.x.min < mX && mX < this.x.min + this.x.p1 + this.x.span + this.x.p2
		&& this.y.min < mY && mY < this.y.min + this.y.p1 + this.y.span + this.y.p2;
	
	if (this.overlayBox) this.showOverlays();
	
},


prepareNextOutline : function() {
	var key = this.key;
	var outlineType = this.outlineType;
	new hs.Outline(outlineType, 
		function () { try { hs.expanders[key].preloadNext(); } catch (e) {} });
},


preloadNext : function() {
	var next = this.getAdjacentAnchor(1);
	if (next && next.onclick.toString().match(/hs\.expand/)) 
		var img = hs.createElement('img', { src: hs.getSrc(next) });
},


getAdjacentAnchor : function(op) {
	var current = this.getAnchorIndex(), as = hs.anchors.groups[this.slideshowGroup || 'none'];
	
	/*< ? if ($cfg->slideshow) : ?>s*/
	if (!as[current + op] && this.slideshow && this.slideshow.repeat) {
		if (op == 1) return as[0];
		else if (op == -1) return as[as.length-1];
	}
	/*< ? endif ?>s*/
	return as[current + op] || null;
},

getAnchorIndex : function() {
	var arr = hs.anchors.groups[this.slideshowGroup || 'none'];
	for (var i = 0; i < arr.length; i++) {
		if (arr[i] == this.a) return i; 
	}
	return null;
},


getNumber : function() {
	if (this[this.numberPosition]) {
		var arr = hs.anchors.groups[this.slideshowGroup || 'none'];
		var s = hs.lang.number.replace('%1', this.getAnchorIndex() + 1).replace('%2', arr.length);
		this[this.numberPosition].innerHTML = 
			'<div class="highslide-number">'+ s +'</div>'+ this[this.numberPosition].innerHTML;
	}
},
initSlideshow : function() {
	if (this.slideshow) return;
	
	if (!this.last) {
		for (var i = 0; i < hs.slideshows.length; i++) {
			var ss = hs.slideshows[i], sg = ss.slideshowGroup;
			if (typeof sg == 'undefined' || sg === null || sg === this.slideshowGroup) 
				this.slideshow = new hs.Slideshow(ss);
		} 
	} else {
		this.slideshow = this.last.slideshow;
	}
	var ss = this.slideshow;
	if (!ss) return;
	var exp = ss.exp = this;
	
	ss.checkFirstAndLast();
	if (ss.controls) {
		var o = ss.overlayOptions || {};
		o.overlayId = ss.controls;
		o.hsId = 'controls';
		this.createOverlay(o);
	}
	if (!this.last && this.autoplay) ss.play(true);
	if (ss.autoplay) {
		ss.autoplay = setTimeout(function() {
			hs.next(exp.key);
		}, (ss.interval || 500));
	}
},

cancelLoading : function() {	
	hs.expanders[this.key] = null;
	if (hs.upcoming == this.a) hs.upcoming = null;
	hs.undim();
	if (this.loading) hs.loading.style.left = '-9999px';
},

writeCredits : function () {
	if (this.credits) return;
	this.credits = hs.createElement('a',
		{
			href: hs.creditsHref,
			className: 'highslide-credits',
			innerHTML: hs.lang.creditsText,
			title: hs.lang.creditsTitle
		}
	);
	this.createOverlay({ 
		overlayId: this.credits, 
		position: 'top left', 
		hsId: 'credits' 
	});
},

getInline : function(types, addOverlay) {
	for (var i = 0; i < types.length; i++) {
		var type = types[i], s = null;
		if (!this[type +'Id'] && this.thumbsUserSetId)  
			this[type +'Id'] = type +'-for-'+ this.thumbsUserSetId;
		if (this[type +'Id']) this[type] = hs.getNode(this[type +'Id']);
		if (!this[type] && !this[type +'Text'] && this[type +'Eval']) try {
			s = eval(this[type +'Eval']);
		} catch (e) {}
		if (!this[type] && this[type +'Text']) {
			s = this[type +'Text'];
		}
		if (!this[type] && !s) {
			var next = this.a.nextSibling;
			while (next && !hs.isHsAnchor(next)) {
				if ((new RegExp('highslide-'+ type)).test(next.className || null)) {
					this[type] = next.cloneNode(1);
					break;
				}
				next = next.nextSibling;
			}
		}
		if (!this[type] && !s && this.numberPosition == type) s = '\n';
		
		if (!this[type] && s) this[type] = hs.createElement('div', 
				{ className: 'highslide-'+ type, innerHTML: s } );
				
		if (addOverlay && this[type]) {
			var o = { position: (type == 'heading') ? 'above' : 'below' };
			for (var x in this[type+'Overlay']) o[x] = this[type+'Overlay'][x];
			o.overlayId = this[type];
			this.createOverlay(o);
		}
	}
},


showHideElements : function (tagName, visibility, imgPos) {
	var els = document.getElementsByTagName(tagName);
	var prop = tagName == '*' ? 'overflow' : 'visibility';
	for (var i = 0; i < els.length; i++) {
		if (prop == 'visibility' || (document.defaultView.getComputedStyle(
				els[i], "").getPropertyValue('overflow') == 'auto'
				|| els[i].getAttribute('hidden-by') != null)) {
			var hiddenBy = els[i].getAttribute('hidden-by');
			if (visibility == 'visible' && hiddenBy) {
				hiddenBy = hiddenBy.replace('['+ this.key +']', '');
				els[i].setAttribute('hidden-by', hiddenBy);
				if (!hiddenBy) els[i].style[prop] = els[i].origProp;
			} else if (visibility == 'hidden') { // hide if behind
				var elPos = hs.getPosition(els[i]);
				elPos.w = els[i].offsetWidth;
				elPos.h = els[i].offsetHeight;
				if (!this.dimmingOpacity) { // hide all if dimming
				
					var clearsX = (elPos.x + elPos.w < imgPos.x || elPos.x > imgPos.x + imgPos.w);
					var clearsY = (elPos.y + elPos.h < imgPos.y || elPos.y > imgPos.y + imgPos.h);
				}
				var wrapperKey = hs.getWrapperKey(els[i]);
				if (!clearsX && !clearsY && wrapperKey != this.key) { // element falls behind image
					if (!hiddenBy) {
						els[i].setAttribute('hidden-by', '['+ this.key +']');
						els[i].origProp = els[i].style[prop];
						els[i].style[prop] = 'hidden';
					} else if (!hiddenBy.match('['+ this.key +']')) {
						els[i].setAttribute('hidden-by', hiddenBy + '['+ this.key +']');
					}
				} else if (hiddenBy == '['+ this.key +']' || hs.focusKey == wrapperKey) { // on move
					els[i].setAttribute('hidden-by', '');
					els[i].style[prop] = els[i].origProp || '';
				} else if (hiddenBy && hiddenBy.match('['+ this.key +']')) {
					els[i].setAttribute('hidden-by', hiddenBy.replace('['+ this.key +']', ''));
				}
						
			}
		}
	}
},

focus : function() {
	this.wrapper.style.zIndex = hs.zIndexCounter++;
	// blur others
	for (var i = 0; i < hs.expanders.length; i++) {
		if (hs.expanders[i] && i == hs.focusKey) {
			var blurExp = hs.expanders[i];
			blurExp.content.className += ' highslide-'+ blurExp.contentType +'-blur';
				blurExp.content.style.cursor = hs.ie ? 'hand' : 'pointer';
				blurExp.content.title = hs.lang.focusTitle;
		}
	}
	
	// focus this
	if (this.outline) this.outline.table.style.zIndex 
		= this.wrapper.style.zIndex;
	this.content.className = 'highslide-'+ this.contentType;
		this.content.title = hs.lang.restoreTitle;
		
		hs.styleRestoreCursor = window.opera ? 'pointer' : 'url('+ hs.graphicsDir + hs.restoreCursor +'), pointer';
		if (hs.ie && hs.ieVersion() < 6) hs.styleRestoreCursor = 'hand';
		this.content.style.cursor = hs.styleRestoreCursor;
		
	hs.focusKey = this.key;	
	hs.addEventListener(document, window.opera ? 'keypress' : 'keydown', hs.keyHandler);	
},

move : function (e) {
	this.x.min = e.left + e.dX;
	this.y.min = e.top + e.dY;
	
	if (e.type == 'image') this.content.style.cursor = 'move';
	hs.setStyles(this.wrapper, { left: this.x.min +'px', top: this.y.min +'px' });
	
	if (this.outline) this.outline.setPosition(this);
	
},
resize : function (e) {
	var w, h, r = e.width / e.height;
	w = Math.max(e.width + e.dX, Math.min(this.minWidth, this.x.full));
	if (this.isImage && Math.abs(w - this.x.full) < 12) w = this.x.full;
	h = w / r;
	
	if (h < Math.min(this.minHeight, this.y.full)) {
		h = Math.min(this.minHeight, this.y.full);
		if (this.isImage) w = h * r;
	}
	
	this.x.span = w;
	this.y.span = h;
	var size = { width: this.x.span +'px', height: this.y.span +'px' };
	hs.setStyles(this.content, size);
	if (this.overlayBox) this.sizeOverlayBox(true);
	hs.setStyles(this.wrapper, {
		width: (this.x.p1 + this.x.p2 +2 * this.x.cb + this.x.span) +'px',
		height: (this.y.p1 + this.y.p2 +2 * this.y.cb + this.y.span) +'px'
	});
	if (this.slideshow && this.isImage) {
		if (w == this.x.full) this.slideshow.disable('full-expand');
		else this.slideshow.enable('full-expand');
	}
	if (this.outline) this.outline.setPosition(this);
	
},

close : function() {
	if (this.isClosing || !this.isExpanded
		 || (hs.upcoming && this.transitions[1] == 'crossfade')
		) return;
	this.isClosing = true;
	if (this.slideshow && !hs.upcoming) this.slideshow.pause();
	
	hs.removeEventListener(document, window.opera ? 'keypress' : 'keydown', hs.keyHandler);
	
	try {
		this.content.style.cursor = 'default';
		this.changeSize(
			0, {
				xmin: this.x.min,
				ymin: this.y.min,
				xspan: this.x.span,
				yspan: parseInt(this.content.style.height),
				xp1: this.x.p1,
				yp1: this.y.p1,
				xp2: this.x.p2,
				yp2: this.y.p2,
				o: this.outline ? this.outline.offset : 0
			}, {
				xmin: this.x.tpos - this.x.cb + this.x.tb,
				ymin: this.y.tpos - this.y.cb + this.y.tb,
				xspan: this.x.t,
				yspan: this.y.t,
				xp1: 0,
				yp1: 0,
				xp2: 0,
				yp2: 0,
				o: hs.outlineStartOffset
			},
			hs.restoreDuration,
			hs.restoreSteps
		);
		
	} catch (e) { this.afterClose(); } 
},

createOverlay : function (o) {
	var el = o.overlayId;
	if (typeof el == 'string') el = hs.getNode(el);
	if (!el || typeof el == 'string') return;
	el.style.display = 'block';
	o.hsId = o.hsId || o.overlayId; 
	if (this.transitions[1] == 'crossfade' && this.reuseOverlay(o, el)) return;
	this.genOverlayBox();
	var width = o.width && /^[0-9]+(px|%)$/.test(o.width) ? o.width : 'auto';
	if (/^(left|right)panel$/.test(o.position) && !/^[0-9]+px$/.test(o.width)) width = '200px';
	
	var overlay = hs.createElement(
		'div',
		{ id: 'hsId'+ hs.idCounter++, hsId: o.hsId },
		{
			position: 'absolute',
			visibility: 'hidden',
			width: width
		},
		this.overlayBox,
		true
	);
	
	overlay.appendChild(el);
	hs.setAttribs(overlay, {
		hideOnMouseOut: o.hideOnMouseOut,
		opacity: o.opacity || 1,
		hsPos: o.position,
		fade: o.fade
	});
	
	if (this.gotOverlays) {
		this.positionOverlay(overlay);
		if (!overlay.hideOnMouseOut || this.mouseIsOver) hs.fade(overlay, 0, overlay.opacity);
	}
	hs.push(this.overlays, hs.idCounter - 1);
},
positionOverlay : function(overlay) {
	var p = overlay.hsPos || 'middle center';
	if (/left$/.test(p)) overlay.style.left = 0; 
	if (/center$/.test(p))	hs.setStyles (overlay, { 
		left: '50%',
		marginLeft: '-'+ Math.round(overlay.offsetWidth / 2) +'px'
	});	
	if (/right$/.test(p))	overlay.style.right = 0;
	
	if (/^leftpanel$/.test(p)) { 
		hs.setStyles(overlay, {
			right: '100%',
			marginRight: this.x.cb +'px',
			top: - this.y.cb +'px',
			bottom: - this.y.cb +'px',
			overflow: 'auto'
		});		 
		this.x.p1 = overlay.offsetWidth;
	
	} else if (/^rightpanel$/.test(p)) {
		hs.setStyles(overlay, {
			left: '100%',
			marginLeft: this.x.cb +'px',
			top: - this.y.cb +'px',
			bottom: - this.y.cb +'px',
			overflow: 'auto'
		});
		this.x.p2 = overlay.offsetWidth;
	}
	if (/^top/.test(p)) overlay.style.top = 0; 
	if (/^middle/.test(p))	hs.setStyles (overlay, { 
		top: '50%', 
		marginTop: '-'+ Math.round(overlay.offsetHeight / 2) +'px'
	});	
	if (/^bottom/.test(p)) overlay.style.bottom = 0;
	if (/^above$/.test(p)) {
		hs.setStyles(overlay, {
			left: (- this.x.p1 - this.x.cb) +'px',
			right: (- this.x.p2 - this.x.cb) +'px',
			bottom: '100%',
			marginBottom: this.y.cb +'px',
			width: 'auto'
		});
		this.y.p1 = overlay.offsetHeight;
	
	} else if (/^below$/.test(p)) {
		hs.setStyles(overlay, {
			position: 'relative',
			left: (- this.x.p1 - this.x.cb) +'px',
			right: (- this.x.p2 - this.x.cb) +'px',
			top: '100%',
			marginTop: this.y.cb +'px',
			width: 'auto'
		});
		this.y.p2 = overlay.offsetHeight;
		overlay.style.position = 'absolute';
	}
},

getOverlays : function() {	
	this.getInline(['heading', 'caption'], true);
	this.getNumber();
	if (this.heading && this.dragByHeading) this.heading.className += ' highslide-move';
	if (hs.showCredits) this.writeCredits();
	for (var i = 0; i < hs.overlays.length; i++) {
		var o = hs.overlays[i], tId = o.thumbnailId, sg = o.slideshowGroup;
		if ((!tId && !sg) || (tId && tId == this.thumbsUserSetId)
				|| (sg && sg === this.slideshowGroup)) {
			this.createOverlay(o);
		}
	}
	var os = [];
	for (var i = 0; i < this.overlays.length; i++) {
		var o = hs.$('hsId'+ this.overlays[i]);
		if (/panel$/.test(o.hsPos)) this.positionOverlay(o);
		else hs.push(os, o);
	}
	var curW = this.x.p1 + this.x.full + this.x.p2;
	if (hs.padToMinWidth && curW < hs.minWidth) {
		this.x.p1 += (hs.minWidth - curW) / 2;
		this.x.p2 += (hs.minWidth - curW) / 2;
	}
	for (var i = 0; i < os.length; i++) this.positionOverlay(os[i]);
	this.gotOverlays = true;
},
genOverlayBox : function() {
	if (!this.overlayBox) this.overlayBox = hs.createElement (
		'div', null, 
		{
			position : 'absolute',
			width: this.x.span ? this.x.span +'px' : this.x.full +'px',
			height: 0,
			visibility : 'hidden',
			overflow : 'hidden',
			zIndex : hs.ie ? 4 : null
		},
		hs.container,
		true
	);
},
sizeOverlayBox : function(doWrapper, doPanels) {
	hs.setStyles( this.overlayBox, {
		width: this.x.span +'px', 
		height: this.y.span +'px'
	});
	if (doWrapper || doPanels) {
		for (var i = 0; i < this.overlays.length; i++) {
			var o = hs.$('hsId'+ this.overlays[i]);
			if (o && /^(above|below)$/.test(o.hsPos)) {
				if (hs.ie && (hs.ieVersion() <= 6 || document.compatMode == 'BackCompat')) {
					o.style.width = (this.overlayBox.offsetWidth + 2 * this.x.cb
						+ this.x.p1 + this.x.p2) +'px';
				}
				this.y[o.hsPos == 'above' ? 'p1' : 'p2'] = o.offsetHeight;
			}
		}
	}
	if (doWrapper) {
		hs.setStyles(this.content, {
			top: this.y.p1 +'px'
		});
		hs.setStyles(this.overlayBox, {
			top: (this.y.p1 + this.y.cb) +'px'
		});
	}
},

showOverlays : function() {
	var b = this.overlayBox;
	hs.setStyles(b, {
		top: (this.y.p1 + this.y.cb) +'px',
		left: (this.x.p1 + this.x.cb) +'px',
		overflow : 'visible'
	});
	if (hs.safari) b.style.visibility = 'visible';
	this.wrapper.appendChild (b);
	for (var i = 0; i < this.overlays.length; i++) {
		var o = hs.$('hsId'+ this.overlays[i]);
		o.style.zIndex = o.hsId == 'controls' ? 5 : 4;
		if (!o.hideOnMouseOut || this.mouseIsOver) hs.fade(o, 0, o.opacity);
	}
},



createFullExpand : function () {
	if (this.slideshow && this.slideshow.controls) {
		this.slideshow.enable('full-expand');
		return;
	}
	this.fullExpandLabel = hs.createElement(
		'a',
		{
			href: 'javascript:hs.expanders['+ this.key +'].doFullExpand();',
			title: hs.lang.fullExpandTitle,
			className: 'highslide-full-expand'
		}
	);
	
	this.createOverlay({ 
		overlayId: this.fullExpandLabel, 
		position: hs.fullExpandPosition, 
		hideOnMouseOut: true, 
		opacity: hs.fullExpandOpacity
	});
},

doFullExpand : function () {
	try {
		if (this.fullExpandLabel) hs.discardElement(this.fullExpandLabel);
		
		this.focus();
		
		this.x.min = parseInt(this.wrapper.style.left) - (this.x.full - this.content.width) / 2;
		if (this.x.min < hs.marginLeft) this.x.min = hs.marginLeft;		
		this.wrapper.style.left = this.x.min +'px';
		
		hs.setStyles(this.content, { width: this.x.full +'px', height: this.y.full +'px'});
		
		this.x.span = this.x.full;
		this.y.span = this.y.full;
		
		
		if (this.overlayBox) this.sizeOverlayBox(true);		
		
		
		hs.setStyles(this.wrapper, {
			width: (this.x.p1 + 2 * this.x.cb + this.x.span + this.x.p2) +'px',
			height: (this.y.p1 + 2 * this.y.cb + this.y.span + this.y.p2) +'px'
		});
		
		
		if (this.outline) this.outline.setPosition(this);
		
		
		this.redoShowHide();
		
		hs.setDimmerSize(this);
		
	
	} catch (e) {
		window.location.href = this.content.src;
	}
},


// on end move and resize
redoShowHide : function() {
	var imgPos = {
		x: parseInt(this.wrapper.style.left) - 20, 
		y: parseInt(this.wrapper.style.top) - 20, 
		w: this.content.offsetWidth + 40 + this.x.p1 + this.x.p2, 
		h: this.content.offsetHeight + 40 + this.y.p1 + this.y.p2
	};
	if (hs.hideSelects) this.showHideElements('SELECT', 'hidden', imgPos);
	if (hs.hideIframes) this.showHideElements('IFRAME', 'hidden', imgPos);
	if (hs.geckoMac) this.showHideElements('*', 'hidden', imgPos);

},

afterClose : function () {
	this.a.className = this.a.className.replace('highslide-active-anchor', '');
	
	if (hs.hideSelects) this.showHideElements('SELECT', 'visible');
	if (hs.hideIframes) this.showHideElements('IFRAME', 'visible');
	if (hs.geckoMac) this.showHideElements('*', 'visible');
		if (this.outline && this.outlineWhileAnimating) this.outline.destroy();
	
		hs.discardElement(this.wrapper);
	if (this.dimmingOpacity) hs.undim(this.key);
	hs.expanders[this.key] = null;		
	hs.reOrder();
}

};


hs.Slideshow = function (options) {
	hs.updateAnchors();
	for (var x in options) this[x] = options[x];
	if (this.useControls) this.getControls();
};
hs.Slideshow.prototype = {
getControls: function() {
	this.controls = hs.createElement('div', { innerHTML: hs.replaceLang(hs.skin.controls) }, 
		null, hs.container);
	
	var buttons = ['play', 'pause', 'previous', 'next', 'move', 'full-expand', 'close'];
	this.btn = {};
	var pThis = this;
	for (var i = 0; i < buttons.length; i++) {
		this.btn[buttons[i]] = hs.getElementByClass(this.controls, 'li', 'highslide-'+ buttons[i]);
		this.enable(buttons[i]);
	}
	this.btn.pause.style.display = 'none';
	this.disable('full-expand');
},
checkFirstAndLast: function() {
	if (this.repeat || !this.controls) return;
	var cur = this.exp.getAnchorIndex(), re = /disabled$/;
	if (cur == 0) 
		this.disable('previous');
	else if (re.test(this.btn.previous.getElementsByTagName('a')[0].className))
		this.enable('previous');
	if (cur + 1 == hs.anchors.groups[this.exp.slideshowGroup || 'none'].length) {
		this.disable('next');
		this.disable('play');
	} else if (re.test(this.btn.next.getElementsByTagName('a')[0].className)) {
		this.enable('next');
		this.enable('play');
	}
},
enable: function(btn) {
	if (!this.btn) return;
	var sls = this, a = this.btn[btn].getElementsByTagName('a')[0], re = /disabled$/;
	a.onclick = function() {
		sls[btn]();
		return false;
	};
	if (re.test(a.className)) a.className = a.className.replace(re, '');
},
disable: function(btn) {
	if (!this.btn) return;
	var a = this.btn[btn].getElementsByTagName('a')[0];
	a.onclick = function() { return false; };
	if (!/disabled$/.test(a.className)) a.className += ' disabled';
},
hitSpace: function() {
	if (this.autoplay) this.pause();
	else this.play();
},
play: function(wait) {
	if (this.btn) {
		this.btn.play.style.display = 'none';
		this.btn.pause.style.display = '';
	}
	
	this.autoplay = true;	
	if (!wait) hs.next(this.exp.key);
},
pause: function() {
	if (this.btn) {
		this.btn.pause.style.display = 'none';
		this.btn.play.style.display = '';
	}
	
	clearTimeout(this.autoplay);
	this.autoplay = null;
},
previous: function() {
	this.pause();
	hs.previous(this.btn.previous);
},
next: function() {
	this.pause();
	hs.next(this.btn.next);
},
move: function() {},
'full-expand': function() {
	hs.getExpander().doFullExpand();
},
close: function() {
	hs.close(this.btn.close);
}

};
if (document.readyState && hs.ie) {
	(function () {
		try {
			document.documentElement.doScroll('left');
		} catch (e) {
			setTimeout(arguments.callee, 50);
			return;
		}
		hs.domReady();
	})();
}
hs.langDefaults = hs.lang;
// history
var HsExpander = hs.Expander;

// set handlers
hs.addEventListener(document, 'mousemove', function(e) {
	hs.mouse = { x: e.clientX, y: e.clientY	};
});
hs.addEventListener(document, 'mousedown', hs.mouseClickHandler);
hs.addEventListener(document, 'mouseup', hs.mouseClickHandler);
hs.addEventListener(window, 'load', hs.preloadImages);