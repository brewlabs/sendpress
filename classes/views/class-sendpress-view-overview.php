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
/*
SendPress_Template_Manager::update_template_content();


echo spnl_do_email_tags( 'here is some {subscriber_list} content that should run tags' , 0 , 0 );


SPNL()->log->add('Bad Email','This email can not be sent');
echo "<pre>";
print_r( SPNL()->log->get_logs() );
echo "</pre>";
*/
?>
<br>


<div class="sp-row">
<div class="w-25 sp-first">
<div class="panel panel-success">
              <div class="panel-heading">
                	<div class="w-50 pull-left">
                   <span class="glyphicon glyphicon-user fa-5x"></span>
                  </div>
                  <div class="w-50 pull-left text-right">
                    <p class="announcement-heading"><?php echo SendPress_Data::bd_nice_number(SendPress_Data::get_count_subscribers()); ?></p>
                    <p class="announcement-text">Subscribers</p>
                  </div>
                  	<br class="clear">
              </div>
              <!--
              <a href="#">
                <div class="panel-footer announcement-bottom">
                  <div class="w-50 pull-left">
                      View Lists
                    </div>
                  <div class="w-50 pull-left text-right">
                      <span class="glyphicon glyphicon-circle-arrow-right"></span>
                    </div>
                  <br class="clear">
                </div>
              </a>
          -->
            </div>
  </div>

<div class="w-25 ">
<div class="panel panel-info">
              <div class="panel-heading">
                	<div class="w-50 pull-left">
                   <span class="glyphicon glyphicon-envelope fa-5x"></span>
                  </div>
                  <div class="w-50 pull-left text-right">
                    <p class="announcement-heading"><?php $report = SendPress_Data::get_last_report(); 
                    if($report){
                    $x= SendPress_Data::get_opens_count($report->ID);
                    if(empty( $x )){
                    	echo "0";
                    } else {
                    	echo $x[0]->count;
                    }
                    } else {
                      echo "0";
                    }
                    ?></p>
                    <p class="announcement-text">Opens in last send</p>
                  </div>
                  	<br class="clear">
              </div>
              <!--
              <a href="#">
                <div class="panel-footer announcement-bottom">
                  <div class="w-50 pull-left">
                      View Report
                    </div>
                  <div class="w-50 pull-left text-right">
                      <span class="glyphicon glyphicon-circle-arrow-right"></span>
                    </div>
                  <br class="clear">
                </div>
              </a>
          -->
            </div>
  </div>
  <div class="w-25 ">
<div class="panel panel-warning">
              <div class="panel-heading">
                	<div class="w-50 pull-left">
                   <span class="glyphicon glyphicon-link fa-5x"></span>
                  </div>
                  <div class="w-50 pull-left text-right">
                    <p class="announcement-heading"><?php $c = SendPress_Data::get_clicks_count($report->ID);
                    if($report){
                    if(empty( $c )){
                    	echo "0";
                    } else {
                    	echo $c[0]->count;
                    } 
                    } else {
                      echo "0";
                    }?></p>
                    <p class="announcement-text">Clicks in last send</p>
                  </div>
                  	<br class="clear">
              </div>
             <!--
              <a href="#">
                <div class="panel-footer announcement-bottom">
                  <div class="w-50 pull-left">
                      View Mentions
                    </div>
                  <div class="w-50 pull-left text-right">
                      <span class="glyphicon glyphicon-circle-arrow-right"></span>
                    </div>
                  <br class="clear">
                </div>
              </a>
          -->
            </div>
  </div>
  <div class="w-25 ">
<div class="panel panel-danger">
              <div class="panel-heading">
                	<div class="w-50 pull-left">
                   <span class="glyphicon glyphicon-envelope fa-5x"></span>
                  </div>
                  <div class="w-50 pull-left text-right">
                    <p class="announcement-heading"><?php echo SendPress_Data::emails_in_queue(); ?></p>
                    <p class="announcement-text">Emails in Queue</p>
                  </div>
                  	<br class="clear">
              </div>
              <!--
              <a href="#">
                <div class="panel-footer announcement-bottom">
                  <div class="w-50 pull-left">
                      View Queue
                    </div>
                  <div class="w-50 pull-left text-right">
                      <span class="glyphicon glyphicon-circle-arrow-right"></span>
                    </div>
                  <br class="clear">
                </div>
              </a>
          -->
            </div>
  </div>

  </div>

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