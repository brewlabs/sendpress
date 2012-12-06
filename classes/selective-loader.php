<?php
require_once ('class-sendpress-helper.php');
require_once ('class-sendpress-email.php');
require_once ('class-sendpress-tables.php');
require_once ('class-sendpress-database.php');
require_once ( 'class-ajax-loader.php' );

require_once ('class-sendpress-option.php');
require_once ('class-sendpress-posts.php');
require_once ('class-sendpress-public-view.php');
require_once ('class-sendpress-sender.php');

require_once ('class-sendpress-template.php');
require_once ('class-sendpress-view.php');
require_once ('class-signup-shortcode.php');
require_once ('class-unsubscribe-shortcode.php');
require_once ('class-widget-signup.php');
require_once ('Mobile_Detect.php');
require_once ('class-smtp-api-sendgrid.php');

if( is_admin() ){
	require_once ( 'class-tour.php' );
	require_once ( 'class-sp-tinymce.php' );
	require_once ( 'class-emails-table.php' );
	require_once ( 'class-lists-table.php' );
	require_once ( 'class-queue-table.php' );
	require_once ( 'class-reports-table.php' );
	require_once ( 'class-subscribers-table.php' );
	require_once ( 'class-sendpress-module.php' );

}



