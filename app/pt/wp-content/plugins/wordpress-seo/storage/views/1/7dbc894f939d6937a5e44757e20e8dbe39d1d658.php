<?php if(is_admin()): ?>
    <img class="img-preview" src="<?php echo e(get_template_directory_uri()); ?>/Blocks/PASevenCast/preview.png" />
<?php else: ?> 
    <div class="pa-widget pa-w-7cast col col-md-4 mb-5">
		<?php if (! empty($title)) : ?>
			<h2 class="mb-4"><?php echo $title; ?></h2>
		<?php endif; ?>

		<?php if (! empty($items)) : ?>
			<div class="mt-4">
				<?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<div class="card mb-2 mb-xl-4 border-0">
						<a href="<?php echo e($item['link']['url']); ?>" href="<?php echo e($item['link']['target']); ?>">
							<div class="row">
								<?php if (! empty($item['featured_media_url'])) : ?>
									<div class="col-4">
										<div class="ratio ratio-1x1">
											<figure class="figure m-xl-0">
												<img
													class="figure-img img-fluid rounded m-0" 
													src="<?php echo e($item['featured_media_url']['pa_block_render']); ?>" 
													alt="<?php echo e($item['title']['rendered']); ?>"
												/>
											</figure>
										</div>
									</div>
								<?php endif; ?>

								<div class="col-8">
									<div class="card-body p-0">
										<i class="fas fa-microphone my-1"></i>

										<?php if (! empty($item['title'])) : ?>
											<h3 class="card-title h6 fw-bolder m-0"><?php echo $item['title']['rendered']; ?></h3>
										<?php endif; ?>	

										<?php if (! empty($item['excerpt'])) : ?>
											<p class="pa-truncate-2"><?php echo $item['excerpt']['rendered']; ?></p>
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
				class="pa-all-content mt-2"
			>
				<?php echo $link['title']; ?>

			</a>
		<?php endif; ?>
    </div>
<?php endif; ?>
<?php /**PATH /Users/eli/Dropbox (ComunicaDSA)/projects/videos.adventistas.org-old/pt/wp-content/themes/pa-theme-sedes/Blocks/PASevenCast/views/frontend.blade.php ENDPATH**/ ?>