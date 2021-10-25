<?php

require_once 'ListaDePosts/_Widget_test.php';

class IASD_Sidebar_test extends WP_UnitTestCase{
	function setUp() {
		parent::setUp();
	}

	function test_classExists() {
		$this->assertTrue(class_exists('IASD_Sidebar'));
		$this->assertTrue(function_exists('iasd_dynamic_sidebar'));
	}

	function test_sidebarsExists() {
		IASD_Sidebar::RegisterFakes();

		global $wp_registered_sidebars;
		$this->assertArrayHasKey('styleguide-aside', $wp_registered_sidebars);
		$this->assertArrayHasKey('styleguide-article', $wp_registered_sidebars);
		$this->assertArrayHasKey('styleguide-banner', $wp_registered_sidebars);
	}

	function test_templateInclude() {
		$template = IASD_Sidebar::TemplateInclude('wrong-template');
		IASD_Sidebar::RegisterTemplateInclude();
		$templateFromFilter = apply_filters('template_include', 'wrong-template');

		$this->assertEquals($template,$templateFromFilter);
		$this->assertNotEquals('wrong-template',$templateFromFilter);
	}

	function test_sidebarDoesntExists() {
		//Sidebar doesn't exists
		global $wp_registered_sidebars;
		if(isset( $wp_registered_sidebars['sidebar-1'] )) {
			unset($wp_registered_sidebars['sidebar-1']);
		}

		ob_start();
		dynamic_sidebar('sidebar-1');
		$dynamicSidebar = ob_get_contents();
		ob_end_clean();

		ob_start();
		iasd_dynamic_sidebar('sidebar-1');
		$iasdDynamicSidebar = ob_get_contents();
		ob_end_clean();
		$this->assertEquals($dynamicSidebar,$iasdDynamicSidebar);

		ob_start();
		dynamic_sidebar('sidebar-1');
		$dynamicSidebar = ob_get_contents();
		ob_end_clean();

		ob_start();
		iasd_dynamic_sidebar('sidebar-1');
		$iasdDynamicSidebar = ob_get_contents();
		ob_end_clean();
		$this->assertEquals($dynamicSidebar,$iasdDynamicSidebar);


		ob_start();
		dynamic_sidebar(1);
		$dynamicSidebar = ob_get_contents();
		ob_end_clean();
		ob_start();
		iasd_dynamic_sidebar(1);
		$iasdDynamicSidebar = ob_get_contents();
		ob_end_clean();
		$this->assertEquals($dynamicSidebar,$iasdDynamicSidebar);
	}

	function test_defaultBehaviour() {
		//Sidebar do exists
		global $wp_registered_sidebars, $_wp_sidebars_widgets;
		if(empty( $wp_registered_sidebars['sidebar-1'] )) {
			$wp_registered_sidebars['sidebar-1'] = array(
				'name'          => 'Sidebar 1',
				'id'            => 'sidebar-1',
				'description'   => '',
				'class'         => '',
				'before_widget' => '<li id="%1$s" class="widget %2$s">',
				'after_widget'  => '</li>',
				'before_title'  => '<h2 class="widgettitle">',
				'after_title'   => '</h2>');
		}

		register_widget('IASD_Sidebar_FakeWidget');

		$fakeWidget = new IASD_Sidebar_FakeWidget();
		$fakeWidget->_set(1);
		$fakeWidget->update(array(), array());

		$_wp_sidebars_widgets['sidebar-1'][] = $fakeWidget->id;

		wp_register_sidebar_widget($fakeWidget->id, 'IASD_Sidebar_FakeWidget', array($fakeWidget, 'widget'), array(), array('number' => $fakeWidget->number));

		ob_start();
		dynamic_sidebar('sidebar-1');
		$dynamicSidebar = ob_get_contents();
		ob_end_clean();

		ob_start();
		iasd_dynamic_sidebar('sidebar-1');
		$iasdDynamicSidebar = ob_get_contents();
		ob_end_clean();

		$this->assertEquals($dynamicSidebar,$iasdDynamicSidebar);

		ob_start();
		dynamic_sidebar(1);
		$dynamicSidebar = ob_get_contents();
		ob_end_clean();
		ob_start();
		iasd_dynamic_sidebar(1);
		$iasdDynamicSidebar = ob_get_contents();
		ob_end_clean();
		$this->assertEquals($dynamicSidebar,$iasdDynamicSidebar);
	}

	function test_rowingBehaviour() {

		$sidebars = array('styleguide-banner', 'styleguide-article', 'styleguide-aside');

		foreach($sidebars as $sidebar) {
			global $wp_registered_sidebars, $_wp_sidebars_widgets;
			$_wp_sidebars_widgets[$sidebar] = array();
			register_widget('IASD_Sidebar_FakeWidget');

			for($i = 1; $i <= 4; $i++) {

				$widget = new IASD_ListaDePosts();
				$widget->number = IASD_ListaDePosts__Widget_test::widgetNumber();

				$instance = IASD_ListaDePosts::DefaultInstance();
				$instance['width']  = 'col-md-4';
				$instance['view']   = 'COL-MD-4';
				$widget->_setInstance($instance);

				$_wp_sidebars_widgets[$sidebar][] = $widget->slug();

				$settings = get_option($widget->option_name, array());
				$settings[$widget->number] = $instance;
				update_option($widget->option_name, $settings);

				wp_register_sidebar_widget($widget->slug(), 'IASD_ListaDePosts', array($widget, 'widget'), false, array(array('number' => $widget->number)));
				

				$fakeWidget = new IASD_Sidebar_FakeWidget();
				$fakeWidget->_set($i);
				$fakeWidget->update(array(), array());
				$settings = get_option($fakeWidget->option_name, array());
				$settings[$widget->number] = $instance;
				update_option($fakeWidget->option_name, $settings);

				$_wp_sidebars_widgets[$sidebar][] = $fakeWidget->id;

				wp_register_sidebar_widget($fakeWidget->id, 'IASD_Sidebar_FakeWidget', array($fakeWidget, 'widget'), false, array('number' => $fakeWidget->number));
			}

			ob_start();
			iasd_dynamic_sidebar($sidebar);
			$iasdDynamicSidebar = ob_get_contents();
			ob_end_clean();

			switch ($sidebar) {
				case 'styleguide-banner':
					$this->assertEquals(2, substr_count($iasdDynamicSidebar, '<!-- Auto Break -->')); //8 itens largura 4 = 36. 36 / 12 = 3 separadores. Ultimo não conta.
					break;
				case 'styleguide-article':
					$this->assertEquals(3, substr_count($iasdDynamicSidebar, '<!-- Auto Break -->')); //8 itens largura 6 = 48. 48 / 12 = 4 separadores. Ultimo não conta.
					break;
				case 'styleguide-aside':
					$this->assertEquals(7, substr_count($iasdDynamicSidebar, '<!-- Auto Break -->')); //8 itens largura 12. 8 separadores. Ultimo não conta.
					break;
			}
		}
	}
}

class IASD_Sidebar_FakeWidget extends WP_Widget {
	function __construct() {
		$widget_ops = array('classname' => $this, 'description' => 'IASD_Sidebar_FakeWidget' );
		parent::__construct('iasd_sidebar_fakewidget', 'IASD_Sidebar_FakeWidget', $widget_ops);
	}

	function widget($a, $b) {
		echo "\n<!-- fake widget -->\n";
	}
}
