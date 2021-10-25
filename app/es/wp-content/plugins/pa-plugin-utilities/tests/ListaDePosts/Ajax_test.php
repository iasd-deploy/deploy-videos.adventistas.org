<?php
require_once dirname(__FILE__) . '/../IASD__Master__test.php';

class IASD_ListaDePosts_Ajax_test extends IASD__Master__test {
	public $taxonomies = array();
	public $fakePostsToAdd = 10;
	public $fakePostTypes = array('fake_post_a', 'fake_post_b');

	function decodeToArray($items) {
		if(is_string($items)) {
			$decoded = json_decode($items);
			if($decoded)
				$items = $decoded;
		}
		if(is_object($items))
			$items = (array) $items;

		if(is_array($items)) {
			foreach($items as $k => $v)
				$items[$k] = $this->decodeToArray($v);
		}

		return $items;
	}

	function test_classExists() {
		$this->assertTrue(class_exists('IASD_ListaDePosts_Ajax'));
	}

	function testHTTPObjectIsCorrect() {
		global $http;
		$http = null;
		$gotHttp = IASD_ListaDePosts_Ajax::GetHTTPObject();
		global $http;

		$this->assertEquals($http, $gotHttp);
		$this->assertEquals('WP_Http', get_class($gotHttp));
	}

	function test_hooksAreSet() {
		$this->assertEquals(10, has_action( 'wp_ajax_' . IASD_ListaDePosts_Ajax::RULES,              array('IASD_ListaDePosts_Ajax', 'Rules')));
		$this->assertEquals(10, has_action( 'wp_ajax_' . IASD_ListaDePosts_Ajax::CHECKSOURCE,        array('IASD_ListaDePosts_Ajax', 'CheckSource')));

		$this->assertEquals(10, has_action( 'wp_ajax_nopriv_' . IASD_ListaDePosts_Ajax::LOCAL_RULES, array('IASD_ListaDePosts_Ajax', 'LocalRules')));
		$this->assertEquals(10, has_action( 'wp_ajax_' . IASD_ListaDePosts_Ajax::LOCAL_RULES,        array('IASD_ListaDePosts_Ajax', 'LocalRules')));
		
		$this->assertEquals(10, has_action( 'admin_menu',                                            array('IASD_ListaDePosts_Ajax', 'AddSubmenuPage')));
	}

	function testMethodViews() {
		$all_views = IASD_ListaDePosts_Views::GetViews();
		$some_views = IASD_ListaDePosts_Ajax::Views();

		$this->assertEquals(count($all_views), count($some_views));
		$this->assertEquals(array_keys($all_views), array_keys($some_views));
	}

	function testMethodForceRulesNoParam() {
		global $http;
		$http = $this->getMock('WP_Http');
		$http->expects($this->never())
			 ->method('get')
			 ->will($this->returnValue(array('body' => json_encode(null), 'response' => array('code' => 200))));

		IASD_ListaDePosts_Ajax::ForceRules();
	}

	function testMethodForceRulesWithParam() {
		global $http;
		$http = $this->getMock('WP_Http');
		$http->expects($this->atLeastOnce())
			 ->method('get')
			 ->will($this->returnValue(array('body' => json_encode(null), 'response' => array('code' => 200))));
		$_GET['forcerules'] = 1;
		IASD_ListaDePosts_Ajax::ForceRules();
	}

	function testSubMenuPageHook() {
		global $current_user;
		$previous = $current_user;
		$current_user = get_user_by('slug', 'admin');

		$admins = get_super_admins();
		$admins[] = 'admin';
		update_site_option('site_admins', $admins);

		global $submenu;
		global $_parent_pages;
		$this->assertNull($submenu);
		$this->assertNull($_parent_pages);

		do_action('admin_menu');
		global $submenu;
		global $_parent_pages;

		$this->assertNotNull($submenu);
		$this->assertNotNull($_parent_pages);

		$this->assertArrayHasKey('pa-adventistas',$submenu);
		$this->assertArrayHasKey('pa-ldp-sources',$_parent_pages);
		$this->assertEquals('pa-adventistas',$_parent_pages['pa-ldp-sources']);

		global $current_user;
		$current_user = $previous;
	}

	function testSourcesListWithAllErrors() {
		ob_start();
		IASD_ListaDePosts_Ajax::SourcesList();
		$contents = ob_get_contents();
		ob_end_clean();

		$this->assertNotEmpty($contents, 'Rendered something');
	}

