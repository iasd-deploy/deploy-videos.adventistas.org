<?php

add_filter( 'pre_option_link_manager_enabled', '__return_true' );
add_action('widgets_init', array('IASD_SliderServicos', 'Init'));

class IASD_SliderServicos extends WP_Widget
{
	function __construct()
	{
		$widget_ops = array('classname' => __CLASS__, 'description' => __('Lista serviços de acordo com os links cadastrados no site', 'iasd') );
		parent::__construct(__CLASS__, __('IASD: Slider de Serviços', 'iasd'), $widget_ops);
	}

	function form($instance = array())
	{
		//Defaults
		$instance = wp_parse_args($instance, array( 'title' => '', 'category' => false, 'limit' => -1, 'width' => 'col-md-4') );
		$title = strip_tags($instance['title']);
		$link_cats = get_terms( 'link_category' );
		if ( ! $limit = intval( $instance['limit'] ) )
			$limit = -1;
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'iasd'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('category'); ?>"><?php _e( 'Selecione uma categoria:', 'iasd'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>">
			<option value=""><?php _ex('All Links', 'links widget'); ?></option>
			<?php
			foreach ( $link_cats as $link_cat ) {
				echo '<option value="' . intval( $link_cat->term_id ) . '"'
					. selected( $instance['category'], $link_cat->term_id, false )
					. '>' . $link_cat->name . "</option>\n";
			}
			?>
			</select>

			<div>
				<label for="<?php echo $this->get_field_id('width'); ?>"><?php _e( 'Disposição:', 'iasd'); ?></label>
				<select name="<?php echo $this->get_field_name('width'); ?>" id="<?php echo $this->get_field_id('orderby'); ?>" class="widefat">
					<option value="col-md-4"<?php selected( $instance['width'], 'col-md-4' ); ?>><?php _e( '1/3 de coluna', 'iasd'); ?></option>
					<option value="col-md-8"<?php selected( $instance['width'], 'col-md-8' ); ?>><?php _e( '2/3 de coluna', 'iasd'); ?></option>
					<option value="col-md-12"<?php selected( $instance['width'], 'col-md-12' ); ?>><?php _e( 'Largura total', 'iasd' ); ?></option>
				</select>
			</div>
		</p>
<?php
	}

	function widgetWidthClass() {
		return $this->getInstanceData('width');
	}

	function _setInstance($instance) {
		$this->instance = $instance;
	}

	function getInstance() {
		return $this->instance;
	}

	function setInstance($instance) {
		$this->instance = $instance;
	}

	function getInstanceData($param) {
		if(is_array($this->instance)) {
			if(isset($this->instance[$param])) {
				return $this->instance[$param];
			}
		}

		return '';
	}

	function getWidgetTitle() {
		$title = apply_filters( 'widget_title', $this->getInstanceData('title'), $this->getInstance(), $this->id_base );
		if($title)
			return $title;
		return '';
	}

	static function Init() {
		register_widget(__CLASS__);
	}
	function update($new_instance, $old_instance)
	{
		$instance['title'] = strip_tags($new_instance['title']);
		if(!count($new_instance))
			$new_instance = $old_instance;

		return $new_instance;
	}

	function demoWidgetItem($id) {
		$object = new stdClass();
		$object->link_id = $id;
		$object->link_url = 'http://adventistas.org/#'.$id;
		$object->link_name = 'Adventistas.ORG #'.$id;
		$object->link_image = '';
		$object->link_target = '';
		$object->link_category = '';
		$object->link_description = '';
		$object->link_visible = '';
		$object->link_owner = '';
		$object->link_rating = '';
		$object->link_updated = '';
		$object->link_rel = '';
		$object->link_notes = '';
		$object->link_rss = '';
		return $object;
	}

	function demoWidget() {
		$demo = array();

		for($i = 1; $i <= 10; $i++)
			$demo[] = sanitize_bookmark($this->demoWidgetItem($i));
		$this->widget(array('bookmarks' => $demo), array());
	}
	function widget($args, $instance)
	{
		$this->setInstance($instance);

		if(isset($instance['limit']))
			if(!$instance['limit'])
				$instance['limit'] = -1;
		$bookmarks = get_bookmarks($instance);
		if(!count($bookmarks) || true) {
			if(isset($args['bookmarks'])) {
				$bookmarks = $args['bookmarks'];
			}
		}
?>

<div class="iasd-widget iasd-widget-slider <?php if($instance['width'] == 'col-md-12'){ echo 'col-md-12'; }elseif($instance['width'] == 'col-md-8'){ echo 'col-md-8'; }else{ echo 'col-md-4';} ?>">
	<?php if($title = $this->getWidgetTitle()){ echo '<h1>',$title,'</h1>'; }else{ echo '<h1>',__('Serviços', 'iasd'),'</h1>'; } ?>
	<div class="owl-carousel services <?php if($instance['width'] == 'col-md-12'){ echo 'full'; }elseif($instance['width'] == 'col-md-8'){ echo 'large'; }else{ echo 'small'; }; ?>">
			<?php
				echo _walk_bookmarks($bookmarks, array('before' => '<div class="slider-item">', 'after' => '</div>', 'show_images' => false));
				if(!$bookmarks || !count($bookmarks))
					echo '';
			?>
	</div>
	<div class="alert alert-danger">
		<strong>Atenção!</strong> O tamanho do Widget selecionado é incompatível com esta Sidebar. Por favor, reveja suas configurações.
	</div>
</div>

<?php
	}
}




