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
            
            var $lists = $('.post_notifications_list'),
            	$allbuttons = $('.meta_radio_button');

            $lists.on('click',function(e){
            	var $list = $(this),
					$rcontainer = $list.closest('.meta-radio-buttons'),
					$buttons = $rcontainer.find('.meta_radio_button');

				
				if( $list.is(':checked') ){
					$buttons.removeAttr('disabled');
				}else{
					$buttons.attr('disabled', 'disabled');
				}
            });

            $allbuttons.on('click',function(e){
            	var $radio = $(this),
            		$wbuttons = $radio.closest('.meta-radio-buttons').find('.meta_radio_button');
            	
            	$wbuttons.removeAttr('checked');
            	$radio.attr('checked', 'checked');


            });

        }
    }

    this.init( $, document);

}).call( window.spwidget=window.spwidget || {}, jQuery, window, document );