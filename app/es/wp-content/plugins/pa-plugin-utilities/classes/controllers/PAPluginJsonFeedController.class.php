<?php


/*************************** Basic Widget */

class PAPluginJsonFeedController extends WP_Widget
{
	public function thumbnailSize($thumbnail, $size) {
		if($thumbnail && strpos($thumbnail, '/static/img/iasd-placeholder.png') === false) {
			$last_dot = strrpos($thumbnail, '.');
			$ext = substr($thumbnail, $last_dot);
			$thumbnail = substr($thumbnail, 0, 	$last_dot) . '-' . $size . $ext;
		} else {
			$thumbnail = DefaultImageController::Image();
		}

		return $thumbnail;
	}

	var $with_current_term = true;
	public static function RegisterWidget($class) {
		register_widget($class);

		$classes = get_option('PAPluginJsonFeedController', array());
		if(!in_array($class, $classes))
			$classes[] = $class;
		update_option('PAPluginJsonFeedController', $classes);

		if(!has_action('PAPluginJsonFeedController', array('PAPluginJsonFeedController', 'CronTask')))
			add_action('PAPluginJsonFeedController', array('PAPluginJsonFeedController', 'CronTask'));
	}

	function getDefaultInstance($instance) {
		return array_merge(array('thumbnail' => '', 'secret' => '', 'limit' => '3', 'source' => $this->getDefaultServer(), 'title' => '', 'link' => '', 'current_term' => $this->getDefaultTerm(), 'taxonomy' => array()),  (array) $instance);

	}

	function form($instance)
	{
		$instance = $this->getDefaultInstance($instance);
?>
		<div>
			<p class="pa_widget_multi_content">
<?php
		$this->Form_Title($instance);
		$this->Form_Limit($instance);
		$this->Form_Source($instance);
		$this->Form_Link($instance);
		$this->Form_Thumbnail($instance);
		$this->Form_Taxonomies($instance);
		$this->Form_Secret($instance);
?>
			</p>
		</div>
	<?php
	}

