<?php


class IASD_Sidebar {
	public static function TemplateInclude($template) {
		return (!is_search()) ? PAPU_VIEW . DIRECTORY_SEPARATOR . 'styleguide_testing.php' : $template;
	}
	
	public static function RegisterTemplateInclude() {
		add_filter( 'template_include', array('IASD_Sidebar', 'TemplateInclude'), 999);
	}

	public static function DefaultSidebar() {
		return array(
			'name'          => 'Sidebar name',
			'id'            => 'unique-sidebar-id',
			'description'   => '',
			'class'         => '',
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '<h1>',
			'after_title'   => '</h1>',
			'col_class'     => 'col-md-12');
	}

	public static function RegisterSidebar($config){
		$config = array_merge(self::DefaultSidebar(), $config);

/*		if($config['col_class'] == 'col-md-4') {
			$config['before_widget'] = '<div class="iasd-widget col-md-12">';
		} else if($config['col_class'] == 'col-md-8') {
			$config['before_widget'] = '<div class="iasd-widget col-md-6">';
		} else {
			$config['before_widget'] = '<div class="iasd-widget col-md-4">';
		}*/

		register_sidebar($config);
	}

	public static function RegisterFakes() {
		self::RegisterSidebar(array('name' => 'Styleguide Article', 'id' => 'styleguide-article', 'col_class' => 'col-md-8'));
		self::RegisterSidebar(array('name' => 'Styleguide Aside',   'id' => 'styleguide-aside',   'col_class' => 'col-md-4'));
		self::RegisterSidebar(array('name' => 'Styleguide Banner',  'id' => 'styleguide-banner'));
	}

	public static function Render($index = 1){
		global $wp_registered_sidebars, $wp_registered_widgets;

		if ( is_int($index) ) {
			$index = "sidebar-$index";
		} else {
			$index = sanitize_title($index);
			foreach ( (array) $wp_registered_sidebars as $key => $value ) {
				if ( sanitize_title($value['name']) == $index ) {
					$index = $key;
					break;
				}
			}
		}

		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( empty( $wp_registered_sidebars[ $index ] ) || empty( $sidebars_widgets[ $index ] ) || ! is_array( $sidebars_widgets[ $index ] ) ) {
			return false;
		}

		$sidebar = $wp_registered_sidebars[$index];

		$did_one = false;
		$widgets = array();

		$sidebar_cols = 0;
		foreach ( (array) $sidebars_widgets[$index] as $widget_id ) {
			if ( !isset($wp_registered_widgets[$widget_id]) ) continue;

			$widget = $wp_registered_widgets[$widget_id];
			$widget_number = explode('-', $widget_id);
			$widget_number = end($widget_number);

			$instances = $widget['callback'][0]->get_settings();
			$instance = isset($instances[$widget_number])
						? $instances[$widget_number]
						: array();

			$rowing_callback = $config_callback = $widget['callback'];
			$config_callback[1] = '_setInstance';
			$rowing_callback[1] = 'widgetWidthClass';

			$params = array_merge(
				array( array_merge( $sidebar, array('widget_id' => $widget_id, 'widget_name' => $widget['name']) ) ),
				(array) $widget['params']
			);

			if(is_callable($config_callback))
				$widget['callback'][0]->_setInstance($instance);
			$widget['callback'][0]->_set($widget_number);

			// Substitute HTML id and class attributes into before_widget
			$classname_ = '';

			foreach ( (array) $widget['classname'] as $cn ) {
				if ( is_string($cn) )
					$classname_ .= '_' . $cn;
				elseif ( is_object($cn) )
					$classname_ .= '_' . get_class($cn);
			}
			$classname_ = ltrim($classname_, '_');
			$params[0]['before_widget'] = sprintf($params[0]['before_widget'], $widget_id, $classname_);

			$params = apply_filters( 'dynamic_sidebar_params', $params );

			$display_callback = $widget['callback'];

			do_action( 'dynamic_sidebar', $widget);
	
			if(isset($sidebar['col_class']) && $sidebar['col_class'] != 'col-md-4') {
				$widget_width = 0;
				if ( is_callable($rowing_callback) ) {
					$class = call_user_func_array($rowing_callback, array());
					$widget_width = explode('-', $class);
					$widget_width = end($widget_width);
				} else {
					if($sidebar['col_class'] == 'col-md-4') {
						$widget_width = 12;
					} else if($sidebar['col_class'] == 'col-md-12') {
						$widget_width = 4;
					} else {
						$widget_width = 6;
					}
				}

				$sidebar_cols_sum = $sidebar_cols + $widget_width;
				if(($sidebar_cols >= 12) || ($sidebar_cols_sum > 12)) {
					echo "\n</div>\n<!-- Auto Break -->\n<div class='row'>\n";
					$sidebar_cols = 0;
				}
				$sidebar_cols += $widget_width;
			}

			if ( is_callable($display_callback) ) {
				call_user_func_array($display_callback, $params);
				$did_one = true;
			}
		}

		return $did_one;
	}
}



if(DEFINED('WP_DEBUG') && WP_DEBUG) {
	if(isset($_GET['fake_sidebars']))
		IASD_Sidebar::RegisterTemplateInclude();
//	IASD_Sidebar::RegisterFakes();
}

add_action('iasd_dynamic_sidebar', array('IASD_Sidebar', 'Render'));
add_action('iasd_register_sidebar', array('IASD_Sidebar', 'RegisterSidebar'));

function iasd_dynamic_sidebar($index = 1) {
	return IASD_Sidebar::Render($index);
}



