<?php

add_action( 'widgets_init', array('JSONScrollerWidget', 'Init'));

class JSONScrollerWidget extends PAPluginJsonFeedController
{
	function __construct()
	{
		$this->with_current_term = true;
		$widget_ops = array('classname' => __CLASS__, 'description' => __('Widget de scroller de links extraidos de um Json Feed. Requer o plugin <b>Fluid Video Embeds</b> e procura o link na meta <b>dp_video_url</b>', 'iasd') );
		parent::__construct(__CLASS__, __('Json Client: Scroller', 'iasd'), $widget_ops);
	}

	function form($instance)
	{
		$instance = array_merge(array('thumbnail', '', 'secret' => '', 'limit' => '3', 'source' => '', 'title' => '', 'link' => '', 'current_term' => '', 'taxonomy' => array()),  (array) $instance);
?>
		<div>
			<p class="pa_widget_multi_content">
<?php
		$this->Form_Title($instance);
		$this->Form_Limit($instance);
		$this->Form_Source($instance);
		$this->Form_Link($instance);
		$this->Form_Taxonomies($instance);
		$this->Form_Secret($instance);
?>
			</p>
		</div>
<?php
	}

	function getYouTubeIdFromURL($url)
	{
	  $url_string = parse_url($url, PHP_URL_QUERY);
	  parse_str($url_string, $args);
	  $ret_var = isset($args['v']) ? $args['v'] : false;

	  if (!$ret_var){
	  	if (strpos($url, 'youtu.be') !== false){
	  		$ret_var = str_replace('http://youtu.be/' , '', $url);
	  	} else {
	  		$ret_var = false;
	  	}
	  }
	  return $ret_var;


	}

	function widget($args, $instance) {
		global $post;
		$json_cache = $this->getMyItems($instance);

		if(count($json_cache)):

?>
			<div class="iasd-widget row-fluid iasd-widget-json_scroller">
				<div class="span12">
					<h1><?php echo (isset($instance['title']) && $instance['title']) ? $instance['title'] : __('Videos Relacionados', 'iasd'); ?></h1>
					<div class="iasd-widget-carousel" data-ideal-item-width="160">
						<div class="iasd-widget-carousel_mask">
							<ul class="unstyled iasd-widget-services">
<?php
							foreach($json_cache as $count => $post):
								$base_thumbnail = $this->thumbnailSize($post->thumbnail, '160x90');
?>
								<li>
									<a href="<?php echo $post->permalink; ?>" target="_blank">
										<img data="<?php $base_thumbnail; ?>" src="<?php echo $base_thumbnail; ?>" />
										<h2><?php echo apply_filters('trim', $post->post_title, 50); ?></h2>
									</a>
								</li>
							<?php endforeach; ?>
							</ul>
						</div>
						<a class="iasd-widget-carousel_control left" href="javascript:void(0)" data-slide="prev">‹</a>
						<a class="iasd-widget-carousel_control right" href="javascript:void(0)" data-slide="next">›</a>
					</div>
				</div>
			</div>

<?php

		endif;

	}

	function update($new_instance, $old_instance)
	{

		$limit = (int) $new_instance['limit'];
		if($limit < 3){
			$new_instance['limit'] = '3';
		}elseif ($limit > 10) {
			$new_instance['limit'] = '10';
		}

		if($new_instance['source'] && !$new_instance['current_term']) {
			call_user_func(array($WidgetClassName, 'DoCronTask'), $new_instance, $WidgetClassName);
		}

		return parent::update($new_instance, $old_instance);
	}

	public function getClassName() {
		return __CLASS__;
	}

	public static function Init() {
		self::RegisterWidget(__CLASS__);
	}
	function getDefaultServer() {
		return 'http://videos.adventistas.org/' . substr(WPLANG, 0, 2) . '/';
	}
}
