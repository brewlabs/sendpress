(function($) {
	tinymce.create('tinymce.plugins.MailMerge', {
		init : function(ed, url) {
			spadmin.current_ed = ed;
			ed.addButton('mailmerge', {
				title : 'SendPress',
				image : url+'/icon.png',
				onclick : function() {
					idPattern = /(?:(?:[^v]+)+v.)?([^&=]{11})(?=&|$)/;
					//var vidId = prompt("YouTube Video", "Enter the id or url for your video");
					//var m = idPattern.exec(vidId);
					//if (m != null && m != 'undefined')
					
					$('#sendpress-helper').modal('show');
						//ed.execCommand('mceInsertContent', false, '[youtube id=""]');
				}
			});
		},
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
				longname : "MailMerge Shortcodes",
				author : 'Josh Lyford',
				authorurl : 'http://sendpress.com/',
				infourl : 'http://sendpress.com/',
				version : "1.0"
			};
		}
	});
	tinymce.PluginManager.add('mailmerge', tinymce.plugins.MailMerge);
})(jQuery);