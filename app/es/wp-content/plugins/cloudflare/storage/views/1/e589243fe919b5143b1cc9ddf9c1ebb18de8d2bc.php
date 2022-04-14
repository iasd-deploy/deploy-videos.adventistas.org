
<?php if(get_field('video_url', get_the_ID())): ?>
    <div class="row mb-3">
        <div class="col-12">
          <?php if(get_field('video_url', get_the_ID())): ?>
            <div class="embed-container">
              <?php echo get_field('video_url', get_the_ID()); ?>  
            </div>
          <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<div class="row my-4">
    <div class="col-md mb-4 mb-md-0 d-flex flex-column align-items-start">
        
        <h1 class="single-title mb-2"><?php echo e(the_title()); ?></h1>

        
        <div class="figure-caption d-flex align-items-center justify-content-start">
            <?php if(get_field('video_url', get_the_ID())): ?>
                <span class="pa-video-time rounded-1">
                    <em class="far fa-clock me-1"></em>        

                    <?= videoLength(get_the_ID()) ?>
                </span>

                <span class="mx-2">|</span>
            <?php endif; ?>

            <span><?= getPrioritySeat(get_the_ID()) ?> </span> 
        </div>
    </div>
    
    
    <div class="col-auto">
        <div class="pa-share">
            <ul class="list-inline">
                <li class="list-inline-item"><?php echo e(__('Share:', 'iasd')); ?> </li>

                
                <li class="list-inline-item">
                    <a target="_blank" rel="noopener" href="<?php (linkToShare(get_the_ID(), 'twitter')); ?>">
                        <em class="fab fa-twitter"></em>
                    </a>
                </li>

                
                <li class="list-inline-item">
                    <a target="_blank" rel="noopener" href="<?php (linkToShare(get_the_ID(), 'facebook')); ?>">
                        <em class="fab fa-facebook-f"></em>
                    </a>
                </li>

                
                <li class="list-inline-item">
                    <a target="_blank" rel="noopener" href="<?php (linkToShare(get_the_ID(), 'whatsapp')); ?>" >
                        <em class="fab fa-whatsapp"></em>
                    </a>
                </li>
            
            </ul>
        </div>
    </div>
</div>
<?php /**PATH /Users/isaltino/Git/deploy-videos.adventistas.org/app/es/wp-content/themes/pa-theme-videos/template-parts/single/header.blade.php ENDPATH**/ ?>