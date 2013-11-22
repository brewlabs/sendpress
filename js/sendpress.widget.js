/* SendPress Widget Helper */

;(function ( $, window, document, undefined ) {
    this.$ = $;

    this.init = function($, document){
        
        $(document).ready(function($){
            spwidget.log("SP Widget Init Started");
         
            //Load SendPress Sections with refence to themselves :)
            spwidget.widget.init.call(spwidget.widget, $);
            
            spwidget.log("SP Widget Finished Started");

        });
    }

    this.log = function($msg){
        if(window.console !== undefined){
            console.log($msg);
        }
    } 

    this.widget = {
        init:function($){
            
            var $lists = $('.post_notifications_list');

            $lists.on('click',function(e){
            	var $list = $(this);
					$buttons = $list.closest('.widget-content').find('.meta_for_list_'+$list.data('listid')+'[type=radio]');

				if( $list.is(':checked') ){
					$buttons.removeAttr('disabled');
				}else{
					$buttons.attr('disabled', 'disabled');
				}
            });

        }
    }

    this.init( $, document);

}).call( window.spwidget=window.spwidget || {}, jQuery, window, document );