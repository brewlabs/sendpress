<?php

// Prevent loading this file directly
if ( !defined('SENDPRESS_VERSION') ) {
    header('HTTP/1.0 403 Forbidden');
    die;
}

class SendPress_View_Reports_Campaign extends SendPress_View_Reports{


    function html(){
        if(!defined('SENDPRESS_PRO_VERSION') ){
        ?>
        <br>



        <h3>Campaign based reports require SendPress Pro version 2.1.12.* or higher.</h3>
            <h3>You currently have version <?php echo SENDPRESS_PRO_VERSION; ?> installed.</h3>

         <?php } else { ?>
            <h3>Campaign based reports require SendPress Pro version 2.1.12.* or higher.</h3>
            <div class='well'>
                Upgrade to SendPress Pro now at <a href="https://www.sendpress.com">https://www.sendpress.com</a>.
                <br>Use discount code <b>PRO2019</b> at checkout and get 15% off your purchase of SendPress Pro.
           </div>
            <?php
        }

    }
}
SendPress_Admin::add_cap('Reports','sendpress_reports');
