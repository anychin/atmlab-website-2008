formUrl = "/form/";

var popup;
var shadow;
var data = {};
var loaded = {};
var words = [];
var curLine = -1;
var curProject = -1;
var lineData = {};
var sidebar;
var lock = false;

onInit(function(){
	var so = new SWFObject("/flash/player.swf", "playerSWF", "348", "195", "8");
	so.addParam("wmode", "transparent");
	so.addParam("allowFullScreen", "true");
	so.addVariable("file", "/webstuby.flv");
	so.addVariable("img", "/flash/cover-pencil.jpg");
	so.write("player");
});

onInit(function(){
	function SB(){
		var obj = jq(".right-menu, .left-menu");
		var slide = jq(".slide-menu", obj);
		var w = slide.width() + 35;
		var startY = obj.offsetTop();
		var opened = false;
		var that = this;
		this.slide = function(to){
			that.quickClose();
			obj.stop();
			obj.animate({top:parseInt(to) + startY}, 400);
		};
		this.open = function(){
			opened = true;
			slide.stop();
			if(obj.hasClass('.left-menu'))
				slide.animate({left:0}, 700);
			else
				obj.animate({width:235}, 700);
		}
		this.close = function(){
			opened = false;
			if(obj.hasClass('.left-menu'))
				slide.animate({left:-200}, 700);
			else
				obj.animate({width:35}, 700);
		}
		this.quickClose = function(){
			opened = false;
			if(obj.hasClass('.left-menu'))
				slide.css({left:-200});
		}
		jq(".bg-png-crop", obj).click(function(){
			if(opened)
				that.close();
			else
				that.open();
			return false;
		});
	};
	sidebar = new SB();
});

var articlecache = {};

function openTag(article){
	jq(".header span", popup).html(article.name);
	jq(".text", popup).html(article.text);
	var l = (getClientWidth() - popup.width()) / 2;
	var t = getClientCenterY() - popup.height() / 2;
	shadow.css({height:jq("#label").offsetTop(), display:"block"});
	popup.css({left:l, top:t, opacity:0, display:"block"}).animate({opacity:1}, 600);
}

onInit(function(){
	jq('.projects-line').each(function(i, gallery){
		gallery = jq(gallery);
		var word = jq(".word", gallery);
		jq(".imghref", word).click(function(){
			var attrs = jq("img", word).attr("class").split("_");
			openPopUp("/html/photo.html?img=" + attrs[0], attrs[1], attrs[2]);
			return false;
		});
		var tags = jq(".tags", gallery);
		tags.width(jq(".word-cont", word).width());
		var tagsCount = jq("a", tags).length;
		jq("a", tags).each(function(i, item){
			item = jq(item);
			item.click(function(){
				if(articlecache[item.attr("href")]){
					openTag(articlecache[item.attr("href")]);
				}else
					jq.get("/xml" + item.attr("href"), function(response){
						var article = XMLParser.deserialize(response);
						articlecache[item.attr("href")] = {text:article.article.text, name:article.article.name};
						openTag(articlecache[item.attr("href")]);
					});
				return false;
			});
		});
		if(tagsCount > 0){
			jq(".word-cont", word).hover(function(){
				tags.show();
				jq(".word-cont span", word).hide();
			}, function(){
				tags.hide();
				jq(".word-cont span", word).show();
			});
			tags.hover(function(){
				tags.show();
				jq(".word-cont span", word).hide();
			}, function(){
			});
		}
		words.push(word);
		jq(".cont", word).css("width", jq(".cont", word).width());
		lineData[i] = {wordLength:jq(".cont", word).width(), imageWidth:jq(".annotation", gallery).width()};
		var photos = jq(".photos", gallery);
		var container = jq(".container", gallery);
		var photosWidth = photos.width();
		var containerWidth = container.width();
		lineData[i]['params'] = {photos:photos, container:container, photosWidth:photosWidth};
		
		jq(".more-photo a", gallery).each(function(k, item){
			item = jq(item);
			var images = item.attr("id").split("_");
			item.bind("click", function(){
				if(!loaded[images[0]]){
					var img = new Image();
					img.onload = function(){
						loaded[images[0]] = true;
						openPhoto2(word, images);
					};
					img.src = "/images/uploaded/" + images[0] + ".jpg";
				}else{
					openPhoto2(word, images);
				}
				return false;
			});
		});
		photos.hover(function(){
			jq(".word-cont span", word).hide();
		}, function(){
			jq(".word-cont span", word).show();
		});
		jq("a", photos).each(function(j, photo){
			photo = jq(photo);
			var id = photo.attr("id").replace("project", "");
			data[id] = photo.attr("class").split("_");
			photo.click(function(){
				if(lock)
					return false;
				lock = true;
				jq("img", word).attr("class", data[id][5] + "_" + data[id][6] + "_" + data[id][7]);
				if(id == curProject)
				{
					closePhoto(word);
					return false;
				}
				curProject = id;
				if(!loaded[data[id][2]]){
					var img = new Image();
					img.onload = function(){
						openPhoto(id, word, i, j);
						loaded[data[id][2]] = true;
					};
					img.src = "/images/uploaded/" + data[id][2] + ".jpg";
				}else{
					openPhoto(id, word, i, j)
				}
				return false;
			});
			var t = jq(".word-cont div.title", word);
			var title = jq("#description" + id + " h2");
			photo.hover(function(){
				jq("img", photo).attr("src", "/images/uploaded/" + data[id][0] + ".jpg");
				if(curLine != i){
					t.show();
					t.html(title.html());
				}
			}, function(){
				jq("img", photo).attr("src", "/images/uploaded/" + data[id][1] + ".jpg");
				if(curLine != i){
					t.hide();
				}
			});
		});
	});
});

