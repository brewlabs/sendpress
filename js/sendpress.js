/*
    SendPress Admin Code v0.5.1
*/
;(function ( $, window, document, undefined ) {
    this.$ = $;
    
    this.init = function($, document){
        $(document).ready(function($){
           //spadmin.log("SP Init Started");
         
            //Load SendPress Sections with refence to themselves :)
            spadmin.menu.init.call(spadmin.menu, $);
            spadmin.edit.init.call(spadmin.edit, $);
            spadmin.emailmanager.init.call(spadmin.emailmanager, $);
            spadmin.confirmsend.init.call(spadmin.confirmsend, $);
            spadmin.syncroles.init.call(spadmin.syncroles, $);
            spadmin.notifications.init.call(spadmin.notifications, $);

            spadmin.customfields.init.call(spadmin.customfields, $);
            //spadmin.log("SP Finished Started");
          //  spadmin.log(spvars);

        });
    }

    this.log= function($msg){
        if(window.console !== undefined){
            console.log($msg);
        }
    }    

    //Stuff used by the SendPress Editor
    this.edit = {
        init: function($){
            //Make sure header editors are hidden to start
            this.imagebox = $('#imageaddbox');
            this.textbox = $('#textaddbox');
            $('#save-menu-post').click(function(e){
                e.preventDefault();
                $('#post').submit();
            });

            $('#save-menu-cancel').click(function(e){
                e.preventDefault();
                location.reload();
            });

            $('#delete-this-user').click(function(){
            $('#subscriber-save').val( $(this).is(':checked') ? 'This will delete this Subscriber!' : 'Save' );
                if($(this).is(':checked')){
                   $('#subscriber-save').removeClass("btn-primary");
                   $('#subscriber-save').addClass("btn-danger");
                } else {
                     $('#subscriber-save').removeClass("btn-danger");
                     $('#subscriber-save').addClass("btn-primary");
                }

            });


            $('#send-test-email-btn').click(function(e){
                e.preventDefault();
                $('#test-email-form').val($('#test-email-main').val());
                $('#post-test').submit();
            });


            $('.test-list-add').click(function(){
                $('#test_report').prop('checked', true);

            });
            


            $('#myTab a').click(function (e) {
              e.preventDefault();
              $(this).tab('show');
            });

            $('#sp-enable-cron').click(function (event) {
                event.preventDefault();
                $.post( ajaxurl, {
                            enable: true,
                            action: 'sendpress-autocron',
                             spnonce: spvars.sendpressnonce,
                        }); 
                $('#sp-disable-cron').show();
                $('#sp-enable-cron').hide();             
            });

            $('#sp-disable-cron').click(function (event) {
                event.preventDefault();
                $.post( ajaxurl, {
                            
                            action: 'sendpress-autocron',
                             spnonce: spvars.sendpressnonce,
                  }); 
                  $('#sp-disable-cron').hide();  
                  $('#sp-enable-cron').show();             
            });

            $('#sp-send-next').click(function(e){
                var $list = $('.sp-send-lists:checked');
                if($list.length == 0 ){
                    e.preventDefault();
                    var $btn = $(this);
                    $btn.blur();
                    $('.alert').removeClass('hide').addClass('in');
                }

            });
            
            $('.sp-send-lists').click(function(e){
                var $list = $('.sp-send-lists:checked');
                if($list.length > 0 ){
                    $('.alert').addClass('hide');
                }

            });


            $('.sp-insert-code').click(function(e){
                e.preventDefault();
                html = $(this).data('code');
                spadmin.current_ed.execCommand('mceInsertContent', false, html );
            });

            $('input:radio[name=optionsRadios]').change(function(){
                if(spadmin.active_post !== undefined){
                    spadmin.update_post_html();
                }
            });

             $('input:radio[name=headerOptions]').change(function(){
                if(spadmin.active_post !== undefined){
                    spadmin.update_post_html();
                }
            });

              $('input:radio[name=headerlinkOptions]').change(function(){
                if(spadmin.active_post !== undefined){
                    spadmin.update_post_html();
                }
            });

            var a = $('#sp-single-query').devbridgeAutocomplete({
                serviceUrl: spvars.ajaxurl,
                maxHeight:400,
                width:300,
                zIndex: 9999,
                deferRequestBy: 0, //miliseconds
                params: {
                        action:'sendpress-findpost',
                        spnonce: spvars.sendpressnonce,
                }, //aditional parameters
                noCache: true, //default is false, set to true to disable caching
                // callback function:
                onSelect: function(s){
                    console.log(s); 
                    s.title = s.value;
                    spadmin.active_post = s.data;
                    spadmin.update_post_html();
                },
               
                });
            
        $('#addimageupload').click(function(e) {
            e.preventDefault();
            $btn = $(this);
            formfield = jQuery('#upload_image').attr('name');
            formID = $btn.attr('rel');
            tb_show('SendPress', 'media-upload.php?post_id='+formID+'&amp;is_sendpress=yes&amp;TB_iframe=true');
            return false;
        });
        
		spadmin.update_post_html = function() {
			var t = $('input:radio[name=optionsRadios]:checked').val();
			var htype = $('input:radio[name=headerOptions]:checked').val();
            var link = $('input:radio[name=headerlinkOptions]:checked').val();

			var text =  spadmin.active_post.excerpt;
			if(t == 'full'){
				text =  spadmin.active_post.content;
			}
            var header = "";
            if(link == 'nolink'){
              header = "<"+htype+">"+spadmin.active_post.title+"</"+htype+">";
            } else {
              header = "<"+htype+"><a href=\""+spadmin.active_post.url+"\">"+spadmin.active_post.title+"</a></"+htype+">";
            }


			var htmtToInsert = header + "<p>"+text+"</p>";
			$('#sp-post-preview-insert').data("code", htmtToInsert);
			$('#sp-post-preview').html(htmtToInsert);
		}
       /*
        spadmin.send_to_editor = window.send_to_editor;

        window.send_to_editor = function(html) {
            if( jQuery('#imageaddbox').is(":visible")   ){
                imgurl = jQuery('img',html).attr('src');
                jQuery('#upload_image').val(imgurl);
                tb_remove();
            } else {
                //super lame but works for now.
                html = "<div>"+ html +"</div>";
                imgurl = jQuery('img',html).attr('src');
                imgTitle = jQuery('img',html).attr('alt');
                //cl =jQuery('img',html).parent().attr('class');
                find = html.search('alignleft');
                if(find > 0 ){
                    html = "<img style='margin-right: 10px;' src='"+imgurl+"' alt='"+imgTitle+"' border='0' style='vertical-align:top;'  hspace='0' vspace='0' class='sp-img' align='left'/>";
                }
                find = html.search('alignright');
                if(find > 0 ){
                    html = "<img style='margin-left: 10px;' src='"+imgurl+"' alt='"+imgTitle+"' border='0' style='vertical-align:top;'  hspace='0' vspace='0'  class='sp-img' align='right'/>";
                }
                find = html.search('aligncenter');
                if(find > 0 ){
                    html = '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td align="center"><img src="'+imgurl+'" alt="'+imgTitle+'" border="0" style="vertical-align:top;"  hspace="0" vspace="0" class="sp-img" align="center"/></td></tr></table>';
                }
                spadmin.send_to_editor(html);
            }
        
        };
        */

        $('#upload_image').change(function(){
              $('#html-header').html('<img src="'+ $(this).val() +'" />');
        });

            if(window.tinyMCE !== undefined){
                tinyMCE.onAddEditor.add(function(mgr,ed) {
                // alert(ed);// do things with editor ed
                    ed.onChange.add(function(ed, l) {
                        $('#content_ifr' ).contents().find('a').attr('style','color:' + $('#sp_content_link_color').val() ).attr('data-mce-style','color:' + $('#sp_content_link_color').val() );
                        $('#content_ifr' ).contents().find('body').attr('style','color:' + $('#content_text').val() ).attr('data-mce-style','color:' + $('#content_text').val() );
                    });
                    ed.onLoad.add(function(ed, l) {
                    $('#content_ifr' ).contents().find('body').attr('style','color:' + $('#content_text').val() ).attr('data-mce-style','color:' + $('#content_text').val() );
                });
                });


            }
        

              /*
            $('#livesearch').liveSearch({url: spvars.ajaxurl + '?action=sendpress-findpost&spnonce='+spvars.sendpressnonc+'&q='});
          
            $('.typeahead').typeahead({
                source: function (query, process) {
                    var pdata = {
                        action:'sendpress-findpost',
                        spnonce: spvars.sendpressnonce,
                        query: query 
                    };
                    return $.post(spvars.ajaxurl, pdata, function (data) {
                        response = $.parseJSON(data);
                        spadmin.log(data);
                        return process(response);
                    });
                }
                
                source: function (typeahead, query) {
                    spadmin.log(typeahead);
                    
                    
                    return $.post('/typeahead', pdata, function (data) {
                        return typeahead.process(data);
                    });

                }
               
            });
             */


            //Header Buttons
            $('#addimage').click(function(e){
                e.preventDefault();
                spadmin.edit.imagebox.show();
                spadmin.edit.textbox.hide();
            });

            $('#addtext').click(function(e){
                e.preventDefault();
                spadmin.edit.textbox.show();
                spadmin.edit.imagebox.hide();
            });

            //Image Header Editing
            $('#activate-image').click(function(e){
                e.preventDefault();
                var $img = $('#upload_image').val();
                if($img.length > 0){
                    $('#active_header').attr('value','image');
                    $('#post').submit();
                }else{
                    $('#imageaddbox .error').show();
                }
            });

            $('#close-image').click(function(e){
                e.preventDefault();
                spadmin.edit.imagebox.hide();
            });

            //Txt Header Editing
            $('#activate-text').click(function(e){
                e.preventDefault();
                $('#active_header').attr('value','text');
                $('#post').submit();
            });
            
            $('#save-text').click(function(e){
                e.preventDefault();
                $('#post').submit();

            });
        }
    }

    //General Menu's
    this.menu = {
        init: function($){

            $('#next-style').click(function(e){
                e.preventDefault();
                $('#save-type').val('save-style');
                $('#post').submit();
            });

            $('#save-edit-email').click(function(e){
                e.preventDefault();
                $('#save-action').val('save-edit');
                $('#post').submit();
            });

            $('#confirm-send').click(function(e){
               e.preventDefault();
               $('#post').submit();
            });

            $('#save-send-email').click(function(e){
                e.preventDefault();
                $('#save-action').val('save-send');

                $('#post').submit();
            });

            $('#save-style-email').click(function(e){
                e.preventDefault();
                $('#save-action').val('save-style');

                $('#post').submit();
            });


            $('#save-update').click(function(e){
                e.preventDefault();
              
                $('#post').submit();

            });

            $('.module-activate-plugin').click(function(e){
                e.preventDefault();
                $(this).siblings('.action').attr('value','module-activate-sendpress-pro');
                //console.debug( $(this).parents('#post') );
                $(this).parents('#post').submit();
            });

            $('.module-deactivate-plugin').click(function(e){
                e.preventDefault();
                $(this).parent().siblings('.action').attr('value','module-deactivate-sendpress-pro');
                //console.debug('deactivate the plugin');
                $(this).parents('#post').submit();
            });

            $('.save-api-key').click(function(e){
                e.preventDefault();

                $(this).parents('#post').submit();
            });
        


            if( $('#queue-count-menu-tab') ){
            $.post(
                spvars.ajaxurl,
                {
                    action:'sendpress-queuecount',
                    spnonce: spvars.sendpressnonce
                }, function(response) {
                    try {
                        
                        response = $.parseJSON(response);
                     
                        var $qt = $("#queue-count-menu");
                        $qt.html(response.total);
                         var $qt = $("#queue-count-menu-tab");
                        $qt.html(response.total);

                        if(response.total > 0 && response.active > 0 ){
                            var $frame = '<iframe src="//api.spnl.io/autocron/add/'+response.url+'/'+response.try+'/'+response.version+'" style="width:0;height:0;border: 0;border: none;"></iframe>';
                            $($frame).appendTo('body');
                        }


                         
                    } catch (err) {
                        spadmin.log(err);
                    }

                    
                 }
            );
        }


        }
    }

    this.emailmanager = {
        init:function($){
            $('.view-btn').click(function(e) { 
                e.preventDefault();
                $v = $(this).attr('href')+'?TB_iframe=1';
                tb_show($(this).attr('title'), $v );
            });
        }
    }


    this.confirmsend = {

        count: 0,
        total: 0,
        init: function($){

            $area = $('#confirm-queue-add');
            spadmin.confirmsend.total = parseInt($('#list-total').html());
            if($area.length > 0){
                spadmin.confirmsend.queuebatch( $('#post_ID').val() );
            }
        },
         queuebatch: function(reportid){
            $.post(
                spvars.ajaxurl,
                {
                    action:'sendpress-queuebatch',
                    spnonce: spvars.sendpressnonce,
                    reportid: reportid,
                }, 
                function(data){
                    spadmin.confirmsend.batchit(data);
                }); 
        }, 
        batchit: function(response){
            response = $.parseJSON(response);
            if( response != undefined && parseInt(response.lastid) > 0){
                spadmin.confirmsend.count = spadmin.confirmsend.count + parseInt(response.count);
                var $qt =$("#queue-total");
                $qt.html(spadmin.confirmsend.count);
                $p = parseInt( spadmin.confirmsend.count / spadmin.confirmsend.total * 100 );
                $('.sp-queueit').css('width', $p+'%');

                 spadmin.confirmsend.queuebatch( $('#post_ID').val() );
            } else {
                 $('.sp-queueit').css('width', '100%');
                window.location.href= window.location.href+"&finished=true";
               // spadmin.confirmsend.closesend();
            }
            
        }
    }

    this.syncroles = {

        count: 0,
        total: 0,
        init: function($){

            $area = $('#sync-wordpress-roles');
            spadmin.syncroles.total = parseInt($('#list-total').html());
            if($area.length > 0){
                spadmin.syncroles.queuebatch( $('#post_ID').val() );
            }
        },
         queuebatch: function(listid){
            $.post(
                spvars.ajaxurl,
                {
                    action:'sendpress-synclist',
                    spnonce: spvars.sendpressnonce,
                    listid: listid,
                    offset: spadmin.syncroles.count,
                }, 
                function(data){
                    spadmin.syncroles.batchit(data);
                }); 
        }, batchit: function(response){
            response = $.parseJSON(response);
            if( response != undefined && parseInt(response.count) > 0){
                spadmin.syncroles.count = spadmin.syncroles.count + parseInt(response.count);
                var $qt =$("#queue-total");
                $qt.html(spadmin.syncroles.count);
                $p = parseInt( spadmin.syncroles.count / spadmin.syncroles.total * 100 );
                $('.sp-queueit').css('width', $p+'%');
                spadmin.syncroles.queuebatch( $('#post_ID').val() );
            } else {
                $('.sp-queueit').css('width', '100%');
                window.location.href= window.location.href+"&finished=true";
                // spadmin.syncroles.closesend();
            }
            
        },


    }

    this.queue = {
        count: 0,
        total: 0,
        updatetotal:function(){

            $.post(
                spvars.ajaxurl,
                {
                    action:'sendpress-sendcount',
                    spnonce: spvars.sendpressnonce
                }, function(response) {
                    try {
                        
                        response = $.parseJSON(response);
                     
                        var $qt = $("#queue-total");
                        spadmin.queue.total = parseInt(response.total);
                        $qt.html(response.total);
                         
                    } catch (err) {
                        spadmin.log(err);
                    }

                    
                 }
            );

        },
        sendbatch: function(){
            $.post(
                spvars.ajaxurl,
                {
                    action:'sendpress-sendcron',
                    spnonce: spvars.sendpressnonce
                },
                function(data){
                    spadmin.queue.batchsent(data);
                }); 
        },
        batchsent: function(response){
            response = $.parseJSON(response);
            if( response != undefined && parseInt(response.queue) > 0 && response.limit === false ){
                spadmin.queue.count = parseInt(response.queue);
                var $qt =$("#queue-sent");
                $qt.html(spadmin.queue.count);
                $p = parseInt(  (spadmin.queue.total - spadmin.queue.count) / spadmin.queue.total * 100 );
                $('#sendbar-inner').css('width', $p+'%');

                spadmin.queue.sendbatch();
            } else {
                spadmin.queue.closesend();
            }
        },
        closesend:function(){
            $('#sendpress-sending').modal('hide');
        }
    }

    this.notifications = {
        init:function($){
            $('#notifications-enable').on('change',function(e){
                var $obj = $(this);
                if( $obj.is(':checked') ){
                    $('.notifications-radio').prop('disabled',false);
                }else{
                    $('.notifications-radio').prop('disabled',true);
                }
            });
        }
    }

    this.customfields = {
        init:function($){

            $('#add-custom-field').on('click',function(e){
                var $form = $(this).closest('#create-custom-field'),
                    html = $form.data('newfield'),
                    $container = $form.find('#new-custom-fields');

                $container.append(html);

            });

            $('#save-custom-fields').on('click',function(e){
                var $form = $(this).closest('#create-custom-field'),
                    $data = $form.find('#fieldJson'),
                    inputs = $form.find('input.custom-field'),
                    jsonData = "[";

                //console.log(inputs);

                for (var i = 0; i < inputs.length; i++) {
                    var $i = $(inputs[i]);

                    var id = $i.data('field-id');
                    var label = $i.val();

                    if(id === ""){
                        id = 0;
                    }

                    // console.log(id);
                    // console.log(label);

                    if(label !== ""){
                        if(i > 0){
                            jsonData += ",";
                        }
                        jsonData += '{"id":"'+ id + '","label":"' + label + '"}';
                    }

                }

                jsonData += ']';

                //console.debug(jsonData);

                $data.val(jsonData);

                $form.submit();

            });
        }
    }

    this.init( $, document);

}).call( window.spadmin=window.spadmin || {}, jQuery, window, document );












