<?php if(is_admin()): ?>
    <img class="img-preview" src="<?php echo e(get_template_directory_uri()); ?>/Blocks/PAFeliz7Play/preview.png" alt="<?php echo e(__('Illustrative image of the front end of the block.', 'iasd')); ?>" />
<?php else: ?> 
	<div class="pa-widget pa-w-feliz7play py-4 col-12 position-relative bg-light mb-5">
		<div class="pa-slider-header mb-4">
			<div class="d-flex justify-content-between align-items-center">
				<img src="<?php echo e(get_template_directory_uri()); ?>/Blocks/PAFeliz7Play/assets/images/f7p-logo.svg" alt="<?php echo e(__('Feliz7Play', 'iasd')); ?>" title="<?php echo e(__('Feliz7Play', 'iasd')); ?>" class="img-fluid">

				<a 
					href="https://www.feliz7play.com" 
					target="_blank"
					class="btn btn-primary"
          rel="noopener"
				>
					<?php echo e(__('Access the website', 'iasd')); ?>

				</a>
			</div>
		</div>

		<?php if (! empty($items)) : ?>
			<div class="row">
				<div class="col">
					<div class="pa-glide-feliz7play">
						<div class="glide__track" data-glide-el="track">
							<div class="glide__slides">
								<?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<?php if (! empty($item['featured_media_url'])) : ?>
										<a
											href="<?php echo e($item['link']['url'] ?? '#'); ?>" 
											target="<?php echo e($item['link']['target'] ?? '_self'); ?>"
										>
											<div class="ratio ratio-16x9">	
												<img 
													class="glide__slide rounded img-fluid" 
													src="<?php echo e($item['featured_media_url']['pa_block_render']); ?>" 
													alt="<?php echo e($item['title']['rendered']); ?>" 
												/>
											</div>	
										</a>
									<?php endif; ?>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</div>
						</div>
						
						<?php if(count($items) > 1): ?>
							<div class="pa-slider-controle d-flex justify-content-between justify-content-xl-center align-items-center mt-4">
								<div data-glide-el="controls">
									<span class="fa-stack" data-glide-dir="&lt;">
										<i class="fas fa-circle fa-stack-2x"></i>
										<i class="icon fas fa-arrow-left fa-stack-1x"></i>
									</span>
								</div>
								<div class="mx-2 pa-slider-bullet" data-glide-el="controls[nav]">
									<?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<?php if (! empty($item['featured_media_url'])) : ?>
											<i class="fas fa-circle fa-xs mx-1" data-glide-dir="=<?php echo e($loop->index); ?>"></i>
										<?php endif; ?>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</div >
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
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>
<?php /**PATH /Users/eli/Dropbox (ComunicaDSA)/projects/deploy-videos.adventistas.org/app/pt/wp-content/themes/pa-theme-sedes/Blocks/PAFeliz7Play/views/frontend.blade.php ENDPATH**/ ?>