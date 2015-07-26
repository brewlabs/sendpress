<?php
//avoid direct calls to this file
if ( !defined('SENDPRESS_VERSION') ) {
    header('HTTP/1.0 403 Forbidden');
    die;
}

class SendPress_Template_Manager {

   static function templates(){
    
        return  array(
            'antwort' => array('guid'=>'cd8ab466-e236-49d3-bd6c-e84db055ae9a', 'path'=> 'templates/v1-0/master.html', 'name'=> 'Responsive Starter' ,'status' => 'sp-standard'),
            'system-base' => array('guid'=>'cd8ab467-e236-49d3-bd6c-e84db055ae9a', 'path'=> 'templates/v1-0/master.html', 'name'=> 'System Starter' ,'status' => 'sp-standard'),
            //'1column' => array('guid'=>'a6789cdf-cb0d-4069-aba5-42fbded54519', 'path'=> SENDPRESS_PATH.'templates/1column.html', 'name'=> 'Responsive 1 Column' ,'status' => 'draft'),
            //'2columns-to-rows' => array('guid'=>'919af989-16b7-427f-bf2e-95adda844e1c', 'path'=> SENDPRESS_PATH.'templates/2columns-to-rows.html', 'name'=> '2 Column Top - Wide Bottom - Responsive','status' => 'draft')
        );
        
    }

    static function install_template_content(){
        $templates = self::templates();
            
            foreach ($templates as $key => $template) {
                //print_r($template);
               SendPress_Data::get_email_template($key,$template);

            }
            
        


        /*
        $update_array= array();
        foreach ($template_data as $template) {
            $guid = get_post_meta($template->ID, '_guid', true);
           
            foreach ($templates as $key => $values) {
                if( $guid == trim($values['guid']) && $values['name'] == $template->post_title ){
                    // $content = file_get_contents( $update['path'] );
                    $template->post_title = $values['name'];
                    $template->post_content = json_encode($values);
                    $template->post_status = $values['status'];
                    wp_update_post( $template );
                    unset($templates[$key]);
                }    
            }
  
        }

        foreach ($templates as $key => $template) {
            //print_r($template);
            //$content = file_get_contents($template['path']);
            // Create post object
            $my_post = array(
                'post_title'    => $template['name'],
                'post_content'  => json_encode($template),
                'post_status'   => $template['status'],
                'post_name' => $key,
                'post_type' => 'sp_template'
            );
            // Insert the post into the database
            $post_id_added = wp_insert_post( $my_post );
            update_post_meta( $post_id_added, '_guid',  $template['guid'] );
            update_post_meta( $post_id_added, '_footer_page', SendPress_Tag_Footer_Page::content() );
            update_post_meta( $post_id_added, '_header_content', SendPress_Tag_Header_Content::content() );
            update_post_meta( $post_id_added, '_header_padding', 'pad-header' );

        }
        */
    }


    static function update_template_content(){
        $templates = self::templates();
        foreach ($templates as $key => $template) {
        
           SendPress_Data::get_email_template($key,$template);
        }

        /*
        $update_array= array();
        foreach ($template_data as $template) {
            $guid = get_post_meta($template->ID, '_guid', true);
           
            foreach ($templates as $key => $values) {
                if( $guid == trim($values['guid']) && $values['name'] == $template->post_title ){
                    // $content = file_get_contents( $update['path'] );
                    $template->post_title = $values['name'];
                    $template->post_content = json_encode($values);
                    $template->post_status = $values['status'];
                    wp_update_post( $template );
                    unset($templates[$key]);
                }    
            }
  
        }

        foreach ($templates as $key => $template) {
            //print_r($template);
            //$content = file_get_contents($template['path']);
            // Create post object
            $my_post = array(
                'post_title'    => $template['name'],
                'post_content'  => json_encode($template),
                'post_status'   => $template['status'],
                'post_name' => $key,
                'post_type' => 'sp_template'
            );
            // Insert the post into the database
            $post_id_added = wp_insert_post( $my_post );
            update_post_meta( $post_id_added, '_guid',  $template['guid'] );
            update_post_meta( $post_id_added, '_footer_page', SendPress_Tag_Footer_Page::content() );
            update_post_meta( $post_id_added, '_header_content', SendPress_Tag_Header_Content::content() );
            update_post_meta( $post_id_added, '_header_padding', 'pad-header' );

        }
        */
    }
}

