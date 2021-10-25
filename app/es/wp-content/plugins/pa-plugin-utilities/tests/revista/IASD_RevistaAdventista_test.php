<?php


class IASD_RevistaAdventista_test extends WP_UnitTestCase {

	function test_if_hooks_were_applied(){
		$this->assertGreaterThan( 0, has_action( 'init', array( 'IASD_RevistaAdventistaController', 'init_post_type' ) ), 'IASD_RevistaAdventistaController::init_post_type' );
		$this->assertGreaterThan( 0, has_action( 'pre_get_posts', array( 'IASD_RevistaAdventistaController', 'filter_only_post_parent_on_archive' ) ), 'IASD_RevistaAdventistaController::filter_only_post_parent_on_archive' );
		$this->assertGreaterThan( 0, has_filter( 'single_template', array( 'IASD_RevistaAdventistaController', 'template_proxy_for_single' ) ), 'IASD_RevistaAdventistaController::template_proxy_for_single' );
		$this->assertGreaterThan( 0, has_filter( 'archive_template', array( 'IASD_RevistaAdventistaController', 'template_proxy_for_archive' ) ), 'IASD_RevistaAdventistaController::template_proxy_for_archive' );
	}

	// init method is executed by tests by default
	function test_if_taxonomy_registered_on_init() {
		$this->assertTrue( post_type_exists( 'revista-adventista' ) );

		$pt = get_post_type_object( 'revista-adventista' );
		$this->assertInternalType( 'array', $pt->rewrite );
		$this->assertEquals( 'revista', $pt->rewrite['slug'] );
		$this->assertEquals( 'page', $pt->capability_type );
		$this->assertTrue( $pt->has_archive );
	}

	// init method is executed by tests by default
	function test_if_sidebar_registered_on_init(){
		global $wp_registered_sidebars;
		$this->assertTrue( is_dynamic_sidebar( 'revista' ) );
		$this->assertArrayHasKey( 'revista', $wp_registered_sidebars );
	}

	function test_if_only_parents_are_listed_on_archive(){
		global $wp_the_query;
  		
		$p1 = $this->factory->post->create_object( array( 'post_title' => 'Parent 1', 'post_type' => 'revista-adventista' ) );
		$p2 = $this->factory->post->create_object( array( 'post_title' => 'Parent 2', 'post_type' => 'revista-adventista' ) );

		$this->factory->post->create_many( 5, array( 'post_type' => 'revista-adventista', 'post_parent' => $p1 ) );
		$this->factory->post->create_many( 6, array( 'post_type' => 'revista-adventista', 'post_parent' => $p2 ) );
		
		// var_dump();
		$this->go_to(get_post_type_archive_link('revista-adventista'));

		// $post = $wp_query->get_queried_object();
		// var_dump($wp_the_query);

		do_filter('archive_template', 'teste');

		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	function test_if_uses_archive_view_from_plugin(){
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	function test_if_uses_single_view_from_plugin(){
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

}
