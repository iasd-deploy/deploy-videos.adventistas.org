<?php

class IASD_Query extends WP_Query {
	const AJAX_ACTION = 'iasd_query';
	const AJAX_NO_RESULTS = 'iasd_query_no_results';
	const AJAX_COMMAND = 'wp-admin/admin-ajax.php?action=';

	const DATA = 'IASDQuery_data_';
	const TTL = 'IASDQuery_ttl_';
	const CACHE_TIME = 3600;

	private $isRemote = false;
	private $source = null;
	private $grouped_queries = null;

	public static function Hooks() {
		add_action('the_post',			array(__CLASS__, 'CheckDetours'), 5);
		add_action('pre_get_posts',		array(__CLASS__, 'CheckDetours'), 5);

		add_action('wp_ajax_'.self::AJAX_ACTION,			array(__CLASS__, 'Remote'), 5);
		add_action('wp_ajax_nopriv_'.self::AJAX_ACTION,		array(__CLASS__, 'Remote'), 5);

	}
	//Apenas para testes
	public static function Unhooks() {
		remove_action('the_post',			array(__CLASS__, 'CheckDetours'), 5);
		remove_action('pre_get_posts',		array(__CLASS__, 'CheckDetours'), 5);

		remove_action('wp_ajax_'.self::AJAX_ACTION,			array(__CLASS__, 'Remote'), 5);
		remove_action('wp_ajax_nopriv_'.self::AJAX_ACTION,	array(__CLASS__, 'Remote'), 5);
	}

	/**
		REMOTES
	*/
	public static function Remote() {
		if(isset($_REQUEST['query_args'])) {
			$encodedQuery = $_REQUEST['query_args'];
			$args = self::Decode($encodedQuery);

			$args = iasdDecodeToArray($args);

			if(isset($args['source']))
				unset($args['source']);

			$return = array();
			if(isset($args['grouping_taxonomy']) && $args['grouping_taxonomy']) {
				$return = self::Remote_GroupingDoQuery($args);
			} else {
				$return = self::Remote_DoQuery($args);
			}

			echo self::Encode($return);
		}
	}

	public static function Remote_GroupingDoQuery($args) {
		$queries = self::GroupingQueries($args);

		foreach($queries as $slug => $query) {
			$queries[$slug] = self::Remote_FixPosts($query);
		}

		return $queries;
	}

	public static function Remote_DoQuery($args) {

		$query = new IASD_Query($args);
		$return = self::Remote_FixPosts($query);
		return $return;
	}

	public static function Remote_FixPosts($query) {

		$return = array();
		if(is_object($query))
			$query = (array) $query;
		$return['request'] = ($query['request']) ? $query['request'] : array();
		$return['posts'] = ($query['posts']) ? $query['posts'] : array();
		$imageSizes = DefaultImageController::ImageSizes();
		$taxonomies = IASD_Taxonomias::GetAllTaxonomies();

		foreach ($return['posts'] as $k => $v) {
			$post = $return['posts'][$k];
			$return['posts'][$k]->is_remote = true;
			
			$c_permalink = get_permalink($post->ID);

			if (empty($c_permalink)){
				$return['posts'][$k]->guid = $return['posts'][$k]->guid;
			} else {
				$return['posts'][$k]->guid = get_permalink($post->ID);
			}

			$return['posts'][$k]->filter = 'raw';
			$return['posts'][$k]->thumb_name = $return['posts'][$k]->post_title; 

			$post_thumbs = array();
			$thumbnailID = apply_filters('iasd_query_thumbnail', get_post_thumbnail_id($post->ID), $post);

			$post_thumbs['full'] = wp_get_attachment_image_src($thumbnailID, 'full');
			foreach($imageSizes as $imageSizeName => $imageSize) {
				$src = false;

				$imageData = wp_get_attachment_image_src($thumbnailID, $imageSizeName);

				if ( $imageData ) {
					$return['posts'][$k]->thumb_name = get_the_title($thumbnailID);
					list($src, $width, $height) = $imageData;
				} else {
					$src = DefaultImageController::Image($imageSize);
				}

				$post_thumbs[$imageSizeName] = $src;
			}

			if (!empty($post_thumbs['full'])){
				$return['posts'][$k]->thumbs = $post_thumbs;
			}


			$post_taxonomies = array();
			foreach($taxonomies as $taxonomy) {
				$post_taxonomies[$taxonomy] = array();
				$terms = wp_get_post_terms($post->ID, $taxonomy);
				foreach($terms as $term)
					$post_taxonomies[$taxonomy][] = array('slug' => $term->slug, 'name' => $term->name);
			}
			$return['posts'][$k]->taxonomies = $post_taxonomies;

			$post_author = array();
			$author = get_user_by('id', $post->post_author);
			if($author) {
				$post_author['slug']   = $author->user_nicename;
				$post_author['name']   = $author->display_name;
				$post_author['id']     = $post->post_author;
				$post_author['avatar'] = get_avatar($post->post_author, 400);
			}
			$post_author = apply_filters('iasd_query_author', $post_author);
			$return['posts'][$k]->author = $post_author;


			$return['posts'][$k]->meta = get_post_meta($post->ID);
		}

		return $return;
	}

