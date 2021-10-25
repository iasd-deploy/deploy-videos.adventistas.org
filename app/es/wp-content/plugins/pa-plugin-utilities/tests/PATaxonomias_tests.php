<?php

class IASD_Taxonomias_tests extends WP_UnitTestCase {

	function test_if_gets_tax_menu() {
		//update_option('xtt_tax_server_url', 'http://tax.iasd.local/pt/');

		$return_arr = array();
		// $GLOBALS['http'] = $this->getMock('WP_Http');
		// $GLOBALS['http']->expects($this->any())
		// 				->method('request')
		// 				->with($this->stringContains('action=multisite-request'))
		// 				// ->with('http://tax.iasd.local/pt/wp-admin/admin-ajax.php?action=taxonomy-request&name=Test+Blog&url=http://iasd.local/', array(CURLOPT_RETURNTRANSFER => 1, 'CURLOPT_USERAGENT' => 'XTT PA Taxonomy Client', 'timeout'=>30))
		// 				->will($this->returnValue(json_encode(array('return'=>'ok'))));

		// IASD_Taxonomias::RequestMultiSiteInformation();
	}

}
