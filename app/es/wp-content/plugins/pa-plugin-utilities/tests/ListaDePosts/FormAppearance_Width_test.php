<?php

require_once 'FormAppearance__test.php';

class IASD_ListaDePosts_FormAppearance_General_test extends IASD_ListaDePosts_FormAppearance__test{
	
	function test_formAppearanceWidth() {
		// Teste para garantir que aparecem apenas as larguras compativeis com a sidebar
		$widget = new IASD_ListaDePosts();
		IASD_ListaDePosts_Views::RegisterFakes();
		IASD_Sidebar::RegisterFakes();
		$sidebars = array('styleguide-banner', 'styleguide-aside', 'styleguide-article');
		$baseCount = 70;
		foreach($sidebars as $sidebarToUse) {
			$baseCount++;
			$widget->number = $baseCount;

			//put it in the sidebar
			global $_wp_sidebars_widgets, $wp_registered_sidebars;
			if(!isset($_wp_sidebars_widgets[$sidebarToUse]))
				$_wp_sidebars_widgets[$sidebarToUse] = array();
			$_wp_sidebars_widgets[$sidebarToUse][] = $widget->slug();
			$sidebar = $wp_registered_sidebars[$sidebarToUse];
			$sidebarColClass = $sidebar['col_class'];

			$basicInstance = IASD_ListaDePosts::DefaultInstance();
			$basicInstance['title'] = $widget->slug();
			$basicInstance['view'] = strtoupper($sidebarColClass);

			$savedInstance = $widget->update($basicInstance, array());
			$widget->_setInstance($savedInstance);

			/* TODO
			$basicInstance['width'] = $sidebarColClass;
			$widget->update($basicInstance, array());
			$this->assertEquals($sidebarColClass, $widget->widgetWidthClass());*/

			//Ensures that each col-width allows the proper options
			$widget->_setInstance($savedInstance);
			ob_start();
			$widget->formAppearanceView($savedInstance);
			$savedInstanceForm = ob_get_contents();
			ob_end_clean();

			$availableCols = $widget->getAvailableCols();
			if($sidebarColClass == 'col-md-12') {
				$this->assertCount(3, $availableCols);
				$this->assertTrue(isset($availableCols['col-md-4']));
				$this->assertTrue(isset($availableCols['col-md-8']));
				$this->assertTrue(isset($availableCols['col-md-12']));
			} else if($sidebarColClass == 'col-md-8') {
				$this->assertCount(2, $availableCols);
				$this->assertTrue(isset($availableCols['col-md-4']));
				$this->assertTrue(isset($availableCols['col-md-8']));
			} else if($sidebarColClass == 'col-md-4') {
				$this->assertCount(1, $availableCols);
				$this->assertTrue(isset($availableCols['col-md-4']));
			}

			//Ensures that all views are being rendered in the select
			unset($basicInstance['view']);
			$savedInstance = $widget->update($basicInstance, array());
			$widget->_setInstance($savedInstance);
			ob_start();
			$widget->formAppearanceView($savedInstance);
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
}