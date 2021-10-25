<?php

require_once '_Widget_test.php';

class IASD_ListaDePosts_Render_widgetWidthClass_test extends IASD_ListaDePosts__Widget_test {
	function setUp() {
		parent::setUp();

		IASD_Sidebar::RegisterFakes();
	}

	// widgetWidthClass && widgetAddWidthClass for Sidebars 12
	function test_widgetWidthClass_Sidebar12() {
		$sidebarToUse = 'styleguide-banner';

		$configs = array();
		$configs[] = array('view' => 'COL-MD-12', 'width' => 'col-md-12', 'expect' => 'col-md-12');
		$configs[] = array('view' => 'COL-MD-12', 'width' => 'col-md-8', 'expect' => 'col-md-8');
		$configs[] = array('view' => 'COL-MD-12', 'width' => 'col-md-4', 'expect' => 'col-md-4');
		$configs[] = array('view' => 'COL-MD-8',  'width' => 'col-md-8', 'expect' => 'col-md-8');
		$configs[] = array('view' => 'COL-MD-8',  'width' => 'col-md-4', 'expect' => 'col-md-4');
		$configs[] = array('view' => 'COL-MD-4',  'width' => 'col-md-4', 'expect' => 'col-md-4');

		foreach($configs as $k => $config) {
			$widget = new IASD_ListaDePosts();
			$widget->number = $this->getWidgetNumber();

			$instance = IASD_ListaDePosts::DefaultInstance();
			$instance['width'] = $config['width'];
			$instance['view'] = $config['view'];
			$instance['number'] = $widget->number;

			$widget->_setInstance($instance);

			global $_wp_sidebars_widgets;
			$_wp_sidebars_widgets[$sidebarToUse] = array($widget->slug());

			$this->assertEquals($config['expect'], $widget->widgetWidthClass());
			
			$_wp_sidebars_widgets[$sidebarToUse] = array();
		}
	}

	function test_widgetWidthClass_Sidebar8() {
		$sidebarToUse = 'styleguide-article';

		$configs = array();
		$configs[] = array('view' => 'COL-MD-8', 'width' => 'col-md-8', 'expect' => 'col-md-12');
		$configs[] = array('view' => 'COL-MD-8', 'width' => 'col-md-4', 'expect' => 'col-md-6');
		$configs[] = array('view' => 'COL-MD-4', 'width' => 'col-md-4', 'expect' => 'col-md-6');


		foreach($configs as $config) {
			$widget = new IASD_ListaDePosts();
			$widget->number = $this->getWidgetNumber();

			$instance = IASD_ListaDePosts::DefaultInstance();
			$instance['width'] = $config['width'];
			$instance['view'] = $config['view'];
			$instance['number'] = $widget->number;
			$widget->_setInstance($instance);

			global $_wp_sidebars_widgets;
			$_wp_sidebars_widgets[$sidebarToUse] = array($widget->slug());

			$this->assertEquals($config['expect'], $widget->widgetWidthClass());

			$_wp_sidebars_widgets[$sidebarToUse] = array();
		}
	}

	function test_widgetWidthClass_Sidebar4() {
		$sidebarToUse = 'styleguide-aside';

		$widget = new IASD_ListaDePosts();
		$widget->number = $this->getWidgetNumber();
		$instance = IASD_ListaDePosts::DefaultInstance();
		$instance['width'] = 'col-md-4';
		$instance['view'] = 'COL-MD-4';
		$instance['number'] = $widget->number;
		$widget->_setInstance($instance);

		global $_wp_sidebars_widgets;
		$_wp_sidebars_widgets[$sidebarToUse] = array($widget->slug());

		$this->assertEquals('col-md-12', $widget->widgetWidthClass());

		$_wp_sidebars_widgets[$sidebarToUse] = array();
	}

	function test_widgetWidthClass_NoSidebar() {
		$widget = new IASD_ListaDePosts();
		$widget->number = $this->getWidgetNumber();
		$instance = IASD_ListaDePosts::DefaultInstance();
		$instance['width'] = 'col-md-4';
		$instance['view'] = 'COL-MD-4';
		$instance['number'] = $widget->number;
		$widget->_setInstance($instance);

		$this->assertEquals('', $widget->widgetWidthClass());
	}

	function test_widgetTitle() {
		$this->addUsersAndPosts();

		$widget = new IASD_ListaDePosts();
		$widget->number = $this->getWidgetNumber();
		$instance = IASD_ListaDePosts::DefaultInstance();
		$instance['number'] = $widget->number;
		$instance['title'] = 'Abcdefgh';
		$widget->_setInstance($instance);

		$this->assertEquals('<h1>Abcdefgh</h1>', $widget->widgettitle());

		$widget->number = $this->getWidgetNumber();
		$instance['number'] = $widget->number;
		$instance['grouping_taxonomy'] = self::TAX_FOR_TEST;
		$widget->_setInstance($instance);
		$this->assertEquals('<h1>Abcdefgh</h1>', $widget->widgettitle());

		$widget->number = $this->getWidgetNumber();
		$instance['number'] = $widget->number;
		$instance['grouping_slug'] = 'author-' . $this->authors[0]->ID;
		$widget->_setInstance($instance);
		$this->assertNotEquals('<h1>Abcdefgh</h1>', $widget->widgettitle());
		$this->assertEquals('<h1>author-'.$this->authors[0]->ID.'</h1>', $widget->widgettitle());

		$widget->number = $this->getWidgetNumber();
		$instance['number'] = $widget->number;
		$instance['grouping_slug'] = 'unexistent-author';
		$widget->_setInstance($instance);
		$this->assertEquals('<h1>Abcdefgh</h1>', $widget->widgettitle());
	}

	function test_widgetAddGroupingTaxonomyHtml() {
		$this->addUsersAndPosts();

		$key1 = 'author-' . $this->authors[0]->ID;
		$key2 = 'author-' . $this->authors[1]->ID;

		$widget = new IASD_ListaDePosts();
		$widget->number = $this->getWidgetNumber();
		$base_instance = IASD_ListaDePosts::DefaultInstance();
		$base_instance['grouping_taxonomy'] = self::TAX_FOR_TEST;
		$base_instance['taxonomy_query'] = array();
		$base_instance['taxonomy_query'][self::TAX_FOR_TEST] = array();
		$base_instance['taxonomy_query'][self::TAX_FOR_TEST]['taxonomy'] = self::TAX_FOR_TEST;
		$base_instance['taxonomy_query'][self::TAX_FOR_TEST]['field'] = 'slug';
		$base_instance['taxonomy_query'][self::TAX_FOR_TEST]['terms'] = array($key1, $key2);
		$base_instance['number'] = $widget->number;
		$widget->_setInstance($base_instance);

		$groupingTerms = $widget->widgetGetGroupingTerms();
		$this->assertArrayHasKey($key1, $groupingTerms);
		$this->assertArrayHasKey($key2, $groupingTerms);
		$this->assertCount(2, $groupingTerms);

		ob_start();
		$widget->widgetAddGroupingTaxonomyHtml();
		$rendered = ob_get_contents();
		ob_end_clean();

		$this->assertContains($key1, $rendered);
		$this->assertContains($key2, $rendered);
	}
}