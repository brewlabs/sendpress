<?php
// SendPress Required Class: SendPress_Widget_Signup

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

/**
 * SendPress Signup Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 1.0
 */
class SendPress_Widget_Signup extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function __construct() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'sendpress', 'description' => __('Displays a signup form so your users can sign up for your public e-mail lists.', 'sendpress') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'sendpress-widget' );

		/* Create the widget. */

		parent::__construct(
	            'sendpress-widget', // Base ID
        	    __('SendPress Signup', 'sendpress'), // Name
 	           	$widget_ops,
 	           	$control_ops 
		);


	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		/* Display name from widget settings if one was input. */

		if( !array_key_exists('lists_checked', $instance) ){
			$instance['lists_checked'] = false;
		}

		$args = "";
		$args.= 'display_firstname="'.$instance['show_first'].'" ';
		$args.= 'display_lastname="'.$instance['show_last'].'" ';
		$args.= 'firstname_label="'.$instance['first_label'].'" ';
		$args.= 'lastname_label="'.$instance['last_label'].'" ';
		$args.= 'email_label="'.$instance['email_label'].'" ';
		$args.= 'list_label="'.$instance['list_label'].'" ';
		$args.= 'redirect_page="'.$instance['redirect_page'].'" ';
		$args.= 'lists_checked="'.$instance['lists_checked'].'" ';
		$args.= 'button_text="'.$instance['button_text'].'" ';
		$args.= 'thank_you="'.$instance['thank_you'].'" ';
		$args.= 'label_display="'.$instance['label_display'].'" ';
		$args.= 'desc="'.$instance['desc'].'" ';
		$args.= 'no_list_error="<div><b>-- '. __('NO LIST HAS BEEN SELECTED IN SENDPRESS WIDGET SETTINGS','sendpress'). ' --</b></div>" ';
	
		$lists = SendPress_Data::get_lists(
			array('meta_query' => array(
				array(
					'key' => 'public',
					'value' => true
				)
			)),
			false
		);
	    $listids = array();

		foreach($lists as $list){
			if( get_post_meta($list->ID,'public',true) == 1 ){
				if(isset( $instance['list_'.$list->ID] ) && filter_var($instance['list_'.$list->ID], FILTER_VALIDATE_BOOLEAN) ){
					$listids[] = $list->ID;
				}
			}
		}
	
		$args.= 'listids="'.implode(',',$listids).'" ';
		
		$args = apply_filters('sendpress_signup_widget_args', $args, $instance);
		//do_shortcode goes here
		echo do_shortcode('[sp-signup '.$args.']');

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// print_r($old_instance);
		// die();

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		// $instance['desc'] = strip_tags( $new_instance['desc'] );
		$instance['desc'] = $new_instance['desc'];

		( strlen($new_instance['first_label']) !== 0 ) ? $instance['first_label'] = strip_tags( $new_instance['first_label'] ) : $instance['first_label'] = 'First Name';
		( strlen($new_instance['last_label']) !== 0 ) ? $instance['last_label'] = strip_tags( $new_instance['last_label'] ) : $instance['last_label'] = 'Last Name';
		( strlen($new_instance['email_label']) !== 0 ) ? $instance['email_label'] = strip_tags( $new_instance['email_label'] ) : $instance['email_label'] = 'E-Mail';
		( strlen($new_instance['list_label']) !== 0 ) ? $instance['list_label'] = strip_tags( $new_instance['list_label'] ) : $instance['list_label'] = 'List Selection';
		( strlen($new_instance['button_text']) !== 0 ) ? $instance['button_text'] = strip_tags( $new_instance['button_text'] ) : $instance['button_text'] = 'Submit';
		( strlen($new_instance['thank_you']) !== 0 ) ? $instance['thank_you'] = strip_tags( $new_instance['thank_you'] ) : $instance['thank_you'] = 'Thank you for subscribing!';

		( !array_key_exists('show_first',$new_instance) ) ? $instance['show_first'] = false : $instance['show_first'] = $new_instance['show_first'];
		( !array_key_exists('show_last',$new_instance) ) ? $instance['show_last'] = false : $instance['show_last'] = $new_instance['show_last'];
		( !array_key_exists('label_display',$new_instance) ) ? $instance['label_display'] = false : $instance['label_display'] = $new_instance['label_display'];
		( !array_key_exists('redirect_page',$new_instance) ) ? $instance['redirect_page'] = false : $instance['redirect_page'] = $new_instance['redirect_page'];

		( !array_key_exists('lists_checked',$new_instance) ) ? $instance['lists_checked'] = false : $instance['lists_checked'] = $new_instance['lists_checked'];

		$instance['lists_checked'] = true;

		$args = array( 
	   		'post_type' 	=> 'sendpress_list',
	   		'numberposts'   => -1,
    		'offset'        => 0,
    		'orderby'       => 'post_title',
    		'order'         => 'DESC'
    	);
		$lists = get_posts( $args );
	    $listids = array();

		foreach($lists as $list){
			
			if( get_post_meta($list->ID,'public',true) == 1 ){
				( !array_key_exists('list_'.$list->ID,$new_instance) ) ? $instance['list_'.$list->ID] = false : $instance['list_'.$list->ID] = $new_instance['list_'.$list->ID];
			}
		}

		return apply_filters( 'sendpress_post_notifications_widget_save', $instance, $new_instance );
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 
			'title' => '', 
			'show_first' => false,
			'show_last' => false,
			'lists_checked' => true,
			'label_display' => 0,
			'first_label' => __('First Name', 'sendpress'), 
			'last_label' => __('Last Name', 'sendpress'), 
			'email_label' => __('E-Mail', 'sendpress'), 
			'list_label' => __('List Selection', 'sendpress'), 
			'desc' => '',
			'redirect_page'=>false,
			'button_text' => __('Submit', 'sendpress'),
			'thank_you' => __('Check your inbox now to confirm your subscription.', 'sendpress')
		);

		$lists = SendPress_Data::get_lists(
			array('meta_query' => array(
				array(
					'key' => 'public',
					'value' => true
				)
			)),
			false
		);

	    $listids = array();

		foreach($lists as $list){
			$defaults['list_'.$list->ID] = false;
		}

		$instance = wp_parse_args( (array) $instance, $defaults ); 

		?>


		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'sendpress'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'desc' ); ?>"><?php _e('Description:', 'sendpress'); ?></label>
			<textarea rows="5" type="text" class="widefat" id="<?php echo $this->get_field_id( 'desc' ); ?>" name="<?php echo $this->get_field_name( 'desc' ); ?>"><?php echo $instance['desc']; ?></textarea>
			<!-- <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'desc' ); ?>" name="<?php echo $this->get_field_name( 'desc' ); ?>" value="<?php echo $instance['desc']; ?>" style="width:100%;" /> -->
		</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_first'], 'on' ); ?> id="<?php echo $this->get_field_id( 'show_first' ); ?>" name="<?php echo $this->get_field_name( 'show_first' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'show_first' ); ?>"><?php _e('Collect First Name', 'sendpress'); ?></label>
		</p> 

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_last'], 'on' ); ?> id="<?php echo $this->get_field_id( 'show_last' ); ?>" name="<?php echo $this->get_field_name( 'show_last' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'show_last' ); ?>"><?php _e('Collect Last Name', 'sendpress'); ?></label>
		</p> 

		<p>
			<label for="<?php echo $this->get_field_id( 'label_display' ); ?>"><?php _e('Display labels inside','sendpress'); ?>?:</label>
			<input type="radio" name="<?php echo $this->get_field_name( 'label_display' ); ?>" value="1"<?php echo $instance['label_display'] == 1 ? ' checked' : ''; ?> /> <?php __('Yes','sendpress') ?>
			<input type="radio" name="<?php echo $this->get_field_name( 'label_display' ); ?>" value="0"<?php echo $instance['label_display'] == 0 ? ' checked' : ''; ?> /> <?php __('No','sendpress') ?>
		</p>
		<!--
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['lists_checked'], 'on' ); ?> id="<?php echo $this->get_field_id( 'lists_checked' ); ?>" name="<?php echo $this->get_field_name( 'lists_checked' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'lists_checked' ); ?>"><?php _e('Check all lists by default', 'sendpress'); ?></label>
		</p> 
		-->

		<p>
			<label for="<?php echo $this->get_field_id( 'first_label' ); ?>"><?php _e('First Name Label:', 'sendpress'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'first_label' ); ?>" name="<?php echo $this->get_field_name( 'first_label' ); ?>" value="<?php echo $instance['first_label']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'last_label' ); ?>"><?php _e('Last Name Label:', 'sendpress'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'last_label' ); ?>" name="<?php echo $this->get_field_name( 'last_label' ); ?>" value="<?php echo $instance['last_label']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'email_label' ); ?>"><?php _e('E-Mail Label:', 'sendpress'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'email_label' ); ?>" name="<?php echo $this->get_field_name( 'email_label' ); ?>" value="<?php echo $instance['email_label']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'button_text' ); ?>"><?php _e('Button Text:', 'sendpress'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'button_text' ); ?>" name="<?php echo $this->get_field_name( 'button_text' ); ?>" value="<?php echo $instance['button_text']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'list_label' ); ?>"><?php _e('Lists Label: multiple lists only', 'sendpress'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'list_label' ); ?>" name="<?php echo $this->get_field_name( 'list_label' ); ?>" value="<?php echo $instance['list_label']; ?>" style="width:100%;" />
		</p>
		<p><b><?php __('Check off the lists you would like<br>users to subscribe to','sendpress') ?>.</b></p>
		<?php 
		if( count($lists) === 0 ){
			?><p><?php
			_e('No public lists available','sendpress');
			?></p><?php
		}else{
			foreach($lists as $list){
				?>
				<p>
					<input class="checkbox" type="checkbox" <?php checked( $instance['list_'.$list->ID], 'on' ); ?> id="<?php echo $this->get_field_id( 'list_'.$list->ID ); ?>" name="<?php echo $this->get_field_name( 'list_'.$list->ID ); ?>" /> 
					<label for="<?php echo $this->get_field_id( 'list_'.$list->ID ); ?>"><?php echo $list->post_title; ?></label>
				</p> 
				<?php
			}
		}
		
		if( !is_wp_error($lists) ||  !is_wp_error($instance) || !is_wp_error($this)   ){
			
			do_action('sendpress_post_notification_widget_form',$lists, $instance, $this);
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'thank_you' ); ?>"><?php _e('Thank you message:', 'sendpress'); ?></label>
			<textarea rows="5" type="text" class="widefat" id="<?php echo $this->get_field_id( 'thank_you' ); ?>" name="<?php echo $this->get_field_name( 'thank_you' ); ?>"><?php echo $instance['thank_you']; ?></textarea>

		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'redirect_page' ); ?>"><?php _e('Thank You Page (AJAX OFF ONLY):', 'sendpress'); ?></label>
			<select name="<?php echo $this->get_field_name( 'redirect_page' ); ?>" id="<?php echo $this->get_field_id( 'redirect_page' ); ?>"> 
 <option value="0">
 	<?php $cpageid = $instance['redirect_page']; 
 	?>
<?php echo esc_attr( __( 'Default' ) ); ?></option> 
 <?php 
  $pages = get_pages(); 
  foreach ( $pages as $page ) {
  	$s ='';
  	if($cpageid == $page->ID){ $s =  "selected"; }
  	$option = '<option value="' . $page->ID .'" ' .$s. '>';
	$option .= $page->post_title;
	$option .= '</option>';
	echo $option;
  }
 ?>
</select>

		</p>

		
		<?php
		
	}
}
