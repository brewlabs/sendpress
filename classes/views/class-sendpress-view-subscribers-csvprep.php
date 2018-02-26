<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

class SendPress_View_Subscribers_Csvprep extends SendPress_View_Subscribers {
	private $_import_fields = array('email','firstname','lastname','ip', 'status', 'phonenumber','salutation');

	function save(){
    //$this->security_check();
    $list_id_clean = SPNL()->validate->_int( 'listID' );
    $the_file = get_post_meta($list_id_clean ,'csv_import',true);
		$file= trim(SendPress_Data::read_file_to_str( $the_file ));
    $subscribers = SendPress_Data::csv_to_array($file);

    $total = count($subscribers);
    $map = array();
    $i = 0;
    foreach($subscribers[0] as $key=>$val) {
                                /* try to automatically match columns */
                              $map[SPNL()->validate->_string('colmatch'.$i)] = $i;

                                $i++;
                            }

    $file_chunks = array_chunk($subscribers, 200);
    $subscribers = null;
    global $wpdb;
    $chunks_count = 0;
    $wpdb->query('set session wait_timeout=600');
    foreach($file_chunks as $key_chunk => $csv_chunk){
          $result = SendPress_Data::import_csv_array( $csv_chunk, $map ,$list_id_clean );

          if($result !== false) $chunks_count++;
          else{
                // there was an error we try 3
                $try=0;
                while($result === false && $try < 3){
                    $result = SendPress_Data::import_csv_array( $csv_chunk , $map, $list_id_clean );
                    if($result !== false){
                         break;
                    }
                    $try++;
                }

                if($result === false ){
                  //                   return false;
                }
            }
            unset($file_chunks[$key_chunk]);
        }

        unlink($the_file);

        SendPress_Admin::redirect('Subscribers_Subscribers',array('listID'=> $list_id_clean));


	}

  function dropdown($value,$id){
    //$this->security_check();

    //print_r($this->_import_fields);

    $match = array_search($value,$this->_import_fields);


    $return = "<select name='colmatch{$id}'>";
    $found = false;
    foreach ($this->_import_fields as $field) {
        $selected = "";
        if($value == $field || strpos($value, $field) !== false ){
          $found = true;
          $selected = " selected='selected'";
        }
        $return .="<option $selected value='$field'>$field</option>";
      }
      if($found == false){
         $return .= "<option selected='selected' >No Match</option>";
      } else {
         $return .= "<option value='nomatch'>No Match</option>";
      }

       $return .= "</select>";
      return $return;



  }


	function html() {
    $list_id_clean = SPNL()->validate->_int( 'listID' );
    //JMH Needs updating once custom fields are done
    $custom_field_list = SendPress_Data::get_custom_fields_new();

    $columns=array("noimport"=>__("Don't Import",'sendpress'),
                    'email'=>__('Email','sendpress'),
                    'firstname'=>__('First name','sendpress'),
                    'lastname'=>__('Last name','sendpress'),
                    'ip'=>__('IP address','sendpress'),
                    'status'=>__('Status','sendpress'),
                    'phonenumber' => __('Phone Number', 'sendpress'),
                    'salutation' => __('Salutation', 'sendpress')
                    );

    foreach ($custom_field_list as $key => $value) {
        //print_r($value);

        $this->_import_fields[] = $value['custom_field_key'];

        $columns[$temp] = $value['custom_field_label'];


    }

    ?>
	<div id="taskbar" class="lists-dashboard rounded group">
	<h2><?php _e('Import CSV to ','sendpress'); ?><?php echo get_the_title( $list_id_clean ); ?></h2>
	</div>
	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form method="post" enctype="multipart/form-data" accept-charset="utf-8" >
		<?php
    $row = 1;
    $header = array();

    $file= trim(SendPress_Data::read_file_to_str(get_post_meta(  $list_id_clean ,'csv_import',true)));
    $subscribers = SendPress_Data::csv_to_array($file);
    $total = count($subscribers);
    ?>
    <?php _e('We found','sendpress'); ?> <?php echo $total; ?> <?php _e('lines in your csv','sendpress'); ?>.
    <table cellspacing="0" class="table  table-bordered table-striped table-condensed" >
                    <thead>
                        <tr class="thead">
                            <th><?php _e('Import Fields:','sendpress');?></th>
                            <?php

                            //moved column array up to the top of the function so I could add custom fields
                            $i=0;

                            $emailcolumnmatched=false;
                            //print_r($subscribers);
                            foreach($subscribers[0] as $key=>$val) {
                                /* try to automatically match columns */
                                $selected="";
                                $value=str_replace(array(" ","-","_"),"",strtolower($val));
                                echo "<th>" . $this->dropdown($value, $i) . "</th>";


                                $i++;
                            }
                            ?>
                        </tr>
                    </thead>
<?php


   $placeholder = false;
  if($total > 5){
    $loop = 4;
    $placeholder = true;
  } else {
    $loop = $total;
  }

  for ($i=0; $i < $loop; $i++) {
    $line = '<tr>';
    $line .="<td>$i</td>";
    $cols = 0;
    foreach ($subscribers[$i] as $key => $value) {
      $cols ++;
      $line .= "<td>$value</td>";

    }
    $line .="</tr>";
    echo $line;
  }
  if($placeholder == true){
    echo "<tr>";
     for ($i=0; $i <= $cols; $i++) {
        echo "<td>...</td>";
     }
    echo"</tr>";
    $last = end($subscribers);
    if(empty($last[0])){
       $total--;
       $last = prev($subscribers);
       if(empty($last[0])){
        $total--;
          $last = prev($subscribers);
        }
    }
    $line = '<tr>';
    $line .="<td>$total</td>";
    foreach ($last as $key => $value) {
      $line .= "<td>$value</td>";

    }
     $line .="</tr>";
    echo $line;
  }


?>
 	</table>
 <button type="submit" class="btn btn-primary"><?php _e('Start Import','sendpress'); ?></button>

<?php SendPress_Data::nonce_field(); ?>
	   </form>

<?php

	}

}
