<?php

require_once '_test.php';

class IASD_Query_FixedIds_test extends IASD_Query__test {

	/**
	AUTHORS
	*/

	function testWillReturnFixedPost() {
		$this->addUsersAndPosts();
		$tested_author = $this->authors_ids[0];

		$post_ids = array();
		foreach($this->posts as $post)
			if($post->post_author == $tested_author)
				$post_ids[] = $post->ID;

		$args = array('fixed_ids' => implode(',', $post_ids), 'author__not_in' => $tested_author, 'posts_per_page' => count($post_ids));
		$query = new IASD_Query($args);

		$returned_posts_ids = array();

		while($query->have_posts()) {
			$query->the_post();
			$returned_posts_ids[] = get_the_ID();
		}

		foreach($post_ids as $post_id)
			$this->assertContains($post_id, $returned_posts_ids);

		$this->assertEquals(count($post_ids), count($returned_posts_ids));
	}

	function testWillReturnFixedPostWithoutPostsPerPage() {
		$this->addUsersAndPosts();
		$tested_author = $this->authors_ids[0];

		$post_ids = array();
		foreach($this->posts as $post)
			if($post->post_author == $tested_author)
				$post_ids[] = $post->ID;

		$args = array('fixed_ids' => implode(',', $post_ids), 'author__not_in' => $tested_author);
		$query = new IASD_Query($args);

		$returned_posts_ids = array();

		while($query->have_posts()) {
			$query->the_post();
			$returned_posts_ids[] = get_the_ID();
		}


		foreach($post_ids as $post_id)
			$this->assertContains($post_id, $returned_posts_ids);

		$this->assertNotEquals(count($post_ids), count($returned_posts_ids));
	}
}








