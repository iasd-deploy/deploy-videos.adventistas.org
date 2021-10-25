<?php


class IASD_Disqus {
	public static function FindThreads() {
		$forum = get_option('disqus_forum_url', false);
		if(!$forum)
			return false;
		$query = new WP_Query(
					array(
						'meta_query' => array(
							'relation' => 'OR',
							array('key' => 'dsq_thread_id', 'value' => '0', 'type' => 'NUMERIC'),
							array('key' => 'dsq_thread_id', 'value' => '0', 'compare' => 'NOT EXISTS'),
							array('key' => 'dsq_comment_count', 'value' => '0', 'compare' => 'NOT EXISTS'),
						),
						'orderby' => 'rand',
						'post_status' => 'publish', 
						'post_type' => array('post', 'colunas', 'events', 'releases'), 
						'posts_per_page' => 20)
					);

		$_posts = $query->posts;
		if(!count($_posts))
			return -1;
		$local_posts = array();
		$local_posts_links = array();

		foreach($_posts as $post) {
			$permalink = get_permalink($post->ID);
			$permalink = str_replace('http://192.168.1.207/noticias/pt', 'http://noticias.iasd.dev.nextt.com.br/pt', $permalink);

			$local_posts[$permalink] = $post->ID;
			$local_posts_links[] = 'ident:'.$post->ID.' '.$permalink;
		}

		$url_request = 'https://disqus.com/api/3.0/threads/set.json';
		$params = array();

		$params['api_key'] = 'xTLE9WhXHBuhEQ3URqWPgmsKO7CNtOD93iyUiu1q1WkXU6HjMzibxZ285AVZvhgG';
		$params['forum'] = $forum;
		$params['thread'] = $local_posts_links;

		$params = http_build_query($params);
		$params = preg_replace('/\%5B([0-9]+)\%5D/', '%5B%5D', $params);

		$full_url_request = $url_request . '?' . $params;
		$response = wp_remote_get($full_url_request, array('timeout' => 30));
		if($response['response']['code'] == 200) {
			$json_body = json_decode($response['body']);

			$threads = $json_body->response;
			foreach ($threads as $thread) {
				$thread_url = $thread->link;
				if(isset($local_posts[$thread_url])) {
					$post_id = $local_posts[$thread_url];
					delete_post_meta($post_id, 'dsq_thread_id');
					add_post_meta($post_id, 'dsq_thread_id', $thread->id);
					delete_post_meta($post_id, 'dsq_comment_count');
					add_post_meta($post_id, 'dsq_comment_count', $thread->posts);
				}
			}
			return count($threads);
		}
		return 0;
	}

	public static function UpdatePopularThreads() {
		$forum = get_option('disqus_forum_url', false);
		if(!$forum)
			return -1;

		$url_request = 'https://disqus.com/api/3.0/threads/listPopular.json';
		$params = array();
		$params['api_key'] = 'xTLE9WhXHBuhEQ3URqWPgmsKO7CNtOD93iyUiu1q1WkXU6HjMzibxZ285AVZvhgG';
		$params['forum'] = $forum;
		$params['limit'] = 100;
		$params['since'] = '2d';

		$full_url_request = $url_request . '?' . http_build_query($params);
		$response = wp_remote_get($full_url_request, array('timeout' => 30));
		if($response['response']['code'] == 200) {
			$json_body = json_decode($response['body']);

			$threads = $json_body->response;
			foreach ($threads as $thread) {
				list($id, $guid) = explode(' ', $thread->identifiers[0]);
				$post = get_post($id);
				if($post) {
					delete_post_meta($id, 'dsq_comment_count');
					add_post_meta($id, 'dsq_comment_count', $thread->posts);
				}
			}
			return count($threads);
		}
		return -1;
	}

	public static function Javascript() {
		$forum = get_option('disqus_forum_url', false);
		if(!$forum)
			return false;
?>
				<script type="text/javascript">
					(function($) {
						var disqus_shortname = '<?php echo $forum; ?>';
						var reset_disqus = function(){
							DISQUS.reset({
								reload: true,
							});
						};				

					 	// var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
					 	// dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
					 	// (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
						

					 	lastWindowWidth = null; //yes, needs to be global
						window.onresize = function() { 
							//IE8 call onresize several times onload... test if size changed to avoid page loading problems
							var width = $(window).width();
							if (lastWindowWidth != width){
								reset_disqus(); 
								lastWindowWidth = width;
							}
						};

					})(jQuery);
				</script>
<?php
	}

	public static function Init() {
		if ( ! wp_next_scheduled( 'IASD_Disqus::Daily' ) )
			wp_schedule_event( time(), 'daily', 'IASD_Disqus::Daily');
		if ( ! wp_next_scheduled( 'IASD_Disqus::Hourly' ) )
			wp_schedule_event( time(), 'hourly', 'IASD_Disqus::Hourly');
	}
}

add_action('iasd_disqus_javascript', array('IASD_Disqus', 'Javascript'));
add_action('init', array('IASD_Disqus', 'Init'), 100);
add_action('IASD_Disqus::Hourly', array('IASD_Disqus', 'FindThreads'));
add_action('IASD_Disqus::Hourly', array('IASD_Disqus', 'UpdatePopularThreads'));


