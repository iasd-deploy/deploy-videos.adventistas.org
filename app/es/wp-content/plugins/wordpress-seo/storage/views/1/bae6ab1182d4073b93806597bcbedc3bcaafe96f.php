<?php if(is_admin()): ?>
    <img class="img-preview" src="<?php echo e(get_template_directory_uri()); ?>/Blocks/PACarouselMinistry/preview.png" />
<?php else: ?>
	<div class="pa-widget pa-w-carousel-ministry col col-md-8 mb-5">
		<?php if (! empty($title)) : ?>
			<h2><?php echo $title; ?></h2>
		<?php endif; ?>

		<?php if (! empty($items)) : ?>
			<div class="mt-4">
				<div class="pa-destaque-deptos-sliders">
					<div class="glide__track" data-glide-el="track">
						<div class="glide__slides">
							<?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<div class="glide__slide">
									<a 
										href="<?php echo e(isset($item['link']) ? $item['link']['url'] : get_permalink($item['id'])); ?>"
										target="<?php echo e(isset($item['link']) && !empty($item['link']['target']) ? $item['link']['target'] : '_self'); ?>"
										class="d-block"
									>
										<div class="ratio ratio-16x9">
											<figure class="figure m-xl-0">
													<img class="figure-img img-fluid m-0 rounded"
														src="<?php echo e(isset($item['featured_media_url']) ? $item['featured_media_url']['pa_block_render'] : get_the_post_thumbnail_url($item['id'], 'medium')); ?>"
														alt="<?php echo e($item['title']['rendered']); ?>" 
													/>
												
												<figcaption class="figure-caption position-absolute w-100 p-3 rounded-bottom">
													<?php if (! empty($item['tag'])) : ?>
														<span class="pa-tag rounded-sm mb-2"><?php echo e($item['tag']); ?></span>
													<?php endif; ?>

													<?php if (! empty($item['title'])) : ?>
														<h3 class="h4 pt-2"><?php echo $item['title']['rendered']; ?></h3>
													<?php endif; ?>
												</figcaption>
											</figure>
										</div>
									</a>
								</div>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</div>
					</div>

					<?php if(count($items) > 1): ?>
						<div class="pa-slider-controle d-flex justify-content-between justify-content-xl-start align-items-center mt-4">
							<div data-glide-el="controls">
								<span class="fa-stack" data-glide-dir="&lt;">
									<i class="fas fa-circle fa-stack-2x"></i>
									<i class="icon fas fa-arrow-left fa-stack-1x"></i>
								</span>
							</div>

							<div class="mx-2 pa-slider-bullet" data-glide-el="controls[nav]">
								<?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<i class="fas fa-circle fa-xs mx-1" data-glide-dir="=<?php echo e($loop->index); ?>"></i>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</div>

							<div data-glide-el="controls">
								<span class="fa-stack" data-glide-dir="&gt;">
									<i class="fas fa-circle fa-stack-2x"></i>
									<i class="icon fas fa-arrow-right fa-stack-1x"></i>
								</span>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>
<?php /**PATH /Users/eli/Dropbox (ComunicaDSA)/projects/videos.adventistas.org-old/pt/wp-content/themes/pa-theme-sedes/Blocks/PACarouselMinistry/views/frontend.blade.php ENDPATH**/ ?>