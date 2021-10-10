<?php if(is_admin()): ?>
	<?php if($block_format == '2/3'): ?>
		<img class="img-preview" src="<?php echo e(get_template_directory_uri()); ?>/Blocks/PAListVideos/preview-2-3.png"/>
	<?php else: ?>
		<img class="img-preview" src="<?php echo e(get_template_directory_uri()); ?>/Blocks/PAListVideos/preview-1-3.png"/>
	<?php endif; ?>
<?php else: ?>
	<div class="pa-widget pa-w-list-videos col mb-5 <?php echo e($block_format == '2/3' ? 'col-md-8' : 'col-md-4'); ?>">
		<?php if (! empty($title)) : ?>
			<h2><?php echo $title; ?></h2>
		<?php endif; ?>

		<?php if (! empty($items)) : ?>
			<div class="row row-cols-auto mt-4 <?php echo e($block_format == '2/3' ? 'row-cols-md-2' : 'row-cols-md-1'); ?>">
				<?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php if($loop->index == 0): ?>
						<div class="col">
							<div class="card mb-4 border-0">
								<a href="<?php echo e(is_array($item['link']) ? $item['link']['title'] : $item['link']); ?>">
									<div class="ratio ratio-16x9">
										<figure class="figure bg-light rounded overflow-hidden">
											<?php if (! empty($item['featured_media_url'])) : ?>
												<img 
													class="figure-img img-fluid m-0"	
													src="<?php echo e($item['featured_media_url']['pa_block_render']); ?>"  
													alt="<?php echo e($item['title']['rendered']); ?>" 
												/>
											<?php endif; ?>

											<div class="figure-caption position-absolute w-100 h-100 d-block">
												<i class="pa-play far fa-play-circle position-absolute"></i>

												<?php if (! empty($item['time'])) : ?>
													<span class="pa-video-time position-absolute px-2 rounded-1"><i class="far fa-clock me-1"></i> <?php echo e(date('i:s', mktime(0, 0, $item['time']))); ?></span>
												<?php endif; ?>
											</div>
										</figure>
									</div>

									<div class="card-body p-0 mt-4">
										<?php if (! empty($item['title'])) : ?>
											<h3 class="card-text h5 fw-bold pa-truncate-1"><?php echo $item['title']['rendered']; ?></h3>
										<?php endif; ?>
										
										<?php if (! empty($item['excerpt'])) : ?>
											<?php if($block_format == '2/3'): ?>
												<p class="card-text d-none d-xl-block pa-truncate-3"><?php echo e(wp_strip_all_tags($item['excerpt']['rendered'])); ?></p>
											<?php endif; ?>
										<?php endif; ?>
									</div>
								</a>
							</div>
						</div>
					<?php else: ?>
						<?php if($loop->index == 1): ?>
							<div class="col">
						<?php endif; ?>

						<div class="card mb-2 mb-xl-4 border-0">
							<a href="<?php echo e($item['link']); ?>">
								<div class="row">
									<div class="col">
										<div class="ratio ratio-16x9">
											<figure class="figure m-xl-0 bg-light rounded overflow-hidden">
												<?php if (! empty($item['featured_media_url'])) : ?>
													<img 
														class="figure-img img-fluid m-0"	
														src="<?php echo e($item['featured_media_url']['pa_block_render']); ?>"  
														alt="<?php echo e($item['title']['rendered']); ?>" 
													/>
												<?php endif; ?>

												<?php if (! empty($item['time'])) : ?>
													<div class="figure-caption position-absolute w-100 h-100 d-block">
														<span class="pa-video-time position-absolute px-2 rounded-1"><i class="far fa-clock me-1"></i><?php echo e(date('i:s', mktime(0, 0, $item['time']))); ?></span>
													</div>
												<?php endif; ?>
											</figure>
										</div>	
									</div>

									<?php if (! empty($item['title'])) : ?>
										<div class="col">
											<div class="card-body p-0">
												<h3 class="card-title h6 pa-truncate-3"><?php echo $item['title']['rendered']; ?></h3>
											</div>
										</div>
									<?php endif; ?>
								</div>
							</a>
						</div>

						<?php if($loop->last): ?>
							</div>
						<?php endif; ?>
					<?php endif; ?>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

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
		<?php endif; ?>
	</div>
<?php endif; ?><?php /**PATH /Users/eli/Dropbox (ComunicaDSA)/projects/videos.adventistas.org-old/pt/wp-content/themes/pa-theme-sedes/Blocks/PAListVideos/views/frontend.blade.php ENDPATH**/ ?>