	public function Form_Secret($instance) {
?>
				<input id="<?php echo $this->get_field_id('secret'); ?>"
					   name="<?php echo $this->get_field_name('secret'); ?>"
					   type="hidden" value="<?php echo esc_attr($instance['secret']); ?>" />
				<?php if($instance['secret']): ?>
				<br /><br />
				<b>
					<?php _e('Last update:'); echo ' ', get_option('jsn_up_'.$instance['secret'], __('Nunca sincronizou', 'iasd')); ?>
				</b>
<?php endif;
	}
	public function Form_Title($instance) {
?>
				<label for="<?php echo $this->get_field_id('title'); ?>">
					<?php _e('Title:'); ?>
				</label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
					   name="<?php echo $this->get_field_name('title'); ?>"
					   type="text" value="<?php echo esc_attr($instance['title']); ?>" />
				<br />
<?php
	}
	public function Form_Limit($instance) {
?>
				<label for="<?php echo $this->get_field_id('limit'); ?>">
					<?php _e('Quantidade:'); ?>
				</label>
				<input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>"
					   name="<?php echo $this->get_field_name('limit'); ?>"
					   type="text" value="<?php echo esc_attr($instance['limit']); ?>" />
				<br />
<?php
	}
	public function Form_Source($instance) {
?>
				<label for="<?php echo $this->get_field_id('source'); ?>">
					<?php _e('Servidor:'); ?>
				</label>
				<input class="widefat" id="<?php echo $this->get_field_id('source'); ?>"
					   name="<?php echo $this->get_field_name('source'); ?>"
					   type="text" value="<?php echo esc_attr($instance['source']); ?>" />
				<br />
<?php
	}
	public function Form_Link($instance) {
?>
				<label for="<?php echo $this->get_field_id('link'); ?>">
					<?php _e('Link do Veja Mais:'); ?>
				</label>
				<input class="widefat" id="<?php echo $this->get_field_id('link'); ?>"
					   name="<?php echo $this->get_field_name('link'); ?>"
					   type="text" value="<?php echo esc_attr($instance['link']); ?>" />
				<br />
<?php
	}
	public function Form_Thumbnail($instance) {
?>
				<label for="<?php echo $this->get_field_id('thumbnail'); ?>">
					<?php _e('Apenas com thumbnail:'); ?>
				</label>
				<input class="widefat" id="<?php echo $this->get_field_id('thumbnail'); ?>"
					   name="<?php echo $this->get_field_name('thumbnail'); ?>"
					   type="checkbox" value="true" <?php if(isset($instance['thumbnail']) && $instance['thumbnail']) echo 'checked="checked"'; ?> />
				<br />
<?php
	}
	public function Form_Taxonomies($instance) {
?>
				<style>
					.json_widgets_taxonomies .json_taxonomies_list {
						display: none;
						margin: 0px;
					}
					.json_widgets_taxonomies .json_taxonomies_list LI {
						list-style: none;
						margin: 0px;
					}
					.json_widgets_taxonomies .json_taxonomies_list UL {
						margin-left: 15px;
					}
				</style>
				<div class="json_widgets_taxonomies">
					<?php
						$taxonomies = IASD_Taxonomias::GetAllTaxonomies();
						foreach($taxonomies as $taxonomy_slug):
							$taxonomy = get_taxonomy($taxonomy_slug);

							if(!isset($instance['taxonomy'][$taxonomy_slug]))
								$instance['taxonomy'][$taxonomy_slug] = array();

							if(!isset($instance['current_term']))
								$instance['current_term'] = '';

						?>
						<br />
						<span class="title inline-edit-categories-label">
							<?php
								echo $taxonomy->label;
								if(count($instance['taxonomy'][$taxonomy_slug])) echo ' <span class="count">('.count($instance['taxonomy'][$taxonomy_slug]).')</span>';
								if(isset($instance['current_term']) && $instance['current_term'] == $taxonomy_slug) echo ' <b>*</b>';
							?>
							<a onclick="jQuery('#<?php echo $this->get_field_id($taxonomy_slug.'-list'); ?>').toggle(); jQuery('#<?php echo $this->get_field_id($taxonomy_slug.'-hide'); ?>').toggle(); jQuery('#<?php echo $this->get_field_id($taxonomy_slug.'-show'); ?>').toggle(); return false;" id="<?php echo $this->get_field_id($taxonomy_slug.'-show'); ?>" style="display: none;" href="">[menos]</a>
							<a onclick="jQuery('#<?php echo $this->get_field_id($taxonomy_slug.'-list'); ?>').toggle(); jQuery('#<?php echo $this->get_field_id($taxonomy_slug.'-hide'); ?>').toggle(); jQuery('#<?php echo $this->get_field_id($taxonomy_slug.'-show'); ?>').toggle(); return false;" id="<?php echo $this->get_field_id($taxonomy_slug.'-hide'); ?>" style="display: inline;" href="">[mais]</a>
						</span>
						<ul id="<?php echo $this->get_field_id($taxonomy_slug.'-list'); ?>" taxonomy="<?php $taxonomy_slug; ?>" class="json_taxonomies_list" data="<?php echo implode(',', $instance['taxonomy'][$taxonomy_slug]); ?>">
							<?php
								if($this->with_current_term) {
									echo '<li><label>
									<input id="'.$this->get_field_id('current_term_'.$taxonomy_slug).'"
					   					name="'.$this->get_field_name('current_term').'"
										type="checkbox" value="'.$taxonomy_slug.'" ' . ((isset($instance['current_term']) && $instance['current_term'] == $taxonomy_slug) ? 'checked="checked"' : '').' />
										<b>'.__('Usar termo atual', 'iasd').'</b>
										</label>
										</li>';
								}
								wp_terms_checklist( null, array('taxonomy' => $taxonomy_slug, 'selected_cats' => $instance['taxonomy'][$taxonomy_slug], 'checked_ontop' => false, 'walker' => new Widget_Category_Checklist($this->get_field_name('taxonomy]['.$taxonomy_slug))));
							?>
						</ul>
					<?php endforeach; ?>
				</div>
<?php
	}

