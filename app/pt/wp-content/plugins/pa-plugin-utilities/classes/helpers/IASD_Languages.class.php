<?php


if (is_admin()){
	add_action('admin_menu', array('IASD_Languages', 'CreateAdminMenu'), 100);
	add_action('admin_init', array('IASD_Languages', 'registerSettings'));
}

class IASD_Languages {
	public static $languages = array('pt_BR', 'es_ES' );

	public static function CreateAdminMenu(){
		add_submenu_page( 'pa-adventistas', __('Outras Línguas', 'iasd'), __('Outras Línguas', 'iasd'), 'edit_pages', 'pa-adv-i18n', array(__CLASS__, 'renderLanguageAdminPage'));
	}
	
	public static function registerSettings(){
		register_setting('pa-adv-i18n', 'pa_i18n_urls', array(__CLASS__, 'validateInputs'));

		add_settings_section( 'default', __('URLS', 'iasd'), array(__CLASS__, 'renderSection'), 'pa-adv-i18n' );
		
		foreach (self::$languages as $i => $lang){
			add_settings_field( $lang, strtoupper(substr($lang, 0, 2)), array(__CLASS__, 'renderURLField'), 'pa-adv-i18n', 'default', array('lang'=>$lang) );
		}	
	}

	public static function renderSection(){
		_e('Para cada língua informe a URL da instalação correlata a esta instalação', 'iasd');
	}

	public static function renderURLField($args){
		$current = get_option('pa_i18n_urls');
		$value = "";
		$title = self::getDefaultLinkTitle($args['lang']);

		if ($args['lang'] == WPLANG){
			echo '<label>URL:</label><br>' . home_url( );
			echo '</td><td>';
			echo '<label>Título do link no menu:</label><br>';
			echo $title;

		} else {
			if (!empty($current[$args['lang']])){
				$value = $current[$args['lang']]['url'];
				$title = $current[$args['lang']]['title'];
			}
			echo '<label>URL:</label><br><input type="text" name="pa_i18n_urls['.$args['lang'].'][url]" value="'.$value.'" />';
			echo '</td><td>';
			echo '<label>Título do link no menu:</label><br><input type="text" name="pa_i18n_urls['.$args['lang'].'][title]" value="'.$title.'" />';
		}
	}

	public static function validateInputs($input){
		foreach ($input as $lang => $value) {
			if (filter_var($value['url'], FILTER_VALIDATE_URL) === false){
				add_settings_error( 'invalid-url-'.$lang, 'invalid-url', __('Você precisa inserir uma URL válida para a lingua '.strtoupper(substr($lang, 0, 2)) . ' (não se esqueça de colocar http:// no início)', 'iasd') );
				return '';
			}

			if (empty( $value['title'] ) ){
				add_settings_error( 'invalid-title-'.$lang, 'invalid-title', __('Você precisa definir um título para o link da lingua '.strtoupper(substr($lang, 0, 2)), 'iasd') );
				return '';
			}
		}
		return $input;
	}

	public static function renderLanguageAdminPage(){
?>
<div class="wrap">
    <?php screen_icon(); ?>
    <h2><?php _e('Instalações em outras línguas', 'iasd'); ?></h2>			
    <form method="post" action="options.php">
     <?php settings_fields('pa-adv-i18n'); ?>
     <?php do_settings_sections( 'pa-adv-i18n' ); ?>
     <?php submit_button(); ?>
    </form>
</div>
<?php
	}

	private static function getDefaultLinkTitle($lang){
		switch ($lang){
			case 'pt_BR': 
				return 'Versão em Português';
			case 'es_ES':
				return 'Versión en español';
		}
	}
}

class LanguageController extends IASD_Languages {
	
}


//
/**
 * Controls menu renderization
 */

add_filter( 'walker_nav_menu_start_el', array('MainNavMenuWalker', 'AddDropdownToggle'), 10, 4);

class MainNavMenuWalker extends Walker_Nav_Menu {
	public static function AddDropdownToggle($item_output, $item, $depth, $args) {
		if(is_array($item->classes)) {
			if(in_array('dropdown', $item->classes)) {
				$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
				$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
				$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
				$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

				$item_output = $args->before;
				$item_output .= '<a'. $attributes .' class="dropdown-toggle" data-toggle="dropdown">';
				$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
				$item_output .= '</a>';
				$item_output .= $args->after;
			}
		}

		return $item_output;
	}

	private $addI18N = FALSE;
	public function __construct($addI18N = FALSE){
		$this->addI18N = (bool) $addI18N;
	}

	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class=\"sub-menu dropdown-menu\">\n";
	}
	
	public function display_element ($element, &$children_elements, $max_depth, $depth = 0, $args, &$output){
		// check, whether there are children for the given ID and append it to the element with a (new) ID
		$element->hasChildren = isset($children_elements[$element->ID]) && !empty($children_elements[$element->ID]);

		return parent::display_element($element, $children_elements, $max_depth, $depth, $args, $output);
	}


	public function start_el ( &$output, $item, $depth = 0, $args = array(), $id = 0){
		if ($item->hasChildren){
			$item->classes[] = 'has_children';
			$item->classes[] = 'dropdown';
		}

		parent::start_el($output, $item, $depth, $args, $id);
	}
}
