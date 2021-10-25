<div id="<?php echo $this->slug(); ?>" class="iasd-widget iasd-widget-slider <?php echo $this->widgetAddClasses(); ?>">
	<div class="well columns">
		<?php echo $this->widgetTitle(); 
			IASD_ListaDePosts::addEditButton();
?>
		<div class="owl-carousel">
<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();

				$post_title = the_title('', '', false);

				$author = $this->getPostAuthor();

				$avatar = get_avatar($author['id'], 400);
			    preg_match("/src=['\"](.*?)['\"]/i", $avatar, $matches);
				$author_thumb = (count($matches)) ? $matches[1] : '';
?>
			<div class="slider-item">
				<a href="<?php the_permalink(); ?>" target="_blank" title="<?php echo $post_title, '. ', __('Clique para ler o artigo completo', 'iasd'); ?>">
					<figure style="background-image: url('<?php echo $author_thumb; ?>');">
						<img src="<?php echo $author_thumb; ?>" alt="<?php echo $author['name']; ?>" class="no-lazy" />
					</figure>
					<h2><?php echo $author['name']; ?></h2>
					<?php if($term = $this->getPostTerm()) echo '<p class="post-taxonomy-tag">'.( is_object($term) ? $term->name : $term ).'</p>'; ?>
					<h3><?php echo apply_filters('trim', $post_title, 55, ''); ?></h3>
					<p><?php echo apply_filters('trim', get_the_excerpt(), 110, '...'); ?></p>
				</a>
			</div>
<?php
			} // end while
		} // end if
?>
		</div>
	</div>
	<?php IASD_ViewFragments::WrongSizeHtml(); ?>
</div>