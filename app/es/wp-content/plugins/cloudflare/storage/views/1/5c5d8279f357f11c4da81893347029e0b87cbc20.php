<?php
    $relatedPosts = getRelatedPosts(get_the_ID());
?>

<?php if(empty(!$relatedPosts)): ?>
    
    <div class="row mb-4">
        <div class="col-12">
            <h2><?php echo e(__('Related video', 'iasd')); ?></h2>
        </div>
    </div>

    
    <div class="pa-blog-itens mb-4">
        <h2 class="mb-4"><?php echo e(isset($title) ? $title : single_term_title()); ?></h2>
        
        <div class="row pa-w-list-videos">
            <?php $__currentLoopData = $relatedPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="pa-blog-item mb-4 mb-md-4 border-0 col-12 col-md-4 position-relative">
                    <div class="ratio ratio-16x9 mb-2">
                        <figure class="figure">
                            <img src="<?php echo e(check_immg($post->ID, 'medium')); ?>" class="figure-img img-fluid rounded m-0 w-100 h-100 object-cover" alt="<?php echo e(get_the_title($post->ID)); ?>">

                            <?php if(get_field('video_length', $post->ID)): ?>
                                <div class="figure-caption position-absolute w-100 h-100 d-block">
                                    <span class="pa-video-time position-absolute px-2 rounded-1">
                                        <em class="far fa-clock me-1" aria-hidden="true"></em> <?= videoLength($post->ID) ?>
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
<?php endif; ?>


<?php /**PATH /Users/isaltino/Git/deploy-videos.adventistas.org/app/es/wp-content/themes/pa-theme-videos/template-parts/single/related-posts.blade.php ENDPATH**/ ?>