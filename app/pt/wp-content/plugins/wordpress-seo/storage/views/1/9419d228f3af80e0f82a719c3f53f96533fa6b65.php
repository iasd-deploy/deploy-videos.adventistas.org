<?php if(is_admin()): ?>
	<img class="img-preview" src="<?php echo e(get_template_directory_uri()); ?>/Blocks/PAListButtons/preview.png"/>
<?php else: ?>
	<div class="pa-widget pa-w-list-buttons col col-md-4 mb-5">
		<?php if (! empty($title)) : ?>
			<h2><?php echo $title; ?></h2>
		<?php endif; ?>

		<?php if (! empty($items)) : ?>
			<ul class="list-unstyled mt-4">
				<?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<li class="pa-widget-button h-25 mb-4">
						<a
							href="<?php echo e(isset($item['link']) ? $item['link']['url'] : get_permalink($item['id'])); ?>"
							target="<?php echo e(isset($item['link']) && !empty($item['link']['target']) ? $item['link']['target'] : '_self'); ?>"
							class="d-block d-flex px-4 align-items-center rounded fw-bold"
						>
							<i class="pa-icon far fa-file-alt me-4 fa-2x"></i>
							<span class="my-4"><?php echo $item['title']['rendered']; ?></span>
							<i class="fas fa-chevron-right ms-auto"></i>
						</a>
					</li>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</ul>
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
<?php endif; ?><?php /**PATH /Users/eli/Dropbox (ComunicaDSA)/projects/videos.adventistas.org-old/pt/wp-content/themes/pa-theme-sedes/Blocks/PAListButtons/views/frontend.blade.php ENDPATH**/ ?>