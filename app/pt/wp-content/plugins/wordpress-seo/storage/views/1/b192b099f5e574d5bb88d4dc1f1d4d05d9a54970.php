<?php echo get_header(); ?>


<?php echo $__env->make('components.header.title', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->yieldContent('content'); ?>

<?php echo get_footer(); ?><?php /**PATH /var/www/html/pt/wp-content/themes/pa-theme-videos/layouts/app.blade.php ENDPATH**/ ?>