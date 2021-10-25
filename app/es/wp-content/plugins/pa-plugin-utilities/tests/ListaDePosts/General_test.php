<?php

require_once '_Widget_test.php';

class IASD_ListaDePosts_General_test extends IASD_ListaDePosts__Widget_test {

	function test_classExists() {
		$this->assertTrue(class_exists('IASD_ListaDePosts'));
		$this->assertTrue(class_exists('IASD_ListaDePosts_Views'));

		$this->assertContains('IASD_ListaDePosts', $this->getRegisteredWidgets());
		IASD_ListaDePosts::UnregisterWidget();
		$this->assertNotContains('IASD_ListaDePosts', $this->getRegisteredWidgets());

		IASD_ListaDePosts::RegisterWidget();
		$this->assertContains('IASD_ListaDePosts', $this->getRegisteredWidgets());

		$this->assertEquals(10, has_action('widgets_init', array('IASD_ListaDePosts_Views','RegisterDefaults')));

		$this->assertEquals(10, has_action('wp_ajax_iasd-listadeposts-refresh', array('IASD_ListaDePosts','Refresh')));
		$this->assertEquals(10, has_action('wp_ajax_nopriv_iasd-listadeposts-refresh', array('IASD_ListaDePosts','Refresh')));


		$widget = new IASD_ListaDePosts();
		$this->assertEquals($widget->id_base, IASD_ListaDePosts::base_id);
		$this->assertEquals($widget->option_name, IASD_ListaDePosts::option_id);
		$instance = array('number' => $this->getWidgetNumber());
		$widget->_setInstance($instance);
		$this->assertEquals($instance['number'], $widget->number);
		$this->assertEquals($widget->id, $widget->slug());

		ob_start();
		$widget->formTitle();
		$rendered = ob_get_contents();
		ob_end_clean();
		$this->assertContains(IASD_ListaDePosts::form_id, $rendered);
	}

	function test_render() {
		$this->addFakePosts();
		$widget = new IASD_ListaDePosts();
		$basicInstance = IASD_ListaDePosts::DefaultInstance();
		$basicInstance['title'] = 'a' . time();
		$basicInstance['post_type'] = $this->fakePostTypes[1];

		$savedInstance = $widget->update($basicInstance, array());
		$this->assertEquals(count($savedInstance),count($basicInstance));

		$widget->_setInstance($savedInstance);
		$queryObject = $widget->query();
		$this->assertTrue($queryObject->have_posts());

		//Render without view
		ob_start();
		$widget->widget(array(), $savedInstance);
		$rendered = ob_get_contents();
		ob_end_clean();
		$this->assertEquals('<!-- VIEW NOT DEFINED -->', $rendered);
		$this->assertEquals($savedInstance, $widget->getInstance());
		$this->assertEquals(array(), $widget->widgetArgs());
		//Title has a default
		$this->assertEquals('<h1>' . $basicInstance['title'] . '</h1>', $widget->widgetTitle());

		//Custom Title
		$args = array('before_title' => 'AAA', 'after_title' => 'BBB');
		ob_start();
		$widget->widget($args, $savedInstance);
		$rendered = ob_get_contents();
		ob_end_clean();
		$this->assertEquals($args['before_title'] . $basicInstance['title'] . $args['after_title'], $widget->widgetTitle());
		$this->assertEquals($args, $widget->widgetArgs());
		$this->assertNull($widget->getSidebar());
	}

	/**
			IASD_ListaDePosts_Views
	*/

	function test_views() {
		if(defined('WP_DEBUG') && WP_DEBUG)
			IASD_ListaDePosts_Views::RegisterFakes();

		$registered_views_1 = IASD_ListaDePosts_Views::GetViews();
		$this->assertGreaterThan(0,count($registered_views_1));

		foreach($registered_views_1 as $view_name => $config) {
			$this->assertTrue(IASD_ListaDePosts_Views::HasView($view_name));
			IASD_ListaDePosts_Views::UnregisterView($view_name);
			$this->assertFalse(IASD_ListaDePosts_Views::HasView($view_name));
		}

		$registered_views_2 = IASD_ListaDePosts_Views::GetViews();
		$this->assertEquals(0,count($registered_views_2));

		IASD_ListaDePosts_Views::RegisterDefaults();
		if(defined('WP_DEBUG') && WP_DEBUG)
			IASD_ListaDePosts_Views::RegisterFakes();

		$registered_views_3 = IASD_ListaDePosts_Views::GetViews();
		$this->assertEquals(count($registered_views_1), count($registered_views_3));
		$this->assertEquals($registered_views_1, $registered_views_3);

		$testingViewName = 'testing_view_name';
		$testingViewPath = __FILE__;

		$base_config = IASD_ListaDePosts_Views::BaseConfig($testingViewName, $testingViewPath);
		$worked = IASD_ListaDePosts_Views::RegisterView($testingViewName, $testingViewPath);
		$this->assertTrue($worked);
		$this->assertTrue(IASD_ListaDePosts_Views::HasView($testingViewName));
		$testingView = IASD_ListaDePosts_Views::GetView($testingViewName);
		$this->assertEquals($base_config, $testingView);
		IASD_ListaDePosts_Views::UnregisterView($view_name);
	}