	function testMethodRemoteRules() {
		ob_start();
		IASD_ListaDePosts_Ajax::LocalRules();
		$resultsEncoded1 = ob_get_contents();
		ob_end_clean();
		$resultsDecoded1 = $this->decodeToArray($resultsEncoded1);

		$resultsDecoded2 = IASD_ListaDePosts_Ajax::LocalRules(false);

		$this->assertTrue(is_array($resultsDecoded1));
		$this->assertTrue(is_array($resultsDecoded2));

		$this->assertArrayHasKey('sources',$resultsDecoded1);
		$this->assertArrayHasKey('sources',$resultsDecoded2);

		$this->assertArrayHasKey('local',$resultsDecoded1['sources']);
		$this->assertArrayHasKey('local',$resultsDecoded2['sources']);

		$this->assertArrayHasKey('post_type',$resultsDecoded1['sources']['local']);
		$this->assertArrayHasKey('post_type',$resultsDecoded2['sources']['local']);

		$this->assertEquals($resultsDecoded1['sources']['local']['post_type'],$resultsDecoded2['sources']['local']['post_type']);

		$expectedContent = IASD_ListaDePosts_AJAX::PostTypes();
		$expectedJson = json_encode($expectedContent);
		$this->assertEquals($expectedContent,$resultsDecoded2['sources']['local']['post_type']);
		$this->assertContains($expectedJson, $resultsEncoded1);
	}

	function testMethodRemoteRules_Authors() {

		$user_id = wp_insert_user(array('user_login' => 'login_name', 'user_pass' => 'sdagvjdkjfhgjk@sdfuigchs'));
		update_user_meta($user_id, 'wp_user_level', 2);

		ob_start();
		IASD_ListaDePosts_Ajax::Rules();
		$resultsEncoded1 = ob_get_contents();
		ob_end_clean();
		$resultsDecoded1 = $this->decodeToArray($resultsEncoded1);
		$resultsDecoded2 = IASD_ListaDePosts_Ajax::Rules(false);

		$this->assertTrue(is_array($resultsDecoded1));
		$this->assertTrue(is_array($resultsDecoded2));

		$this->assertArrayHasKey('sources',$resultsDecoded1);
		$this->assertArrayHasKey('sources',$resultsDecoded2);

		$this->assertArrayHasKey('local',$resultsDecoded1['sources']);
		$this->assertArrayHasKey('local',$resultsDecoded2['sources']);

		$this->assertArrayHasKey('authors',$resultsDecoded1['sources']['local']);
		$this->assertArrayHasKey('authors',$resultsDecoded2['sources']['local']);

		$this->assertEquals($resultsDecoded1['sources']['local']['authors'],$resultsDecoded2['sources']['local']['authors']);

		$expectedContent = IASD_ListaDePosts_Ajax::Authors();
		$expectedJson = json_encode($expectedContent);
		$this->assertEquals($expectedContent,$resultsDecoded2['sources']['local']['authors']);
		$this->assertContains($expectedJson, $resultsEncoded1);
	}

	function testMethodRemoteRulesActions() {
		$expectedContent = IASD_ListaDePosts_Ajax::Rules(false);
		ob_start();
		IASD_ListaDePosts_Ajax::Rules();
		$expectedJson = ob_get_contents();
		ob_end_clean();

		ob_start();
		do_action('wp_ajax_' . IASD_ListaDePosts_Ajax::RULES);
		$resultsPriv = ob_get_contents();
		ob_end_clean();
		ob_start();
		do_action('wp_ajax_nopriv_' . IASD_ListaDePosts_Ajax::RULES);
		$resultsNoPriv = ob_get_contents();
		ob_end_clean();

		$this->assertEmpty($resultsNoPriv, 'Without privileges it shouldn\'t get any information');

		$this->assertEquals($expectedJson, $resultsPriv, 'The rules json should be equal');

		$resultsDecoded = $this->decodeToArray($resultsPriv);

		$this->assertEquals($expectedContent, $resultsDecoded, 'The rules array should be equal');
	}

	function testMethodValidateSourceUrl() {
		$this->assertEquals('http://adventistas.org/pt/', IASD_ListaDePosts_Ajax::ValidateSourceUrl('adventistas.org/pt'));
		$this->assertEquals('http://adventistas.org/pt/', IASD_ListaDePosts_Ajax::ValidateSourceUrl('adventistas.org/pt/'));
		$this->assertEquals('http://adventistas.org/pt/', IASD_ListaDePosts_Ajax::ValidateSourceUrl('http://adventistas.org/pt'));
		$this->assertEquals('http://adventistas.org/pt/', IASD_ListaDePosts_Ajax::ValidateSourceUrl('http://adventistas.org/pt/'));
	}

