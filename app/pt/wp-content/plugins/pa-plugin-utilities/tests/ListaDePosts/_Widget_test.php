<?php
require_once dirname(__FILE__) . '/../IASD__Master__test.php';

class IASD_ListaDePosts__Widget_test extends IASD__Master__test {


	function setUp() {
		parent::setUp();
//		$this->addFakePosts();

		//Default Response for ListaDePosts
		global $http;
		$http = $this->getMock('WP_Http');
		$http->expects($this->any())
			 ->method('get')
			 ->will($this->returnValue(array('body' => json_encode(null), 'response' => array('code' => 200))));
	}
}