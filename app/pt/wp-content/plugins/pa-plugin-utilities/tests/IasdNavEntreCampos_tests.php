<?php


class IasdNavEntreCampos_tests extends WP_UnitTestCase{

	function setUp() {
		parent::setUp();
	}

	function test_if_class_exists() {
		$this->assertTrue(class_exists('IasdNavEntreCampos'));
	}

	function test_if_hooks_were_applied() {
		$this->assertGreaterThan(0, has_action('init', array('IasdNavEntreCampos','Init')), 'IasdNavEntreCampos::Init');

		$this->assertGreaterThan(0, has_action('refresh_blog_details', array('IasdNavEntreCampos','UpdateTaxSiteList')), 'IasdNavEntreCampos::UpdateTaxSiteList');
//		$this->assertGreaterThan(0, has_action('refresh_blog_details', array('IasdNavEntreCampos','TriggerMultiSiteUpdate')), 'IasdNavEntreCampos::TriggerMultiSiteUpdate');

		$this->assertGreaterThan(0, has_action('IASD_Taxonomias::UpdateFinish', array('IasdNavEntreCampos','TriggerMultiSiteUpdate')), 'IasdNavEntreCampos::TriggerMultiSiteUpdate');
		$this->assertGreaterThan(0, has_action('wp_ajax_TriggerMultiSiteUpdate', array('IasdNavEntreCampos','TriggerMultiSiteUpdate')), 'IasdNavEntreCampos::TriggerMultiSiteUpdate');
		$this->assertGreaterThan(0, has_action('wp_ajax_nopriv_TriggerMultiSiteUpdate', array('IasdNavEntreCampos','TriggerMultiSiteUpdate')), 'IasdNavEntreCampos::TriggerMultiSiteUpdate');

		$this->assertGreaterThan(0, has_action(IasdNavEntreCampos::UPDATE_ACTION, array('IasdNavEntreCampos','TriggerMultiSiteUpdate')), 'IasdNavEntreCampos::TriggerMultiSiteUpdate');
	}

	function test_if_can_create_blog() {
		global $http, $wpdb;
		update_site_option('xtt_tax_server_url', 'http://tax.iasd.dev.nextt.com.br/pt/');
		$host = explode('.', WP_TESTS_DOMAIN);
		$subdomain = $host[0];

		$body = 'install='.$subdomain.'&sites[]=/mulher/&sites[]=portal_home';

		$returnValue = json_encode(array('return'=>'OK'));
		$http = $this->getMock('WP_Http');

		$http->expects($this->atLeastOnce())
			 ->method('request')
			 ->with('http://tax.iasd.dev.nextt.com.br/pt/wp-admin/admin-ajax.php?action=updateMultiSiteList', array('method' => 'POST', 'body' => $body, 'timeout'=>30))
			 ->will($this->returnValue($returnValue));

		$args = array('domain' => 'iasd.dev.nextt.com.br', 'path' => '/mulher/', 'title' =>'Ministério das Mulheres' , 'meta' => array('public'=>1, 'deleted'=>0, 'archived'=>0) );
		$return_id_newblog = $this->factory->blog->create_object($args);

		$current_site = get_current_site();

		$this->assertGreaterThan(0,$return_id_newblog);
	}

	function test_if_sends_right_site_list_when_blog_is_deleted() {
		// $this->markTestSkipped();
		global $http, $wpdb;
		update_site_option('xtt_tax_server_url', 'http://tax.iasd.dev.nextt.com.br/pt/');
		// $this->markTestSkipped();
		$host = explode('.', WP_TESTS_DOMAIN);
		$subdomain = $host[0];
		# setup http mock blog create
		$http = $this->getMock('WP_Http');
		$http->expects($this->atLeastOnce())
			 ->method('request')
			 ->with('http://tax.iasd.dev.nextt.com.br/pt/wp-admin/admin-ajax.php?action=updateMultiSiteList', array('method' => 'POST', 'body' => 'install='.$subdomain.'&sites[]=/mulher/&sites[]=portal_home', 'timeout'=>30))
			 ->will($this->returnValue($returnValue));

		$args = array('domain' => 'iasd.local', 'path' => '/mulher/', 'title' =>'Ministério das Mulheres' , 'meta' => array('public'=>1, 'deleted'=>0, 'archived'=>0) );
		$return_id_newblog = $this->factory->blog->create_object($args);
		$blogs = $wpdb->get_results("SELECT blog_id, domain, path FROM $wpdb->blogs WHERE public = '1' AND archived = '0' AND deleted = '0' ORDER BY blog_id DESC", ARRAY_A );
		$count_start = count($blogs);
		$this->assertEquals(2, $count_start , 'Blog count should be 2' );

		# setup http mock blog delete
		$http = $this->getMock('WP_Http');
		$http->expects($this->atLeastOnce())
			 ->method('request')
			 ->with('http://tax.iasd.dev.nextt.com.br/pt/wp-admin/admin-ajax.php?action=updateMultiSiteList', array('method' => 'POST', 'body' => 'install='.$subdomain.'&sites[]=portal_home', 'timeout'=>30))
			 ->will($this->returnValue($returnValue));

		$return_delete = wpmu_delete_blog( $return_id_newblog, true );
		$blogs = $wpdb->get_results("SELECT blog_id, domain, path FROM $wpdb->blogs WHERE public = '1' AND archived = '0' AND deleted = '0' ORDER BY blog_id DESC", ARRAY_A );
		$count_end = count($blogs);
		$this->assertEquals(1, $count_end , 'Blog count should be 1' );

	}

