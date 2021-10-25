<?php

require_once 'FormContent__test.php';

class IASD_ListaDePosts_FormContent_General_test extends IASD_ListaDePosts_FormContent__test {

	function test_formContent() {

		$widget = new IASD_ListaDePosts();
		$widget->number = 77;

		$basicInstance = IASD_ListaDeposts::DefaultInstance();
		$savedInstance = $widget->update($basicInstance, array());
		ob_start();
		$widget->formContent($savedInstance);
		$savedInstanceForm = ob_get_contents();
		ob_end_clean();

	/**
		Redundancy tests.
		The tests here should also be inside IASD_ListaDePostsWidget_General_Test::test_afterSave
	*/

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
