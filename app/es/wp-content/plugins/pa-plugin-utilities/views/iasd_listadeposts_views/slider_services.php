<div id="<?php echo $this->slug(); ?>" class="iasd-widget iasd-widget-slider <?php echo $this->widgetWidthClass(); ?>">
	<?php echo $this->widgetTitle(); 
		IASD_ListaDePosts::addEditButton();
?>
	<div class="owl-carousel services <?php echo ($this->widgetGet('width') == 'col-md-8') ? 'large' : 'small'; ?>">
<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			$post_title = the_title('', '', false);
?>
		<div class="slider-item">
			<a href="<?php the_permalink(); ?>" target="_blank" 
				title="<?php echo __('Clique para acessar a página', 'iasd'), ' \'', $post_title, '\''; ?>">
				<?php echo apply_filters('trim', $post_title, 46)."\r\n"; ?>
			</a>
		</div>
<?php
			
		} // end while
	} // end if
?>
	</div>
	<?php IASD_ViewFragments::WrongSizeHtml(); ?>
</div>