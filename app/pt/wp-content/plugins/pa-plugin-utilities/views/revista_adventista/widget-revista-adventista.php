<div class="iasd-widget iasd-widget-slider col-md-4">
	<h1><?php _e( 'Revista', 'iasd' ); ?></h1>

	<?php
	$args = array(
		'post_type' => 'revista-adventista',
		'posts_per_page' => 3,
		'orderby' => 'date',
		'post_parent' => 0,
	);
	$revista_query = new WP_Query( $args );
	?>

	<div class="owl-carousel magazine">
<?php 
while ( $revista_query->have_posts() ): 
	$revista_query->the_post(); 
	$children_args = array(
		'post_parent' => $revista_query->post->ID,
		'numberposts' => 1,
		'tax_query' => array(
			array(
				'taxonomy' => 'xtt-pa-destaque',
				'field' => 'slug',
				'terms' => 'arquivo-principal',
			),
		)
	);
	$post_children = get_children( $children_args );
	$featured_post = array_shift( $post_children );

	$image_id = get_post_thumbnail_id();
	$image_src = wp_get_attachment_image_src( $image_id, 'thumb_145x190' );
	$image_data = wp_prepare_attachment_for_js( $image_id );
	?>
		<div class="slider-item">
			<a href="<?php the_permalink(); ?>" target="_blank" title="<?php _e( 'Clique para acessar a edição completa', 'iasd' ); ?>">
				<figure>
					<img data-src="<?php echo $image_src[0]; ?>" alt="<?php the_title(); ?>" class="lazyOwl">
				</figure>
				<h2><?php if( is_object($featured_post) ) echo $featured_post->post_title; ?></h2>
				<time><?php the_title(); ?></time>
			</a>
		</div>
<?php 
endwhile; 
wp_reset_query() 
?>
	</div>
	<div class="row">
		<div class="col-md-12">
			<a href="<?php echo get_post_type_archive_link( 'revista-adventista' ); ?>" title="<?php _e( 'Clique para ver todas as edições', 'iasd' ); ?>" class="more-link"><?php _e( 'Mais edições &raquo;', 'iasd' ); ?></a>
		</div>
	</div>
	<div class="alert alert-danger">
		<strong>Atenção!</strong> O tamanho do Widget selecionado é incompatível com esta Sidebar. Por favor, reveja suas configurações.
	</div>
</div>