<?php

require_once '_test.php';

class IASD_Query_GroupingQueries_test extends IASD_Query__test {
	function test_methodGroupingQueries_NotRemote() {
		$this->addUsersAndPosts();

		//No Grouping Slug with OBJECT
		$base_query_args_obj = new stdClass();
		$base_query_args_obj->grouping_taxonomy = self::TAX_FOR_TEST;
		$base_query_args_obj->tax_query = array();
		$base_query_args_obj->tax_query[0] = new stdClass();
		$base_query_args_obj->tax_query[0]->taxonomy = self::TAX_FOR_TEST;
		$base_query_args_obj->tax_query[0]->field = 'slug';
		$base_query_args_obj->tax_query[0]->terms = array('author-' . $this->authors[0]->ID, 'author-' . $this->authors[1]->ID);
		$base_query_args_obj->tax_query[1] = new stdClass();
		$base_query_args_obj->tax_query[1]->taxonomy = 'category';
		$base_query_args_obj->tax_query[1]->field = 'slug';
		$base_query_args_obj->tax_query[1]->terms = array('cat-authors');
		$base_query_args_obj->posts_per_page = 4;
		$queries_obj = IASD_Query::GroupingQueries($base_query_args_obj);


		//No Grouping Slug with ARRAY
		$base_query_args_array = (array) $base_query_args_obj;
		$base_query_args_array['tax_query'] = (array) $base_query_args_array['tax_query'];
		$base_query_args_array['tax_query'][0] = (array) $base_query_args_array['tax_query'][0];
		$base_query_args_array['tax_query'][0]['terms'] = (array) $base_query_args_array['tax_query'][0]['terms'];
		$queries_array = IASD_Query::GroupingQueries($base_query_args_array);

		$this->assertEquals($queries_obj, $queries_array);
	}

	function test_methodGroupingQuery_NotRemote() {
		$this->addUsersAndPosts();

		//No Grouping Slug with OBJECT
		$base_query_args_obj = new stdClass();
		$base_query_args_obj->grouping_taxonomy = self::TAX_FOR_TEST;
		$base_query_args_obj->tax_query = array();
		$base_query_args_obj->tax_query[0] = new stdClass();
		$base_query_args_obj->tax_query[0]->taxonomy = self::TAX_FOR_TEST;
		$base_query_args_obj->tax_query[0]->field = 'slug';
		$base_query_args_obj->tax_query[0]->terms = array('author-' . $this->authors[0]->ID, 'author-' . $this->authors[1]->ID);
		$base_query_args_obj->posts_per_page = 4;

		$queries_obj_no_slug = IASD_Query::GroupingQuery($base_query_args_obj);

		$base_query_args_obj->grouping_slug = 'author-' . $this->authors[1]->ID;
		$queries_obj_with_slug = IASD_Query::GroupingQuery($base_query_args_obj);
	}

	function test_methodGroupingQuery_Remote() {
		$this->addUsersAndPosts();

		//ASSET: Result to be used by the ajax request
		$asset_remote_results = array();
		$asset_query = array('tax_query' => array(), 'posts_per_page' => 4);
		$asset_query['tax_query'][0] = array();
		$asset_query['tax_query'][0]['taxonomy'] = self::TAX_FOR_TEST;
		$asset_query['tax_query'][0]['field'] = 'slug';

		$asset_query['tax_query'][0]['terms'] = array('author-' . $this->authors[0]->ID);
		$query = new IASD_Query($asset_query);
		$asset_remote_results['author-' . $this->authors[0]->ID] = array('request' => $query->request, 'posts' => $query->posts);
		wp_reset_query();


		$asset_query['tax_query'][0]['terms'] = array('author-' . $this->authors[1]->ID);
		$query = new IASD_Query($asset_query);
		$asset_remote_results['author-' . $this->authors[1]->ID] = array('request' => $query->request, 'posts' => $query->posts);
		wp_reset_query();

		//Append 0 to simulate Wordpress' behaviour
		$asset_remote_body = IASD_Query::Encode($asset_remote_results) . '0';
		$asset_response = array('response' => array('code' => 200), 'body' => $asset_remote_body);

		//Create a mock to the request
		global $http;
		$http = $this->getMock('WP_Http');
		$http->expects($this->atLeastOnce())
				->method('post')
				->will($this->returnValue(array('body' => $asset_remote_body, 'response' => array('code' => 200))));

		//The Test
		$base_query_args_obj = new stdClass();
		$base_query_args_obj->grouping_taxonomy = self::TAX_FOR_TEST;
		$base_query_args_obj->tax_query = array();
		$base_query_args_obj->tax_query[0] = new stdClass();
		$base_query_args_obj->tax_query[0]->taxonomy = self::TAX_FOR_TEST;
		$base_query_args_obj->tax_query[0]->field = 'slug';
		$base_query_args_obj->tax_query[0]->terms = array('author-' . $this->authors[0]->ID, 'author-' . $this->authors[1]->ID);
		$base_query_args_obj->grouping_slug = 'author-' . $this->authors[1]->ID;
		$base_query_args_obj->posts_per_page = 4;
		$base_query_args_obj->non_cacheable = true;
		$base_query_args_obj->source = 'http://127.0.0.1/';

		$all_queries = IASD_Query::GroupingQueries($base_query_args_obj);
		$specific_query = IASD_Query::GroupingQuery($base_query_args_obj);

		$this->assertCount(3, $all_queries);
		$this->assertArrayHasKey('author-' . $this->authors[0]->ID, $all_queries);
		$this->assertArrayHasKey('author-' . $this->authors[1]->ID, $all_queries);
		$this->assertArrayHasKey('total_posts', $all_queries);

		$dummy_query = new IASD_Query();

		$external_data = $all_queries['author-' . $this->authors[0]->ID];
		$dummy_query->loadFromExternalData($external_data['request'], $external_data['posts']); 
		$this->assertNotEquals($dummy_query, $specific_query);
		
		$external_data = $all_queries['author-' . $this->authors[1]->ID];
		$dummy_query->loadFromExternalData($external_data['request'], $external_data['posts']); 
		$this->assertEquals($dummy_query, $specific_query);

		global $http;
		$http = null;
	}
}