	function test_if_sends_right_site_list_when_blog_is_archived() {
		// $this->markTestSkipped();
		global $http, $wpdb;
		update_site_option('xtt_tax_server_url', 'http://tax.iasd.dev.nextt.com.br/pt/');
		// $this->markTestSkipped();
		$host = explode('.', WP_TESTS_DOMAIN);
		$subdomain = $host[0];
		# setup http mock blog create
		$http = $this->getMock('WP_Http');
		$http->expects($this->atLeastOnce())
			 ->method('request')
			 ->with('http://tax.iasd.dev.nextt.com.br/pt/wp-admin/admin-ajax.php?action=updateMultiSiteList', array('method' => 'POST', 'body' => 'install='.$subdomain.'&sites[]=/mulher/&sites[]=portal_home', 'timeout'=>30))
			 ->will($this->returnValue($returnValue));

		$args = array('domain' => 'iasd.local', 'path' => '/mulher/', 'title' =>'Ministério das Mulheres' , 'meta' => array('public'=>1, 'deleted'=>0, 'archived'=>0) );
		$return_id_newblog = $this->factory->blog->create_object($args);
		$blogs = $wpdb->get_results("SELECT blog_id, domain, path FROM $wpdb->blogs WHERE public = '1' AND archived = '0' AND deleted = '0' ORDER BY blog_id DESC", ARRAY_A );
		$count_start = count($blogs);
		$this->assertEquals(2, $count_start , 'Blog count should be 2' );

		# setup http mock blog archive
		$http = $this->getMock('WP_Http');
		$http->expects($this->atLeastOnce())
			 ->method('request')
			 ->with('http://tax.iasd.dev.nextt.com.br/pt/wp-admin/admin-ajax.php?action=updateMultiSiteList', array('method' => 'POST', 'body' => 'install='.$subdomain.'&sites[]=portal_home', 'timeout'=>30))
			 ->will($this->returnValue($returnValue));

		$return_archive = update_blog_status( $return_id_newblog, 'archived', 1 );

	}

	function test_if_sends_right_site_list_when_blog_is_created() {
		// $this->markTestSkipped();
	    global $http;
	    update_site_option('xtt_tax_server_url', 'http://tax.iasd.dev.nextt.com.br/pt/');
	    $host = explode('.', WP_TESTS_DOMAIN);
		$subdomain = $host[0];
	    // $this->markTestSkipped();
		$returnValue = json_encode(array('return'=>'OK'));
		$http = $this->getMock('WP_Http');
		$http->expects($this->atLeastOnce())
			 ->method('request')
			 ->with('http://tax.iasd.dev.nextt.com.br/pt/wp-admin/admin-ajax.php?action=updateMultiSiteList', array('method' => 'POST', 'body' => 'install='.$subdomain.'&sites[]=/jovens/&sites[]=portal_home', 'timeout'=>30))
			 ->will($this->returnValue($returnValue));

		$args = array('domain' => 'iasd.local', 'path' => '/jovens/', 'title' =>'Ministerio Jovem', 'meta' => array('public'=>1, 'deleted'=>0, 'archived'=>0));
		$this->factory->blog->create_object($args);
	}


	// verificar se a cron foi instalada com repeticao diaria
	// verificar se sempre que a action da cron é executada ela faz o que deveria...

	function test_if_cron_is_set_daily() {	

		$this->assertEquals('daily', wp_get_schedule(IasdNavEntreCampos::UPDATE_ACTION ));	

	}

