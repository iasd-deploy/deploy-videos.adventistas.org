<?php

add_action('wp_head', array('IASD_Header', 'WPHead'));

class IASD_Header {
	public static function WPHead() {

		if (is_user_logged_in() && ( current_user_can('editor') || current_user_can('administrator')) ) {
			
			echo "<script type='text/javascript'>var iasd_rules_action = '".IASD_ListaDePosts_Ajax::RULES."';</script>\n";
			echo "<script href='http://iasd.dev/pt/wp-admin/load-scripts.php?c=0&load%5B%5D=hoverIntent,common,thickbox,underscore,shortcode,media-upload,admin-bar,jquery-ui-core,jquery-ui-widget,jquery-ui-mouse,jquery-u&load%5B%5D=i-sortable,jquery-ui-draggable,jquery-ui-droppable,admin-widgets,svg-painter,heartbeat,wp-auth-check,backbone,wp-util,wp-backbon&load%5B%5D=e,media-models,plupload,json2,wp-plupload,mediaelement,wp-mediaelement,media-views,media-editor,media-audiovideo,wp-playlist,mce&load%5B%5D=-view,imgareaselect,image-edit&ver=4.1'></script>";
			
			wp_enqueue_style('iasd-ldp-admin', PAPURL_STTC . '/css/iasdlistadeposts_admin.css');

			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('jquery-ui-draggable');
			wp_enqueue_script('jquery-ui-droppable');
			wp_enqueue_script('jquery-migrate');
			wp_enqueue_script('utils');
			wp_enqueue_script('i-sortable');
			wp_enqueue_script('thickbox');


			wp_enqueue_script('iasd-ldp-admin', PAPURL_STTC . '/js/iasdlistadeposts_admin.js', array('jquery'));
			wp_enqueue_script('jquery-validate', PAPURL_STTC . '/js/jquery.validate/jquery.validate.js', array('jquery'));
			// wp_enqueue_script('jquery-validate-additional-methods', PAPURL_STTC . '/js/jquery.validate/additional-methods.js', array('jquery', 'jquery-validate'));
			// wp_enqueue_script('jquery-validate-l10n', PAPURL_STTC . '/js/jquery.validate/localization/messages_'.WPLANG.'.js', array('jquery', 'jquery-validate'));

			add_thickbox();
		}

		if (WPLANG == "pt_BR"){
			echo '<link rel="alternate" href="'. get_home_url() .'" hreflang="pt-br" />';
		} else {
			echo '<link rel="alternate" href="'. get_home_url() .'" hreflang="es" />';
		}
		
?>

<script type="text/javascript">var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';</script>
<?php
	}

	public static function get_headquarter_title(){
		$sede = get_option('paheader_sede');
		if($sede > 0) {
			$sede = get_term($sede, IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS);
			if($sede)
				$sede = $sede->name;

		} else {
			$sede = '';
		}

		echo $sede;
	}

	public static function Show($subtitle = false, $header = false, $hide_menu = false) {
		$classes = apply_filters('iasd-header', array());		
		?>
		<div class="container">
			<div class="row">
			<?php 
				$sede = get_option('paheader_sede');

			if($sede > 0 && get_option( 'stylesheet' ) == 'pa-thema-sedes') {
			?>

				<div class="col-md-7 identifier <?php if (!is_front_page()) { echo "institutional headquarters"; } ?> <?php if (is_front_page()) { echo "iasd-institucional"; } ?>" >

			<?php } else { ?>
				<div class="col-md-7 identifier <?php echo implode(' ', $classes); ?>">

			<?php } ?>
					<!-- Institutional Headquarters Pattern -->
					<div class="brand">
						<a href="<?php echo get_home_url(); ?>" title="<?php _e('Clique aqui para retornar à página inicial', 'iasd'); ?>"><?php echo get_option('paheader_titulo'); ?></a>
					</div>
					<div class="title">
						<hgroup>
							<h1><?php bloginfo('name'); ?></h1>
							<h2><?php do_action('iasd-headquarter-title'); ?></h2>
						</hgroup>
					</div>
				</div>
					<!-- End Headquarters Pattern -->
				<div class="col-md-5 visible-md visible-lg">
					<?php echo apply_filters('iasd-header-right-container', '');// include '../_common/dropdown_nav.php'; ?><!--  Begin Dropdown Navigation  -->
				</div>

			</div>
		</div>

		<?php
	}

/**			Informações da Sede
*/

