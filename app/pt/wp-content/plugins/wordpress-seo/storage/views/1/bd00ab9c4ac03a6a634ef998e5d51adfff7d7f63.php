<?php if(is_admin()): ?>
	<img class="img-preview" src="<?php echo e(get_template_directory_uri()); ?>/Blocks/PAListIcons/preview.png"/>
<?php else: ?>
	<div class="pa-widget pa-w-list-buttons pa-w-list-buttons-icons col col-md-4 mb-5">
		<?php if (! empty($title)) : ?>
			<h2><?php echo $title; ?></h2>
		<?php endif; ?>

		<?php if (! empty($items)) : ?>
			<ul class="list-unstyled mt-4">
				<?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php if (! empty($item['link'])) : ?>
						<li class="pa-widget-button h-25 mb-4">
							<a 
								href="<?php echo e($item['link']['url'] ?? '#'); ?>" 
								target="<?php echo e($item['link']['target'] ?? '_self'); ?>"
								class="d-block d-flex px-4 align-items-center rounded fw-bold" 
							>
								<?php if (! empty($item['icon'])) : ?>
									<i class="<?php echo e($item['icon']); ?> me-4 fa-2x"></i>
								<?php endif; ?>

								<span class="my-4"><?php echo e($item['link']['title']); ?></span>

								<i class="fas fa-chevron-right ms-auto"></i>
							</a>
						</li>
					<?php endif; ?>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</ul>
		<?php endif; ?>
	</div>
<?php endif; ?><?php /**PATH /Users/eli/Dropbox (ComunicaDSA)/projects/videos.adventistas.org-old/pt/wp-content/themes/pa-theme-sedes/Blocks/PAListIcons/views/frontend.blade.php ENDPATH**/ ?>