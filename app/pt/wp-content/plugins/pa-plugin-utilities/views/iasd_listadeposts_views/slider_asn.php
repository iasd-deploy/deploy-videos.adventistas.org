<div id="<?php echo $this->slug(); ?>" class="iasd-widget iasd-widget-slider <?php echo $this->widgetAddClasses(); ?>">
	<div class="owl-carousel posts">
<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			$post_title = the_title('', '', false);
?>
		<div class="slider-item">
			<a href="<?php the_permalink(); ?>" title="<?php echo $post_title, '. ', __('Clique para ler a matéria completa', 'iasd'); ?>.">
				<figure>
					<img data-src="<?php echo $this->getThumbnail('thumb_740x475'); ?>" alt="<?php echo $this->getThumbnailName(); ?>" class="lazyOwl eli">
					<figcaption>
						<h2><?php echo $post_title; ?></h2>
<!-- 						<?php if($this->widgetView('show_intro')) echo '<p>', apply_filters('trim', get_the_excerpt(), 180), '</p>'; ?>
 -->					</figcaption>
				</figure>
			</a>
		</div>
<?php
			
		} // end while
	} // end if
?>
	</div>
	<?php IASD_ViewFragments::WrongSizeHtml(); ?>
</div>