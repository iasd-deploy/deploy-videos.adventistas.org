<?php echo get_header(); ?>


<?php echo $__env->make('components.header.title', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->yieldContent('content'); ?>

<?php echo get_footer(); ?><?php /**PATH /Users/eli/Dropbox (ComunicaDSA)/projects/videos.adventistas.org-old/pt/wp-content/themes/pa-theme-videos/layouts/app.blade.php ENDPATH**/ ?>