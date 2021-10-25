<?php


add_action( 'widgets_init', array('IASD_Text', 'Init'));

class IASD_Text extends WP_Widget {

	static function Init() {
		register_widget(__CLASS__);
	}

	function __construct() {

		$widget_ops = array('classname' => __CLASS__, 'description' => __('Widget de texto (ou html) da IASD'));
		parent::__construct(__CLASS__, __('IASD: Texto', 'iasd'), $widget_ops);
	}

	function widget($args, $instance) {
?>
	<div class="iasd-widget iasd-widget-text_title <?php if($instance['width'] == 'col-md-12'){ echo 'col-md-12'; }else{ if($instance['width'] == 'col-md-8'){ echo 'col-md-8'; } else { echo 'col-md-4'; }}; ?>">
		<div>
			<?php if($title = $instance['title']) echo '<h1>',$title,'</h1>'; ?>
			<?php echo $instance['text']; ?>
		</div>
		<div class="alert alert-danger">
			<strong>Atenção!</strong> O tamanho do Widget selecionado é incompatível com esta Sidebar. Por favor, reveja suas configurações.
		</div>
	</div>
<?php

	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
        if(isset($new_instance['sidebar']))
            $instance['sidebar'] = $new_instance['sidebar'];
        if(isset($new_instance['width']))
            $instance['width'] = $new_instance['width'];
		$instance['title'] = strip_tags($new_instance['title']);
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		$instance['filter'] = isset($new_instance['filter']);

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( $instance, array( 'title' => '', 'text' => '', 'sidebar' => false, 'width' => 'col-md-4' ) );

		$title = strip_tags($instance['title']);
		$text = esc_textarea($instance['text']);
?>	
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<p><textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea></p>

		<div>
			<label for="<?php echo $this->get_field_id('width'); ?>"><?php _e( 'Disposição:' ); ?></label>
			<select name="<?php echo $this->get_field_name('width'); ?>" id="<?php echo $this->get_field_id('orderby'); ?>" class="widefat">
				<option value="col-md-4"<?php selected( $instance['width'], 'col-md-4' ); ?>><?php _e( '1/3 de coluna' ); ?></option>
				<option value="col-md-8"<?php selected( $instance['width'], 'col-md-8' ); ?>><?php _e( '2/3 de coluna' ); ?></option>
				<option value="col-md-12"<?php selected( $instance['width'], 'col-md-12' ); ?>><?php _e( 'Largura total' ); ?></option>
			</select>
		</div>
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

}
