<?php

require_once 'FormContent__test.php';

class IASD_ListaDePostsWidget_FormContent_Taxonomies_test extends IASD_ListaDePosts_FormContent__test {

	function testBasicStructure() {
		$widget = new IASD_ListaDePosts();
		$widget->number = $this->getWidgetNumber();
		$basicInstance = IASD_ListaDeposts::DefaultInstance();
		$savedInstance = $widget->update($basicInstance, array());

		$widget->_setInstance($savedInstance);
		ob_start();
		$widget->formContentTaxonomies();
		$savedInstanceForm = ob_get_contents();
		ob_end_clean();

		//Taxonomies
		$taxonomies = IASD_Taxonomias::GetAllTaxonomies();
		foreach($taxonomies as $taxonomy_slug) {
			$taxonomy_slug_field = $widget->get_field_id($taxonomy_slug);
			$taxonomy = get_taxonomy($taxonomy_slug);

			$this->assertContains($taxonomy->label, $savedInstanceForm);
			$this->assertContains($taxonomy_slug_field . '-show', $savedInstanceForm);
			$this->assertContains($taxonomy_slug_field . '-hide', $savedInstanceForm);
			$this->assertContains($taxonomy_slug_field . '-list', $savedInstanceForm);

			//Has X checkboxes, where X is the name of terms in the taxonomy
			$termCount = wp_count_terms($taxonomy_slug, array('hide_empty' => false));
			$termsFieldName = $widget->get_field_name('taxonomy_query]['.$taxonomy_slug.'][terms');
			$substrCount = substr_count($savedInstanceForm, $termsFieldName);
			$this->assertEquals($termCount, $substrCount);
		}

		//NoRepeat
		$this->assertEquals(count($taxonomies), substr_count($savedInstanceForm, IASD_ListaDePosts::form_id . '-'.$widget->number.'-taxonomy_norepeat'));
	}
	function testIfHasCheckboxes() {
		$this->addFakePosts();
		$widget = new IASD_ListaDePosts();
		$widget->number = $this->getWidgetNumber();
		$basicInstance = IASD_ListaDeposts::DefaultInstance();
		$savedInstance = $widget->update($basicInstance, array());

		$widget->_setInstance($savedInstance);
		ob_start();
		$widget->formContentTaxonomies();
		$savedInstanceForm = ob_get_contents();
		ob_end_clean();

		//Ensures that, by default, the DSA term is checked
		$term_dsa_checked = 'id="'.$widget->get_field_id('taxonomy_query-'.IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS.'-terms') . '-dsa" checked=\'checked\'';
		$this->assertContains($term_dsa_checked, $savedInstanceForm);

		foreach($this->taxonomies as $taxonomy_slug => $terms) {
			$testingStance = $savedInstance;
			$testingStance['taxonomy_query'][$taxonomy_slug]['terms'] = $terms;
			$widget->_setInstance($testingStance);
			ob_start();
			$widget->formContentTaxonomies();
			$testingStanceForm = ob_get_contents();
			ob_end_clean();


			foreach($terms as $term) {
				$term_dsa_checked = 'id="'.$widget->get_field_id('taxonomy_query-'.$taxonomy_slug.'-terms') . '-' . $term . '" checked=\'checked\'';
				$this->assertContains($term_dsa_checked, $testingStanceForm);
			}
		}
	}
}
