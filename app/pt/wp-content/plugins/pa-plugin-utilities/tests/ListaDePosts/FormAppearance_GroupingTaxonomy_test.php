<?php

require_once 'FormAppearance__test.php';

class IASD_ListaDePosts_FormAppearance_GroupingTaxonomy_test extends IASD_ListaDePosts_FormAppearance__test {

	function setUp() {
		parent::setUp();
		IASD_ListaDePosts_Views::RegisterFakes();
	}

	function testViewAllowConfig() {
		$widget = new IASD_ListaDePosts();
		$basicInstance = IASD_ListaDePosts::DefaultInstance();
		$basicInstance['view'] = 'COL-MD-8';
		$basicInstance['number'] = $widget->number = $this->getWidgetNumber();

		$widget->_setInstance($basicInstance);

		$this->assertTrue($widget->mayHaveConfig());

		ob_start();
		$widget->formAppearanceGroupingTaxonomy();
		$formAppearanceGroupingTaxonomy = ob_get_contents();
		ob_end_clean();

		$formAppearanceGroupingTaxonomy = trim(preg_replace('/\s\s+/', ' ', $formAppearanceGroupingTaxonomy));

		$this->assertNotContains('<fieldset class="iasdlistadeposts iasd-widget-appearance-grouping_taxonomy-container" disabled="disabled">', $formAppearanceGroupingTaxonomy);

		$this->assertContains($widget->get_field_id('grouping_taxonomy'), $formAppearanceGroupingTaxonomy);
		$this->assertContains($widget->get_field_name('grouping_taxonomy'), $formAppearanceGroupingTaxonomy);

		$expectToBeRendered = '<select class="widefat iasd-widget mandatory iasd-widget-appearance-grouping_taxonomy" id="'.$widget->get_field_id('grouping_taxonomy').'" name="'.$widget->get_field_name('grouping_taxonomy').'">';
		$this->assertContains($expectToBeRendered, $formAppearanceGroupingTaxonomy);
	}

	function testViewDontAllowConfig() {
		$widget = new IASD_ListaDePosts();
		$basicInstance = IASD_ListaDePosts::DefaultInstance();
		$basicInstance['view'] = 'COL-MD-4';
		$basicInstance['number'] = $widget->number = $this->getWidgetNumber();

		$widget->_setInstance($basicInstance);

		$this->assertFalse($widget->mayHaveConfig());

		ob_start();
		$widget->formAppearanceGroupingTaxonomy();
		$formAppearanceGroupingTaxonomy = ob_get_contents();
		ob_end_clean();

		$formAppearanceGroupingTaxonomy = trim(preg_replace('/\s\s+/', ' ', $formAppearanceGroupingTaxonomy));

		$this->assertContains('<fieldset class="iasdlistadeposts iasd-widget-appearance-grouping_taxonomy-container" disabled="disabled">', $formAppearanceGroupingTaxonomy);
	}

	function testDefaultAvailableTaxonomies() {
		$widget = new IASD_ListaDePosts();
		$basicInstance = IASD_ListaDePosts::DefaultInstance();
		$basicInstance['view'] = 'COL-MD-8';
		$basicInstance['number'] = $widget->number = $this->getWidgetNumber();
		$widget->_setInstance($basicInstance);

		$availableTaxonomies = $widget->getAvailableTaxonomies();

		ob_start();
		$widget->formAppearanceGroupingTaxonomy();
		$formAppearanceGroupingTaxonomy = ob_get_contents();
		ob_end_clean();
		//remove line break
		$formAppearanceGroupingTaxonomy = trim(preg_replace('/\s\s+/', ' ', $formAppearanceGroupingTaxonomy));

		$this->assertEquals(1, substr_count($formAppearanceGroupingTaxonomy, '<option'), 'Apenas o não permitir');
	}

	function testAvailableTaxonomiesWithSelectedTaxonomies() {
		$widget = new IASD_ListaDePosts();
		$basicInstance = IASD_ListaDePosts::DefaultInstance();
		$basicInstance['view'] = 'COL-MD-8';
		$basicInstance['number'] = $widget->number = $this->getWidgetNumber();
		$basicInstance['taxonomy_query'] = array('category' => array(), 'post_tag' => array());
		$widget->_setInstance($basicInstance);

		ob_start();
		$widget->formAppearanceGroupingTaxonomy();
		$formAppearanceGroupingTaxonomy = ob_get_contents();
		ob_end_clean();
		//remove line break
		$formAppearanceGroupingTaxonomy = trim(preg_replace('/\s\s+/', ' ', $formAppearanceGroupingTaxonomy));

		$this->assertEquals(3, substr_count($formAppearanceGroupingTaxonomy, '<option'), 'Includes the selected taxonomies');
		$this->assertContains('post_tag', $formAppearanceGroupingTaxonomy);
		$this->assertContains('category', $formAppearanceGroupingTaxonomy);
	}

	function testAvailableTaxonomiesWithInvalidSelectedTaxonomies() {
		$widget = new IASD_ListaDePosts();
		$basicInstance = IASD_ListaDePosts::DefaultInstance();
		$basicInstance['view'] = 'COL-MD-8';
		$basicInstance['number'] = $widget->number = $this->getWidgetNumber();
		$basicInstance['taxonomy_query'] = array('categoryx' => array(), 'post_tagx' => array());
		$widget->_setInstance($basicInstance);

		ob_start();
		$widget->formAppearanceGroupingTaxonomy();
		$formAppearanceGroupingTaxonomy = ob_get_contents();
		ob_end_clean();
		//remove line break
		$formAppearanceGroupingTaxonomy = trim(preg_replace('/\s\s+/', ' ', $formAppearanceGroupingTaxonomy));

		$this->assertEquals(1, substr_count($formAppearanceGroupingTaxonomy, '<option'), 'Includes the selected taxonomies');
	}

	function testAvailableTaxonomiesWithOneSelected() {
		$widget = new IASD_ListaDePosts();
		$basicInstance = IASD_ListaDePosts::DefaultInstance();
		$basicInstance['view'] = 'COL-MD-8';
		$basicInstance['number'] = $widget->number = $this->getWidgetNumber();
		$basicInstance['taxonomy_query'] = array('category' => array(), 'post_tag' => array());
		$basicInstance['grouping_taxonomy'] = 'category';

		$widget->_setInstance($basicInstance);
		$availableTaxonomies = $widget->getAvailableTaxonomies();

		ob_start();
		$widget->formAppearanceGroupingTaxonomy();
		$formAppearanceGroupingTaxonomy = ob_get_contents();
		ob_end_clean();
		//remove line break
		$formAppearanceGroupingTaxonomy = trim(preg_replace('/\s\s+/', ' ', $formAppearanceGroupingTaxonomy));

		$this->assertEquals(1, substr_count($formAppearanceGroupingTaxonomy, 'selected="selected"'));
	}
}



