<?php

class IASD_ImageGallery {
    const POST_TYPE  = 'pa_image_gallery';
    const TAXONOMY   = 'pa_ig_taxonomy';
    const error_meta      = 'pa_image_gallery_error';

    static $post_type_name  = 'pa_image_gallery';
    static $taxonomy_name   = 'pa_ig_taxonomy';
    static $error_meta      = 'pa_image_gallery_error';

    public static function Init() {
    	self::RegisterType();
    	self::RegisterTaxonomies();

  		wp_register_script( 'pa_plugin_image_gallery', PAPURL_STTC . '/js/pa_plugin_image_gallery.js', array('jquery'), 1, true );
  		wp_enqueue_script('pa_plugin_image_gallery', true);

  		add_filter( 'be_gallery_metabox_post_types', array(__CLASS__, 'MetaBox' ) );
      
      load_plugin_textdomain( 'iasd', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }
	public static function AdminMenu() {
		register_setting('general', 'asf_galeria_permalink_single');
		register_setting('general', 'asf_galeria_permalink_archive');
		add_settings_section('ass_permalinks_galerias', __('Galeria de Imagens', 'iasd'), array(__CLASS__, 'ASSPermalinks'), 'general');
		add_settings_field('asf_galeria_permalink_single', __('Single', 'iasd'), array(__CLASS__, 'ASFPermalinks'), 'general', 'ass_permalinks_galerias', 'single');
		add_settings_field('asf_galeria_permalink_archive', __('Archive', 'iasd'), array(__CLASS__, 'ASFPermalinks'), 'general', 'ass_permalinks_galerias', 'archive');
	}
	public static function ASSPermalinks() {
		echo '<p>' . __('Use os campos abaixo para configurar os permalinks relacionados à galeria de imagens', 'iasd') . '</p>';
	}
	public static function ASFPermalinks($type) {
		switch ($type) {
			case 'single':
				echo '<input name="asf_galeria_permalink_single" id="asf_galeria_permalink_single" type="input" value="'. self::SingleSlug() .'" class="code"  />';
				break;
			case 'archive':
				echo '<input name="asf_galeria_permalink_archive" id="asf_galeria_permalink_archive" type="input" value="'. self::ArchiveSlug() .'" class="widefat"  />';
				break;
		}
	}
	public static function SingleSlug() {
		return get_option('asf_galeria_permalink_single', 'galeria');
	}
	public static function ArchiveSlug() {
		return get_option('asf_galeria_permalink_archive', 'galerias');
	}

    public static function MetaBox($post_types) {
    	foreach($post_types as $k => $post_type) {
    		if($post_type == 'post' || $post_type == 'page')
    			$post_types[$k] = false;
    	}

    	$post_types[] = 'pa_image_gallery';

    	$post_types = array_filter($post_types);

    	return $post_types;
    }

    public static function RegisterTaxonomies() {
        register_taxonomy( self::TAXONOMY, self::POST_TYPE, array(
          'hierarchical' => true,
          'label' => __( 'Categorias de Galerias' ),
          'show_ui' => true
          )
        );
    }

    public static function TypeLabels($return = false) {
        $labels = array(
            'name'              => __('Galerias', 'iasd'),
            'singular_name'     => __('galeria', 'iasd'),
            'add_new'           => __('Adicionar nova', 'iasd'),
            'add_new_item'      => __('Adicionar nova galeria', 'iasd'),
            'edit_item'         => __('Editar galeria', 'iasd'),
            'new_item'          => __('Novo galeria', 'iasd'),
            'view_item'         => __('Visualizar galeria', 'iasd'),
            'search_items'      => __('Buscar galeria', 'iasd')
        );
		if($return && isset($labels[$return]))
			return $labels[$return];
        return $labels;
    }

    public static function TypeIcon() {
        return '';
    }

    public static function TypeSlug() {
		$slug = apply_filters('image_gallery_slug_single', self::SingleSlug());

		return array('slug' => $slug);
    }

    public static function TypeArchiveSlug() {
    	$slug = apply_filters('image_gallery_slug_archive', self::ArchiveSlug());

    	return $slug;
    }

    public static function TypeArguments() {
		$archive_slug = self::TypeArchiveSlug();

        $args = array(
        	'map_meta_cap' => true,
            'labels' => self::TypeLabels(),
            'public' => true,
            'rewrite' => self::TypeSlug(),
            'capability_type' => array( 'post', 'posts' ),
            'hierarchical' => false,
			'has_archive' => $archive_slug,
            'supports' => array('title','thumbnail','excerpt','comments'),
            
          );
        return $args;
    }

    public static function RegisterType() {
        register_post_type( self::POST_TYPE , self::TypeArguments() );
        flush_rewrite_rules(false);
    }

    public static function Nonce() {
        return wp_nonce_field( plugin_basename( __FILE__ ), self::POST_TYPE );
    }

    public static function GalleryMetaboxFilter($types) {
        if(!in_array(self::POST_TYPE, $types))
            $types[] = self::POST_TYPE;

        return $types;
    }

	public static function AutoRefresh($intro) {
		$intro .= "
<script>
	jQuery(document).ready(
		function() { 
			old_tb_remove = tb_remove; 
			tb_remove = function() { 
				old_tb_remove();
				jQuery('#update-gallery').click();
			};
		}
	);
</script>";
		return $intro;
	}

    public static function RSSTreatment($stacked_item, $feedInformation, SimplePie_Item $feedItem) {
        $should_return = $stacked_item;

		$cat_id_Imagens_ANN = 0;
		$categoria_Imagens_Ann = get_term_by('slug', 'imagens-ann', self::TAXONOMY);
		if(!$categoria_Imagens_Ann) {
			$termInformation = wp_create_term('Imagens ANN', self::TAXONOMY);
			$cat_id_Imagens_ANN = $termInformation['term_id'];
		} else {
			$cat_id_Imagens_ANN = $categoria_Imagens_Ann->term_id;
		}


		if(in_array($cat_id_Imagens_ANN, $stacked_item['mybcatid'])) {
			$term_id_Imagens_ANN = 0;
			$term_Imagens_ANN = get_term_by('slug', 'galeria-ann', self::TAXONOMY);
			if(!$term_Imagens_ANN) {
				$termInformation = wp_create_term('Galeria ANN', self::TAXONOMY);
				$term_id_Imagens_ANN = $termInformation['term_id'];
			} else {
				$term_id_Imagens_ANN = $term_Imagens_ANN->term_id;
			}

			echo '<p>-------------</p>';
			$option = 'ann_import_' . md5($stacked_item['mylink']);

			if(!get_option($option) || true) {
				add_option($option, date('Y-m-d H:i:s'), '', 'no');

				$args = array(
					'tax_query' => array(
						array(
							'taxonomy' => self::TAXONOMY,
							'field' => 'id',
							'terms' => array($term_id_Imagens_ANN)
						)
					),
					'post_status' => 'any',
					'post_type' => self::POST_TYPE
				);
				$posts = get_posts($args);
				$post_id = 0;
				if(!count($posts)) {
					$post_array = array(
						'post_status'			=> 'publish',
						'post_title'            => __('Galeria de Imagens Mundo (ANN)', 'iasd'),
						'post_type'             => self::POST_TYPE,
						'tax_input'             => array( self::TAXONOMY => array( $term_id_Imagens_ANN ) )
					);
					$post_id = wp_insert_post($post_array);
				} else {
					$post_id = $posts[0]->ID;
				}
				echo '<p>'.__('Post de Galeria Encontrado: ', 'iasd').get_the_title($post_id).'</p>';

				$enclosures = $feedItem->get_enclosures();
				if(count($enclosures)) {
					echo '<p>'.__('Imagem ANN Encontrada', 'iasd').'</p>';
					$enclosure = $enclosures[0];
					if( !class_exists( 'WP_Http' ) )
						include_once( ABSPATH . WPINC. '/class-http.php' );

					$photoHttp = new WP_Http();
					$photoObject = $photoHttp->request($enclosure->get_link());
					$photo_name = sha1($enclosure->get_title());

					if(is_object($photoObject)) {
						var_dump($photoObject);
						return false;
					}

					$uploaded_bits = wp_upload_bits( $photo_name . '.jpg', null, $photoObject['body'], date("Y-m", strtotime( $photoObject['headers']['last-modified'] ) ) );

					$file_mime_ype = wp_check_filetype( basename( $uploaded_bits['file'] ), null );
					$content = html_entity_decode($enclosure->get_description());

					$date = date('Y-m-d H:i:s', (($feedItem->get_date()) ? strtotime($feedItem->get_date()) : time()));

					$attachment_array = array(
						'post_mime_type'	=> $file_mime_ype['type'],
						'post_title'		=> $enclosure->get_title(),
						'post_excerpt'		=> strip_tags($content, '<BR><br>'),
						'post_content'		=> $content,
						'post_date'			=> $date,
						'post_status'		=> 'inherit',
					);

					$filename = $uploaded_bits['file'];
					$attach_id = wp_insert_attachment( $attachment_array, $filename, $post_id );
					update_post_meta($attach_id, 'rssmi_source_link', $stacked_item['mylink']);

					echo __('<p>Criou o post Attachment</p>', 'iasd');
					delete_post_thumbnail($post_id);
					set_post_thumbnail($post_id, $attach_id);
					echo __('<p>Atualizou Thumbnail</p>', 'iasd');

					if( !function_exists( 'wp_generate_attachment_data' ) )
						require_once(ABSPATH . "wp-admin" . '/includes/image.php');
					$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
					wp_update_attachment_metadata( $attach_id,  $attach_data );
					echo '<p>Atualizações finais do attachment</p>';
					$should_return = false;
				}
				echo '<p>-------------</p>';
			} else {

				echo __('<p>Item já cadastrado!</p>', 'iasd');
			}
		} else if(strpos($feedInformation['FeedName'], self::POST_TYPE)) {
			$should_return['post_type'] = self::POST_TYPE;
		}

		return $should_return;
    }

/// Precisa ser revisto e ajustado!!!
	public static function TemplateProxy($current_template) {
		global $wp, $post,$wp_query;
		$is_archive = is_archive();
		$is_single = is_single();

		if(isset($wp->query_vars['attachment']) || isset($wp->query_vars['post_type']) || $is_archive) {
			if($is_archive || (isset($wp->query_vars['post_type']) && $wp->query_vars['post_type'] == self::POST_TYPE)) {
				if($is_single) {
					$mundo_id = get_option('single_gallery_post_id');
					/*$terms_slugs = array();
					if($post) {
						$terms = wp_get_post_terms($post->ID, self::TAXONOMY);
						foreach($terms as $term)
							$terms_slugs[] = $term->slug;
					}*/

					if($mundo_id == $post->ID) {
						$wp->query_vars['gallery_override_pagination_url'] = true;
						return PAPU_VIEW . '/images_single_mundo.php';
					} else {
						return PAPU_VIEW . '/images_single.php';
					}
				} else if($is_archive && ($is_archive === self::POST_TYPE || (isset($wp->query_vars['post_type']) && $wp->query_vars['post_type'] == self::POST_TYPE))) {
					return PAPU_VIEW . '/images_archive.php';
				}
			}
		}
		if((isset($wp->query_vars['attachment_id']) || isset($wp->query_vars['attachment'])) && is_single()) {
			if($post->post_parent) {
				$parent = get_post($post->post_parent);
				if($parent->post_type == self::POST_TYPE) {
					return PAPU_VIEW . '/images_attachment.php';
				}
			}
		}
		return $current_template;
	}

	public static function PageNumTreatment($result) {
		global $wp;

		if(isset($wp->query_vars['gallery_override_pagination_url'])) {
			$result = preg_replace('/([0-9]+)\\/page\\/([0-9]+)/', '${2}', $result);
			$result = preg_replace('/page\\/([0-9]+)/', '${1}', $result);
			if(isset($wp->query_vars['page']) && $wp->query_vars['page']) {
				$current_page = preg_replace('/[^0-9]/', '', $wp->query_vars['page']);
				$result = preg_replace('/\\/'.$current_page.'\\//', '/', $result);
			}
		}

		return $result;
	}
}

class PAImageGallery extends IASD_ImageGallery {

}

if(apply_filters('IASD_ImageGallery_enabled', true)) {
	add_action( 'init', array('IASD_ImageGallery', 'Init'), 100);
	add_filter( 'be_gallery_metabox_post_types', array('IASD_ImageGallery', 'GalleryMetaboxFilter') );
	add_filter( 'be_gallery_metabox_intro', array('IASD_ImageGallery', 'AutoRefresh') );
	add_filter( 'feed-item-post-rss', array('IASD_ImageGallery', 'RSSTreatment'), 10, 3);
	add_filter( 'get_pagenum_link', array('IASD_ImageGallery', 'PageNumTreatment'), 10, 3);
	//add_action( 'template_include', array('IASD_ImageGallery', 'TemplateProxy'), 100);
	add_action( 'admin_menu', array('IASD_ImageGallery', 'AdminMenu') );
}
