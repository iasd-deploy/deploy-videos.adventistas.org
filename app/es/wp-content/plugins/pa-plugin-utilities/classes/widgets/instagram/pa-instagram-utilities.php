<?php

// function wpiw_init() {

// 	// define some constants.
// 	define( 'WP_INSTAGRAM_WIDGET_JS_URL', plugins_url( '/assets/js', __FILE__ ) );
// 	define( 'WP_INSTAGRAM_WIDGET_CSS_URL', plugins_url( '/assets/css', __FILE__ ) );
// 	define( 'WP_INSTAGRAM_WIDGET_IMAGES_URL', plugins_url( '/assets/images', __FILE__ ) );
// 	define( 'WP_INSTAGRAM_WIDGET_PATH', dirname( __FILE__ ) );
// 	define( 'WP_INSTAGRAM_WIDGET_BASE', plugin_basename( __FILE__ ) );
// 	define( 'WP_INSTAGRAM_WIDGET_FILE', __FILE__ );

// 	// load language files.
// 	load_plugin_textdomain( 'wp-instagram-widget', false, dirname( WP_INSTAGRAM_WIDGET_BASE ) . '/assets/languages/' );
// }
// add_action( 'init', 'wpiw_init' );

function wpiw_widget() {
	register_widget( 'IASD_InstagramWidget' );
}
add_action( 'widgets_init', 'wpiw_widget' );

function carrega_style(){
    wp_register_style( 'style-insta',  plugin_dir_url( __FILE__ ) . 'insta-style.css' );
    wp_enqueue_style( 'style-insta' );
}
add_action( 'wp_enqueue_scripts', 'carrega_style' );

