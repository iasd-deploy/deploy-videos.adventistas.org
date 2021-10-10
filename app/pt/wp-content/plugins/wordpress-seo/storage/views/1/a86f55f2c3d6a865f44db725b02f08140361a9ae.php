<?php $__env->startSection('content'); ?>
    <div class="pa-content py-5">
        <div class="container">
            <div class="row justify-content-md-center">
                
                <section class="col-auto col-md-8<?php echo e(is_active_sidebar('single') ? ' col-xl-8' : ''); ?>">          
                    
                    <?php echo $__env->make('template-parts.single.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    
                    <?php echo the_content(); ?>


                    <hr class="separator">

                    
                    <?php echo $__env->make('template-parts.single.related-posts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </section>

                
                <?php if(is_active_sidebar('single')): ?>
                    <aside class="col-md-4 d-none d-xl-block">
                        <?php (dynamic_sidebar('single')); ?>
                    </aside>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/eli/Dropbox (ComunicaDSA)/projects/videos.adventistas.org-old/pt/wp-content/themes/pa-theme-videos/single.blade.php ENDPATH**/ ?>