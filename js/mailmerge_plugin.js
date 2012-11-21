(function() {
	tinymce.create('tinymce.plugins.SendPress', {
		init : function(ed, url) {
			spadmin.current_ed = ed;
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('WP_SP', function() {
				jQuery('#sendpress-helper').modal('show');
				}, {
					plugin_url : url // Plugin absolute URL
				});
		

			ed.addButton('sendpress', {
				title : 'SendPress',
				image : url + '/icon.png',
				cmd : 'WP_SP'
			});
		},
		
		getInfo : function() {
			return {
				longname : "SendPressShortcodes",
				author : 'Josh Lyford',
				authorurl : 'http://sendpress.com/',
				infourl : 'http://sendpress.com/',
				version : "1.0"
			};
		}
	});
	tinymce.PluginManager.add('sendpress', tinymce.plugins.SendPress);
})();