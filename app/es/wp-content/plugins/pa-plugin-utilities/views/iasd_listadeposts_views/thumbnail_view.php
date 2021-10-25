<div id="<?php echo $this->slug(); ?>" class="iasd-widget iasd-widget-posts_box <?php echo $this->widgetAddClasses(); ?>">

<?php
	echo $this->widgetTitle();
	IASD_ListaDePosts::addEditButton();

	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			$title = the_title('', '', false);
			$thumbnail_url = $this->getThumbnail('thumb_730xAUTO');

?>

		<div class="iasd-widget-thumbnail">
			<a href="<?php the_permalink(); ?>" title="<?php echo $title, '. ', __('Clique para ver os infográficos', 'iasd'); ?>.">
					<a href="<?php the_permalink(); ?>" title="<?php echo $post_title, '. ', __('Clique para ver os infográficos', 'iasd'); ?>.">
						<figure>
							<img src="<?php echo $thumbnail_url ?>" class="img-responsive" alt="<?php echo $this->getThumbnailName(); ?>">
							<?php if($term = $this->getPostTerm()) echo '<figcaption class="post-taxonomy-tag"><span>'.( is_object($term) ? $term->name : $term ).'</span></figcaption>'; ?>
						</figure>
					</a>
			</a>
		</div>
<?php
			
	// 	} // end while
		if($this->widgetGet('seemore')) echo '<div>', $this->widgetSeeMoreHtml(__('Mais notícias', 'iasd'), __('Clique para ver todas as notícias', 'iasd')), '</div>';
	// } // end if
	// echo ($this->widgetView('ordered')) ? '</ol>' : '</ul>';
		}
	}
	$this->widgetAddGroupingTaxonomyHtml();
	IASD_ViewFragments::WrongSizeHtml();
?>

</div>