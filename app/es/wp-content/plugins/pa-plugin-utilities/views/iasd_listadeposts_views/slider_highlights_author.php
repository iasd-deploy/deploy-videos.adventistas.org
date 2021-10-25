<div id="<?php echo $this->slug(); ?>" class="iasd-widget iasd-widget-slider <?php echo $this->widgetAddClasses(); ?>">
	<?php echo $this->widgetTitle(); ?>

	<div class="owl-carousel posts header">
<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			// var_dump();
			$post_title = the_title('', '', false);
?>
		<div class="slider-item">
			<a href="<?php the_permalink(); ?>" title="<?php echo $post_title, '. ', __('Clique para ler a matéria completa', 'iasd'); ?>.">
				<figure>
					<img data-src="<?php echo $this->getThumbnail('thumb_720x300'); ?>" alt="<?php echo $this->getThumbnailName(); ?>" class="lazyOwl">
					<figcaption>
						<figure class="img-circle">
							<?php echo get_avatar( $post->post_author, 65 ); ?>
							<?php /* <img src="http://placehold.it/65x65/" alt="Título da Imagem"> */ ?>
							<div class="img-gradient"></div>		
						</figure>
						<h2><?php echo $post_title; ?></h2>
						<?php if($this->widgetView('show_intro')) echo '<p><i>', get_the_author_meta( 'display_name', $posts_list[ $i ]->post_author ), '</i></p>'; ?>
						<ul>
							<li class="icon_clock"><time><?php the_time( 'd \d\e F \d\e Y' ); ?></time></li>
							<?php
							$tags_list = get_the_terms( $post->ID, get_taxonomies( array( 'public' => true ) ) );
							if ( $tags_list ) :
							?>
								<li class="icon_tag">
									<ul>
										<?php 
										$count = count( $tags_list ) - 1;
										foreach ( $tags_list as $i=>$tag ) :
										?>
										<li><a href="#" title="Tag"><?php echo $tag->name; ?></a><?php if ( $count != $i ) : echo ','; else : echo ''; endif; ?></li>
									<?php endforeach; ?>
									</ul>
								</li>
							<?php endif; ?>
							<li class="icon_comments"><a href="<?php comments_link(); ?>" title="<?php _e( 'Comentários' ); ?>"><?php comments_number( 'Não há comentários', '1 comentário', '% Comentários' ); ?></a></li>
						</ul>
					</figcaption>
				</figure>
			</a>
		</div>
<?php
			
		} // end while
	} // end if
?>
	</div>
	<?php IASD_ViewFragments::WrongSizeHtml(); ?>
</div>