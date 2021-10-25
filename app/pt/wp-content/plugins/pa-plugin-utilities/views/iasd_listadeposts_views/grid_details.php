<div class="iasd-widget inst-address inst-secondary col-md-12">
	<div class="row mar-top-30 iasd-widget-list">
			<div class="col-md-12 iasd-widget-list">
				<?php
					echo $this->widgetTitle();
					IASD_ListaDePosts::addEditButton();

					if ( have_posts() ) {
						while ( have_posts() ) {
							the_post();
				?>
						<div class="col-sm-4 col-xs-12 inst-item">
							<h2><?php the_title(); if($postmeta = $this->getPostMeta('instituicao_sigla')) echo " ($postmeta)";?></h2>
							<p><?php echo apply_filters('trim', get_the_excerpt(), 90); ?></p>
							<a href="<?php the_permalink(); ?>"><div class="btn btn-default"><?php _e('Detalhes', 'iasd'); ?></div></a>
						</div>
				<?php } } ; ?>
		</div>
	</div>
	<div class="row iasd-widget-posts_grid">
		<div class="col-md-12">
			<?php echo $this->widgetSeeMoreHtml(__('Mais notícias', 'iasd'), __('Clique para ver todas as notícias', 'iasd')); ?>
		</div>
	</div>
	<?php IASD_ViewFragments::WrongSizeHtml(); ?>
</div>
