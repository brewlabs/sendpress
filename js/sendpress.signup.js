;(function ( $, window, document, undefined ) {


    $(document).ready(function($) {
    	var $signups = $('.sendpress-signup');
        
        $signups.each(function(){
            $form = $(this),
            $error = $form.find('#error'),
            $thanks = $form.find('#thanks'),
            $exists = $form.find('#exists');

            //$error.hide();
            //$thanks.hide();
        });

    	$signups.submit(function(e){
            e.preventDefault();

            var signup = {},
                $form = $(this),
                $error = $form.find('#error'),
                $thanks = $form.find('#thanks'),
                $formwrap = $form.find('#form-wrap'),
                $submit = $form.find('#submit'),
                $ajaxInd = $form.find('.ajaxloader'),
                submit_ok = true,
                emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
             
            $error.hide();
            $ajaxInd.show();

            signup['first'] = $form.find('.sp_firstname').val();
            signup['last'] = $form.find('.sp_lastname').val();
            signup['email'] = $form.find('.sp_email').val();
            signup['phonenumber'] = $form.find('.sp_phonenumber').val();
            signup['salutation'] = $form.find('.sp_salutation').val();
            signup['listid'] = "";//$form.find('.sp_list').val();
            signup['formid'] = $form.data('form-id');

            $form.find("input:checkbox.sp_list:checked").each(function(){
                signup['listid'] += $(this).val() +",";
            });
            if(signup['listid'] === ""){
                signup['listid'] = $form.find('.sp_list').val();
            }

            $form.find('.sp_custom_field').each(function(){
                var $obj = $(this);
                signup[$obj.attr('id')] = $obj.val();
            });

            //adding this back in for post notifications
            $form.find('.custom-field').each(function(){
                var $obj = $(this);
                signup[$obj.attr('id')] = $obj.val();
            });

            signup['action'] = 'sendpress_subscribe_to_list';

            if( signup.email.length === 0 ){
                $error.show();
                $error.html('<div class="item">*'+sendpress.missingemail+'.</div>');
                submit_ok = false;
            }else if(!emailReg.test(signup.email)) {
                $error.show();
                $error.html('<div class="item">'+sendpress.invalidemail+'.</div>');
                submit_ok = false;
            }

            $.each($form.find('.required'), function(index, value){
                if($(value).val().length === 0 && submit_ok){
                    $error.show();
                    $error.html('<div class="item">'+sendpress.required+'</div>');
                    submit_ok = false;
                }
            });



            if(submit_ok){
                $submit.attr("disabled", "disabled");



                jQuery.post(sendpress.ajaxurl, signup, function(response){
                
                    try {
                        response = JSON.parse(response);
                    } catch (err) {
                        // Invalid JSON.
                        $submit.removeAttr("disabled");
                        if(!jQuery.trim(response).length) {
                            response = { error: 'Server returned empty response during add attempt'};
                        } else {
                            response = {error: 'Server returned invalid response:<br /><br />' + response};
                        }
                    }

                   if(response.success){
                        $error.hide();
                        $formwrap.hide();
                        if(response.exists){
                            $exists.show();
                        }else{
                            $thanks.show();
                        }
                        
                    }else{
                        //possibly display an error here
                        
                    }
                });

            }else{
                $ajaxInd.hide();
            }

            return false;
            
        });
        /*
    	$('.sendpress-signup input').bind('focus blur',function(e){
    		var $obj = $(this),
    			$value = $obj.val(),
    			$orig = $obj.attr('orig');

    		if(e.type === "focus"){
    			if($value === $orig){
    				$obj.val('');
    			}
    		}else{
    			if($value === ''){
    				$obj.val($orig);
    			}
    		}

    	});
        */
        $('.sendpress-signup .post-notifications-list').on('click', function(e){
            var $obj = $(this),
                $form = $obj.closest('form.sendpress-signup'),
                type = $obj.data('type'),
                hidden = "<input class='post-notifications custom-field' type='hidden' value='"+type+"' name='post_notifications' id='post_notifications'/>";

            if($obj.is(':checked')){
                $form.append(hidden);
            }else{
                $form.find('.post-notifications').remove();
            }
        });

    });

}).call( window.sendpress=window.sendpress || {}, jQuery, window, document );