	function update($new_instance, $old_instance)
	{
		if(!count($old_instance))
			$old_instance = $this->getDefaultInstance(array());

		if(!count($new_instance))
			$new_instance = $old_instance;

		$new_instance = array_merge($old_instance, $new_instance);

		if(!isset($new_instance['secret']) || !$new_instance['secret'])
			$new_instance['secret'] = sha1(md5(rand()));

		$WidgetClassName = get_class($this);

		if($new_instance['source'] && !$new_instance['current_term']) {
			call_user_func(array($WidgetClassName, 'DoCronTask'), $new_instance, $WidgetClassName);
		}

		return $new_instance;
	}

	function widget($args, $instance)
	{
		//Precisa Implementar
	}

	static function Init() {
		//register_widget(__CLASS__);
		//Não vai

		add_image_size('thumb_60x35', 60, 35, true);
		add_image_size('thumb_60x40', 60, 40, true);
		add_image_size('thumb_45x45', 45, 45, true);
		add_image_size('thumb_140x90', 140, 90, true);
		add_image_size('thumb_300x300', 300, 300, true);
		if(!wp_next_scheduled('PAPluginJsonFeedController')) {
			wp_schedule_event(time(), 'hourly', 'PAPluginJsonFeedController');
		}
	}

	static function BasicOptions() {
		return array();
	}

	static function CronTask() {
		$classes = get_option('PAPluginJsonFeedController', array());
		foreach($classes as $WidgetClassName) {
			var_dump($WidgetClassName);
			if(!class_exists($WidgetClassName))
				continue;

			$option = 'widget_' . strtolower($WidgetClassName);
			$widgets = get_option( $option, array() );
			if(!is_array($widgets))
				$widgets = array();

			$ignores = array();
			foreach($widgets as $k => $instance) {
				if(!is_array($instance))
					continue;

				if(!isset($instance['secret']))
					continue;

				if(isset($ignores[$instance['secret']]))
					continue;
				$ignores[$instance['secret']] = true;

				if(!isset($instance['source']))
					continue;

				call_user_func(array($WidgetClassName, 'DoCronTask'), $instance, $WidgetClassName);
			}
		}

	}

