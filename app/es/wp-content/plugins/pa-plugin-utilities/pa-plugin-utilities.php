<?php

/**
Plugin Name: Utilitários - Portal Adventista
Description: Série de widgets e ferramentas usadas pelo Portal Adventista.
Author: Divisão Sul Americana da IASD
Version: 2.0.0-webservice
 */

define('PAPU', dirname(__FILE__));
define('PAPURL', plugins_url('pa-plugin-utilities'));

define('PAPU_CLSS', PAPU . DIRECTORY_SEPARATOR . 'classes');
define('PAPU_CONT', PAPU_CLSS . DIRECTORY_SEPARATOR . 'controllers');
define('PAPU_HELP', PAPU_CLSS . DIRECTORY_SEPARATOR . 'helpers');
define('PAPU_WDGT', PAPU_CLSS . DIRECTORY_SEPARATOR . 'widgets');
define('PAPU_PEAR', PAPU_CLSS . DIRECTORY_SEPARATOR . 'pear');

define('PAPU_VIEW', PAPU . DIRECTORY_SEPARATOR . 'views');
define('PAPU_LANG', 'pa-plugin-utilities' . DIRECTORY_SEPARATOR . 'languages');
define('PAPURL_LIBS', PAPURL . '/lib');

define('PAPU_STTC', PAPU . DIRECTORY_SEPARATOR . 'static');
define('PAPURL_STTC', PAPURL . '/static');

class IASD_Utilities
{
	static function AfterSetupTheme()
	{
		//		require_once PAPU_CONT.DIRECTORY_SEPARATOR.'IASD_ImageGallery.class.php';
		//		require_once PAPU_CONT.DIRECTORY_SEPARATOR.'IASD_VideoGallery.class.php';
		//		require_once PAPU_CONT.DIRECTORY_SEPARATOR.'IASD_RevistaAdventistaController.class.php';
	}

	/**
Controllers: Ferramentas de apoio que possuem output muito especifico
	 */
	static function Controllers()
	{
		//		require_once PAPU_CONT.DIRECTORY_SEPARATOR.'IasdNavEntreCampos.class.php';
		//		require_once PAPU_CONT.DIRECTORY_SEPARATOR.'PAGoogleAnalytics.class.php';

		if (strstr(get_site_url(), "adventistas.org") !== false) {
			//			require_once PAPU_CONT.DIRECTORY_SEPARATOR.'PAGoSquared.class.php';
			//			require_once PAPU_CONT.DIRECTORY_SEPARATOR.'PAHotjar.class.php';
			//			require_once PAPU_CONT.DIRECTORY_SEPARATOR.'PAFacebookPixel.class.php';
		}

		if (isset($_GET['widget_test']) && $_GET['widget_test'] == 'active') {
			//			require_once PAPU_CONT.DIRECTORY_SEPARATOR.'AllWidgetsTest.class.php';
		}

		require_once PAPU_CONT . DIRECTORY_SEPARATOR . 'IASD_Query.class.php';
		//		require_once PAPU_CONT.DIRECTORY_SEPARATOR.'IASD_Sidebar.class.php';
		//		require_once PAPU_CONT.DIRECTORY_SEPARATOR.'IASD_Footer.class.php';
		//		require_once PAPU_CONT.DIRECTORY_SEPARATOR.'IASD_Menu.class.php';
		//		require_once PAPU_CONT.DIRECTORY_SEPARATOR.'IASD_Header.class.php';
		//		require_once PAPU_CONT.DIRECTORY_SEPARATOR.'IASD_GlobalNav.class.php';
		//		require_once PAPU_CONT.DIRECTORY_SEPARATOR.'IASD_Disqus.class.php';
		//		require_once PAPU_CONT.DIRECTORY_SEPARATOR.'IASD_Referer.class.php';
		//		require_once PAPU_CONT.DIRECTORY_SEPARATOR.'IASD_AdminUser.class.php';
		//		require_once PAPU_CONT.DIRECTORY_SEPARATOR.'IASD_BannerHeder.php';
		//		require_once PAPU_CONT.DIRECTORY_SEPARATOR.'IASD_lgpd.php';
		//		require_once PAPU_CONT.DIRECTORY_SEPARATOR.'IASD_BannerF7.php';
	}

	/**
Helpers: Itens que não possui um output próprio, mas servem de base para outras ferramentas
	 */
	static function Helpers()
	{
		require_once PAPU_HELP . DIRECTORY_SEPARATOR . 'IASD_Taxonomias.class.php';
		//		require_once PAPU_HELP.DIRECTORY_SEPARATOR.'IASD_Languages.class.php';
		//		require_once PAPU_HELP.DIRECTORY_SEPARATOR.'IASD_TextManipulation.class.php';
		//		require_once PAPU_HELP.DIRECTORY_SEPARATOR.'IASD_Shortcodes.class.php';
		//		require_once PAPU_HELP.DIRECTORY_SEPARATOR.'IASD_SEO.class.php';
		//		require_once PAPU_HELP.DIRECTORY_SEPARATOR.'IASD_ViewFragments.class.php';
		//		require_once PAPU_HELP.DIRECTORY_SEPARATOR.'IASD_Checklist_Walker.class.php';
		//		require_once PAPU_HELP.DIRECTORY_SEPARATOR.'IASD_ListaDePosts_Views.class.php';
		//		require_once PAPU_HELP.DIRECTORY_SEPARATOR.'IASD_PostTypeControl.class.php';
		//		require_once PAPU_HELP.DIRECTORY_SEPARATOR.'IASD_DefaultMidia.class.php';
		//		require_once PAPU_HELP.DIRECTORY_SEPARATOR.'IASD_Users.class.php';


		if (is_admin()) {
			//			require_once PAPU_HELP.DIRECTORY_SEPARATOR.'RemoveColumns.class.php';
			//			require_once PAPU_HELP.DIRECTORY_SEPARATOR.'RemoveCategoriesAndTags.class.php';
		}
		require_once PAPU_HELP . DIRECTORY_SEPARATOR . 'TaxonomyImageController.class.php'; //Revisado em 08/10
		require_once PAPU_HELP . DIRECTORY_SEPARATOR . 'IASD_DefaultImage.class.php'; //Revisado em 22/10
		//		require_once PAPU_HELP.DIRECTORY_SEPARATOR.'ImgCaptionShortcodeFix.class.php';
		//		require_once PAPU_HELP.DIRECTORY_SEPARATOR.'IASD_SearchPage.class.php';
	}
}

