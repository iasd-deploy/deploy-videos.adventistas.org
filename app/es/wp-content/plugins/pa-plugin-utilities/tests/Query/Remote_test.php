<?php

require_once '_test.php';

class IASD_Query_Remote_test extends IASD_Query__test {

	function test_methodRemote_Grouping_NotForced() {
		$this->addUsersAndPosts();

		$decodedQuery = array('grouping_taxonomy' => self::TAX_FOR_TEST);
		$decodedQuery['tax_query'] = array();
		$decodedQuery['tax_query'][0] = array();
		$decodedQuery['tax_query'][0]['taxonomy'] = self::TAX_FOR_TEST;
		$decodedQuery['tax_query'][0]['field'] = 'slug';
		$decodedQuery['tax_query'][0]['terms'] = array('author-' . $this->authors[0]->ID, 'author-' . $this->authors[1]->ID);
		$decodedQuery['posts_per_page'] = 4;

		$encodedQuery = IASD_Query::Encode($decodedQuery);
		$_REQUEST['query_args'] = $_POST['query_args'] = $encodedQuery;

		ob_start();
		IASD_Query::Remote();
		$remote_encoded_output = ob_get_contents();
		ob_end_clean();
		$remote_output = IASD_Query::Decode($remote_encoded_output);
		$remote_output = (array) $remote_output;

		$this->assertEquals(3, count($remote_output), 'Selected + Default');
		
		$this->assertTrue(isset($remote_output['author-' . $this->authors[0]->ID]));
		$author1 = (array) $remote_output['author-' . $this->authors[0]->ID];
		$this->assertTrue(isset($author1['request']));
		$this->assertTrue(isset($author1['posts']));
		$this->assertEquals(2, count($author1));
		$this->assertEquals($decodedQuery['posts_per_page'], count($author1['posts']));

		$this->assertTrue(isset($remote_output['author-' . $this->authors[1]->ID]));
		$author2 = (array) $remote_output['author-' . $this->authors[1]->ID];
		$this->assertTrue(isset($author2['request']));
		$this->assertTrue(isset($author2['posts']));
		$this->assertEquals(2, count($author2));
		$this->assertEquals($decodedQuery['posts_per_page'], count($author2['posts']));

		$posts = $author2['posts'];
		foreach($posts as $post) {
			$this->assertTrue(isset($post->is_remote));
			$this->assertTrue(isset($post->thumbs));
			$this->assertTrue(isset($post->filter));
		}
	}


	function test_methodRemote_Grouping_Forced() {
		$this->addUsersAndPosts();

		$decodedQuery = array('grouping_taxonomy' => self::TAX_FOR_TEST);
		$decodedQuery['grouping_forced'] = true;
		$decodedQuery['tax_query'] = array();
		$decodedQuery['tax_query'][0] = array();
		$decodedQuery['tax_query'][0]['taxonomy'] = self::TAX_FOR_TEST;
		$decodedQuery['tax_query'][0]['field'] = 'slug';
		$decodedQuery['tax_query'][0]['terms'] = array('author-' . $this->authors[0]->ID, 'author-' . $this->authors[1]->ID);
		$decodedQuery['posts_per_page'] = 4;

		$encodedQuery = IASD_Query::Encode($decodedQuery);
		$_REQUEST['query_args'] = $_POST['query_args'] = $encodedQuery;

		ob_start();
		IASD_Query::Remote();
		$remote_encoded_output = ob_get_contents();
		ob_end_clean();
		$remote_output = IASD_Query::Decode($remote_encoded_output);
		$remote_output = (array) $remote_output;

		$this->assertEquals(2, count($remote_output));
		
		$this->assertTrue(isset($remote_output['author-' . $this->authors[0]->ID]));
		$author1 = (array) $remote_output['author-' . $this->authors[0]->ID];
		$this->assertTrue(isset($author1['request']));
		$this->assertTrue(isset($author1['posts']));
		$this->assertEquals(2, count($author1));
		$this->assertEquals($decodedQuery['posts_per_page'], count($author1['posts']));

		$this->assertTrue(isset($remote_output['author-' . $this->authors[1]->ID]));
		$author2 = (array) $remote_output['author-' . $this->authors[1]->ID];
		$this->assertTrue(isset($author2['request']));
		$this->assertTrue(isset($author2['posts']));
		$this->assertEquals(2, count($author2));
		$this->assertEquals($decodedQuery['posts_per_page'], count($author2['posts']));

		$posts = $author2['posts'];
		foreach($posts as $post) {
			$this->assertTrue(isset($post->is_remote));
			$this->assertTrue(isset($post->thumbs));
			$this->assertTrue(isset($post->filter));
		}
	}

	function test_methodRemote_NotGrouping() {
		$this->addUsersAndPosts();

		$decodedQuery = array();
		$decodedQuery['author'] = $this->authors[0]->ID;
		$decodedQuery['posts_per_page'] = 3;

		$encodedQuery = IASD_Query::Encode($decodedQuery);
		$_REQUEST['query_args'] = $_POST['query_args'] = $encodedQuery;

		ob_start();
		IASD_Query::Remote();
		$remote_encoded_output = ob_get_contents();
		ob_end_clean();
		$remote_output = IASD_Query::Decode($remote_encoded_output);
		$remote_output = (array) $remote_output;

		$this->assertTrue(isset($remote_output['request']));
		$this->assertTrue(isset($remote_output['posts']));
		$this->assertEquals(2, count($remote_output));
		$this->assertEquals($decodedQuery['posts_per_page'], count($remote_output['posts']));

		$posts = $remote_output['posts'];
		foreach($posts as $post) {
			$this->assertTrue(isset($post->is_remote));
			$this->assertTrue(isset($post->thumbs));
			$this->assertTrue(isset($post->filter));
		}

		$_POST = array();
	}
}








