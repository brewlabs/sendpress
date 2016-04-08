<?php
// SendPress Required Class: SendPress_Widget_Forms

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

/**
 * SendPress Form Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 1.0
 */
class SendPress_Widget_Forms extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function __construct() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'sendpress', 'description' => __('Displays a form so your users can interact with SendPress.', 'sendpress') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'sendpress-forms-widget' );

		/* Create the widget. */
		parent::__construct(
            'sendpress-forms-widget', // Base ID
    	    __('SendPress Forms', 'sendpress'), // Name
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
		
		//do_shortcode goes here
		echo do_shortcode('[sp-form formid='.$instance['form_to_display'].']');

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );

		$instance['form_to_display'] = strip_tags( $new_instance['form_to_display'] );

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {
	$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
	$id = isset( $instance['form_to_display'] ) ? esc_attr( $instance['form_to_display'] ) : 0;


	?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'sendpress'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" style="width:100%;" />
		</p>
		
		<?php 
		$forms = SendPress_Data::get_forms_for_widget(); 
		
		if(count($forms)){
			if(count($forms) > 1){

				?>
				<p>
					<label for="<?php echo $this->get_field_id( 'form_to_display' ); ?>"><?php _e('Form to Display:', 'sendpress'); ?></label>
					<select name="<?php echo $this->get_field_name( 'form_to_display' ); ?>" id="<?php echo $this->get_field_id( 'form_to_display' ); ?>"> 

					<?php 

					  	foreach ( $forms as $form ) {
						  	$s ='';
						  	if($id == $form->ID){ $s =  "selected"; }
						  	$option = '<option value="' . $form->ID .'" ' .$s. '>';
							$option .= $form->post_title;
							$option .= '</option>';
							echo $option;
						}
					?>
					</select>
				</p>
				<?php
			}else{
				?>
				<input type="hidden" name="<?php echo $this->get_field_name( 'form_to_display' ); ?>" id="<?php echo $this->get_field_name( 'form_to_display' ); ?>" value="<?php echo $forms[0]->ID; ?>" />
				<?php
			}
		}
		
	}
}
