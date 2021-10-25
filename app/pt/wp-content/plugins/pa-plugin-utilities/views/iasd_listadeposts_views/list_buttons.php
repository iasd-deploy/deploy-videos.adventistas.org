<div id="<?php echo $this->slug(); ?>" class="iasd-widget iasd-widget-projects <?php echo $this->widgetAddClasses(); ?>">
			<?php
				IASD_ListaDePosts::addEditButton();
 ?>

<?php echo $this->widgetTitle(); ?>
	<ul class="iasd-widget-list">
<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			$post_title = the_title('', '', false);
?>
		<li>
			<a href="<?php the_permalink(); ?>" 
				title="<?php echo __('Clique para acessar o projeto', 'iasd'), ' \'', $post_title, '\''; ?>">
				<?php echo apply_filters('trim', $post_title, 46)."\r\n"; ?>
			</a>
		</li>
<?php } } ; ?>
	</ul>
	<?php IASD_ViewFragments::WrongSizeHtml(); ?>
</div>