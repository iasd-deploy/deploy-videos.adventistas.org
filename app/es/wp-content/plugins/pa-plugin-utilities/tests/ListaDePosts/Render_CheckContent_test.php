<?php

require_once '_Widget_test.php';

class IASD_ListaDePosts_Render_CheckContent_test extends IASD_ListaDePosts__Widget_test {

	function hasHooksSet() {
		$this->assertEquals(10, has_action('wp_ajax_iasd-listadeposts-check-contents', array('IASD_ListaDePosts','CheckContentsAjax')));
		$this->assertEquals(10, has_action('sidebar_admin_page',                       array('IASD_ListaDePosts','CheckContentsHtml')));
	}

	function testContentWillBeRendered() {
		$this->addFakePosts();
		$sidebarToUse = 'styleguide-aside';

		$widget = new IASD_ListaDePosts();
		$widget->number = $this->getWidgetNumber();
		$instance = IASD_ListaDePosts::DefaultInstance();
		$instance['width'] = 'col-md-4';
		$instance['view'] = 'COL-MD-4';
		$instance['number'] = $widget->number;
		$instance['title'] = 'lista fake_post_a';
		$instance['post_type'] = 'fake_post_a';
		$instance['taxonomy_query'] = array();

		$widget->_setInstance($instance);
		ob_start();
		$widget->checkContents();
		$rendered = ob_get_contents();
		ob_end_clean();

		$this->assertGreaterThan(0, substr_count($rendered, '<li>'));
		$this->assertContains('lista fake_post_a', $rendered);
	}

	function testContentWillNotBeRenderedWithActionParamsMissing() {
		$this->addFakePosts();
		$sidebarToUse = 'styleguide-aside';

		$widget = new IASD_ListaDePosts();
		$widget->number = $this->getWidgetNumber();
		$instance = IASD_ListaDePosts::DefaultInstance();
		$instance['width'] = 'col-md-4';
		$instance['view'] = 'COL-MD-4';
		$instance['number'] = $widget->number;
		$instance['title'] = 'lista fake_post_a';
		$instance['post_type'] = 'fake_post_a';
		$instance['taxonomy_query'] = array();
		update_option(IASD_ListaDePosts::option_id, array($widget->number => $instance));

		$widget->_setInstance($instance);
		ob_start();
		do_action('wp_ajax_iasd-listadeposts-check-contents');
		$rendered = ob_get_contents();
		ob_end_clean();

		$this->assertEquals(0, substr_count($rendered, '<li>'));
	}

	function testContentWillBeRenderedWithAction() {
		$this->addFakePosts();
		$sidebarToUse = 'styleguide-aside';

		$widget = new IASD_ListaDePosts();
		$number = $widget->number = $this->getWidgetNumber();
		$instance = IASD_ListaDePosts::DefaultInstance();
		$instance['width'] = 'col-md-4';
		$instance['view'] = 'COL-MD-4';
		$instance['number'] = $widget->number;
		$instance['title'] = 'lista fake_post_a';
		$instance['post_type'] = 'fake_post_a';
		$instance['taxonomy_query'] = array();
		update_option(IASD_ListaDePosts::option_id, array($widget->number => $instance));

		$widget->_setInstance($instance);

		$_GET = $_REQUEST = array('widget_number' => $number, 'multi_number' => '', IASD_ListaDePosts::form_id => array($number => $instance));
		ob_start();
		do_action('wp_ajax_iasd-listadeposts-check-contents');
		$rendered = ob_get_contents();
		ob_end_clean();

		$this->assertGreaterThan(0, substr_count($rendered, '<li>'));
	}

	function testContentWillBeRenderedWithActionAndMultiNumber() {
		$this->addFakePosts();
		$sidebarToUse = 'styleguide-aside';

		$widget = new IASD_ListaDePosts();
		$number = $widget->number = $this->getWidgetNumber();
		$instance = IASD_ListaDePosts::DefaultInstance();
		$instance['width'] = 'col-md-4';
		$instance['view'] = 'COL-MD-4';
		$instance['number'] = '';
		$instance['title'] = 'lista fake_post_a';
		$instance['post_type'] = 'fake_post_a';
		$instance['taxonomy_query'] = array();
		update_option(IASD_ListaDePosts::option_id, array($widget->number => $instance));

		$widget->_setInstance($instance);

		$_GET = $_REQUEST = array('widget_number' => 0, 'multi_number' => $number, IASD_ListaDePosts::form_id => array($number => $instance));
		ob_start();
		do_action('wp_ajax_iasd-listadeposts-check-contents');
		$rendered = ob_get_contents();
		ob_end_clean();

		$this->assertGreaterThan(0, substr_count($rendered, '<li>'));
	}

	function testContentWillNotBeRenderedIfNoPriv() {
		$this->addFakePosts();
		$sidebarToUse = 'styleguide-aside';

		$widget = new IASD_ListaDePosts();
		$number = $widget->number = $this->getWidgetNumber();
		$instance = IASD_ListaDePosts::DefaultInstance();
		$instance['width'] = 'col-md-4';
		$instance['view'] = 'COL-MD-4';
		$instance['number'] = $widget->number;
		$instance['multi_number'] = '';
		$instance['title'] = 'lista fake_post_a';
		$instance['post_type'] = 'fake_post_a';
		$instance['taxonomy_query'] = array();
		update_option(IASD_ListaDePosts::option_id, array($widget->number => $instance));

		$widget->_setInstance($instance);

		$_GET = $_REQUEST = array('widget_number' => $number, 'multi_number' => '', IASD_ListaDePosts::form_id => array($number => $instance));
		ob_start();
		do_action('wp_ajax_nopriv_iasd-listadeposts-check-contents');
		$rendered = ob_get_contents();
		ob_end_clean();

		$this->assertEquals(0, strlen($rendered));
	}

	function testWillRenderContentCheckHtml() {
		ob_start();
		do_action('sidebar_admin_page');
		$rendered = ob_get_contents();
		ob_end_clean();

		$this->assertNotEquals(0, strlen($rendered));
	}
}