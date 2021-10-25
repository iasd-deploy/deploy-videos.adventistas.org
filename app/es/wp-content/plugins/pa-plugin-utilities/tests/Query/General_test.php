<?php

require_once '_test.php';

class IASD_Query_General_test extends IASD_Query__test {
	function setUp() {
		parent::setUp();
	}

	function test_classExists() {
		$this->assertTrue(class_exists('IASD_Query'));
	}
	function test_constantExists() {
		$this->assertNotEmpty(constant('IASD_Query::AJAX_ACTION'));
		$this->assertNotEmpty(constant('IASD_Query::AJAX_COMMAND'));
		$this->assertNotEmpty(constant('IASD_Query::AJAX_NO_RESULTS'));
		$this->assertNotEmpty(constant('IASD_Query::DATA'));
		$this->assertNotEmpty(constant('IASD_Query::TTL'));
		$this->assertNotEmpty(constant('IASD_Query::CACHE_TIME'));
	}

	function test_defaultHooksEnabled() {
		$this->assertEquals(5, has_action('the_post', 									array('IASD_Query','CheckDetours')));
		$this->assertEquals(5, has_action('pre_get_posts', 								array('IASD_Query','CheckDetours')));
		$this->assertEquals(5, has_action('wp_ajax_'.IASD_Query::AJAX_ACTION, 			array('IASD_Query','Remote')));
		$this->assertEquals(5, has_action('wp_ajax_nopriv_'.IASD_Query::AJAX_ACTION,	array('IASD_Query','Remote')));

		IASD_Query::Unhooks();
		$this->assertFalse(has_action('the_post', 									array('IASD_Query','CheckDetours')));
		$this->assertFalse(has_action('pre_get_posts', 								array('IASD_Query','CheckDetours')));
		$this->assertFalse(has_action('wp_ajax_'.IASD_Query::AJAX_ACTION, 			array('IASD_Query','Remote')));
		$this->assertFalse(has_action('wp_ajax_nopriv_'.IASD_Query::AJAX_ACTION,	array('IASD_Query','Remote')));


		IASD_Query::Hooks();
		$this->assertEquals(5, has_action('the_post', 									array('IASD_Query','CheckDetours')));
		$this->assertEquals(5, has_action('pre_get_posts', 								array('IASD_Query','CheckDetours')));
		$this->assertEquals(5, has_action('wp_ajax_'.IASD_Query::AJAX_ACTION, 			array('IASD_Query','Remote')));
		$this->assertEquals(5, has_action('wp_ajax_nopriv_'.IASD_Query::AJAX_ACTION,	array('IASD_Query','Remote')));
	}

	function test_detoursBasic() {
		$this->assertFalse(has_action('post_link',				array('IASD_Query','DetourForPermalink')));
		$this->assertFalse(has_action('post_type_link', 		array('IASD_Query','DetourForPermalink')));
		$this->assertFalse(has_action('get_post_metadata',		array('IASD_Query','DetourForPostmeta')));
		$this->assertFalse(has_action('post_thumbnail_html', 	array('IASD_Query','DetourForThumbnail')));
		$this->assertFalse(has_action('get_avatar', 	        array('IASD_Query','DetourForAvatar')));

		IASD_Query::EnableDetours();
		$this->assertEquals( 5, has_action('post_link',				array('IASD_Query','DetourForPermalink')));
		$this->assertEquals( 5, has_action('post_type_link', 		array('IASD_Query','DetourForPermalink')));
		$this->assertEquals( 5, has_action('get_post_metadata',		array('IASD_Query','DetourForPostmeta')));
		$this->assertEquals( 5, has_action('post_thumbnail_html', 	array('IASD_Query','DetourForThumbnail')));
		$this->assertEquals(99, has_action('get_avatar', 	        array('IASD_Query','DetourForAvatar')));

		IASD_Query::DisableDetours();
		$this->assertFalse(has_action('post_link',				array('IASD_Query','DetourForPermalink')));
		$this->assertFalse(has_action('post_type_link', 		array('IASD_Query','DetourForPermalink')));
		$this->assertFalse(has_action('get_post_metadata',		array('IASD_Query','DetourForPostmeta')));
		$this->assertFalse(has_action('post_thumbnail_html', 	array('IASD_Query','DetourForThumbnail')));
		$this->assertFalse(has_action('get_avatar', 	        array('IASD_Query','DetourForAvatar')));
	}

