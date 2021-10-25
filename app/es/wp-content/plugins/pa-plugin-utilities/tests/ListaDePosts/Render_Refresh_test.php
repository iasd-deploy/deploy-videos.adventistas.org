<?php

require_once '_Widget_test.php';

class IASD_ListaDePosts_Render_Refresh_test extends IASD_ListaDePosts__Widget_test {

	function testIfRenderAndRefreshAreEqual() {
		$sidebarToUse = 'styleguide-aside';

		$widget = new IASD_ListaDePosts();
		$widget->number = $this->getWidgetNumber();
		$instance = IASD_ListaDePosts::DefaultInstance();
		$instance['width'] = 'col-md-4';
		$instance['view'] = 'COL-MD-4';
		$instance['number'] = $widget->number;

		update_option(IASD_ListaDePosts::option_id, array($widget->number => $instance));

		$widget->_setInstance($instance);

		global $_wp_sidebars_widgets;
		$_wp_sidebars_widgets[$sidebarToUse] = array($widget->slug());

		ob_start();
		$widget->widget(array(), $instance);
		$method_widget = ob_get_contents();
		ob_end_clean();

		$_REQUEST = array();
		$_REQUEST['action'] = 'listadeposts-refresh';
		$_REQUEST['widget'] = $widget->slug();


		ob_start();
		do_action('wp_ajax_nopriv_iasd-listadeposts-refresh');
		$action_nopriv = ob_get_contents();
		ob_end_clean();

		ob_start();
		do_action('wp_ajax_iasd-listadeposts-refresh');
		$action_priv = ob_get_contents();
		ob_end_clean();

		ob_start();
		IASD_ListaDePosts::Refresh();
		$method_refresh = ob_get_contents();
		ob_end_clean();

		$this->assertEquals($method_widget, $action_nopriv);
		$this->assertEquals($action_nopriv, $action_priv);
		$this->assertEquals($action_priv, $method_refresh);
	}

	function testIfPreviewIsCalledProperly() {
		$sidebarToUse = 'styleguide-aside';

		$widget = new IASD_ListaDePosts();
		$number = $widget->number = $this->getWidgetNumber();
		$instance = IASD_ListaDePosts::DefaultInstance();
		$instance['width'] = 'col-md-4';
		$instance['view'] = 'COL-MD-4';
		$instance['number'] = $widget->number;

		update_option(IASD_ListaDePosts::option_id, array($widget->number => $instance));

		$widget->_setInstance($instance);

		global $_wp_sidebars_widgets;
		$_wp_sidebars_widgets[$sidebarToUse] = array($widget->slug());

		ob_start();
		$widget->widget(array(), $instance);
		$method_widget = ob_get_contents();
		ob_end_clean();
/*
		$_REQUEST = array();
		$_REQUEST['preview'] = 1;
		$_REQUEST[IASD_ListaDePosts::form_id] = array($number => $instance);
		$_REQUEST['action'] = 'listadeposts-refresh';

		ob_start();
		IASD_ListaDePosts::Refresh();
		$method_refresh = ob_get_contents();
		ob_end_clean();

		$this->assertContains($method_widget, $method_refresh);*/
	}
}