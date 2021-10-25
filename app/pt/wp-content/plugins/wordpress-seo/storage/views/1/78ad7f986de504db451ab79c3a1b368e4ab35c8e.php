<?php if (! empty($post)) : ?>
    <div class="pa-blog-itens mb-5">
        <h2 class="mb-4"><?php echo e(__('Feature', 'iasd')); ?></h2>

        <div class="pa-blog-feature pa-w-list-videos">
            <a href="<?php echo e(get_the_permalink($post->ID)); ?>" title="<?php echo e(get_the_title($post->ID)); ?>">
                <div class="ratio ratio-16x9">
                    <figure class="figure m-xl-0 w-100">
                        <img src="<?php echo e(check_immg($post->ID, 'full')); ?>" class="figure-img img-fluid m-0 rounded w-100 h-100 object-cover" alt="<?php echo e(get_the_title($post->ID)); ?>">

                        <?php if(get_field('video_length', $post->ID)): ?>
                            <div class="figure-caption position-absolute w-100 h-100 d-block">
                                <i class="fas fa-play position-absolute top-50 start-50 translate-middle pa-icon-play"></i>
                                <span class="pa-video-time position-absolute px-2 rounded-1">
                                    <i class="far fa-clock me-1" aria-hidden="true"></i> <?= videoLength($post->ID) ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </figure>
                </div>
            </a>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH /Users/eli/Dropbox (ComunicaDSA)/projects/deploy-videos.adventistas.org/app/pt/wp-content/themes/pa-theme-videos/template-parts/feature.blade.php ENDPATH**/ ?>