<?php

require_once '_Widget_test.php';

class IasdListaDePostsUpdateAndValidateMethod_test extends IASD_ListaDePosts__Widget_test {

	function setUp() {
		parent::setUp();
		
		$this->registerDSA();
	}

	function testAfterSaveHasFieldsFromDefaultInstance() {
		$widget = new IASD_ListaDePosts();

		$assetDefaultInstance = IASD_ListaDePosts::DefaultInstance();
		$this->assertGreaterThan(0, count($assetDefaultInstance));

		$validatedInstance = IASD_ListaDePosts::Validate(array(), array());

		$updatedInstance = $widget->update(array('number' => $this->getWidgetNumber()), array());

		/// TAXONOMY QUERY
		$this->assertTrue(isset($validatedInstance['taxonomy_query']), 'Validated instance has a TaxonomyQuery');
		$this->assertTrue(isset($validatedInstance['taxonomy_query'][IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS]), 'And it is for xtt-pa-sedes');
		$validatedInstance['taxonomy_query'] = array();

		$this->assertTrue(isset($updatedInstance['taxonomy_query']), 'Updated instance has a TaxonomyQuery');
		$this->assertTrue(isset($updatedInstance['taxonomy_query'][IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS]), 'And it is for xtt-pa-sedes');
		$updatedInstance['taxonomy_query'] = array();

		/// NUMBER
		$this->assertTrue(isset($validatedInstance['number']), 'Validated instance has a NUMBER');
		$this->assertEquals(0, $validatedInstance['number'], 'And it is ZERO');

		$this->assertTrue(isset($updatedInstance['number']), 'Updated instance has a NUMBER');
		$this->assertTrue($updatedInstance['number'] !== 0, 'And it is NOT ZERO');
		$updatedInstance['number'] = 0;

		/// SAVED
		$this->assertTrue(isset($validatedInstance['saved']), 'Validated instance has a SAVED');
		$this->assertEquals(0, $validatedInstance['saved'], 'And it is ZERO');

		$this->assertTrue(isset($updatedInstance['saved']), 'Updated instance has a SAVED');
		$this->assertTrue($updatedInstance['saved'] !== 0, 'And it is NOT ZERO');
		$updatedInstance['saved'] = 0;

		/// SIDEBAR
		$this->assertArrayHasKey('sidebar', $validatedInstance, 'Validated instance has a SIDEBAR');
		$this->assertEmpty($validatedInstance['sidebar'], 'And it is EMPTY');
		$validatedInstance['sidebar'] = false;

		$this->assertArrayHasKey('sidebar', $updatedInstance, 'Updated instance has a SIDEBAR');
		$this->assertNull($updatedInstance['sidebar'], 'And it is NULL, cause it has no sidebar set');
		$updatedInstance['sidebar'] = false;

		/// SECRET
		$this->assertTrue(isset($assetDefaultInstance['secret']), 'Validated instance has a Secret');
		$this->assertNotEmpty($assetDefaultInstance['secret'], 'And is not empty');
		$assetDefaultInstance['secret'] = '';

		$this->assertTrue(isset($validatedInstance['secret']), 'Validated instance has a Secret');
		$this->assertNotEmpty($validatedInstance['secret'], 'And is not empty');
		$validatedInstance['secret'] = '';

		$this->assertTrue(isset($updatedInstance['secret']), 'Updated instance has a Sected');
		$this->assertNotEmpty($updatedInstance['secret'], 'And is not empty');
		$updatedInstance['secret'] = '';

		//Finally: Everything is equals :)

		$this->assertEquals($assetDefaultInstance, $validatedInstance);
		$this->assertEquals($validatedInstance, $updatedInstance);
		$this->assertEquals($updatedInstance, $assetDefaultInstance);
	}

