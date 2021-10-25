<div id="<?php echo $this->slug(); ?>" class="iasd-widget <?php echo $this->widgetAddClasses(); ?>">

<?php
	echo $this->widgetTitle();
	IASD_ListaDePosts::addEditButton();
	echo "\n<ul class='iasd-widget-list'>\n";

	if ( have_posts() ) {
		$count = 0;
		while ( have_posts() ) {
			the_post();
			$post_title = the_title('', '', false);
			$post_thumbnail = '';
			$item_length = $this->widgetView('item_max_length');

			$li_class = '';
			if($count === 0 && $highlight_item_length = $this->widgetView('highlight_max_length')) {
				$item_length = $highlight_item_length;
				$li_class .= $this->widgetView('highlight_class');

				$thumb_url = $this->getThumbnail('thumb_740x475');


				$post_thumbnail = '<figure style="background-image: url('.$thumb_url.');"><img src="'.$thumb_url.'" class="img-responsive" alt="'.$post_title.'"></figure>';
			} else if($this->widgetView('thumbnail')) {
				//$post_thumbnail = '<figure>'.get_the_post_thumbnail(get_the_ID(), 'thumb_70x45').'</figure>';
				$post_thumbnail = '<figure><img src="'.$this->getThumbnail('thumb_70x45').'" class="img-responsive" alt="'.$post_title.'"></figure>';
			}
?>

		<li class="<?php echo $li_class; ?>">
			<a href="<?php the_permalink(); ?>" title="<?php echo $post_title, '. ', __('Clique para ler a matéria completa', 'iasd'); ?>.">
				<?php 
					$show_tax = $this->widgetView('show_taxonomy', false);
					if($show_tax)
						if($term = $this->getPostTerm()) echo '<p class="post-taxonomy-tag">'.( is_object($term) ? $term->name : $term ).'</p>';

					echo $post_thumbnail;
					if($show_tax)
						echo '<div class="info">';
					echo '<h2>', apply_filters('trim', $post_title, $item_length), '</h2>';
					if($this->widgetView('show_intro', false)) {
						echo '<p>', apply_filters('trim', get_the_excerpt(), $this->widgetView('item_excerpt_length', $item_length), '...'), '</p>';
					}
					if($show_tax)
						echo '</div>';
				?>
			</a>
		</li>
<?php
			$count++;
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