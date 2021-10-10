<?php if(is_admin()): ?>
	<?php if($block_format == '2/3'): ?>
		<img class="img-preview" src="<?php echo e(get_template_directory_uri()); ?>/Blocks/PAListNews/preview-2-3.png"/>
	<?php else: ?>
		<img class="img-preview" src="<?php echo e(get_template_directory_uri()); ?>/Blocks/PAListNews/preview-1-3.png"/>
	<?php endif; ?>
<?php else: ?>
	<div class="pa-widget pa-w-list-news col mb-5 <?php echo e($block_format == '2/3' ? 'col-md-8' : 'col-md-4'); ?>">
		<?php if (! empty($title)) : ?>
			<h2><?php echo $title; ?></h2>
		<?php endif; ?>

		<?php if (! empty($items)) : ?>
			<div class="mt-4">
				<?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<div class="card mb-5 mb-xl-4 border-0">
						<a 
							href="<?php echo e(isset($item['link']) ? (is_array($item['link']) ? $item['link']['url'] : $item['link']) : get_permalink($item['id'])); ?>"
							target="<?php echo e(isset($item['link']) && is_array($item['link']) && !empty($item['link']['target']) ? $item['link']['target'] : '_self'); ?>" 
						>
							<div class="row">
								<div class="col-12 col-md-5">
									<?php if (! empty($item['featured_media_url'])) : ?>
										<div class="ratio ratio-16x9">
											<figure class="figure m-xl-0">
												<img 
													class="figure-img img-fluid rounded m-0"
													src="<?php echo e(isset($item['featured_media_url']) ? $item['featured_media_url']['pa_block_render'] : get_the_post_thumbnail_url($item['id'], 'medium')); ?>"
													alt="<?php echo e($item['title']['rendered']); ?>" 
												/>
												
												<?php if(($block_format == '2/3' && isset($item['editorial']) && !empty($editorial = $item['editorial'])) ||
													$block_format == '2/3' && !empty($editorial = get_the_terms($item['id'], 'xtt-pa-editorias'))): ?>
													<figcaption class="pa-img-tag figure-caption text-uppercase rounded-right d-none d-xl-block pa-truncate-3">
														<?php echo e(is_array($editorial) ? $editorial[0]->name : $editorial); ?>

													</figcaption>
												<?php endif; ?>
											</figure>
										</div>
									<?php endif; ?>
								</div>
								
								<div class="col-12 col-md-7">
									<div class="card-body p-0">
										
										<?php if (! empty($item['post_format'])) : ?>
											<span class="pa-tag text-uppercase d-none d-xl-table-cell rounded"><?php echo e($item['post_format']); ?></span>
										<?php endif; ?>

										<?php if (! empty($item['title'])) : ?>
											<h3 class="card-title mt-xl-2 <?php echo e($block_format == '2/3' ? 'fw-bold h5' : 'h6'); ?>"><?php echo $item['title']['rendered']; ?></h3>
										<?php endif; ?>

										<?php if (! empty($item['excerpt'])) : ?>
											<?php if($block_format == '2/3'): ?>
												<p class="card-text d-none d-xl-block pa-truncate-3"><?php echo e(wp_strip_all_tags($item['excerpt']['rendered'])); ?></p>
											<?php endif; ?>
										<?php endif; ?>
									</div>
								</div>
							</div>
						</a>
					</div>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</div>
		<?php endif; ?>

		<?php if (! empty($enable_link)) : ?>
			<a 
				href="<?php echo e($link['url'] ?? '#'); ?>" 
				target="<?php echo e($link['target'] ?? '_self'); ?>"
				class="pa-all-content"
			>
				<?php echo $link['title']; ?>

			</a>
		<?php endif; ?>
	</div>
<?php endif; ?><?php /**PATH /Users/eli/Dropbox (ComunicaDSA)/projects/videos.adventistas.org-old/pt/wp-content/themes/pa-theme-sedes/Blocks/PAListNews/views/frontend.blade.php ENDPATH**/ ?>