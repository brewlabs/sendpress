<?php
//avoid direct calls to this file
if ( !defined('SENDPRESS_VERSION') ) {
    header('HTTP/1.0 403 Forbidden');
    die;
}

class SendPress_Videos{

    static function &init() {
        static $instance = false;

        if ( !$instance ) {
            $instance = new SendPress_Videos;
            $instance->add_hooks();
        }

        return $instance;
    }

    function add_hooks(){
        add_action('sendpress_template_loaded', array($this, 'add_video_filter') );
    }

    function add_video_filter(){
        add_filter( 'oembed_dataparse', array($this, 'fix_video'), 10, 3 );
    }

    function fix_video( $return, $data, $url ){

        if ($data->provider_name == 'YouTube' || $data->provider_name == 'Vimeo') {
            return "<a href='{$url}' target='_blank'><img src='{$data->thumbnail_url}'></a>";
        }
        return $return;

    }

}