	public static function AdminMenu() {
		add_submenu_page( 'pa-adventistas', __('Cabeçalho', 'iasd'), __('Cabeçalho', 'iasd'), 'edit_pages', 'pa-adv-header', array('IASD_Taxonomias', 'SettingsRender'));

		register_setting('pa-adv-header', 'paheader_titulo');
		$paheader_titulo = get_option('paheader_titulo');
		update_option('paheader_titulo', get_bloginfo('name'));

		register_setting('pa-adv-header', 'paheader_descricao');
		$paheader_descricao = get_option('paheader_descricao');

		register_setting('pa-adv-header', 'paheader_sede');

		add_settings_section('pa-adv-header-default', __('Configurações do Cabeçalho', 'iasd'), array(__CLASS__, 'AdminMenuInfoSection'), 'pa-adv-header');
		add_settings_field('paheader_titulo',   __('Titulo', 'iasd'),   array(__CLASS__, 'AdminMenuFieldSetting'), 'pa-adv-header', 'pa-adv-header-default', 'paheader_titulo');
		add_settings_field('paheader_descricao',   __('Descrição', 'iasd'),   array(__CLASS__, 'AdminMenuFieldSetting'), 'pa-adv-header', 'pa-adv-header-default', 'paheader_descricao');
		add_settings_field('paheader_sede', __('Sede', 'iasd'), array(__CLASS__, 'AdminMenuFieldSetting'), 'pa-adv-header', 'pa-adv-header-default', 'paheader_sede');
		// add_settings_field('paheader_show', __('Mostrar nome da sede', 'iasd'),'paheader_show_callback', 'pa-adv-header', 'pa-adv-header-default', 'paheader_show');

	}
	public static function AdminMenuInfoSection() {
		echo '<p>' . __('Use os campos abaixo para configurar o cabeçalho global', 'iasd') . '</p>';
	}
	public static function AdminMenuFieldSetting($setting_name) {
		if($setting_name == 'paheader_titulo') {
			update_option('paheader_titulo', get_bloginfo('name'));
		} else if($setting_name == 'paheader_descricao') {
			update_option('paheader_descricao', get_bloginfo('description'));
		}
		$value = get_option($setting_name);

		switch ($setting_name) {
			case 'paheader_sede':
					wp_dropdown_categories(array('hide_empty' => 0, 'name' => 'paheader_sede', 'id' => 'paheader_sede',
													'selected' => $value, 'show_option_all' => false,
													'show_option_none' => __('Não é sede regional', 'iasd'),
													'taxonomy' => IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS, 'hierarchical' => true));
				break;
			default:

				echo '<input  name="'.$setting_name.'" id="'.$setting_name.'" type="input" value="'. $value .'" class="widefat" />';
			break;
		}
	}
}

function paheader_show_callback() {
 	echo '<input name="paheader_show" id="paheader_show" type="checkbox" value="1" class="code" ' . checked( 1, get_option( 'paheader_show' ), false ) . ' /> ';
 }


class IasdHeader extends IASD_Header {

}

add_action( 'admin_menu', array('IASD_Header', 'AdminMenu'), 100);
add_action( 'network_admin_menu', array('IASD_Header', 'AdminMenu'), 100);
add_action( 'header_content', array('IASD_Header', 'Show'), 10, 2);
add_action( 'iasd-headquarter-title', array( 'IASD_Header', 'get_headquarter_title' ), 10 );

/**
Support classes
*/

class IASD_MainMenuWalker extends Walker_Nav_Menu {
	const MENU_I18N_ID = 987654321;
	function walk($elements, $max_depth, ...$args){
		$languagesURLs = get_option('pa_i18n_urls');
		$hasMoreLanguages = (is_array($languagesURLs) && (count($languagesURLs) > 0));

		if ($hasMoreLanguages) {

			$id_field = $this->db_fields['id'];
			$parent_field = $this->db_fields['parent'];

			$menuI18n = new stdClass();
			$menuI18n->title = strtoupper(substr( WPLANG , 0, 2));
			$menuI18n->db_id = self::MENU_I18N_ID;
			$menuI18n->classes = array('language');
			$menuI18n->$parent_field = 0;
			$menuI18n->$id_field = self::MENU_I18N_ID;
			$menuI18n->ID = self::MENU_I18N_ID;
			$menuI18n->url = '#';

			$elements[] = $menuI18n;

			$currentLang = ( !defined('WPLANG') ) ? 'pt_BR' : WPLANG;
			foreach (IASD_Languages::$languages as $key => $lang) {
				if ( $lang != $currentLang && !empty($languagesURLs[$lang])  && is_array($languagesURLs[$lang]) ){
					$menuI18nChild = new stdClass();
					$menuI18nChild->title = $languagesURLs[$lang]['title'];
					$menuI18nChild->url = $languagesURLs[$lang]['url'];
					$menuI18nChild->menu_item_parent = $menuI18n->db_id;
					$menuI18nChild->$parent_field = $menuI18n->db_id;
					$menuI18nChild->$id_field = 0;
					$menuI18nChild->ID = 0;
					$elements[] = $menuI18nChild;
				}
			}
 		};

		return parent::walk($elements, $max_depth, array(array()));
	}

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ){
		$item->classes = empty( $item->classes ) ? array() : (array) $item->classes;
		if($args['has_children'])
			$item->classes[] = 'has-children';

		if(!isset($item->attr_title) || !$item->attr_title)
			$item->attr_title = $item->title;
		if(!isset($args['before']))
			$args['before'] = '';
		if(!isset($args['link_before']))
			$args['link_before'] = '';
		if(!isset($args['link_after']))
			$args['link_after'] = '';
		if(!isset($args['after']))
			$args['after'] = '';

		parent::start_el($output, $item, $depth, (object) $args, $id);
	}
}
