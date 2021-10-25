<?php
/**
Tamanhos Oficiais
*/

class IASD_DefaultImage {
	static function Init() {
		$imageSizes = self::ImageSizes();

		foreach($imageSizes as $thumbName => $imageSize) {
			list($width, $height) = explode('x', $imageSize);
			if ($height == 'AUTO') {
				add_image_size($thumbName, $width);
			} else {
				add_image_size($thumbName, $width, $height, true);
			}
		}
		add_filter('image_downsize', array(__CLASS__, 'Downsize'), 100, 3);

		self::EnableDetour();

		add_filter( 'pre_option_thumbnail_size_w', function() { return 140; });
		add_filter( 'pre_option_thumbnail_size_h', function() { return  90; });
		add_filter( 'pre_option_medium_size_w',    function() { return 290; });
		add_filter( 'pre_option_medium_size_h',    function() { return 220; });
		add_filter( 'pre_option_large_size_w',     function() { return 617; });
		add_filter( 'pre_option_large_size_h',     function() { return 460; });

		add_action('enable_default_image', array(__CLASS__, 'EnableDetour'));
		add_action('disable_default_image', array(__CLASS__, 'EnableDetour'));
	}

	static function EnableDetour() {
		global $as3cf;
		add_filter('post_thumbnail_html', array(__CLASS__, 'HtmlFilter'), 100, 5);
		if (isset($as3cf) && !has_filter( 'wp_get_attachment_url', array( $as3cf, 'wp_get_attachment_url' ))){
			add_filter( 'wp_get_attachment_url', array( $as3cf, 'wp_get_attachment_url' ), 9, 2 );
		}
		add_filter('wp_get_attachment_url', array(__CLASS__, 'UrlFilter'), 100, 2);
	}

	static function DisableDetour() {
		remove_filter('post_thumbnail_html', array(__CLASS__, 'HtmlFilter'), 100, 5);
		remove_filter('wp_get_attachment_url', array(__CLASS__, 'UrlFilter'), 100, 2);
	}

	static function ImageSizes() {
		return array(
				'thumbnail'      => '140x90',
				'medium'         => '290x220',
				'large'          => '617x460',
				'thumb_40x40'    => '40x40',
				'thumb_70x45'    => '70x45',
				'thumb_80x80'    => '80x80',
				'thumb_95x95'    => '95x95',
				'thumb_124x124'  => '124x124',
				'thumb_132x85'   => '132x85',
				'thumb_140x90'   => '140x90',
				'thumb_140x140'  => '140x140',
				'thumb_145x190'  => '145x190',
				'thumb_150x100'  => '150x100',
				'thumb_180x180'  => '180x180',
				'thumb_180x235'  => '180x235',
				'thumb_212x375'  => '212x375',
				'thumb_220x220'  => '220x220',
				'thumb_290x186'  => '290x186',
				'thumb_290x220'  => '290x220',
				'thumb_293x185'  => '293x185',
				'thumb_300x220'  => '300x220',
				'thumb_330x268'  => '330x268',
				'thumb_345x218'  => '345x218',
				'thumb_346x222'  => '345x218',
				'thumb_400x260'  => '400x260',
				'thumb_400x400'  => '400x400',
				'thumb_460x200'  => '460x200',
				'thumb_617x220'  => '617x220',
				'thumb_617x460'  => '617x460',
				'thumb_720x300'  => '720x300',
				'thumb_720x350'  => '720x350',
				'thumb_720x400'  => '720x400',
				'thumb_740x475'  => '740x475',
				'thumb_940x415'  => '940x415',
				'thumb_1920x600' => '1920x600',
				'thumb_730xAUTO' => '730xAUTO'

			);
	}

	static function CheckThumbs() {
		$date = new DateTime();
		$last_date = get_option('placeholder_thumb_generation_date');

		if($date->format('YmdHis') > $last_date) {
			$date->add(new DateInterval('P1D'));
			update_option('last_thumb_generation_date', $date->format('YmdHis'));
			self::Generate();
		}
	}

