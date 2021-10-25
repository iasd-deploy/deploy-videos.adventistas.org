<?php


add_action( 'widgets_init', array('IASD_TwitterWidget', 'Init'));

class IASD_TwitterWidget extends WP_Widget {
	private $instance = array();

	static function Init() {
		register_widget(__CLASS__);
	}

	function __construct() {
		$widget_ops = array('classname' => __CLASS__, 'description' => __('Widget da IASD onde se insere o plug-in social do twitter.'));
		parent::__construct('iasd_twitter', __('IASD: Twitter'), $widget_ops);
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

	function getWidgetContent() {
		$content = '<div class="textwidget">';
		$content .= apply_filters( __CLASS__, $this->getInstanceData('text'), $this->getInstance() );
		$content .= '</div>';

		return $content;
	}

	function widget( $args, $instance ) {
		$this->setInstance($instance);

		$this->renderWidget($args, $instance);
	}

	function renderWidget($args, $instance) {
?>
	<div class="iasd-widget iasd-widget-social_media twitter col-md-4">
		<h1><?php _e('Twitter', 'iasd'); ?></h1>
		<div class="widget-box">
			<?php echo $this->getWidgetContent(); ?>
		</div>
		<div class="alert alert-danger">
			<strong>Atenção!</strong> O tamanho do Widget selecionado é incompatível com esta Sidebar. Por favor, reveja suas configurações.
		</div>
	</div>
<?php

	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		$instance['filter'] = isset($new_instance['filter']);
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'text' => '' ) );
		$text = esc_textarea($instance['text']);
?>

		<p><label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Insira abaixo o código fornecido pelo twitter.'); ?></label>
		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea></p>
<?php
	}
}