function closePhoto(word){
	var params = {height:91};
	if(Math.abs(lineData[curLine].imageWidth - lineData[curLine].wordLength) > 10)
		params.width = lineData[curLine].wordLength;
	lineData[curLine].params.photos.stop().css({left:0});
	curLine = -1;
	curProject = -1;
	jq("img", word).animate({opacity:0}, 200, function(){
		jq("img", word).hide();
		jq("[id^=description]").hide();
		jq(".word-cont", word).show();
		sidebar.close();
		jq(".cont", word).animate(params, 400);
		lock = false;
	});
}

function openPhoto2(word, images){
	var img = jq("img", word);
	img.attr("class", images[3] + "_" + images[4] + "_" + images[5]);
	if(jq(".cont", word).height() == images[2]){
		img.animate({opacity:0}, 200, function(){
			img.attr("src", "/images/uploaded/" + images[0] + ".jpg");
			img.animate({opacity:1}, 300);
		});
	}else{
		img.animate({opacity:0}, 200, function(){
			jq("img", word).css({width:images[6], height:images[7]});
		});
		sidebar.close();
		jq(".cont", word).animate({height:images[2]}, 400, function(){
			img.animate({opacity:0}, 200, function(){
				img.attr("src", "/images/uploaded/" + images[0] + ".jpg");
				img.animate({opacity:1}, 300);
			});
		});
	}
}

function openPhoto(id, word, i, j){
	for(var k = 0; k < words.length; k++){
		jq(".word-cont div.title", words[k]).hide();
		if(k != i)
		{
			jq("img", words[k]).hide();
			lineData[k].params.photos.stop().css({left:0});
			var params = {height:91};
			if(Math.abs(lineData[k].imageWidth - lineData[k].wordLength) > 10)
				params.width = lineData[k].wordLength;
			if(curLine == k){
				jq(".cont", words[k]).animate(params, 400, function(){
					jq(".word-cont", word).hide();
				});
			}else{
				jq(".cont", words[k]).css(params);
				jq(".word-cont", word).hide();
			}
			//jq(".word-cont", words[k]).css({opacity:1, display:'block'});
			jq(".word-cont", words[k]).css({display:'block'});
			jq(".text-content").hide();
		}
	}
	var img = jq("img", word);
	if(curLine != i)
	{
		var params = {height:data[id][4]};
		sidebar.close();
		if(Math.abs(lineData[i].imageWidth - lineData[i].wordLength) > 10)
			params.width = lineData[i].imageWidth;
		jq(".cont", word).animate(params, 400, function(){
			lineData[i].params.photos.stop().animate({left:getScrollX(j, lineData[i].params.container.width(), lineData[i].params.photosWidth)}, 300);
			img.css({opacity:0, display:"block"});
			img.attr("src", "/images/uploaded/" + data[id][2] + ".jpg");
			img.css({width:data[id][3], height:data[id][4]});
			img.css("z-index", 100);
			img.animate({opacity:1}, 800);
			jq("#description" + id).show();
			lock = false;
		});
		curLine = i;
	}
	else
	{
		if(jq(".cont", word).height() != data[id][4]){
			sidebar.close();
			img.animate({opacity:0}, 200, function(){
				img.css({width:data[id][3], height:data[id][4]});
			});
			jq(".cont", word).animate({height:data[id][4]}, 400, function(){
				lineData[i].params.photos.stop().animate({left:getScrollX(j, lineData[i].params.container.width(), lineData[i].params.photosWidth)}, 300);
				img.animate({opacity:0}, 200, function(){
					img.attr("src", "/images/uploaded/" + data[id][2] + ".jpg");
					img.animate({opacity:1}, 300);
					jq("#description" + id).show();
					lock = false;
				});
			});
		}else{
				lineData[i].params.photos.stop().animate({left:getScrollX(j, lineData[i].params.container.width(), lineData[i].params.photosWidth)}, 300);
				img.animate({opacity:0}, 200, function(){
				img.attr("src", "/images/uploaded/" + data[id][2] + ".jpg");
				img.animate({opacity:1}, 300);
				jq("#description" + id).show();
				lock = false;
			});
		}
		
	}
}

