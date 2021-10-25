<?php

require_once 'FormAppearance__test.php';

class IASD_ListaDePosts_FormAppearance_SeeMore_test extends IASD_ListaDePosts_FormAppearance__test {

	function testAllowSeeMore() {
		$widget = new IASD_ListaDePosts();
		$basicInstance = IASD_ListaDePosts::DefaultInstance();
		$basicInstance['view'] = 'COL-MD-8';
		$basicInstance['number'] = $widget->number = $this->getWidgetNumber();

		$widget->_setInstance($basicInstance);

		$this->assertTrue($widget->mayHaveSeeMore());

		ob_start();
		$widget->formAppearanceSeeMore();
		$formAppearanceSeeMore = ob_get_contents();
		ob_end_clean();

		$formAppearanceSeeMore = trim(preg_replace('/\s\s+/', ' ', $formAppearanceSeeMore));

		$this->assertNotContains('<fieldset class="iasdlistadeposts iasd-widget-appearance-seemore-container" disabled="disabled">', $formAppearanceSeeMore);
	}

	function testDontAllowSeeMore() {
		$widget = new IASD_ListaDePosts();
		$basicInstance = IASD_ListaDePosts::DefaultInstance();
		$basicInstance['view'] = 'COL-MD-4';
		$basicInstance['number'] = $widget->number = $this->getWidgetNumber();

		$widget->_setInstance($basicInstance);

		$this->assertFalse($widget->mayHaveSeeMore());

		ob_start();
		$widget->formAppearanceSeeMore();
		$formAppearanceSeeMore = ob_get_contents();
		ob_end_clean();

		$formAppearanceSeeMore = trim(preg_replace('/\s\s+/', ' ', $formAppearanceSeeMore));

		$this->assertContains('<fieldset class="iasdlistadeposts iasd-widget-appearance-seemore-container" disabled="disabled">', $formAppearanceSeeMore);
	}

	function testDefaultFormDisplay() {
		$widget = new IASD_ListaDePosts();
		$basicInstance = IASD_ListaDePosts::DefaultInstance();
		$basicInstance['view'] = 'COL-MD-8';
		$basicInstance['number'] = $widget->number = $this->getWidgetNumber();

		$widget->_setInstance($basicInstance);

		ob_start();
		$widget->formAppearanceSeeMore();
		$formAppearanceSeeMore = ob_get_contents();
		ob_end_clean();
		//remove line break
		$formAppearanceSeeMore = trim(preg_replace('/\s\s+/', ' ', $formAppearanceSeeMore));

		$this->assertContains($widget->get_field_id('seemore'), $formAppearanceSeeMore, 'SEEMORE Field should have ID');
		$this->assertContains($widget->get_field_name('seemore'), $formAppearanceSeeMore, 'SEEMORE Field should have NAME');

		$expectToBeRendered = '<input class="iasd-widget-appearance-seemore" id="'.$widget->get_field_id('seemore').'" name="'.$widget->get_field_name('seemore').'" type="checkbox" value="1"';
		$this->assertContains($expectToBeRendered, $formAppearanceSeeMore, "The field should appear");
		$expectToBeRendered .= ' checked="checked" />';
		$this->assertContains($expectToBeRendered, $formAppearanceSeeMore, "The field should be checked");

		$this->assertContains($widget->get_field_id('seemore_text'), $formAppearanceSeeMore, 'SEEMORE_TEXT Field should have ID');
		$this->assertContains($widget->get_field_name('seemore_text'), $formAppearanceSeeMore, 'SEEMORE_TEXT Field should have ID');

		$expectToBeRendered = '<input id="'.$widget->get_field_id('seemore_text').'" name="'.$widget->get_field_name('seemore_text').'" type="text" value="Veja mais" class="iasd-widget-appearance-seemore-text mandatory widefat" />';
		$this->assertContains($expectToBeRendered, $formAppearanceSeeMore);
	}

	function testDisabledFormDisplay() {
		$widget = new IASD_ListaDePosts();
		$basicInstance = IASD_ListaDePosts::DefaultInstance();
		$basicInstance['seemore'] = false;
		$basicInstance['view'] = 'COL-MD-8';
		$basicInstance['number'] = $widget->number = $this->getWidgetNumber();

		$widget->_setInstance($basicInstance);

		ob_start();
		$widget->formAppearanceSeeMore();
		$formAppearanceSeeMore = trim(preg_replace('/\s\s+/', ' ', ob_get_contents()));
		ob_end_clean();

		$expectToBeRendered = ' type="checkbox" value="1" />';
		$this->assertContains($expectToBeRendered, $formAppearanceSeeMore, "The field should NOT be checked");

		$expectToBeRendered = ' value="Veja mais" class="iasd-widget-appearance-seemore-text mandatory widefat" readonly="readonly" />';
		$this->assertContains($expectToBeRendered, $formAppearanceSeeMore);
	}

	function testTextIsDifferent() {
		$widget = new IASD_ListaDePosts();
		$basicInstance = IASD_ListaDePosts::DefaultInstance();
		$basicInstance['number'] = $widget->number = $this->getWidgetNumber();
		$basicInstance['seemore_text'] = 'abc';
		$widget->_setInstance($basicInstance);

		ob_start();
		$widget->formAppearanceSeeMore();
		$formAppearanceSeeMore = ob_get_contents();
		ob_end_clean();
		//remove line break
		$formAppearanceSeeMore = trim(preg_replace('/\s\s+/', ' ', $formAppearanceSeeMore));

		$expectToBeRendered = ' type="text" value="abc" class="iasd-widget-appearance-seemore-text mandatory widefat" />';
		$this->assertContains($expectToBeRendered, $formAppearanceSeeMore, 'Text should be different');
	}

	function testTextIsDifferentButFieldIsDisabled() {
		$widget = new IASD_ListaDePosts();
		$basicInstance = IASD_ListaDePosts::DefaultInstance();
		$basicInstance['number'] = $widget->number = $this->getWidgetNumber();
		$basicInstance['seemore'] = false;
		$basicInstance['seemore_text'] = 'abc';
		$widget->_setInstance($basicInstance);

		ob_start();
		$widget->formAppearanceSeeMore();
		$formAppearanceSeeMore = ob_get_contents();
		ob_end_clean();
		//remove line break
		$formAppearanceSeeMore = trim(preg_replace('/\s\s+/', ' ', $formAppearanceSeeMore));

		$expectToBeRendered = ' type="checkbox" value="1" />';
		$this->assertContains($expectToBeRendered, $formAppearanceSeeMore, 'Should not be checked');

		$expectToBeRendered = ' type="text" value="abc" class="iasd-widget-appearance-seemore-text mandatory widefat" readonly="readonly" />';
		$this->assertContains($expectToBeRendered, $formAppearanceSeeMore, 'Text should be different BUT readonly');
	}
}