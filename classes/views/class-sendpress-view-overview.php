<?php
// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
	header('HTTP/1.0 403 Forbidden');
	die;
}

if( !class_exists('SendPress_View_Overview') ){
class SendPress_View_Overview extends SendPress_View{

	function tracking( $get, $sp ){
		SendPress_Option::set('allow_tracking', $get['allow_tracking']);
		SendPress_Admin::redirect('Overview');
	}

	
	function html($sp){
		 SendPress_Tracking::event('Overview Tab');
		 //print_r( SendPress_Data::get_subcribers_by_meta('test','test') );

	
global $wp_version;

$classes = 'sp-welcome-panel';

$option = get_user_meta( get_current_user_id(), 'show_sp_welcome_panel', true );
// 0 = hide, 1 = toggled to show or single site creator, 2 = multisite site owner
$hide = 0 == $option || ( 2 == $option && wp_get_current_user()->user_email != get_option( 'admin_email' ) );
//if ( $hide )
//	$classes .= ' hidden';
/*
$args = array( 'post_type' => 'sendpress_list','numberposts'     => -1,
	    'offset'          => 0,
	    'orderby'         => 'post_title',
	    'order'           => 'DESC', );
		$lists = get_posts( $args );
$sp->send_optin(1,array('1','2','3'),$lists);
*/
list( $display_version ) = explode( '-', $wp_version );
//SendPress_Template_Manager::update_template_content();
/*



echo spnl_do_email_tags( 'here is some {subscriber_list} content that should run tags' , 0 , 0 );


SPNL()->log->add('Bad Email','This email can not be sent');
echo "<pre>";
print_r( SPNL()->log->get_logs() );
echo "</pre>";
*/
?>
<br>



<div class="sp-row ">

  <div class="sp-block sp-25 sp-first"> 
    <h2 class="nomargin nopadding"><?php echo SendPress_Data::bd_nice_number(SendPress_Data::get_total_subscribers()); ?></h2> <p class="fwb"><?php _e('Subscribers', 'sendpress');?></p>  
  </div>
  <div class="sp-block sp-25">
    <h2 class="nomargin nopadding"><?php $report = SendPress_Data::get_last_report(); ?><?php echo SendPress_Data::emails_active_in_queue(); ?></h2> <p class="fwb"><?php _e('Emails Actively Sending', 'sendpress');?></small></p>
  </div>
  <div class="sp-block sp-25">
    <h2 class="nomargin nopadding"><?php echo  SendPress_Data::emails_maxed_in_queue(); ?></h2> <p class="fwb"><?php _e('Emails Stuck in Queue', 'sendpress');?></p>
  </div>
  <div class="sp-block sp-25">
    <h2 class="nomargin nopadding"><?php echo SENDPRESS_VERSION ?></h2> <p class="fwb"><?php _e('SendPress Version', 'sendpress');?></p>
  </div>

</div>
<?php 
if($report){ 
$rec = get_post_meta($report->ID, '_send_last_count', true);
$this->panel_start($report->post_title ." <small style='color:#333;'>".__('This email had', 'sendpress')." ". $rec ." ".__('Recipients', 'sendpress')."</small>");

?>

<div class="sp-row">
  <div class="sp-50 sp-first">
    <h4 style="text-align:center;"><?php _e('Opens', 'sendpress');?></h4>
      <?php 
        $this->panel_start();
        $open = 0;
        $rec = get_post_meta($report->ID, '_send_last_count', true);
          if($report){
            $open= SendPress_Data::get_opens($report->ID);
            $p = $open/$rec * 100;
          }
        ?>
        <div class="sp-row">
        <div class="sp-50 sp-first">
          <div style="float:left;">
          <div id="myStat" class="chartid" data-dimension="150" data-text="<?php echo floor($p); ?>%" data-info="Total Opens" data-width="15" data-fontsize="30" data-percent="<?php echo floor($p); ?>" data-fgcolor="#61a9dc" data-bgcolor="#eee" data-fill="#ddd" data-total="<?php echo  $rec; ?>" data-part="<?php echo  $open; ?>" data-icon="long-arrow-up" data-icon-size="28" data-icon-color="#fff"></div>
         </div>
         <div style="text-align:center;">
         <h5>Total</h5>
         <?php echo $open; ?>
        </div>
        </div>
        <div class="sp-50">
        <?php 
          $ou = 0;
          $ou =  SendPress_Data::get_opens_unique_total($report->ID);
          $px = $ou/$rec * 100;

        ?>
        <div style="float:left;">
          <div id="myStat" class="chartid" data-dimension="150" data-text="<?php echo floor($px); ?>%" data-info="Unique Opens" data-width="15" data-fontsize="30" data-percent="35" data-fgcolor="#85d002" data-bgcolor="#eee" data-fill="#ddd" data-total="<?php echo  $rec; ?>" data-part="<?php echo  $ou; ?>" data-icon="long-arrow-up" data-icon-size="28" data-icon-color="#fff"></div>
        </div>
          <div style="text-align:center;">
          <h5>Unique</h5>
          <?php echo $ou; ?>
          </div>
       </div>
       </div>
        
      <?php
        $this->panel_end();
      ?>
  </div>
  <div class="sp-50">
  <h4 style="text-align:center;"><?php _e('Clicks', 'sendpress');?></h4>
    <?php 
        $this->panel_start();
         $click = 0;
        $rec = get_post_meta($report->ID, '_send_last_count', true);
          if($report){
              $click= SendPress_Data::get_clicks($report->ID);
             
              $p = $click/$rec * 100;
          }
     ?>
     <div class="sp-row">
        <div class="sp-50 sp-first">
          <div style="float:left;">
          <div id="myStat" class="chartid" data-dimension="150" data-text="<?php echo floor($p); ?>%" data-info="Total Opens" data-width="15" data-fontsize="30" data-percent="<?php echo floor($p); ?>" data-fgcolor="#61a9dc" data-bgcolor="#eee" data-fill="#ddd" data-total="<?php echo  $rec; ?>" data-part="<?php echo  $click; ?>" data-icon="long-arrow-up" data-icon-size="28" data-icon-color="#fff"></div>
         </div>
         <div style="text-align:center;">
         <h5><?php _e('Total', 'sendpress');?></h5>
         <?php echo $click; ?>
         </div>
        </div>
        <div class="sp-50">
        <?php 
          $ou = 0;

          $ou = SendPress_Data::get_clicks_unique_total($report->ID);
          
          $px = $ou/$rec * 100;

        ?>
        <div style="float:left;">
          <div id="myStat" class="chartid" data-dimension="150" data-text="<?php echo floor($px); ?>%" data-info="Unique Opens" data-width="15" data-fontsize="30" data-percent="35" data-fgcolor="#85d002" data-bgcolor="#eee" data-fill="#ddd" data-total="<?php echo  $rec; ?>" data-part="<?php echo  $ou; ?>" data-icon="long-arrow-up" data-icon-size="28" data-icon-color="#fff"></div>
        </div>
        <div style="text-align:center;">
          <h5><?php _e('Unique', 'sendpress');?></h5>
          <?php echo $ou; ?>
         </div>
       </div>
       </div>
        
     <?php
        $this->panel_end();
      ?>
  </div>
</div>
<?php
        $this->panel_end();

}
      ?>




<div class="sp-row">
<div class="sp-33 sp-first">
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title"><?php _e('Recent Subscribers', 'sendpress');?></h3>
  </div>
  <div class="panel-body">
  	<table class="table table-striped table-condensed">
    <tr>
    <th>Date</th>
    <th>List</th>
    <th><div style="text-align:right;">Email</div></th>
    </tr>
  	<?php 
  		$recent = SendPress_Data::get_subscribed_events();
  		foreach($recent as $item){
        echo "<tr>";
         echo "<td>";
  			if(property_exists($item,'subscriberID')){
       
  			$d = 	SendPress_Data::get_subscriber($item->subscriberID);
        
        if(property_exists($item,'eventdate')){
  			   echo date_i18n("m.d.y" ,strtotime($item->eventdate) );
        }
          echo "</td>";
echo "<td >";
           if(property_exists($item,'listID')){
           echo get_the_title($item->listID);
        }
         echo "</td>";
           echo "<td align='right'>";
        if(is_object($d)){
  			echo  $d->email ."<br>";
        }
        
       echo "</td>";
  			echo "</tr>";
  		  }
      }

  		

  	?>
  </table>
  </div>
</div>
</div>
<div class="sp-33">
	<div class="panel panel-default">
	  <div class="panel-heading">
	    <h3 class="panel-title"><?php _e('Most Active Subscribers', 'sendpress');?></h3>
	  </div>
	  <div class="panel-body">
	  	<ul>
	  	<?php
	  	$recent = SendPress_Data::get_most_active_subscriber();
  		foreach($recent as $item){
        if(property_exists($item,'subscriberID')){
  			echo "<li>";
  			$d = 	SendPress_Data::get_subscriber($item->subscriberID);
        if(is_object($d)){
  		    echo  $d->email;
        }
  			echo "</li>";
      }
  		}
	  	?>
	  	</ul>
	  </div>
	</div>
</div>
<div class="sp-33">
	<div class="panel panel-default">
	  <div class="panel-heading">
	    <h3 class="panel-title"><?php _e('Go Pro!', 'sendpress');?></h3>
	  </div>
	  <div class="panel-body">
	  	<ul>
	  		<li><a href="http://sendpress.com/purchase-pricing/"><?php _e('Advanced Reports', 'sendpress');?></a></li>
	  		<li><a href="http://sendpress.com/purchase-pricing/"><?php _e('Check Spam Scores', 'sendpress');?></a></li>
	  		<li><a href="http://sendpress.com/purchase-pricing/"><?php _e('Post Notifications', 'sendpress');?></a></li>
	  	</ul>
   
	  </div>
	</div>
</div>
</div>

<script>
jQuery( document ).ready(function($) {
        $('.chartid').circliful();
    });
</script>
<!--
<div class="panel panel-default">
  <div class="panel-body">
   <h2>Welcome to SendPress</h2>
  </div>
</div>

-->
<?php
	}

}
// Add Access Controll!
SendPress_Admin::add_cap('Overview','sendpress_view');
//SendPress_View_Overview::cap('sendpress_access');
}
