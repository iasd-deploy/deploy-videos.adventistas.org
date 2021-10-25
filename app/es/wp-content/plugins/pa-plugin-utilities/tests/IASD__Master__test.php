<?php

// @codeCoverageIgnore
define('UNIT_TESTING', true);

class IASD__Master__test extends WP_UnitTestCase {
	var $authors = null;
	var $authors_ids = null;
	var $authors_exists = null;
	var $posts = null;
	const TAX_FOR_TEST = 'iasdquerytax';
	public $fakePostTypes = array('fake_post_a', 'fake_post_b');

	static function SetTaxonomies($posts) {
		if(!in_array('post', $posts))
			$posts[] = 'post';
		if(!in_array('fake_post_a', $posts))
			$posts[] = 'fake_post_a';
		if(!in_array('fake_post_b', $posts))
			$posts[] = 'fake_post_b';
		return $posts;
	}

	static public $authorNumber = 100;
	static function AuthorNumber() {
		self::$authorNumber++;

		return self::$authorNumber;
	}
	function getAuthorNumber() {
		return self::AuthorNumber();
	}

	static public $postNumber = 100;
	static function PostNumber() {
		self::$postNumber++;

		return self::$postNumber;
	}
	function getPostNumber() {
		return self::PostNumber();
	}

	static public $widgetNumber = 100;
	static function widgetNumber() {
		self::$widgetNumber++;

		return self::$widgetNumber;
	}
	function getWidgetNumber() {

		return self::widgetNumber();
	}

	function setUp() {
		$_GET = $_REQUEST = $_POST = array();
		global $postTypesRegistered, $http;
		if(!$postTypesRegistered) {
			foreach($this->fakePostTypes as $fakePostType)
				register_post_type( $fakePostType, array('public' => true, 'taxonomies' => array(IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS)));
			IASD_Taxonomias::RegisterTaxonomies();
		}
		IASD_Taxonomias::RegisterTaxonomies();
		$postTypesRegistered = true;

		register_taxonomy(self::TAX_FOR_TEST, array('post'));

		parent::setUp();
	}

	function addFakeAuthor($data = array()) {
		$id = $this->getAuthorNumber();
		if(!isset($data['user_login']))
			$data['user_login'] = 'test_user_' . $id;
		$id = $this->getAuthorNumber();
		if(!isset($data['user_pass']))
			$data['user_pass'] = md5('test_user_' . $id);

		$user_id = wp_insert_user($data);
		update_user_meta($user_id, 'wp_user_level', 2);
		$user = get_user_by('id', $user_id);

		return $user;
	}

	function registerDSA() {
		$term_dsa = wp_insert_term('dsa', IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS);
		if(!is_wp_error($term_dsa)) {
			$this->taxonomies[IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS][] = 'dsa';

			$term_dsa_child = wp_insert_term('dsa-child', IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS, array('parent' => $term_dsa['term_id']));
		}
	}

	function addFakePost($post = array()) {
		$id = $this->getPostNumber();
		
		$post['post_content'] = sprintf('Post content 1 %s', $id);
		$post['post_title'] = sprintf('Post title %s', $id);
		$post['post_excerpt'] = sprintf('Post excerpt %s', $id);
		$post['post_status'] = 'publish';
		if(!isset($post['post_type']))
			$post['post_type'] = 'post';

		$post_id = wp_insert_post($post);

		return get_post($post_id);
	}

	public $taxonomies = array();
	public $fakePostsToAdd = 12;
	function addFakePosts() {
		$terms_ids = array();
		$taxonomies = IASD_Taxonomias::GetAllTaxonomies(); 
		foreach($taxonomies as $taxonomy_slug) {
			$this->taxonomies[$taxonomy_slug] = array();
			$terms_ids[$taxonomy_slug] = array();

			for($i = 1; $i < $this->fakePostsToAdd; $i++) {
				$term = wp_insert_term($taxonomy_slug . '-' . $i, $taxonomy_slug);
				if(!is_wp_error($term)) {
					$this->taxonomies[$taxonomy_slug][] = $taxonomy_slug . '-' . $i;
				}
			}
		}

		$this->registerDSA();

		foreach($this->fakePostTypes as $post_type) {
			for($i = 0; $i < $this->fakePostsToAdd; $i++) {

				$post = $this->addFakePost(array('post_type' => $post_type));
				add_post_meta($post->ID, 'post_meta_title', $post->post_title);

				if($i > 0) {
					$term_added = wp_set_object_terms($post->ID, 'dsa', IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS, true);
				}

				foreach($taxonomies as $taxonomy_slug) {
					if(isset($this->taxonomies[$taxonomy_slug][$i])) {
						$term_added = wp_set_object_terms($post->ID, $taxonomy_slug . '-' . $i, $taxonomy_slug, true);
					}
				}
			}
		}
	}

