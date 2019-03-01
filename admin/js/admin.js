formUrl = "/admin/form/";

var timeoutHandler;
function showOk(){
	jq(".ok-panel").show(200, function(){
		clearTimeout(timeoutHandler);
		timeoutHandler = setTimeout(function(){
			jq(".ok-panel").hide();
		}, 2000);
	});
}
function showPhotoWindow(o){
	var width = parseInt(o.width);
	var height = parseInt(o.height);
	var ww = 0;
	var hh = 0;
	if(width < 600)
	{
		ww = width
		hh = height;
	}else{
		ww = 600;
		hh = height * 600 / width;
	}
	var x = (jq(window).width() - ww) /2;
	w.css({width:ww, height:hh, display:"block", left:x});
	w.empty();
	w.append("<img src='/images/uploaded/original" + o.id + ".jpg' width='" + ww + "' height='" + hh + "'>");
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

Article = {
	data:{},
	init:function(id){
		if(Article.data[id] != undefined)
			return Article.data[id];
		var o = {};
		o.container = jq("#article" + id);
		var pl = jq("#plus" + id);
		o.plus = jq("img[src*=plus]", pl);
		o.minus = jq("img[src*=minus]", pl);
		var buttons = jq(".buttons", o.container).eq(0);
		o.publ = jq(".publ", buttons);
		o.unpubl = jq(".unpubl", buttons);
		o.del = jq(".del", buttons);
		o.down = jq(".down", buttons);
		o.up = jq(".up", buttons);
		o.ord = jq(".count", buttons);
		o.sub = jq("#sub" + id);
		Article.data[id] = o;
		return Article.data[id];
	},
	publish:function(id){
		var d = Article.init(id);
		jq.get("/admin/article/publish/" + id  + "/?empty=1");
		d.publ.hide();
		d.unpubl.show();
		return false;
	},
	unpublish:function(id){
		var d = Article.init(id);
		jq.get("/admin/article/publish/" + id  + "/?empty=1");
		d.publ.show();
		d.unpubl.hide();
		return false;
	},
	del:function(id){
		var d = Article.init(id);
		if(!confirm("Вы действительно хотите удалить статью?"))
			return;
		if(d.sub.length > 0)
			d.sub.remove();
		d.container.remove();
		jq.get("/admin/article/delete/" + id  + "/?empty=1");
	},
	up:function(id){
		var d = Article.init(id);
		Article.setOrder(id, -1);
	},
	down:function(id){
		var d = Article.init(id);
		Article.setOrder(id, 1);
	},
	setOrder:function(id, dif){
		var d = Article.init(id);
		var order = parseInt(d.ord.html());
		var newV = order + dif;
		if(newV < 0)
			return;
		d.ord.html("" + newV)
		jq.get("/admin/article/ord/" + id + "/" + newV  + "/?empty=1");
	},
	plus:function(id){
		var d = Article.init(id);
		d.minus.show();
		d.plus.hide();
		d.sub.show();
	},
	minus:function(id){
		var d = Article.init(id);
		d.minus.hide();
		d.plus.show();
		d.sub.hide();
	}
};
News = {
	data:{},
	init:function(id){
		if(News.data[id] != undefined)
			return News.data[id];
		var o = {};
		o.container = jq("#news" + id);
		o.del = jq(".del", o.container);
		o.publ = jq(".publ", o.container);
		o.unpubl = jq(".unpubl", o.container);
		News.data[id] = o;
		return News.data[id];
	},
	del:function(id){
		var d = News.init(id);
		if(!confirm("Вы действительно хотите удалить новость?"))
			return;
		d.container.remove();
		jq.get("/admin/news/delete/" + id  + "/?empty=1");
	},
	publ:function(id){
		var d = News.init(id);
		jq.get("/admin/news/publish/" + id  + "/?empty=1");
		d.publ.hide();
		d.unpubl.show();
		return false;
	},
	unpubl:function(id){
		var d = News.init(id);
		jq.get("/admin/news/publish/" + id  + "/?empty=1");
		d.publ.show();
		d.unpubl.hide();
		return false;
	}
};

Projects = {
	data:{},
	init:function(id){
		if(Projects.data[id] != undefined)
			return Projects.data[id];
		var o = {};
		o.container = jq("#project" + id);
		o.del = jq(".del", o.container);
		o.publ = jq(".publ", o.container);
		o.unpubl = jq(".unpubl", o.container);
		Projects.data[id] = o;
		return Projects.data[id];
	},
	del:function(id){
		var d = Projects.init(id);
		if(!confirm("Вы действительно хотите удалить проект?"))
			return;
		d.container.remove();
		jq.get("/admin/projects/delete/" + id  + "/?empty=1");
	},
	publ:function(id){
		var d = Projects.init(id);
		jq.get("/admin/projects/publish/" + id  + "/?empty=1");
		d.publ.hide();
		d.unpubl.show();
		return false;
	},
	unpubl:function(id){
		var d = Projects.init(id);
		jq.get("/admin/projects/publish/" + id  + "/?empty=1");
		d.publ.show();
		d.unpubl.hide();
		return false;
	}
};

Catalog = {
	data:{},
	init:function(id){
		if(Catalog.data[id] != undefined)
			return Catalog.data[id];
		var o = {};
		o.container = jq("#product" + id);
		o.del = jq(".del", o.container);
		o.publ = jq(".publ", o.container);
		o.unpubl = jq(".unpubl", o.container);
		Catalog.data[id] = o;
		return Catalog.data[id];
	},
	del:function(id){
		var d = Catalog.init(id);
		if(!confirm("Вы действительно хотите удалить продукт?"))
			return;
		d.container.remove();
		jq.get("/admin/catalog/category/delete/" + id  + "/?empty=1");
	},
	publ:function(id){
		var d = Catalog.init(id);
		jq.get("/admin/catalog/category/publish/" + id  + "/?empty=1");
		d.publ.hide();
		d.unpubl.show();
		return false;
	},
	unpubl:function(id){
		var d = Catalog.init(id);
		jq.get("/admin/catalog/category/publish/" + id  + "/?empty=1");
		d.publ.show();
		d.unpubl.hide();
		return false;
	}
};

Role = {
	data:{},
	init:function(id){
		if(Role.data[id] != undefined)
			return Role.data[id];
		var o = {};
		o.container = jq("#role" + id);
		o.publ = jq(".publ", o.container);
		o.unpubl = jq(".unpubl", o.container);
		Role.data[id] = o;
		return Role.data[id];
	},
	del:function(id){
		var d = Role.init(id);
		if(!confirm("Вы действительно хотите удалить роль?"))
			return;
		d.container.remove();
		jq.get("/admin/users/roles/delete/" + id  + "/?empty=1");
	}
};

User = {
	data:{},
	init:function(id){
		if(User.data[id] != undefined)
			return User.data[id];
		var o = {};
		o.container = jq("#user" + id);
		o.del = jq(".del", o.container);
		o.publ = jq(".publ", o.container);
		o.unpubl = jq(".unpubl", o.container);
		User.data[id] = o;
		return User.data[id];
	},
	del:function(id){
		var d = User.init(id);
		if(!confirm("Вы действительно хотите удалить пользователя?"))
			return;
		d.container.remove();
		jq.get("/admin/users/delete/" + id  + "/?empty=1");
	},
	publ:function(id){
		var d = User.init(id);
		jq.get("/admin/users/block/" + id  + "/?empty=1");
		d.publ.hide();
		d.unpubl.show();
		return false;
	},
	unpubl:function(id){
		var d = User.init(id);
		jq.get("/admin/users/block/" + id  + "/?empty=1");
		d.publ.show();
		d.unpubl.hide();
		return false;
	}
};

NewsCategory = {
	data:{},
	init:function(id){
		if(NewsCategory.data[id] != undefined)
			return NewsCategory.data[id];
		var o = {};
		o.container = jq("#category" + id);
		var buttons = jq(".buttons", o.container).eq(0);
		o.del = jq(".del", buttons);
		NewsCategory.data[id] = o;
		return NewsCategory.data[id];
	},
	del:function(id){
		var d = NewsCategory.init(id);
		if(!confirm("Вы действительно хотите удалить категорию новостей?"))
			return;
		d.container.remove();
		jq.get("/admin/news/categories/delete/" + id  + "/?empty=1");
	}
};

ProjectsCategory = {
	data:{},
	init:function(id){
		if(ProjectsCategory.data[id] != undefined)
			return ProjectsCategory.data[id];
		var o = {};
		o.container = jq("#category" + id);
		var buttons = jq(".buttons", o.container).eq(0);
		o.del = jq(".del", buttons);
		ProjectsCategory.data[id] = o;
		return ProjectsCategory.data[id];
	},
	del:function(id){
		var d = ProjectsCategory.init(id);
		if(!confirm("Вы действительно хотите удалить категорию проектов?"))
			return;
		d.container.remove();
		jq.get("/admin/projects/categories/delete/" + id  + "/?empty=1");
	}
};

Gallery = {
	data:{},
	init:function(id){
		if(Gallery.data[id] != undefined)
			return Gallery.data[id];
		var o = {};
		o.container = jq("#gallery" + id);
		var buttons = jq(".buttons", o.container).eq(0);
		o.del = jq(".del", buttons);
		Gallery.data[id] = o;
		return Gallery.data[id];
	},
	del:function(id){
		var d = Gallery.init(id);
		if(!confirm("Вы действительно хотите удалить галлерею?"))
			return;
		d.container.remove();
		jq.get("/admin/photo/delete/" + id  + "/?empty=1");
	}
};


CatalogCategory = {
	data:{},
	init:function(id){
		if(CatalogCategory.data[id] != undefined)
			return CatalogCategory.data[id];
		var o = {};
		o.container = jq("#category" + id);
		var pl = jq("#plus" + id);
		o.plus = jq("img[src*=plus]", pl);
		o.minus = jq("img[src*=minus]", pl);
		var buttons = jq(".buttons", o.container).eq(0);
		o.publ = jq(".publ", buttons);
		o.unpubl = jq(".unpubl", buttons);
		o.del = jq(".del", buttons);
		o.down = jq(".down", buttons);
		o.up = jq(".up", buttons);
		o.ord = jq(".count", buttons);
		o.sub = jq("#sub" + id);
		CatalogCategory.data[id] = o;
		return CatalogCategory.data[id];
	},
	publish:function(id){
		var d = CatalogCategory.init(id);
		jq.get("/admin/catalog/categories/publish/" + id  + "/?empty=1");
		d.publ.hide();
		d.unpubl.show();
		return false;
	},
	unpublish:function(id){
		var d = CatalogCategory.init(id);
		jq.get("/admin/catalog/categories/publish/" + id  + "/?empty=1");
		d.publ.show();
		d.unpubl.hide();
		return false;
	},
	del:function(id){
		var d = CatalogCategory.init(id);
		if(!confirm("Вы действительно хотите удалить категорию?"))
			return;
		if(d.sub.length > 0)
			d.sub.remove();
		d.container.remove();
		jq.get("/admin/catalog/categories/delete/" + id  + "/?empty=1");
	},
	up:function(id){
		var d = CatalogCategory.init(id);
		CatalogCategory.setOrder(id, -1);
	},
	down:function(id){
		var d = CatalogCategory.init(id);
		CatalogCategory.setOrder(id, 1);
	},
	setOrder:function(id, dif){
		var d = CatalogCategory.init(id);
		var order = parseInt(d.ord.html());
		var newV = order + dif;
		if(newV < 0)
			return;
		d.ord.html("" + newV)
		jq.get("/admin/catalog/categories/ord/" + id + "/" + newV  + "/?empty=1");
	},
	plus:function(id){
		var d = CatalogCategory.init(id);
		d.minus.show();
		d.plus.hide();
		d.sub.show();
	},
	minus:function(id){
		var d = CatalogCategory.init(id);
		d.minus.hide();
		d.plus.show();
		d.sub.hide();
	}
};


ReferenceCategory = {
		data:{},
		init:function(id){
			if(ReferenceCategory.data[id] != undefined)
				return ReferenceCategory.data[id];
			var o = {};
			o.container = jq("#category" + id);
			var pl = jq("#plus" + id);
			o.plus = jq("img[src*=plus]", pl);
			o.minus = jq("img[src*=minus]", pl);
			var buttons = jq(".buttons", o.container).eq(0);
			o.publ = jq(".publ", buttons);
			o.unpubl = jq(".unpubl", buttons);
			o.del = jq(".del", buttons);
			o.down = jq(".down", buttons);
			o.up = jq(".up", buttons);
			o.ord = jq(".count", buttons);
			o.sub = jq("#sub" + id);
			ReferenceCategory.data[id] = o;
			return ReferenceCategory.data[id];
		},
		publish:function(id){
			var d = ReferenceCategory.init(id);
			jq.get("/admin/reference/references/publish/" + id  + "/?empty=1");
			d.publ.hide();
			d.unpubl.show();
			return false;
		},
		unpublish:function(id){
			var d = ReferenceCategory.init(id);
			jq.get("/admin/reference/references/publish/" + id  + "/?empty=1");
			d.publ.show();
			d.unpubl.hide();
			return false;
		},
		del:function(id){
			var d = ReferenceCategory.init(id);
			if(!confirm("Вы действительно хотите удалить раздел?"))
				return;
			if(d.sub.length > 0)
				d.sub.remove();
			d.container.remove();
			jq.get("/admin/reference/references/delete/" + id  + "/?empty=1");
		},
		up:function(id){
			var d = ReferenceCategory.init(id);
			ReferenceCategory.setOrder(id, -1);
		},
		down:function(id){
			var d = ReferenceCategory.init(id);
			ReferenceCategory.setOrder(id, 1);
		},
		setOrder:function(id, dif){
			var d = ReferenceCategory.init(id);
			var order = parseInt(d.ord.html());
			var newV = order + dif;
			if(newV < 0)
				return;
			d.ord.html("" + newV)
			jq.get("/admin/reference/references/ord/" + id + "/" + newV  + "/?empty=1");
		},
		plus:function(id){
			var d = ReferenceCategory.init(id);
			d.minus.show();
			d.plus.hide();
			d.sub.show();
		},
		minus:function(id){
			var d = ReferenceCategory.init(id);
			d.minus.hide();
			d.plus.show();
			d.sub.hide();
		}
};

Reference = {
		data:{},
		init:function(id){
			if(Reference.data[id] != undefined)
				return Reference.data[id];
			var o = {};
			o.container = jq("#product" + id);
			o.del = jq(".del", o.container);
			o.publ = jq(".publ", o.container);
			o.unpubl = jq(".unpubl", o.container);
			Reference.data[id] = o;
			return Reference.data[id];
		},
		del:function(id){
			var d = Reference.init(id);
			if(!confirm("Вы действительно хотите удалить?"))
				return;
			d.container.remove();
			jq.get("/admin/reference/category/delete/" + id  + "/?empty=1");
		},
		publ:function(id){
			var d = Reference.init(id);
			jq.get("/admin/reference/category/publish/" + id  + "/?empty=1");
			d.publ.hide();
			d.unpubl.show();
			return false;
		},
		unpubl:function(id){
			var d = Reference.init(id);
			jq.get("/admin/reference/category/publish/" + id  + "/?empty=1");
			d.publ.show();
			d.unpubl.hide();
			return false;
		}
};