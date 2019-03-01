(function() {
	tinymce.create('tinymce.plugins.AdvancedImagePlugin', {
		init : function(ed, url) {
			ed.addCommand('mceAdvImage', function() {
				if (ed.dom.getAttrib(ed.selection.getNode(), 'class').indexOf('mceItem') != -1)
					return;
				ed.windowManager.open({
					file : '/admin/editor/list',
					width : 520,
					height : 275,
					inline:1
				},{
					name:"uploader"
				});
			});
			
			ed.addButton('image', {
				title : 'advimage.image_desc',
				cmd : 'mceAdvImage'
			});
		}
	});

	tinymce.PluginManager.add('advimage', tinymce.plugins.AdvancedImagePlugin);
})();