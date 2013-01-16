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
		if(isset($_POST['sp'])){
			$post = $_POST['sp'];
			$result = SendPress_Data::subscribe_user($post['list'], $post['email'],'','');
			print_r($result);
			if($result){
				$this->message = "You have been sent a confirmation email.";
			} else {
				$this->message = "Sorry. We had a problem adding you please try again.";
			}
		}
			
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
				<input type="hidden" name="sp[list]" id="list" value="<? echo $_GET['list']; ?>">
				<?php if($this->message !=''){ ?>
					<div id="thanks"><?php echo $this->message; ?></div>
				<?php }  else { ?>
				<div id="form-wrap">
										
					<p name="email">
													<label for="email">EMail:</label>
												<input class="sp-text" type="text" id="sp-email" orig="EMail" value="" name="sp[email]">
					</p>

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