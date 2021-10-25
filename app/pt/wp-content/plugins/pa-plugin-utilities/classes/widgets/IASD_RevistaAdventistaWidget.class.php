<?php

class IASD_RevistaAdventistaWidget extends WP_Widget
{

	static function init(){
		register_widget( __CLASS__ );
	}

	function __construct(){
		$widget_ops = array(
			'classname' => __CLASS__, 
			'description' => __( 'Lista os posts que representa as capa das edições da revista', 'iasd' ),
		);
		parent::__construct( __CLASS__, __( 'IASD: Revista', 'iasd' ), $widget_ops );
	}

	function form( $instance ){
		$instance = wp_parse_args( $instance, array() );
		//nothing to do here
	}

	function update( $new_instance, $old_instance ){
		return $new_instance;
	}

	function widget( $args, $instance ){
		require PAPU_VIEW.'/revista_adventista/widget-revista-adventista.php';
	}

}

add_action( 'widgets_init', array('IASD_RevistaAdventistaWidget', 'init') );