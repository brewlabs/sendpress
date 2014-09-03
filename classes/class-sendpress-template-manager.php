<?php
//avoid direct calls to this file
if ( !defined('SENDPRESS_VERSION') ) {
    header('HTTP/1.0 403 Forbidden');
    die;
}

class SendPress_Template_Manager {

   static function templates(){
    
        return  array(
            'antwort' => array('path'=> SENDPRESS_PATH.'templates/master.html', 'name'=> 'Responsive Starter' ,'status' => 'sp-standard'),
            '1column' => array('path'=> SENDPRESS_PATH.'templates/1column.html', 'name'=> 'Responsive 1 Column' ,'status' => 'draft'),
            '2columns-to-rows' => array('path'=> SENDPRESS_PATH.'templates/2columns-to-rows.html', 'name'=> '2 Column Top - Wide Bottom - Responsive','status' => 'draft')
        );
    
    }


    static function update_template_content(){
        $templates = self::templates();
        $template_data = get_posts( array(
            'post_type' => 'sp_template',
            'post_status'=>array('sp-standard','draft')
             ));
        foreach ($template_data as $template) {
            if(isset( $templates[$template->post_name]) ){
               
                $content = file_get_contents( $templates[$template->post_name]['path'] );
                $template->post_title = $templates[$template->post_name]['name'];
                $template->post_content = $content;
                $template->post_status = $templates[$template->post_name]['status'];
                wp_update_post($template);
                unset($templates[$template->post_name]);
            }
  
        }

        foreach ($templates as $key => $template) {
            //print_r($template);
            $content = file_get_contents($template['path']);
            // Create post object
            $my_post = array(
                'post_title'    => $template['name'],
                'post_content'  => $content,
                'post_status'   => $template['status'],
                'post_name' => $key,
                'post_type' => 'sp_template'
            );
            // Insert the post into the database
            $post_id_added = wp_insert_post( $my_post );

            update_post_meta( $post_id_added, '_footer_page', SendPress_Tag_Footer_Page::content() );
            update_post_meta( $post_id_added, '_header_content', SendPress_Tag_Header_Content::content() );
            update_post_meta( $post_id_added, '_header_padding', 'pad-header' );

        }
    }
}

