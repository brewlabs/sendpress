<?php
// SendPress Required Class: SendPress_Posts

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}


class SendPress_Posts{
	
	static function delete($post_id){
		wp_delete_post($post_id);
	}

	/**
	 * Create a Duplicate post
	 */
	static function copy($post, $post_title ='',$post_name='', $post_type= 'sp_report') {

		// We don't want to clone revisions
		if ($post->post_type == 'revision') return;

		if ($post->post_type != 'attachment'){
			$status = 'draft';
		}
		
		$new_post_author = wp_get_current_user();

		$new_post = array(
		'menu_order' => $post->menu_order,
		'comment_status' => $post->comment_status,
		'ping_status' => $post->ping_status,
		'post_author' => $new_post_author->ID,
		'post_content' => $post->post_content,
		'post_excerpt' => $post->post_excerpt,
		'post_mime_type' => $post->post_mime_type,
		//'post_parent' =>  $post->parent_id,
		'post_password' => $post->post_password,
		'post_status' => $post->post_status,
		'post_title' => (empty($post_title))? $post->post_title: $post_title, 
		'post_type' => (empty($post_type))? $post->post_type: $post_type, 
		);

		if($post_name != ''){
			$new_post['post_name'] = $post_name;
		}
		/*
		$new_post['post_date'] = $new_post_date =  $post->post_date ;
		$new_post['post_date_gmt'] = get_gmt_from_date($new_post_date);
		*/

		$new_post_id = wp_insert_post($new_post);


		// If you have written a plugin which uses non-WP database tables to save
		// information about a post you can hook this action to dupe that data.
		if ($post->post_type == 'page' || (function_exists('is_post_type_hierarchical') && is_post_type_hierarchical( $post->post_type )))
		do_action( 'sp_duplicate_page', $new_post_id, $post );
		else
		do_action( 'sp_duplicate_post', $new_post_id, $post );

		delete_post_meta($new_post_id, '_sp_original');
		add_post_meta($new_post_id, '_sp_original', $post->ID);
		add_post_meta($new_post_id, '_open_count', 0 );

		return $new_post_id;
	}


	static function copy_meta_info($new_id, $old_id) {
		$post_meta_keys = get_post_custom_keys($old_id);
		if (empty($post_meta_keys)) return;
		
		
		foreach ($post_meta_keys as $meta_key) {
			$meta_values = get_post_custom_values($meta_key, $old_id);
			foreach ($meta_values as $meta_value) {
				$meta_value = maybe_unserialize($meta_value);
				update_post_meta($new_id, $meta_key, $meta_value);
			}
		}
	}

	// Added 'exclude_from_search'=>true
	static function email_post_type($name){

		$sp_debug_post_type = false;
		if(defined('SP_DEBUG') && SP_DEBUG){
			$sp_debug_post_type = true;
		}

		register_post_type( $name , array(	
				'show_in_menu' => $sp_debug_post_type,
				'public' => $sp_debug_post_type,
				'publicly_queryable' =>true,
				'show_ui' => $sp_debug_post_type,
				'query_var' => true,
				'exclude_from_search'=>true,
				'_edit_link' => 'sendpress.php',
				'capability_type' => 'post',
				'hierarchical' => false,
				'rewrite' => array('slug' => 'emails','with_front'=>false),
				'supports' => array('title','editor'),
				//'has_archive' => 'emails',
				'labels' => array (
				'name' => 'emails',
				'singular_name' => 'email',
				'menu_name' => 'email',
				'add_new' => 'Add email',
				'add_new_item' => 'Add New email',
				'edit' => 'Edit',
				'edit_item' => 'Edit email',
				'new_item' => 'New email',
				'view' => 'View Emails',
				'view_item' => 'View Email',
				'search_items' => 'Search email',
				'not_found' => 'No email Found',
				'not_found_in_trash' => 'No email Found in Trash',
				'parent' => 'Parent email',
			),) );
	}

