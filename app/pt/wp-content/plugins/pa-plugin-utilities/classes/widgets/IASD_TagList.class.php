<?php

add_action('widgets_init', array('IASD_TagList', 'RegisterWidget'));

class IASD_TagList extends WP_Widget {
	const base_id = 'iasd_taglist';
	function __construct() {
		$widget_ops = array('classname' => __CLASS__, 'description' => __('Apresenta uma lista das tags, taxonomias e categorias.', 'iasd') );
		parent::__construct(self::base_id, __('IASD: Lista de Tags', 'iasd', $widget_ops));

		$tag_list = get_terms(get_taxonomies(array('public' => true)));
	}
	static function RegisterWidget() {
		register_widget(__CLASS__);
	}
	static function UnregisterWidget() {
		unregister_widget(__CLASS__);
	}
	function update($new_instance, $old_instance)
	{
		if(!count($new_instance))
			$new_instance = $old_instance;

		return $new_instance;
	}
	function demoWidget() {
		$this->widget(array(), array());
	}
	function widget(){
		$terms = get_terms(get_taxonomies(array('public' => true)));
		?>
		<h1>Marcadores:</h1>
		<nav class="main iasd-tags">
		<?php foreach($terms as $term) :
		?>

			<a href="<?php echo get_term_link($term); ?>" title="<?php echo $term->name; ?>"><?php echo $term->name; ?><span class="badge"><?php echo $term->count; ?></span></a>

		<?php endforeach; ?>
		</nav>
		<?php
	}
}