	/**
		DETOURS
	*/

	public static function CheckDetours($param) {
		if(!$param)
			return false;
		if(get_class($param) == 'WP_Post') {
			global $wp_query;
			$param = $wp_query;
		}

		$enable = false;
		if(get_class($param) == __CLASS__) {
			$enable = $param->isRemote() || $param->hasCustomPosts();
		}

		if($enable)
			self::EnableDetours();
		else
			self::DisableDetours();
	}

	//Has unit tests
	public static function DisableDetours() {
		remove_filter('post_link',          	array(__CLASS__, 'DetourForPermalink'), 5, 2);
		remove_filter('post_type_link',     	array(__CLASS__, 'DetourForPermalink'), 5, 2);
		remove_filter('get_post_metadata',  	array(__CLASS__, 'DetourForPostmeta'), 5, 4);
		remove_filter('post_thumbnail_html',	array(__CLASS__, 'DetourForThumbnail'), 5, 5);
		remove_filter('get_avatar',				array(__CLASS__, 'DetourForAvatar'), 99, 5);
	}

	//Has unit tests
	public static function EnableDetours() {
		add_filter('post_link',				array(__CLASS__, 'DetourForPermalink'), 5, 2);
		add_filter('post_type_link',		array(__CLASS__, 'DetourForPermalink'), 5, 2);
		add_filter('get_post_metadata',		array(__CLASS__, 'DetourForPostmeta'), 5, 4);
		add_filter('post_thumbnail_html',	array(__CLASS__, 'DetourForThumbnail'), 5, 5);

		add_filter('get_avatar',			array(__CLASS__, 'DetourForAvatar'), 99, 5);
	}

	public static function DetourForPermalink($permalink, $post = null) {
		if(!$post)
			global $post;
		if(property_exists($post, 'is_remote'))
			if($post->is_remote)
				$permalink = $post->guid;
		if(property_exists($post, 'isManual'))
			if($post->isManual)
				$permalink = $post->guid;
		return $permalink;
	}

	public static function DetourForPostmeta($null, $object_id, $meta_key, $single) {
		if($meta_key === '_thumbnail_id') {
			global $post;

			if ( isset($post) ){
				if(property_exists($post, 'is_remote'))
					if($post->is_remote)
						$null = null;
				if(property_exists($post, 'isManual'))
					if($post->isManual)
						$null = null;
			}
		}

		return $null;
	}