	function testAfterSaveHasDesiredFields() {
		$widget = new IASD_ListaDePosts();
		$widget->_set($this->getWidgetNumber());
		$updatedInstance = $widget->update(array('number' => $widget->number), array());

		$tests = 0;
		$this->assertArrayHasKey('authors',$updatedInstance); $tests++;
		$this->assertArrayHasKey('authors_norepeat',$updatedInstance); $tests++;
		$this->assertArrayHasKey('fixed_ids',$updatedInstance); $tests++;
		$this->assertArrayHasKey('grouping_taxonomy',$updatedInstance); $tests++;
		$this->assertArrayHasKey('grouping_forced',$updatedInstance); $tests++;
		$this->assertArrayHasKey('meta_query',$updatedInstance); $tests++;
		$this->assertArrayHasKey('number',$updatedInstance); $tests++;
		$this->assertArrayHasKey('orderby',$updatedInstance); $tests++;
		$this->assertArrayHasKey('order',$updatedInstance); $tests++;
		$this->assertArrayHasKey('post_status',$updatedInstance); $tests++;
		$this->assertArrayHasKey('posts_per_page',$updatedInstance); $tests++;
		$this->assertArrayHasKey('post_type',$updatedInstance); $tests++;
		$this->assertArrayHasKey('saved',$updatedInstance); $tests++;
		$this->assertArrayHasKey('secret',$updatedInstance); $tests++;
		$this->assertArrayHasKey('seemore',$updatedInstance); $tests++;
		$this->assertArrayHasKey('seemore_text',$updatedInstance); $tests++;
		$this->assertArrayHasKey('sidebar',$updatedInstance); $tests++;
		$this->assertArrayHasKey('source_id',$updatedInstance); $tests++;
		$this->assertArrayHasKey('taxonomy_query',$updatedInstance); $tests++;
		$this->assertArrayHasKey('taxonomy_norepeat',$updatedInstance); $tests++;
		$this->assertArrayHasKey('title',$updatedInstance); $tests++;
		$this->assertArrayHasKey('view',$updatedInstance); $tests++;

		$this->assertEquals(count($updatedInstance), $tests, 'Tests made here should match the number of fields of the instance');
	}

	function testRecoverTitleIfEmpty() {
		$assetDefaultInstance = IASD_ListaDePosts::DefaultInstance();
		$validatedInstance = IASD_ListaDePosts::Validate(array(), $assetDefaultInstance);
		$assetDefaultInstance['title'] = 'It has a title';

		$validatedInstance = IASD_ListaDePosts::Validate(array('title' => ''), $assetDefaultInstance);
		$this->assertNotEquals($validatedInstance, $assetDefaultInstance);
		$this->assertEquals('It has a title', $validatedInstance['title']);
	}

	function testTaxonomySedesRegionaisFilterIsCreated(){
		$widget = new IASD_ListaDePosts();

		$validatedInstance = $widget->update(array(), array());
		$this->assertArrayHasKey('taxonomy_query', $validatedInstance);

		$taxonomyQueries = $validatedInstance['taxonomy_query'];
		$this->assertArrayHasKey(IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS, $taxonomyQueries);

		$taxQuerySedes = $validatedInstance['taxonomy_query'][IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS];

		$this->assertArrayHasKey('field', $taxQuerySedes);
		$this->assertArrayHasKey('terms', $taxQuerySedes);

		$this->assertEquals('slug', $taxQuerySedes['field']);
		$this->assertContains('dsa', $taxQuerySedes['terms']);
	}

	function testForDisabledFields() {
		$widget = new IASD_ListaDePosts();
		$oldInstance = $widget->update(array(), array());
		$oldInstance['seemore'] = 1;

		$oldInstance['grouping_forced'] = 1;
		$oldInstance['authors_norepeat'] = 1;
		$oldInstance['taxonomy_norepeat'] = array(IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS);
		$newInstance = $widget->update(array('number' => $this->getWidgetNumber(), 'title' => 'some title'), $oldInstance);

		$this->assertArrayHasKey('seemore', $newInstance);
		$this->assertEquals(0, $newInstance['seemore']);

		$this->assertArrayHasKey('grouping_forced', $newInstance);
		$this->assertEquals(0, $newInstance['grouping_forced']);

		$this->assertArrayHasKey('authors_norepeat', $newInstance);
		$this->assertEquals(0, $newInstance['authors_norepeat']);

		$this->assertArrayHasKey('taxonomy_norepeat', $newInstance);
		$this->assertEquals(array(), $newInstance['taxonomy_norepeat']);

		$this->assertArrayHasKey('taxonomy_query', $newInstance);
		$this->assertArrayHasKey(IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS, $newInstance['taxonomy_query']);


		$this->assertArrayHasKey('post_type', $newInstance);
		$this->assertNotEmpty($newInstance['taxonomy_query']);
		$this->assertArrayHasKey('source_id', $newInstance);
		$this->assertNotEmpty($newInstance['source_id']);

		$widget = new IASD_ListaDePosts();
		$oldInstance = $widget->update(array(), array());
		$oldInstance['seemore'] = 1;
		$oldInstance['grouping_forced'] = 1;
		$oldInstance['authors_norepeat'] = 1;
		$oldInstance['taxonomy_norepeat'] = array(IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS);
		unset($oldInstance['secret']);
		$newInstance = $widget->update(array('number' => $this->getWidgetNumber(), 'title' => 'some title', 'grouping_forced' => 1), $oldInstance);

		$this->assertArrayHasKey('grouping_forced', $newInstance);
		$this->assertEquals(0, $newInstance['grouping_forced'], 'The field should be remoced once there`s no grouping taxonomy');
		$this->assertNotEmpty($newInstance['secret']);
	}
}



