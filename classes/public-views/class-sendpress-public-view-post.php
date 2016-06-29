<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Public_View_Post extends SendPress_Public_View{
	
	function page_start(){}

	function page_end(){}

	function html() {

		if(isset($_POST['sp-shortcode']) && (strpos($_POST['sp-shortcode'], 'SC-') !== false )){
			$cls = str_replace('-', '_',	trim($_POST['sp-shortcode']) );
			call_user_func(array('SendPress_'. $cls, "form_post"), '');
		
		} else {


		$post_options = array('list','email','firstname','lastname','return','status');
		$user_info = array();
		foreach ($post_options as $opt) {
			$user_info[$opt] = isset($_POST['sp_' . $opt]) ?  $_POST['sp_' . $opt]: false ;
		}

		$valid_user = array();
		//foreach()
		if(isset($user_info['list'])){

			if(!is_array($user_info['list'])){
				$user_info['list'] = array($user_info['list']);
			}

			$data_error = false;
			if( $user_info['status'] !== false ){
				$valid_user['status'] = SPNL()->validate->int( $user_info['status'] );
			} else {
				$valid_user['status'] = 2;
			}

			if( $user_info['email'] !== false && is_email( $user_info['email'] )){
				$valid_user['email'] = $user_info['email'];
			} else {
				$data_error = __('Invalid Email','sendpress');
			}

			if( $user_info['firstname'] !== false ){
				$valid_user['firstname'] =  sanitize_text_field( $user_info['firstname'] );
			} else {
				$valid_user['firstname'] = '';
			}
			
			if( $user_info['lastname'] !== false ){
				$valid_user['lastname'] =  sanitize_text_field( $user_info['lastname'] );
			} else {
				$valid_user['lastname'] = '';
			}
			$status = false;
			
			if($data_error ==  false){
				$list = implode(",", $user_info['list']);
				$custom = '';
				$post_notifications = SPNL()->validate->_string('post_notifications');
				if( $post_notifications ){
					$custom['post_notifications'] = $post_notifications;
				}

				//$custom = apply_filters('sendpress_subscribe_to_list_custom_fields', array(), $_POST);

		
				$status =  SendPress_Data::subscribe_user($list, $valid_user['email'], $valid_user['firstname'], $valid_user['lastname'], $valid_user['status'], $custom);
				if($status == false){
					$data_error = __('Problem with subscribing user.','sendpress');
				}
			} 

			
			$post_responce = get_post_meta($user_info['list'][0], 'post-page', true);
			if($post_responce == false){
				$post_responce = 'default';
			}
			if($user_info['return'] != false){
				$post_responce = $user_info['return'];
			}

		
			$optin = SendPress_Option::is_double_optin();

			if(isset($_POST['redirect']) && $_POST['redirect'] > 0 ){
				$plink = get_permalink( $_POST['redirect'] );
							if($plink != ""){
								wp_redirect( esc_url_raw( $plink ) );
								exit();
							}
			}


			switch($post_responce){

				case "json": //Respond to post with json data
					if( $status == false || $data_error != false  ){
						// { success: true/false, list: listid , name: listname, optin: true/false }
						$info= array(
							"success" => false,
							"error" => $data_error,
							"list" => $user_info['list'],
							);



					}
					if($status){
						$info= array(
							"success" => true,
							"error" => $data_error,
							"list" => $user_info['list'],
							"optin" => $optin,
							"email"=> $valid_user['email']
							);



					}
					$encoded = json_encode($info);
					header('Content-type: application/json');
					exit($encoded);
				break;
				case "custom":
					$post_redirect = get_post_meta($user_info['list'][0], 'post-page-id', true);
						if($post_redirect == false){
							$post_redirect = site_url();
						} else {
							$plink = get_permalink($post_redirect);
							if($plink != ""){
								wp_safe_redirect( esc_url_raw( $plink) );
								exit();
							}
						}

						wp_redirect( esc_url_raw( $post_redirect ));
						exit();
				break;
				case "redirect":
						$post_redirect = get_post_meta($user_info['list'][0], 'post-redirect', true);
						if($post_redirect == false){
							$post_redirect = site_url();
						}

						wp_redirect( esc_url_raw ( $post_redirect ) );
						exit();

				break;

				default:

				$this->default_page($status , $data_error );
			}





				





		} else {
			SendPress_Public_View::page_start();
			?>
			<div class="span12">
				<div class='area'>
					<h1><?php _e('You must provide a list ID for the post page to work','sendpress'); ?>.</h1>
				</div>
			</div>
			<?php
			SendPress_Public_View::page_end();



		}
		}

		
	}



		function default_page($status, $error){
			SendPress_Public_View::page_start();

			?>
		
					<?php if( $status == true && $error == false){ ?>
						<h1><?php _e('Thank You for subscribing','sendpress'); ?>.</h1>

					<?php } else { ?>
						<h1><?php _e('Sorry, We had a Problem adding your email','sendpress'); ?>.</h1>


					<?php } ?>



				
			<?php
			SendPress_Public_View::page_end();

		}


}