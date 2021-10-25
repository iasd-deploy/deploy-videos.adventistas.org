<?php

require_once '_test.php';

class IASD_Query_PrepareQuery_test extends IASD_Query__test {


	function setUp() {
		parent::setUp();
	}

	/**
	AUTHORS
	*/

	function test_authorsSelected() {
		$this->addUsersAndPosts();

		$query_args['authors'] = array($this->authors[0]->user_nicename);
		$query_args['posts_per_page'] = 5;

		$query = new IASD_Query();

		$prepared_query = $query->prepareQuery($query_args);

		$this->assertArrayHasKey('author__in', $prepared_query);
		$this->assertTrue(in_array($this->authors[0]->ID, $prepared_query['author__in']));

		$queryExecuted = new IASD_Query($query_args);

		while($queryExecuted->have_posts()) {
			$queryExecuted->the_post();
			global $post;
			$this->assertEquals($this->authors[0]->ID, $post->post_author);
		}

		$this->assertEquals($queryExecuted->post_count, $query_args['posts_per_page']);

		wp_reset_query();
	}

	function test_authorsNoRepeat() {
		$this->addUsersAndPosts();
		
		$query_args['authors_norepeat'] = 1;
		$query_args['posts_per_page'] = 5;

		$query = new IASD_Query();

		$prepared_query = $query->prepareQuery($query_args);

		$this->assertArrayNotHasKey('author__in', $prepared_query);
		$this->assertArrayHasKey('post__in', $prepared_query);

		$queryExecuted = new IASD_Query($query_args);

		$authors_exists = $this->authors_exists;
		while($queryExecuted->have_posts()) {
			$queryExecuted->the_post();
			global $post;
			$this->assertTrue(isset($authors_exists[$post->post_author]));

			unset($authors_exists[$post->post_author]);
		}
		$this->assertEquals(0, count($authors_exists));
		$this->assertNotEquals($queryExecuted->post_count, $query_args['posts_per_page']);
		$this->assertEquals($queryExecuted->post_count, count($this->authors_exists));

		wp_reset_query();
	}

	function test_authorsSelectedNoRepeat() {
		$this->addUsersAndPosts();
		
		$query_args['authors_norepeat'] = 1;
		$query_args['authors'] = array($this->authors[0]->user_nicename, $this->authors[2]->user_nicename);
		$query_args['posts_per_page'] = 5;

		$query = new IASD_Query($query_args);
		
		$queryExecuted = new IASD_Query($query_args);

		$authors_exists = $this->authors_exists;
		while($queryExecuted->have_posts()) {
			$queryExecuted->the_post();
			global $post;
			$this->assertTrue(isset($authors_exists[$post->post_author]));

			unset($authors_exists[$post->post_author]);
		}
		$this->assertEquals(1, count($authors_exists));
		foreach($authors_exists as $author_id => $true)
			$this->assertEquals($this->authors[1]->ID, $author_id);

		$this->assertNotEquals($queryExecuted->post_count, $query_args['posts_per_page']);
		$this->assertEquals($queryExecuted->post_count, count($query_args['authors']));

		wp_reset_query();
	}

	/**
	TAXONOMIES
	*/

	function test_taxonomyNoRepeat() {
		$this->addUsersAndPosts();

		$query_args['taxonomy_norepeat'] = array(self::TAX_FOR_TEST);
		$query_args['posts_per_page'] = 10;

		$query = new IASD_Query();

		$prepared_query = $query->prepareQuery($query_args);

		$this->assertArrayHasKey('post__in', $prepared_query);
		$this->assertEquals(3, count($prepared_query['post__in']));

		$queryExecuted = new IASD_Query($query_args);

		$taxonomies_got = array();
		while($queryExecuted->have_posts()) {
			$queryExecuted->the_post();
			global $post;
			$terms = wp_get_post_terms($post->ID, self::TAX_FOR_TEST);
			foreach($terms as $term) {
				$this->assertFalse(in_array($term->slug, $taxonomies_got));
				$taxonomies_got[] = $term->slug;
			}

		}
		$this->assertNotEquals($queryExecuted->post_count, 5);
		$this->assertNotEquals($queryExecuted->post_count, $query_args['posts_per_page']);

		wp_reset_query();
	}

	function test_taxonomySelectedNoRepeat() {
		$this->addUsersAndPosts();

		$query_args['taxonomy_norepeat'] = array(self::TAX_FOR_TEST);
		$reference_terms = array('author-' . $this->authors[1]->ID, 'author-' . $this->authors[2]->ID);
		$query_args['tax_query'] = array(
				array(
					'taxonomy' => self::TAX_FOR_TEST,
					'field' => 'slug',
					'terms' => $reference_terms,
				)
			);
		$query_args['posts_per_page'] = 10;

		$query = new IASD_Query();

		$prepared_query = $query->prepareQuery($query_args);

		$this->assertArrayHasKey('post__in', $prepared_query);
		$this->assertEquals(2, count($prepared_query['post__in']));

		$queryExecuted = new IASD_Query($query_args);

		$terms_reversed = array_flip($reference_terms);

		while($queryExecuted->have_posts()) {
			$queryExecuted->the_post();
			global $post;
			$terms = wp_get_post_terms($post->ID, self::TAX_FOR_TEST);
			foreach($terms as $term) {
				$this->assertTrue(isset($terms_reversed[$term->slug]));
				unset($terms_reversed[$term->slug]);
			}
		}
		$this->assertEquals(0, count($terms_reversed));
		$this->assertNotEquals($queryExecuted->post_count, $query_args['posts_per_page']);

		wp_reset_query();
	}
}








