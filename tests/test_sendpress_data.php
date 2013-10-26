<?php

/**
* Test For SendPress Data
 */
class WP_Test_SendPress_Data extends WP_UnitTestCase {

    /**
     * Run a simple test to ensure that the tests are running
     */
    function test_tests() {
            $this->assertTrue( true );
    }

    function test_nonce() {
            $this->assertEquals( 'sendpress-is-awesome',
                    SendPress_Data::nonce() );
    }
    
    function test_email_post_type(){
        
      $this->assertEquals( 'sp_newsletters',
                    SendPress_Data::email_post_type() );
    }
    
    function test_report_post_type(){
        
      $this->assertEquals( 'sp_report',
                    SendPress_Data::report_post_type() );
    }

}