	// Added 'exclude_from_search'=>true
	static function report_post_type($name){
		$sp_debug_post_type = false;
		if(defined('SP_DEBUG') && SP_DEBUG){
			$sp_debug_post_type = true;
		}

		register_post_type( $name , array(	
			'show_in_menu' => $sp_debug_post_type,
			'public' => $sp_debug_post_type,
			'publicly_queryable' =>true,
			'show_ui' => $sp_debug_post_type,
			'query_var' => true,
			'exclude_from_search'=>true,
			'capability_type' => 'post',
			//'has_archive' => 'send',
			'hierarchical' => false,
			'rewrite' => array('slug' => 'send','with_front'=>false),
			'supports' => array('title','editor'),
			'labels' => array (
			'name' => 'Newsletters',
			'singular_name' => 'Newsletter',
			'exclude_from_search' => 'false',
			'menu_name' => 'Newsletters',
			'add_new' => 'Add Newsletter',
			'add_new_item' => 'Add New Newsletter',
			'edit' => 'Edit',
			'edit_item' => 'Edit Newsletter',
			'new_item' => 'New Newsletter',
			'view' => 'View Emails',
			'view_item' => 'View Email',
			'search_items' => 'Search Newsletters',
			'not_found' => 'No Newsletters Found',
			'not_found_in_trash' => 'No Newsletters Found in Trash',
			'parent' => 'Parent Newsletter',
		),) );
		
	}

	
	// Added 'exclude_from_search'=>true
	static function template_post_type(){
		// Template Post Statuses
		register_post_status( 'sp-standard', array(
			'label'                     => _x( 'Standard',  'sendpress' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Standard <span class="count">(%s)</span>', 'Standard <span class="count">(%s)</span>', 'sendpress' )
		)  );

		register_post_status( 'sp-custom', array(
			'label'                     => _x(  'Custom Template', 'sendpress' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Custom <span class="count">(%s)</span>', 'Custom <span class="count">(%s)</span>', 'sendpress' )
		)  );

		register_post_status( 'sp-form', array(
			'label'                     => _x( 'Form', 'sendpress' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Form Setting <span class="count">(%s)</span>', 'Forms Settings <span class="count">(%s)</span>', 'sendpress' )
		)  );


		register_post_type( 'sptemplates', array(
			'labels' => array(
				'name' => __( 'SendPress Internal Container', 'sendpress' ),
			),
			'public' => false,
			'show_ui' => false,
			'capability_type' => 'post',
			
			'hierarchical' => false,
			'rewrite' => false,
			'supports' => array( 'title', 'editor' ),
			'query_var' => false,
			'can_export' => true,
			'show_in_nav_menus' => false,
			'exclude_from_search'=>'true',
		) );

		register_post_type( 'sp_template', array(
			'labels' => array(
				'name' => __( 'SendPress Templates', 'sendpress' ),
			),
			'public' 			=> false,
			'query_var' 		=> false,
			'rewrite' 			=> false,
			'show_ui'           => false,
			'capability_type' 	=> 'post',
			'map_meta_cap'      => true,
			'supports' 			=> array( 'title' ),
			'can_export'		=> true,
			'exclude_from_search'=>'true',
		) );

		register_post_type( 'sp_settings', array(
			'labels' => array(
				'name' => __( 'SendPress Settings', 'sendpress' ),
			),
			'public' 			=> false,
			'query_var' 		=> false,
			'rewrite' 			=> false,
			'show_ui'           => false,
			'capability_type' 	=> 'post',
			'map_meta_cap'      => true,
			'supports' 			=> array( 'title' ),
			'can_export'		=> true,
			'exclude_from_search'=>'true',
		) );

	}

		// Added 'exclude_from_search'=>true
	static function list_post_type(){
		register_post_type( 'sendpress_list', array(
			'labels' => array(
				'name' => __( 'SendPress List', 'sendpress' ),
			),
			'public' => false,
			'show_ui' => false,
			'capability_type' => 'post',
			'hierarchical' => false,
			'rewrite' => false,
			'supports' => array( 'title', 'editor' ),
			'query_var' => false,
			'can_export' => true,
			'show_in_nav_menus' => false,
			'exclude_from_search'=>'true',
		) );

	}
	// Added 'exclude_from_search'=>true
	function jobs_post_type(){
		register_post_type( 'sendpress_jobs', array(
			'labels' => array(
				'name' => __( 'SendPress Jobs', 'sendpress' ),
			),
			'public' => false,
			'show_ui' => false,
			'capability_type' => 'post',
			'hierarchical' => false,
			'rewrite' => false,
			'supports' => array( 'title', 'editor' ),
			'query_var' => false,
			'can_export' => false,
			'show_in_nav_menus' => false,
			'exclude_from_search'=>'true',
		) );

	}

	/*
	Unused for now
	*/

	static function pages_post_type(){
		register_post_type( 'sendpress',
            array(
                'labels' => array(
                    'name' => __( 'Sendpress Pages' ),
                    'singular_name' => __( 'SendPress Page' )
               ),
            'public' => true,
            'has_archive' => false,
            'show_ui' =>true,
            'show_in_menu' =>true,
            'hierarchical' => true,
            'rewrite' => array("slug"=>"sendpress"),
            'show_in_nav_menus'=>false,
            'can_export'=>false,
            'publicly_queryable'=>true,
            'exclude_from_search'=>'true',
            )
        );

		$post = get_page_by_title('Manage Subscription', 'OBJECT','sendpress' );
		    if(!isset($post)){
			$new_page = array(
			'post_title' => 'Manage Subscription',
			'post_name' => 'manage-subscription',
			'post_status' => 'publish',
			'post_type' => 'sendpress',
			'post_author' => 1,
			'post_parent' => 0,
			'menu_order' => 0,
			'comment_status' => 'closed'
			);
			wp_insert_post( $new_page );
			}

	}











}