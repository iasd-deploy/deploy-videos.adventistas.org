<?php $__env->startSection('content'); ?>
	<?php
		global $wp_query, $queryFeatured;
	?>

	<div class="pa-content py-5">
		<div class="container">
			<div class="row justify-content-md-center">
				<section class="col-12 col-md-8<?php echo e(is_active_sidebar('archive') ? ' col-xl-8' : ''); ?>">
					<?php echo $__env->renderWhen(get_query_var('paged') < 1 && $queryFeatured->found_posts > 0, 'template-parts.feature', [
						'post' => $queryFeatured->posts[0],
					], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>

					<?php echo $__env->renderWhen($wp_query->found_posts >= 1, 'template-parts.grid-posts', [
						// 'title' => single_term_title('', false),
						'posts' => $wp_query->posts,
					], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
					
					<div class="pa-pg-numbers row">
						<?php (new PaPageNumbers()); ?>
					</div>
				</section>

				<?php if(is_active_sidebar('archive')): ?>
					<aside class="col-md-4 d-none d-xl-block">
						<?php (dynamic_sidebar('archive')); ?>
					</aside>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/eli/Dropbox (ComunicaDSA)/projects/videos.adventistas.org-old/pt/wp-content/themes/pa-theme-videos/archive.blade.php ENDPATH**/ ?>