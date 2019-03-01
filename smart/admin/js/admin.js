AjaxForm.options.prefix = '/admin';

var timeoutHandler;
function showOk(){
	jq(".ok-panel").show(200, function(){
		clearTimeout(timeoutHandler);
		timeoutHandler = setTimeout(function(){
			jq(".ok-panel").hide();
		}, 2000);
	});
}

onInit(function(){
	var sel = jq(".top1 select");
	if(sel.length == 0)
		return;
	sel.change(function(){
		jq.get("/admin/language/?lang=" + sel.val(), function(){
			document.location.href = document.location.href;
		});
	});
});

TreeMenu = {
	data:{},
	init: function(id){
		if(TreeMenu.data[id] != undefined)
			return TreeMenu.data[id];
		var o = {};
		o.container = jq("#item" + id);
		var pl = jq("#plus" + id);
		if(pl.length > 0){
			o.plus = jq("img[src*=plus]", pl);
			o.minus = jq("img[src*=minus]", pl);
		}
		var buttons = jq(".buttons", o.container).eq(0);
		o.publ = jq(".publ", buttons);
		o.unpubl = jq(".unpubl", buttons);
		o.del = jq(".del", buttons);
		o.down = jq(".down", buttons);
		o.up = jq(".up", buttons);
		o.ord = jq(".count", buttons);
		o.sub = jq("#sub" + id);
		TreeMenu.data[id] = o;
		return TreeMenu.data[id];
	},
	pub: function(id, url){
		var d = TreeMenu.init(id);
		jq.get(url + "/id/" + id  + "/?empty=1", function(){
			d.publ.toggle();
			d.unpubl.toggle();
		});
		return false;
	},
	del: function(id, url){
		var d = TreeMenu.init(id);
		if(!confirm("Вы действительно хотите удалить?"))
			return;
		if(d.sub.length > 0)
			d.sub.remove();
		d.container.remove();
		jq.get(url + "/id/" + id  + "/?empty=1");
	},
	sub: function(id){
		var d = TreeMenu.init(id);
		d.minus.toggle();
		d.plus.toggle();
		d.sub.toggle();
	},
	up:function(id, url){TreeMenu.setOrder(id, -1, url);},
	down:function(id, url){TreeMenu.setOrder(id, 1, url);},
	setOrder:function(id, dif, url){
		var d = TreeMenu.init(id);
		var order = parseInt(d.ord.html());
		var newV = order + dif;
		if(newV < 0)
			return;
		jq.get(url + "/id/" + id + "/ord/" + newV  + "/?empty=1", function(){
			d.ord.html("" + newV);
		});
	}
}

List = {
	data:{},
	init: function(id){
		if(List.data[id] != undefined)
			return List.data[id];
		var o = {};
		o.container = jq("#list_item" + id);
		o.del = jq(".del", o.container);
		o.publ = jq(".publ", o.container);
		o.unpubl = jq(".unpubl", o.container);
		List.data[id] = o;
		return List.data[id];
	},
	del: function(id, url){
		var d = List.init(id);
		if(!confirm("Вы действительно хотите удалить?"))
			return;
		jq.get(url + "/id/" + id  + "/?empty=1", function(){
			d.container.remove();
		});
	},
	pub: function(id, url){
		var d = List.init(id);
		jq.get(url + "/id/" + id  + "/?empty=1", function(){
			d.publ.toggle();
			d.unpubl.toggle();
		});
		return false;
	}
};

function getUrl(){
	var url = jq.trim(this.control.val());
	if(url.length < 1)
		return url;
	if(url.charAt(0) == '/')
		url = url.substr(1, url.length);
	return url;
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