	function test_checkDetours() {
		IASD_Query::CheckDetours(new WP_Query());
		$this->assertFalse(has_action('post_link',				array('IASD_Query','DetourForPermalink')));
		$this->assertFalse(has_action('post_type_link', 		array('IASD_Query','DetourForPermalink')));
		$this->assertFalse(has_action('get_post_metadata',		array('IASD_Query','DetourForPostmeta')));
		$this->assertFalse(has_action('post_thumbnail_html', 	array('IASD_Query','DetourForThumbnail')));

		//Não Remoto
		IASD_Query::CheckDetours(new IASD_Query());
		$this->assertFalse(has_action('post_link',				array('IASD_Query','DetourForPermalink')));
		$this->assertFalse(has_action('post_type_link', 		array('IASD_Query','DetourForPermalink')));
		$this->assertFalse(has_action('get_post_metadata',		array('IASD_Query','DetourForPostmeta')));
		$this->assertFalse(has_action('post_thumbnail_html', 	array('IASD_Query','DetourForThumbnail')));

		//Não Remoto
		$iasd_query = new IASD_Query();
		$iasd_query->setSource('anysource'); //Melhorar!
		IASD_Query::CheckDetours($iasd_query);
		$this->assertEquals(5, has_action('post_link',				array('IASD_Query','DetourForPermalink')));
		$this->assertEquals(5, has_action('post_type_link', 		array('IASD_Query','DetourForPermalink')));
		$this->assertEquals(5, has_action('get_post_metadata',		array('IASD_Query','DetourForPostmeta')));
		$this->assertEquals(5, has_action('post_thumbnail_html', 	array('IASD_Query','DetourForThumbnail')));

		wp_reset_query();
		$postRaw = new stdClass();
		$postRaw->post_title = "Title";
		$post = new WP_Post($postRaw);
		IASD_Query::CheckDetours($post);
		$this->assertFalse(has_action('post_link',				array('IASD_Query','DetourForPermalink')));
		$this->assertFalse(has_action('post_type_link', 		array('IASD_Query','DetourForPermalink')));
		$this->assertFalse(has_action('get_post_metadata',		array('IASD_Query','DetourForPostmeta')));
		$this->assertFalse(has_action('post_thumbnail_html', 	array('IASD_Query','DetourForThumbnail')));
	}

	function test_iasdQueryLifeCicle() {
		new WP_Query(array('posts_per_page' => 1));

		$args = array();
		$args['post_type'] = 'colunas';
		$args['posts_per_page'] = 1;
		$args['non_cacheable'] = true;

		$argsForRequest = $args;
		$argsForRequest['source'] = 'noticias.adventistas.org/pt'; //Source parameter is cut off
		$requestUrl = 'http://' . $argsForRequest['source'] . '/' . IASD_Query::AJAX_COMMAND . IASD_Query::AJAX_ACTION;

		global $http;
		$http = $this->getMock('WP_Http');


		$encodedArgs = IASD_Query::Encode($args);
		$postParams = IASD_Query::BuildRequestParams($encodedArgs);

		$returnValue = array('body' => IASD_Query::Encode(array('request' => '', 'posts' => array())), 
							'response' => array('code' => 200));

		$http->expects($this->once())
			 ->method('post') //Metodo a ser chamado do objeto!!!
			 ->with($requestUrl, $postParams)
			 ->will($this->returnValue($returnValue));

		
		$queryObject = new IASD_Query($argsForRequest);

		global $wp_query;
		$this->assertEquals($wp_query, $queryObject);

		//has_action vai retornar a prioridade
		$this->assertEquals(5, has_action('post_link',				array('IASD_Query','DetourForPermalink')));
		$this->assertEquals(5, has_action('post_type_link', 		array('IASD_Query','DetourForPermalink')));
		$this->assertEquals(5, has_action('get_post_metadata',		array('IASD_Query','DetourForPostmeta')));
		$this->assertEquals(5, has_action('post_thumbnail_html', 	array('IASD_Query','DetourForThumbnail')));

		wp_reset_query();
		global $wp_query;
		$this->assertNotEquals($wp_query, $queryObject);

//		$this->assertFalse(has_action('post_link',				array('IASD_Query','PermalinkDetour')));
//		$this->assertFalse(has_action('post_type_link', 		array('IASD_Query','PermalinkDetour')));
//		$this->assertFalse(has_action('get_post_metadata',		array('IASD_Query','DetourForPostmeta')));
//		$this->assertFalse(has_action('post_thumbnail_html', 	array('IASD_Query','DetourForThumbnail')));

		new WP_Query(array('posts_per_page' => 1));
		$this->assertFalse(has_action('post_link',				array('IASD_Query','DetourForPermalink')));
		$this->assertFalse(has_action('post_type_link', 		array('IASD_Query','DetourForPermalink')));
		$this->assertFalse(has_action('get_post_metadata',		array('IASD_Query','DetourForPostmeta')));
		$this->assertFalse(has_action('post_thumbnail_html', 	array('IASD_Query','DetourForThumbnail')));

		$this->markTestIncomplete('Wordpress não tem actions/hooks no wp_reset_query se a query tiver 0 posts');
	}

