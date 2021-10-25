<div id="<?php echo $this->slug(); ?>" class="iasd-widget iasd-widget-posts_list <?php echo $this->widgetAddClasses(); ?>">
	<?php echo $this->widgetTitle(); 
		IASD_ListaDePosts::addEditButton();
?>
	<ul class="iasd-widget-list">
<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			$post_title = the_title('', '', false);
?>
		<li>
			<a href="<?php the_permalink(); ?>" title="<?php echo $post_title, '. ', __('Clique para ler a matéria completa', 'iasd'); ?>.">
				<figure class="gallery">
					<img src="<?php echo $this->getThumbnail('thumb_70x45'); ?>" alt="<?php echo $this->getThumbnailName(); ?>">
				</figure>
				<div class="info">
					<h2><?php echo apply_filters('trim', $post_title, 60); ?></h2>
					<time><?php the_date(); ?></time>
				</div>
			</a>
		</li>
<?php
			
		} // end while
	} // end if
?>
	</ul>
	<div class="row">
		<div class="col-md-12">
			<?php echo $this->widgetSeeMoreHtml(__('Mais notícias', 'iasd'), __('Clique para ver todas as notícias', 'iasd')); ?>
		</div>
	</div>
	<?php $this->widgetAddGroupingTaxonomyHtml(); ?>
	<?php IASD_ViewFragments::WrongSizeHtml(); ?>
</div>