	function addUsersAndPosts($post_number = 5, $author_number = 3) {
		$this->authors = array();
		$this->authors_ids = array();
		wp_insert_term('cat-authors', 'category');
		for($i = 0; $i < $author_number; $i++) {
			$this->authors[$i] = $this->addFakeAuthor(array('role' => 'author'));
			$this->authors_ids[$i] = $this->authors[$i]->ID;
			$this->authors_exists[$this->authors[$i]->ID] = true;

			wp_insert_term('author-' . $this->authors[$i]->ID, self::TAX_FOR_TEST);
			wp_insert_term('cat-author-' . $this->authors[$i]->ID, 'category');
		}

		$this->posts = array();
		foreach ($this->authors as $author) {
			for($i = 0; $i < $post_number; $i++) {
				$k = count($this->posts);
				$this->posts[$k] = $this->addFakePost(array('post_author' => $author->ID, 'post_type' => 'post'));

				wp_set_object_terms($this->posts[$k]->ID, 'author-' . $author->ID, self::TAX_FOR_TEST, true);
				wp_set_object_terms($this->posts[$k]->ID, 'cat-author-' . $author->ID, 'category', true);
				wp_set_object_terms($this->posts[$k]->ID, 'cat-authors', 'category', true);
			}
		}
	}

	function createNonSavedFakePost($post = array()) {
		$baseFakePost = new stdClass();
		$baseFakePost->ID = $id = $this->getPostNumber();
		$baseFakePost->post_author = 0;
		$baseFakePost->post_date = '0000-00-00 00:00:00';
		$baseFakePost->post_date_gmt = '0000-00-00 00:00:00';
		$baseFakePost->post_content = sprintf('Post content %s', $id);
		$baseFakePost->post_title = sprintf('Post title %s', $id);
		$baseFakePost->post_excerpt = sprintf('Post excerpt %s', $id);
		$baseFakePost->post_status = 'publish';
		$baseFakePost->comment_status = 'open';
		$baseFakePost->ping_status = 'open';
		$baseFakePost->post_password = '';
		$baseFakePost->post_name = '';
		$baseFakePost->to_ping = '';
		$baseFakePost->pinged = '';
		$baseFakePost->post_modified = '0000-00-00 00:00:00';
		$baseFakePost->post_modified_gmt = '0000-00-00 00:00:00';
		$baseFakePost->post_content_filtered = '';
		$baseFakePost->post_parent = 0;
		$baseFakePost->guid = 'permalink_used_for_test';
		$baseFakePost->menu_order = 0;
		$baseFakePost->post_type = 'post';
		$baseFakePost->post_mime_type = '';
		$baseFakePost->comment_count = 0;
		$baseFakePost->filter = 'raw';
		$baseFakePost->is_remote = true;
		$baseFakePost->thumbs = array();
		$baseFakePost->thumbs['post-thumbnail'] = 'http://placehold.it/150x150';
		$baseFakePost->author = array('avatar' => 'http://placehold.it/400x400');
		$baseFakePost->taxonomies = array();

		$fakePost = new WP_Post($baseFakePost);

		return $fakePost;
	}

	function getRegisteredWidgets() {
		global $wp_widget_factory;
		return array_keys($wp_widget_factory->widgets);
	}

	function saveAndStop() {
		global $wpdb;
		$wpdb->query('COMMIT;');
		die;
	}

}

add_filter(IASD_Taxonomias::ACTION_SEDES_REGIONAIS, array('IASD__Master__test', 'SetTaxonomies'));










