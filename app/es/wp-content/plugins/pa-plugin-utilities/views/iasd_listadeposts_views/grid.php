<div id="<?php echo $this->slug(); ?>" class="iasd-widget iasd-widget-posts_grid <?php echo $this->widgetAddClasses(); ?>">
	<?php echo $this->widgetTitle(); 
		IASD_ListaDePosts::addEditButton();
?>
	<div class="row iasd-widget-list">
<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			$post_title = the_title('', '', false);
			$post_thumbnail = '<figure><img src="'.$this->getThumbnail('thumb_140x90').'" class="img-responsive" alt="'.$post_title.'"></figure>';

?>
		<div class="post-horizontal-list-item col-md-6 col-sm-6">
			<a href="<?php the_permalink(); ?>" title="<?php echo $post_title, '. ', __('Clique para ler a matéria completa', 'iasd'); ?>.">
				<h2><?php echo apply_filters('trim', $post_title, 70); ?></h2>
				<figure>
					<?php //echo get_the_post_thumbnail(get_the_ID(), 'thumb_140x90'); ?>
					<?php echo $post_thumbnail; ?>
					<?php /*<img src="<?php echo $this->getThumbnail('thumb_140x90'); ?>" alt="<?php echo $this->getThumbnailName(); ?>">*/ ?>
					<?php if($term = $this->getPostTerm()) echo '<figcaption class="post-taxonomy-tag"><span>'.( is_object($term) ? $term->name : $term ).'</span></figcaption>'; ?>
				</figure>
				<p><?php echo apply_filters('trim', get_the_excerpt(), 120, '...'); ?></p>
			</a>
		</div>
<?php
			
		} // end while
	} // end if
?>	
	</div>
	<div class="row">
		<div class="col-md-12">
			<?php echo $this->widgetSeeMoreHtml(__('Mais notícias', 'iasd'), __('Clique para ver todas as notícias', 'iasd')); ?>
		</div>
	</div>
	<?php $this->widgetAddGroupingTaxonomyHtml(); ?>
	<?php IASD_ViewFragments::WrongSizeHtml(); ?>
</div>