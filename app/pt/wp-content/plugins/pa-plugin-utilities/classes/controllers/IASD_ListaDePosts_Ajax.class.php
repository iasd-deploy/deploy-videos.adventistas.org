<?php

add_action( 'wp_ajax_' . IASD_ListaDePosts_Ajax::RULES, array('IASD_ListaDePosts_Ajax', 'Rules'), 10, 0);
add_action( 'wp_ajax_' . IASD_ListaDePosts_Ajax::CHECKSOURCE, array('IASD_ListaDePosts_Ajax', 'CheckSource'), 10, 0);

add_action( 'wp_ajax_' . IASD_ListaDePosts_Ajax::LOCAL_RULES, array('IASD_ListaDePosts_Ajax', 'LocalRules'), 10, 0);
add_action( 'wp_ajax_nopriv_' . IASD_ListaDePosts_Ajax::LOCAL_RULES, array('IASD_ListaDePosts_Ajax', 'LocalRules'), 10, 0);

add_action( 'sidebar_admin_setup', array('IASD_ListaDePosts_Ajax', 'ForceRules'));

add_action( 'admin_menu', array('IASD_ListaDePosts_Ajax', 'AddSubmenuPage'));


class IASD_ListaDePosts_Ajax {
	const LOCAL_RULES = 'iasd-localrules';
	const RULES       = 'iasd-rules';
	const CHECKSOURCE = 'iasd-checksource';
/**
		ADMIN PAGE
*/
	static function AddSubmenuPage() {
		return add_submenu_page(
			'pa-adventistas',
			__('Fontes de Conteúdo', 'iasd'), 
			__('Fontes de Conteúdo', 'iasd'), 
			'activate_plugins', 
			'pa-ldp-sources', 
			array(__CLASS__, 'SourcesList'));
	}

	static function SourcesList() {
?>
<div class="wrap">
	<div id="icon-tools" class="icon32">
		<br>
	</div>
	<h2><?php _e('Fontes de Conteúdo', 'iasd'); ?></h2>
	<h3><?php _e('Relatório de conectividade das fontes de conteúdo', 'iasd'); ?></h3>
	<form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<div>
			<div id="post-body" class="metabox-holder">
				<div id="post-body-content">
<?php
		$configs = self::CheckSources();
		foreach($configs as $source_id => $config) {
?>
					<fieldset style="border: 1px solid; padding: 0 20px; margin-top: 30px">
						<legend><b style="font-size: 20px; text-transform:capitalize;"><?php echo $source_id; ?></b></legend>
						<p><b>Status</b>: <?php echo ($config['status']) ? 'OK' : 'ERROR'; ?></p>
						<p><b>URL</b>: <?php echo $config['url']; ?></p>
<?php if($config['status']): ?>
						<p><b>Tipos de Conteúdo</b>: <?php echo count($config['post_type']); ?></p>
						<p><b>Autores</b>: <?php echo count($config['authors']); ?></p>
<?php else:
	if(is_wp_error($config['request']))
		echo '<p><b>Erro</b>: ', $config['request']->get_error_message(), '</p>';

endif; ?>
					</fieldset>
<?php
		}

?>
				</div><!-- /post-body -->
			</div>
		</div>
	</form>
</div>
<?php
	}
/**
		HELPERS
*/
	static function GetHTTPObject() {
		global $http;

		if(is_null($http))
			$http = apply_filters('get_http_object', new WP_Http());

		return $http;
	}

	static function DecodeToArray($items) {
		if(is_string($items)) {
			$decoded = json_decode($items);
			if($decoded)
				$items = $decoded;
		}
		if(is_object($items))
			$items = (array) $items;

		if(is_array($items)) {
			foreach($items as $k => $v)
				$items[$k] = self::DecodeToArray($v);
		}

		return $items;
	}
/**
		BASIC RULES
*/

