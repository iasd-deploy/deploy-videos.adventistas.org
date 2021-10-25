<?php

require_once dirname(__FILE__) . '/../IASD__Master__test.php';

class IASD_ListaDePosts_SeeMore_text  extends IASD__Master__test {

	function testTheFiltersAreSet() {
		$this->assertTrue(class_exists('IASD_ListaDePosts_SeeMore'));

		$this->assertEquals(10, has_filter('request', array('IASD_ListaDePosts_SeeMore', 'Request')));
		apply_filters('request', array());
		$this->assertNotEquals(10, has_filter('request', array('IASD_ListaDePosts_SeeMore', 'Request')));
	}


	function testSeeMoreLinkCausesSearch() {
		$author = $this->addFakeAuthor();
		$post_id = wp_insert_post(array('post_title' => 'SeeMore Test', 'post_excerpt' => 'Excerpt SeeMore', 'post_status' => 'publish', 'post_author' => $author->ID));

		add_filter('request', array('IASD_ListaDePosts_SeeMore', 'Request'));
		$_GET['authors_norepeat'] = 0;
		$vars = apply_filters('request', array());
		$this->assertArrayNotHasKey('post__in', $vars);

		add_filter('request', array('IASD_ListaDePosts_SeeMore', 'Request'));
		$_GET['iasd_s'] = 0;
		$_GET['authors_norepeat'] = 0;
		$vars = apply_filters('request', array());
		$this->assertArrayNotHasKey('post__in', $vars);

		add_filter('request', array('IASD_ListaDePosts_SeeMore', 'Request'));
		$_GET['iasd_s'] = 1;
		$vars = apply_filters('request', array());
		$this->assertArrayNotHasKey('authors_norepeat', $vars);
		$this->assertArrayHasKey('post__in', $vars);

		$this->assertEquals(10, has_action('wp', array('IASD_ListaDePosts_SeeMore', 'WPRefArray')));
		global $wp_query;
		$wp_query = new WP_Query(array('p' => $post_id));

		$this->assertTrue($wp_query->have_posts());

		do_action('wp');

		global $wp_query;
		$this->assertTrue($wp_query->is_search);
		$this->assertTrue(is_search());
	}

	function testRenderTextAndLinksAsShould() {
		$widget = new IASD_ListaDePosts();
		$widget->number = $this->getWidgetNumber();
		$instance = IASD_ListaDePosts::DefaultInstance();
		$instance['number'] = $widget->number;
		$widget->_setInstance($instance);

		$rendered = $widget->widgetSeeMoreHtml();
		$this->assertContains('&iasd_s=1" title="Veja mais" class="more-link">Veja mais ', $rendered);

		$widget->number = $this->getWidgetNumber();
		$instance['number'] = $widget->number;
		$instance['seemore'] = 0;
		$widget->_setInstance($instance);
		$rendered = $widget->widgetSeeMoreHtml();
		$this->assertNotContains('&iasd_s=1" title="Veja mais" class="more-link">Veja mais ', $rendered);


		$widget->number = $this->getWidgetNumber();
		$instance['number'] = $widget->number;
		$instance['seemore'] = 1;
		$instance['seemore_text'] = 'Não veja mais';
		$widget->_setInstance($instance);

		$this->assertContains('title="Não veja mais" class="more-link">Não veja mais', $widget->widgetSeeMoreHtml('Veja mais'));

		$widget->number = $this->getWidgetNumber();
		$instance['number'] = $widget->number;
		$instance['seemore'] = 0;
		$widget->_setInstance($instance);

		$this->assertNotContains('title="Não veja mais" class="more-link">Não veja mais', $widget->widgetSeeMoreHtml('Veja mais'));
	}
}