	public static function DetourForThumbnail($html, $post_id, $post_thumbnail_id, $size, $attr) {
		global $post;
		if(property_exists($post, 'thumbs')) {
			if($post->ID == $post_id) {
				if ( is_array($size) )
					$size = join('x', $size);
				if(isset($post->thumbs[$size])) {
					$src = $post->thumbs[$size];

					$default_attr = array(
						'src'	=> $src,
						'class'	=> "iasd-query attachment-$size",
						'alt'   => trim(strip_tags( $post->post_excerpt ? $post->post_excerpt : $post->post_title ))
					);

					$attr = wp_parse_args($attr, $default_attr);
					$attr = array_map( 'esc_attr', $attr );
					$html = rtrim("<img ");
					foreach ( $attr as $name => $value ) {
						$html .= " $name=" . '"' . $value . '"';
					}
					$html .= ' />';
				}
			}
		}

		return $html;
	}

	public static function DetourForAvatar($avatar = false, $id_or_email = false, $size = false, $default = false, $alt = false) {
		if(in_the_loop()) {
			global $post;
			if(isset($post->is_remote) && $post->is_remote) {
				$avatar = $post->author['avatar'];
			}
		}
		return $avatar;
	}

/**
	FIXES
*/

	public static function FixThePosts($the_posts, $qthis) {
		$query_args = $qthis->query;

		if(isset($query_args['fixed_ids'])) {
			$fixed_ids_string = $query_args['fixed_ids'];
			$fixed_ids = array_filter(explode(',', $fixed_ids_string));
			if(!$fixed_ids)
				$fixed_ids = array();

			$custom_posts = (isset($query_args['custom_posts']) && is_array($query_args['custom_posts'])) ? $query_args['custom_posts'] : array();


			if(count($fixed_ids)) {
				$fixed_ids = array_reverse($fixed_ids);
				$posts_base = get_posts(array('posts_per_page' => count($fixed_ids), 'post_type' => 'any', 'post__in' => $fixed_ids));
				$posts = array();
				foreach($posts_base as $post)
					$posts[$post->ID] = $post;

				foreach ($custom_posts as $custom_id => $custom_post) {
					$custom_post = (object) $custom_post;
					$posts[$custom_id] = new WP_Post($custom_post);
				}

				foreach($fixed_ids as $fixed_id)
					if(isset($posts[$fixed_id]))
						array_unshift($the_posts, $posts[$fixed_id]);

				$ppp = isset($query_args['posts_per_page']) ? $query_args['posts_per_page'] : get_option('posts_per_page');
				if(!$ppp)
					$ppp = 10;
				while(count($the_posts) > $ppp)
					array_pop($the_posts);
			}
		}

		return $the_posts;
	}

/**
	QUERY
*/
	function prepareQuery($query) {

		if(!isset($query['post__in']))
			$query['post__in'] = array();
		if(!isset($query['author__in']))
			$query['author__in'] = array();
		if(!isset($query['tax_query']))
			$query['tax_query'] = array();

		if(isset($query['slug_search']))
			$query['s'] = $query['slug_search'];

		if(isset($query['authors'])) {
			if(count($query['authors'])) {
				$authors = $query['authors'];
				foreach($authors as $k => $v)
					$authors[$k] = get_user_by('slug', $v );

				$authors = array_filter($authors);

				foreach($authors as $k => $v)
					$authors[$k] = $v->ID;

				$query['author__in'] = $authors;
			}
			unset($query['authors']);
		}

		if(isset($query['authors_norepeat'])) {
			$authors_norepeat = $query['authors_norepeat'];
			unset($query['authors_norepeat']);

			if($authors_norepeat) {
				if(!count($query['author__in'])) {
					$users = get_users(array('who' => 'authors'));
					foreach ($users as $user) {
						$query['author__in'][] = $user->ID;
					}
				}

				foreach($query['author__in'] as $author_id) {
					$get_posts_params = $query;
					$get_posts_params['posts_per_page'] = 1;
					$get_posts_params['author__in'] = $author_id; //Only author specified
					
					$get_posts_params['post__not_in'] = $get_posts_params['post__in']; //Fetched posts are ignored
					unset($get_posts_params['post__in']); //Fetched posts are ignored

					$posts = get_posts($get_posts_params);

					if(count($posts))
						$query['post__in'][] = $posts[0]->ID;
				}

				unset($query['author__in']);
			}
		}

		if(isset($query['taxonomy_norepeat'])) {
			if(count($query['taxonomy_norepeat'])) {
				$tax_queries = $query['tax_query'];
				$non_repeat_terms = array();
				$taxonomies_pending = array_flip($query['taxonomy_norepeat']);

				foreach($tax_queries as $k => $tax_query) {
					if(is_array($tax_query)){
						if(in_array($tax_query['taxonomy'], $query['taxonomy_norepeat'])) {
							unset($taxonomies_pending[$tax_query['taxonomy']]);
							foreach($tax_query['terms'] as $term_slug)
								$non_repeat_terms[] = get_term_by('slug', $term_slug, $tax_query['taxonomy']);
							$tax_queries[$k] = false;
						}
					}
				}

				foreach($taxonomies_pending as $taxonomy_slug => $useless) {
					$terms = get_terms($taxonomy_slug);
					$non_repeat_terms = array_merge($non_repeat_terms, $terms);
				}

				$tax_queries = array_filter($tax_queries);
				foreach ($non_repeat_terms as $term) {
					$tax_query = $tax_queries;
					$tax_query[] = array(
						'terms' => $term->slug,
						'field' => 'slug',
						'taxonomy' => $term->taxonomy
					);
					$get_posts_params = array();
					$get_posts_params['posts_per_page'] = 1;
					$get_posts_params['tax_query'] = $tax_query; //Only author specified
					$get_posts_params['post__not_in'] = $query['post__in']; //Fetched posts are ignored
					$posts = get_posts($get_posts_params);
					if(count($posts))
						$query['post__in'][] = $posts[0]->ID;
				}
				unset($query['tax_query']);
			}
			unset($query['taxonomy_norepeat']);
		}

		if(isset($query['orderby']) && $query['orderby']) {
			if(strpos($query['orderby'], '%')) {
				list($orderby, $value) = explode('%', $query['orderby']);
				$query['orderby'] = $orderby;
				if($orderby == 'meta_value_num') {
					$query['meta_key'] = $value;
				}
			}
		}

		if(isset($query['date_query']) && $query['date_query']) {
			$query['date_query'] = array('after' => $query['date_query']);
		}

		if(isset($query['fixed_ids'])) {
			$fixed_ids_string = $query['fixed_ids'];
			$fixed_ids = array_filter(explode(',', $fixed_ids_string));

			if(count($fixed_ids)) {
				if(!isset($query['post__not_in']))
					$query['post__not_in'] = array();
				$query['post__not_in'] = array_merge($query['post__not_in'], $fixed_ids);
			}
		}

		return $query;
	}