	function testMethodCheckSourceRequestNotMade() {
		$source = 'a.b';
		$fieldId = md5(rand());

		$ajaxResponse = array();
		$ajaxResponse['source']   = IASD_ListaDePosts_Ajax::ValidateSourceUrl($source);
		$ajaxResponse['status']   = false;

		global $http;
		$http = $this->getMock('WP_Http');

		$http->expects($this->never())->method('get');

		$nonEcho = IASD_ListaDePosts_Ajax::CheckSource($source, $fieldId, false);
		$nonEcho = $this->decodeToArray($nonEcho);

		$this->assertArrayHasKey('field', $nonEcho);
		$this->assertArrayHasKey('status', $nonEcho);
		$this->assertArrayHasKey('url', $nonEcho);

		$this->assertFalse($nonEcho['status']);
		$this->assertArrayNotHasKey('post_type', $nonEcho);

		global $http;
		$http = $this->getMock('WP_Http');

		$http->expects($this->never())->method('get');

		ob_start();
		IASD_ListaDePosts_Ajax::CheckSource($source, $fieldId);
		$echoEncoded = ob_get_contents();
		ob_end_clean();
		$echo = $this->decodeToArray($echoEncoded);

		$this->assertEquals($echo, $nonEcho);

	}

	function inner_checkSource($source, $fieldId, $returnExpected = array(), $timesExpected) {
		$timesExpectedNonEcho = clone $timesExpected;
		$timesExpectedEcho1 = clone $timesExpected;
		$timesExpectedEcho2 = clone $timesExpected;

		$ajaxResponse = $returnExpected;
		$ajaxResponse['source']   = IASD_ListaDePosts_Ajax::ValidateSourceUrl($source);
		$ajaxResponse['status']   = false;

		global $http;
		$http = $this->getMock('WP_Http');

		$returnValue = array('body' => false, 'response' => array('code' => 400));
		if(count($returnExpected)) {
			$returnValue['body'] = json_encode($returnExpected);
			$returnValue['response']['code'] = 200;
		}

		$requestUrl = $ajaxResponse['source'] . 'wp-admin/admin-ajax.php?action=' . IASD_ListaDePosts_Ajax::LOCAL_RULES;

		global $http;
		$http = $this->getMock('WP_Http');

		$http->expects($timesExpectedNonEcho)
			 ->method('get') //Metodo a ser chamado do objeto!!!
			 ->with($requestUrl, array())
			 ->will($this->returnValue($returnValue));
		$nonEcho = IASD_ListaDePosts_Ajax::CheckSource($source, $fieldId, false);
		$nonEcho = $this->decodeToArray($nonEcho);

		$this->assertArrayHasKey('field', $nonEcho);
		$this->assertArrayHasKey('status', $nonEcho);
		if(count($returnExpected)) {
			$this->assertArrayHasKey('url', $nonEcho);
			$this->assertTrue($nonEcho['status']);
			$this->assertArrayHasKey('post_type', $nonEcho);
			$this->assertArrayHasKey('authors', $nonEcho);
		} else {
			$this->assertFalse($nonEcho['status']);
			$this->assertArrayNotHasKey('post_type', $nonEcho);
			$this->assertArrayNotHasKey('authors', $nonEcho);
		}

		//Using Action
		global $http;
		$http = $this->getMock('WP_Http');

		$http->expects($timesExpectedEcho1)
			 ->method('get') //Metodo a ser chamado do objeto!!!
			 ->with($requestUrl, array())
			 ->will($this->returnValue($returnValue));

		$_REQUEST['source'] = $source;
		$_REQUEST['fieldId'] = $fieldId;

		ob_start();
		do_action('wp_ajax_' . IASD_ListaDePosts_Ajax::CHECKSOURCE);
		$echoEncoded1 = ob_get_contents();
		ob_end_clean();
		$echo1 = $this->decodeToArray($echoEncoded1);

		$this->assertEquals($echo1, $nonEcho);


		//Using Method
		global $http;
		$http = $this->getMock('WP_Http');

		$http->expects($timesExpectedEcho2)
			 ->method('get') //Metodo a ser chamado do objeto!!!
			 ->with($requestUrl, array())
			 ->will($this->returnValue($returnValue));

		ob_start();
		IASD_ListaDePosts_Ajax::CheckSource($source, $fieldId);
		$echoEncoded2 = ob_get_contents();
		ob_end_clean();
		$echo2 = $this->decodeToArray($echoEncoded2);

		$this->assertEquals($echoEncoded1, $echoEncoded2);
		$this->assertEquals($echo2, $echo1);
	}

