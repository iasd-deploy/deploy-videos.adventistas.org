<?php

require_once 'FormContent__test.php';

class IASD_ListaDePostsWidget_FormContent_PostType_test extends IASD_ListaDePosts_FormContent__test {

	function test_hasField() {
		$widget = new IASD_ListaDePosts();
		$widget->number = $this->getWidgetNumber();

		$basicInstance = IASD_ListaDeposts::DefaultInstance();
		$savedInstance = $widget->update($basicInstance, array());
		ob_start();
		$widget->formContent($savedInstance);
		$savedInstanceForm = ob_get_contents();
		ob_end_clean();
		$this->assertContains($widget->get_field_id('post_type'), $savedInstanceForm);
		$this->assertContains($widget->get_field_name('post_type'), $savedInstanceForm);
	}

	function test_returnNullIfInvalidPostType() {
		$widget = new IASD_ListaDePosts();
		$widget->number = $this->getWidgetNumber();

		$basicInstance = IASD_ListaDeposts::DefaultInstance();
		$basicInstance['post_type'] = 'post';
		$widget->_setInstance($basicInstance);
		$this->assertNotNull($widget->getCurrentPostType());

		$basicInstance['post_type'] = 'notpost';
		$widget->_setInstance($basicInstance);
		$this->assertNull($widget->getCurrentPostType());
	}

	function testIsSelectIsGeneratedProperly() {
		$widget = new IASD_ListaDePosts();
		$widget->number = $this->getWidgetNumber();

		$post_type_names = get_post_types(array('_builtin' => false, 'public' => true));
		$post_type_names[] = 'post';

		$allTaxonomies = IASD_Taxonomias::GetAllTaxonomies();

		foreach($post_type_names as $post_type_name) {
			$basicInstance = IASD_ListaDeposts::DefaultInstance();
			$basicInstance['post_type'] = $post_type_name;
			$savedInstance = $widget->update($basicInstance, array());

			$widget->_setInstance($savedInstance);
			ob_start();
			$widget->formContentPostType();
			$savedInstancePostTypeForm = ob_get_contents();
			ob_end_clean();

			$this->assertContains('<option value="'.$post_type_name.'" selected="selected"', $savedInstancePostTypeForm, 'Should be selected: ' . $post_type_name);
			$this->assertEquals(1, substr_count($savedInstancePostTypeForm, 'selected="selected"'), 'Should have ONE selected: ' . $post_type_name);

			//DEFAULT has been removed
			$this->assertEquals(count($post_type_names), substr_count($savedInstancePostTypeForm, '<option value='), 'Should have ALL types + Default');
			$this->assertNotContains('<option value="">Selecione...</option>', $savedInstancePostTypeForm, 'Should NOT have a default option');
		}
	}

	function test_taxonomiesAvailable() {
		$widget = new IASD_ListaDePosts();
		$widget->number = $this->getWidgetNumber();

		$post_type_names = get_post_types(array('_builtin' => false, 'public' => true));
		$post_type_names[] = 'post';

		$allTaxonomies = IASD_Taxonomias::GetAllTaxonomies();

		$testedA = false;
		$testedB = false;

		foreach($post_type_names as $post_type_name) {
			$basicInstance = IASD_ListaDeposts::DefaultInstance();
			$basicInstance['post_type'] = $post_type_name;
			$savedInstance = $widget->update($basicInstance, array());

			$widget->_setInstance($savedInstance);
			ob_start();
			$widget->formContentTaxonomies();
			$savedInstanceTaxonomyForm = ob_get_contents();
			ob_end_clean();
			//remove line break
			$savedInstanceTaxonomyForm = trim(preg_replace('/\s\s+/', ' ', $savedInstanceTaxonomyForm));

			$availableTaxonomies = $widget->getAvailableTaxonomies();
			$taxonomy_query = $widget->widgetGet('taxonomy_query', array());
			$taxonomy_norepeat = $widget->widgetGet('taxonomy_norepeat', array());
			if(!count($availableTaxonomies))
				$availableTaxonomies = array_keys($allTaxonomies);

			ob_start();
			$widget->formContentTaxonomies();
			$savedInstanceInnerTaxonomyForm = ob_get_contents();
			ob_end_clean();

			foreach ($allTaxonomies as $taxonomy) {
				$savedInstanceInnerTaxonomyForm = trim(preg_replace('/\s\s+/', ' ', $savedInstanceInnerTaxonomyForm));

				$this->assertContains($savedInstanceInnerTaxonomyForm, $savedInstanceTaxonomyForm);
				$disabled_text = 'iasd-widget-content-'.$taxonomy.'-container fieldsetborder fieldsetborder" disabled="disabled"';
				if(in_array($taxonomy, $availableTaxonomies)) {
					$this->assertNotContains($disabled_text, $savedInstanceInnerTaxonomyForm, "For $post_type_name the $taxonomy should not be disabled;");
					$testedA = true;
				} else {
					$this->assertContains($disabled_text, $savedInstanceInnerTaxonomyForm, "For $post_type_name the $taxonomy should be disabled;");
					$testedB = true;
				}
			}
		}

		$this->assertTrue($testedA);
		$this->assertTrue($testedB);
	}
}