	function test_iasdQueryNoResults() {
		$args = array();
		$args['post_type'] = 'colunas';
		$args['posts_per_page'] = 1;
		$args['non_cacheable'] = true;

		$argsForRequest = $args;
		$argsForRequest['source'] = 'noticias.adventistas.org/pt'; //Source parameter is cut off
		$requestUrl = 'http://' . $argsForRequest['source'] . '/' . IASD_Query::AJAX_COMMAND . IASD_Query::AJAX_ACTION;

		global $http;
		$http = $this->getMock('WP_Http');

		$encodedArgs = IASD_Query::Encode($args);
		$postParams = IASD_Query::BuildRequestParams($encodedArgs);

		$returnBase = array('request' => '', 'posts' => array());
		$returnValue = array('body' => IASD_Query::Encode($returnBase), 
							'response' => array('code' => 200));

		$http->expects($this->once())
			 ->method('post') //Metodo a ser chamado do objeto!!!
			 ->with($requestUrl, $postParams)
			 ->will($this->returnValue($returnValue));

		$queryObject = new IASD_Query($argsForRequest, $requestUrl);

		$this->assertFalse($queryObject->have_posts());
		$this->assertEquals(0, $queryObject->post_count);
	}

	function test_iasdQueryResults() {
		$args = array();
		$args['post_type'] = 'colunas';
		$args['posts_per_page'] = 1;
		$args['non_cacheable'] = true;

		$argsForRequest = $args;
		$argsForRequest['source'] = 'noticias.adventistas.org/pt'; //Source parameter is cut off
		$requestUrl = 'http://' . $argsForRequest['source'] . '/' . IASD_Query::AJAX_COMMAND . IASD_Query::AJAX_ACTION;

		global $http;
		$http = $this->getMock('WP_Http');

		$encodedArgs = IASD_Query::Encode($args);
		$postParams = IASD_Query::BuildRequestParams($encodedArgs);

		$fakePost = new WP_Post($this->createNonSavedFakePost());

		$returnBase = array('request' => '', 'posts' => array($fakePost));
		$returnValue = array('body' => IASD_Query::Encode($returnBase), 
							'response' => array('code' => 200));

		$http->expects($this->once())
			 ->method('post') //Metodo a ser chamado do objeto!!!
			 ->with($requestUrl, $postParams)
			 ->will($this->returnValue($returnValue));

		$queryObject = new IASD_Query($argsForRequest, $requestUrl);

		$this->assertTrue($queryObject->have_posts());
		$this->assertEquals(1, $queryObject->post_count);;
		$this->assertTrue($queryObject->have_posts());

		while($queryObject->have_posts()) {
			$queryObject->the_post();
			global $post;
			$this->assertEquals($post, $fakePost);
			$this->assertTrue($post->is_remote);

			$this->assertEquals($post->author['avatar'], get_avatar($post->post_author));
		}
	}

