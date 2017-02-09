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
        
    }

    static function add_video_filter(){
        add_filter('embed_oembed_html', array('SendPress_Videos','avoid_cache'),10, 4);
        add_filter( 'oembed_dataparse', array('SendPress_Videos', 'fix_video'), 10, 3 );
    }

    static function avoid_cache($cache, $url, $attr, $post_ID ){
        return wp_oembed_get($url,$attr);
    }

    static function fix_video( $return, $data, $url ){

        $output = '';

        if ($data->provider_name == 'YouTube' || $data->provider_name == 'Vimeo') {
            return self::get_video($url, $data->thumbnail_url);
            //return "<a href='{$url}' target='_blank'><img src='{$data->thumbnail_url}'></a>";
        }
        return $return;

    }

    static function get_video($url, $img){
        ob_start();
        ?>
        
        
        <a href='<?php echo $url; ?>' target='_blank'><img src='<?php echo $img; ?>'></a>
                
        
        <?php
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

}
