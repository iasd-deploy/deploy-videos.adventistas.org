<?php


add_action( 'widgets_init', array('IASD_MonthlyArchives', 'Init'));

class IASD_MonthlyArchives extends WP_Widget {

	static function Init() {
		register_widget(__CLASS__);
	}

	function __construct() {
		$widget_ops = array('classname' => __CLASS__, 'description' => __('Widget de arquivos mensais da IASD'));
		parent::__construct(__CLASS__, __('IASD: Arquivos mensais', 'iasd'), $widget_ops);
	}

	function getWidgetTitle() {
		$title = apply_filters( 'widget_title', $this->getInstanceData('title'), $this->getInstance(), $this->id_base );
		if($title)
			return $title;
		return '';
	}

	// function getWidgetContent() {
	// 	$content = '<div class="textwidget">';
	// 	$content .= apply_filters( __CLASS__, $this->getInstanceData('text'), $this->getInstance() );
	// 	$content .= '</div>';

	// 	return $content;
	// }

	function subWidget($args, $instance) {
?>
	<div class="iasd-widget iasd-widget-list <?php echo $this->getRenderedWidth(); ?>">
		<?php if($title = $this->getWidgetTitle()) echo '<h1>',$title,'</h1>'; ?>
			<ul>
				<?php 
				$args = array(
					'type'            => 'monthly',
					'limit'           => '',
					'order'           => 'DESC'
				);
				wp_get_archives( $args ); 
				?> 
			</ul>
		<div class="alert alert-danger">
			<strong>Atenção!</strong> O tamanho do Widget selecionado é incompatível com esta Sidebar. Por favor, reveja suas configurações.
		</div>
	</div>
<?php

	}

	function subUpdate( $new_instance, $old_instance ) {
		$instance = $old_instance;
        if(isset($new_instance['sidebar']))
            $instance['sidebar'] = $new_instance['sidebar'];
        if(isset($new_instance['width']))
            $instance['width'] = $new_instance['width'];
		$instance['title'] = strip_tags($new_instance['title']);

		$instance['filter'] = isset($new_instance['filter']);

		return $instance;
	}

	function subForm( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'sidebar' => false, 'width' => 'col-md-4' ) );

		$title = strip_tags($instance['title']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
<?php
        $this->renderWidthOptions();
	}
}