	function test_getMultiSiteList_result_root_blog() {	
		
		global $http;

		update_site_option('xtt_tax_server_url', 'http://tax.iasd.dev.nextt.com.br/pt/');
		
		$returnValue = array('body'=>'OK');
		$http = $this->getMock('WP_Http');
		$http->expects($this->atLeastOnce())
			 ->method('request')
			 ->with('http://tax.iasd.dev.nextt.com.br/pt/wp-admin/admin-ajax.php?action=getMultiSiteList', array('method' => 'POST', 'body' => 'slug=portal_home', 'timeout'=>30))
			 ->will($this->returnValue($returnValue));

		apply_filters(IasdNavEntreCampos::UPDATE_ACTION);

		$this->assertEquals(get_option('pa-multisite-menu') , 'OK' );
		$this->assertEquals(get_option('pa-multisite-menu-compilado') , '' );

	}
	

	function test_getMultiSiteList_result_pt() {	

		global $http;

		$args = array('domain' => 'iasd.dev', 'path' => '/pt/mulher/', 'title' =>'Ministério das Mulheres', 'meta' => array('public'=>1, 'deleted'=>0, 'archived'=>0));	
		$return_id_newblog = $this->factory->blog->create_object($args);
		switch_to_blog( $return_id_newblog );
		update_site_option('xtt_tax_server_url', 'http://tax.iasd.dev.nextt.com.br/pt/');
		
		$returnValue = array('body'=>'OK');
		$http = $this->getMock('WP_Http');
		$http->expects($this->atLeastOnce())
			 ->method('request')
			 ->with('http://tax.iasd.dev.nextt.com.br/pt/wp-admin/admin-ajax.php?action=getMultiSiteList', array('method' => 'POST', 'body' => 'slug=mulher', 'timeout'=>30))
			 ->will($this->returnValue($returnValue));

		apply_filters(IasdNavEntreCampos::UPDATE_ACTION);

		$this->assertEquals(get_option('pa-multisite-menu') , 'OK' );
		$this->assertEquals(get_option('pa-multisite-menu-compilado') , '' );

	}

	function test_getMultiSiteList_result_es() {	
		
		global $http;

		$args = array('domain' => 'iasd.dev', 'path' => '/es/mulher/', 'title' =>'Ministério das Mulheres', 'meta' => array('public'=>1, 'deleted'=>0, 'archived'=>0));	
		$return_id_newblog = $this->factory->blog->create_object($args);
		switch_to_blog( $return_id_newblog );
		update_site_option('xtt_tax_server_url', 'http://tax.iasd.dev.nextt.com.br/es/');
		
		$returnValue = array('body'=>'OK');
		$http = $this->getMock('WP_Http');
		$http->expects($this->atLeastOnce())
			 ->method('request')
			 ->with('http://tax.iasd.dev.nextt.com.br/es/wp-admin/admin-ajax.php?action=getMultiSiteList', array('method' => 'POST', 'body' => 'slug=mulher', 'timeout'=>30))
			 ->will($this->returnValue($returnValue));

		apply_filters(IasdNavEntreCampos::UPDATE_ACTION);

		$this->assertEquals(get_option('pa-multisite-menu') , 'OK' );
		$this->assertEquals(get_option('pa-multisite-menu-compilado') , '' );

	}

