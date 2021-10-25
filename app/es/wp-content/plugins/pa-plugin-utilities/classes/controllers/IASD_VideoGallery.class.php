<?php


class IASD_VideoGallery {

	static $text_domain     = 'pa-video-gallery';
	static $post_type_name  = 'pa_video_gallery';
	static $field_name      = 'pa_video_gallery_video_share_url_field';
	static $error_meta      = 'pa_video_gallery_error';
	static $taxonomy_name   = 'pa_vg_taxonomy';

	public static function Init() {
		self::RegisterType();
		self::RegisterTaxonomies();
		self::ThumbSize();
	}

	public static function AdminMenu() {
		register_setting('general', 'asf_video_permalink_single');
		register_setting('general', 'asf_video_permalink_archive');
		add_settings_section('ass_permalinks_videos', __('Galerias de Videos', 'iasd'), array(__CLASS__, 'ASSPermalinks'), 'general');
		add_settings_field('asf_video_permalink_single', __('Single', 'iasd'), array(__CLASS__, 'ASFPermalinks'), 'general', 'ass_permalinks_videos', 'single');
		add_settings_field('asf_video_permalink_archive', __('Archive', 'iasd'), array(__CLASS__, 'ASFPermalinks'), 'general', 'ass_permalinks_videos', 'archive');
	}
	public static function ASSPermalinks() {
		echo '<p>' . __('Use os campos abaixo para configurar os permalinks relacionados à galeria de videos', 'iasd') . '</p>';
	}
	public static function ASFPermalinks($type) {
		switch ($type) {
			case 'single':
				echo '<input name="asf_video_permalink_single" id="asf_video_permalink_single" type="input" value="'. self::SingleSlug() .'" class="code"  />';
				break;
			case 'archive':
				echo '<input name="asf_video_permalink_archive" id="asf_video_permalink_archive" type="input" value="'. self::ArchiveSlug() .'" class="code"  />';
				break;
		}
	}
	public static function SingleSlug() {
		return get_option('asf_video_permalink_single', 'video');
	}
	public static function ArchiveSlug() {
		return get_option('asf_video_permalink_archive', 'videos');
	}


	public static function ThumbSize()
	{
		if ( function_exists( 'add_image_size' ) ) {
			add_image_size( 'thumb_300x170', 300, 170, true ); //(cropped)
			add_image_size( 'thumb_60x35', 60, 35, true ); //(cropped)
		}
	}

	public static function RegisterTaxonomies() {
		register_taxonomy( self::$taxonomy_name, self::$post_type_name, array(
				'hierarchical' => true,
				'label' => __( 'Categorias de Videos', 'iasd' ),
				'show_ui' => true
			)
		);
	}

	public static function TypeLabels() {
		$labels = array(
			'name'              => __('Videos', 'iasd'),
			'singular_name'     => __('video', 'iasd'),
			'add_new'           => __('Adicionar novo', 'iasd'),
			'add_new_item'      => __('Adicionar novo video', 'iasd'),
			'edit_item'         => __('Editar video', 'iasd'),
			'new_item'          => __('Novo video', 'iasd'),
			'view_item'         => __('Visualizar video', 'iasd'),
			'search_items'      => __('Buscar videos', 'iasd')
		);
		return $labels;
	}

	public static function TypeIcon() {
		return '';
	}

	public static function TypeSlug() {
		$slug = apply_filters('video_gallery_slug_single', self::SingleSlug());

		return array('slug' => $slug);
	}

	public static function TypeArguments() {
		$archive_slug = apply_filters('video_gallery_slug_archive', self::ArchiveSlug());

		$args = array(
			'map_meta_cap' => true,
			'labels' => self::TypeLabels(),
			'public' => true,
			'rewrite' => self::TypeSlug(),
			'capability_type' => array('post', 'posts'),
			'hierarchical' => false,
			'show_in_rest' => true,
			'supports' => array('title','thumbnail','excerpt','comments'),
			'has_archive' => $archive_slug,
			'menu_position' => 7774
		);
		return $args;
	}

	public static function RedirectArchive($templates) {
		return self::RedirectTemplate($templates, 'archive');
	}
	public static function RedirectSingle($templates) {
		return self::RedirectTemplate($templates, 'single');
	}