	function test_iasdQueryQueryMethod() {
		$args = array();
		$args['post_type'] = 'colunas';
		$args['posts_per_page'] = 1;

		$queryObject = new IASD_Query($args);
		$this->assertFalse($queryObject->isRemote());

		$arg_source = 'noticias.adventistas.org/pt';
		$queryObject->setSource($arg_source);

		$this->assertTrue($queryObject->isRemote());
		$this->assertContains($arg_source, $queryObject->getSource());
		$this->assertNotEquals($arg_source, $queryObject->isRemote());
		$this->assertEquals('http://'.$arg_source .'/', $queryObject->getSource());

		$queryObject->unsetSource();
		$this->assertFalse($queryObject->isRemote());
		$this->assertFalse($queryObject->getSource());
		$this->assertFalse($queryObject->isRemote());
		$this->assertFalse(IASD_Query::SourceRequestUrl($queryObject->getSource()));

		$queryObject->setSource($arg_source);

		$requestUrl = 'http://noticias.adventistas.org/pt/' . IASD_Query::AJAX_COMMAND . IASD_Query::AJAX_ACTION;
		$this->assertEquals($requestUrl, IASD_Query::SourceRequestUrl($queryObject->getSource()));

		global $http;
		$http = $this->getMock('WP_Http');

		$encodedArgs = IASD_Query::Encode($args);
		$postParams = IASD_Query::BuildRequestParams($encodedArgs);

		$baseFakePost = $this->createNonSavedFakePost();
		$fakePost = new WP_Post($baseFakePost);

		$returnBase = array('request' => '', 'posts' => array($fakePost));
		$returnValue = array('body' => IASD_Query::Encode($returnBase), 
							'response' => array('code' => 200));

		$http->expects($this->once())
			 ->method('post') //Metodo a ser chamado do objeto!!!
			 ->with($requestUrl, $postParams)
			 ->will($this->returnValue($returnValue));

		//Non
		$posts = $queryObject->query($args);
		global $wp_query;

		$this->assertEquals($posts, $wp_query->posts);
		$this->assertTrue($queryObject->have_posts());
		$this->assertGreaterThan(0, $queryObject->post_count);

		$queryObject->the_post();
		global $post;
		$this->assertEquals($post, $fakePost);
		$postPermalink = get_permalink();
		$this->assertEquals($baseFakePost->guid, $postPermalink);

		$post_thumbnail = get_the_post_thumbnail();
		$this->assertContains($baseFakePost->thumbs['post-thumbnail'], $post_thumbnail);

		$http = $this->getMock('WP_Http');
		$http->expects($this->never())->method('post');

		//cached!
		$posts = new IASD_Query($args);

		//No Results -- Error Code
		global $noresults_called;
		$noresults_called = 0;

		add_action(IASD_Query::AJAX_NO_RESULTS, function() { global $noresults_called; $noresults_called++; });
		$this->assertEquals(0, $noresults_called);

		$http = $this->getMock('WP_Http');
		$returnValue = array('body' => IASD_Query::Encode(array('request' => '', 'posts' => array())), 'response' => array('code' => 201));

		$args['non_cacheable'] = true;
		$encodedArgs = IASD_Query::Encode($args);
		$postParams = IASD_Query::BuildRequestParams($encodedArgs);

		$http->expects($this->once())
			 ->method('post') //Metodo a ser chamado do objeto!!!
			 ->with($requestUrl, $postParams)
			 ->will($this->returnValue($returnValue));

		$posts = $queryObject->query($args);
		$this->assertEquals(1, $noresults_called);


		$http = $this->getMock('WP_Http');
		$returnValue = array('body' => '0', 'response' => array('code' => 200));

		$args['non_cacheable'] = true;
		$encodedArgs = IASD_Query::Encode($args);
		$postParams = IASD_Query::BuildRequestParams($encodedArgs);

		$http->expects($this->once())
			 ->method('post') //Metodo a ser chamado do objeto!!!
			 ->with($requestUrl, $postParams)
			 ->will($this->returnValue($returnValue));

		$posts = $queryObject->query($args);
		$this->assertEquals(2, $noresults_called);
	}


