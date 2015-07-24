<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Public_View_Manage extends SendPress_Public_View {
	
	function startup(){
		
	}

	function prerender(){
		
	}

	function scripts(){
		?>
		<script>
		(function($) {
			$(".xbutton").change(function(){
				var d = {};
				rbutton = $(this);
				d['lid'] = String(rbutton.data('list'));
				d['sid'] = $('#subscriberid').val();
				d['status'] = rbutton.val();
				d['spnonce'] = spdata.nonce;
				d['action'] = 'sendpress-list-subscription';
				$.post(spdata.ajaxurl, d, function(response){
					$('.alert').slideDown('slow');
					console.log(response);
					response = $.parseJSON(response);
					$('#list_'+d['lid']).html(response.updated);
					setTimeout(function(){ $('.alert').slideUp('slow'); },2000);
				});
            	


			});

				setTimeout(function(){ $('.alert').slideUp('slow'); },1000);

			})(jQuery);	
		</script>	
		<?php
	}
		

	function html() {

		echo do_shortcode('[sp-form formid=manage]');

		
		
	}

}
