<?php if(is_admin()): ?>
	<img class="img-preview" src="<?php echo e(get_template_directory_uri()); ?>/Blocks/PAListDownloads/preview.png"/>
<?php else: ?> 
	<div class="pa-widget pa-w-list-downloads col col-md-4 mb-5">
		<?php if (! empty($title)) : ?>
			<h2><?php echo $title; ?></h2>
		<?php endif; ?>

		<?php if (! empty($items)) : ?>
			<div class="mt-4">
				<?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<div class="card mb-2 mb-xl-4 border-0">
						<a 
							href="<?php echo e(is_array($item['link']) ? $item['link']['url'] : $item['link']); ?>" 
							target="<?php echo e(is_array($item['link']) && !empty($item['link']['target']) ? $item['link']['target'] : '_self'); ?>"
						>
							<div class="row">
								<div class="col-4">
									<div class="ratio ratio-16x9">	
										<figure class="figure m-xl-0 bg-light rounded overflow-hidden">
											<?php if (! empty($item['featured_media_url'])) : ?>
												<img 
													class="figure-img img-fluid m-0"	
													src="<?php echo e($item['featured_media_url']['pa_block_render']); ?>"  
													alt="<?php echo e($item['title']['rendered']); ?>" 
												/>
											<?php endif; ?>
										</figure>	
									</div>
								</div>
								<div class="col-8">
									<div class="card-body p-0">
										<?php if (! empty($item['file_format'])) : ?>
											<span class="pa-tag text-uppercase me-1 rounded"><?php echo e($item['file_format']); ?></span>
										<?php endif; ?>

										<?php if (! empty($item['file_size'])) : ?>
											<span class="pa-tag text-uppercase rounded"><?php echo e($item['file_size']); ?></span>
										<?php endif; ?>

										<?php if (! empty($item['title'])) : ?>
											<h3 class="card-title h6 m-0 pa-truncate-1"><?php echo $item['title']['rendered']; ?></h3>
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
<?php endif; ?><?php /**PATH /Users/eli/Dropbox (ComunicaDSA)/projects/videos.adventistas.org-old/pt/wp-content/themes/pa-theme-sedes/Blocks/PAListDownloads/views/frontend.blade.php ENDPATH**/ ?>