<?php if (! empty($posts)) : ?>
    <div class="pa-blog-itens mb-4">
        
        
        <div class="row pa-w-list-videos">
            <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="pa-blog-item mb-4 mb-md-4 border-0 col-12 col-md-4 position-relative">
                    <div class="ratio ratio-16x9 mb-2">
                        <figure class="figure">
                            <img src="<?php echo e(check_immg($post->ID, 'full')); ?>" class="figure-img img-fluid rounded m-0 w-100 h-100 object-cover" alt="<?php echo e(get_the_title($post->ID)); ?>">

                            <?php if(get_field('video_length', $post->ID)): ?>
                                <div class="figure-caption position-absolute w-100 h-100 d-block">
                                    <span class="pa-video-time position-absolute px-2 rounded-1">
                                        <i class="far fa-clock me-1" aria-hidden="true"></i> <?= videoLength($post->ID) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </figure>	
                    </div>

                    <a class="stretched-link" href="<?php echo e(get_the_permalink($post->ID)); ?>" title="<?php echo e(get_the_title($post->ID)); ?>">
                        <h3 class="card-title fw-bold h6 pa-truncate-2"><?php echo get_the_title($post->ID); ?></h3>
                    </a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php endif; ?><?php /**PATH /Users/eli/Dropbox (ComunicaDSA)/projects/deploy-videos.adventistas.org/app/pt/wp-content/themes/pa-theme-videos/template-parts/grid-posts.blade.php ENDPATH**/ ?>