	function query( $query ) {
		if(isset($query['source'])) {
			$this->setSource($query['source']);
			unset($query['source']);
		}
		if($this->isRemote()) {
			return $this->queryForRemote( $query );
		} else {
			$this->query = $this->query_vars = wp_parse_args($this->prepareQuery($query));

			add_filter('the_posts', 			array(__CLASS__, 'FixThePosts'), 5, 2);

			return $this->get_posts();
		}
	}

	function queryForRemote($query) {
		if(isset($query['slug_search']))
			$query['s'] = $query['slug_search'];

		$this->query = $this->query_vars = wp_parse_args( $query );
		do_action_ref_array('pre_get_posts', array(&$this));

		$external_data = self::RequestRemote($query, $this->getSource());

		$this->loadFromExternalData($external_data['request'], $external_data['posts']);

		return $this->posts;
	}

	function loadFromExternalData($request, $posts) {
		$this->request = $request;
		$this->posts = $posts;
		$this->isRemote = true;

		$this->posts = $posts;
		$this->post_count = count($this->posts);

		if($this->post_count > 0) {
			reset($this->posts);
			$this->post = current($this->posts);
		}

		$GLOBALS['wp_query'] = $this;
	}

	/**
		GROUPED QUERY (Seleção de Taxonomias)
	*/