	public static function RedirectTemplate($templates, $type = '') {
		global $wp, $wp_query;
		if(isset($wp->query_vars['post_type']) && $wp->query_vars['post_type'] == self::$post_type_name) {
			switch ($type) {
				case 'archive':
					$templates = PAPU_VIEW . DIRECTORY_SEPARATOR . 'videos_archive.php';
					break;
				case 'single':
					$templates = PAPU_VIEW . DIRECTORY_SEPARATOR . 'videos_single.php';
					break;
			}
		}

		return $templates;
	}

	public static function ft_job_cpt_template_filter( $content ) {

		global $wp_query;
		$jobID = $wp_query->post->ID;

		$output = ''; // Build markup fetching info from postmeta

		return $output;
	}

	public static function RegisterType() {
		register_post_type( self::$post_type_name , self::TypeArguments() );
		flush_rewrite_rules(false);
	}

	public static function Nonce() {
		return wp_nonce_field( plugin_basename( __FILE__ ), self::$post_type_name );
	}

	public static function SavePost($post_id) {
		$post = get_post($post_id);
		if($post->post_type == self::$post_type_name ) {
			if(!wp_is_post_revision( $post ) && !wp_is_post_autosave( $post ) && count($_POST)) {
				if(isset($_POST[self::$field_name])) {
					$video_share_url_unfiltered = $_POST[self::$field_name];

					$video_share_url = filter_var($video_share_url_unfiltered, FILTER_VALIDATE_URL);

					if($video_share_url) {
						update_post_meta($post_id, self::$field_name, $video_share_url);
						$post->post_content = $video_share_url;

						remove_action( 'save_post', array(__CLASS__, 'SavePost') );

						wp_update_post($post);
						if(class_exists('Video_Thumbnails')) {
							$vtn = new Video_Thumbnails();
							$vtn->settings->options['set_fatured'] = true;
							$vtn->settings->options['save_media'] = true;
							$vtn->settings->options['custom_field'] = self::$field_name;
							$new_thumbnail = $vtn->get_video_thumbnail($post_id);
						}

						add_action( 'save_post', array(__CLASS__, 'SavePost') );
					} else if($video_share_url_unfiltered) {
						$current_user = wp_get_current_user();
						update_user_meta( $current_user->ID, self::$error_meta, __('O endereço usado não é válido. Preencha o campo de acordo com as instruções.', 'iasd') );
					}
				} else {
					$current_user = wp_get_current_user();
					update_user_meta( $current_user->ID, self::$error_meta, __('O campo de video não foi encontrado', 'iasd') );
				}
			}
		}
	}
	//META BOX
	public static function AddMetaBox() {
		add_meta_box( self::$post_type_name . '_video_share_url',
			'Endereço do Video',
			array(__CLASS__, 'AddMetaBoxVideoUrl'),
			self::$post_type_name,
			'normal',
			'high'
		);
	}

	public static function AddMetaBoxVideoUrl( $post ) {
		self::Nonce();

		$video_share_url = get_post_meta($post->ID, self::$field_name, true);
		echo '<textarea class="attachmentlinks" id="' . self::$field_name . '" name="'.self::$field_name . '">'.$video_share_url.'</textarea><br />';
		echo '<label for="'.self::$field_name . '">'. __('Por favor, preencha o campo acima com o endereço do video. Para isso, acesse o video, no YouTube ou Vimeo, clique em SHARE (Compartilhar) e copie a URL exibida.', 'iasd') .'</label> ';
	}

	public static function AdminNotices() {
		$current_user = wp_get_current_user();
		$error = get_user_meta($current_user->ID, self::$error_meta, true);
		if(!$error)
			return false;
		$msg = '
		<div class="error">
			<p>'.$error.'</p>
		</div>
';
		echo $msg;
		update_user_meta( $current_user->ID, self::$error_meta, '' );
	}
}

class PAVideoGallery extends IASD_VideoGallery {

}


if(apply_filters('IASD_VideoGallery_enabled', true)) {
	add_action('init', array('IASD_VideoGallery', 'Init'), 100);
	add_action('admin_menu', array('IASD_VideoGallery', 'AdminMenu'), 100);
	add_action('add_meta_boxes', array('IASD_VideoGallery', 'AddMetaBox') );
	add_action('save_post', array('IASD_VideoGallery', 'SavePost') );
	add_action('admin_notices', array('IASD_VideoGallery', 'AdminNotices') );
}
