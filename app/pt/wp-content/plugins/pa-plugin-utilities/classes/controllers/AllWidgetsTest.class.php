<?php


class AllWidgetsTest {
	public static function TemplateLoader() {
		return PAPU_VIEW.DIRECTORY_SEPARATOR.'AllWidgetsTest.php';
	}
}

add_filter( 'template_include', array('AllWidgetsTest', 'TemplateLoader') );
