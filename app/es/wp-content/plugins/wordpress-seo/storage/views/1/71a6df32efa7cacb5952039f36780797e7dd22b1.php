<?php if(is_admin()): ?>
	<img class="img-preview" src="<?php echo e(get_stylesheet_directory_uri()); ?>/Blocks/PAListVideosColumn/preview.png"/>
<?php else: ?>
	<?php if (! empty($items)) : ?> 
		<div class="pa-widget pa-w-list-posts col-lg-4 pa-w-list-videos mb-5">			
			<h2 class="mb-4"><?php echo e($title); ?></h2>

			<?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<div class="card mb-35 mb-xl-4 border-0">
					<a href="<?php echo e(get_the_permalink($id)); ?>" title="<?php echo e(get_the_title($id)); ?>">
						<div class="row">
							<div class="img-container">
								<div class="ratio ratio-16x9">
									<figure class="figure m-xl-0">
										<img src="<?php echo e(check_immg($id, 'full')); ?>" class="figure-img img-fluid rounded m-0" alt="<?php echo e(get_the_title($id)); ?>">

										<?php if(get_field('video_length', $id)): ?>
											<div class="figure-caption position-absolute w-100 h-100 d-block">
												<span class="pa-video-time position-absolute px-2 rounded-1">
													<i class="far fa-clock me-1" aria-hidden="true"></i> <?= videoLength($id) ?>
												</span>
											</div>
										<?php endif; ?>
									</figure>	
								</div>
							</div>
							<div class="col">
								<div class="card-body p-0">
									<h3 class="card-title h6 pa-truncate-3"><?php echo e(get_the_title($id)); ?></h3>
								</div>
							</div>
						</div>
					</a>
				</div>
			<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

			<?php if(!empty($enable_link) && !empty($link)): ?>
				<a 
					href="<?php echo e($link['url'] ?? '#'); ?>" 
					target="<?php echo e($link['target'] ?? '_self'); ?>"
					class="pa-all-content"
				>
					<?php echo $link['title']; ?>

				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
<?php endif; ?><?php /**PATH /Users/eli/Dropbox (ComunicaDSA)/projects/videos.adventistas.org-old/pt/wp-content/themes/pa-theme-videos/Blocks/PAListVideosColumn/views/frontend.blade.php ENDPATH**/ ?>