	function test_render_initial_pt() {	

		global $http;

		$args = array('domain' => 'iasd.dev', 'path' => '/pt/teste-sede-filho', 'title' =>'Ministério das Mulheres', 'meta' => array('public'=>1, 'deleted'=>0, 'archived'=>0));	
		$return_id_newblog = $this->factory->blog->create_object($args);

		switch_to_blog( $return_id_newblog );

		$args = array('name' => 'Teste sede', 'taxonomy' => 'xtt-pa-sedes');
		$term_parent = $this->factory->term->create_object($args);

		$args = array('name' => 'Teste sede filho', 'taxonomy' => 'xtt-pa-sedes', 'parent' => $term_parent);
		$this->factory->term->create_object($args);

		update_site_option('xtt_tax_server_url', 'http://tax.iasd.dev.nextt.com.br/pt/');
		
		$json_string = '{"teste-sede-filho":"http:\/\/iasd.dev\/teste_filho","teste-sede":"http:\/\/iasd.dev\/"}0';
		update_option('pa-multisite-menu', $json_string);
		update_option('pa-multisite-main-url', 'http://iasd.dev/pt');

		ob_start();
		IasdNavEntreCampos::RenderMultiSiteMenu();
		$response = ob_get_contents();

		$assert1 = '<div class="dropdown iasd-dropdown-navigation visible-desktop">';
		$assert2 = '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">';
		$assert3 = '<li class="heading"><h1>Uniões</h1></li>';
		$assert4 = '<a href="" title="Teste sede (TESTE-SEDE)"><h2>Teste sede (TESTE-SEDE)</h2></a>';
		$assert5 = '<li class="heading"><h3>Teste sede (TESTE-SEDE)</h3></li>';
		$assert6 = '<li><a href="http://iasd.dev/" title="Portal TESTE-SEDE">Portal TESTE-SEDE</a></li>';
		$assert7 = '<li class="dsa-link"><a href="http://iasd.dev/pt" title="Clique para ver o Portal Adventista" class="btn">Divisão Sul-Americana (DSA)</a></li>';
		$assert8 = '<li><a href="http://teste-sede-filho.iasd.dev/ptteste-sede-filho" title="Teste sede filho (TESTE-SEDE-FILHO)">Teste sede filho (TESTE-SEDE-FILHO)</a></li>';

		$this->assertContains($assert1, $response);
		$this->assertContains($assert2, $response);
		$this->assertContains($assert3, $response);
		$this->assertContains($assert4, $response);
		$this->assertContains($assert5, $response);
		$this->assertContains($assert6, $response);
		$this->assertContains($assert7, $response);
		$this->assertContains($assert8, $response);

	}

	function test_render_initial_es() {	

		global $http;

		$args = array('domain' => 'iasd.dev', 'path' => '/es/teste-sede-filho/', 'title' =>'Ministério das Mulheres', 'meta' => array('public'=>1, 'deleted'=>0, 'archived'=>0));	
		$return_id_newblog = $this->factory->blog->create_object($args);

		switch_to_blog( $return_id_newblog );

		$args = array('name' => 'Teste sede', 'taxonomy' => 'xtt-pa-sedes');
		$term_parent = $this->factory->term->create_object($args);

		$args = array('name' => 'Teste sede filho', 'taxonomy' => 'xtt-pa-sedes', 'parent' => $term_parent);
		$this->factory->term->create_object($args);

		update_site_option('xtt_tax_server_url', 'http://tax.iasd.dev.nextt.com.br/es/');
		
		$json_string = '{"teste-sede-filho":"http:\/\/iasd.dev\/teste_filho","teste-sede":"http:\/\/iasd.dev\/"}0';
		update_option('pa-multisite-menu', $json_string);
		update_option('pa-multisite-main-url', 'http://iasd.dev/es');

		ob_start();
		IasdNavEntreCampos::RenderMultiSiteMenu();
		$response = ob_get_contents();

		$assert1 = '<div class="dropdown iasd-dropdown-navigation visible-desktop">';
		$assert2 = '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">';
		$assert3 = '<li class="heading"><h1>Uniões</h1></li>';
		$assert4 = '<a href="" title="Teste sede (TESTE-SEDE)"><h2>Teste sede (TESTE-SEDE)</h2></a>';
		$assert5 = '<li class="heading"><h3>Teste sede (TESTE-SEDE)</h3></li>';
		$assert6 = '<li><a href="http://iasd.dev/" title="Portal TESTE-SEDE">Portal TESTE-SEDE</a></li>';
		$assert7 = '<li class="dsa-link"><a href="http://iasd.dev/es" title="Clique para ver o Portal Adventista" class="btn">Divisão Sul-Americana (DSA)</a></li>';
		$assert8 = '<li><a href="http://teste-sede-filho.iasd.dev/esteste-sede-filho" title="Teste sede filho (TESTE-SEDE-FILHO)">Teste sede filho (TESTE-SEDE-FILHO)</a></li>';


		$this->assertContains($assert1, $response);
		$this->assertContains($assert2, $response);
		$this->assertContains($assert3, $response);
		$this->assertContains($assert4, $response);
		$this->assertContains($assert5, $response);
		$this->assertContains($assert6, $response);
		$this->assertContains($assert7, $response);
		$this->assertContains($assert8, $response);

	}

