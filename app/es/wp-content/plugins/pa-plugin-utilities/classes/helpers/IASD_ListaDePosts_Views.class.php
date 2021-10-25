<?php

if(!class_exists('IASD_ListaDePosts_Views')) {
	define('PAPU_LDPV', PAPU_VIEW . '/iasd_listadeposts_views');

	global $IASD_ListaDePosts_Widgets_Array, $IASD_ListaDePosts_Widgets_Groups;
	$IASD_ListaDePosts_Widgets_Groups = $IASD_ListaDePosts_Widgets_Array = array();

	class IASD_ListaDePosts_Views {

		/**
			=== DECISÃO ESTRATÉGICA ===
			Embora usa-se OO na maioria do projeto, a escolha da 
			estrutura de array para a criação das Views se baseia em:
			- Expansivel (o uso de objetos iria 
				aumentar o numero de verificações, validações e arquivos)
			- Serializavel (em alguns casos a criação de objetos
				requer a criação de metodos de serialização)
			- Reutilização (usar uma classe/objeto para cada View
				poderia aumentar a complexidade, e na estrutura
				atual usa-se um arquivo para várias Views)
		*/
		static function BaseConfig($name, $path) {
			$base_config = array();
			$base_config['group'] = 'all';
			$base_config['name'] = $name;
			$base_config['path'] = $path;
			$base_config['description'] = $name;
			$base_config['thumbnail'] = true;
			$base_config['allow_grouping'] = true;
			$base_config['allow_see_more'] = true;
			$base_config['cols'] = array('col-md-4', 'col-md-8');
			$base_config['post_type'] = array();
			$base_config['posts_per_page'] = 4;
			$base_config['posts_per_page_forced'] = false;
			

			return $base_config;
		}

		static function HasView($name) {
			global $IASD_ListaDePosts_Widgets_Array;
			return isset($IASD_ListaDePosts_Widgets_Array[$name]);
		}

		static function RegisterView($name, $path, $args = array()) {
			if(!$name)
				return false;
			if(!$path)
				return false;
			if(!is_file($path))
				return false;
			if(self::HasView($name))
				return false;

			$args = array_merge(self::BaseConfig($name, $path), $args);
			global $IASD_ListaDePosts_Widgets_Array, $IASD_ListaDePosts_Widgets_Groups;
			$IASD_ListaDePosts_Widgets_Array[$name] = $args;

			$IASD_ListaDePosts_Widgets_Groups[$args['group']][] = $name;

			return true;
		}

		static function UnregisterView($name) {
			global $IASD_ListaDePosts_Widgets_Array;
			if(self::HasView($name))
				unset($IASD_ListaDePosts_Widgets_Array[$name]);
		}

		static function GetViews() {
			global $IASD_ListaDePosts_Widgets_Array;
			return $IASD_ListaDePosts_Widgets_Array;
		}

		static function GetGroups() {
			global $IASD_ListaDePosts_Widgets_Groups;
			return $IASD_ListaDePosts_Widgets_Groups;
		}

		static function GetView($name) {
			global $IASD_ListaDePosts_Widgets_Array;

			if(self::HasView($name))
				return $IASD_ListaDePosts_Widgets_Array[$name];

			return false;
		}

		static function RegisterFakes() {
			$col_md_4 = array();
			$col_md_4['group'] = 'Fake';
			$col_md_4['description'] = __('COL-MD-4', 'iasd');
			$col_md_4['cols'] = array('col-md-4');
			$col_md_4['allow_grouping'] = false;
			$col_md_4['allow_see_more'] = false;
			self::RegisterView('COL-MD-4', PAPU_LDPV . '/fake.php', $col_md_4);
			$col_md_8 = array();
			$col_md_8['group'] = 'Fake';
			$col_md_8['description'] = __('COL-MD-8', 'iasd');
			$col_md_8['cols'] = array('col-md-4', 'col-md-8');
			self::RegisterView('COL-MD-8', PAPU_LDPV . '/fake.php', $col_md_8);
			$col_md_12 = array();
			$col_md_12['group'] = 'Fake';
			$col_md_12['description'] = __('COL-MD-12', 'iasd');
			$col_md_12['cols'] = array('col-md-4', 'col-md-8', 'col-md-12');
			self::RegisterView('COL-MD-12', PAPU_LDPV . '/fake.php', $col_md_12);


			$col_md_4_only = array();
			$col_md_4_only['group'] = 'Fake';
			$col_md_4_only['description'] = __('COL-MD-4-ONLY', 'iasd');
			$col_md_4_only['cols'] = array('col-md-4');
			self::RegisterView('COL-MD-4-ONLY', PAPU_LDPV . '/fake.php', $col_md_4_only);
			$col_md_8_only = array();
			$col_md_8_only['group'] = 'Fake';
			$col_md_8_only['description'] = __('COL-MD-8-ONLY', 'iasd');
			$col_md_8_only['cols'] = array('col-md-8');
			self::RegisterView('COL-MD-8-ONLY', PAPU_LDPV . '/fake.php', $col_md_8_only);
			$col_md_12_only = array();
			$col_md_12_only['group'] = 'Fake';
			$col_md_12_only['description'] = __('COL-MD-12-ONLY', 'iasd');
			$col_md_12_only['cols'] = array('col-md-12');
			self::RegisterView('COL-MD-12-ONLY', PAPU_LDPV . '/fake.php', $col_md_12_only);
		}

		static function RegisterDefaults() {
			/**
			Boxes
			*/

			$box_simple = array('group' => __('Boxes', 'iasd'));
			$box_simple['description'] = __('Box simples', 'iasd');
			$box_simple['ordered'] = false;
			$box_simple['thumbnail'] = false;
			$box_simple['item_max_length'] = 80;
			self::RegisterView('box_simple', PAPU_LDPV . '/box.php', $box_simple);

			$box_simple_thumbs = array('group' => __('Boxes', 'iasd'));
			$box_simple_thumbs['description'] = __('Box simples com miniaturas', 'iasd');
			$box_simple_thumbs['ordered'] = false;
			$box_simple_thumbs['item_max_length'] = 60;
			self::RegisterView('box_simple_thumbs', PAPU_LDPV . '/box.php', $box_simple_thumbs);


			$box_ordered = array('group' => __('Boxes', 'iasd'));
			$box_ordered['description'] = __('Box ordenado', 'iasd');
			$box_ordered['ordered'] = true;
			$box_ordered['thumbnail'] = false;
			$box_ordered['item_max_length'] = 60;
			self::RegisterView('box_ordered', PAPU_LDPV . '/box.php', $box_ordered);

			$box_ordered_thumbs = array('group' => __('Boxes', 'iasd'));
			$box_ordered_thumbs['description'] = __('Box ordenado com miniaturas', 'iasd');
			$box_ordered_thumbs['ordered'] = true;
			$box_ordered_thumbs['item_max_length'] = 50;
			self::RegisterView('box_ordered_thumbs', PAPU_LDPV . '/box.php', $box_ordered_thumbs);

			$box_events = array('group' => __('Boxes', 'iasd'));
			$box_events['description'] = __('Box Eventos', 'iasd');
			$box_events['post_type'] = array('event');
			$box_events['cols'] = array('col-md-4');
			self::RegisterView('box_events', PAPU_LDPV . '/box_events.php', $box_events);

			/**
			Listas
			*/

			$list_simple = array('group' => __('Listas', 'iasd'));
			$list_simple['description'] = __('Lista', 'iasd');
			$list_simple['thumbnail'] = false;
			$list_simple['item_max_length'] = 90;
			$list_simple['widget_class'] = 'iasd-widget-posts_list';
			$list_simple['posts_per_page'] = 5;
			self::RegisterView('list_simple', PAPU_LDPV . '/list.php', $list_simple);

			$list_simple_thumbs = array('group' => __('Listas', 'iasd'));
			$list_simple_thumbs['description'] = __('Lista com miniaturas', 'iasd');
			$list_simple_thumbs['item_max_length'] = 60;
			$list_simple_thumbs['widget_class'] = 'iasd-widget-posts_list';
			$list_simple_thumbs['posts_per_page'] = 5;
			self::RegisterView('list_simple_thumbs', PAPU_LDPV . '/list.php', $list_simple_thumbs);

			$list_simple_thumbs_gallery = array('group' => __('Listas', 'iasd'));
			$list_simple_thumbs_gallery['description'] = __('Lista de galerias de imagens', 'iasd');
			$list_simple_thumbs_gallery['item_max_length'] = 60;
			$list_simple_thumbs_gallery['widget_class'] = 'iasd-widget-posts_list';
			$list_simple_thumbs_gallery['posts_per_page'] = 5;
			self::RegisterView('list_simple_thumbs_gallery', PAPU_LDPV . '/list_galleries.php', $list_simple_thumbs_gallery);
			
			$list_simple_highlight = array('group' => __('Listas', 'iasd'));
			$list_simple_highlight['description'] = __('Lista com 1 destaque', 'iasd');
			$list_simple_highlight['thumbnail'] = false;
			$list_simple_highlight['item_max_length'] = 90;
			$list_simple_highlight['highlight_max_length'] = 50;
			$list_simple_highlight['widget_class'] = 'iasd-widget-posts_list';
			$list_simple_highlight['highlight_class'] = 'highlight';
			self::RegisterView('list_simple_highlight', PAPU_LDPV . '/list.php', $list_simple_highlight);

			$list_simple_highlight_video = array('group' => __('Listas', 'iasd'));
			$list_simple_highlight_video['description'] = __('Lista com 1 destaque video', 'iasd');
			$list_simple_highlight_video['thumbnail'] = false;
			$list_simple_highlight_video['item_max_length'] = 90;
			$list_simple_highlight_video['highlight_max_length'] = 50;
			$list_simple_highlight_video['widget_class'] = 'iasd-widget-posts_list';
			$list_simple_highlight_video['highlight_class'] = 'highlight youtube';
			self::RegisterView('list_simple_highlight_video', PAPU_LDPV . '/list.php', $list_simple_highlight_video);

			$list_simple_thumbs_highlight = array('group' => __('Listas', 'iasd'));
			$list_simple_thumbs_highlight['description'] = __('Lista com miniaturas e 1 destaque', 'iasd');
			$list_simple_thumbs_highlight['item_max_length'] = 60;
			$list_simple_thumbs_highlight['highlight_max_length'] = 50;
			$list_simple_thumbs_highlight['widget_class'] = 'iasd-widget-posts_list';
			$list_simple_thumbs_highlight['highlight_class'] = 'highlight';
			self::RegisterView('list_simple_thumbs_highlight', PAPU_LDPV . '/list.php', $list_simple_thumbs_highlight);

			$list_simple_thumbs_highlight_video = array('group' => __('Listas', 'iasd'));
			$list_simple_thumbs_highlight_video['description'] = __('Lista com miniaturas e 1 destaque video', 'iasd');
			$list_simple_thumbs_highlight_video['item_max_length'] = 60;
			$list_simple_thumbs_highlight_video['highlight_max_length'] = 50;
			$list_simple_thumbs_highlight_video['widget_class'] = 'iasd-widget-posts_list';
			$list_simple_thumbs_highlight_video['highlight_class'] = 'highlight youtube';
			self::RegisterView('list_simple_thumbs_highlight_video', PAPU_LDPV . '/list.php', $list_simple_thumbs_highlight_video);

			$list_simple_thumbs_taxonomy = array('group' => __('Listas', 'iasd'));
			$list_simple_thumbs_taxonomy['description'] = __('Lista de Posts Simples com Miniaturas e Marcadores', 'iasd');
			$list_simple_thumbs_taxonomy['item_max_length'] = 60;
			$list_simple_thumbs_taxonomy['item_excerpt_length'] = 60;
			$list_simple_thumbs_taxonomy['show_taxonomy'] = true;
			$list_simple_thumbs_taxonomy['show_intro'] = true;
			$list_simple_thumbs_taxonomy['widget_class'] = 'iasd-widget-posts_categories';
			$list_simple_thumbs_taxonomy['posts_per_page'] = 5;
			self::RegisterView('list_simple_thumbs_taxonomy', PAPU_LDPV . '/list.php', $list_simple_thumbs_taxonomy);

			$list_projects = array('group' => __('Listas', 'iasd'));
			$list_projects['description'] = __('Lista de Botões', 'iasd');
			$list_projects['allow_grouping'] = false;
			$list_projects['allow_see_more'] = false;
			$list_projects['cols'] = array('col-md-4');
			self::RegisterView('list_projects', PAPU_LDPV . '/list_buttons.php', $list_projects);

			$list_galleries = array('group' => __('Listas', 'iasd'));
			$list_galleries['description'] = __('Lista de Galerias', 'iasd');
			$list_galleries['posts_per_page'] = 5;
			$list_galleries['post_type'] = array(IASD_ImageGallery::POST_TYPE);
			self::RegisterView('list_galleries', PAPU_LDPV . '/list_galleries.php', $list_galleries);

			/**
			GRIDS
			*/

			$grid = array('group' => __('Grids', 'iasd'));
			$grid['description'] = __('Grid de Posts', 'iasd');
			$grid['posts_per_page'] = 4;
			self::RegisterView('grid', PAPU_LDPV . '/grid.php', $grid);

			$grid_three = array('group' => __('Grids', 'iasd'));
			$grid_three['description'] = __('Grid de 3 Posts', 'iasd');
			$grid_three['posts_per_page'] = 3;
			$grid_three['posts_per_page_forced'] = true;
			self::RegisterView('grid_three', PAPU_LDPV . '/grid_three.php', $grid_three);

			$grid_five = array('group' => __('Grids', 'iasd'));
			$grid_five['description'] = __('Grid de 5 Posts', 'iasd');
			$grid_five['posts_per_page'] = 5;
			$grid_five['posts_per_page_forced'] = true;
			self::RegisterView('grid_five', PAPU_LDPV . '/grid_five.php', $grid_five);

			/**
			GRIDS Com Detalhes
			*/

			$grid_details = array('group' => __('Grids', 'iasd'));
			$grid_details['description'] = __('Grid detalhes', 'iasd');
			$grid_details['allow_grouping'] = false;
			$grid_details['allow_see_more'] = true;
			$grid_details['cols'] = array('col-md-12');
			self::RegisterView('grid_detalhes', PAPU_LDPV . '/grid_details.php', $grid_details);

			$grid_details_thumbs = array('group' => __('Grids', 'iasd'));
			$grid_details_thumbs['description'] = __('Grid detalhes com miniaturas', 'iasd');
			$grid_details_thumbs['allow_grouping'] = false;
			$grid_details_thumbs['allow_see_more'] = true;
			$grid_details_thumbs['cols'] = array('col-md-12');
			self::RegisterView('grid_details_thumbs', PAPU_LDPV . '/grid_details_thumbs.php', $grid_details_thumbs);

			$grid_details_highlight = array('group' => __('Grids', 'iasd'));
			$grid_details_highlight['description'] = __('Grid detalhes com miniaturas e destaque', 'iasd');
			$grid_details_highlight['allow_grouping'] = false;
			$grid_details_highlight['allow_see_more'] = false;
			$grid_details_highlight['cols'] = array('col-md-12');
			$grid_details_highlight['highlight'] = 1;
			self::RegisterView('grid_details_highlight', PAPU_LDPV . '/grid_details_thumbs.php', $grid_details_highlight);

			/**
			SLIDERS
			*/

			$slider_services = array('group' => __('Sliders', 'iasd'));
			$slider_services['description'] = __('Slider de Serviços', 'iasd');
			$slider_services['allow_grouping'] = false;
			$slider_services['allow_see_more'] = false;
			self::RegisterView('slider_services', PAPU_LDPV . '/slider_services.php', $slider_services);

			$slider_highlights_v = array('group' => __('Sliders', 'iasd'));
			$slider_highlights_v['description'] = __('Slider de Destaques Verticais', 'iasd');
			$slider_highlights_v['allow_grouping'] = false;
			$slider_highlights_v['allow_see_more'] = false;
			$slider_highlights_v['cols'] = array('col-md-8');
			$slider_highlights_v['widget_class'] = 'highlights';
			$slider_highlights_v['show_intro'] = true;
			self::RegisterView('slider_highlights_v', PAPU_LDPV . '/slider_highlights.php', $slider_highlights_v);

			// ASN 

			if ( SITE == "noticias" ) {
				$slider_asn = array('group' => __('Sliders', 'iasd'));
				$slider_asn['description'] = __('Slider ASN', 'iasd');
				$slider_asn['allow_grouping'] = false;
				$slider_asn['allow_see_more'] = false;
				$slider_asn['cols'] = array('col-md-8');
				$slider_asn['widget_class'] = 'highlights_asn';
				$slider_asn['show_intro'] = true;
				self::RegisterView('slider_asn', PAPU_LDPV . '/slider_asn.php', $slider_asn);
			}

			$slider_highlights_h = array('group' => __('Sliders', 'iasd'));
			$slider_highlights_h['description'] = __('Slider de Destaques Horizontais', 'iasd');
			$slider_highlights_h['allow_grouping'] = false;
			$slider_highlights_h['allow_see_more'] = false;
			$slider_highlights_h['cols'] = array('col-md-8');
			$slider_highlights_h['show_intro'] = false;
			self::RegisterView('slider_highlights_h', PAPU_LDPV . '/slider_highlights.php', $slider_highlights_h);

			$slider_newsstand = array('group' => __('Sliders', 'iasd'));
			$slider_newsstand['description'] = __('Slider de Publicações', 'iasd');
			$slider_newsstand['allow_grouping'] = false;
			self::RegisterView('slider_newsstand', PAPU_LDPV . '/slider_newsstand.php', $slider_newsstand);

			$slider_galleries = array('group' => __('Sliders', 'iasd'));
			$slider_galleries['description'] = __('Slider de Galerias', 'iasd');
			$slider_galleries['post_type'] = array(IASD_ImageGallery::POST_TYPE);
			$slider_galleries['cols'] = array('col-md-4');
			$slider_galleries['allow_grouping'] = false;
			self::RegisterView('slider_galleries', PAPU_LDPV . '/slider_galleries.php', $slider_galleries);

			$slider_columns = array('group' => __('Sliders', 'iasd'));
			$slider_columns['description'] = __('Slider de Autores', 'iasd');
			$slider_columns['cols'] = array('col-md-12');
			$slider_columns['allow_grouping'] = false;
			$slider_columns['allow_see_more'] = false;
			$slider_columns['posts_per_page'] = 5;
			self::RegisterView('slider_columns', PAPU_LDPV . '/slider_columns.php', $slider_columns);

			/**
			THUMBNAIL VIEW
			*/

			$thumbnail_view = array('group' => __('Exceções', 'iasd'));
			$thumbnail_view['description'] = __('Thumbnail', 'iasd');
			$thumbnail_view['cols'] = array('col-md-4');
			$thumbnail_view['allow_grouping'] = false;
			$thumbnail_view['allow_see_more'] = true;
			$thumbnail_view['posts_per_page'] = 1;
			self::RegisterView('thumbnail_view', PAPU_LDPV . '/thumbnail_view.php', $thumbnail_view);
		}
	}

	add_action('widgets_init', array('IASD_ListaDePosts_Views', 'RegisterDefaults'));

	if(defined('WP_DEBUG') && WP_DEBUG) {
		//add_action('widgets_init', array('IASD_ListaDePosts_Views', 'RegisterFakes'));
	}
}