	public static function BuildWidgetRequest($instance, $WidgetClassName) {
		$request = call_user_func(array($WidgetClassName, 'BasicOptions'));
		$request['posts_per_page'] = $instance['limit'];
		$request['limit'] = $instance['limit'];
		if(isset($instance['post_type']))
			$request['post_type'] = $instance['post_type'];
		if(isset($instance['include_author_information']))
			$request['include_author_information'] = $instance['include_author_information'];

		if(isset($instance['taxonomy'])) {
			$request['tax_query'] = array();
			foreach($instance['taxonomy'] as $taxonomy => $id_list) {
				foreach($id_list as $k => $term_id) {
					$term = get_term($term_id, $taxonomy);
					$id_list[$k] = $term->slug;
				}
				$request['tax_query'][] = array('taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => $id_list,);
			}
		}
		if(isset($instance['thumbnail']) && $instance['thumbnail']) {
			if(!isset($request['meta_query']))
				$request['meta_query'] = array();

			$request['meta_query'][] = array('key' => '_thumbnail_id', 'compare' => 'EXISTS');
		}
		if(isset($instance['group_of_children']))
			$request['group_of_children'] = $instance['group_of_children'];
		$request['widget_class'] = $WidgetClassName;

		return $request;
	}

	public static function UpdateCache($instance, $WidgetClassName) {
		$request = self::BuildWidgetRequest($instance, $WidgetClassName);

		$json_feed = self::DoCurlRequest($request, $instance);

		return $json_feed;
	}

	public static function DoCronTask($instance, $WidgetClassName, $return = false) {
		self::SetInstance($instance);

		$json_feed = self::UpdateCache($instance, $WidgetClassName);

		if($json_feed) {
			self::SetDate(date_i18n('d/m/Y H:i'));
			if($return)
				return $json_feed;

			self::SetCache($json_feed);
		} else {
			self::SetDate(date_i18n('d/m/Y H:i') . __(' - Sem Resultados', 'iasd'));
		}
	}

	public static $current_instance = false;

	public static function SetInstance($instance) {
		self::$current_instance = $instance;
	}
	public static function GetInstance($instance = false) {
		if($instance)
			self::SetInstance($instance);
		return self::$current_instance;
	}
	public static function GetInstanceSecret() {
		return self::$current_instance['secret'];
	}
	function getMyCache() {
		return self::GetCache($this->getClassName());
	}
	public static function GetCache($class) {
		$instance = self::GetInstance();

		$currentSite = site_url() . '/';
		$jcache = '[]';
		if($instance['source'] == $currentSite) {
			$jcache = self::UpdateCache($instance, $class);
		} else {
			$jcache = get_option('jsn_fc_'.self::GetInstanceSecret(), '[]');
		}

		return json_decode($jcache);
	}
	public static function SetCache($cache = false) {
		if($cache)
			update_option('jsn_fc_' . self::GetInstanceSecret() , $cache);
	}
	public static function GetDate() {
		return get_option('jsn_up_' . self::GetInstanceSecret() );
	}
	public static function SetDate($date = false) {
		if($date)
			update_option('jsn_up_' . self::GetInstanceSecret() , $date);
	}

	public static function DoCurlRequest($request, $instance) {
		$currentSite = site_url() . '/';
		if($instance['source'] == $currentSite && false) {
			return PAPluginJsonFeedController::ExecuteWidgetQuery($request);
		}

		$request_string = http_build_query($request);

		$url = $instance['source'] . 'wp-admin/admin-ajax.php?action=json_feed&' . $request_string;

		if(isset($_REQUEST['add_new']))
			if($_REQUEST['add_new'] != '')
				return false;
		$response = wp_remote_get($url, array('timeout' => 20));

		if(is_wp_error( $response ))
			return false;

		if($response['response']['code'] != 200)
			return false;

		$body_string = $response['body'];

		$cache = json_decode($body_string);

		if(!$cache) {
			$body_string = substr($body_string, 3);
			$cache = json_decode($body_string);
		}

		return ($cache) ? $body_string : false;
	}

	public function getClassName() {
		return __CLASS__;
	}

	public function getMyItems($instance) {
		self::SetInstance($instance);

		$cache = $this->getMyCache();

		if(isset($instance['current_term']) && $instance['current_term']) {
			global $wp_query;
			$cache_array = (array) $cache;
			$taxonomy = $instance['current_term'];
			$query_vars = $wp_query->query_vars;
			$term = (isset($query_vars[$taxonomy])) ? get_term_by('slug', $query_vars[$taxonomy], $taxonomy) : get_editoria(get_the_ID(), $taxonomy);

			if($term) {
				$term_slug = $term->slug;
				if(!isset($cache_array[$taxonomy]))
					$cache_array[$taxonomy] = array();

				$cache_array[$taxonomy] = (array) $cache_array[$taxonomy];

				if(!isset($cache_array[$taxonomy][$term_slug]))
					$cache_array[$taxonomy][$term_slug] = array('ttl' => 0, 'items' => array());
				else if(is_object($cache_array[$taxonomy][$term_slug]))
					$cache_array[$taxonomy][$term_slug] = (array) $cache_array[$taxonomy][$term_slug];

				if($cache_array[$taxonomy][$term_slug]['items'] == null)
					$cache_array[$taxonomy][$term_slug]['items'] = array();

				$current_time = time();
				if($current_time > $cache_array[$taxonomy][$term_slug]['ttl'] || !count($cache_array[$taxonomy][$term_slug]['items'])) {
					$cache_array[$taxonomy][$term_slug]['ttl'] = $current_time + ( 60 * 60);
					$WidgetClassName = get_class($this);

					$instance['taxonomy'] = array();
					$instance['taxonomy'][$taxonomy] = array($term->term_id);

					$information = call_user_func(array($WidgetClassName, 'DoCronTask'), $instance, $WidgetClassName, true);

					$json_data = json_decode($information);
					$cache_array[$taxonomy][$term_slug]['items'] = $json_data;

					self::SetCache(json_encode($cache_array));
				}
				$cache = $cache_array[$taxonomy][$term_slug]['items'];
			}
		}
		if(!is_array($cache) && !is_object($cache))
			$cache = array();

		return $cache;
	}

	function getDefaultServer() {
		return '';
	}

	function getDefaultTerm() {
		return '';
	}

	function demoWidget($params = array()) {
		$params = array_merge(array('secret' => 'demo_1'.date('Ymd')), $params);

		$instance = $this->update($params, array());

		$this->number = 7777777;

		$this->widget(array(), $instance);
	}

	public static function _DoQuery($wp_query_args, $args) {
		$wp_query_args = apply_filters('PAPluginJsonFeedController::wp_query_args', $wp_query_args, $args);

		$query = new WP_Query($wp_query_args);
		$json_output = array();

		while($query->have_posts()) {
			$query->the_post();
			global $post;
			$post_array = (array) $post;
			if(!$post_array['post_excerpt'])
				$post_array['post_excerpt'] = get_the_excerpt();

			$post_array['meta'] = get_post_meta(get_the_ID());
			$post_array['taxonomy'] = array();
			$post_array['permalink'] = get_permalink();
			if(isset($post_array['meta']['_video_thumbnail']))
				$post_array['thumbnail'] = $post_array['meta']['_video_thumbnail'][0];
			else
				$post_array['thumbnail'] = wp_get_attachment_url( get_post_thumbnail_id(get_the_ID()));

			$taxonomies = get_post_taxonomies(get_the_ID());
			foreach($taxonomies as $taxonomy) {
				$post_array['taxonomy'][$taxonomy] = wp_get_post_terms(get_the_ID(), $taxonomy);
			}
			if(isset($args['include_author_information'])) {
				$author = get_userdata($post->post_author );
				if($author) {
					$post_array['author'] = (array) get_userdata($post->post_author )->data;
					$post_array['author']['nome_da_coluna'] = get_user_meta($post->post_author, 'nome_da_coluna', true);
					$post_array['author']['descricao_da_coluna'] = get_user_meta($post->post_author, 'descricao_da_coluna', true);
					$avatar_id = get_user_meta($post->post_author, 'wp_user_avatar', true);
					if($avatar_id)
						$post_array['author']['avatar'] = wp_get_attachment_url( $avatar_id );
				}
			}
			$post_array = apply_filters('PAPluginJsonFeedController::post_array', $post_array);
			$json_output[] = $post_array;
		}
		wp_reset_query();
		$json_output = apply_filters('PAPluginJsonFeedController::json_output', $json_output);

		return $json_output;
	}

	public static function ExecuteWidgetQuery($args = array()) {
		$wp_query_args = PAPluginJsonFeedController::BuildQuery($args);
		$json_output = false;

		if(isset($args['group_of_children'])) {
			$taxonomy = $args['group_of_children']['taxonomy'];

			foreach($wp_query_args['tax_query'] as $k => $tax_query) {
				if($tax_query['taxonomy'] == $taxonomy)
					$wp_query_args['tax_query'][$k] = false;
			}
			$wp_query_args['tax_query'] = array_filter($wp_query_args['tax_query']);
			$base_wp_query_args = apply_filters('PAPluginJsonFeedController::wp_query_args::group_of_children', $wp_query_args, $args);

			$terms = $args['group_of_children']['terms'];
			foreach($terms as $k => $term_slug) {
				$term = get_term_by('slug', $term_slug, $taxonomy);
				$terms[$k] = ($term->parent == 0) ? $term : false;
			}
			$terms = array_filter($terms);

			$json_output = array();
			foreach($terms as $parent) {
				$child_terms = get_terms($taxonomy, array('hide_empty' => true, 'parent' => $parent->term_id));
				$wp_query_args = $base_wp_query_args;
				$wp_query_args['tax_query'][] = array('taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => $parent->slug);
				$json_output[$parent->slug] = PAPluginJsonFeedController::_DoQuery($wp_query_args, $args);
				foreach ($child_terms as $key => $child_term) {
					$wp_query_args = $base_wp_query_args;
					$wp_query_args['tax_query'][] = array('taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => $child_term->slug);
					$json_output[$child_term->slug] = PAPluginJsonFeedController::_DoQuery($wp_query_args, $args);
				}
			}
		} else {
			$json_output = PAPluginJsonFeedController::_DoQuery($wp_query_args, $args);
		}
		return json_encode($json_output);
	}

	public static function BuildQuery($args = array(), $defaultsFilter = 'PAPluginJsonFeedController::wp_query_args-default') {
		$wp_query_args = array(
			'limit' => 5,
			'posts_per_page' => 5,
			'orderby' => 'date',
			'order' => 'DESC',
			'tax_query' => array(),
			'post_status' => 'publish',
			'meta_query' => array(),
			'post_type' => 'post',
		);

		$json_output = null;
		$wp_query_args = apply_filters($defaultsFilter, $wp_query_args, $args);

		foreach($wp_query_args as $k => $v) {
			if(isset($args[$k])) {
				if($args[$k]) {
					if(is_array($v)) { // validação para tax_query e meta_query
						if(is_array($args[$k]))
							if(count($args[$k]))
								$wp_query_args[$k] = $args[$k];
					} else { //demais parametros
						$wp_query_args[$k] = $args[$k];
					}
				}
				unset($args[$k]);
			}
		}

		$sedes_filter_count = 0;
		foreach($wp_query_args['tax_query'] as $k => $tax_query) {
			if($tax_query['taxonomy'] == IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS) {
				$sedes_filter_count++;
				$wp_query_args['tax_query'][$k]['include_children'] = false;
			}
		}

		if(!$sedes_filter_count) {
			$wp_query_args['tax_query'][] = array('taxonomy' => IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS, 'field' => 'slug', 'terms' => array('dsa'), 'include_children' => false);
		}

		$taxonomies = get_taxonomies();
		foreach($taxonomies as $taxonomy) {
			if(isset($args[$taxonomy])) {
				$terms = explode(',', $args[$taxonomy]);
				$wp_query_args['tax_query'][] = array('taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => $terms);
				unset($args[$taxonomy]);
			}
		}
		$wp_query_args['posts_per_page'] = $wp_query_args['limit'];

		return $wp_query_args;
	}

	public static function WidgetBehaviours($wp_query_args, $args) {
		if(isset($args['widget_class'])) {
			switch ($args['widget_class']) {
				case 'ColunasArtigosWidget': {
					$author_list = get_users('role=colunista');
					$post_ids = array();
					foreach ($author_list as $author) {
						$author_query = new WP_Query (array('author_name' => $author->user_nicename, 'posts_per_page' => 1, 'post_type' => PAColunas::$post_type_name));

						if (!$author_query->have_posts())
							continue;
						$author_query->the_post();

						$post_ids[] = get_the_ID();
					}
					$wp_query_args['post__in'] = $post_ids;
				}
			}
		}

		return $wp_query_args;
	}
}



if(!class_exists('Widget_Category_Checklist')) {
	class Widget_Category_Checklist extends Walker {
		var $tree_type = 'category';
		var $field_name = 'category';
		var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this

		function start_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat("\t", $depth);
			$output .= "$indent<ul class='children'>\n";
		}

		function end_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat("\t", $depth);
			$output .= "$indent</ul>\n";
		}

		function start_el( &$output, $category, $depth, $args, $id = 0 ) {
			extract($args);
			if ( empty($taxonomy) )
				$taxonomy = 'category';

			if ( $taxonomy == 'category' )
				$name = 'post_category';
			else
				$name = 'tax_input['.$taxonomy.']';

			if($this->field_name)
				$name = $this->field_name;

			$class = in_array( $category->term_id, $popular_cats ) ? ' class="popular-category"' : '';
			$output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" . '<label class="selectit"><input value="' . $category->term_id . '" type="checkbox" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '" ' . checked( in_array( $category->term_id, $selected_cats ), true, false ) . disabled( empty( $args['disabled'] ), false, false ) . ' /> ' . esc_html( apply_filters('the_category', $category->name )) . '</label>';
		}

		function end_el( &$output, $category, $depth = 0, $args = array() ) {
			$output .= "</li>\n";
		}

		function Widget_Category_Checklist($field_name) {
			$this->field_name = $field_name;
		}
	}
}

class PAPluginJsonFeedNoticias extends PAPluginJsonFeedController {

	function getDefaultServer() {
		return 'http://noticias.adventistas.org/' . substr(WPLANG, 0, 2) . '/';
	}

}


add_action('init', array('PAPluginJsonFeedController', 'Init'));
add_filter('PAPluginJsonFeedController::wp_query_args-default', array('PAPluginJsonFeedController', 'WidgetBehaviours'), 10, 2);

