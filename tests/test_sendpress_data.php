<?php
require_once '../../sendpress.php';
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
}
