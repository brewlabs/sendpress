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

    function test_get_encoding_types(){
       $this->assertEquals(array("8bit", "7bit", "binary", "base64",  "quoted-printable"), SendPress_Data::get_encoding_types());

    }


    function test_get_charset_types(){
     $this->assertEquals( array(
        "UTF-8",
        "UTF-7",
        "BIG5",
        "ISO-8859-1",
        "ISO-8859-2",
        "ISO-8859-3",
        "ISO-8859-4",
        "ISO-8859-5",
        "ISO-8859-6",
        "ISO-8859-7",
        "ISO-8859-8",
        "ISO-8859-9",
        "ISO-8859-10",
        "ISO-8859-13",
        "ISO-8859-14",
        "ISO-8859-15",
        "Windows-1251",
        "Windows-1252"), SendPress_Data::get_charset_types());

    }




}
