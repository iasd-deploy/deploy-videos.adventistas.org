<div class="iasd-widget inst-address col-md-12 ">
	<div class="row iasd-widget-list">
		<div class="col-md-12 iasd-widget-list">
		<?php
			echo $this->widgetTitle();
			IASD_ListaDePosts::addEditButton();

			if ( have_posts() ) {
				$isFirst = true;
				while ( have_posts() ) {
					the_post();
					if($this->widgetView('highlight') && $isFirst): 
						$thumb_url = $this->getThumbnail('thumb_617x220');
		?>
				<div class="col-sm-8">
					<div class="row">
						<a href="<?php the_permalink(); ?>"><img src="<?php echo $thumb_url; ?>" class="img-responsive img-rounded"></a>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-4">
						<h2><?php the_title(); if($postmeta = $this->getPostMeta('instituicao_sigla')) echo " ($postmeta)";?></h2>
						<p><?php echo apply_filters('trim', get_the_excerpt(), 100); ?></p>
						<p><?php echo $this->getPostMeta('poboxzip'); ?></p>
						<p><?php if($postmeta = $this->getPostMeta('instituicao_tel')) echo __('Tel', 'iasd'), ': ', $postmeta; ?></p>
						<p><?php if($postmeta = $this->getPostMeta('instituicao_fax')) echo __('Fax', 'iasd'), ': ', $postmeta; ?></p>
						<a href="<?php the_permalink(); ?>"><div class="btn btn-default"><?php _e('Detalhes', 'iasd'); ?></div></a>
					</div>
				</div>
				<div class="clearfix"></div>
				<hr>
		<?php 
					else: 
						$thumb_url = $this->getThumbnail('thumb_140x90');
		?>
				<div class="col-sm-6 col-xs-12 inst-item">
					<div class="row">
						<div class="col-sm-4">
							<div class="row">
								<a href="<?php the_permalink(); ?>"><img src="<?php echo $thumb_url; ?>" class="img-responsive img-rounded hidden-xs"></a>
							</div>
						</div>
						<div class="col-sm-8 col-xs-12">
							<div class="col-sm-12 col-xs-12">
								<h2><?php the_title(); if($postmeta = $this->getPostMeta('instituicao_sigla')) echo " ($postmeta)";?></h2>
								<p>
									<?php 
										$excerpt = get_the_excerpt();
										if($cep = $this->getPostMeta('instituicao_cep')) {
											if($excerpt)
												$excerpt .= ' - ';
											$excerpt .= __('CEP', 'iasd') . ' ' . $cep; 
										} 
										echo apply_filters('trim', $excerpt, 85);
									?>
								</p>
								<a href="<?php the_permalink(); ?>"><div class="btn btn-default"><?php _e('Detalhes', 'iasd'); ?></div></a>
							</div>
						</div>
					</div>
				</div>
		<?php 
					endif; 
					$isFirst = false;
				} 
			};
		?>
		</div>
	</div>
	<div class="row iasd-widget-posts_grid">
		<div class="col-md-12">
			<?php echo $this->widgetSeeMoreHtml(__('Mais notícias', 'iasd'), __('Clique para ver todas as notícias', 'iasd')); ?>
		</div>			
	</div>
	<?php IASD_ViewFragments::WrongSizeHtml(); ?>
</div>
			
