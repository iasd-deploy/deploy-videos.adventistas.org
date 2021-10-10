<?php if(is_admin()): ?>
	<img class="img-preview" src="<?php echo e(get_template_directory_uri()); ?>/Blocks/PAFacebook/preview.png" />
<?php else: ?>
	<div class="pa-widget pa-w-facebook col col-md-4 mb-5">
		<?php if (! empty($title)) : ?>
			<h2><?php echo $title; ?></h2>
		<?php endif; ?>

		<?php if (! empty($url)) : ?>
			<div 
				data-href=<?php echo e($url); ?>

				class="fb-page" 
				data-tabs="timeline" 
				data-height="" 
				data-small-header="false" 
				data-adapt-container-width="true"
				data-hide-cover="false" 
				data-width="500" 
				data-show-facepile="true"
				data-lazy="true"
			></div>
		<?php endif; ?>
	</div>
<?php endif; ?><?php /**PATH /Users/eli/Dropbox (ComunicaDSA)/projects/videos.adventistas.org-old/pt/wp-content/themes/pa-theme-sedes/Blocks/PAFacebook/views/frontend.blade.php ENDPATH**/ ?>