	static function GroupingQuery($base_query_args) {
		$grouping_slug = null;
		if(is_object($base_query_args))
			$base_query_args = (array) $base_query_args;

		if(isset($base_query_args['grouping_slug'])) {
			$grouping_slug = $base_query_args['grouping_slug'];
			unset($base_query_args['grouping_slug']);
		}

		$grouped_queries = self::GroupingQueries($base_query_args, $grouping_slug);

		$query = new IASD_Query();
		if(!$grouping_slug && (!isset($base_query_args['grouping_forced']) || !$base_query_args['grouping_forced']) || !isset($grouped_queries[$grouping_slug]))
			$grouping_slug = 'default';

		if(isset($grouped_queries[$grouping_slug])) {
			$external_data = $grouped_queries[$grouping_slug];
			$query->loadFromExternalData($external_data['request'], $external_data['posts']);
		}

		return $query;
	}

	static function GroupingQueries($base_query_args, $grouping_slug = null) {
		$grouped_queries = array();
		if(is_object($base_query_args))
			$base_query_args = (array) $base_query_args;

		if(isset($base_query_args['source'])) {
			$grouped_queries = self::RequestRemote($base_query_args, $base_query_args['source'], true);
		} else {
			$taxonomy = get_taxonomy($base_query_args['grouping_taxonomy']);
			unset($base_query_args['grouping_taxonomy']);

			$grouping_slugs = array();
			$base_query_tax = null;

			if(is_object($base_query_args['tax_query']))
				$base_query_args['tax_query'] = (array) $base_query_args['tax_query'];


			if ( is_array($base_query_args['tax_query'] )) {
				foreach($base_query_args['tax_query'] as $k => $tax_query) {
					if(is_object($tax_query))
						$tax_query = (array) $tax_query;

					if(is_array($tax_query) && is_object($taxonomy) && $tax_query['taxonomy'] == $taxonomy->name) {
						$base_query_tax = $tax_query;
						$grouping_slugs = array_merge($grouping_slugs, $tax_query['terms']);
						$base_query_args['tax_query'][$k] = false;
					} else {
						$base_query_args['tax_query'][$k] = $tax_query;
					}
				}
				$base_query_args['tax_query'] = array_filter($base_query_args['tax_query']);
			}

			$post_ids = array();

			foreach($grouping_slugs as $term_slug) {
				if($grouping_slug && $grouping_slug != $term_slug)
					continue;

				$query_args = $base_query_args;
				$base_query_tax['terms'] = array($term_slug);
				$query_args['tax_query'][] = $base_query_tax;
				$query = new IASD_Query($query_args);
				$grouped_queries[$term_slug] = array('request' => $query->request, 'posts' => $query->posts);
				foreach($query->posts as $post)
					if(!in_array($post->ID, $post_ids))
						$post_ids[] = $post->ID;
				wp_reset_query();
			}
			if(!isset($base_query_args['grouping_forced']) || !$base_query_args['grouping_forced']) {
				$query_args = $base_query_args;
				$query_args['post__in'] = $post_ids;
				$query = new IASD_Query($query_args);
				$grouped_queries['default'] = array('request' => $query->request, 'posts' => $query->posts);
			}
		}
		

		return $grouped_queries;
	}

	/**
		ASSESSORS
	*/
	function setSource($url) {
		$this->source = self::FixSourceUrl($url);
		$this->isRemote = !empty($url);
	}
	static function FixSourceUrl($url) {
		if($url) {
			if(substr($url, -1) !== '/')
				$url .= '/';
			if(!strpos($url, ':'))
				$url = 'http://'.$url;
		}
		return $url;
	}
	function unsetSource() {
		$this->setSource(false);
	}
	function getSource() {
		return $this->source;
	}
	static function SourceRequestUrl($source) {
		return ($source) ? $source . self::AJAX_COMMAND.self::AJAX_ACTION : $source;
	}

