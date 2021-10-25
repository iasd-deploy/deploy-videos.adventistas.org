<?php

class IASD_OutputFormat_text  extends WP_UnitTestCase {

	function setUp() {
		parent::setUp();
	}

	function test_hasFiltersSet() {
		$this->assertTrue(class_exists('IASD_OutputFormat'));

		$filter = has_filter('template_include', array('IASD_OutputFormat', 'TemplateInclude'));

		$this->assertEquals(9999, $filter);

		$filter2 = has_filter('wp_headers', array('IASD_OutputFormat', 'WPHeaders'));
		$this->assertEquals(10, $filter2);
	}

	function test_parameterValues() {
		$template = apply_filters('template_include', 'no-template-set');
		$this->assertNotContains('iasd_output_format_json', $template);

		$_GET[IASD_OutputFormat::PARAMETER] = 'nothing';
		$template = apply_filters('template_include', 'no-template-set');
		$this->assertNotContains('iasd_output_format_json', $template);

		$_GET[IASD_OutputFormat::PARAMETER] = 'json';
		$template = apply_filters('template_include', 'no-template-set');
		$this->assertContains('iasd_output_format_json', $template);

		$_GET[IASD_OutputFormat::PARAMETER] = 'everything';
		$template = apply_filters('template_include', 'no-template-set');
		$this->assertNotContains('iasd_output_format_json', $template);
		unset($_GET[IASD_OutputFormat::PARAMETER]);
	}

	function test_headerValues() {
		$headers = apply_filters('wp_headers', array());
		$this->assertEquals(0, count($headers));

		$_GET[IASD_OutputFormat::PARAMETER] = 'nothing';
		$headers = apply_filters('wp_headers', array());
		$this->assertEquals(0, count($headers));

		$_GET[IASD_OutputFormat::PARAMETER] = 'json';
		$headers = apply_filters('wp_headers', array());
		$this->assertNotEquals(0, count($headers));

		$_GET[IASD_OutputFormat::PARAMETER] = 'everything';
		$headers = apply_filters('wp_headers', array());
		$this->assertEquals(0, count($headers));
		unset($_GET[IASD_OutputFormat::PARAMETER]);
	}

	function fakeMetas() {
		return array('wp_headers');
	}

	function test_OutputRendering() {
		add_filter('IASD_ListaDePosts::AvailablePostTypes::PostMeta', array($this, 'fakeMetas'));
		$post_id = wp_insert_post(array('post_title' => 'Output Rendering Test', 'post_excerpt' => 'Excerpt Rendering', 'post_status' => 'publish'));
		$post = get_post($post_id);
		$wp_headers = md5(time());
		add_post_meta($post_id, 'wp_headers', $wp_headers);
		$category = 'cat_'.time();
		$category_id = wp_create_category($category);
		wp_set_object_terms( $post_id, $category_id, 'category', 'true');

		global $wp_the_query;
		$wp_the_query->query(array('p' => $post_id));

		$this->assertTrue($wp_the_query->have_posts());

		$_GET[IASD_OutputFormat::PARAMETER] = 'json';
		$template = apply_filters('template_include', 'no-template-set');
		ob_start();
		include $template;
		$rendered = ob_get_contents();
		ob_get_clean();
		unset($_GET[IASD_OutputFormat::PARAMETER]);

		$this->assertGreaterThan(0, strlen($rendered));

		$this->assertContains('Output Rendering Test', $rendered);
		$this->assertContains('Excerpt Rendering', $rendered);
		$this->assertContains('wp_headers', $rendered);
		$this->assertContains($wp_headers, $rendered);
		$this->assertContains('category', $rendered);
		$this->assertContains($category, $rendered);

/*
		$headers = apply_filters('wp_headers', array());
		$this->assertEquals(0, count($headers));

		$_GET[IASD_OutputFormat::PARAMETER] = 'nothing';
		$headers = apply_filters('wp_headers', array());
		$this->assertEquals(0, count($headers));

		$_GET[IASD_OutputFormat::PARAMETER] = 'json';
		$headers = apply_filters('wp_headers', array());
		$this->assertNotEquals(0, count($headers));

		$_GET[IASD_OutputFormat::PARAMETER] = 'everything';
		$headers = apply_filters('wp_headers', array());
		$this->assertEquals(0, count($headers));*/


		remove_filter('IASD_ListaDePosts::AvailablePostTypes::PostMeta', array($this, 'fakeMetas'));
	}

}