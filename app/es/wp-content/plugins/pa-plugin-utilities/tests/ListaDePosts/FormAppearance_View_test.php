<?php

require_once 'FormAppearance__test.php';

class IASD_ListaDePosts_FormAppearance_View_test extends IASD_ListaDePosts_FormAppearance__test{

	function test_formAppearanceView() {
		// Teste para garantir que aparecem apenas as views compativeis com a sidebar
		$widget = new IASD_ListaDePosts();

		IASD_Sidebar::RegisterFakes();
		$sidebars = array('styleguide-banner', 'styleguide-aside', 'styleguide-article');
		foreach($sidebars as $sidebarToUse) {
			$widget->number = $this->getWidgetNumber();

			$basicInstance = IASD_ListaDePosts::DefaultInstance();
			$basicInstance['title'] = $widget->slug();
			$savedInstance = $widget->update($basicInstance, array());
			$widget->_setInstance($savedInstance);

			//put it in the sidebar
			global $_wp_sidebars_widgets, $wp_registered_sidebars;
			if(!isset($_wp_sidebars_widgets[$sidebarToUse]))
				$_wp_sidebars_widgets[$sidebarToUse] = array();
			$_wp_sidebars_widgets[$sidebarToUse][] = $widget->slug();

			$sidebar = $wp_registered_sidebars[$sidebarToUse];

			$my_sidebar = $widget->getSidebar();

			$sidebarColClass = $my_sidebar['col_class'];

			ob_start();
			$widget->formAppearanceView();
			$savedInstanceForm = ob_get_contents();
			ob_end_clean();

			$registered_views = IASD_ListaDePosts_Views::GetViews();
			foreach($registered_views as $name => $info) {
				if(count($info['post_type'])) {
					$this->assertNotContains('<option value="'.$name.'">'.$info['description'].'</option>', $savedInstanceForm);
					continue;
				}
				if($sidebarColClass == 'col-md-12') {
					$this->assertContains('<option value="'.$name.'">'.$info['description'].'</option>', $savedInstanceForm);
				} else if($sidebarColClass == 'col-md-8') {
					if(in_array('col-md-8', $info['cols']) || in_array('col-md-4', $info['cols']))
						$this->assertContains('<option value="'.$name.'">'.$info['description'].'</option>', $savedInstanceForm);
					else
						$this->assertNotContains('<option value="'.$name.'">'.$info['description'].'</option>', $savedInstanceForm);
				} else if($sidebarColClass == 'col-md-4') {
					if(in_array('col-md-4', $info['cols']))
						$this->assertContains('<option value="'.$name.'">'.$info['description'].'</option>', $savedInstanceForm);
					else
						$this->assertNotContains('<option value="'.$name.'">'.$info['description'].'</option>', $savedInstanceForm);
				}
			}
		}
	}

	function test_formAppearanceViewGallery() {
		// Teste para garantir que aparecem apenas as views compativeis com a sidebar
		$widget = new IASD_ListaDePosts();

		IASD_Sidebar::RegisterFakes();
		$sidebarToUse = 'styleguide-banner';
		$widget->number = $this->getWidgetNumber();

		$basicInstance = IASD_ListaDePosts::DefaultInstance();
		$basicInstance['title'] = $widget->slug();
		$basicInstance['post_type'] = IASD_ImageGallery::POST_TYPE;
		$savedInstance = $widget->update($basicInstance, array());
		$widget->_setInstance($savedInstance);

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

		ob_start();
		$widget->formAppearanceView();
		$savedInstanceForm = ob_get_contents();
		ob_end_clean();

		$this->assertContains('<option value="list_galleries">', $savedInstanceForm);
		$this->assertContains('<option value="slider_galleries">', $savedInstanceForm);
	}
}