	function isRemote() {
		return $this->isRemote;
	}
	function hasCustomPosts() {
		return isset($this->query['custom_posts']) && count($this->query['custom_posts']);
	}

	static function Encode($decoded) {
		return base64_encode(json_encode($decoded));
	}

	static function Decode($encoded) {
		$decoded = json_decode(base64_decode($encoded));
		if(isset($decoded->posts) && count($decoded->posts)) {
			foreach($decoded->posts as $k => $post) {
				$decoded->posts[$k] = new WP_Post($post);
			}
		}
		return $decoded;
	}

	static function GetHTTPObject() {
		global $http;

		if(is_null($http))
			$http = apply_filters('get_http_object', new WP_Http());

		return $http;
	}

	static function BuildRequestParams($encoded) {
		return array('body' =>  array('query_args' => $encoded), 'timeout' => 60);
	}

	static function EncodeKey($query) {
		return md5(self::Encode($query));
	}

	static function RequestRemote($query, $requestUrl, $grouped = false) {
		$requestUrl = self::SourceRequestUrl($requestUrl);

		$nonCacheable = isset($query['non_cacheable']);
		$encodedKey = self::EncodeKey($query);
		$isDead = true;
		$results = array('request' => '', 'posts' => array());

		if(!$nonCacheable) {
			if(get_option(self::DATA . $encodedKey, 'not-existing') == 'not-existing')
				add_option(self::DATA . $encodedKey, $results, false, 'no');
			$timeToLive = get_option(self::TTL . $encodedKey, 0);
			$isDead = ($timeToLive <= time()) ? true : false;
		}
		if($isDead || true) {
			$requestResult = self::_RequestRemote($query, $requestUrl, $grouped);

			if(!is_wp_error($requestResult) && $requestResult['response']['code'] == 200 && strlen($requestResult['body']) > 2) {
				if(substr($requestResult['body'], -1) === '0')
					$requestResult['body'] = substr($requestResult['body'], 0, -1); //Tirando o 0 que o Wordpress adiciona nos ajax

				$results = self::Decode($requestResult['body']);

				if(!$results) {
					print_r($requestResult);
					die;
				}

				if(is_object($results))
					$results = (array) $results;

				$results['total_posts'] = 0;
				$results = self::RequestRemote_FixPosts($results, $grouped);

				if($results['total_posts']) {
					delete_option(self::DATA . $encodedKey);
					add_option(self::DATA . $encodedKey, $results, null, 'no');

					update_option(self::TTL  . $encodedKey, time() + self::CACHE_TIME);
				}
			} else {
				do_action(self::AJAX_NO_RESULTS, $query, $requestResult);
			}
		} else {
			$results = get_option(self::DATA . $encodedKey);
		}

		return $results;
	}

	static function _RequestRemote($query, $requestUrl) {
		$http = self::GetHTTPObject();

		$encoded = self::Encode($query);

		$requestResult = $http->post($requestUrl, self::BuildRequestParams($encoded));

		return $requestResult;
	}

	static function RequestRemote_FixPosts($results, $grouped = false) {
		
		if($grouped) {
			foreach($results as $group_slug => $group_result) {
				if(is_object($group_result))
					$group_result = (array) $group_result;
				if(is_array($group_result)) {
					$results[$group_slug] = self::RequestRemote_FixPosts($group_result);
					$results['total_posts'] += $results[$group_slug]['total_posts'];
				}
			}
		} else {

			if (is_array($results['posts'])) {
				foreach ($results['posts'] as $k => $v) {
					$results['posts'][$k]->is_remote = true;

					$results['posts'][$k] = new WP_Post($results['posts'][$k]);
					$results['posts'][$k]->thumbs = (array) $results['posts'][$k]->thumbs;
					$results['posts'][$k]->author = (array) $results['posts'][$k]->author;
					$results['posts'][$k]->taxonomies = (array) $results['posts'][$k]->taxonomies;
				}
			}	
			$results['total_posts'] = count($results['posts']);	
		}
		return $results;
	}
}

IASD_Query::Hooks();