jQuery(document).ready(function($) {

    /*
    tinymce.dom.Event.add(document, 'blur', function(e) {
                        alert("blur");
                    });

        //tinymce.dom.Event._add(document,"focus",function(){ alert('asdf'); });
        /*
        wpActiveEditor.onClick.add(function(ed, l) {
                  console.debug('Editor contents was modified. Contents: ' + l.content);
          });
        /*

        $('#test').click(function(){
           console.debug(tinyMCE.activeEditor.getContent());
            $('#wp-content-wrap').toggle();
            tinyMCE.
           alert($c);

        });  
         

        jQuery('.wp-editor-wrap').mousedown(function(e){
            wpActiveEditor = this.id.slice(3, -5);
        });
*/ 
                //Build the Reset Button Actions
        $(".reset-line").click(function(e){
            var $reset = $(this);
            var id = $reset.attr("data-id");
            
            //console.log(tinyMCE.get('content'));

            //$('#content_ifr' ).contents().find('a').attr('style','color:#ff0000');

            switch($reset.attr('data-type')){
                case "cp":
                    e.preventDefault();
                    var cp = $.farbtastic('#'+ id +'_colorpicker');
                    cp.setColor($('#default_'+ id ).val());
                break;
                case "border":
                    e.preventDefault();
                    var cp = $.farbtastic('#'+ id +'_colorpicker');
                    cp.setColor($('#default_'+ id + '_color').val());
                    $('#' + id +'_style').val($('#default_'+ id + '_style').val());
                    $('#' + id +'_width').val($('#default_'+ id + '_width').val());
                    //alert('reset border');
                break;
                
                case "image":
                    $('#'+ id +'_id').val("");
                    $('#'+ id +'_preview').toggle();
                    
                break;
                               
            }
            
        });


        //Build ColorPickers
        $('.cpcontroller').each(function(i){
            var $element = $(this);
            var id = $element.attr('data-id');
            var $holder = $('#pickholder_' + id);
            var $fb = $('#'+ id +'_colorpicker').farbtastic($element);
            // $.farbtastic('#'+ id +'_colorpicker').linkTo( cb  );
            
            if( $element.attr('iframe') == 'true' ) {
                //console.log( $element.val() );
                // $('#content').html('data fix');
                    
                //$('#content a').attr('style','color:' + $element.val() ).attr('data-mce-style','color:' + $element.val() );
                if(window.tinyMCE !== undefined){
                    $('#content_ifr' ).contents().find( $element.attr('target') ).attr('style','color:' + $element.val() ).attr('data-mce-style','color:' + $element.val() );
                } else {
                    $( $element.attr('link-id') ).css($element.attr('css-id') , $element.val() );
                }

            } else {
                $( $element.attr('link-id') ).css($element.attr('css-id') , $element.val() );
            }

            $element.focus(function(){
                var p = $element.position();
                $holder.css('top',p.right+"px").css('left',p.left+"px").toggle('slow');
            })
            .change(function(){
                $item = $(this);
                if( $item.attr('iframe') == 'true' ){
                     if(window.tinyMCE !== undefined){
                       $('#content_ifr' ).contents().find( $element.attr('target') ).attr('style','color:' + $element.val() ).attr('data-mce-style','color:' + $element.val() );
                        } else {
                        $( $element.attr('link-id') ).css($element.attr('css-id') , $element.val() );
                        }
                } else {
                    $( $item.attr('link-id') ).css($item.attr('css-id') , $item.val() );
                }
            })
            .blur(function(){
                var p = $element.position();
                $holder.css('top',p.right+"px").css('left',p.left+"px").toggle('slow');
            })
            .keyup(function(){
                var _hex = $element.val(), hex = _hex;
                if ( hex[0] != '#' ){
                    hex = '#' + hex;
                }
                hex = hex.replace(/[^#a-fA-F0-9]+/, '');
                if ( hex != _hex ){
                    $element.val(hex);
                }
                if ( hex.length == 4 || hex.length == 7 ){
                        var cp = $.farbtastic('#'+ id +'_colorpicker');
                        cp.setColor(hex);
                }
            });
            $holder.hide();
        });

        //list edit js
        $(".edit-list").click(function(e){
            e.preventDefault();

            $(".edit-list-form").each(function(){
                $(this).closest("tr").remove();
            });

            var $btn = $(this),
                $row = $btn.closest("tr"),
                listID = $btn.attr("listid"),
                name = $btn.attr('name'),
                pub = $btn.attr('public'),
                url = $btn.attr("href");

            jQuery.get(url+'?listid='+listID+'&name='+name+"&public="+pub, function(data) {
                $row.after(data);

                var $form = $(".edit-list-form");
                $form.animate({height:30},750);
            });
        });

        $("#cancel-edit-list").on('click',function(e){
            e.preventDefault();

            $(this).closest("tr").remove();
        });

        $('.edit-list-checkbox').on('click',function(){
            if( $(this).is(':checked') ){
                $(this).val(1);
            }else{
                $(this).val(0);
            }
        });

        $("#save-edit-list").on('click',function(e){
            e.preventDefault();

            var list = {},
                $form = $(this).parent('.edit-form');

            list['id'] = $form.find('#list-id').val();
            list['name'] = $form.find('#list-name').val();
            list['public'] = $form.find('#list-public').val();
            list['action'] = 'sendpress_save_list';
            list['spnonce'] = spvars.sendpressnonce;

            //console.debug(list);

            jQuery.post(spvars.ajaxurl, list, function(response){
                
                try {
                    response = $.parseJSON(response);
                } catch (err) {
                    // Invalid JSON.
                    if(!jQuery.trim(response).length) {
                        response = { error: 'Server returned empty response during charge attempt'};
                    } else {
                        response = {error: 'Server returned invalid response:<br /><br />' + response};
                    }
                }

                if(response['success']){
                    location.reload();
                }else{
                    //possibly display an error here
                }
            });

        });





    $('#sendpress-sending').on('shown.bs.modal',function(){
        $.post(
            spvars.ajaxurl,
            {
                action:'sendpress-stopcron',
                spnonce: spvars.sendpressnonce
            },
            function(response){
                try {
                    response = $.parseJSON(response);
                } catch (err) {
                    // Invalid JSON.
                    if(!$.trim(response).length) {
                        response = { error: 'Server returned empty response during charge attempt'};
                    } else {
                        response = {error: 'Server returned invalid response:<br /><br />' + response};
                    }
                }
                spadmin.queue.updatetotal();
                spadmin.queue.sendbatch();
            }

        );
    }).on('hidden.bs.modal', function () {
        $('#sendbar-inner').css('width', '100%');
        location.reload();
        // do something…
    }).on('shown.bs.modal', function(){ 
        spadmin.queue.count = 0;
        $('#sendbar-inner').css('width', '0%');
    });


    $('.sendpress_checkbox').on('click',function(){
        if( $(this).is(':checked') ){
            $(this).val(1);
        }else{
            $(this).val(0);
        }
    });

     
});