	function test_render_real_json_pt() {	

		global $http;

		$args = array('domain' => 'apv.iasd.dev', 'path' => '', 'title' =>'Ministério das Mulheres', 'meta' => array('public'=>1, 'deleted'=>0, 'archived'=>0));	
		$return_id_newblog = $this->factory->blog->create_object($args);

		switch_to_blog( $return_id_newblog );

		$args = array('name' => 'ucb', 'taxonomy' => 'xtt-pa-sedes');
		$term_parent = $this->factory->term->create_object($args);

		$args = array('name' => 'apv', 'taxonomy' => 'xtt-pa-sedes', 'parent' => $term_parent);
		$this->factory->term->create_object($args);

		update_site_option('xtt_tax_server_url', 'http://tax.iasd.dev.nextt.com.br/pt/');
		
		$json_string = '{"aamar":"","aamo":"","ab":"","abac":"","abs":"","abc":"","ac":"http:\/\/ac.adventistas.org\/familia","aceam":"http:\/\/aceam.adventistas.org\/familia","acp":"http:\/\/acp.adventistas.org\/familia","acsr":"http:\/\/acsr.adventistas.org\/familia","acn":"","aes":"","ama":"","amt":"","amc":"","aml":"","ams":"","anc":"http:\/\/anc.adventistas.org\/familia","anpa":"","anp":"http:\/\/anp.adventistas.org\/familia","apac":"","apv":"http:\/\/apv.adventistas.org\/familia","apl":"http:\/\/apl.adventistas.org\/familia","apo":"http:\/\/apo.adventistas.org\/familia","apso":"http:\/\/apso.adventistas.org\/familia","aps":"","ap":"","ape":"","apec":"","aplac":"","arj":"","arf":"","ars":"","asur":"","aspa":"","ases":"","asm":"","asp":"http:\/\/asp.adventistas.org\/familia","asr":"http:\/\/asr.adventistas.org\/familia","dsa":"","misal":"","mbs":"","mbn":"","mto":"","mmn":"","mn":"","mosr":"http:\/\/mosr.adventistas.org\/familia","mopa":"","mopr":"http:\/\/mopr.adventistas.org\/familia","ms":"","msma":"","ucb":"http:\/\/ucb.adventistas.org\/familia","ucob":"","ulb":"","uneb":"","unob":"http:\/\/unob.adventistas.org\/familia","unb":"","useb":"","usb":"http:\/\/usb.adventistas.org\/familia"}0';
		
		update_option('pa-multisite-menu', $json_string);
		update_option('pa-multisite-main-url', 'http://iasd.dev/pt');

		ob_start();
		IasdNavEntreCampos::RenderMultiSiteMenu();
		$response = ob_get_contents();

		$assert1 = '<div class="dropdown iasd-dropdown-navigation visible-desktop">';
		$assert2 = '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">';
		$assert3 = '<li class="heading"><h1>Uniões</h1></li>';
		$assert4 = '<a href="" title="ucb (UCB)"><h2>ucb (UCB)</h2></a>';
		$assert5 = '<li class="heading"><h3>ucb (UCB)</h3></li>';
		$assert6 = '<li><a href="http://apv.iasd.dev/pt" title="apv (APV)">apv (APV)</a></li>';
		$assert7 = '<li class="dsa-link"><a href="http://iasd.dev/pt" title="Clique para ver o Portal Adventista" class="btn">Divisão Sul-Americana (DSA)</a></li>';

		$this->assertContains($assert1, $response);
		$this->assertContains($assert2, $response);
		$this->assertContains($assert3, $response);
		$this->assertContains($assert4, $response);
		$this->assertContains($assert5, $response);
		$this->assertContains($assert6, $response);
		$this->assertContains($assert7, $response);

	}











	// function test_RenderMultiSiteMenu(){
	   

	//     update_option('pa-multisite-main-url', 'http://iasd.dev.nextt.com.br/pt/');
	// 	$args = array('domain' => 'iasd.local', 'path' => '/mulher/', 'title' =>'Ministério das Mulheres' , 'meta' => array('public'=>1, 'deleted'=>0, 'archived'=>0) );
	// 	$return_id_newblog = $this->factory->blog->create_object($args);

	// 	$renderReponse = IasdNavEntreCampos::RenderMultiSiteMenu();

	// }




//Precisa entender Melhor
	// function test_if_fails_when_call_updateMultiSiteList_with_insuficient_params() {
	// 	// $this->markTestSkipped();
	// 	global $http;
	// 	update_site_option('xtt_tax_server_url', 'http://tax.iasd.dev.nextt.com.br/pt/');
	// 	$host = explode('.', WP_TESTS_DOMAIN);
	// 	$subdomain = $host[0];
	// 	// $this->markTestSkipped();
	// 	$wp_error = new WP_Error;
	// 	$http = $this->getMock('WP_Http');
	// 	$http->expects($this->atLeastOnce())
	// 		 ->method('request')
	// 		 ->with('http://tax.iasd.dev.nextt.com.br/pt/wp-admin/admin-ajax.php?action=updateMultiSiteList', array('method' => 'POST', 'body' => 'install='.$subdomain.'&sites[]=/jovens/&sites[]=portal_home', 'timeout'=>30))
	// 		 ->will($this->returnValue($wp_error));

