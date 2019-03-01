onInit(function(){
	var menu = jq(".menu");
	jq(".item", menu).each(function(i, item){
		item = jq(item);
		var sub = item.next();
		if(sub.length == 0)
			return;
		var y = item.offsetTop() + 30;
		if(jq.browser.mozilla)
			y--;
		sub.css({top:y, opacity:0.9});
		item.hover(function(){
			sub.show();
			item.css({backgroundColor: "#333",borderColor: "#333", color: "#fff"});
		}, function(){
			item.css({backgroundColor: "#f1f3f5",borderColor: "#f1f3f5",color: "#000"});
			sub.hide();
		});
		sub.hover(function(){
			sub.stop();
			sub.show();
			item.css({backgroundColor: "#333",borderColor: "#333" ,color: "#fff"});
		}, function(){
			item.css({backgroundColor: "#f1f3f5",borderColor: "#f1f3f5",color: "#000"});
			sub.hide();
		});
	});
});