<div id="<?php echo $this->slug(); ?>" class="iasd-widget iasd-widget-slider <?php echo $this->widgetWidthClass(); ?>">
<?php 
	echo $this->widgetTitle();
	IASD_ListaDePosts::addEditButton();

	$size = ($this->widgetGet('width') == 'col-md-4') ? 'small' : 'large';
?>
	<div class="owl-carousel newsstand <?php echo $size; ?>">
<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			$post_title = the_title('', '', false);
?>
		<div class="slider-item">
			<a href="<?php the_permalink(); ?>" target="_blank" title="<?php _e('Clique para acessar a edição completa', 'iasd'); ?>">
				<figure>
					<img data-src="<?php echo $this->getThumbnail('thumb_145x190'); ?>" alt="<?php echo $this->getThumbnailName(); ?>" class="lazyOwl">
				</figure>
				<h2><?php echo apply_filters('trim', $post_title, 60); ?></h2>
				<?php if($term = $this->getPostTerm('edicoes-revista')) echo '<time>'.( is_object($term) ? $term->name : $term ).'</time>'; ?>
			</a>
		</div>
<?php
			
		} // end while
	} // end if
?>
	</div>
	<div class="row">
		<div class="col-md-12">
			<?php echo $this->widgetSeeMoreHtml(__('Mais edições', 'iasd'), __('Clique para ver todas as edições', 'iasd')); ?>
		</div>
	</div>
	<?php IASD_ViewFragments::WrongSizeHtml(); ?>
</div>