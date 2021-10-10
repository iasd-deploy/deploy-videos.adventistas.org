<?php if(is_admin()): ?>
    <img class="img-preview" src="<?php echo e(get_stylesheet_directory_uri()); ?>/Blocks/PAFeaturePost/preview.png" />
<?php else: ?>
    <div class="col-lg-8">
        <div class="pa-blog-itens mb-5">    
            <h2 class="mb-4"><?php echo e($title); ?></h2>

            <div class="pa-blog-feature pa-w-list-videos">
                
                <a href="<?php echo e(get_the_permalink($id)); ?>" title="<?php echo e(get_the_title($id)); ?>">
                    <div class="ratio ratio-16x9">
                        <figure class="figure m-xl-0 w-100">
                            <img src="<?php echo e(check_immg($id, 'full')); ?>" class="figure-img img-fluid m-0 rounded w-100 h-100 object-cover" alt="<?php echo e(get_the_title($id)); ?>">

                            <?php if(get_field('video_length', $id)): ?>
                                <div class="figure-caption position-absolute w-100 h-100 d-block">
                                    <span class="pa-video-time position-absolute px-2 rounded-1">
                                        <i class="far fa-clock me-1" aria-hidden="true"></i> <?= videoLength($id) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </figure>
                    </div>
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH /Users/eli/Dropbox (ComunicaDSA)/projects/videos.adventistas.org-old/pt/wp-content/themes/pa-theme-videos/Blocks/PAFeaturePost/views/frontend.blade.php ENDPATH**/ ?>