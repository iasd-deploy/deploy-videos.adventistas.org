<?php
/*
 * Template Name: Revista Adventista - Matéria
 */

	get_header(); 

	if(have_posts())
		the_post();

	global $post;
	$post_type = get_post_type_object($post->post_type);
	$archive_link = get_post_type_archive_link( $post->post_type );

	$post_title = get_the_title();
	$post_link = get_permalink();
?>

<!-- *************************** -->
<!-- ********* Content ********* -->
<!-- *************************** -->

<div class="container">
	<section class="row">
		<article class="col-md-8 entry-content">
			<header>
				<time><?php the_time('j \d\e F \d\e Y'); ?></time>
				<h1><?php single_post_title(); ?></h1>
				<div class="sharing-links">
					<?php do_action('sharing_links'); ?>
				</div>
			</header>
			<?php the_content(); ?>
			<hr class="iasd-footer-top clear-footer-top">
			<?php if ( comments_open() || get_comments_number() ) comments_template(); ?>
			<?php do_action('iasd_disqus_javascript'); ?>
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

<?php get_footer(); ?>