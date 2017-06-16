<?php

class SendPress_Email_Customizer {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = 'sendpress';
		$this->version = $version;
		$this->defaults = $this->defaults_f();

	}

	public static function defaults_f() {
		return apply_filters( 'sendpress/defaults_opts', array(
			'from_name'         => get_bloginfo('name'),
			'from_email'        => get_bloginfo('admin_email'),
			'template'          => 'boxed',
			'body_bg'           => '#e3e3e3',
			'body_size'         => '680',
			'footer_text'       => '&copy;'.date('Y').' ' .get_bloginfo('name'),
			'footer_aligment'   => 'center',
			'footer_bg'         => '#eee',
			'footer_text_size'  => '12',
			'footer_text_color' => '#777',
			'footer_powered_by' => 'off',
			'header_aligment'   => 'center',
			'header_bg'         => '#454545',
			'header_text_size'  => '30',
			'header_text_color' => '#f1f1f1',
			'email_body_bg'     => '#fafafa',
			'body_text_size'    => '14',
			'body_text_color'   => '#888',
		));
	}

	/**
	 * Add all panels to customizer
	 * @param $wp_customize
	 */
	public function register_customize_sections( $wp_customize ){

		$wp_customize->add_panel( 'sendpress', array(
			'title'         => __( 'SendPress Templates', $this->plugin_name ),
			'description'   => __( 'Within the Email Templates customizer you can change how your WordPress Emails looks. It\'s fully compatible with WooCommerce and Easy Digital Downloads html emails', $this->plugin_name ),
		) );

		do_action('sendpress/sections/before', $wp_customize );
		// Add sections
		$wp_customize->add_section( 'section_sendpress_settings', array(
			'title' => __( 'Settings', $this->plugin_name ),
			'panel' => 'sendpress',
		) );
		$wp_customize->add_section( 'section_sendpress_template', array(
			'title' => __( 'Template', $this->plugin_name ),
			'panel' => 'sendpress',
		) );
		$wp_customize->add_section( 'section_sendpress_header', array(
			'title' => __( 'Email Header', $this->plugin_name ),
			'panel' => 'sendpress',
		) );
		$wp_customize->add_section( 'section_sendpress_body', array(
			'title' => __( 'Email Body', $this->plugin_name ),
			'panel' => 'sendpress',
		) );
		$wp_customize->add_section( 'section_sendpress_footer', array(
			'title' => __( 'Footer', $this->plugin_name ),
			'panel' => 'sendpress',
		) );
		$wp_customize->add_section( 'section_sendpress_test', array(
			'title' => __( 'Send test email', $this->plugin_name ),
			'panel' => 'sendpress',
		) );
		// Populate sections
		$this->settings_section( $wp_customize );
		$this->template_section( $wp_customize );
		$this->header_section( $wp_customize );
		$this->body_section( $wp_customize );
		$this->footer_section( $wp_customize );
		$this->test_section( $wp_customize );

		do_action('sendpress/sections/after', $wp_customize );

	}

	/**
	 * Remover other sections
	 * @param $active
	 * @param $section
	 *
	 * @return bool
	 */
	public function remove_other_sections( $active, $section ) {
		if ( isset( $_GET['sendpress_display'] ) ) {
			if (
				in_array( $section->id,
					apply_filters( 'sendpress/customizer_sections',
							array(  'section_sendpress_footer',
									'section_sendpress_template',
									'section_sendpress_header',
									'section_sendpress_body',
									'section_sendpress_test',
									'section_sendpress_settings'
							)
					)
				)
			) {
				return true;
			}
			return false;
		}
		return true;
	}

	/**
	 * Remover other panels
	 * @param $active
	 * @param $panel
	 *
	 * @return bool
	 */
	public function remove_other_panels( $active, $panel ){
		if ( isset( $_GET['sendpress_display'] ) ) {
			if ( 'sendpress' == $panel->id ) {
				return true;
			}
			return false;
		}
		return true;
	}
	/**
	 * Here we capture the page and show template acordingly
	 * @param $template
	 *
	 * @return string
	 */
	public function capture_customizer_page( $template ){
		if( is_customize_preview() && isset( $_GET['sendpress_display'] ) && 'true' == $_GET['sendpress_display'] ){
			return apply_filters( 'sendpress/customizer_template', SENDPRESS_PATH . "/templates/master.php");
		}
		return $template;
	}


	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'sendpress-customizer-js', SENDPRESS_PLUGIN_URL . '/js/customizer-admin.js', '', $this->version, false );

	}

	/**
	 * Enqueue scripts for preview area
	 * @since 1.0.0
	 */
	public function enqueue_template_scripts(){
		wp_enqueue_script( 'sendpress-customizer-front-js', SENDPRESS_PLUGIN_URL . '/admin/js/sendpress-public.js', array(  'jquery', 'customize-preview' ), $this->version, true );
		wp_enqueue_style( 'sendpress-customizer-css', SENDPRESS_PLUGIN_URL . '/admin/css/sendpress-admin.css', '', $this->version, false );
	}

	/**
	 * Template Section
	 * @param $wp_customize WP_Customize_Manager
	 */
	private function settings_section($wp_customize) {
		$email_id = SPNL()->validate->_int(  'spemail'  );
		do_action('sendpress/sections/settings/before_content', $wp_customize);

		$wp_customize->add_setting( 'sendpress_opts[from_name]', array(
			'type'                  => 'option',
			'default'               => $this->defaults['from_name'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'sanitize_text_field',
			'sanitize_js_callback'  => '',
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize,
			'sendpress_from_name', array(
				'label'         => __( 'From name', $this->plugin_name ),
				'type'          => 'text',
				'section'       => 'section_sendpress_settings',
				'settings'      => 'sendpress_opts[from_name]',
				'description'   => __('Default: ', $this->plugin_name ) . get_bloginfo('name')
			)
		) );

		$wp_customize->add_setting( 'sendpress_opts[from_email]', array(
			'type'                  => 'option',
			'default'               => $this->defaults['from_email'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'sanitize_text_field',
			'sanitize_js_callback'  => '',
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize,
			'sendpress_from_email', array(
				'label'         => __( 'From Email', $this->plugin_name ),
				'type'          => 'text',
				'section'       => 'section_sendpress_settings',
				'settings'      => 'sendpress_opts[from_email]',
				'description'   => __('Default: ', $this->plugin_name ) . get_bloginfo('admin_email')
			)
		) );


		do_action('sendpress/sections/settings/after_content', $wp_customize);
	}


	/**
	 * Template Section
	 * @param $wp_customize WP_Customize_Manager
	 */
	private function template_section($wp_customize) {
		//require_once sendpress_PLUGIN_DIR . '/includes/customize-controls/class-font-size-customize-control.php';
		do_action('sendpress/sections/template/before_content', $wp_customize);

		$wp_customize->add_setting( 'sendpress_opts[template]', array(
			'type'                  => 'option',
			'default'               => $this->defaults['template'],
			'transport'             => 'refresh',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => array( $this, 'sanitize_templates'),
			'sanitize_js_callback'  => '',
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize,
			'sendpress_template', array(
				'label'         => __( 'Choose one', $this->plugin_name ),
				'type'          => 'select',
				'section'       => 'section_sendpress_template',
				'settings'      => 'sendpress_opts[template]',
				'choices'       => apply_filters( 'sendpress/template_choices', array(
					'boxed'    => 'Boxed',
					'fullwidth' => 'Fullwidth'
				)),
				'description'   => ''
			)
		) );
		// body size
		$wp_customize->add_setting( 'sendpress_opts[body_size]', array(
			'type'                  => 'option',
			'default'               => $this->defaults['body_size'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => array( $this,'sanitize_text'),
			'sanitize_js_callback'  => '',
		) );
		$wp_customize->add_control( new SendPress_Font_Customizer( $wp_customize,
			'sendpress_body_size', array(
				'label'         => __( 'Email body size', $this->plugin_name ),
				'section'       => 'section_sendpress_template',
				'settings'      => 'sendpress_opts[body_size]',
				'description'   => __( 'Choose boxed size', $this->plugin_name )
			)
		) );
		// body bg
		$wp_customize->add_setting( 'sendpress_opts[body_bg]', array(
			'type'                  => 'option',
			'default'               => $this->defaults['body_bg'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'sanitize_hex_color',
			'sanitize_js_callback'  => '',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize,
			'sendpress_body_bg', array(
				'label'         => __( 'Background Color', $this->plugin_name ),
				'section'       => 'section_sendpress_template',
				'settings'      => 'sendpress_opts[body_bg]',
				'description'   => __( 'Choose email background color', $this->plugin_name )
			)
		) );
		do_action('sendpress/sections/template/after_content', $wp_customize);
	}


	/**
	 * Header section
	 * @param $wp_customize WP_Customize_Manager
	 */
	private function header_section( $wp_customize ) {
		//require_once sendpress_PLUGIN_DIR . '/includes/customize-controls/class-font-size-customize-control.php';
		do_action('sendpress/sections/header/before_content', $wp_customize);

		// image logo
		$wp_customize->add_setting( 'sendpress_opts[header_logo]', array(
			'type'                  => 'option',
			'default'               => '',
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => '',
			'sanitize_js_callback'  => '',
		) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize,
			'sendpress_header', array(
				'label'         => __( 'Logo', $this->plugin_name ),
				'type'          => 'image',
				'section'       => 'section_sendpress_header',
				'settings'      => 'sendpress_opts[header_logo]',
				'description'   => __( 'Add an image to use in header. Leave empty to use text instead', $this->plugin_name )
			)
		) );

		// image logo
		$wp_customize->add_setting( 'sendpress_opts[header_logo_text]', array(
			'type'                  => 'option',
			'default'               => '',
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => array( $this,'sanitize_text'),
			'sanitize_js_callback'  => '',
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize,
			'sendpress_header_logo_text', array(
				'label'         => __( 'Logo', $this->plugin_name ),
				'type'          => 'textarea',
				'section'       => 'section_sendpress_header',
				'settings'      => 'sendpress_opts[header_logo_text]',
				'description'   => __( 'Add text to your mail header', $this->plugin_name )
			)
		) );
		// header alignment
		$wp_customize->add_setting( 'sendpress_opts[header_aligment]', array(
			'type'                  => 'option',
			'default'               => $this->defaults['header_aligment'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => array( $this,'sanitize_alignment'),
			'sanitize_js_callback'  => '',
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize,
			'sendpress_aligment', array(
				'label'         => __( 'Aligment', $this->plugin_name ),
				'type'          => 'select',
				'default'       => 'center',
				'choices'       => array(
					'left'  => 'Left',
					'center'=> 'Center',
					'right' => 'Right'
				),
				'section'       => 'section_sendpress_header',
				'settings'      => 'sendpress_opts[header_aligment]',
				'description'   => __( 'Choose alignment for header', $this->plugin_name )
			)
		) );

		// background color
		$wp_customize->add_setting( 'sendpress_opts[header_bg]', array(
			'type'                  => 'option',
			'default'               => $this->defaults['header_bg'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'sanitize_hex_color',
			'sanitize_js_callback'  => '',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize,
			'sendpress_header_bg', array(
				'label'         => __( 'Background Color', $this->plugin_name ),
				'section'       => 'section_sendpress_header',
				'settings'      => 'sendpress_opts[header_bg]',
				'description'   => __( 'Choose header background color', $this->plugin_name )
			)
		) );
		// text size
		$wp_customize->add_setting( 'sendpress_opts[header_text_size]', array(
			'type'                  => 'option',
			'default'               => $this->defaults['header_text_size'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => array( $this,'sanitize_text'),
			'sanitize_js_callback'  => '',
		) );
		$wp_customize->add_control( new SendPress_Font_Customizer( $wp_customize,
			'sendpress_header_text_size', array(
				'label'         => __( 'Text size', $this->plugin_name ),
				'type'          => 'sendpress_send_mail',
				'section'       => 'section_sendpress_header',
				'settings'      => 'sendpress_opts[header_text_size]',
				'description'   => __( 'Slide to change text size', $this->plugin_name )
			)
		) );

		// text color
		$wp_customize->add_setting( 'sendpress_opts[header_text_color]', array(
			'type'                  => 'option',
			'default'               => $this->defaults['header_text_color'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'sanitize_hex_color',
			'sanitize_js_callback'  => '',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize,
			'sendpress_header_text_color', array(
				'label'         => __( 'Text Color', $this->plugin_name ),
				'section'       => 'section_sendpress_header',
				'settings'      => 'sendpress_opts[header_text_color]',
				'description'   => __( 'Choose header text color', $this->plugin_name )
			)
		) );
		do_action('sendpress/sections/header/after_content', $wp_customize);
	}

	/**
	 * Body section
	 * @param $wp_customize WP_Customize_Manager
	 */
	private function body_section( $wp_customize ) {
		//require_once sendpress_PLUGIN_DIR . '/includes/customize-controls/class-font-size-customize-control.php';
		do_action('sendpress/sections/body/before_content', $wp_customize);

		// background color
		$wp_customize->add_setting( 'sendpress_opts[email_body_bg]', array(
			'type'                  => 'option',
			'default'               => $this->defaults['email_body_bg'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'sanitize_hex_color',
			'sanitize_js_callback'  => '',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize,
			'sendpress_email_body_bg', array(
				'label'         => __( 'Background Color', $this->plugin_name ),
				'section'       => 'section_sendpress_body',
				'settings'      => 'sendpress_opts[email_body_bg]',
				'description'   => __( 'Choose email body background color', $this->plugin_name )
			)
		) );
		// text size
		$wp_customize->add_setting( 'sendpress_opts[body_text_size]', array(
			'type'                  => 'option',
			'default'               => $this->defaults['body_text_size'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => array( $this,'sanitize_text'),
			'sanitize_js_callback'  => '',
		) );
		$wp_customize->add_control( new SendPress_Font_Customizer( $wp_customize,
			'sendpress_body_text_size', array(
				'label'         => __( 'Text size', $this->plugin_name ),
				'type'          => 'sendpress_send_mail',
				'section'       => 'section_sendpress_body',
				'settings'      => 'sendpress_opts[body_text_size]',
				'description'   => __( 'Slide to change text size', $this->plugin_name )
			)
		) );

		// text color
		$wp_customize->add_setting( 'sendpress_opts[body_text_color]', array(
			'type'                  => 'option',
			'default'               => $this->defaults['body_text_color'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'sanitize_hex_color',
			'sanitize_js_callback'  => '',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize,
			'sendpress_body_text_color', array(
				'label'         => __( 'Text Color', $this->plugin_name ),
				'section'       => 'section_sendpress_body',
				'settings'      => 'sendpress_opts[body_text_color]',
				'description'   => __( 'Choose body text color', $this->plugin_name )
			)
		) );
		do_action('sendpress/sections/body/after_content', $wp_customize);
	}

	/**
	 * Footer section
	 *
	 * @param $wp_customize WP_Customize_Manager
	 */
	private function footer_section($wp_customize) {

		//require_once sendpress_PLUGIN_DIR . '/includes/customize-controls/class-font-size-customize-control.php';
		do_action('sendpress/sections/footer/before_content', $wp_customize);

		$wp_customize->add_setting( 'sendpress_opts[footer_text]', array(
			'type'                  => 'option',
			'default'               => $this->defaults['footer_text'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => array( $this,'sanitize_text'),
			'sanitize_js_callback'  => '',
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize,
			'sendpress_footer', array(
				'label'     => __( 'Footer text', $this->plugin_name ),
				'type'      => 'textarea',
				'section'   => 'section_sendpress_footer',
				'settings'  => 'sendpress_opts[footer_text]',
				'description'   => __('Change the email footer here', $this->plugin_name )
			)
		) );

		// footer alignment
		$wp_customize->add_setting( 'sendpress_opts[footer_aligment]', array(
			'type'                  => 'option',
			'default'               => $this->defaults['footer_aligment'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => array( $this,'sanitize_alignment'),
			'sanitize_js_callback'  => '',
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize,
			'sendpress_footer_aligment', array(
				'label'         => __( 'Aligment', $this->plugin_name ),
				'type'          => 'select',
				'default'       => 'center',
				'choices'       => array(
					'left'  => 'Left',
					'center'=> 'Center',
					'right' => 'Right'
				),
				'section'       => 'section_sendpress_footer',
				'settings'      => 'sendpress_opts[footer_aligment]',
				'description'   => __( 'Choose alignment for footer', $this->plugin_name )
			)
		) );

		// background color
		$wp_customize->add_setting( 'sendpress_opts[footer_bg]', array(
			'type'                  => 'option',
			'default'               => $this->defaults['footer_bg'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'sanitize_hex_color',
			'sanitize_js_callback'  => '',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize,
			'sendpress_footer_bg', array(
				'label'         => __( 'Background Color', $this->plugin_name ),
				'section'       => 'section_sendpress_footer',
				'settings'      => 'sendpress_opts[footer_bg]',
				'description'   => __( 'Choose footer background color', $this->plugin_name )
			)
		) );
		// text size
		$wp_customize->add_setting( 'sendpress_opts[footer_text_size]', array(
			'type'                  => 'option',
			'default'               => $this->defaults['footer_text_size'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => array( $this,'sanitize_text'),
			'sanitize_js_callback'  => '',
		) );
		$wp_customize->add_control( new SendPress_Font_Customizer( $wp_customize,
			'sendpress_footer_text_size', array(
				'label'         => __( 'Text size', $this->plugin_name ),
				'type'          => 'sendpress_send_mail',
				'section'       => 'section_sendpress_footer',
				'settings'      => 'sendpress_opts[footer_text_size]',
				'description'   => __( 'Slide to change text size', $this->plugin_name )
			)
		) );
		// text color
		$wp_customize->add_setting( 'sendpress_opts[footer_text_color]', array(
			'type'                  => 'option',
			'default'               => $this->defaults['footer_text_color'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => 'sanitize_hex_color',
			'sanitize_js_callback'  => '',
		) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize,
			'sendpress_footer_text_color', array(
				'label'         => __( 'Text Color', $this->plugin_name ),
				'section'       => 'section_sendpress_footer',
				'settings'      => 'sendpress_opts[footer_text_color]',
				'description'   => __( 'Choose footer text color', $this->plugin_name )
			)
		) );

		// Powered by
		$wp_customize->add_setting( 'sendpress_opts[footer_powered_by]', array(
			'type'                  => 'option',
			'default'               => $this->defaults['footer_powered_by'],
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => '',
			'sanitize_js_callback'  => '',
		) );
		$wp_customize->add_control( new WP_Customize_Control( $wp_customize,
			'sendpress_footer_powered_by', array(
				'label'         => __( 'Powered by', $this->plugin_name ),
				'section'       => 'section_sendpress_footer',
				'settings'      => 'sendpress_opts[footer_powered_by]',
				'type'          => 'select',
				'choices'       => array(
					'off'   => 'Off',
					'on'    => 'On',
				),
				'description'   => __( 'Display a tiny link to the plugin page', $this->plugin_name )
			)
		) );
		do_action('sendpress/sections/footer/after_content', $wp_customize);
	}

	/**
	 * Send test email section
	 * @param $wp_customize Wp_Customize_Manager
	 */
	private function test_section( $wp_customize ) {
		//require_once sendpress_PLUGIN_DIR . '/includes/customize-controls/class-send-mail-customize-control.php';

		do_action('sendpress/sections/test/before_content', $wp_customize);

		// image logo
		$wp_customize->add_setting( 'sendpress_opts[send_mail]', array(
			'type'                  => 'option',
			'default'               => '',
			'transport'             => 'postMessage',
			'capability'            => 'edit_theme_options',
			'sanitize_callback'     => '',
			'sanitize_js_callback'  => '',
		) );
		/*
		$wp_customize->add_control( new WP_Send_Mail_Customize_Control( $wp_customize,
			'sendpress_test', array(
				'label'         => __( 'Send test email', $this->plugin_name ),
				'type'          => 'sendpress_send_mail',
				'section'       => 'section_sendpress_test',
				'settings'      => 'sendpress_opts[send_mail]',
				'description'   => __( 'Save the template and then click the button to send a test email to admin email ', $this->plugin_name ) . get_bloginfo('admin_email')
			)
		) );
		*/
		do_action('sendpress/sections/test/after_content', $wp_customize);
	}

	/**
	 * We let them use some safe html
	 * @param $input string to sanitize
	 *
	 * @return string
	 */
	public function sanitize_text( $input ) {
		return wp_kses_post( force_balance_tags( $input ) );
	}


	/**
	 * Sanitize aligment selects
	 * @param $input string to sanitize
	 *
	 * @return string
	 */
	public function sanitize_alignment( $input ) {
		$valid = array(
			'left',
			'right',
			'center',
		);

		if ( in_array( $input, $valid ) ) {
			return $input;
		} else {
			return '';
		}
	}
	/**
	 * Sanitize template select
	 * @param $input string to sanitize
	 *
	 * @return string
	 */
	public function sanitize_templates( $input ) {
		$valid = apply_filters( 'sendpress/template_choices', array(
			'boxed'    => 'Simple Theme',
			'fullwidth' => 'Fullwidth'
		));

		if ( array_key_exists( $input, $valid ) ) {
			return $input;
		} else {
			return '';
		}
	}

	public function remove_all_actions(){
		global $wp_scripts, $wp_styles;
		$exceptions = array(
			'sendpress-js',
			'jquery',
			'query-monitor',
			'sendpress-front-js',
			'customize-preview',
			'customize-controls',
		);
		
		if ( is_object( $wp_scripts ) && isset( $wp_scripts->queue ) && is_array( $wp_scripts->queue ) ) {
			foreach( $wp_scripts->queue as $handle ){
				if( in_array($handle, $exceptions))
					continue;
				wp_dequeue_script($handle);
			}
		}
		if ( is_object( $wp_styles ) && isset( $wp_styles->queue ) && is_array( $wp_styles->queue ) ) {
			foreach( $wp_styles->queue as $handle ){
				if( in_array($handle, $exceptions) )
					continue;
				wp_dequeue_style($handle);
			}
		}
		// Now remove actions
		$action_exceptions = array(
			'wp_print_footer_scripts',
			'wp_admin_bar_render',
		);
		// No core action in header
		remove_all_actions('wp_header');
		global $wp_filter;
		foreach( $wp_filter['wp_footer'] as $priority => $handle ){
			if( in_array( key($handle), $action_exceptions ) )
				continue;
			unset( $wp_filter['wp_footer'][$priority] );
		}
	}



}