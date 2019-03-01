(function() {
	tinymce.create('tinymce.plugins.StylerPlugin', {
		init : function(ed, url) {
			this.editor = ed;
			ed.onInit.add(function() {
				if(ed.settings.styles.css != undefined)
					ed.dom.loadCSS(ed.settings.styles.css);
				if(ed.settings.styles.styles == undefined || ed.settings.styles.styles.length == 0)
				{
					ed.controlManager.get("styleselect").destroy();
					return;
				}
				for(var i = 0; i < ed.settings.styles.styles.length; i++){
					var style = ed.settings.styles.styles[i];
					ed.controlManager.get("styleselect").add(style.title, style.name);
				}
			});
		},
		getInfo : function() {return {};}
	});

	tinymce.PluginManager.add('styler', tinymce.plugins.StylerPlugin);
})();