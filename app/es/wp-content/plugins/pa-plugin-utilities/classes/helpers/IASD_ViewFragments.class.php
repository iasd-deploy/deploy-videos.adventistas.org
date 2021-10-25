<?php


class IASD_ViewFragments {
	static function SocialWidgets($title = '', $url = '') {
		$url = get_permalink();
		$url_formated = str_replace(":", "%3A", str_replace("/", "%2F", $url));
?>
			<ul class="social-media">
				<div id="fb-root"></div>
				<script>(function(d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) return;
				js = d.createElement(s); js.id = id;
				js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.1&appId=221752697999220&autoLogAppEvents=1';
				fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));</script>
				<li class="facebook"><div class="fb-share-button" data-href="<?php echo $url; ?>" data-layout="button_count" data-size="small" data-mobile-iframe="true"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url_formated; ?>&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Like</a></div></li>
				<li class="twitter"><a href="https://twitter.com/share" class="twitter-share-button" data-lang="pt"><?php _e('Tweetar', 'iasd'); ?></a><script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></li>
				<li class="whatsapp" style="visibility:hidden;">
					<a href="whatsapp://send?text=<?php echo $url_formated; ?>" data-action="share/whatsapp/share">
						<svg aria-hidden="true" data-prefix="fab" data-icon="whatsapp" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="icon-whatsapp">
							<path fill="currentColor" d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z" class=""></path>
						</svg>
						<span>Whatsapp</span>
					</a>
				</li>
			</ul>
			<ul class="functions">
				<li><a href="javascript:window.print();" class="print" title="<?php _e('Imprimir página', 'iasd'); ?>"><?php _e('Imprimir página', 'iasd'); ?></a></li>
				<li><a href="mailto:?subject=<?php _e('Leia este artigo do Portal Adventista', 'iasd'); ?>&body=<?php single_post_title(); ?> - <?php the_permalink() ?>" class="mail" title="<?php _e('Enviar por e-mail', 'iasd'); ?>"><?php _e('Enviar por e-mail', 'iasd'); ?></a></li>
			</ul>
<?php
	}

	static function PostNavigation() {
?>
			<div id="iasd-page-prevnext">
				<div class="page-prevnext">
						<?php 
							$prev_post = get_adjacent_post(false, '', false); 
							if(!empty($prev_post)) {
								$adjacent_post_type = get_post_type_object($prev_post->post_type);
								$adjacent_author = get_post($prev_post->ID);
								echo '<a href="' . get_permalink($prev_post->ID) . '" title="'. sprintf(__('Clique para ir ao anterior', 'iasd')) .'" ><div class="btn btn-default">«</div></a>
								<span class="hidden-xs">
									<em>'.sprintf(__('Anterior', 'iasd')) . '</em>
									<a href="' . get_permalink($prev_post->ID) . '" title="'. sprintf(__('Clique para ir ao anterior', 'iasd')) .'">' . $prev_post->post_title . '</a>
								</span>
								<a href="' . get_permalink($prev_post->ID) . '" class="single visible-xs" title="'. sprintf(__('Clique para ir ao anterior', 'iasd')) .'">'.__('Anterior', 'iasd') . '</a>';
							}
						?>
				</div>
				<div class="page-prevnext pull-right">
						<?php
							$next_post = get_adjacent_post(false, '', true); 
							if(!empty($next_post)) {
								echo '<a href="' . get_permalink($next_post->ID) . '" title="'.sprintf(__('Clique para ir ao próximo', 'iasd')) . '"><div class="btn btn-default">»</div></a>
								<span class="hidden-xs">
									<em>'.sprintf(__('Próximo', 'iasd')) . ':</em>
									<a href="' . get_permalink($next_post->ID) . '" title="'.sprintf(__('Clique para ir ao próximo', 'iasd')) . '">' . $next_post->post_title . '</a>
								</span>
								<a href="' . get_permalink($next_post->ID) . '" class="single visible-xs" title="'.sprintf(__('Clique para ir ao próximo', 'iasd')) . '">'.__('Próximo', 'iasd') . '</a>';
							}
						?>
				</div>
			</div>
<?php
	}

	static function WrongSizeHtml() {
?>
						<div class="alert alert-danger">
							<strong>Atenção!</strong> O tamanho do Widget selecionado é incompatível com esta Sidebar. Por favor, reveja suas configurações.
						</div>
<?php
	}
}

add_action('sharing_links', array('IASD_ViewFragments','SocialWidgets'), 10, 2 );
add_action('post_navigation', array('IASD_ViewFragments','PostNavigation'));
add_action('load_more', array('IASD_ViewFragments','LoadMore'));



