<?php
/*
 * Template Name: Revista Adventista - Edição
 */

get_header();

$highlighIds = array();

?>

<!-- *************************** -->
<!-- ********* Content ********* -->
<!-- *************************** -->

<div class="container">
	<section class="row iasd-archive">
		<article class="col-md-8">
			<header>
				<h1><?php the_title(); ?></h1>
			</header>
			<?php
			$highlightFirstLevelVars = array(
				'post_type'=>$post->post_type,
				'post_parent'=>$post->ID,
				'posts_per_page'=>1,
				'tax_query'=>array( array('taxonomy' => 'xtt-pa-destaque', 'field' => 'slug','terms' => 'arquivo-principal'))
			);
			$highlightFirstLevel = new WP_Query( $highlightFirstLevelVars );

			// $highlightFirstLevelVars = $query_vars;
			// $highlightFirstLevelVars['tax_query'][] = array( 'taxonomy' => 'xtt-pa-destaque', 'field' => 'slug','terms' => 'arquivo-principal');
			// $highlightFirstLevel = new WP_Query( array( 'post_parent' => $post->ID, $highlightFirstLevelVars) );
			//var_dump($highlightFirstLevel);

			while ( $highlightFirstLevel->have_posts() ):
				$highlightFirstLevel->the_post();
				$highlighIds[] = get_the_ID();
			?>
				<a href="<?php the_permalink() ?>" title="<?php _e('Clique para ler o artigo completo', 'iasd'); ?>">
					<div class="iasd-main-highlight">
						<figure>
							<?php the_post_thumbnail('thumb_720x350'); ?>
						</figure>
						<figcaption>
							<h2><?php the_title(); ?></h2>
							<?php the_excerpt(); ?>
						</figcaption>
					</div>
				</a>				

			<?php
				endwhile;
				wp_reset_query();					
			?>
			<div class="row">
<?php
			$highlightSecondLevelVars = array(
				'post_type'=>$post->post_type,
				'post_parent'=>$post->ID,
				'posts_per_page'=>2,
				'post__not_in' => $highlighIds,
				'tax_query' => array( array('taxonomy' => 'xtt-pa-destaque', 'field' => 'slug','terms' => 'arquivo-pequeno')),
			);

			$highlightSecondLevel = new WP_Query( $highlightSecondLevelVars );

			while ( $highlightSecondLevel->have_posts() ):
				$highlightSecondLevel->the_post();
				$highlighIds[] = get_the_ID();
?>
						
					<div class="col-sm-6 iasd-secondary-highlight">
						<a href="<?php the_permalink() ?>" title="C<?php _e('Clique para ler o artigo completo', 'iasd'); ?>">
							<?php the_post_thumbnail('thumb_345x218', array('class' => 'img-responsive')); ?>
							<h2><?php echo apply_filters('trim', get_the_title(), 50); ?></h2>
							<p><?php echo apply_filters('trim', get_the_excerpt(), 170); ?></p>
						</a>
					</div>						

<?php
			endwhile;
			wp_reset_query();
?>
			</div>
			<h1><?php _e('Artigos', 'iasd'); ?></h1>
			<ul class="iasd-post-list-ajax" data-page="<?php echo (get_query_var('paged')) ? get_query_var('paged') : 1; ?>" data-pages="<?php echo $wp_query->max_num_pages; ?>">
				<?php 

				$otherQueryVars = array(
					'post_type'=>$post->post_type,
					'post_parent'=>$post->ID,
					'post__not_in' => $highlighIds,					
				);
				$otherPosts = new WP_Query( $otherQueryVars );
				while ( $otherPosts->have_posts() ):
					$otherPosts->the_post();				
				?>
					<li class="iasd-post-list-item-ajax">
						<a href="<?php the_permalink() ?>" title="<?php _e('Clique para ler o artigo completo', 'iasd'); ?>">
							<figure class="hidden-xs">
								<?php the_post_thumbnail('thumb_140x90'); ?>
							</figure>
							<div class="info">
								<time><?php the_time('j \d\e F \d\e Y'); ?></time>
								<h2><?php the_title(); ?></h2>
								<p class="hidden-xs"><?php echo apply_filters('trim', get_the_excerpt(), 150); ?></p>
							</div>
						</a>
					</li>					

				<?php
					endwhile;
					wp_reset_query();
				?>
			</ul>
			<?php if (($wp_query->max_num_pages > 1) && (get_query_var('paged') < $wp_query->max_num_pages)): ?>
				<a href="<?php echo next_posts($wp_query->max_num_pages, false); ?>" class="btn btn-default btn-block load-more_posts-link" title="<?php _e('Mostrar mais', 'iasd'); ?>"><?php _e('Mostrar mais', 'iasd'); ?></a>
			<?php endif ?>
		</article>
		<?php do_action('iasd_dynamic_sidebar', 'revista'); ?>
		<?php /* <aside class="col-md-4 visible-md visible-lg iasd-aside">
			<div class="iasd-widget iasd-widget-magazine_more">
				<h1><?php _e('Assine', 'iasd'); ?></h1>
				<p><?php _e('Mantenha-se informado a respeito dos acontecimentos da Igreja no Brasil e no mundo. Receba mensalmente mensagens que contribuirão para o seu crescimento espiritual.', 'iasd'); ?></p>
				<a href="http://www.cpb.com.br/produto-359-assinatura.html" target="_blank" class="btn btn-default btn-block" title="<?php _e('Clique aqui para assinar a revista', 'iasd'); ?>"><?php _e('Clique aqui para assinar a revista', 'iasd'); ?></a>
			</div>
			<div class="iasd-widget iasd-widget-magazine_more">
				<h1><?php _e('Acervo histórico', 'iasd'); ?></h1>
				<p><?php _e('Conheça o projeto "Um Século de História". Acesse o acervo histórico da revista com mais de 100 anos de edições.', 'iasd'); ?></p>
				<a href="http://www.revistaadventista.com.br/" target="_blank" class="btn btn-default btn-block" title="<?php _e('Clique aqui para conhecer o projeto', 'iasd'); ?>"><?php _e('Clique aqui para conhecer o projeto', 'iasd'); ?></a>
			</div>
		</aside> */ ?>
	</section>
</div>

<!-- *************************** -->
<!-- ******* End Content ******* -->
<!-- *************************** -->

<?php


get_footer(); 

?>