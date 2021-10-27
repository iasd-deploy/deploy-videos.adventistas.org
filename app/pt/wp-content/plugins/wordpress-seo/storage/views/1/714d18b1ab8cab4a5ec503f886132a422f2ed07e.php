<?php if(is_admin()): ?>
    <img class="img-preview" src="<?php echo e(get_stylesheet_directory_uri()); ?>/Blocks/PACarouselVideos/preview.png" alt="<?php echo e(__('Illustrative image of the front end of the block.', 'iasd')); ?>"/>
<?php else: ?>
    <?php if (! empty($items)) : ?> 
        <div class="pa-widget pa-w-carousel-videos col-12 mb-5">
            <div class="pa-glide-videos">
                            
                <div class="pa-slider-controle d-flex align-items-center mb-4">
                    <h2 class="flex-grow-1"><?php echo $title; ?></h2>	

                    <div class="d-none d-xl-block" data-glide-el="controls">
                        <span class="fa-stack" data-glide-dir="&lt;">
                            <i class="fas fa-circle fa-stack-2x"></i>
                            <i class="icon fas fa-arrow-left fa-stack-1x"></i>
                        </span>
                    </div>
                    
                    <div class="d-none d-xl-block" data-glide-el="controls">
                        <span class="fa-stack" data-glide-dir="&gt;">
                            <i class="fas fa-circle fa-stack-2x"></i>
                            <i class="icon fas fa-arrow-right fa-stack-1x"></i> 
                        </span>
                    </div>
                </div>
                
                
                <div class="glide__track" data-glide-el="track">
                    <div class="glide__slides">    
                        
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="glide__slide">
                                <a href="<?php echo e(get_the_permalink($id)); ?>" title="<?php echo wp_strip_all_tags(get_the_title($id)); ?>">
                                    <div class="ratio ratio-16x9 mb-2">
                                        <figure class="figure">
                                            <img src="<?php echo e(check_immg($id, 'medium')); ?>" class="figure-img img-fluid rounded m-0" alt="<?php echo wp_strip_all_tags(get_the_title($id)); ?>">
                                            
                                            <?php if(get_field('video_length', $id)): ?>
                                                <div class="figure-caption position-absolute w-100 h-100 d-block">
                                                    <span class="pa-video-time position-absolute px-2 rounded-1">
                                                        <i class="far fa-clock me-1" aria-hidden="true"></i> <?= videoLength($id) ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                        </figure>
                                    </div>

                                    <h3 class="card-title fw-bold h6"><?php echo wp_strip_all_tags(get_the_title($id)); ?></h3>
                                </a>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php /**PATH /Users/isaltino/Git/deploy-videos.adventistas.org/app/pt/wp-content/themes/pa-theme-videos/Blocks/PACarouselVideos/views/frontend.blade.php ENDPATH**/ ?>