	static function Translations() {
		$translations = array();

		$translations['may-lose-data-check']         = __('Algumas configurações atuais podem ser perdidas. Deseja continuar? Se confirmar lembre de verificar a compatibilidade da fonte de conteúdo.', 'iasd');
		$translations['may-lose-data']               = __('Algumas configurações atuais podem ser perdidas. Continuar?', 'iasd');
		$translations['mandatory']                   = __('Por favor, preencha os campos obrigatórios!', 'iasd');
		$translations['source-extra-invalid']        = __('Endereço para a fonte de conteúdos é inválido.', 'iasd');
		$translations['source-extra-not-compatible'] = __('O endereço não corresponde a um site compatível.', 'iasd');
		$translations['not-allowed']                 = __('Não permitir', 'iasd');
		$translations['select-one']                  = __('Selecione...', 'iasd');

		$translations['grouping_forced']             = __('Forçar Escolha', 'iasd');
		$translations['grouping_default']            = __('Mais Recentes', 'iasd');
		$translations['grouping_terms']              = __('Marcadores', 'iasd');

		return $translations;
	}

	static function Views() {
		$views = array();

		$all_views = IASD_ListaDePosts_Views::GetViews();
		foreach($all_views as $view_name => $view_info) {
			$views[$view_name] = array();
			$views[$view_name]['description'] = $view_info['description'];
			$views[$view_name]['cols'] = $view_info['cols'];
			$views[$view_name]['allow_grouping'] = $view_info['allow_grouping'];
			$views[$view_name]['allow_see_more'] = $view_info['allow_see_more'];
			$views[$view_name]['post_type'] = $view_info['post_type'];
			$views[$view_name]['posts_per_page'] = $view_info['posts_per_page'];
			$views[$view_name]['posts_per_page_forced'] = $view_info['posts_per_page_forced'];
		}

		return $views;
	}

	static function Sidebars() {
		$sidebars = array();
		global $wp_registered_sidebars;

		foreach($wp_registered_sidebars as $sidebar_name => $info)
			if(isset($info['col_class']) && in_array($info['col_class'], array('col-md-4', 'col-md-8', 'col-md-12')))
				$sidebars[$sidebar_name] = $info['col_class'];

		return $sidebars;
	}

	static function Widths() {
		return array(
				'col-md-4' => __('1/3 da Coluna ', 'iasd'),
				'col-md-8' => __('2/3 da Coluna ', 'iasd'),
				'col-md-12' => __('Coluna Inteira', 'iasd')
			);
	}

/**
		LOCAL RULES
*/

	static function Authors() {
		$authors = array();

		$args = apply_filters('local_authors', array('who' => 'authors'));
		$user_query = new WP_User_Query( $args );

		foreach ( $user_query->results as $user ) {
			$authors[$user->user_nicename] = apply_filters('local_authors_data', array('name' => $user->display_name));
		}

		return $authors;
	}

	static function PostTypes() {
		$post_type_names = get_post_types(array('_builtin' => false, 'public' => true));
		array_unshift($post_type_names, 'post', 'page');

		$post_type_names = apply_filters('local_post_types', $post_type_names);

		$availablePostTypes = array();
		foreach($post_type_names as $name) {
			$postType = get_post_type_object($name);

			$availablePostTypes[$name] = array();
			$availablePostTypes[$name]['name'] = $postType->label;

			$availablePostTypes[$name]['taxonomy'] = array();
			$taxonomies = get_object_taxonomies( $name, 'objects' );
			foreach($taxonomies as $taxonomy) {
				$availablePostTypes[$name]['taxonomy'][] = $taxonomy->name;
			}

			$availablePostTypes[$name]['postmeta'] = apply_filters('local_post_types-postmeta', array(), $name);

			$availablePostTypes[$name]['formats'] = apply_filters('local_post_types-formats', array(), $name);
		}

		return $availablePostTypes;
	}

	static function LocalRules($echo = true) {
		$response = array();
		$response['sources'] = array();

		$response['sources']['local'] = array();
		$response['sources']['local']['post_type'] = self::PostTypes();

		$response['sources']['local']['authors'] = self::Authors();

		$response['sources']['local']['url'] = site_url();

		if($echo) {
//			header('Content-type: application/json');
			echo json_encode($response);
		} else {
			return $response;
		}
	}

/**
		CHECK SOURCE
*/

	static function ValidateSourceUrl($source) {
		if(strpos($source, ':') === false)
			$source = 'http://' . $source;

		if(substr($source,  -1) != '/')
			$source .= '/';

		return $source;
	}

