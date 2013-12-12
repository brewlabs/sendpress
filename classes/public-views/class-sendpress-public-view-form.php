<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Public_View_Form extends SendPress_Public_View{
	var $message = '';
	function page_start(){}

	function page_end(){}

	function save(){
		$post_options = array('list','email','firstname','lastname','status');
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
			if( isset($user_info['status']) ){
				$valid_user['status'] = $user_info['status'];
			} else {
				$valid_user['status'] = 2;
			}

			$data_error = false;
			if( isset($user_info['email']) && is_email( $user_info['email'] )){
				$valid_user['email'] = $user_info['email'];
			} else {
				$data_error = __('Invalid Email','sendpress');
			}

			if( isset($user_info['firstname']) ){
				$valid_user['firstname'] = $user_info['firstname'];
			} else {
				$valid_user['firstname'] = '';
			}
			
			if( isset($user_info['lastname']) ){
				$valid_user['lastname'] = $user_info['lastname'];
			} else {
				$valid_user['lastname'] = '';
			}
			$status = false;
			if($data_error ==  false){
				$list = implode(",", $user_info['list']);
				$custom = apply_filters('sendpress_subscribe_to_list_custom_fields', array(), $_POST);

		
				$status =  SendPress_Data::subscribe_user($list, $valid_user['email'], $valid_user['firstname'], $valid_user['lastname'], $valid_user['status'], $custom);
				if($status == false){
					$data_error = __('Problem with subscribing user.','sendpress');
				} else{
					$data_error =__('Thanks for subscribing.','sendpress');
		
				}
			} 
		}

		$this->message = $data_error;
			

			
	}

	function html() { ?>
	<html>
	<head>
		<title>Subscribe to Newsletter</title>
		<link rel='stylesheet' href="<?php echo SENDPRESS_URL;?>/css/public.0.8.7.bootstrap.min.css" type='text/css' media='all'/>
		<link rel='stylesheet' href="<?php echo SENDPRESS_URL;?>/css/style.css" type='text/css' media='all'/>
		<style>
			body{background: transparent; padding: 0;}
			input.sp-text{ height: auto; }
		</style>
	</head>
	<body>
	
	
			<form id="sendpress_signup" method="POST" >
				<input type="hidden" name="sp_list" id="list" value="<? echo $_GET['list']; ?>">
				<input type="hidden" name="sendpress" value="post">
				<?php if($this->message !=''){ ?>
					<div id="thanks"><?php echo $this->message; ?></div>
				<?php }  else { ?>
				<div id="form-wrap">
											
					<?php if(isset($_GET['f'])) { ?>
					<p name="firstname">
						<label for="firstname">First Name:</label>
						<input class="sp-text" type="text" id="sp_firstname" orig="EMail" value="" name="sp_firstname">
					</p>
					<?php } ?>
					<?php if(isset($_GET['l'])) { ?>
					<p name="lastname">
						<label for="lastname">Last Name:</label>
						<input class="sp-text" type="text" id="sp_lastname" orig="EMail" value="" name="sp_lastname">
					</p>
					<?php } ?>

					<p name="email">
						<label for="email">EMail:</label>
						<input class="sp-text" type="text" id="sp_email" orig="EMail" value="" name="sp_email">
					</p>


					<?php wp_nonce_field('sendpress-form-post','sp'); ?>
					<p class="submit">
						<input value="Submit" class="sendpress-submit" type="submit" id="submit" name="submit">
					</p>
				</div>
				<?php } ?>
			</form>
		</body>
	</html>
	<?php
		//echo do_shortcode('[sendpress-signup listids="1115"]');
		/*
		$email = $_POST['sp'];	
		//foreach()

		print_r($email);

		echo "Nice Post";
	
		/*

		$sp->track_click( $info->id , $info->report, $info->urlID , $ip  );

		$link = get_query_var('spurl');

		if( get_query_var('fxti') &&  get_query_var('spreport') ){


		$this->register_click(get_query_var('fxti'), get_query_var('spreport'), $link);

		}

		



		header( 'Location: '.$link ) ;
		*/
	}

}