Class IASD_InstagramWidget extends WP_Widget {


    function __construct() {
		parent::__construct(
			'IASD_InstagramWidget','IASD: Instagram Box',
			array(
                'description' => 'Widget Permite criar link para redes sociais',
				'classname' => 'null-instagram-feed',
				'customize_selective_refresh' => true,
			)
		);
	}

	function widget( $args, $instance ) {

		$title = empty( $instance['title'] ) ? '' : apply_filters( 'widget_title', $instance['title'] );
		$username = empty( $instance['username'] ) ? '' : $instance['username'];
		$limit = empty( $instance['number'] ) ? 9 : $instance['number'];
		$size = empty( $instance['size'] ) ? 'large' : $instance['size'];
		$target = empty( $instance['target'] ) ? '_self' : $instance['target'];
		$link = empty( $instance['link'] ) ? '' : $instance['link'];

		echo $args['before_widget'];


		do_action( 'wpiw_before_widget', $instance );

		if ( '' !== $username ) {

			$media_array = $this->scrape_instagram( $username );

			if ( is_wp_error( $media_array ) ) {

				echo wp_kses_post( $media_array->get_error_message() );

			} else {

				// filter for images only?
				if ( $images_only = apply_filters( 'wpiw_images_only', false ) ) {
					$media_array = array_filter( $media_array, array( $this, 'images_only' ) );
				}

				// slice list down to required limit.
				$media_array = array_slice( $media_array, 0, $limit );

				// filters for custom classes.
				$ulclass = apply_filters( 'wpiw_list_class', 'instagram-pics instagram-size-' . $size );
				$liclass = apply_filters( 'wpiw_item_class', '' );
				$aclass = apply_filters( 'wpiw_a_class', '' );
				$imgclass = apply_filters( 'wpiw_img_class', '' );
				$template_part = apply_filters( 'wpiw_template_part', 'parts/wp-instagram-widget.php' );

				switch ( substr( $username, 0, 1 ) ) {
				case '#':
					$url = '//instagram.com/explore/tags/' . str_replace( '#', '', $username );
					break;

				default:
					$url = '//instagram.com/' . str_replace( '@', '', $username );
					break;
				}

				?>
				<div class="iasd-widget col-md-4">

					<?php if ( ! empty( $title ) ) { echo $args['before_title'] . wp_kses_post( $title ) . $args['after_title']; }; ?>
					
					<div class="box box-insta-thumb">
						<div class="box-heder">
                            <a href="<?php echo trailingslashit( esc_url($url)); ?>" target="_blank" title="<?php echo $trr->user->full_name; ?>">
                                <img class="img_box img-circle" src="<?php print_r($media_array[0]['perfil']); ?>" height="40" width="40">
                            </a>
                            <div class="box-name">
                                <a href="<?php echo trailingslashit( esc_url($url)); ?>" target="_blank" title="<?php echo  $username; ?>">
                                    <?php echo  $username; ?>
                                </a>
                            </div>
                            <div class="box-button">
                                <a style="color: white !important;" href="<?php echo trailingslashit(esc_url($url)); ?>" target="_blank">Seguir</a>
                            </div>
                        </div>
						<ul class="no-bullet thumbnails box_col_3 <?php echo esc_attr( $ulclass ); ?>">
							<?php
				foreach( $media_array as $item ) {
					// copy the else line into a new file (parts/wp-instagram-widget.php) within your theme and customise accordingly.
					if ( locate_template( $template_part ) !== '' ) {
						include locate_template( $template_part );
					} else {
						echo '<li class="' . esc_attr( $liclass ) . '"><a href="' . esc_url( $item['link'] ) . '" target="' . esc_attr( $target ) . '"  class="' . esc_attr( $aclass ) . '"><img src="' . esc_url( $item[$size] ) . '"  alt="' . esc_attr( $item['description'] ) . '" title="' . esc_attr( $item['description'] ) . '"  class="' . esc_attr( $imgclass ) . '"/></a></li>';
					}
				}
				?></ul></div></div><?php
			}
		}

		$linkclass = apply_filters( 'wpiw_link_class', 'clear' );
		$linkaclass = apply_filters( 'wpiw_linka_class', '' );


		do_action( 'wpiw_after_widget', $instance );

		echo $args['after_widget'];
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
			'title' => 'Instagram',
			'username' => '',
			'size' => 'large',
			'link' => 'Follow Me!',
			'number' => 9,
			'target' => '_blank',
		) );
		$title = $instance['title'];
		$username = $instance['username'];
		$number = absint( $instance['number'] );
		$size = $instance['size'];
		$target = $instance['target'];
		$link = $instance['link'];
		?>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">Título: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></label></p>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>">@username: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'username' ) ); ?>" type="text" value="<?php echo esc_attr( $username ); ?>" /></label></p>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>">Número de imagens: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" /></label></p>
		<p style="display:none;"><label for="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>"><?php esc_html_e( 'Photo size', 'wp-instagram-widget' ); ?>:</label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'size' ) ); ?>" class="widefat">
				<option value="thumbnail" <?php selected( 'thumbnail', $size ); ?>><?php esc_html_e( 'Thumbnail', 'wp-instagram-widget' ); ?></option>
				<option value="small" <?php selected( 'small', $size ); ?>><?php esc_html_e( 'Small', 'wp-instagram-widget' ); ?></option>
				<option value="large" <?php selected( 'large', $size ); ?>><?php esc_html_e( 'Large', 'wp-instagram-widget' ); ?></option>
				<option value="original" <?php selected( 'original', $size ); ?>><?php esc_html_e( 'Original', 'wp-instagram-widget' ); ?></option>
			</select>
		</p>
		<p style="display:none;"><label for="<?php echo esc_attr( $this->get_field_id( 'target' ) ); ?>"><?php esc_html_e( 'Open links in', 'wp-instagram-widget' ); ?>:</label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'target' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'target' ) ); ?>" class="widefat">
				<option  value="_self" ><?php esc_html_e( 'Current window (_self)', 'wp-instagram-widget' ); ?></option>
				<option value="_blank" selected="selected"><?php esc_html_e( 'New window (_blank)', 'wp-instagram-widget' ); ?></option>
			</select>
		</p>
		<p style="display:none;"><label for="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>"><?php esc_html_e( 'Link text', 'wp-instagram-widget' ); ?>: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link' ) ); ?>" type="text" value="<?php echo esc_attr( $link ); ?>" /></label></p>
		<?php

	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['username'] = trim( strip_tags( $new_instance['username'] ) );
		$instance['number'] = ! absint( $new_instance['number'] ) ? 9 : $new_instance['number'];
		$instance['size'] = ( ( 'thumbnail' === $new_instance['size'] || 'large' === $new_instance['size'] || 'small' === $new_instance['size'] || 'original' === $new_instance['size'] ) ? $new_instance['size'] : 'large' );
		$instance['target'] = ( ( '_self' === $new_instance['target'] || '_blank' === $new_instance['target'] ) ? $new_instance['target'] : '_self' );
		$instance['link'] = strip_tags( $new_instance['link'] );
		return $instance;
	}

	// based on https://gist.github.com/cosmocatalano/4544576.
	function scrape_instagram( $username ) {

		$username = trim( strtolower( $username ) );

		switch ( substr( $username, 0, 1 ) ) {
			case '#':
				$url              = 'https://instagram.com/explore/tags/' . str_replace( '#', '', $username );
				$transient_prefix = 'h';
				break;

			default:
				$url              = 'https://instagram.com/' . str_replace( '@', '', $username );
				$transient_prefix = 'u';
				break;
		}

		if ( false === ( $instagram = get_transient( 'insta-a10-' . $transient_prefix . '-' . sanitize_title_with_dashes( $username ) ) ) ) {

			$remote = wp_remote_get( $url );

			if ( is_wp_error( $remote ) ) {
				return new WP_Error( 'site_down', 'Unable to communicate with Instagram.' );
			}

			if ( 200 !== wp_remote_retrieve_response_code( $remote ) ) {
				return new WP_Error( 'invalid_response', 'Instagram did not return a 200.');
			}

			$shards      = explode( 'window._sharedData = ', $remote['body'] );
			$insta_json  = explode( ';</script>', $shards[1] );
			$insta_array = json_decode( $insta_json[0], true );


			if ( ! $insta_array ) {
				return new WP_Error( 'bad_json', 'Instagram has returned invalid data - Erro: 1');
			}


			if ( isset( $insta_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'] ) ) {
				$images = $insta_array['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'];
				$perfil = $insta_array['entry_data']['ProfilePage'][0]['graphql']['user']['profile_pic_url_hd'];
			} elseif ( isset( $insta_array['entry_data']['TagPage'][0]['graphql']['hashtag']['edge_hashtag_to_media']['edges'] ) ) {
				$images = $insta_array['entry_data']['TagPage'][0]['graphql']['hashtag']['edge_hashtag_to_media']['edges'];
			} else {
				return new WP_Error( 'bad_json_2','Instagram has returned invalid data - Erro: 2' );
			}

			if ( ! is_array( $images ) ) {
				return new WP_Error( 'bad_array', 'Instagram has returned invalid data - Erro: 3' );
			}

			$instagram = array();

			// var_dump($perfil);
			// die;

			foreach ( $images as $image ) {
				if ( true === $image['node']['is_video'] ) {
					$type = 'video';
				} else {
					$type = 'image';
				}

				$caption = 'Instagram Image';
				if ( ! empty( $image['node']['edge_media_to_caption']['edges'][0]['node']['text'] ) ) {
					$caption = wp_kses( $image['node']['edge_media_to_caption']['edges'][0]['node']['text'], array() );
				}

				$instagram[] = array(
					'description' => $caption,
					'link'        => trailingslashit( '//instagram.com/p/' . $image['node']['shortcode'] ),
					'time'        => $image['node']['taken_at_timestamp'],
					'comments'    => $image['node']['edge_media_to_comment']['count'],
					'likes'       => $image['node']['edge_liked_by']['count'],
					'thumbnail'   => preg_replace( '/^https?\:/i', '', $image['node']['thumbnail_resources'][0]['src'] ),
					'small'       => preg_replace( '/^https?\:/i', '', $image['node']['thumbnail_resources'][2]['src'] ),
					'large'       => preg_replace( '/^https?\:/i', '', $image['node']['thumbnail_resources'][4]['src'] ),
					'original'    => preg_replace( '/^https?\:/i', '', $image['node']['display_url'] ),
					'type'        => $type,
					'perfil'	  => $perfil,
				);
			} // End foreach().

			// do not set an empty transient - should help catch private or empty accounts.
			if ( ! empty( $instagram ) ) {
				$instagram = base64_encode( serialize( $instagram ) );
				set_transient( 'insta-a10-' . $transient_prefix . '-' . sanitize_title_with_dashes( $username ), $instagram, apply_filters( 'null_instagram_cache_time', HOUR_IN_SECONDS * 2 ) );
			}
		}

		if ( ! empty( $instagram ) ) {

			return unserialize( base64_decode( $instagram ) );

		} else {

			return new WP_Error( 'no_images', 'Instagram did not return any images.');

		}
	}

	function images_only( $media_item ) {

		if ( 'image' === $media_item['type'] ) {
			return true;
		}

		return false;
	}
}