function getScrollX(n, cw, lw){
	if(n == 0 || lw < cw)
		return 0;
	n ++;
	if(n * 58 < (cw / 2))
		return 0;
	var newX = Math.floor(n * 58 - cw / 2);
	if(lw - newX < cw)
		newX = lw - cw;
	newX = Math.round((newX / 58)) * 58;
	if(newX < 0)
		newX = 0;
	return -1 * newX;
}

function closePopup(){
	popup.stop();
	popup.animate({opacity:0}, 300, function(){
		shadow.hide();
		popup.hide();
	});
}

onInit(function(){
	popup = jq(".popup");
	shadow = jq(".shadow");
	jq(".close", popup).click(function(){
		closePopup();
		return false;
	});
	jq(".shadow").click(function(){
		closePopup();
	});
	jq(".window").each(function(i, item){
		item = jq(item);
		item.click(function(){
			var id = item.attr("id");
			if(jq("#title_" + id).length == 0)
				return false;
			jq(".header span", popup).html(jq("#title_" + id).html());
			jq(".text", popup).html(jq("#content_" + id).html());
			var l = (getClientWidth() - popup.width()) / 2;
			var t = getClientCenterY() - popup.height() / 2;
			shadow.css({height:jq("#label").offsetTop(), display:"block"});
			popup.css({left:l, top:t, opacity:0, display:"block"}).animate({opacity:1}, 600);
			return false;
		});
	});
});

onInit(function(){
	var n = Math.ceil(Math.random() * 10);
	var remarks = jq(".remark");
	var container = jq(".slogan .current");
	var c = remarks.length;
	if(n >= c)
		n = 0;
	container.html(jq(remarks.get(n)).html());
	jq(".slogan a").click(function(){
		n ++;
		if(n >= c)
			n = 0;
		container.html(jq(remarks.get(n)).html());
		return false;
	});
});

function onWhy(d, data){
	var popup2 = jq(".popup2");
	jq("textarea", popup2).hide();
	jq(".ok", popup2).show();
	jq(".submit", popup2).hide();
}

onInit(function(){
	var popup2 = jq(".popup2");
	popup2.click(function(){
		return false;
	});
	jq(".close", popup2).click(function(){
		popup2.stop();
		popup2.animate({opacity:0}, 300, function(){
			shadow.hide();
			popup2.hide();
		});
		return false;
	});
	jq(".shadow").click(function(){
		popup2.stop();
		popup2.animate({opacity:0}, 300, function(){
			shadow.hide();
			popup2.hide();
		});
	});
	jq(".why").click(function(){
		var l = (getClientWidth() - popup2.width()) / 2;
		var t = getClientCenterY() - popup2.height() / 2;
		jq("textarea", popup2).val("");
		jq("textarea", popup2).show();
		jq(".ok", popup2).hide();
		jq(".submit", popup2).show();
		shadow.css({height:jq("#label").offsetTop(), display:"block"});
		popup2.css({left:l, top:t, opacity:0, display:"block"}).animate({opacity:1}, 600);
		jq("textarea", popup2).focus();
		return false;
	});
});

var newsData = [];
onInit(function(){
	var text = jq(".news-content .text");
	var title = jq(".news-content .title");
	var container = jq(".news-content");
	var n = parseInt(jq("#newsc").html());
	var position = 	jq(".news-cont a").length;
	jq(".news-cont a").each(function(i, item){
		item = jq(item);
		newsData.push({title:item.html(), text:item.next().html()})
		item.click(function(){
			if(title.html() == newsData[i].title)
			{
				container.hide();
				title.html("");
			}
			else
			{
				text.html(newsData[i].text);
				title.html(newsData[i].title);
				container.show();
			}
			return false;
		});
	});
	var t = jq(".t");
	var d = jq(".d");
	d.click(function(){
		t.show();
		position += 3;
		if(position > n)
			position = n;
		if(position == n)
			d.hide();
		if(newsData.length < position){
			jq.get("/news/xml", {position:newsData.length}, function(result){
				var d = XMLParser.deserialize(result);
				for(var i = 0; i < d.news.news.length; i++)
				{
					var news = d.news.news[i];
					newsData.push({title:news.title, text:news.text});
					jq(".news-cont ul").append("<li><a href='/news/" + news.id + "'>" + news.title + "</a><span class='ncontent'>" + news.text + "</span></li>");
				}
				jq(".news-cont ul").css({top:-25 * (position - 3)});
			});
		}else
			jq(".news-cont ul").css({top:-25 * (position - 3)});
			
	});
	t.click(function(){
		d.show();
		position -= 3;
		if(position < 3)
			position = 3;
		if(position == 3)
			t.hide();
		jq(".news-cont ul").css({top:-25 * (position - 3)});
	});
});