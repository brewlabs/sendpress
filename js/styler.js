jQuery(document).ready(function($) {

        $('#addimageupload').click(function(e) {
            e.preventDefault();
            $btn = $(this);
            formfield = jQuery('#upload_image').attr('name');
            formID = $btn.attr('rel');
            tb_show('SendPress', 'media-upload.php?post_id='+formID+'&amp;is_sendpress=yes&amp;TB_iframe=true');
            return false;
        });
        
        spadmin.send_to_editor = window.send_to_editor;

        window.send_to_editor = function(html) {
            if( jQuery('#imageaddbox').is(":visible")   ){
                imgurl = jQuery('img',html).attr('src');
                jQuery('#upload_image').val(imgurl);
                tb_remove();
            } else {
                spadmin.log(html);
                imgurl = jQuery('img',html).attr('src');
                //cl =jQuery('img',html).parent().attr('class');
                find = html.search('alignleft');
                if(find > 0 ){
                    html = "<img style='margin-right: 10px;' src='"+imgurl+"' align='left'/>";
                }
                find = html.search('alignright');
                if(find > 0 ){
                    html = "<img style='margin-left: 10px;' src='"+imgurl+"' align='right'/>";
                }
                find = html.search('aligncenter');
                if(find > 0 ){
                    html = '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td align="center"><img style="margin-left: 10px; margin-right: 10px;" src="'+imgurl+'" align="center"/></td></tr></table>';
                }


                spadmin.send_to_editor(html);
            }

        };

        $('#upload_image').change(function(){
              $('#html-header').html('<img src="'+ $(this).val() +'" />');
        });

            if(window.tinyMCE !== undefined){
                tinyMCE.onAddEditor.add(function(mgr,ed) {
                // alert(ed);// do things with editor ed
                    ed.onChange.add(function(ed, l) {
                        $('#content_ifr' ).contents().find('a').attr('style','color:' + $('#sp_content_link_color').val() ).attr('data-mce-style','color:' + $('#sp_content_link_color').val() );
                    });
                });
            }
        
});