	function test_Remote() {
		ob_start();
		IASD_Query::Remote();
		$encodedReturn = ob_get_contents();
		ob_end_clean();
		$decodedReturn = IASD_Query::Decode($encodedReturn);

		$this->assertEquals("", $decodedReturn);

		$argsLocal = array();
		$argsLocal['posts_per_page'] = 1;

		$fakePost_0 = array(
			'post_status' => 'publish',
			'post_title' => 'Post title 0',
			'post_content' => 'Post content 0',
			'post_excerpt' => 'Post excerpt 0',
			'post_type' => 'post'
		);
		$post_id_0 = wp_insert_post($fakePost_0);

		$queryLocal = new WP_Query($argsLocal);

		$argsRemote = $argsLocal;
		$argsRemote['source'] = 'noticias.adventistas.org/pt';
		$argsRemote['non_cacheable'] = true;

		$_REQUEST['query_args'] = $_POST['query_args'] = IASD_Query::Encode($argsRemote);

		ob_start();
		IASD_Query::Remote();
		$encodedReturn = ob_get_contents();
		ob_end_clean();

		$decodedReturn = IASD_Query::Decode($encodedReturn);

		$this->assertEquals($queryLocal->request, $decodedReturn->request);
		$this->assertEquals(count($queryLocal->posts), count($decodedReturn->posts));

		$this->assertObjectHasAttribute('is_remote', $decodedReturn->posts[0]);
		$this->assertObjectHasAttribute('thumbs', $decodedReturn->posts[0]);

		//Remove "is_remote" and "thumbs" to make it compatible
		$this->assertTrue(isset($decodedReturn->posts[0]->is_remote));
		unset($decodedReturn->posts[0]->is_remote);
		$this->assertTrue(isset($decodedReturn->posts[0]->thumbs));
		unset($decodedReturn->posts[0]->thumbs);
		$this->assertTrue(isset($decodedReturn->posts[0]->thumb_name));
		unset($decodedReturn->posts[0]->thumb_name);
		$this->assertTrue(isset($decodedReturn->posts[0]->taxonomies));
		unset($decodedReturn->posts[0]->taxonomies);
		$this->assertTrue(isset($decodedReturn->posts[0]->author));
		unset($decodedReturn->posts[0]->author);
		$this->assertTrue(isset($decodedReturn->posts[0]->meta));
		unset($decodedReturn->posts[0]->meta);

		$this->assertEquals($queryLocal->posts, $decodedReturn->posts);

		$argsLocal = array();
		$argsLocal['posts_per_page'] = 2;
		$argsLocal['post_type'] = 'any';

		$fakePost_1 = array(
			'post_status' => 'publish',
			'post_title' => 'Post title 1',
			'post_content' => 'Post content 1',
			'post_excerpt' => 'Post excerpt 1',
			'post_type' => 'post'
		);
		$post_id_1 = wp_insert_post($fakePost_1);
		$fakePost_2 = array(
			'post_status' => 'publish',
			'post_title' => 'Post title 2',
			'post_content' => 'Post content 2',
			'post_excerpt' => 'Post excerpt 2',
			'post_type' => 'post'
		);
		$post_id_2 = wp_insert_post($fakePost_2);


		$filename = DIRNAME(__FILE__).'/images/1500x500.gif';
		$wp_filetype = wp_check_filetype(basename($filename), null );
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
			'post_content' => '',
			'post_status' => 'inherit'
		);
		$attach_id = wp_insert_attachment( $attachment, $filename, $post_id_2 );
		$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
		wp_update_attachment_metadata( $attach_id, $attach_data );
		add_post_meta($post_id_2, '_thumbnail_id', $attach_id, true);

		$argsLocal['post__in'] = array($post_id_1, $post_id_2);
		$queryLocal = new IASD_Query($argsLocal);

		$argsRemote = $argsLocal;
		$argsRemote['source'] = 'noticias.adventistas.org/pt';
		$argsRemote['non_cacheable'] = true;

		$_REQUEST['query_args'] = $_POST['query_args'] = IASD_Query::Encode($argsRemote);

		ob_start();
		IASD_Query::Remote();
		$encodedReturn = ob_get_contents();
		ob_end_clean();

		$decodedReturn = IASD_Query::Decode($encodedReturn);
		foreach($decodedReturn->posts as $k => $v) {
			//Removendo o thumbs para fins de testes;
			$decodedReturn->posts[$k] = new WP_Post($v);
		}

		$this->assertObjectHasAttribute('is_remote', $decodedReturn->posts[0]);
		$this->assertTrue($decodedReturn->posts[0]->is_remote);

		$this->assertEquals(count($decodedReturn->posts[0]->thumbs), count($decodedReturn->posts[1]->thumbs));

		$this->assertEquals($queryLocal->request, $decodedReturn->request);

