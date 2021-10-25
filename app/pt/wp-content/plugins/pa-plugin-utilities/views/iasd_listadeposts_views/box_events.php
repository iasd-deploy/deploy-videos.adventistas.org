<div id="<?php echo $this->slug(); ?>" class="iasd-widget iasd-widget-posts_box <?php echo $this->widgetAddClasses(); ?>">
<?php echo $this->widgetTitle(); 
	IASD_ListaDePosts::addEditButton();

?>
	<ul class="iasd-widget-list">
<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			$title = the_title('', '', false);
			$link_title = $date_start = date('d/m', strtotime($this->getPostMeta('st_date')));
			$date_end = date('d/m', strtotime($this->getPostMeta('end_date')));
			$time = $time_start = $this->getPostMeta('st_time');
//			$time_end = $this->getPostMeta('end_time');
			$address = $this->getPostMeta('address');

			$link_title_mask = sprintf(__('De %s a %s', 'iasd'), $date_start, $date_end);

			if($date_end)
				if($date_start != $date_end)
					$link_title = $link_title_mask;

/*			if($time_end)
				if($time_start != $time_end)
					$time = $time_start . ' - ' . $time_end;*/
			
?>

		<li>
			<a href="<?php the_permalink(); ?>" title="<?php echo $title, ' - ', $link_title;?>">
				<time>
					<span class="date"><?php echo $date_start; ?></span>
					<span class="hour"><?php echo $time; ?></span>
				</time>
				<div class="info">
					<h2><?php echo apply_filters('trim', $title, 80); ?></h2>
					<address><?php echo $address; ?></address>
				</div>
			</a>
		</li>
<?php
			
		} // end while
		echo '<li>', $this->widgetSeeMoreHtml(__('Mais notícias', 'iasd'), __('Clique para ver todas as notícias', 'iasd')), '</li>';
	} // end if
?>
	</ul>
	<?php $this->widgetAddGroupingTaxonomyHtml(); ?>
	<?php IASD_ViewFragments::WrongSizeHtml(); ?>
</div>