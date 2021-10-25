<?php

class IASD_RevistaAdventistaController {

	public static function init_post_type() {
		$labels = array(
			'name' => __( 'Revistas', 'iasd' ),
			'singular_name' => __( 'Revista', 'iasd' ),
			'add_new' => __( 'Adicionar artigo/revista', 'iasd' ),
			'add_new_item' => __( 'Adicionar novo artigo/revista', 'iasd' ),
			'edit_item' => __( 'Editar artigo/revista', 'iasd' ),
			'new_item' => __( 'Novo artigo/revista', 'iasd' ),
			'view_item' => __( 'Visualizar artigo/revista', 'iasd' ),
			'search_items' => __( 'Buscar artigo/revista', 'iasd' ),
		);


		$args = array(
			'map_meta_cap' => true,
			'labels' => $labels,
			'public' => true,
			'capability_type' => array('post', 'posts'),
			'has_archive' => true,
			'hierarchical' => true,
			'rewrite' => array(
				'slug' => 'revista',
			),
			'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'comments', 'page-attributes'),
			'menu_position' => 7775,
		);

		register_post_type( 'revista-adventista', $args );
		self::register_sidebar();

	}

	public static function filter_only_post_parent_on_archive( $query ) {
		if ( ! is_admin() && is_post_type_archive( 'revista-adventista' ) ) {
	    	$query->set( 'post_parent', '0' );
	  	}
	}

	public static function template_proxy_for_single( $single_template ){
		global $post;

		if ( ($post->post_type == 'revista-adventista') && ($post->post_parent == 0) ) {
     		return PAPU_VIEW . '/revista_adventista/template-edicao-revista-adventista.php';
		} else if ( $post->post_type == 'revista-adventista' ) {
     		return PAPU_VIEW . '/revista_adventista/single-revista-adventista.php';
     	}

		return $single_template;
	}

	public static function template_proxy_for_archive( $archive_template ){
		global $post;
		if ( is_post_type_archive( 'revista-adventista' ) ) {
     		return PAPU_VIEW . '/revista_adventista/archive-revista-adventista.php';
     	}

		return $archive_template;
	}

	public static function register_sidebar() {
		$opts = array( 
			'name' => __( 'Revista', 'iasd' ),
			'id' => 'revista',
			'col_class' => 'col-md-4',
			'description' => __( 'Sidebar de colunagem 1/3', 'iasd' ),
		);
		do_action( 'iasd_register_sidebar', $opts );
	}

}

if( apply_filters( 'IASD_RevistaAdventista_enabled', true ) ) {

	add_action( 'init', array('IASD_RevistaAdventistaController', 'init_post_type') );
	add_action( 'pre_get_posts', array('IASD_RevistaAdventistaController', 'filter_only_post_parent_on_archive') );
	add_filter( 'single_template', array('IASD_RevistaAdventistaController', 'template_proxy_for_single') );
	add_filter( 'archive_template', array('IASD_RevistaAdventistaController', 'template_proxy_for_archive') );

}