	static function CheckSource($source = false, $fieldId = false, $echo = true) {
		if(!$source)
			$source = $_REQUEST['source'];
		if(!$fieldId)
			$fieldId = $_REQUEST['fieldId'];

		$response = array();
		$response['status'] = false;
		$response['url'] = $source;

		if($source && $fieldId) {

			$source = self::ValidateSourceUrl($source);

			if(strlen($source) >= 13) {
				$response['url'] = $source;

				$request = self::GetHTTPObject()->get($source . 'wp-admin/admin-ajax.php?action=' . IASD_ListaDePosts_Ajax::LOCAL_RULES, array('timeout' => 20));

				$response['request'] = $request;

				if(!is_wp_error($request) 
					&& isset($request['response']) 
					&& isset($request['response']['code'])
					&& $request['response']['code'] == 200
					&& ($request['body'] != '0')
					&& (strlen($request['body']) > 1)) {

					$body = $request['body'];
					if(substr($body, -1) == '0')
						$body = substr($body, 0, -1);

					$remote_rules = self::DecodeToArray($body);
					if($remote_rules) {
						$response['status'] = true;
						if(isset($remote_rules['sources'])) {
							if(isset($remote_rules['sources']['local'])) {
								if(isset($remote_rules['sources']['local']['post_type']))
									$response['post_type'] = $remote_rules['sources']['local']['post_type'];

								if(isset($remote_rules['sources']['local']['authors']))
									$response['authors'] = $remote_rules['sources']['local']['authors'];
							}
						}
					}
				} else {
					$response['request'] = new WP_Error('Error', 'Invalid Content');
				}
			}
		}

		$response['field'] = $fieldId;

		if($echo)
			echo json_encode($response);
		else
			return $response;
	}

/**
		RULES
*/

	static function ForceRules() {
		if(isset($_GET['forcerules']))
			self::Rules(false, true);
	}

	static function CheckSources() {
		$basicSources = IASD_ListaDePosts::BasicSources();
		$otherSources = IASD_ListaDePosts::OtherSources();
		$sources = array_merge($basicSources, $otherSources);

		$configs = array();
		foreach($sources as $source_id => $source_info) {
			if(!in_array($source_id, array('local', 'outra')) && isset($source_info['url']) && $source_info['url']) {
				$configs[$source_id] = self::CheckSource($source_info['url'], 'none', false);
			}
		}

		return $configs;
	}

	static function Rules($echo = true, $ignoreCache = false) {
		$cache_ttl = get_option('IASD_ListaDePosts_Ajax::Rules::ttl', 0);
		$updateCache = (time() > $cache_ttl);

		if($ignoreCache)
			$updateCache = true;

		$response = get_option('IASD_ListaDePosts_Ajax::Rules::Cache', array('sources' => array()));
		if($updateCache && ($ignoreCache || defined('DOING_AJAX') || defined('DOING_CRON'))) {
			$local_rules = self::LocalRules(false);
			$response['sources']['local'] = $local_rules['sources']['local'];

			$response['width']        = self::Widths();

			$response['translations'] = self::Translations();

			$response['views']        = self::Views();

			$response['sidebars']     = self::Sidebars();

			$response['groups']       = IASD_ListaDePosts_Views::GetGroups();

			// $taxonomies = get_taxonomies(array('_builtin' => false), 'objects');
			$taxonomies = get_taxonomies(null, 'objects');
			$response['taxonomies']       = array();
			$response['taxonomies_names'] = array();
			foreach($taxonomies as $slug => $object) {
				$response['taxonomies'][] = $slug;
				$response['taxonomies_names'][$slug] = $object->label;
			}

			$availableSources = self::CheckSources();
			foreach($availableSources as $source => $config){
				if($config['status'] && isset($config['post_type'])) {
					$response['sources'][$source] = array();
					$response['sources'][$source]['url'] = $config['url'];
					$response['sources'][$source]['post_type'] = $config['post_type'];
					$response['sources'][$source]['authors'] = $config['authors'];
				}
			}

			foreach($response['sources'] as $source => $info)
				if(!isset($availableSources[$source]) && !in_array($source, array('local', 'outra')))
					unset($response['sources'][$source]);

			update_option('IASD_ListaDePosts_Ajax::Rules::ttl', time() + 3600);

			delete_option('IASD_ListaDePosts_Ajax::Rules::Cache');
			add_option('IASD_ListaDePosts_Ajax::Rules::Cache', $response, null, 'no');
		}

		if($echo) {
//			header('Content-type: application/json');
			echo json_encode($response);
		} else {
			return $response;
		}
	}
}


