<?php
/*
Template Name: Revista Adventista - Todas as Edições
*/
get_header(); ?>

<!-- *************************** -->
<!-- ********* Content ********* -->
<!-- *************************** -->

<div class="container">
	<section class="row iasd-newsstand">
		<article class="col-md-8">
			<header>
				<h1><?php _e('Revista Adventista', 'iasd'); ?></h1>
			</header>
			<ul class="row xs-landscape iasd-post-list-ajax" data-page="<?php echo (get_query_var('paged')) ? get_query_var('paged') : 1; ?>" data-pages="<?php echo $wp_query->max_num_pages; ?>">
				<?php
					while (have_posts()):
						the_post(); ?>
				<li class="col-sm-4 iasd-post-list-item-ajax">
					<a href="<?php the_permalink(); ?>" title="<?php _e('Clique para ver a edição', 'iasd'); ?>">
						<time><?php the_title(); ?></time>
						<?php the_post_thumbnail('thumb_180x235', array('alt'=> get_the_title(), 'class'=>"img-responsive img-rounded")); ?>
					</a>
				</li>
				<?php
					endwhile;
				?>
			</ul>
			<?php if (($wp_query->max_num_pages > 1) && (get_query_var('paged') < $wp_query->max_num_pages)): ?>
				<a href="<?php echo next_posts($wp_query->max_num_pages, false); ?>" class="btn btn-default btn-block load-more_posts-link" title="<php _e('Mostrar mais', 'iasd'); ?>"><?php _e('Mostrar mais', 'iasd'); ?></a>
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

<?php get_footer(); ?>