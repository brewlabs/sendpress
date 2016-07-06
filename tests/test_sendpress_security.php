<?php
/**
* Test For SendPress Data
 */
class WP_Test_SendPress_Security extends WP_UnitTestCase {

	function test_html(){
		$sec = new SendPress_Security();
		$html =  "<p style='color:#000;'>red</p>";
		$_GET['html'] = $html;
		$d = $sec->_html('html');
		$this->assertEquals( $html, $d);
	}

}