		unset($decodedReturn->posts[0]->is_remote);
		unset($decodedReturn->posts[0]->thumbs);
		unset($decodedReturn->posts[0]->thumb_name);
		unset($decodedReturn->posts[0]->taxonomies);
		unset($decodedReturn->posts[0]->author);
		unset($decodedReturn->posts[0]->meta);
		$this->assertEquals($queryLocal->posts[0], $decodedReturn->posts[0]);
	}

	function test_willGetFromCache() {
		$this->addUsersAndPosts();

		//ASSET: Result to be used by the ajax request
		$asset_remote_results = array();
		$asset_query = array('posts_per_page' => 4);
		$query = new IASD_Query($asset_query);
		$asset_remote_results = array('request' => $query->request, 'posts' => $query->posts);
		wp_reset_query();
		$asset_encodedKey = IASD_Query::EncodeKey($asset_query);

		//Append 0 to simulate Wordpress' behaviour
		$asset_remote_body = IASD_Query::Encode($asset_remote_results) . '0';
		$asset_response = array('response' => array('code' => 200), 'body' => $asset_remote_body);

		//Create a mock to the request
		global $http;
		$http = $this->getMock('WP_Http');
		$http->expects($this->once())
				->method('post')
				->will($this->returnValue($asset_response));

		//The Test
		$base_query_args_obj = array();
		$base_query_args_obj['posts_per_page'] = 4;

		$option_data = get_option(IASD_Query::DATA . $asset_encodedKey, 'none');
		$this->assertEquals('none', $option_data);
		$timeToLive = get_option(IASD_Query::TTL . $asset_encodedKey, 0);
		$this->assertEquals(0, $timeToLive);

		$firstQuery = IASD_Query::RequestRemote($asset_query, 'http://127.0.0.1/');

		$option_data = get_option(IASD_Query::DATA . $asset_encodedKey, 'none');
		$this->assertNotEquals('none', $option_data);

		$timeToLive = get_option(IASD_Query::TTL . $asset_encodedKey, 0);
		$this->assertNotEquals(0, $timeToLive);
		$this->assertTrue($timeToLive > time());

		$isDead = ($timeToLive <= time()) ? true : false;
		$this->assertFalse($isDead);

		$secondQuery = IASD_Query::RequestRemote($asset_query, 'http://127.0.0.1/');
		$this->assertEquals($firstQuery, $secondQuery);

		global $http;
		$http = null;
	}

	function test_willIgnoreFromCache() {
		$this->addUsersAndPosts();

		//ASSET: Result to be used by the ajax request
		$asset_remote_results = array();
		$asset_query = array('posts_per_page' => 4);
		$query = new IASD_Query($asset_query);
		$asset_remote_results = array('request' => $query->request, 'posts' => $query->posts);
		wp_reset_query();
		$asset_encodedKey = IASD_Query::EncodeKey($asset_query);

		//Append 0 to simulate Wordpress' behaviour
		$asset_remote_body = IASD_Query::Encode($asset_remote_results) . '0';
		$asset_response = array('response' => array('code' => 200), 'body' => $asset_remote_body);

		//Create a mock to the request
		global $http;
		$http = $this->getMock('WP_Http');
		$http->expects($this->exactly(2))
				->method('post')
				->will($this->returnValue($asset_response));

		//The Test
		$base_query_args_obj = array();
		$base_query_args_obj['posts_per_page'] = 4;

		$option_data = get_option(IASD_Query::DATA . $asset_encodedKey, 'none');
		$this->assertEquals('none', $option_data);
		$timeToLive = get_option(IASD_Query::TTL . $asset_encodedKey, 0);
		$this->assertEquals(0, $timeToLive);

		$firstQuery = IASD_Query::RequestRemote($asset_query, 'http://127.0.0.1/');

		$option_data = get_option(IASD_Query::DATA . $asset_encodedKey, 'none');
		$this->assertNotEquals('none', $option_data);

		$timeToLive = get_option(IASD_Query::TTL . $asset_encodedKey, 0);
		$this->assertNotEquals(0, $timeToLive);
		$this->assertTrue($timeToLive > time());

		$isDead = ($timeToLive <= time()) ? true : false;
		$this->assertFalse($isDead);

		$secondQuery = IASD_Query::RequestRemote($asset_query, 'http://127.0.0.1/');
		$this->assertEquals($firstQuery, $secondQuery);

		$asset_query['non_cacheable'] = true;
		$secondQuery = IASD_Query::RequestRemote($asset_query, 'http://127.0.0.1/');
		$this->assertEquals($firstQuery, $secondQuery);

		global $http;
		$http = null;
	}
}








