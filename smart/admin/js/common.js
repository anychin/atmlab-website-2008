var jq = $.noConflict();$ = jq;

var initListeners = [];

function init(){
	try{AjaxForm.init();}catch(e){}
	for(var i = 0; i < initListeners.length; i++)
		initListeners[i]();
}

jq(document).ready(init);

function onInit(handler){initListeners.push(handler);}

function getBodyScrollTop(){return self.pageYOffset || (document.documentElement && document.documentElement.scrollTop) || (document.body && document.body.scrollTop);}

function getClientHeight(){return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientHeight:document.body.clientHeight;}

function getClientWidth(){return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth;}

function getClientCenterY(){return parseInt(getClientHeight()/2)+getBodyScrollTop();}

function getClientCenterX(){return parseInt(getClientWidth()/2);}

function openPopUp(url,w,h){
	if(jq.browser.msie)
    	window.open(url,'_blank','width='+(w + 18)+',height='+h+',copyhistory=no,directories=no,menubar=no,location=no,resizable=no,scrollbars=yes');
    else
    	window.open(url,'_blank','width='+(w)+',height='+h+',copyhistory=no,directories=no,menubar=no,location=no,resizable=no,scrollbars=yes');
    return false;
}


function openUrl(url, width, height, scroll){
	if(!scroll)
		window.open(url, 'page', 'width=' + width + ',height=' + height + ',copyhistory=no,directories=no,menubar=no,location=no,resizable=no,scrollbars=no');
	else
		window.open(url, 'page', 'width=' + width + ',height=' + height + ',copyhistory=no,directories=no,menubar=no,location=no,resizable=no,scrollbars=yes');
}


jq.fn.offsetTop = function() {
	var e = this.get(0);
	if(!e.offsetParent) return e.offsetTop;
	return e.offsetTop + jq(e.offsetParent).offsetTop();
};

jq.fn.offsetLeft = function() {
	var e = this.get(0);
	if(!e.offsetParent) return e.offsetLeft;
	return e.offsetLeft + jq(e.offsetParent).offsetLeft();
};

function callMethod(name){
	var a = [];
	for(var i = 1; i < arguments.length; i++)
		a.push(arguments[i]);
	if(name instanceof Function){
		name.apply(name.instance, a);
		return;
	}
	var target = window;
	var method = name;
	if(name.indexOf('.') > -1){
		var arr = name.split('.');
		target = window[arr[0]];
		method = arr[1];
	}
	if(target == undefined || target[method] == undefined)
		return null;
	return target[method].apply(this, a);
}

try{
jq.extend(DateInput.DEFAULT_OPTS, {
  stringToDate: function(string) {
    var matches;
    if (matches = string.match(/^(\d{2,2})-(\d{2,2})-(\d{4,4})$/)) {
      return new Date(matches[3], matches[2] - 1, matches[1]);
    } else {
      return null;
    };
  },

  dateToString: function(date) {
    var month = (date.getMonth() + 1).toString();
    var dom = date.getDate().toString();
    if (month.length == 1) month = "0" + month;
    if (dom.length == 1) dom = "0" + dom;
    return dom + "-" + month + "-" + date.getFullYear();
  }
});
}catch(e){};