	// 	$args = array('domain' => 'iasd.local', 'path' => '/jovens/', 'title' =>'Ministerio Jovem', 'meta' => array('public'=>1, 'deleted'=>0, 'archived'=>0));
	// 	$blog_id = $this->factory->blog->create_object($args);

	// 	$this->assertGreaterThan(0, has_action('admin_notices', array('IasdNavEntreCampos','showUpdateTaxSiteListErrorMessage')));
	// }




	// # test if save menu from tax
	// function test_if_save_menu_from_tax() {
	// 	// $this->markTestSkipped();
	// 	global $http;
	// 	update_site_option('xtt_tax_server_url', 'http://tax.iasd.dev.nextt.com.br/pt/');
	// 	// $this->markTestSkipped();
	// 	$host = explode('.', WP_TESTS_DOMAIN);
	// 	$subdomain = $host[0];
	// 	$returnValue = json_encode(array('return'=>'OK'));
	// 	$http = $this->getMock('WP_Http');
	// 	$http->expects($this->atLeastOnce())
	// 		 ->method('request')
	// 		 ->with('http://tax.iasd.dev.nextt.com.br/pt/wp-admin/admin-ajax.php?action=updateMultiSiteList'
	// 		 		,array('method' => 'POST', 'body' => 'install='.$subdomain.'&sites[]=/mulheres/&sites[]=portal_home', 'timeout'=>30))
	// 		 ->will($this->returnValue($returnValue));
	// 	$args = array('domain' => 'iasd.local', 'path' => '/mulheres/', 'title' =>'Ministério das Mulheres' , 'meta' => array('public'=>1, 'deleted'=>0, 'archived'=>0) );
	// 	$return_id_newblog = $this->factory->blog->create_object($args);

	// 	# mock http for create user
	// 	$http = $this->getMock('WP_Http');
	// 	$http->expects($this->atLeastOnce())
	// 		 ->method('request')
	// 		 ->with('http://tax.iasd.dev.nextt.com.br/pt/wp-admin/admin-ajax.php?action=updateMultiSiteList'
	// 		 		, array('method' => 'POST', 'body' => 'install='.$subdomain.'&sites[]=/jovens/&sites[]=/mulheres/&sites[]=portal_home', 'timeout'=>30))
	// 		 ->will($this->returnValue($returnValue));

	// 	$args = array('domain' => 'iasd.local', 'path' => '/jovens/', 'title' =>'Ministerio Jovem', 'meta' => array('public'=>1, 'deleted'=>0, 'archived'=>0));
	// 	$return_id_newblog2 = $this->factory->blog->create_object($args);

	// 	switch_to_blog( $return_id_newblog );
	// 	// update_option('xtt_tax_server_url', 'http://tax.iasd.dev.nextt.com.br/pt/');
	// 	update_site_option('xtt_tax_server_url', 'http://tax.iasd.dev.nextt.com.br/pt/');

	// 	$returnValue = array('body' => json_encode(array('asc.local'=>'jovens','usb.local'=>'jovens')));

	// 	// #mock http for get multisite list
	// 	$http = $this->getMock('WP_Http');
	// 	$http->expects($this->atLeastOnce())
	// 		 ->method('request')
	// 		 ->with('http://tax.iasd.dev.nextt.com.br/pt/wp-admin/admin-ajax.php?action=getMultiSiteList', array('method' => 'POST', 'body' => 'slug=mulheres', 'timeout'=>30))
	// 		 ->will($this->returnValue($returnValue));

	// 	# do action
	// 	$return = do_action('wp_ajax_nopriv_TriggerMultiSiteUpdate');

	// 	# check if wp_option is saved
	// 	$actual_option = get_blog_option( $return_id_newblog, 'pa-multisite-menu' );

	// 	$this->assertEquals($returnValue['body'], $actual_option , 'wp_ajax_triggerMultiSiteUpdate gera o retorno esperado ou não');

	// 	$blog_id = get_current_blog_id();

	// 	switch_to_blog( 1 );

	// }


}