	function testMethodCheckSourceRequestFailed() {
		$this->inner_checkSource('adventistas.org', md5(rand()), null, $this->once());
	}

	function testMethodCheckSourceRequestSuccess() {
		$returnExpected = IASD_ListaDePosts_Ajax::Rules(false);

		$this->inner_checkSource('adventistas.org', md5(rand()), $returnExpected, $this->once());
	}

	function testMethodRules() {
		$number = $this->getWidgetNumber();
		$all_instances = get_option(IASD_ListaDePosts::option_id, array());
		$instance_outra = array('source_id' => 'outra', 'source_extra' => md5(time()), 'number' => $number);
		$all_instances[$number] = $instance_outra;
		update_option(IASD_ListaDePosts::option_id, $all_instances);

		$sources = array_merge(IASD_ListaDePosts::BasicSources(), IASD_ListaDePosts::OtherSources());
		$external = count($sources) - 2; //LOCAL and OUTRA doesn't count

		$empty_response_source = array('sources' => array('local' => array('post_type' => array(), 'authors' => array())));

		global $http;
		$http = $this->getMock('WP_Http');

		$http->expects($this->exactly($external))
			 ->method('get')
			 ->will($this->returnValue(array('body' => json_encode($empty_response_source), 'response' => array('code' => 200))));

		ob_start();
		IASD_ListaDePosts_Ajax::Rules();
		$echoContent = ob_get_contents();
		ob_end_clean();

		global $http;
		$http = $this->getMock('WP_Http');

		$http->expects($this->never())
			 ->method('get')
			 ->will($this->returnValue(array('body' => json_encode($empty_response_source), 'response' => array('code' => 200))));
		ob_start();
		do_action('wp_ajax_' . IASD_ListaDePosts_Ajax::RULES);
		$actionContent = ob_get_contents();
		ob_end_clean();

		global $http;
		$http = $this->getMock('WP_Http');

		$http->expects($this->exactly($external))
			 ->method('get')
			 ->will($this->returnValue(array('body' => json_encode($empty_response_source), 'response' => array('code' => 200))));
		$methodContent = IASD_ListaDePosts_Ajax::Rules(false, true);

		$this->assertEquals($echoContent, $actionContent);

		$contents = $this->decodeToArray($actionContent);

		$this->assertArrayHasKey('translations', $contents);
		$this->assertEquals($contents['translations'], IASD_ListaDePosts_Ajax::Translations());

		$this->assertArrayHasKey('views', $contents);
		$this->assertEquals($contents['views'], IASD_ListaDePosts_Ajax::Views());

		$this->assertArrayHasKey('taxonomies', $contents);
		$this->assertEquals($contents['taxonomies'], IASD_Taxonomias::GetAllTaxonomies());

		$this->assertArrayHasKey('sources', $contents);
		$this->assertLessThanOrEqual(count($sources), count($contents['sources']));

		foreach($contents['sources'] as $source) {
			$this->assertArrayHasKey('post_type', $source);
			$this->assertTrue(is_array($source['post_type']));
			$this->assertArrayHasKey('authors', $source);
			$this->assertTrue(is_array($source['authors']));
		}

		global $http;
		$http = new WP_Http();
	}

	function test_Cache() {
		global $http;
		$http = $this->getMock('WP_Http');

		$maxRequests = count(array_merge(IASD_ListaDePosts::BasicSources(), IASD_ListaDePosts::OtherSources())) - 2; //Ignore LOCAL and OUTRA

		$empty_response_source = array('sources' => array('local' => array('post_type' => array(), 'authors' => array())));

		$http->expects($this->exactly($maxRequests))
			 ->method('get')
			 ->will($this->returnValue(array('body' => json_encode($empty_response_source), 'response' => array('code' => 200))));

		$rulesA = IASD_ListaDePosts_Ajax::Rules(false, true);
		$rulesB = IASD_ListaDePosts_Ajax::Rules(false, false); //should use cache

		$this->assertEquals($rulesA, $rulesB);

		ob_start();
		IASD_ListaDePosts_Ajax::Rules();
		$rulesC = IASD_ListaDePosts_Ajax::decodeToArray(ob_get_contents());
		ob_end_clean();

		$this->assertEquals($rulesA, $rulesC);
	}
}