<div id="<?php echo $this->slug(); ?>" class="iasd-widget iasd-widget-slider <?php echo $this->widgetWidthClass(); ?>">
	<?php echo $this->widgetTitle(); 
		IASD_ListaDePosts::addEditButton();
?>
	<div class="owl-carousel galleries">
<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();

			$post_title = the_title('', '', false);
			$thumb_url = $this->getThumbnail('thumb_740x475');
?>
		<div class="slider-item">
			<a href="<?php the_permalink(); ?>" title="<?php echo $post_title, '. ', __('Clique para ler a matéria completa', 'iasd'); ?>.">
				<figure style="background-image: url('<?php echo $thumb_url; ?>');">
					<img src="<?php echo $thumb_url; ?>" class="img-responsive" alt="<?php echo $this->getThumbnailName(); ?>">
				</figure>
				<?php
					echo '<h2>', apply_filters('trim', $post_title, 54), '</h2>';
					echo '<time>', get_the_date(),'</time>';
					echo '<p>', apply_filters('trim', get_the_excerpt(), 90, '...'), '</p>';
				?>
			</a>
		</div>
<?php
			
		} // end while
	} // end if
?>
	</div>
	<div class="row">
		<div class="col-md-12">
			<?php echo $this->widgetSeeMoreHtml(__('Mais imagens', 'iasd'), __('Clique para ver todas as imagens', 'iasd')); ?>
		</div>
	</div>
	<?php IASD_ViewFragments::WrongSizeHtml(); ?>
</div>