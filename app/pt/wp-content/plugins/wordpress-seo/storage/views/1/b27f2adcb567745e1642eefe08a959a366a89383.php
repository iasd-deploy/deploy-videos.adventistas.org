<?php if(is_admin()): ?>
	<img class="img-preview" src="<?php echo e(get_template_directory_uri()); ?>/Blocks/PAListItems/preview.png"/>
<?php else: ?>
	<div class="pa-widget pa-w-list-projects col col-md-4 mb-5">
		<?php if (! empty($title)) : ?>
			<h2><?php echo $title; ?></h2>
		<?php endif; ?>

		<?php if (! empty($items)) : ?>
			<div class="mt-4">
				<?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<div class="project mb-2 mb-xl-4 border-0">
						<div class="row">
							<div class="col">
								<a 
									href="<?php echo e(isset($item['link']) ? $item['link']['url'] : get_permalink($item['id'])); ?>" 
									target="<?php echo e(isset($item['link']) && !empty($item['link']['target']) ? $item['link']['target'] : '_self'); ?>"
									class="d-block d-flex align-items-center rounded fw-bold ratio ratio-16x9"
								>
									<figure class="figure m-xl-0">
										<img 
											src="<?php echo e(isset($item['featured_media_url']) ? $item['featured_media_url']['pa_block_render'] : get_the_post_thumbnail_url($item['id'], 'medium')); ?>" 
											alt="<?php echo e($item['title']['rendered']); ?>"
											class="figure-img img-fluid rounded m-0"
										/>
									</figure>
								</a>
							</div>
						</div>
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
<?php endif; ?><?php /**PATH /Users/eli/Dropbox (ComunicaDSA)/projects/videos.adventistas.org-old/pt/wp-content/themes/pa-theme-sedes/Blocks/PAListItems/views/frontend.blade.php ENDPATH**/ ?>