	function test_render_box() {
		$this->addFakePosts();
		$widget = new IASD_ListaDePosts();
		$widget->number = $this->getWidgetNumber();
		$this->assertEquals(IASD_ListaDePosts::base_id . '-' . $widget->number, $widget->slug());

		IASD_Sidebar::RegisterFakes();
		$sidebar_to_use = 'styleguide-aside'; //MD 4

		$basicInstance = IASD_ListaDePosts::DefaultInstance();
		$basicInstance['title'] = 'a' . time();
		$basicInstance['post_type'] = 'fake_post_b';
		$basicInstance['posts_per_page'] = 100;
		$basicInstance['sidebar'] = $sidebar_to_use;

		$basicInstance = IASD_ListaDePosts::Validate($basicInstance, $basicInstance);

		$widget->_setInstance($basicInstance);

		global $_wp_sidebars_widgets, $wp_registered_sidebars;
		$_wp_sidebars_widgets[$sidebar_to_use] = array($widget->slug());

		$this->assertEquals($sidebar_to_use, $widget->findSidebar());

		$queryObject = $widget->query();

		$args = array('before_title' => '<h9>', 'after_title' => '</h9>');

		//Render without view
		ob_start();
		$widget->widget($args, $basicInstance);
		$rendered = ob_get_contents();
		ob_end_clean();
		$this->assertNull($widget->widgetGet('some_weird_field'));
		$this->assertEquals($widget->widgetGet('title'), $basicInstance['title']);

		//Rendering Title

		$this->assertEquals($widget->widgetGroupingTaxonomyClass(), '');
		$this->assertEquals($widget->widgetAddGroupingTaxonomyHtml(), '');

		$box_views = array('box_simple', 'box_simple_thumbs', 'box_ordered', 'box_ordered_thumbs');
		foreach($box_views as $viewNameInUse) {
			$this->assertTrue(IASD_ListaDePosts_Views::HasView($viewNameInUse));
			$viewInUse = IASD_ListaDePosts_Views::GetView($viewNameInUse);
			$basicInstance['view'] = $viewNameInUse;
			$widget->_setInstance($basicInstance);
			$widget->widgetCacheReset();

			$views = array();
			ob_start();
			$widget->widget($args, $basicInstance);
			$rendered = ob_get_contents();
			ob_end_clean();
			$this->assertContains($basicInstance['title'],$rendered);
			$this->assertContains($args['before_title'],$rendered);
			$this->assertContains($widget->widgetTitle(),$rendered);
			$this->assertContains($args['after_title'],$rendered);
			$this->assertContains($args['before_title'] . $widget->widgetGet('title') . $args['after_title'],$rendered);

			$view = $widget->getSelectedView();
			$this->assertEquals($viewInUse, $view);
			$this->assertEquals($widget->widgetWidthClass(), 'col-md-12'); //COL MD 4 dentro de COL MD 4 vira COL MD 12

			//Count is $fakePostsToAdd - 1 cause ONE of them has no DSA taxonomy
			$taggedPosts = $this->fakePostsToAdd - 1;
			$query_args = IASD_ListaDePosts::BuildQuery($widget->getInstance());

			$this->assertEquals($taggedPosts, $queryObject->post_count);

			$this->assertEquals(PAPU_LDPV . '/box.php', $view['path']);
		}

		//MD8
/*		$sidebar_to_use = 'styleguide-article';
		if(!isset($_wp_sidebars_widgets[$sidebar_to_use]))
			$_wp_sidebars_widgets[$sidebar_to_use] = array();
		$_wp_sidebars_widgets[$sidebar_to_use][] = $widget->slug();

		$basicInstance = IASD_ListaDePosts::DefaultInstance();
		$basicInstance['title'] = 'a' . time();

		$savedInstance = $widget->update($basicInstance, array());
		$viewNameInUse = 'box_simple';
		$savedInstance['view'] = $viewNameInUse;
		$queryObject = $widget->query($savedInstance);
		$args = array('before_title' => '<h9>', 'after_title' => '</h9>');

		//Render without view
		ob_start();
		$widget->widget($args, $savedInstance);
		$rendered = ob_get_contents();
		ob_end_clean();*/
	}

