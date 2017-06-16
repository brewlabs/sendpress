<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_Module_Pro extends SendPress_Module{
	
	function html(){

		$key_active = false;
		if( get_transient( 'sendpress_key_state' ) === 'valid' ){
			$key_active = true;
		}
		// SendPress_Helper::log('API Key = '.SendPress_Option::get('api_key'));
		// SendPress_Helper::log('API State = '.get_transient( 'sendpress_key_state' ));
		
		$key = SendPress_Option::get('api_key');
		if(empty($key) || $key == '' ){
			$key_active = false;
			delete_transient( 'sendpress_key_state' );
		}

		?>
		<h4><?php _e('SendPress API Key','sendpress');?></h4>
		

		<form method="post" id="post">
			
			
			<?php if($key_active){
				echo '<span class="icon-ok-sign"></span>';
			}?>
			<input <?php if($key_active){ echo 'disabled'; } ?> name="api_key" type="text" id="api_key" value="<?php echo SendPress_Option::get('api_key'); ?>" class="regular-text sp-text">
			<?php if( !$key_active ): ?>
				<a href="#" class="save-api-key btn-success  btn"><?php _e('Register Key','sendpress');?></a>
			<?php else: ?>
				<a href="#" class="save-api-key btn-danger btn"><?php _e('Deactivate Key','sendpress');?></a>
			<?php endif; ?>
			<div class="description">
				<?php echo sprintf(	__( 'Enter your API key to enable premium support and automatic updates. Get your API key by logging into <a href="%s">SendPress.com</a>.','sendpress' ), 'http://sendpress.com' ); ?>
			</div>
			<input class="action " type="hidden" name="action" value="<?php if($key_active){ echo 'module-deactivate-api-key'; }else{ echo 'module-save-api-key'; }?>" />
			<?php wp_nonce_field($this->_nonce_value); ?>
		</form>
		
	<?php
	}

	function buttons($plugin_path){
		
		switch( $this->pro_plugin_state() ){
			case 'installable':
				$button = array(
					'class' => 'btn btn-success btn-activate', 
					'href' => wp_nonce_url(admin_url('update.php?action=install-plugin&plugin=sendpress-pro'), 'install-plugin_sendpress-pro'), 
					'target' => '', 
					'text' => __('Install Pro','sendpress')
				);
				break;
			case 'not-installed':
				$button = array(
					'class' => 'btn-primary btn-buy btn', 
					'href' => 'http://www.sendpress.com/pricing/', 
					'target' => '_blank', 
					'text' => __('Buy Now','sendpress')
				);
				break;
			case 'activated':
				$button = array(
					'class' => 'btn btn-default module-deactivate-plugin', 
					'href' => '#', 
					'target' => '', 
					'text' => __('Deactivate','sendpress')
				);
				break;
			case 'installed':
				$button = array(
					'class' => 'module-activate-plugin btn-success btn-activate btn', 
					'href' => '#',
					'target' => '',
					'text' => __('Activate','sendpress')
				);
				break;
		}

			
		$btn = $this->build_button($button);
		
		
		echo '<div class="inline-buttons">'.$btn.'</div>';
	}

	function module_start(){
		echo '<div class="sendpress-module pro-module">';
		echo '<div class="inner-module">';
	}

}
