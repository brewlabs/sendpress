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
SendPress_Template_Manager::update_template_content();
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
    <h2 class="nomargin nopadding"><?php echo SendPress_Data::bd_nice_number(SendPress_Data::get_total_subscribers()); ?></h2> <p class="fwb">Subscribers</p>  
  </div>
  <div class="sp-block sp-25">
    <h2 class="nomargin nopadding"><?php $report = SendPress_Data::get_last_report(); ?><?php echo SendPress_Data::emails_active_in_queue(); ?></h2> <p class="fwb">Emails Actively Sending</small></p>
  </div>
  <div class="sp-block sp-25">
    <h2 class="nomargin nopadding"><?php echo  SendPress_Data::emails_maxed_in_queue(); ?></h2> <p class="fwb">Emails Stuck in Queue</p>
  </div>
  <div class="sp-block sp-25">
    <h2 class="nomargin nopadding"><?php echo SENDPRESS_VERSION ?></h2> <p class="fwb">SendPress Version</p>
  </div>

</div>
<?php 
if($report){ 
$rec = get_post_meta($report->ID, '_send_last_count', true);
$this->panel_start($report->post_title ." <small style='color:#333;'>This email had " . $rec ." Recipients</small>");

?>

<div class="sp-row">
  <div class="sp-50 sp-first">
    <h4 style="text-align:center;">Opens</h4>
      <?php 
        $this->panel_start();
        $open = 0;
        $rec = get_post_meta($report->ID, '_send_last_count', true);
          if($report){
              $x= SendPress_Data::get_opens_count($report->ID);
              if(!empty( $x )){
                $open = $x[0]->count;
              }
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
          $unk = SendPress_Data::get_opens_unique_count($report->ID);
          if( !empty($unk) ){
            $ou = $unk[0]->count;
          }
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
  <h4 style="text-align:center;">Clicks</h4>
    <?php 
        $this->panel_start();
         $open = 0;
        $rec = get_post_meta($report->ID, '_send_last_count', true);
          if($report){
              $x= SendPress_Data::get_clicks_count($report->ID);
              if(!empty( $x )){
                $open = $x[0]->count;
              }
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

          $unk = SendPress_Data::get_clicks_unique_count($report->ID);
          if( !empty($unk) ){
            $ou = $unk[0]->count;
          }
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
</div>
<?php
        $this->panel_end();

}
      ?>




<div class="sp-row">
<div class="sp-33 sp-first">
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Recent Subscribers</h3>
  </div>
  <div class="panel-body">
  	<ul>
  	<?php 
  		$recent = SendPress_Data::get_optin_events();
  		foreach($recent as $item){
  			if(property_exists($item,'subscriberID')){
        echo "<li>";

  			$d = 	SendPress_Data::get_subscriber($item->subscriberID);
        if(property_exists($item,'eventdate')){
  			   echo date_i18n("m.d.Y" ,strtotime($item->eventdate) );
        }
        if(is_object($d)){
  			echo "<span class='sp-email'>" . $d->email . "</span>";
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
	    <h3 class="panel-title">Most Active Subscribers</h3>
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
	    <h3 class="panel-title">Go Pro!</h3>
	  </div>
	  <div class="panel-body">
	  	<ul>
	  		<li><a href="http://sendpress.com/purchase-pricing/">Advanced Reports</a></li>
	  		<li><a href="http://sendpress.com/purchase-pricing/">Check Spam Scores</a></li>
	  		<li><a href="http://sendpress.com/purchase-pricing/">Post Notifications</a></li>
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