	/**
	FORM
	*/

	function test_formUnsaved() {

		$widget = new IASD_ListaDePosts();
		$widget->number = 77;

		$basicInstance = IASD_ListaDePosts::DefaultInstance();

		global $_wp_sidebars_widgets, $wp_registered_sidebars;
		$sidebar_to_use = 'styleguide-banner'; //MD 12
		if(!isset($_wp_sidebars_widgets[$sidebar_to_use]))
			$_wp_sidebars_widgets[$sidebar_to_use] = array();
		$_wp_sidebars_widgets[$sidebar_to_use][] = $widget->slug();

		//Ensures that the form will be displayed only if the instance already have been saved
		$savedInstance = $widget->update($basicInstance, array());
		$this->assertEquals(count($savedInstance),count($basicInstance));

		ob_start();
		$this->assertFalse($widget->form($basicInstance));
		$basicInstanceForm = ob_get_contents();
		ob_end_clean();
		$this->assertContains('Clique em "Salvar" para mostrar as opções', $basicInstanceForm);
	}

	function test_afterSave() {

		$widget = new IASD_ListaDePosts();
		$widget->number = 77;

		$basicInstance = IASD_ListaDePosts::DefaultInstance();

		global $_wp_sidebars_widgets, $wp_registered_sidebars;
		$sidebar_to_use = 'styleguide-banner'; //MD 12
		if(!isset($_wp_sidebars_widgets[$sidebar_to_use]))
			$_wp_sidebars_widgets[$sidebar_to_use] = array();
		$_wp_sidebars_widgets[$sidebar_to_use][] = $widget->slug();

		//Ensures that the form will be displayed only if the instance already have been saved
		$savedInstance = $widget->update($basicInstance, array());
		$this->assertEquals(count($savedInstance),count($basicInstance));

		$savedInstance['title'] = 'Some title';
		ob_start();
		$this->assertTrue($widget->form($savedInstance));
		$savedInstanceForm = ob_get_contents();
		ob_end_clean();

		$this->assertNotContains('Clique em "Salvar" para mostrar as opções', $savedInstanceForm);

		//POSTS_PER_PAGE have been rendered
		$this->assertContains('value="' . $savedInstance['posts_per_page'] . '"', $savedInstanceForm);
		$this->assertContains($widget->get_field_id('posts_per_page'), $savedInstanceForm);
		$this->assertContains($widget->get_field_name('posts_per_page'), $savedInstanceForm);

		//WIDTH have been rendered
		$this->assertContains($widget->get_field_id('width'), $savedInstanceForm);
		$this->assertContains($widget->get_field_name('width'), $savedInstanceForm);

		//Title
		$this->assertContains($savedInstance['title'], $savedInstanceForm);
		$this->assertContains('value="' . $savedInstance['title'] . '"', $savedInstanceForm);
		$this->assertContains($widget->get_field_id('title'), $savedInstanceForm);
		$this->assertContains($widget->get_field_name('title'), $savedInstanceForm);

		//Secret
		$this->assertContains($savedInstance['secret'], $savedInstanceForm);
		$this->assertContains($widget->get_field_id('secret'), $savedInstanceForm);
		$this->assertContains($widget->get_field_name('secret'), $savedInstanceForm);

		//Views
		$this->assertContains('mandatory iasd-widget-appearance-view', $savedInstanceForm);

		//ORDER have been rendered
		$this->assertContains($widget->get_field_id('order'), $savedInstanceForm);
		$this->assertContains($widget->get_field_name('order'), $savedInstanceForm);

		//ORDERBY have been rendered
		$this->assertContains($widget->get_field_id('orderby'), $savedInstanceForm);
		$this->assertContains($widget->get_field_name('orderby'), $savedInstanceForm);

		//Post Type
		$this->assertContains($widget->get_field_id('post_type'), $savedInstanceForm);
		$this->assertContains($widget->get_field_name('post_type'), $savedInstanceForm);

		//Taxonomies
		$taxonomies = IASD_Taxonomias::GetAllTaxonomies();
		foreach($taxonomies as $taxonomy_slug) {
			$taxonomy_slug_field = $widget->get_field_id($taxonomy_slug);
			$taxonomy = get_taxonomy($taxonomy_slug);

			$this->assertContains($taxonomy->label, $savedInstanceForm);
			$this->assertContains($taxonomy_slug_field . '-show', $savedInstanceForm);
			$this->assertContains($taxonomy_slug_field . '-hide', $savedInstanceForm);
			$this->assertContains($taxonomy_slug_field . '-list', $savedInstanceForm);
		}
	}

}
