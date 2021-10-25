<?php

require_once '_Widget_test.php';

class IASD_ListaDePosts_Assessors_test extends IASD_ListaDePosts__Widget_test {
	function testWhenIseSetTheInstanceValuesAreNulled() {
		$widget = new IASD_ListaDeposts();

		$widget->_setInnerVariables('Not Null','Not Null','Not Null','Not Null');

		$widget->_set($this->getWidgetNumber());

		$variables = $widget->_getInnerVariables();
		$this->assertNull($variables['view_config'], 'It should change for NULL after changing the widget');
		$this->assertNull($variables['availableViews'], 'It should change for NULL after changing the widget');
		$this->assertNull($variables['availablePostTypes'], 'It should change for NULL after changing the widget');

		$this->assertCount(4,$variables, 'instance, view_config, availableViews, availablePostTypes');
	}
	function testWhenUseSetInstanceTheInstanceValuesAreNulled() {
		$widget = new IASD_ListaDeposts();

		$widget->_setInnerVariables('Not Null','Not Null','Not Null','Not Null');

		$widget->_setInstance(array());

		$variables = $widget->_getInnerVariables();
		$this->assertNull($variables['view_config'], 'It should change for NULL after changing the widget');
		$this->assertNull($variables['availableViews'], 'It should change for NULL after changing the widget');
		$this->assertNull($variables['availablePostTypes'], 'It should change for NULL after changing the widget');

		$this->assertTrue(is_array($variables['instance']), 'It should be an empty array');
	}

	function test_staticAvailablePostTypes() {
		global $filtersApplied;
		$filtersApplied = array();

		$filtersApplied['local_post_types'] = 0;
		add_filter('local_post_types', 
			function($value) { global $filtersApplied; $filtersApplied['local_post_types']++; return $value; } );

		$filtersApplied['local_post_types-postmeta'] = 0;
		add_filter('local_post_types-postmeta', 
			function($value) { global $filtersApplied; $filtersApplied['local_post_types-postmeta']++; return $value; } );

		$filtersApplied['local_post_types-formats'] = 0;
		add_filter('local_post_types-formats', 
			function($value) { global $filtersApplied; $filtersApplied['local_post_types-formats']++; return $value; } );

		$availablePostTypes = IASD_ListaDePosts_AJAX::PostTypes();

		global $filtersApplied;

		$this->assertGreaterThan(0, $filtersApplied['local_post_types-postmeta']);
		$this->assertGreaterThan(0, $filtersApplied['local_post_types-postmeta']);
		$this->assertGreaterThan(0, $filtersApplied['local_post_types-formats']);

		$this->assertEquals($filtersApplied['local_post_types-postmeta'], 
			$filtersApplied['local_post_types-formats']);

		$this->assertEquals($filtersApplied['local_post_types-formats'], count($availablePostTypes));

		foreach($availablePostTypes as $name => $info) {
			$this->assertArrayHasKey('name', $info);
			$this->assertArrayHasKey('taxonomy', $info);
			$this->assertArrayHasKey('postmeta', $info);
			$this->assertArrayHasKey('formats', $info);
		}
	}

	function test_otherAvailableSources() {
		$number = $this->getWidgetNumber();

		$new_instance = array('source_id' => 'outra', 'source_extra' => md5(time()), 'number' => $number);
		$all_instances = array($number => $new_instance);
		update_option(IASD_ListaDePosts::option_id, $all_instances);

		$otherSources = IASD_ListaDePosts::OtherSources();

		$this->assertArrayHasKey(IASD_ListaDePosts::base_id . '_' . $number, $otherSources);
		$source = $otherSources[IASD_ListaDePosts::base_id . '_' . $number];
		$this->assertEquals($new_instance['source_extra'], $source['url']);

		$source_videos = IASD_ListaDePosts::BuildQuery(array('source_id' => 'videos'));
		$this->assertArrayHasKey('source', $source_videos);
		$this->assertContains('videos', $source_videos['source']);

		$source_outra = IASD_ListaDePosts::BuildQuery($new_instance);
		$this->assertArrayHasKey('source', $source_outra);
		$this->assertEquals($new_instance['source_extra'], $source_outra['source']);
	}

