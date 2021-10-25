<?php
/** 
	PARA EXECUTAR O SCRIPT, RODE NO TERMINAL (com wpcli instalado): 
 	wp eval-file criaPaginas.php
*/

criaPaginas();

function criaPaginas() {
	ob_start();

	$args = array(
		'taxonomy' => 'xtt-pa-sedes'
	);
	$sedes = get_terms($args);
	$user = get_user_by( 'email', 'suporte@internetdsa.com' );
	
	echo "\n\n";
	echo "POSTS A PROCESSAR: ". count($sedes);
	echo "\n\n";

	foreach ($sedes as &$sede){
		sleep(0.5);


		$post_content = '
			<!-- wp:acf/p-a-row {"id":"block_615ef0a36cd7b","name":"acf/p-a-row","align":"","mode":"preview","wpClassName":"wp-block-acf-p-a-row"} -->
			<!-- wp:acf/p-a-carousel-videos {"id":"block_6172bf622df3b","name":"acf/p-a-carousel-videos","data":{"field_c305e364":"Últimos Vídeos","field_7f90ccc9":"latest","field_db8e6120":{"manual":"","sticky":"","limit":"10","taxonomies":"[\u0022xtt-pa-sedes\u0022]","terms":"[[\u0022'. $sede->slug .'\u0022]]"}},"align":"","mode":"edit","wpClassName":"wp-block-acf-p-a-carousel-videos"} /-->
			<!-- /wp:acf/p-a-row -->
			';
		
		// if ($count == 1){
		// 	break;
		// }

		// $args = new stdClass();
		// $args->post_title = $sede->name
        // $args->post_content = $updatedContent;
		$count++;
		echo $count ." - ". $sede->slug ." - ". $sede->name ."\n";

		$post_data = array(
			'post_title'    => $sede->name,
			'post_content'  => wp_slash($post_content),
			'post_name'		=> $sede->slug,
			'post_status'   => 'publish',
			'post_author'   => $user->ID,
			'post_type'     => 'page',
			'page_template'	=> 'page-front-page.blade.php',
		);
		
		wp_insert_post( $post_data, $error_obj );

	}
}


#aa4b07