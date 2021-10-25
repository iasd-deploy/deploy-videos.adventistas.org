<?php

class IASD_Menu {
	public static function Show($menu_name, $site_identifier = '') {
		echo self::Get($menu_name, $site_identifier);
	}
	public static function Get($menu_name, $site_identifier = '') {
		return str_replace('SITEIDENTIFIER', $site_identifier, get_option( $menu_name ));
	}

	public static function GetGlobalMenu() {
		$menu_object = get_option('multisite_1');
        if(!is_object($menu_object))
            return;
		$name = $menu_object->name;
		$json_decode = json_decode($menu_object->structure);
		$k = count($json_decode);
		$json_decode[$k] = new stdClass();
		$json_decode[$k]->info = new stdClass();
		$json_decode[$k]->info->title = __('Mais...', 'iasd');
		$json_decode[$k]->info->href = 'javascript.void(0);';
		$json_decode[$k]->info->classes = array('more', 'hidden-xs');
		$json_decode[$k]->info->name = __('Clique para ver mais itens', 'iasd');

		$structure = self::Render($json_decode);
		return $structure;
	}

	public static function GetGlobalNavSubMenuName($menu_slug) {
		$menu_object = get_option($menu_slug);
		return $menu_object->name;
	}

	public static function GetGlobalNavSubMenu($menu_slug) {
		$menu_object = get_option($menu_slug);
		$name = $menu_object->name;
		$menu_structure = json_decode($menu_object->structure);
		$structure = false;
		if($menu_slug == 'multisite_aba2') {
			foreach($menu_structure as $k => $menu) {
				if(!isset($menu->items))
					$menu->items = array();
				$second = new stdClass();
				$second->info = $menu->info;
				array_unshift($menu->items, $second);
				$first = new stdClass();
				$first->info = new stdClass();
				$first->info->title = __('Clique para ver os sites:', 'iasd');
				$first->info->href = null;
				$first->info->classes = array('instruction', 'hidden-xs');
				$first->info->name = __('Clique para ver mais itens', 'iasd');
				array_unshift($menu->items, $first);
			}
		}
		$structure = self::Render($menu_structure);
		return $structure;
	}

	public static function GetGlobalNavSubMenus($default) {
		$menu_object = get_option('multisite_1');
		if(isset($menu_object->submenus)){
			$default = (array) $menu_object->submenus;
		}

		return $default;
	}

	public static function Init() {
		register_nav_menu('footer_4', __('Quarto rodapé, da esquerda para a direita, nas instalações da IASD)', 'iasd'));
	}

	public static function Render($items, $depth = 0, $goDown = true) {
		$output = '';

		foreach($items as $item) {
			$info = $item->info;
			$classes = ' class="'.implode(' ', $info->classes).'"';
			$output_items = '';
			if(isset($item->items) && $goDown) {
				$output_items .= '<ul>' . self::Render($item->items, $depth + 1) . '</ul>';
			}

			$extra = (isset($info->xfn) && !empty($info->xfn)) ? '" data-region="'.$info->xfn : '" target="' . ((isset($info->target)) ? $info->target : '' );

			if(!isset($info->name) || empty($info->name))
				$info->name = $info->title;

			$output .= "\n\t" . '<li '.$classes.'>';

			if($info->href !== null)
				$output .= '<a href="'.$info->href.'" title="' .$info->name. $extra .'">';

			$output .= $info->title;
			if($info->href !== null)
				$output .= '</a>';
			$output .= $output_items;
			$output .='</li>';
		}

		return $output;
	}

	public static function RenderUnions($items, $depth = 0, $goDown = true) {
		$output = '';

		foreach($items as $item) {
			$info = $item->info;
			$classes = ' class="'.implode(' ', $info->classes).'"';
			$output_items = '';
			if(isset($item->items) && $goDown) {
				$output_items .= '<ul>' . self::Render($item->items, $depth + 1) . '</ul>';
			}
			$output .= "\n\t" . '<li '.$classes.'><a href="'.$info->href.'" title="' .$info->title. '" data-region="'.$info->xfn.'">' . $info->title.'</a>' . $output_items . '</li>' ;
		}

		return $output;
	}

	public static function GetFooter($default = '', $id = 'footer_1') {
		$menu_object = get_option($id);
		if($menu_object) {
			$structure = self::Render(json_decode($menu_object->structure), 0, false);
			$default = '<h1>' . $menu_object->name . '</h1><ul>' . $structure . '</ul>';

		}
		return $default;
	}
}

class PAMenu extends IASD_Menu {

}

add_action('init', array('IASD_Menu', 'Init'), 100);

add_action('menu_content', array('IASD_Menu', 'Show'), 10, 2);

add_filter('IasdGlobalNav::GlobalNav', array('IASD_Menu', 'GetGlobalMenu'));
add_filter('IasdGlobalNav::GlobalNavSubMenu', array('IASD_Menu', 'GetGlobalNavSubMenu'));
add_filter('IasdGlobalNav::GlobalNavSubMenuName', array('IASD_Menu', 'GetGlobalNavSubMenuName'));
add_filter('IasdGlobalNav::GlobalNavSubMenus', array('IASD_Menu', 'GetGlobalNavSubMenus'));

add_filter('IASD_Footer::LeftMenu', array('IASD_Menu', 'GetFooter'), 10, 2);