IASD_Utilities::Controllers();

IASD_Utilities::Helpers();

if (!function_exists('iasdDecodeToArray')) {
	function iasdDecodeToArray($items)
	{
		if (is_string($items)) {
			$decoded_items = json_decode($items);
			if ($decoded_items)
				$items = $decoded_items;
		}
		if (is_object($items))
			$items = (array) $items;

		if (is_array($items)) {
			$fixed_items = array();
			foreach ($items as $k => $v) {
				$fixed_items[$k] = iasdDecodeToArray($v);
			}
			$items = $fixed_items;
		}
		return $items;
	}
}


/**
 * Registers the `xtt_pa_format` taxonomy,
 * for use with 'post'.
 */
function xtt_pa_format_init()
{
	register_taxonomy('xtt-pa-format', ['post'], [
		'hierarchical'          => true,
		'public'                => true,
		'show_in_nav_menus'     => true,
		'show_ui'               => true,
		'show_admin_column'     => false,
		'query_var'             => true,
		'rewrite'               => true,
		'capabilities'          => [
			'manage_terms' => 'edit_posts',
			'edit_terms'   => 'edit_posts',
			'delete_terms' => 'edit_posts',
			'assign_terms' => 'edit_posts',
		],
		'labels'                => [
			'name'                       => __('Post Format', 'YOUR-TEXTDOMAIN'),
			'singular_name'              => _x('Post Format', 'taxonomy general name', 'YOUR-TEXTDOMAIN'),
			'search_items'               => __('Search Xtt pa formats', 'YOUR-TEXTDOMAIN'),
			'popular_items'              => __('Popular Xtt pa formats', 'YOUR-TEXTDOMAIN'),
			'all_items'                  => __('All Xtt pa formats', 'YOUR-TEXTDOMAIN'),
			'parent_item'                => __('Parent Xtt pa format', 'YOUR-TEXTDOMAIN'),
			'parent_item_colon'          => __('Parent Xtt pa format:', 'YOUR-TEXTDOMAIN'),
			'edit_item'                  => __('Edit Xtt pa format', 'YOUR-TEXTDOMAIN'),
			'update_item'                => __('Update Xtt pa format', 'YOUR-TEXTDOMAIN'),
			'view_item'                  => __('View Xtt pa format', 'YOUR-TEXTDOMAIN'),
			'add_new_item'               => __('Add New Xtt pa format', 'YOUR-TEXTDOMAIN'),
			'new_item_name'              => __('New Xtt pa format', 'YOUR-TEXTDOMAIN'),
			'separate_items_with_commas' => __('Separate xtt pa formats with commas', 'YOUR-TEXTDOMAIN'),
			'add_or_remove_items'        => __('Add or remove xtt pa formats', 'YOUR-TEXTDOMAIN'),
			'choose_from_most_used'      => __('Choose from the most used xtt pa formats', 'YOUR-TEXTDOMAIN'),
			'not_found'                  => __('No xtt pa formats found.', 'YOUR-TEXTDOMAIN'),
			'no_terms'                   => __('No xtt pa formats', 'YOUR-TEXTDOMAIN'),
			'menu_name'                  => __('Post Format', 'YOUR-TEXTDOMAIN'),
			'items_list_navigation'      => __('Xtt pa formats list navigation', 'YOUR-TEXTDOMAIN'),
			'items_list'                 => __('Xtt pa formats list', 'YOUR-TEXTDOMAIN'),
			'most_used'                  => _x('Most Used', 'xtt-pa-format', 'YOUR-TEXTDOMAIN'),
			'back_to_items'              => __('&larr; Back to Xtt pa formats', 'YOUR-TEXTDOMAIN'),
		],
		'show_in_rest'          => true,
		'rest_base'             => 'xtt-pa-format',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
	]);
}

//add_action( 'init', 'xtt_pa_format_init' );

/**
 * Sets the post updated messages for the `xtt_pa_format` taxonomy.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `xtt_pa_format` taxonomy.
 */
function xtt_pa_format_updated_messages($messages)
{

	$messages['xtt-pa-format'] = [
		0 => '', // Unused. Messages start at index 1.
		1 => __('Xtt pa format added.', 'YOUR-TEXTDOMAIN'),
		2 => __('Xtt pa format deleted.', 'YOUR-TEXTDOMAIN'),
		3 => __('Xtt pa format updated.', 'YOUR-TEXTDOMAIN'),
		4 => __('Xtt pa format not added.', 'YOUR-TEXTDOMAIN'),
		5 => __('Xtt pa format not updated.', 'YOUR-TEXTDOMAIN'),
		6 => __('Xtt pa formats deleted.', 'YOUR-TEXTDOMAIN'),
	];

	return $messages;
}

add_filter('term_updated_messages', 'xtt_pa_format_updated_messages');