	function test_getAvailableViews() {

		// Teste para garantir que aparecem apenas as views compativeis com a sidebar
		$widget = new IASD_ListaDePosts();

		IASD_Sidebar::RegisterFakes();
		$sidebars = array('styleguide-banner', 'styleguide-aside', 'styleguide-article');
		foreach($sidebars as $sidebarToUse) {
			$widget->number = $this->getWidgetNumber();

			$basicInstance = IASD_ListaDePosts::DefaultInstance();
			$basicInstance['title'] = $widget->slug();
			$basicInstance['number'] = $widget->number;
			$widget->_setInstance($basicInstance);

			//put it in the sidebar
			global $_wp_sidebars_widgets, $wp_registered_sidebars;
			if(!isset($_wp_sidebars_widgets[$sidebarToUse]))
				$_wp_sidebars_widgets[$sidebarToUse] = array();
			$_wp_sidebars_widgets[$sidebarToUse][] = $widget->slug();

			$sidebar = $wp_registered_sidebars[$sidebarToUse];

			$sidebar_name = $widget->findSidebar();
			$my_sidebar = $widget->getSidebar();

			$this->assertEquals($sidebar, $my_sidebar);

			$sidebarColClass = $my_sidebar['col_class'];

			$availableViews = $widget->getAvailableViews();

			$registered_views = IASD_ListaDePosts_Views::GetViews();

			foreach($registered_views as $name => $info) {
				if(count($info['post_type'])) {
					$this->assertArrayNotHasKey($name, $availableViews);
					continue;
				}
				if($sidebarColClass == 'col-md-12') {
					$this->assertArrayHasKey($name, $availableViews);
				} else if($sidebarColClass == 'col-md-8') {
					if(in_array('col-md-8', $info['cols']) || in_array('col-md-4', $info['cols']))
						$this->assertArrayHasKey($name, $availableViews);
					else
						$this->assertArrayNotHasKey($name, $availableViews);
				} else if($sidebarColClass == 'col-md-4') {
					if(in_array('col-md-4', $info['cols']))
						$this->assertArrayHasKey($name, $availableViews);
					else
						$this->assertArrayNotHasKey($name, $availableViews);
				}
			}
		}
	}

	function testGetRightSidebarBanner() {
		$this->inner_getSidebar('styleguide-banner');
	}

	function testGetRightSidebarArticle() {
		$this->inner_getSidebar('styleguide-article');
	}

	function testGetRightSidebarAside() {
		$this->inner_getSidebar('styleguide-aside');
	}

	function inner_getSidebar($sidebarUsedSlug) {
		$this->addFakePosts();
		// Teste para garantir que aparecem apenas as views compativeis com a sidebar
		$widget = new IASD_ListaDePosts();
		IASD_Sidebar::RegisterFakes();

		$widget->number = $this->getWidgetNumber();

		$basicInstance = IASD_ListaDePosts::DefaultInstance();
		$basicInstance['title'] = $widget->slug();
		$savedInstance = $widget->update($basicInstance, array());
		$widget->_setInstance($savedInstance);

		//put it in the sidebar
		global $_wp_sidebars_widgets, $wp_registered_sidebars;
		if(!isset($_wp_sidebars_widgets[$sidebarUsedSlug]))
			$_wp_sidebars_widgets[$sidebarUsedSlug] = array();
		$_wp_sidebars_widgets[$sidebarUsedSlug][] = $widget->slug();

		$sidebarSelectedSlug = $widget->findSidebar();
		$this->assertEquals($sidebarSelectedSlug, $sidebarUsedSlug);

		$sidebarSelectedObject = $widget->getSidebar();
		$sidebarUsedObject = $wp_registered_sidebars[$sidebarUsedSlug];

		$this->assertEquals($sidebarUsedObject, $sidebarSelectedObject);
	}

	function test_cookieGetAssessors() {
		$_COOKIE = array();
		$widget = new IASD_ListaDePosts();
		$basicInstance = IASD_ListaDePosts::DefaultInstance();
		$basicInstance['number'] = $this->getWidgetNumber();
		$widget->_setInstance($basicInstance);
		$cookieVal = md5(time());

		$idx = $widget->slug() . '::grouping_slug';

		$this->assertNull($widget->getCookieGroupingSlug());
		$this->assertNull($widget->getCookie('grouping_slug'));

		$_COOKIE[$idx] = $cookieVal;

		$this->assertEquals($cookieVal, $widget->getCookieGroupingSlug());
		$this->assertEquals($cookieVal, $widget->getCookie('grouping_slug'));

		$_COOKIE = array();
	}

	function test_cookieSetAssessors() {
		$cookieVal = md5(time());
		global $IASD_ListaDePosts_CookieHandler;
		$_COOKIE = array();
		$widget = new IASD_ListaDePosts();
		$basicInstance = IASD_ListaDePosts::DefaultInstance();
		$basicInstance['number'] = $this->getWidgetNumber();
		$basicInstance['grouping_slug'] = $cookieVal;
		$widget->_setInstance($basicInstance);

		//Code Coverage
		$idx = $widget->slug() . '::grouping_slug';
		$widget->setCookieGroupingSlug();
	}

	function testLoadRulesForInvalidSource() {
		$rules = IASD_ListaDeposts::LoadSourceRules('invalid-source');

		$this->assertNull($rules);
	}

	function testLoadRulesForInvalidPostType() {
		$rules = IASD_ListaDeposts::LoadPostTypeRules('invalid-post-type');

		$this->assertNull($rules);
	}
}



