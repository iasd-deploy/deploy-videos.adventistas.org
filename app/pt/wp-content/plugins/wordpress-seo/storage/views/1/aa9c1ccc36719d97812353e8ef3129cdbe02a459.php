<?php if(is_admin()): ?>
	<img class="img-preview" src="<?php echo e(get_template_directory_uri()); ?>/Blocks/PAMagazines/preview.png"/>
<?php else: ?>
	<div class="pa-widget pa-w-carousel-magazines col col-md-4 mb-5">
		<?php if (! empty($title)) : ?>
			<h2><?php echo $title; ?></h2>
		<?php endif; ?>

		<?php if (! empty($items)) : ?>
			<div class="mt-4 p-4 bg-light">
				<div class="position-relative p-4">
					<div class="pa-slider-magazines d-flex align-items-center ">
						<div class="glide__track" data-glide-el="track">
							<div class="glide__slides">
								<?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<div class="glide__slide">
										<a 
											class="ratio ratio-16x21 d-block"
											href="<?php echo e(isset($item['link']) ? $item['link']['url'] : get_permalink($item['id'])); ?>"
											target="<?php echo e(isset($item['link']) && !empty($item['link']['target']) ? $item['link']['target'] : '_self'); ?>"
										>
											<figure class="figure m-xl-0">
												<img
													src="<?php echo e(isset($item['featured_media_url']) ? $item['featured_media_url']['pa_block_render'] : get_the_post_thumbnail_url($item['id'], 'medium')); ?>"
													alt="<?php echo e($item['title']['rendered']); ?>" 
													class="rounded img-fluid" 
												/>
											</figure>
										</a>
									</div>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</div>
						</div>

						<?php if(count($items) > 1): ?>
							<div class="pa-slider-controle pa-controle-left" data-glide-el="controls">
								<div data-glide-dir="&lt;">
									<i class="fas fa-angle-left fa-3x"></i>
								</div>
							</div>

							<div class="pa-slider-controle pa-controle-right" data-glide-el="controls">
								<div data-glide-dir="&gt;">
									<i class="fas fa-angle-right fa-3x"></i>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?><?php /**PATH /Users/eli/Dropbox (ComunicaDSA)/projects/videos.adventistas.org-old/pt/wp-content/themes/pa-theme-sedes/Blocks/PAMagazines/views/frontend.blade.php ENDPATH**/ ?>