<?php
// SendPress Required Class: SendPress_Template

// Prevent loading this file directly
if (!defined('SENDPRESS_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    die;
}

if (class_exists('SendPress_Template')) {return;}

/**
 * SendPress_Options
 *
 * @uses
 *
 *
 * @package  SendPRess
 * @author   Josh Lyford
 * @license  See SENPRESS
 * @since     0.8.7
 */
class SendPress_Template
{

    private static $instance;
    static $cache_key_templates = 'sendpress:email_template_cache';
    public $_info                  = array();

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        if (false === ($sp_templates = get_transient(self::$cache_key_templates))) {
            $mainfiles      = $this->glob_php(SENDPRESS_PATH . 'templates');
            $themmefiles    = $this->glob_php(TEMPLATEPATH . '/sendpress');
            $wordpressfiles = $this->glob_php(WP_CONTENT_DIR . '/sendpress');

            $childfiles = array();
            if (is_child_theme()) {
                $childfiles = $this->glob_php(STYLESHEETPATH . '/sendpress');
            }
            $temps        = array_merge($mainfiles, $themmefiles, $childfiles, $wordpressfiles);
            $sp_templates = array();
            foreach ($temps as $temp) {
                if ($info = $this->get_template_info($temp[0])) {
                    $sp_templates[$temp[1]] = $info;
                }
            }
            set_transient(self::$cache_key_templates, $sp_templates, 60 * 60);
        }
        $this->info($sp_templates);
        do_action('sendpress_template_loaded');
    }

    public function info($info = null)
    {
        if (!isset($info)) {
            return $this->_info;
        }

        $this->_info = $info;
    }

    public static function get_instance()
    {
        if (!isset(self::$instance)) {
            $class_name     = __CLASS__;
            self::$instance = new $class_name;
        }
        return self::$instance;
    }

    /**
     * Load module data from module file. Headers differ from WordPress
     * plugin headers to avoid them being identified as standalone
     * plugins on the WordPress plugins page.
     */
    public function get_template_info($file)
    {
        $headers = array(
            'name'        => 'SendPress',
            'regions'     => 'Regions',
            'description' => 'Description',
            'sort'        => 'Sort Order',
        );
        $mod         = get_file_data($file, $headers);
        $mod['file'] = $file;

        if (empty($mod['sort'])) {
            $mod['sort'] = 10;
        }

        if (!empty($mod['name']) || !empty($mod['regions'])) {
            return $mod;
        }

        return false;
    }

    /**
     * Returns an array of all PHP files in the specified absolute path.
     * Equivalent to glob( "$absolute_path/*.php" ).
     *
     * @param string $absolute_path The absolute path of the directory to search.
     * @return array Array of absolute paths to the PHP files.
     */
    public function glob_php($absolute_path)
    {
        $absolute_path = untrailingslashit($absolute_path);
        $files         = array();
        if (is_dir($absolute_path)) {
            if (!$dir = @opendir($absolute_path)) {
                return $files;
            }

            while (false !== $file = readdir($dir)) {
                if ('.' == substr($file, 0, 1) || '.php' != substr($file, -4)) {
                    continue;
                }

                $file2 = "$absolute_path/$file";

                if (!is_file($file2)) {
                    continue;
                }
                $basename = str_replace($absolute_path, '', $file);
                $files[]  = array($file2, $basename);
            }

            closedir($dir);
        }

        return $files;
    }

    public function get_template($post_id)
    {
        $temp = get_post_meta($post_id, '_sendpress_template', true);
        if ($temp == false) {
            return 'simple.php';
        }
        return $temp;
    }

    public function render($post_id = false, $render = true, $inline = false, $no_links = false)
    {
        global $post;

        $saved = false;
        if ($post_id !== false) {
            $post  = get_post($post_id);
            $saved = $post;
        }
        $saved = $post;
        if (!isset($post)) {
            //echo __('Sorry we could not find your email.','sendpress');
            return;
        }
        $selected_template = $this->get_template($post_id);
        $template_list     = $this->info();
        if (isset($template_list[$selected_template])) {

            ob_start();
            require_once $template_list[$selected_template]['file'];
            $HtmlCode = ob_get_clean();

            $post = $saved;

            $HtmlCode = str_replace("*|SP:SUBJECT|*", $post->post_title, $HtmlCode);

            $body_bg           = get_post_meta($post->ID, 'body_bg', true);
            $body_text         = get_post_meta($post->ID, 'body_text', true);
            $body_link         = get_post_meta($post->ID, 'body_link', true);
            $header_bg         = get_post_meta($post->ID, 'header_bg', true);
            $active_header     = get_post_meta($post->ID, 'active_header', true);
            $upload_image      = get_post_meta($post->ID, 'upload_image', true);
            $header_text_color = get_post_meta($post->ID, 'header_text_color', true);
            $header_text       = get_post_meta($post->ID, 'header_text', true); //needs adding to the template
            $header_link       = get_post_meta($post->ID, 'header_link', true); //needs adding to the template
            $sub_header_text   = get_post_meta($post->ID, 'sub_header_text', true); //needs adding to the template
            $image_header_url  = get_post_meta($post->ID, 'image_header_url', true); //needs adding to the template
            $content_bg        = get_post_meta($post->ID, 'content_bg', true);
            $content_text      = get_post_meta($post->ID, 'content_text', true);
            $content_link      = get_post_meta($post->ID, 'sp_content_link_color', true);
            $content_border    = get_post_meta($post->ID, 'content_border', true);

            $header_link_open  = '';
            $header_link_close = '';

            if ($active_header == 'image') {
                if (!empty($image_header_url)) {
                    $header_link_open  = "<a style='color:" . $header_text_color . "' href='" . $image_header_url . "'>";
                    $header_link_close = "</a>";

                }
                $headercontent = $header_link_open . "<img style='display:block;' src='" . $upload_image . "' border='0' />" . $header_link_close;
                $HtmlCode      = str_replace("*|SP:HEADERCONTENT|*", $headercontent, $HtmlCode);
            } else {
                if (!empty($header_link)) {
                    $header_link_open  = "<a style='color:" . $header_text_color . "' href='" . $header_link . "'>";
                    $header_link_close = "</a>";

                }
                $headercontent = "<div style='padding: 10px; text-align:center;'><h1 style='text-align:center; color: " . $header_text_color . " !important;'>" . $header_link_open . $header_text . $header_link_close . "</h1>" . $sub_header_text . "</div>";
                $HtmlCode      = str_replace("*|SP:HEADERCONTENT|*", $headercontent, $HtmlCode);
            }

            $HtmlCode = str_replace("*|SP:HEADERBG|*", $header_bg, $HtmlCode);
            $HtmlCode = str_replace("*|SP:HEADERTEXT|*", $header_text_color, $HtmlCode);

            $HtmlCode = str_replace("*|SP:BODYBG|*", $body_bg, $HtmlCode);
            $HtmlCode = str_replace("*|SP:BODYTEXT|*", $body_text, $HtmlCode);
            $HtmlCode = str_replace("*|SP:BODYLINK|*", $body_link, $HtmlCode);

            $HtmlCode = str_replace("*|SP:CONTENTBG|*", $content_bg, $HtmlCode);
            $HtmlCode = str_replace("*|SP:CONTENTTEXT|*", $content_text, $HtmlCode);
            $HtmlCode = str_replace("*|SP:CONTENTLINK|*", $content_link, $HtmlCode);
            $HtmlCode = str_replace("*|SP:CONTENTBORDER|*", $content_border, $HtmlCode);

            $HtmlCode = $this->tag_replace($HtmlCode);

            // Date processing

            $canspam = wpautop(SendPress_Option::get('canspam'));

            $HtmlCode = str_replace("*|SP:CANSPAM|*", $canspam, $HtmlCode);

            $social = '';
            if ($twit = SendPress_Option::get('twitter')) {
                $social .= "<a href='$twit' style='color: $body_link;'>Twitter</a>";
            }

            if ($fb = SendPress_Option::get('facebook')) {
                if ($social != '') {
                    $social .= " | ";
                }
                $social .= "<a href='$fb'  style='color: $body_link;'>Facebook</a>";
            }
            if ($ld = SendPress_Option::get('linkedin')) {
                if ($social != '') {
                    $social .= " | ";
                }
                $social .= "<a href='$ld'  style='color: $body_link;'>LinkedIn</a>";
            }
            $social   = SendPress_Data::build_social($body_link);
            $HtmlCode = str_replace("*|SP:SOCIAL|*", $social, $HtmlCode);
/*
$dom = new DomDocument();
$dom->strictErrorChecking = false;
@$dom->loadHtml($HtmlCode);
$iTags = $dom->getElementsByTagName('img');
foreach ($iTags as $iElement) {
$class = $iElement->getAttribute('class');
}
$body_html = $dom->saveHtml();

$simplecss = file_get_contents(SENDPRESS_PATH.'/templates/simple.css');

// create instance
$cssToInlineStyles = new CSSToInlineStyles($HtmlCode, $simplecss);

// grab the processed HTML
$HtmlCode = $cssToInlineStyles->convert();

 */
            $display_correct = __("Is this email not displaying correctly?", "sendpress");
            $view            = __("View it in your browser", "sendpress");
            $start_text      = __("Not interested anymore?", "sendpress");
            $unsubscribe     = __("Unsubscribe", "sendpress");
            $instantly       = __("Instantly", "sendpress");
            $manage          = __("Manage Subscription", "sendpress");
            if ($render) {
                //RENDER IN BROWSER

                if ($inline) {
                    $link      = get_permalink($post->ID);
                    $browser   = $display_correct . ' <a style="color: ' . $body_link . ';" href="' . $link . '">' . $view . '</a>.';
                    $HtmlCode  = str_replace("*|SP:BROWSER|*", $browser, $HtmlCode);
                    $remove_me = ' <a href="#"  style="color: ' . $body_link . ';" >' . $unsubscribe . '</a> | ';
                    $manage    = ' <a href="#"  style="color: ' . $body_link . ';" >' . $manage . '</a> ';

                    $HtmlCode = str_replace("*|SP:MANAGE|*", $manage, $HtmlCode);
                    $HtmlCode = str_replace("*|SP:UNSUBSCRIBE|*", $remove_me, $HtmlCode);

                } else {
                    $HtmlCode = str_replace("*|SP:BROWSER|*", '', $HtmlCode);
                    $HtmlCode = str_replace("*|SP:UNSUBSCRIBE|*", '', $HtmlCode);
                    $HtmlCode = str_replace("*|SP:MANAGE|*", '', $HtmlCode);
                    $HtmlCode = str_replace("*|ID|*", '', $HtmlCode);
                    $HtmlCode = str_replace("*|FNAME|*", '', $HtmlCode);
                    $HtmlCode = str_replace("*|LNAME|*", '', $HtmlCode);
                    $HtmlCode = str_replace("*|EMAIL|*", '', $HtmlCode);
                }
                echo $HtmlCode;
            } else {
                //PREP FOR SENDING
                if ($no_links == false) {
                    $link      = get_permalink($post->ID);
                    $open_info = array(
                        "id"   => $post->ID,

                        "view" => "email",
                    );
                    $code = SendPress_Data::encrypt($open_info);

                    $xlink    = SendPress_Manager::public_url($code);
                    $browser  = $display_correct . ' <a style="color: ' . $body_link . ';" href="' . $xlink . '">' . $view . '</a>.';
                    $HtmlCode = str_replace("*|SP:BROWSER|*", $browser, $HtmlCode);

                } else {
                    $HtmlCode = str_replace("*|SP:BROWSER|*", '', $HtmlCode);
                    $HtmlCode = str_replace("*|SP:UNSUBSCRIBE|*", '', $HtmlCode);
                    $HtmlCode = str_replace("*|SP:MANAGE|*", '', $HtmlCode);
                }
                return $HtmlCode;
            }

        } else {
            //echo __('Sorry we could not find your email template.','sendpress');
            return;
        }
    }

    public function render_html($post_id = false, $render = true, $inline = false, $no_links = false, $custom_html = false)
    {
        global $post;
        remove_filter('the_content', 'sharing_display', 19);
        remove_filter('the_excerpt', 'sharing_display', 19);

        remove_filter('the_content', 'A2A_SHARE_SAVE_add_to_content', 98);
        remove_filter('the_excerpt', 'A2A_SHARE_SAVE_add_to_content', 98);

        $saved = false;
        if ($post_id !== false) {
            $post  = get_post($post_id);
            $saved = $post;
        }
        $saved = $post;

        if (!isset($post)) {
            //echo __('Sorry we could not find your email.','sendpress');
            return;
        }
        //sp-custom
        //$selected_template = $this->get_template( $post_id );
        //$template_list = $this->info();
        $custom        = false;
        $post_template = get_post_meta($post->ID, '_sendpress_template', true);
        if ($post_template != '' && is_numeric($post_template) && $post_template > 0) {
            $HtmlCode = SendPress_Email_Render_Engine::render_template($post_template, $post_id, $custom_html);
            if (get_post_status($post_template) == 'sp-custom') {
                $custom = true;
            };
        } else {
            $old = get_post_meta($post->ID, '_sendpress_system', true);
            if ($old == 'old') {
                $HtmlCode = file_get_contents(SENDPRESS_PATH . '/templates/original.html');
            } else {
                $HtmlCode = file_get_contents(SENDPRESS_PATH . '/templates/simple.html');
            }

        }

        if ($HtmlCode != false) {
            /*
            ob_start();
            require_once( $template_list[$selected_template]['file'] );
            $HtmlCode= ob_get_clean();
             */
            $HtmlCode = do_shortcode($HtmlCode);

            add_filter('the_content', 'do_shortcode', 11);
            if ($custom_html == false) {
                $content = $post->post_content;
            } else {
                $content = $custom_html;
            }

            $content = apply_filters('the_content', $content);
            //print_r($post->post_content);
            $content = str_replace(']]>', ']]&gt;', $content);
            //$content = do_shortcode( $content );
            $HtmlCode = str_replace("*|SP:CONTENT|*", $content, $HtmlCode);

            $post = $saved;

            $HtmlCode = str_replace("*|SP:SUBJECT|*", $post->post_title, $HtmlCode);

            $body_bg           = get_post_meta($post->ID, 'body_bg', true);
            $body_text         = get_post_meta($post->ID, 'body_text', true);
            $body_link         = get_post_meta($post->ID, 'body_link', true);
            $header_bg         = get_post_meta($post->ID, 'header_bg', true);
            $active_header     = get_post_meta($post->ID, 'active_header', true);
            $upload_image      = get_post_meta($post->ID, 'upload_image', true);
            $header_text_color = get_post_meta($post->ID, 'header_text_color', true);
            $header_text       = get_post_meta($post->ID, 'header_text', true); //needs adding to the template
            $header_link       = get_post_meta($post->ID, 'header_link', true); //needs adding to the template
            $sub_header_text   = get_post_meta($post->ID, 'sub_header_text', true); //needs adding to the template
            $image_header_url  = get_post_meta($post->ID, 'image_header_url', true); //needs adding to the template
            $content_bg        = get_post_meta($post->ID, 'content_bg', true);
            $content_text      = get_post_meta($post->ID, 'content_text', true);
            $content_link      = get_post_meta($post->ID, 'sp_content_link_color', true);
            $content_border    = get_post_meta($post->ID, 'content_border', true);

            $header_link_open  = '';
            $header_link_close = '';

            if ($active_header == 'image') {
                if (!empty($image_header_url)) {
                    $header_link_open  = "<a style='color:" . $header_text_color . "' href='" . $image_header_url . "'>";
                    $header_link_close = "</a>";

                }
                $headercontent = $header_link_open . "<img style='display:block;' src='" . $upload_image . "' border='0' />" . $header_link_close;
                $HtmlCode      = str_replace("*|SP:HEADERCONTENT|*", $headercontent, $HtmlCode);
            } else {
                if (!empty($header_link)) {
                    $header_link_open  = "<a style='color:" . $header_text_color . "' href='" . $header_link . "'>";
                    $header_link_close = "</a>";

                }
                $headercontent = "<div style='padding: 10px; text-align:center;'><h1 style='text-align:center; color: " . $header_text_color . " !important;'>" . $header_link_open . $header_text . $header_link_close . "</h1>" . $sub_header_text . "</div>";
                $HtmlCode      = str_replace("*|SP:HEADERCONTENT|*", $headercontent, $HtmlCode);
            }

            $HtmlCode = str_replace("*|SP:HEADERBG|*", $header_bg, $HtmlCode);
            $HtmlCode = str_replace("*|SP:HEADERTEXT|*", $header_text_color, $HtmlCode);

            $HtmlCode = str_replace("*|SP:BODYBG|*", $body_bg, $HtmlCode);
            $HtmlCode = str_replace("*|SP:BODYTEXT|*", $body_text, $HtmlCode);
            $HtmlCode = str_replace("*|SP:BODYLINK|*", $body_link, $HtmlCode);

            $HtmlCode = str_replace("*|SP:CONTENTBG|*", $content_bg, $HtmlCode);
            $HtmlCode = str_replace("*|SP:CONTENTTEXT|*", $content_text, $HtmlCode);
            $HtmlCode = str_replace("*|SP:CONTENTLINK|*", $content_link, $HtmlCode);
            $HtmlCode = str_replace("*|SP:CONTENTBORDER|*", $content_border, $HtmlCode);

            $HtmlCode = $this->tag_replace($HtmlCode);

            // Date processing

            $canspam = wpautop(SendPress_Option::get('canspam'));

            $HtmlCode = str_replace("*|SP:CANSPAM|*", $canspam, $HtmlCode);

            $social = '';
            if ($twit = SendPress_Option::get('twitter')) {
                $social .= "<a href='$twit' style='color: $body_link;'>Twitter</a>";
            }

            if ($fb = SendPress_Option::get('facebook')) {
                if ($social != '') {
                    $social .= " | ";
                }
                $social .= "<a href='$fb'  style='color: $body_link;'>Facebook</a>";
            }
            if ($ld = SendPress_Option::get('linkedin')) {
                if ($social != '') {
                    $social .= " | ";
                }
                $social .= "<a href='$ld'  style='color: $body_link;'>LinkedIn</a>";
            }
            $social   = SendPress_Data::build_social($body_link);
            $HtmlCode = str_replace("*|SP:SOCIAL|*", $social, $HtmlCode);
            /*
            $dom = new DomDocument();
            $dom->strictErrorChecking = false;
            @$dom->loadHtml($HtmlCode);
            $iTags = $dom->getElementsByTagName('img');
            foreach ($iTags as $iElement) {
            $class = $iElement->getAttribute('class');
            }
            $body_html = $dom->saveHtml();
             */
            /*
            $simplecss = file_get_contents(SENDPRESS_PATH.'/templates/simple.css');

            // create instance
            $cssToInlineStyles = new CSSToInlineStyles($HtmlCode, $simplecss);

            // grab the processed HTML
            $HtmlCode = $cssToInlineStyles->convert();

             */
            $display_correct = __("Is this email not displaying correctly?", "sendpress");
            $view            = __("View it in your browser", "sendpress");
            $start_text      = __("Not interested anymore?", "sendpress");
            $unsubscribe     = __("Unsubscribe", "sendpress");
            $instantly       = __("Instantly", "sendpress");
            $manage          = __("Manage Subscription", "sendpress");
            if ($render) {
                //RENDER IN BROWSER
                if ($inline) {
                    $link      = get_permalink($post->ID);
                    $browser   = $display_correct . ' <a style="color: ' . $body_link . ';" href="' . $link . '">' . $view . '</a>.';
                    $HtmlCode  = str_replace("*|SP:BROWSER|*", $browser, $HtmlCode);
                    $remove_me = ' <a href="#"  style="color: ' . $body_link . ';" >' . $unsubscribe . '</a> | ';
                    $manage    = ' <a href="#"  style="color: ' . $body_link . ';" >' . $manage . '</a> ';

                    $HtmlCode = str_replace("*|SP:MANAGE|*", $manage, $HtmlCode);
                    $HtmlCode = str_replace("*|SP:UNSUBSCRIBE|*", $remove_me, $HtmlCode);

                } else {
                    $HtmlCode = str_replace("*|SP:BROWSER|*", '', $HtmlCode);
                    $HtmlCode = str_replace("*|SP:UNSUBSCRIBE|*", '', $HtmlCode);
                    $HtmlCode = str_replace("*|SP:MANAGE|*", '', $HtmlCode);
                    $HtmlCode = str_replace("*|ID|*", '', $HtmlCode);
                    $HtmlCode = str_replace("*|FNAME|*", '', $HtmlCode);
                    $HtmlCode = str_replace("*|LNAME|*", '', $HtmlCode);
                    $HtmlCode = str_replace("*|EMAIL|*", '', $HtmlCode);
                }
                echo $HtmlCode;
            } else {
                //PREP FOR SENDING
                if ($no_links == false) {
                    $link      = get_permalink($post->ID);
                    $open_info = array(
                        "id"   => $post->ID,

                        "view" => "email",
                    );
                    $code = SendPress_Data::encrypt($open_info);

                    $xlink    = SendPress_Manager::public_url($code);
                    $browser  = $display_correct . ' <a style="color: ' . $body_link . ';" href="' . $xlink . '">' . $view . '</a>.';
                    $HtmlCode = str_replace("*|SP:BROWSER|*", $browser, $HtmlCode);

                } else {
                    $HtmlCode = str_replace("*|SP:BROWSER|*", '', $HtmlCode);
                    $HtmlCode = str_replace("*|SP:UNSUBSCRIBE|*", '', $HtmlCode);
                    $HtmlCode = str_replace("*|SP:MANAGE|*", '', $HtmlCode);
                }

                if (class_exists("DomDocument")) {
                    //parse html to fix image
                    $dom                      = new DOMDocument();
                    $dom->strictErrorChecking = false;
                    @$dom->loadHTML('<meta http-equiv="Content-Type" content="charset=utf-8" />' . $HtmlCode);

                    /*
                    DOMElement Object
                    (
                    [tagName] => img
                    [schemaTypeInfo] =>
                    [nodeName] => img
                    [nodeValue] =>
                    [nodeType] => 1
                    [parentNode] => (object value omitted)
                    [childNodes] => (object value omitted)
                    [firstChild] =>
                    [lastChild] =>
                    [previousSibling] => (object value omitted)
                    [nextSibling] => (object value omitted)
                    [attributes] => (object value omitted)
                    [ownerDocument] => (object value omitted)
                    [namespaceURI] =>
                    [prefix] =>
                    [localName] => img
                    [baseURI] =>
                    [textContent] =>
                    )
                     */
                    if ($custom != true) {
                        $strings = array('sp-img', 'sp-social', 'sp-skip');

                        foreach ($dom->getElementsByTagName('img') as $k => $img) {
                            $c         = explode(' ', $img->getAttribute('class'));
                            $styled    = $img->getAttribute('style');
                            $replace_w = false;
                            $replace_h = false;

                            $width_r = $img->getAttribute('width');
                            $w_r     = strpos($width_r, '%');
                            if ($w_r === false) {
                                $replace_w = true;
                            }

                            $height_r = $img->getAttribute('height');
                            $h_r      = strpos($height_r, '%');
                            if ($h_r === false) {
                                $replace_h = true;
                            }

                            if (is_array($c) && (count(array_intersect($c, $strings)) == 0)) {
                                $replace_image = false;
                                if (in_array('alignleft', $c)) {
                                    $replace_image = true;
                                    $img->setAttribute('align', 'left');
                                    if ($styled == '') {
                                        $img->setAttribute('style', 'margin-right: 10px');
                                        $img->setAttribute('class', 'sp-img ' . implode(' ', $c));
                                        if ($replace_w) {
                                            $img->setAttribute('width', '');
                                        }
                                        if ($replace_h) {
                                            $img->setAttribute('height', '');
                                        }
                                    }

                                }
                                if (in_array('alignright', $c)) {
                                    $replace_image = true;
                                    $img->setAttribute('align', 'right');
                                    if ($styled == '') {
                                        $img->setAttribute('style', 'margin-left: 10px');
                                        $img->setAttribute('class', 'sp-img ' . implode(' ', $c));
                                        if ($replace_w) {
                                            $img->setAttribute('width', '');
                                        }
                                        if ($replace_h) {
                                            $img->setAttribute('height', '');
                                        }
                                    }
                                }
                                //Center any image that has not been updated..
                                if (in_array('aligncenter', $c) || $replace_image == false) {

                                    $table = $dom->createElement('table');
                                    $table->setAttribute('width', '100%');
                                    $table->setAttribute('border', '0');
                                    $table->setAttribute('cellspacing', '0');
                                    $table->setAttribute('cellpadding', '0');

                                    $domAttribute        = $dom->createAttribute('id');
                                    $domAttribute->value = 'spnl-ximage-' . $k;

                                    $tr = $dom->createElement('tr');
                                    $table->appendChild($tr);

                                    $td = $dom->createElement('td');
                                    $td->setAttribute('align', 'center');
                                    $tr->appendChild($td);
                                    $img_r = $img->clonenode(true);
                                    $img_r->setAttribute('align', 'center');
                                    if ($replace_w) {
                                        $img_r->setAttribute('width', '');
                                    }
                                    if ($replace_h) {
                                        $img_r->setAttribute('height', '');
                                    }

                                    $img_r->setAttribute('class', 'sp-img ' . implode(' ', $c));
                                    $added_it    = $img_r;
                                    $px          = $img->parentNode;
                                    $target_node = $img;
                                    $replace     = $img;
                                    if ($px->tagName == 'a') {
                                        $added_it = $px->clonenode(false);
                                        $added_it->appendChild($img_r);
                                        //$relace = $px;
                                        $target_node = $px;
                                    }

                                    $td->appendChild($added_it);

                                    $table->appendChild($domAttribute);

                                    //    insertBefore()
                                    $target_node->parentNode->replaceChild($table, $target_node);

//                            print_r($px);
                                    // $px->insertBefore($table, $img);
                                    //  $px->removeChild($img);
                                    //this still needs work
                                    /*
                                $newNode = '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td align="center"><img src="'.$img->src.'" alt="'.$img->getAttribute('title').'" border="0" style="vertical-align:top;"  hspace="0" vspace="0" class="sp-img" align="center"/></td></tr></table>';

                                $tmpDoc = new DOMDocument();
                                $tmpDoc->loadHTML($newNode);
                                foreach ($tmpDoc->getElementsByTagName('body')->item(0)->childNodes as $node) {
                                $node = $img->parentNode->ownerDocument->importNode($node);
                                $img->parentNode->replaceChild($node, $img);
                                }

                                 */

                                }
                            }
                        }
                    }
   					/*
                    $head = $dom->getElementsByTagName('head');
                    if( $head->length == 0 ) {
                    	$body = $dom->getElementsByTagName('html');
                    	$h = $dom->createElement('head');
                    	
						$titleNode = $dom->createElement("meta");
						$titleNode->setAttribute("http-equiv","Content-Type");
						$titleNode->setAttribute("content","text/html; charset=utf-8");




						$h->appendChild($titleNode);

						$titleNode = $dom->createElement("meta");
					
						$titleNode->setAttribute("charset","utf-8");




						$h->appendChild($titleNode);


						
                    
						$body[0]->insertBefore($h,$body[0]->firstChild);
					}
					*/
                    $HtmlCode = $dom->saveHTML();
                }

                return $HtmlCode;
            }

        } else {
            //echo __('Sorry we could not find your email template.','sendpress');
            return;
        }
    }

    public static function link_style($color, $content)
    {
        if (class_exists("DomDocument")) {
            $dom = new DomDocument('1.0', 'UTF-8');
            //$content = str_replace ('&nbsp;', '@nbsp;', $content);
            if (function_exists('mb_convert_encoding')) {
                $content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'); // htmlspecialchars($content);
            }
            $dom->strictErrorChecking = false;
            @$dom->loadHtml($content);
            $aTags = $dom->getElementsByTagName('a');
            foreach ($aTags as $aElement) {
                $style = $aElement->getAttribute('style');
                $style .= ' color: ' . $color . '; ';
                $aElement->setAttribute('style', $style);
            }
            //$content = $dom->saveHTML();
            $content = preg_replace(array("/^\<\!DOCTYPE.*?<html><body>/si",
                "!</body></html>$!si"),
                "",
                $dom->saveHTML());

            $content = str_replace("%7B", "{", $content);
            $content = str_replace("%7D", "}", $content);
            return $content;

        }
        return $content;
    }

    public static function tag_replace($HtmlCode)
    {
        $HtmlCode = str_replace('*|SITE:URL|*', get_option('home'), $HtmlCode);
        $HtmlCode = str_replace('*|SITE:TITLE|*', get_option('blogname'), $HtmlCode);
        $HtmlCode = str_replace('*|DATE|*', date_i18n(get_option('date_format')), $HtmlCode);
        $HtmlCode = str_replace('*|SITE:DESCRIPTION|*', get_option('blogdescription'), $HtmlCode);
        $x        = 0;
        while (($x = strpos($HtmlCode, '*|DATE:', $x)) !== false) {
            $y = strpos($HtmlCode, '|*', $x);
            if ($y === false) {
                continue;
            }

            $f        = substr($HtmlCode, $x + 7, $y - $x - 7);
            $HtmlCode = substr($HtmlCode, 0, $x) . date_i18n($f) . substr($HtmlCode, $y + 2);
        }

        return $HtmlCode;
    }

}

// Initialize!
SendPress_Template::get_instance();