	static function Generate() {
		$filename = self::ImagePath();

		$attach_id = get_option('placeholder_thumb_id');
		if(!$attach_id) {
			$wp_filetype = wp_check_filetype(basename($filename), null );
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
				'post_content' => '',
				'post_status' => 'inherit'
			);
			$attach_id = wp_insert_attachment( $attachment, $filename );
		}
		$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
		wp_update_attachment_metadata( $attach_id, $attach_data );
		update_option('placeholder_thumb_id', $attach_id);
	}

	static function ImagePath($size = '') {
		return PAPU_STTC . self::ImageForSize($size);
	}
	static function Image($size = '') {
		return PAPURL_STTC . self::ImageForSize($size);
	}

	static function FixSize($size) {
		$sizeList = self::ImageSizes();

		if(is_array($size))
			$size = implode('x', $size);

		if(isset($sizeList[$size]))
			$size = $sizeList[$size];

		return $size;
	}

	static function ImageForSize($displayedSize) {
		$sizeList = self::ImageSizes();
		$displayedSize = self::FixSize($displayedSize);

		if(!$displayedSize)
			$displayedSize = '400x400';

		if(!in_array($displayedSize, $sizeList)) {
			error_log('Tamanho de thumbnail fora do padrão, impossível fornecer uma imagem de "Imagem não disponível". Tamanho solicitado: '.$displayedSize);
			return false;
		}

		if(apply_filters('no_default_image', false)) return false;

		return '/img/default/no_image'.( ($displayedSize) ? '_'.$displayedSize : '' ).'.png';
	}

	static function HtmlFilter($html, $post_id, $post_thumbnail_id, $size, $attr ) {
		if($html) {
			$html = str_replace('width=', 'data-width=', $html);
			$html = str_replace('height=', 'data-height=', $html);
			return $html;
		}

		if(is_string($size)) {
			global $_wp_additional_image_sizes;
			if(isset($_wp_additional_image_sizes[$size])) {
				$size = $_wp_additional_image_sizes[$size];
				$size = array($size['width'], $size['height']);
			} else if(is_string($size) && strpos($size, 'x') > 1) {
				$size = explode('x', $size);
			} else if(!is_array($size)) {
				$size = false;
			}
		}

		$hwstring = '';
		if($size) {
			list($width, $height) = $size;
			$hwstring = image_hwstring($width, $height);
			if ( is_array($size) )
				$size = join('x', $size);
		}

		$default_attr = array(
			'src'	=> self::Image($size),
			'class'	=> 'no-image attachment-'.$size,
			'alt'	=> __('Imagem não disponível', 'iasd'),
		);

		$attr = wp_parse_args($attr, $default_attr);
		$attr = array_map( 'esc_attr', $attr );
		$html = rtrim("<img $hwstring");
		foreach ( $attr as $name => $value ) {
			$html .= " $name=" . '"' . $value . '"';
		}
		$html .= ' />';

		return $html;
	}

	static function PostThumbnailUrl($size = null) {
		global $needed_thumbs, $post, $as3cf;
		$thumbnail_id = get_post_thumbnail_id();

		if(property_exists($post, 'thumbs')) {
			if(isset($post->thumbs[$size])){
				$image = array($post->thumbs[$size]);
			}	
		} else {
			$image = wp_get_attachment_image_src($thumbnail_id, $size);
		}
		
		if ( !$image ){
			if(property_exists($post->meta, '_video_thumbnail')) {
				$video_thumb = $post->meta->_video_thumbnail;
			}
			if ( !empty( $video_thumb ) ){
				$image = $video_thumb;


			} else {
				$image = array(self::Image($size));
				
			}

		}

		return $image[0];
	}

	static function PostThumbnailName() {
		global $post;
		if($thumbnailID = get_post_thumbnail_id() && !isset($post->is_remote))
			return get_the_title($thumbnailID);
		return __('Imagem não disponível', 'iasd');
	}


	static function UrlFilter($url, $post_id) {
		if($url)
			return $url;

		return self::Image();
	}

	static function Downsize($false, $id, $size){

		global $post;
		$meta = wp_get_attachment_metadata($id);
		if(!$meta)
			$meta = array();
		if(isset($post->thumbs))
			$meta['sizes'] = $post->thumbs;
		if(!count($meta))
			return false;
		$availableSizes = self::ImageSizes();
		$availableSizesR = array_flip($availableSizes);
		$size = self::FixSize($size);
		$size_name = (isset($availableSizesR[$size])) ? $availableSizesR[$size] : false;

		if(!$size_name)
			return false;

		list($width, $height) = explode('x', $size);

		if (empty($meta['sizes'][$size_name])){
			if ($img_url = self::Image($size)){
				return array( $img_url, $width, $height, false );
			}
		} else {
			if ( isset($data['file']) ) {
				$data = $meta['sizes'][$size_name];
				$file = $data['file'];

				$img_url = wp_get_attachment_url($id);
				$img_url_basename = wp_basename($img_url);
				$img_url = str_replace($img_url_basename, $data['file'], $img_url);

				return array( $img_url, $width, $height, true );
			} else {
				return false;
			}
			
		}

		return false;
	}
}

class DefaultImageController extends IASD_DefaultImage {

}


IASD_DefaultImage::Init();


