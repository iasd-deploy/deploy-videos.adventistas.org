<div id="<?php echo $this->slug(); ?>" class="iasd-widget iasd-widget-posts_box <?php echo $this->widgetAddClasses(); ?>">
<?php
	echo $this->widgetTitle();
	IASD_ListaDePosts::addEditButton();

	echo ($this->widgetView('ordered')) ? '<ol class="iasd-widget-list">' : '<ul class="iasd-widget-list">';

	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			$title = the_title('', '', false);
?>

		<li>
			<a href="<?php the_permalink(); ?>" title="<?php echo $title, '. ', __('Clique para ler a matéria completa', 'iasd'); ?>.">
				<?php 
					if($this->widgetView('thumbnail')) echo '<figure>', the_post_thumbnail('thumb_40x40'), '</figure>';
					echo '<h2>', apply_filters('trim', $title, $this->widgetView('item_max_length')), '</h2>';
				?>
			</a>
		</li>
<?php
			
		} // end while
		if($this->widgetGet('seemore')) echo '<li>', $this->widgetSeeMoreHtml(__('Mais notícias', 'iasd'), __('Clique para ver todas as notícias', 'iasd')), '</li>';
	} // end if
	echo ($this->widgetView('ordered')) ? '</ol>' : '</ul>';
	$this->widgetAddGroupingTaxonomyHtml();
	IASD_ViewFragments::WrongSizeHtml();
?>
</div>
