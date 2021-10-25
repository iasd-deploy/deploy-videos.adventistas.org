<?php

require_once 'FormContent__test.php';

class IASD_ListaDePostsWidget_FormContent_Authors_test extends IASD_ListaDePosts_FormContent__test {

	function test_formNoContentAuthors() {
		$rules = IASD_ListaDeposts::LoadRules();

		$widget = new IASD_ListaDePosts();
		$widget->number = $this->getWidgetNumber();
		$basicInstance = IASD_ListaDeposts::DefaultInstance();

		$widget->_setInstance($basicInstance);
		ob_start();
		$widget->formContentAuthors();
		$testingStanceForm = ob_get_contents();
		ob_end_clean();
	
		$textToCheck = $widget->get_field_name('authors][');


		$this->assertContains($textToCheck, $testingStanceForm);
	}

	function test_formWithContentAuthors() {

		$user_id = wp_insert_user(array('user_login' => 'login_name', 'user_pass' => 'sdagvjdkjfhgjk@sdfuigchs'));
		update_user_meta($user_id, 'wp_user_level', 2);

		$rules = IASD_ListaDeposts::LoadRules(true);

		$widget = new IASD_ListaDePosts();
		$widget->number = $this->getWidgetNumber();
		$basicInstance = IASD_ListaDeposts::DefaultInstance();

		$widget->_setInstance($basicInstance);
		ob_start();
		$widget->formContentAuthors();
		$testingStanceForm = ob_get_contents();
		ob_end_clean();
	
		$textToCheck = $widget->get_field_name('authors][');

		$this->assertContains($textToCheck, $testingStanceForm);
		//$this->assertTrue(false, 'message');
	}

	function test_noRepeatChecked() {
		$widget = new IASD_ListaDePosts();
		$widget->number = $this->getWidgetNumber();
		$basicInstance = IASD_ListaDeposts::DefaultInstance();
		$basicInstance['authors_norepeat'] = 1;

		$widget->_setInstance($basicInstance);
		ob_start();
		$widget->formContentAuthors();
		$testingStanceForm = ob_get_contents();
		ob_end_clean();

		$testingStanceForm = trim(preg_replace('/\s\s+/', ' ', $testingStanceForm));
	
		$this->assertContains('[authors_norepeat]" type="checkbox" value="1" checked="checked" />', $testingStanceForm);
	}

	function test_noRepeatNotChecked() {
		$widget = new IASD_ListaDePosts();
		$widget->number = $this->getWidgetNumber();
		$basicInstance = IASD_ListaDeposts::DefaultInstance();
		$basicInstance['authors_norepeat'] = false;

		$widget->_setInstance($basicInstance);
		ob_start();
		$widget->formContentAuthors();
		$testingStanceForm = ob_get_contents();
		ob_end_clean();

		$testingStanceForm = trim(preg_replace('/\s\s+/', ' ', $testingStanceForm));
	
		$this->assertNotContains('[authors_norepeat]" type="checkbox" value="1" checked="checked" />', $testingStanceForm);
	}
}