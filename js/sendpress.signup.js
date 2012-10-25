jQuery(document).ready(function($) {
	var $signups = $('.sendpress-signup');
    
    $signups.each(function(){
        $form = $(this),
        $error = $form.find('#error'),
        $thanks = $form.find('#thanks');

        $error.hide();
        $thanks.hide();
    });

	$signups.submit(function(e){
        e.preventDefault();

        var signup = {},
            $form = $(this),
            $error = $form.find('#error'),
            $thanks = $form.find('#thanks'),
            $formwrap = $form.find('#form-wrap'),
            $submit = $form.find('#submit'),
            
            submit_ok = true,
            emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
         $error.hide();

        signup['first'] = $form.find('#firstname').val();
        signup['last'] = $form.find('#lastname').val();
        signup['email'] = $form.find('#email').val();
        signup['listid'] = $form.find('#list').val();
        signup['action'] = 'sendpress_subscribe_to_list';

        if( signup.email.length === 0 ){
            $error.show();    
            $error.html('<div class="item">*Please enter your e-mail address.</div>');
            submit_ok = false;
        }else if(!emailReg.test(signup.email)) {
            $error.show();
            $error.html('<div class="item">Enter a valid email address.</div>');
            submit_ok = false;
        }

        if(submit_ok){
            $submit.attr("disabled", "disabled");
            jQuery.post(sendpress.ajaxurl, signup, function(response){
            
                try {
                    response = JSON.parse(response);
                } catch (err) {
                    // Invalid JSON.
                    $submit.removeAttr("disabled");
                    if(!jQuery.trim(response).length) {
                        response = { error: 'Server returned empty response during charge attempt'};
                    } else {
                        response = {error: 'Server returned invalid response:<br /><br />' + response};
                    }
                }

                if(response['success']){
                    $error.hide();
                    $formwrap.hide();
                    $thanks.show();
                }else{
                    //possibly display an error here
                }
            });

        }

        return false;
        
    });

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

	

});










