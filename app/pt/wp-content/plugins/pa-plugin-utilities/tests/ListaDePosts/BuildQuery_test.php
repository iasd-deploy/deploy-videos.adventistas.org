<?php

require_once '_Widget_test.php';

class IASD_ListaDePosts_Render_GroupingTaxonomy_test extends IASD_ListaDePosts__Widget_test {
	function test_BuildQueryUrl() {
		$assets_defaultInstance = IASD_ListaDePosts::DefaultInstance();

		$assets_query_args = IASD_ListaDePosts::BuildQuery($assets_defaultInstance);

		$this->assertArrayNotHasKey('number', $assets_query_args);
		$this->assertArrayNotHasKey('saved', $assets_query_args);
		$this->assertArrayNotHasKey('secret', $assets_query_args);
		$this->assertArrayNotHasKey('sidebar', $assets_query_args);
		$this->assertArrayNotHasKey('taxonomy_query', $assets_query_args);
		$this->assertArrayNotHasKey('title', $assets_query_args);
		$this->assertArrayNotHasKey('view', $assets_query_args);
		$this->assertArrayNotHasKey('width', $assets_query_args);
		$this->assertArrayNotHasKey('seemore', $assets_query_args);
		$this->assertArrayNotHasKey('seemore_text', $assets_query_args);
		$this->assertArrayNotHasKey('source_id', $assets_query_args);
		$this->assertArrayNotHasKey('source_extra', $assets_query_args);

		$assets_query_params = IASD_ListaDePosts::BuildQueryUrl($assets_query_args);

		$query_params = array();
		parse_str($assets_query_params, $query_params);

		$this->assertGreaterThan(count($query_params), count($assets_query_args));

		$this->assertArrayNotHasKey('posts_per_page', $query_params);
		$this->assertArrayNotHasKey('taxonomy_norepeat', $query_params);
		$this->assertArrayNotHasKey('authors_norepeat', $query_params);
		$this->assertArrayHasKey('iasd_s', $query_params);
	}

	function testIfProperlyUsesGroupingSlugNotForced() {
		$this->addUsersAndPosts();

		$key1 = 'author-' . $this->authors[0]->ID;
		$key2 = 'author-' . $this->authors[1]->ID;

		$widget = new IASD_ListaDePosts();
		$widget->number = $this->getWidgetNumber();
		$base_instance = IASD_ListaDePosts::DefaultInstance();
		$base_instance['grouping_taxonomy'] = self::TAX_FOR_TEST;
		$base_instance['taxonomy_query'] = array();
		$base_instance['taxonomy_query'][self::TAX_FOR_TEST] = array();
		$base_instance['taxonomy_query'][self::TAX_FOR_TEST]['taxonomy'] = self::TAX_FOR_TEST;
		$base_instance['taxonomy_query'][self::TAX_FOR_TEST]['field'] = 'slug';
		$base_instance['taxonomy_query'][self::TAX_FOR_TEST]['terms'] = array($key1, $key2);
		$base_instance['number'] = $widget->number;
		$widget->_setInstance($base_instance);

		$query0 = $widget->query();
		$this->assertNotNull($query0->posts, 'No slug defined, posts from all returned');

		$base_instance['grouping_slug'] = $key1;
		$widget->_setInstance($base_instance);
		$query1 = $widget->query();
		$this->assertNotNull($query1->posts);

		$base_instance['grouping_slug'] = $key2;
		$widget->_setInstance($base_instance);
		$query2 = $widget->query();
		$this->assertNotNull($query2->posts);

		$this->assertNotEquals($query1, $query2);
	}

	function testIfProperlyUsesGroupingSlugForced() {
		$this->addUsersAndPosts();

		$key1 = 'author-' . $this->authors[0]->ID;
		$key2 = 'author-' . $this->authors[1]->ID;

		$widget = new IASD_ListaDePosts();
		$widget->number = $this->getWidgetNumber();
		$base_instance = IASD_ListaDePosts::DefaultInstance();
		$base_instance['grouping_taxonomy'] = self::TAX_FOR_TEST;
		$base_instance['grouping_forced'] = 1;
		$base_instance['taxonomy_query'] = array();
		$base_instance['taxonomy_query'][self::TAX_FOR_TEST] = array();
		$base_instance['taxonomy_query'][self::TAX_FOR_TEST]['taxonomy'] = self::TAX_FOR_TEST;
		$base_instance['taxonomy_query'][self::TAX_FOR_TEST]['field'] = 'slug';
		$base_instance['taxonomy_query'][self::TAX_FOR_TEST]['terms'] = array($key1, $key2);
		$base_instance['number'] = $widget->number;
		$widget->_setInstance($base_instance);

		$query0 = $widget->query();
		$this->assertNull($query0->posts, 'No slug defined, nothing returned');
	}

}