<?php

add_filter('template_redirect', array('IASD_Taxonomias', 'SingleRedirect'));
add_action('init', array('IASD_Taxonomias', 'Init'));
add_action('init', array('IASD_Taxonomias', 'RegisterTaxonomies'), 100);
add_action('init', array('IASD_Taxonomias', 'Validation'), 200);
add_filter('term_link', array('IASD_Taxonomias', 'TratamentoPermalinks'), 100, 3);

add_action('admin_menu', array('IASD_Taxonomias', 'AdminMenu'), 9);
add_action('admin_menu', array('IASD_Taxonomias', 'SaveSettings'), 9999);
add_action('network_admin_menu', array('IASD_Taxonomias', 'AdminMenu'), 9);

add_action( 'wp_ajax_taxonomy-notify-update-available', array('IASD_Taxonomias', 'PushNotifyUpdate') );
add_action( 'wp_ajax_nopriv_taxonomy-notify-update-available', array('IASD_Taxonomias', 'PushNotifyUpdate') );

add_filter('query_string', array('IASD_Taxonomias', 'TratamentoSedes'), 100);

add_action('registered_taxonomy', array('IASD_Taxonomias', 'FixTaxonomies'), 10, 3);

// Global variables
$screen_log 		= '';
$error_on_sync 		= false;


class IASD_Taxonomias {
	const NOTIFICATION_RECEIVED = 'Update Notification Received';
	const UPDATE_ACTION = 'taxonomy_update_action';

	public static function Profile($context, $restart = false) {
		if(!defined('WP_DEBUG') || !WP_DEBUG) {
			return false;
		}
		global $IASD_TaxonomiasProfile;
		global $IASD_TaxonomiasProfileCount;
		$time = microtime(true);
		if(!$IASD_TaxonomiasProfile) {
			$IASD_TaxonomiasProfile = array();
			$IASD_TaxonomiasProfileCount = array();
		}
		if($restart || !isset($IASD_TaxonomiasProfile[$context])) {
			$IASD_TaxonomiasProfile[$context] = $time;
			$IASD_TaxonomiasProfileCount[$context] = -1;
		}
		$IASD_TaxonomiasProfileCount[$context]++;
		$time = $time - $IASD_TaxonomiasProfile[$context];
	}

	public static function FixTaxonomies($taxonomy, $object_type, $args) {
		if(in_array($taxonomy, array('category', 'post_tag', 'post_format'))) {
			global $wp_taxonomies;
			$args = $wp_taxonomies[$taxonomy];
			$args->public = false;

			$args->capabilities = array(
				'manage_terms' => 'edit_theme_options',
				'edit_terms'   => 'edit_theme_options',
				'delete_terms' => 'edit_theme_options',
				'assign_terms' => 'edit_theme_options',
			);

			$wp_taxonomies[$taxonomy] = $args;
		}
	}

	public static function SingleRedirect() {

		$elementor = FALSE;
		if ( class_exists('Elementor\Plugin') ) {
			$elementor = (\Elementor\Plugin::$instance->preview->is_preview_mode());
		}

		if(is_single() && !$elementor ) {
			global $post;
			$permalink = get_permalink($post->ID);
			if(strpos($permalink, $_SERVER['REQUEST_URI']) === false) {
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: $permalink");
				die;
			}
		}
	}


	// Validate mandatory itens before saving a post

	public static function Validation() {

		$objpost = $_POST;

		if (!isset($objpost['post_type']))
			return;
		
			
		if(!count($_POST)){
			return false;
		}
		if(!isset($_POST['post_title']) || !isset($_POST['post_ID']) || !isset($_POST['post_type'])) {
			return false;
		}

		// Return the active taxonomies in this current post type
		$active_taxonomies = $objpost['tax_input']; 

		// Create validation erros array 
		$_POST['validation_errors'] = array();

		
		if ( is_array( $active_taxonomies ) ) {

			foreach ($active_taxonomies as $key => $active_taxonomy) {

				if ( self::IsMandatory($key) ){
					
					// // If is mandatory tax and the count == 1 (none of terms selected)
					$terms_selected = count($active_taxonomy);
					if ($terms_selected <= 1){
						$_POST['validation_errors'][] = $key;
					}
				}
			}
		}


		if(strlen($_POST['post_title']) > 100)
			$_POST['validation_errors'][] = 'post_title';

		if(count($_POST['validation_errors'])) {
			if(isset($_POST['publish'])) {
				$_POST['save'] = $_POST['publish'];
				unset($_POST['publish']);
			}
		}
		
	}

	public static function Init() {
		add_action( 'redirect_post_location', array(__CLASS__, 'RedirectLocation'), 10, 2 );

		add_filter(self::UPDATE_ACTION, array(__CLASS__, 'UpdateInformation') );
	}

	public static function RedirectLocation( $location, $post_id ) {
		if(isset($_POST['validation_errors']) && count($_POST['validation_errors'])) {
			$serialize = serialize($_POST['validation_errors']);
			$base64 = base64_encode($serialize);
			$encoded = urlencode($base64);
			$location = add_query_arg( 'validation_errors', $encoded, $location);
			$location = add_query_arg( 'message', 777, $location);
		}

		return $location;
	}

	public static function ContextualHelp( $messages ) {
		$messages['post'][777] = __('Algumas informações estão equivocadas. Verifique as taxonomias e o tamanho do titulo.', 'iasd');
		if(isset($_GET['validation_errors'])) {
			$encoded = $_GET['validation_errors'];
			$base64 = urlencode($encoded);
			$serialize = base64_decode($base64);
			$validation_errors = unserialize($serialize);

			$messages['post'][777] = '';

			if(in_array('post_title', $validation_errors))
				$messages['post'][777] .= __('O titulo precisa ter menos de 100 caracteres.', 'iasd');

			$taxonomies = self::GetAllTaxonomies();
			foreach($taxonomies as $tax) {
				if(in_array($tax, $validation_errors)) {
					$taxonomy = get_taxonomy( $tax );
					$labels = $taxonomy->labels;

					if($messages['post'][777])
						$messages['post'][777] .= '<br />';

					$messages['post'][777] .= sprintf(__('A taxonomia "%s" é obrigatória.', 'iasd'), $labels->name);
				}
			}
		}
		return $messages;
	}

	public static function AdminMenu() {
		add_menu_page(__('Instituição', 'iasd'),
			__('Instituição', 'iasd'),
			'edit_pages',
			'pa-adventistas',
			array(__CLASS__, 'Menu'), false, 64);

		add_action('post_updated_messages', array(__CLASS__, 'ContextualHelp'), 11, 3 );

		add_submenu_page(
			'pa-adventistas',
			__('Taxonomias', 'iasd'),
			__('Taxonomias', 'iasd'),
			'activate_plugins',
			'pa-adv-taxonomias',
			array(__CLASS__, 'Page'));

		add_submenu_page(
			'pa-adventistas',
			__('Permalinks', 'iasd'),
			__('Permalinks', 'iasd'),
			'activate_plugins',
			'pa-adv-permalinks',
			array(__CLASS__, 'SettingsRender'));


		add_settings_section('pa-adv-permalinks-taxonomy', __('Taxonomias', 'iasd'), array(__CLASS__, 'TaxonomySettingsInfoSection'), 'pa-adv-permalinks');

		$taxonomies = self::GetAllTaxonomies();
		foreach($taxonomies as $taxonomy_slug) {
			$taxonomy = get_taxonomy($taxonomy_slug);

			add_settings_section('pa-adv-permalinks-taxonomy_' . $taxonomy_slug, __('Permalinks de Taxonomias', 'iasd') . ' ' . $taxonomy->label, false, 'pa-adv-permalinks');

			register_setting('pa-adv-permalinks', 'papermalinks_single_' . $taxonomy_slug);
			add_settings_field('papermalinks_single_' . $taxonomy_slug, __('Single', 'iasd'),
				array(__CLASS__, 'AdminMenuFieldSetting'), 'pa-adv-permalinks', 'pa-adv-permalinks-taxonomy_' . $taxonomy_slug, 'papermalinks_single_' . $taxonomy_slug);

			register_setting('pa-adv-permalinks', 'papermalinks_archive_' . $taxonomy_slug);
			add_settings_field('papermalinks_archive_' . $taxonomy_slug, __('Archive', 'iasd'),
				array(__CLASS__, 'AdminMenuFieldSetting'), 'pa-adv-permalinks', 'pa-adv-permalinks-taxonomy_' . $taxonomy_slug, 'papermalinks_archive_' . $taxonomy_slug);
		}
	}

	public static function TaxonomySettingsInfoSection() {
		echo '<p>' . __('Use os campos abaixo para configurar os permalinks das taxonomias', 'iasd') . '</p>';
	}

	public static function AdminMenuFieldSetting($setting_name) {
		switch ($setting_name) {
			case 'pafooter_endereco':
				echo '<textarea name="'.$setting_name.'" id="'.$setting_name.'" class="widefat">'. get_option($setting_name) .'</textarea>';
				break;
			default:
				echo '<input name="'.$setting_name.'" id="'.$setting_name.'" type="input" value="'. get_option($setting_name) .'" class="widefat" />';
				break;
		}
	}

	public static function SettingsRender() {
?>
<div class="wrap">
	<form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
<?php
		do_settings_sections($_GET['page']);
?>
		<div>
			<div id="publishing-action">
				<input type="submit" name="pa-adv-update" id="pa-adv-update" class="button-primary"
					value="<?php _e('Salvar'); ?>" name="Salvar" tabindex="4">
			</div>
		</div>
	</form>
</div>
<?php
	}

	public static function Menu () {

	}

	public static function SaveSettings() {
		if(isset($_POST['pa-adv-update'])) {
			$whitelist_options = apply_filters( 'whitelist_options', array());
			foreach($whitelist_options as $page => $options) {
				if(strpos($page, 'pa-adv-') === 0) {
					foreach($options as $option) {
						if(isset($_POST[$option])) {
							update_option($option, $_POST[$option]);
							if($option == 'paheader_titulo')
								update_option('blogname', $_POST[$option]);
							if($option == 'paheader_descricao')
								update_option('blogdescription', $_POST[$option]);
						}
					}
				}
			}
		}
	}

	public static function SetServerUrl($new_url, $network = false) {
		$old_url = self::GetServerUrl();
		$requestsResults = array();
		update_option('xtt_tax_server_url', $new_url);
		$requestsResults = self::UpdateInformation();

		if($network) {
			self::NetworkCopyInformation($requestsResults);
		}
	}

	public static function NetworkCopyInformation($requestsResults = array()) {
		$count = get_blog_count();
		$o1 = get_option('xtt_tax_server_url');
		$o2 = get_option('master_footer_1');
		$o3 = get_option('master_footer_2');
		$o4 = get_option('master_footer_3');
		$o5 = get_option('master_header');
		$o6 = get_option('asf_taxonomy_default_subtitle');


		for($i = 1; $i <= $count; $i++) {
			switch_to_blog($i);
			do_action('IASD_Taxonomias::UpdateStart');
			update_option('xtt_tax_server_url', $o1);
			update_option('master_footer_1', $o2);
			update_option('master_footer_2', $o3);
			update_option('master_footer_3', $o4);
			update_option('master_header', $o5);
			update_option('asf_taxonomy_default_subtitle', $o6);
			do_action('IASD_Taxonomias::UpdateFinish');

			restore_current_blog();
		}

	}

	public static function UpdateInformation($requestsResults = array()) {
		
		try {

			global $logger;
			
			echo '<h2>Resultado da sincronização</h2>';

			self::SetStatusDate('request');
			do_action('IASD_Taxonomias::UpdateStart');
			if(!$requestsResults || $requestsResults == 3600)
				$requestsResults = array();

			$requestsResults['footer'] = self::RequestFooterInformation( (isset($requestsResults['footer'])) ? $requestsResults['footer'] : false );
			$requestsResults['taxonomy'] = self::RequestTaxonomyInformation( (isset($requestsResults['taxonomy'])) ? $requestsResults['taxonomy'] : false );
			$requestsResults['menu'] = self::RequestMenuInformation( (isset($requestsResults['menu'])) ? $requestsResults['menu'] : false );
			
			add_thickbox();

			if ($error_on_sync || !$requestsResults['footer'] || !$requestsResults['footer'] || !$requestsResults['footer']){
				echo '<div class="error">Houve falha(s) na sincronização.</div>';
			} else {
				echo '<div class="updated below-h2" id="sync_message" style="padding: 20px; margin-top: 10px; margin-bottom: 10px;">Sincronização realizada com sucesso.<a style="margin-left:10px" href="#TB_inline?width=600&height=550&inlineId=log_result" class="thickbox">Visualisar log</a></div>';
				$screen_log_to_user = get_option('xtt_last_sync');

				echo '<div class="metabox-holder">
				<div id="post-body-content"  style="margin-left: 2%; width: 95%;">
				<div class="stuffbox" id="log_result" style="display:none;">
				<h2> Log de sincronização</h2>
				<div class="inside"><p>' .$screen_log_to_user. '</p>
				</div>
				</div>
				</div>
				</div>';

				
			}

			if(!count($requestsResults))
				self::RequestInformation('ack');

			self::SetStatusDate('sync');

			do_action('IASD_Taxonomias::UpdateFinish');

			return $requestsResults;
		
		} catch (Exception $e) {
			$logger->info('ERRO UpdateInformation ' . var_export($e, TRUE));
		}
		
	}

	public static function RequestInformation($action) {
		$server_url = self::GetServerUrl();

		global $http;
		if (empty($http)) $http = new WP_Http();

		$url = $server_url . 'wp-admin/admin-ajax.php?action='.$action.'-request&name='.urlencode(get_bloginfo()).'&url='.site_url('/');

		$result = $http->request($url, array(CURLOPT_RETURNTRANSFER => 1, 'CURLOPT_USERAGENT' => 'XTT PA Taxonomy Client', 'timeout' => 30));

		if(is_wp_error($result))
			return array(500, $result->get_error_message());
		
		return array($result['response']['code'], $result['body']);
	}

	public static function RequestMenuInformation($requestResult = false) {
		$show_new = apply_filters('IasdHelperShowNew', true);
		$menuTypeRequest = $show_new ? 'nav' : 'menu';
		if(!$requestResult) {
			$requestResult = self::RequestInformation($menuTypeRequest);
		}
		list($responseCode, $curlResult) = $requestResult;

		if($responseCode != 200) {
			$menuTypeRequest = ucfirst($menuTypeRequest);
			echo '<div class="error">', $menuTypeRequest, ': ', $curlResult, '</div>';
			$error_on_sync = true;
			return false;

		} elseif($curlResult == '0') {
			echo '<div class="error">Servidor Desativado</div>';
			$error_on_sync = true;
			return false;

		} else {
			$menus = json_decode($curlResult);
			if(!is_array($menus) && !is_object($menus)) {
				echo '<div class="error">', $menuTypeRequest, ': ', count($curlResult), ' caracteres</div>';
				$error_on_sync = true;
				return false;
			}
			foreach($menus as $menu_name => $menu_html) {
				// echo '<div class="error">Atualizado ', $menuTypeRequest, ':', $menu_name, '</div>';
				update_option($menu_name, $menu_html);
			}
		}

		return $requestResult;
	}


	public static function RequestFooterInformation($requestResult = false) {
		if(!$requestResult) {
			$requestResult = self::RequestInformation('footer');
		}

		list($responseCode, $curlResult) = $requestResult;

		if($responseCode != 200) {
			echo '<div class="error">Footer: ', $curlResult, '</div>';
			$error_on_sync = true;
			return false;

		} elseif($curlResult == '0') {
			echo '<div class="error">Servidor Desativado</div>';
			$error_on_sync = true;
			return false;

		} else {
			$footer_infos = json_decode($curlResult);
			if(!is_array($footer_infos) && !is_object($footer_infos)) {
				echo '<div class="error">Footer: ', count($curlResult), ' caracteres</div>';
				$error_on_sync = true;

				return false;
			}
			foreach($footer_infos as $name => $value) {
				update_option($name, $value);
			}
		}

		return $requestResult;
	}

	public static function RequestTaxonomyInformation( $requestResult = false ) {

		try {

			global $logger;
			global $screen_log;
			$logger = &Log::singleton( 'memory' );

			global $wpdb;

			if ( ! $requestResult ) {
				$requestResult = self::RequestInformation( 'taxonomy' );
			}
			list($responseCode, $curlResult) = $requestResult;

			$taxonomies = IASD_Taxonomias::GetAllTaxonomies();

			$all_terms = get_terms( $taxonomies, array( 'hide_empty' => false, 'hierarchical' => false ) );

			$all_terms_cache = array();
			foreach ( $all_terms as $all_term ){
				$all_terms_cache[$all_term->term_id] = $all_term;
			}

			if($responseCode != 200) {
				$logger->info('Requisição por taxonomias retornou HTTP STATUS '.$responseCode);
				echo '<div class="error">Taxonomias: ', count($curlResult), ' caracteres</div>';
				$error_on_sync = true;
				return false;


			} elseif($curlResult == '0') {
				$logger->err('Impossível conectar com servidor de taxonomias. Recebido conteúdo "0"');
				echo '<div class="error">Servidor Desativado</div>';
				$error_on_sync = true;
				return false;

			} else {

				$taxonomyTree = json_decode($curlResult);

				if(!is_array($taxonomyTree) && !is_object($taxonomyTree)) {
					$logger->err('Formato inválido para taxonomias.');
					$logger->debug( $taxonomyTree);
					return false;
				}
				
				$screen_log = $screen_log . ' <b> * Iniciando sincronização * </b><br><br>';


				foreach ( $taxonomyTree as $taxonomy => $remote_terms ) {

					// Bloqueia a sincronização de category e post_tag
					if ($taxonomy == 'category' || $taxonomy == 'post_tag') { continue; }

					$local_terms 			= get_terms( $taxonomy, array('hide_empty' => false) );
					
					$update_term_list 		= array();
					$create_terms_list 		= array();

					usort($remote_terms, 'cmp');

					$logger->info('----------------- Taxonomia '.$taxonomy . '-----------------');
					$screen_log = $screen_log . ' <b> Sincronizando taxonomia '.$taxonomy . '</b><br>';

					  foreach ( $remote_terms as $i => $remote_term ) {

						$remote_term_slug 			= $remote_term->slug;
						$remote_term_parent_id 		= $remote_term->parent;
						$remote_slug_parent			= '';
						$remote_term->parent_slug	= '';

						$have_slug 			= FALSE;
						$local_term_aux 	= '';
						$local_term_i_aux	= '';
						
						// verify the remote term parent slug
						if ($remote_term_parent_id != '0' && $remote_term_parent_id !=null){	
							foreach ( $remote_terms as $u => $remote_term2 ) {
								if ($remote_term->parent == $remote_term2->term_id) {
									$remote_slug_parent  = $remote_term2->slug;
									$remote_term->parent_slug = $remote_term2->slug;
									break;
								}
							}
						}

						foreach ($local_terms as $j => $local_term) {

							$local_term_aux = $local_term;

							if ( $remote_term->slug == $local_term->slug ) {
								$have_slug = TRUE;
								$local_term_i_aux = $j;
								break;
							}
						}

						
						// this functions below verify if the current local term already have its parent
						// if doesnt have its parent the term is moved to the last position of array
						if ($remote_slug_parent != '0' && $remote_slug_parent != null) {
							$local_parent_id = self::getLocalParentId($remote_slug_parent, $taxonomy);
						} else {
							$local_parent_id = '0';
						}


						if ($local_parent_id != null || $remote_term_parent_id == 0){
							
							if ( $have_slug ) {

								if ($remote_slug_parent != '0' && $remote_slug_parent != null) {
									$local_parent_id_update = self::getLocalParentId($remote_slug_parent, $taxonomy);
								} else {
									$local_parent_id_update = '0';
								}
								$remote_term->parent = $local_parent_id_update;

								self::UpdateTerm($taxonomy, $local_term_aux, $remote_term);
								unset($local_terms[$local_term_i_aux]);

							} else {

								$id_term = term_exists($remote_term->slug);
					
								if ($id_term == 0 || $id_term == null) {
									
									if ($remote_slug_parent != '0' && $remote_slug_parent != null) {
										$local_parent_id_update = self::getLocalParentId($remote_slug_parent, $taxonomy);
									} else {
										$local_parent_id_update = '0';
									}
									$remote_term->parent = $local_parent_id_update;

									array_push($create_terms_list, $remote_term);
									unset($local_terms[$local_term_i_aux]);

								} else {

									try {

										if ($remote_slug_parent != '0' && $remote_slug_parent != null) {
											$local_parent_id_update = self::getLocalParentId($remote_slug_parent, $taxonomy);
										} else {
											$local_parent_id_update = '0';
										}

										$remote_term->parent = $local_parent_id_update;

										if ($id_term != null || $id_term !='0'){

											$res_query = $wpdb->update( 
												'wp_terms', 
												array('name' => $remote_term->name),
												array( 'term_id' => $id_term )
											); 

										}
										
										array_push($create_terms_list, $remote_term);

										unset($local_terms[$local_term_i_aux]);

									} catch (Exception $e) {

									}
								
								}

							}

						} else {
							self::moveElement($remote_terms,$i, sizeof($remote_terms)-1 ) ;
							$i = $i - 1; 
						}				

					}


					// Process the existing list (update)
					// foreach ($update_term_list as $update_term) {
					// 	self::UpdateTerm($taxonomy, $update_term, $local_term_i_aux);
					// }

					$remove_terms_list	= $local_terms;

					// Process the create list 
					foreach ($remove_terms_list as $remove_term) {
					 	self::RemoveTerm($taxonomy, $remove_term);
					}

					// Process the create list
					foreach ($create_terms_list as $create_term) {
					 	 self::CreateTerm($taxonomy, $create_term);
					}


				}
			
			}

		$screen_log = $screen_log . ' <br> <b> * Fim da sincronização * </b><br>';

		// Verify if exist the option xtt_last_sync
		// if exists update saving the new log else its create the option
		update_option('xtt_last_sync', $screen_log);

		return $requestResult;

		} catch (Exception $e) {

		}
	}


	private static function UpdateTerm($taxonomy, $update_term, $remote_term) {
		global $logger;
		global $screen_log;

		$args = array(	'slug' => $remote_term->slug, 'name' => $remote_term->name, 'taxonomy' => $taxonomy,
						'description' => $remote_term->description, 'parent'=> $remote_term->parent );

		if ($update_term->name != $remote_term->name ||
			$update_term->description != $remote_term->description ||
			$update_term->parent != $remote_term->parent ) {

			$return = wp_update_term($update_term->term_id, $taxonomy, $args);
			$logger->info(' ** Termo  '.$update_term->slug . ' foi atualizado em'  . $taxonomy);
			$screen_log = $screen_log . ' ** Termo  '.$update_term->slug . ' foi atualizado em ' . $taxonomy .'<br>';

		}
	}


	private static function CreateTerm($taxonomy, $create_term) {
		global $logger;
		global $screen_log;
	
		$args = array(	'slug' => $create_term->slug, 'name' => $create_term->name, 'taxonomy' => $taxonomy,
						'description' => $create_term->description,
						'parent'=> $create_term->parent );


		$return = wp_insert_term($create_term->name, $taxonomy, $args);
		$screen_log = $screen_log . ' ++ Termo  '.$create_term->slug . ' foi criado em ' . $taxonomy .'<br>';

		if(is_object($return)){
			if(isset($return->error_data['term_exists'])){
				throw new Exception('Erro ao adicionar termo ' . $create_term->slug . ' | taxonomy: ' . $taxonomy, 1);
																			
			}
		}
	}


	private static function RemoveTerm($taxonomy, $remove_term) {
		
		global $logger;
		global $screen_log;
	
		$return = wp_delete_term($remove_term->term_id, $taxonomy);	
		$logger->info(' -- Termo  '.$remove_term->slug . ' foi removido em ' . $taxonomy);
		$screen_log = $screen_log . ' -- Termo  '.$remove_term->slug . ' foi removido em ' . $taxonomy .'<br>' ;

	}




	private static function getLocalParentId($remote_slug_parent, $taxonomy) {

		try {

			global $logger;

			if ($remote_slug_parent != '') {
				$id_parent 	= term_exists($remote_slug_parent, $taxonomy);


				if ($id_parent != 0 && $id_parent != null) {
					return $id_parent["term_id"];		
				} else {
					return null;
				}
			} else {
				return null;
			}
			

		} catch (Exception $e) {
			throw 'Erro funcao getTheParent - tamxonomy.php ' . $e;
		}

	}

	private static function moveElement(&$array, $a, $b) {
    	$out = array_splice($array, $a, 1);
    	array_splice($array, $b, 0, $out);
	}





	// private static function classify_remote_term_actions( $remote_term, &$taxonomyTree ){
	// 	global $logger;

	// 	$term_exists = false;

	// 	foreach ( $taxonomyTree as $taxonomy => $local_term_list) {

	// 		$logger->info( '===================== taxonomyTree '. $taxonomy . '=====================');		
	// 		$local_term_list = get_terms( $taxonomy, array('hide_empty' => false) );

	// 		foreach ( $local_term_list as $z => $local_term ) {

	// 			if ( $local_term->slug == $remote_term->slug ){
	// 				// $local_term already exists
	// 				$term_exists = true;
	// 				$logger->info( 'Exists: '. $remote_term->slug );

	// 				//remove from local_term_list... remaining items will be deleted.
	// 				array_splice( $local_term_list, $z, 1 );
	// 				break;
	// 			}
	// 		}

	// 	}

	// 	if ( ! $term_exists ){
	// 		$logger->info( 'Dont exists: '. $remote_term->slug );
	// 	}


	// }


// 	private static function ProcessTerm($taxonomy, $remote_term, $remote_terms, &$existing_ids, &$existing_ids_reverse, &$synced_ids, &$all_terms_cache) {
// 		$parent_term_id = 0;
// 		if($remote_term->parent && !isset($existing_ids_reverse[$remote_term->parent])) {
// 			echo PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL;
// 			//var_dump($remote_term);
// 			$parent = $remote_terms[$remote_term->parent];
// 			$parent_term_id = self::ProcessTerm($taxonomy, $parent, $remote_terms, $existing_ids, $existing_ids_reverse, $synced_ids, $all_terms_cache);
// 		}
// 		if(isset($existing_ids_reverse[$remote_term->parent]) || !$remote_term->parent) {
// 			self::Profile('TaxonomiasForeach');
// 			$local_term_id = isset($existing_ids_reverse[$remote_term->term_id]) ? $existing_ids_reverse[$remote_term->term_id] : false;

// 			$args = array('slug' => $remote_term->slug, 'name' => $remote_term->name, 'taxonomy' => $taxonomy,
// 						'description' => $remote_term->description,
// 						'parent'=> ($remote_term->parent) ? $existing_ids_reverse[$remote_term->parent] : 0 );

// 			if($local_term_id === false) {
// 				$return = wp_insert_term($remote_term->name, $taxonomy, $args);

// 				if(is_array($return))
// 					$local_term_id = $return['term_id'];
// 				if(is_object($return))
// 					if(get_class($return) == 'WP_Error')
// 						if(is_array($return->error_data))
// 							if(isset($return->error_data['term_exists']))
// 								$local_term_id = $return->error_data['term_exists'];
// 				if($local_term_id)
// 					$all_terms_cache[$local_term_id] = get_term_by('id', $local_term_id, $taxonomy);
// 				echo '<div class="error"><h1>Criou ', ucfirst($taxonomy), ': ', $remote_term->name, '.</h1></div>';
// 			}
// 			self::Profile('TaxonomiasForeach');

// 			$taxonomy_current = $taxonomy;

// 			$local_term = (isset($all_terms_cache[$local_term_id])) ? $all_terms_cache[$local_term_id] : false;

// 			if($local_term) {
// 				self::Profile('TaxonomiasForeach');
// 				if($local_term->taxonomy != $taxonomy_current) {
// 					echo "Term: " . $local_term->slug . ' ('.$local_term->taxonomy.')'.PHP_EOL;
// 					echo $local_term->taxonomy .' != '.$taxonomy_current.PHP_EOL.PHP_EOL;
// 					//o mesmo termo pode estar em várias taxonomias.
// 					//não estamos tratando a taxonomia correta neste momento.
// 					//passar para o próximo
// 					return $local_term_id;
// 				}
// 				self::Profile('TaxonomiasForeach');


// 				$need_to_update = false;
// 				if($args['slug'] != $local_term->slug)
// 					$need_to_update = true;
// 				if($args['name'] != $local_term->name)
// 					$need_to_update = true;
// 				if($args['taxonomy'] != $local_term->taxonomy)
// 					$need_to_update = true;
// 				if($args['description'] != $local_term->description)
// 					$need_to_update = true;
// 				if($args['parent'] != $local_term->parent)
// 					$need_to_update = true;

// 				self::Profile('TaxonomiasForeach');
// 				if($need_to_update)
// 					wp_update_term( $local_term_id, $taxonomy_current, $args);

// 				self::Profile('TaxonomiasForeach');
// 				if(property_exists($remote_term, 'extra_information')) {
// 					self::Profile('TaxonomiasForeach');

// 					$remote_extra_information = (is_object($remote_term->extra_information)) ? get_object_vars($remote_term->extra_information) : $remote_term->extra_information;
// 					$local_extra_information =  get_option('xtt_cat_info_' . $local_term_id, array());

// 					if(isset($remote_extra_information['thumbnail_url']) && !empty($remote_extra_information['thumbnail_url'])) {
// 						if(!isset($local_extra_information['thumbnail_master_id']))
// 							$local_extra_information['thumbnail_master_id'] = -1;

// 						if($local_extra_information['thumbnail_master_id'] != $remote_extra_information['thumbnail_id']) {
// 							// Download file to temp location
// 							$tmp = download_url( $remote_extra_information['thumbnail_url'] );

// 							// Set variables for storage
// 							// fix file filename for query strings
// 							preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $remote_extra_information['thumbnail_url'], $matches );
// 							$file_array['name'] = basename($matches[0]);
// 							$file_array['tmp_name'] = $tmp;

// 							// If error storing temporarily, unlink
// 							if ( is_wp_error( $tmp ) ) {
// 								@unlink($file_array['tmp_name']);
// 								$file_array['tmp_name'] = '';
// 							}

// 							// do the validation and storage stuff
// 							$local_thumbnail_id = media_handle_sideload( $file_array, 0 );
// 							// If error storing permanently, unlink
// 							if ( is_wp_error($local_thumbnail_id) ) {
// 								$remote_extra_information['thumbnail_url'] = '';
// 							} else {
// 								$remote_extra_information['thumbnail_master_id'] = $remote_extra_information['thumbnail_id'];
// 								$remote_extra_information['thumbnail_id'] = $local_thumbnail_id;
// 								$remote_extra_information['thumbnail_url'] = wp_get_attachment_url( $local_thumbnail_id );
// 							}
// 						}
// 					}

// 					if(empty($remote_extra_information['thumbnail_url'])) {
// 						$remote_extra_information['thumbnail_url'] = '';
// 						$remote_extra_information['thumbnail_id'] = '';
// 						$remote_extra_information['thumbnail_old'] = '';
// 					}
// 					if(count(array_diff($remote_extra_information, $local_extra_information)))
// 						update_option('xtt_cat_info_' . $local_term_id, $remote_extra_information);

// 					self::Profile('TaxonomiasForeach');
// 				}

// 				self::Profile('TaxonomiasForeach');
// 				$existing_ids[$local_term_id] = $remote_term->term_id;
// 				$synced_ids[$local_term_id] = $remote_term->term_id;
// 				$existing_ids_reverse = array_flip($existing_ids);

// 				// echo $taxonomy_current . PHP_EOL.PHP_EOL;
// 				// var_dump($synced_ids);
// 				// echo PHP_EOL.PHP_EOL.PHP_EOL;
// 				// var_dump($existing_ids);
// 				// echo PHP_EOL.PHP_EOL."##########################################".PHP_EOL.PHP_EOL;

// 				self::Profile('TaxonomiasForeach');
// 				return $local_term_id;
// 			} else {
// //								ocorreu erro!
// 				echo '<div class="error"><h1>Taxonomia local não encontrada: ID: ', $local_term_id, '. Remoto: '.$remote_term->name,'</h1></div>';
// 			}
// 			self::Profile('TaxonomiasForeach');
// 			$remote_terms[$remote_term->term_id] = false;
// 			$remote_terms = array_filter( $remote_terms );
// 			self::Profile('TaxonomiasForeach');
// 		} else {
// 			var_dump($existing_ids_reverse);
// 			var_dump($remote_term->parent);
// 			die;
// 		}
// 	}

	public static function GetServerUrl() {
		$url = get_option('xtt_tax_server_url');
		if(!$url)
			add_option('xtt_tax_server_url', '', '', 'no');

		return $url;
	}

	public static function GetSubtitle() {
		$subtitle = get_option('pa-iasd-header-subtitle');

		return $subtitle;
	}

	public static function SetSubtitle($subtitle) {
		update_option('pa-iasd-header-subtitle', $subtitle);
	}

	public static function GetAllPostTypes() {
		$a = array('post', 'page');
		$b = get_post_types(array('_builtin' => false,));

		return array_merge($a, $b);
	}

	public static function GetAllTaxonomies() {
		$taxonomies = array();
		$taxonomies[] = self::TAXONOMY_PROJETOS;
		$taxonomies[] = self::TAXONOMY_DEPARTAMENTOS;
		$taxonomies[] = self::TAXONOMY_EDITORIAS;
		$taxonomies[] = self::TAXONOMY_REGIAO;
		$taxonomies[] = self::TAXONOMY_COLECOES;
		$taxonomies[] = self::TAXONOMY_EVENTOS;
		$taxonomies[] = self::TAXONOMY_SEDES_REGIONAIS;
		$taxonomies[] = self::TAXONOMY_TIPO_MATERIAL;
		$taxonomies[] = self::TAXONOMY_TIPO_MIDIA;
		$taxonomies[] = self::TAXONOMY_OWNER;
		$taxonomies[] = self::TAXONOMY_DESTAQUE;
		$taxonomies[] = self::TAXONOMY_SECAO;
		$taxonomies[] = self::TAXONOMY_CATEGORY;


		return $taxonomies;
	}

	public static function IsMandatory($type) {
		$taxonomies = array();
		$taxonomies[] = self::TAXONOMY_EDITORIAS;
		$taxonomies[] = self::TAXONOMY_DEPARTAMENTOS;
		$taxonomies[] = self::TAXONOMY_SEDES_REGIONAIS;
		$taxonomies[] = self::TAXONOMY_EVENTOS;
		$taxonomies[] = self::TAXONOMY_TIPO_MATERIAL;
		$taxonomies[] = self::TAXONOMY_TIPO_MIDIA;
		$taxonomies[] = self::TAXONOMY_OWNER;

		return in_array($type, $taxonomies);

	}

	public static function GetTaxonomyMap($type = false) {
		$fullMap = get_option('xtt_tax_map');
		if(!$fullMap) {
			$taxonomies = self::GetAllTaxonomies();
			$fullMap = array();
			foreach($taxonomies as $tslug)
				$fullMap[$tslug] = array();

			update_option('xtt_tax_map', $fullMap);
		}

		if($type)
			if(isset($fullMap[$type]))
				return $fullMap[$type];
			else
				return false;

		return $fullMap;
	}

	public static function SetTaxonomyMap($new_map) {
		$map = self::GetTaxonomyMap();
		$taxonomies = self::GetAllTaxonomies();

		foreach($taxonomies as $slug) {
			$map[$slug] = array();

			if(isset($new_map[$slug]) && is_array($new_map[$slug]))
				$map[$slug] = $new_map[$slug];
		}

		update_option('xtt_tax_map', $map );
	}

	public static function Page() {
		$is_network = is_network_admin();


		if(isset($_REQUEST['pa-iasd-header-subtitle']) && isset($_REQUEST['save_subtitle']))
			self::SetSubtitle($_REQUEST['pa-iasd-header-subtitle']);

		if(isset($_REQUEST['taxonomy_server']) && isset($_REQUEST['init_updates']))
			self::SetServerUrl($_REQUEST['taxonomy_server'], $is_network);

		if(isset($_REQUEST['tax_map']) && isset($_REQUEST['save_map']))
			self::SetTaxonomyMap($_REQUEST['tax_map']);

		$types = self::GetAllPostTypes();

		$taxonomies = self::GetAllTaxonomies();

		$taxonomy_map = self::GetTaxonomyMap();

?>

<div class="wrap">
	<div id="icon-tools" class="icon32"><br></div><h2><?php _e('Taxonomias', 'iasd'); ?></h2>
	<form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<div id="">
			<div id="post-body" class="metabox-holder">
				<div id="post-body-content">
<?php if(!$is_network): ?>

					<div id="admin-taxo" class="stuffbox">
						<div class="inside">
							<h2><?php _e('Taxonomias', 'iasd'); ?></h2>
							<table class="form-table editcomment">
								<tbody>
									<tr valign="top">
										<td><?php _e('Ativação de Taxonomias', 'iasd');?></td>
									</tr>
									<tr valign="top">
										<td>
											<table>
												<tr bgcolor="#bbb">
													<td>Post Types x <br /><?php _e('Taxonomias', 'iasd');?></td>
													<?php foreach($types as $k => $typeslug): $type = get_post_type_object($typeslug); ?>
														<td><?php echo $type->label; ?></td>
													<?php endforeach; ?>
												</tr>
												<?php
													$i = 0;
													foreach($taxonomies as $taxslug):
														$taxonomy = get_taxonomy($taxslug);
														$i++;
												?>
													<tr bgcolor="<?php echo ($i % 2) ? '#eee' : '#ddd';?>" >
														<td><?php echo $taxonomy->label; ?></td>
														<?php foreach($types as $typeslug): if(!isset($taxonomy_map[$taxslug])) $taxonomy_map[$taxslug] = array(); ?>
															<td><input type="checkbox" name="tax_map[<?php echo $taxslug; ?>][]" <?php if(in_array($typeslug, $taxonomy_map[$taxslug])) echo 'checked="checked"'; ?> value="<?php echo $typeslug; ?>" /> </td>
														<?php endforeach; ?>
													</tr>
												<?php endforeach; ?>
											</table>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<div id="">
												<div id="">
													<input type="submit" name="save_map" id="save_map" class="button-primary"
														value="<?php _e('Salvar', 'iasd'); ?>" tabindex="4">
												</div>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
							<br />
						</div>
					</div>
					<br />
<?php endif; ?>

					<div id="avisa-master" class="stuffbox">
						<div class="inside">
							<h2><?php _e('Configuração e Sincronia', 'iasd'); ?></h2>
							<table class="form-table editcomment">
								<tbody>
									<tr valign="top">
										<td><?php _e('Endereço do Servidor de Taxonomias', 'iasd');?></td>
									</tr>
									<tr valign="top">
										<td><input type="text" name="taxonomy_server" class="widefat" id="taxonomy_server" value="<?php echo self::GetServerUrl(); ?>"></td>
									</tr>
									<tr valign="top">
										<td>
											<div id="">
												<div id="">
													<input type="submit" name="init_updates" id="init_updates" class="button-primary"
														value="<?php _e('Atualizar e Sincronizar', 'iasd'); ?>" tabindex="4">
												</div>
											</div>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<h2><?php _e('Últimos Eventos', 'iasd');?></h2>
											<table>
												<thead>
													<tr>
														<th align="center"><?php _e('Notificações do Servidor', 'iasd');?></th>
														<th align="center"><?php _e('Pedidos de Atualização', 'iasd');?></th>
														<th align="center"><?php _e('Atualização Completa', 'iasd');?></th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td align="center"><b><?php if(self::GetNotifyDate()) echo date('Y-m-d H:i:s', self::GetNotifyDate()); ?></b></td>
														<td align="center" bgcolor="<?php echo (self::IsGoodRequestDate()) ? 'B0FFB0' : 'FFB0B0'; ?>" ><b><?php if(self::GetRequestDate()) echo date('Y-m-d H:i:s', self::GetRequestDate()); ?></b></td>
														<td align="center" bgcolor="<?php echo (self::IsGoodSyncDate()) ? 'B0FFB0' : 'FFB0B0'; ?>"><b><?php if(self::GetSyncDate()) echo date('Y-m-d H:i:s', self::GetSyncDate()); ?></b></td>
													</tr>
												</tbody>
											</table>
											<div id="sync_logs">
											<p>
											<?php
												if ($screen_log_to_user != ''){
													echo $screen_log_to_user;
												}
											?>
											</p>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
							<br />
						</div>
					</div>
					<script>
					jQuery( "#init_updates" ).click(function( event ) {
						var new_value = jQuery("#taxonomy_server").val();
						if ( '<?php echo self::GetServerUrl(); ?>' != new_value){ 
							var conf = confirm("<?php _e('ATENÇÃO: Você modificou o endereço do servidor de sincronização de taxonomias. Revise o endereço digitado e clique em OK se estiver correto. \n \n Endereço digitado: \n ', 'iasd');?>" + new_value + "\n \n ");
							if(!conf)
								event.preventDefault();
						}
					});
					</script>
				</div>
			</div><!-- /post-body -->
		</div>
	</form>
</div>
<?php
	}

	public static function SetStatusDate($type) {
		self::GetStatusDate($type);
		$option = 'xtt_tax_'.$type;

		update_option($option, time());
	}

	public static function GetStatusDate($type) {
		$option = 'xtt_tax_'.$type;
		$last = get_option($option);
		if(!$last)
			add_option($option, '', '', 'no' );

		return $last;
	}

	public static function GetNotifyDate() {
		return self::GetStatusDate('notify');
	}

	public static function GetRequestDate() {
		return self::GetStatusDate('request');
	}

	public static function GetSyncDate() {
		return self::GetStatusDate('sync');
	}

	public static function IsGoodRequestDate() {
		$request_date = self::GetRequestDate();
		$notify_date = self::GetNotifyDate();

		return ($request_date >= $notify_date);
	}

	public static function IsGoodSyncDate() {
		$request_date = self::GetRequestDate();
		$sync_date = self::GetSyncDate();

		return ($sync_date >= $request_date);
	}


	public static function RegisterTaxonomies() {
		// self::Register_Projetos();
		// self::Register_Departamentos();
		// self::Register_Editorias();
		// self::Register_Regiao();
		// self::Register_Colecoes();
		// self::Register_Midia();
		// self::Register_Material();
		// self::Register_Destaque();
		// self::Register_Evento();
		// self::Register_Owner();
		// self::Register_Secao();
		// self::Register_Sedes();
		// self::Register_Category();


		if(!wp_get_schedule( self::UPDATE_ACTION, array('useless' => 3600) ) )
			wp_schedule_event(time(), 'hourly', self::UPDATE_ACTION, array('useless' => 3600) );
	}

	public static function PushNotifyUpdate() {
		self::UpdateInformation();

		self::SetStatusDate('notify');

		$scheduled = -1;

		if(!wp_get_schedule( self::UPDATE_ACTION ) )
			$scheduled = wp_schedule_single_event( time() + (60), self::UPDATE_ACTION );

		if($scheduled === null) {
			header('HTTP/1.0 200 '.self::NOTIFICATION_RECEIVED);
			echo '<h1>HTTP/1.0 200 '. self::NOTIFICATION_RECEIVED .' </h1>';
		}

		die;
	}


	public static function TaxonomyPermissions() {
		return array('manage_terms' => 'activate_plugins', 'edit_terms' => 'activate_plugins', 'delete_terms' => 'activate_plugins');
	}

/**
	SEDES REGIONAIS
*/

	const TAXONOMY_SEDES_REGIONAIS = 'xtt-pa-sedes';
	const ACTION_SEDES_REGIONAIS = 'post-types-for-sedes_regionais';
	static function Register_Sedes() {
		$labels = array(
			'name'                => __( 'Sedes Regionais', 'iasd'),
			'singular_name'       => __( 'Sede Regional', 'iasd'),
			'search_items'        => __( 'Buscar Sede', 'iasd'),
			'all_items'           => __( 'Todas Sedes', 'iasd'),
			'parent_item'         => __( 'Sede Adminitrativa', 'iasd'),
			'parent_item_colon'   => __( 'Sede Adminitrativa', 'iasd'),
			'edit_item'           => __( 'Editar Sede', 'iasd' ),
			'update_item'         => __( 'Atualizar Sede', 'iasd'),
			'add_new_item'        => __( 'Adicionar Nova Sede', 'iasd'),
			'new_item_name'       => __( 'Nome da Sede', 'iasd'),
			'menu_name'           => __( 'Sedes Regionais', 'iasd')
		);

		$args = array(
			'hierarchical'        => true,
			'labels'              => $labels,
			'show_ui'             => true,
			'show_admin_column'   => true,
			'show_in_rest'        => true,
			'query_var'           => true,
			'rewrite'             => true,
			'public'              => true,
			'capabilities'        => self::TaxonomyPermissions()
		);

		$post_types = apply_filters(self::ACTION_SEDES_REGIONAIS, self::GetTaxonomyMap(self::TAXONOMY_SEDES_REGIONAIS));

		register_taxonomy( self::TAXONOMY_SEDES_REGIONAIS, $post_types, $args );

		TaxonomyImageController::AddToTaxonomy(self::TAXONOMY_SEDES_REGIONAIS);
	}

	static function TratamentoSedes($params) {
		global $wp, $wp_query;

		if($params) {
			parse_str($params, $matched);
			if(count($matched) && isset($matched['name'])&& $name = $matched['name']) {
				$term = get_term_by('slug', $name, IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS);
				if(is_object($term)) {
					$matched[$term->taxonomy] = $term->slug;
					unset($matched['name']);
					$params = http_build_query($matched);
				}
			}
		}

		return $params;
	}

    static function TratamentoPermalinks($termlink, $term = false, $taxonomy = false) {
		if($taxonomy == IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS) {
			$termlink = str_replace(IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS, '', $termlink);
			$termlink = str_replace('//', '/', $termlink);

		}
		return $termlink;
	}

/**
	SEDES PROPRIETARIAS
*/
	const TAXONOMY_OWNER= 'xtt-pa-owner';
	const ACTION_OWNER = 'post-types-for-owner';
	static function Register_Owner() {
		$labels = array(
			'name'                => __( 'Sedes Proprietárias', 'iasd'),
			'singular_name'       => __( 'Sede Proprietária', 'iasd'),
			'search_items'        => __( 'Buscar Sede', 'iasd'),
			'all_items'           => __( 'Todas Sedes', 'iasd'),
			'parent_item'         => __( 'Sede', 'iasd'),
			'parent_item_colon'   => __( 'Sede', 'iasd'),
			'edit_item'           => __( 'Editar Sede', 'iasd' ),
			'update_item'         => __( 'Atualizar Sede', 'iasd'),
			'add_new_item'        => __( 'Adicionar Nova Sede', 'iasd'),
			'new_item_name'       => __( 'Nome do Sede', 'iasd'),
			'menu_name'           => __( 'Sede Proprietária', 'iasd')
		);

		$args = array(
			'hierarchical'        => true,
			'labels'              => $labels,
			'show_ui'             => true,
			'show_admin_column'   => false,
			'query_var'           => true,
			'show_in_rest'        => true,
			'rewrite'             => array( 'slug' => __('proprietario', 'iasd') ),
			'public'              => false,
			'capabilities'        => self::TaxonomyPermissions()
		);

		$post_types = apply_filters(self::ACTION_OWNER, self::GetTaxonomyMap(self::TAXONOMY_OWNER));

		register_taxonomy( self::TAXONOMY_OWNER, $post_types, $args );
	}

/**
	Projetos
*/

	const TAXONOMY_PROJETOS = 'xtt-pa-projetos';
	const ACTION_PROJETOS = 'post-types-for-projetos_departamentos';
    static function Register_Projetos() {
		$labels = array(
			'name'                => __( 'Projetos', 'iasd'),
			'singular_name'       => __( 'Projeto', 'iasd'),
			'search_items'        => __( 'Buscar Projeto', 'iasd'),
			'all_items'           => __( 'Todos os Projetos', 'iasd'),
			'parent_item'         => __( 'Projeto Pai', 'iasd'),
			'parent_item_colon'   => __( 'Projeto Pai', 'iasd'),
			'edit_item'           => __( 'Editar Projeto', 'iasd' ),
			'update_item'         => __( 'Atualizar Projeto', 'iasd'),
			'add_new_item'        => __( 'Adicionar Novo Projeto', 'iasd'),
			'new_item_name'       => __( 'Nome do Projeto', 'iasd'),
			'menu_name'           => __( 'Projetos', 'iasd')
		);

		$args = array(
			'hierarchical'        => true,
			'labels'              => $labels,
			'show_ui'             => true,
			'show_admin_column'   => false,
			'show_in_rest'        => true,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => __('projeto', 'iasd') ),
			'public'              => true,
			'capabilities'        => self::TaxonomyPermissions()
		);

		$post_types = apply_filters(self::ACTION_PROJETOS, self::GetTaxonomyMap(self::TAXONOMY_PROJETOS) );

		register_taxonomy( self::TAXONOMY_PROJETOS, $post_types, $args );

		TaxonomyImageController::AddToTaxonomy(self::TAXONOMY_PROJETOS);
	}
/**
	Departamentos
*/

	const TAXONOMY_DEPARTAMENTOS = 'xtt-pa-departamentos';
	const ACTION_DEPARTAMENTOS = 'post-types-for-departamentos';
	static function Register_Departamentos() {
		$labels = array(
			'name'                => __( 'Departamentos', 'iasd'),
			'singular_name'       => __( 'Departamento', 'iasd'),
			'search_items'        => __( 'Buscar Departamento', 'iasd'),
			'all_items'           => __( 'Todos os Departamento', 'iasd'),
			'parent_item'         => __( 'Departamento Superior', 'iasd'),
			'parent_item_colon'   => __( 'Departamento Superior', 'iasd'),
			'edit_item'           => __( 'Editar Departamento', 'iasd' ),
			'update_item'         => __( 'Atualizar Departamento', 'iasd'),
			'add_new_item'        => __( 'Adicionar Novo Departamento', 'iasd'),
			'new_item_name'       => __( 'Nome do Departamento', 'iasd'),
			'menu_name'           => __( 'Departamentos', 'iasd')
		);

		$args = array(
			'hierarchical'        => true,
			'labels'              => $labels,
			'show_ui'             => true,
			'show_admin_column'   => false,
			'show_in_rest'        => true,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => __('departamento', 'iasd') ),
			'public'              => true,
			'capabilities'        => self::TaxonomyPermissions()
		);

		$post_types = apply_filters(self::ACTION_DEPARTAMENTOS, self::GetTaxonomyMap(self::TAXONOMY_DEPARTAMENTOS));

		register_taxonomy( self::TAXONOMY_DEPARTAMENTOS, $post_types, $args );

		TaxonomyImageController::AddToTaxonomy(self::TAXONOMY_DEPARTAMENTOS);
	}
/**
	Editorias
*/

	const TAXONOMY_EDITORIAS = 'xtt-pa-editorias';
	const ACTION_EDITORIAS = 'post-types-for-editorias';
	static function Register_Editorias() {
		$labels = array(
			'name'                => __( 'Editorias', 'iasd'),
			'singular_name'       => __( 'Editoria', 'iasd'),
			'search_items'        => __( 'Buscar Editoria', 'iasd'),
			'all_items'           => __( 'Todas as Editorias', 'iasd'),
			'parent_item'         => __( 'Editoria Superior', 'iasd'),
			'parent_item_colon'   => __( 'Editoria Superior', 'iasd'),
			'edit_item'           => __( 'Editar Editoria', 'iasd' ),
			'update_item'         => __( 'Atualizar Editoria', 'iasd'),
			'add_new_item'        => __( 'Adicionar Nova Editoria', 'iasd'),
			'new_item_name'       => __( 'Nome da Editoria', 'iasd'),
			'menu_name'           => __( 'Editorias', 'iasd')
		);

		$args = array(
			'hierarchical'        => true,
			'labels'              => $labels,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'show_admin_column'   => true,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => __('editoria', 'iasd') ),
			'public'              => true,
			'capabilities'        => self::TaxonomyPermissions()
		);

		$post_types = apply_filters(self::ACTION_EDITORIAS, self::GetTaxonomyMap(self::TAXONOMY_EDITORIAS));

		register_taxonomy( self::TAXONOMY_EDITORIAS, $post_types, $args );

		TaxonomyImageController::AddToTaxonomy(self::TAXONOMY_EDITORIAS);
	}
/**
	Seção
*/

	const TAXONOMY_SECAO = 'xtt-pa-secao';
	const ACTION_SECAO = 'post-types-for-secao';
	static function Register_Secao() {
		$labels = array(
			'name'                => __( 'Seções', 'iasd'),
			'singular_name'       => __( 'Seção', 'iasd'),
			'search_items'        => __( 'Buscar Seção', 'iasd'),
			'all_items'           => __( 'Todas Seções', 'iasd'),
			'parent_item'         => __( 'Seção', 'iasd'),
			'parent_item_colon'   => __( 'Seção', 'iasd'),
			'edit_item'           => __( 'Editar Seção', 'iasd' ),
			'update_item'         => __( 'Atualizar Seção', 'iasd'),
			'add_new_item'        => __( 'Adicionar Seção', 'iasd'),
			'new_item_name'       => __( 'Nome da Seção', 'iasd'),
			'menu_name'           => __( 'Seção', 'iasd')
		);

		$args = array(
			'hierarchical'        => false,
			'labels'              => $labels,
			'show_ui'             => false,
			'show_admin_column'   => false,
			'query_var'           => false,
			'rewrite'             => array( 'slug' => __('secao', 'iasd')),
			'public'              => false,
			'capabilities'        => self::TaxonomyPermissions()
		);

		$post_types = apply_filters(self::ACTION_SECAO, self::GetTaxonomyMap(self::TAXONOMY_SECAO));

		register_taxonomy( self::TAXONOMY_SECAO, $post_types, $args );
	}
/**
	Região
*/

	const TAXONOMY_REGIAO = 'xtt-pa-regiao';
	const ACTION_REGIAO = 'post-types-for-regiao';
	static function Register_Regiao() {
		$labels = array(
			'name'                => __( 'Regiões', 'iasd'),
			'singular_name'       => __( 'Região', 'iasd'),
			'search_items'        => __( 'Buscar Região', 'iasd'),
			'all_items'           => __( 'Todas as Regiões', 'iasd'),
			'parent_item'         => __( 'Região Superior', 'iasd'),
			'parent_item_colon'   => __( 'Região Superior', 'iasd'),
			'edit_item'           => __( 'Editar Sede', 'iasd' ),
			'update_item'         => __( 'Atualizar Região', 'iasd'),
			'add_new_item'        => __( 'Adicionar Nova Região', 'iasd'),
			'new_item_name'       => __( 'Nome da Região', 'iasd'),
			'menu_name'           => __( 'Regiões', 'iasd')
		);

		$args = array(
			'hierarchical'        => true,
			'labels'              => $labels,
			'show_ui'             => true,
			'show_admin_column'   => false,
			'show_in_rest'        => true,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => __('regiao', 'iasd') ),
			'public'              => true,
			'capabilities'        => self::TaxonomyPermissions()
		);

		$post_types = apply_filters(self::ACTION_REGIAO, self::GetTaxonomyMap(self::TAXONOMY_REGIAO));

		register_taxonomy( self::TAXONOMY_REGIAO, $post_types, $args );

		TaxonomyImageController::AddToTaxonomy(self::TAXONOMY_REGIAO);
	}
/**
	Coleções
*/

	const TAXONOMY_COLECOES = 'xtt-pa-colecoes';
	const ACTION_COLECOES = 'post-types-for-colecoes';
	static function Register_Colecoes() {
		$labels = array(
			'name'                => __( 'Coleções', 'iasd'),
			'singular_name'       => __( 'Coleção', 'iasd'),
			'search_items'        => __( 'Buscar Coleção', 'iasd'),
			'all_items'           => __( 'Todas as Coleções', 'iasd'),
			'parent_item'         => __( 'Coleção Superior', 'iasd'),
			'parent_item_colon'   => __( 'Coleção Superior', 'iasd'),
			'edit_item'           => __( 'Editar Coleção', 'iasd' ),
			'update_item'         => __( 'Atualizar Coleção', 'iasd'),
			'add_new_item'        => __( 'Adicionar Nova Coleção', 'iasd'),
			'new_item_name'       => __( 'Nome da Coleção', 'iasd'),
			'menu_name'           => __( 'Coleções', 'iasd')
		);

		$args = array(
			'hierarchical'        => true,
			'labels'              => $labels,
			'show_ui'             => true,
			'show_admin_column'   => true,
			'show_in_rest'        => true,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => __('colecao', 'iasd') ),
			'public'              => true,
			'capabilities'        => self::TaxonomyPermissions()
		);

		$post_types = apply_filters(self::ACTION_COLECOES, self::GetTaxonomyMap(self::TAXONOMY_COLECOES));

		register_taxonomy( self::TAXONOMY_COLECOES, $post_types, $args );

		TaxonomyImageController::AddToTaxonomy(self::TAXONOMY_COLECOES);
	}
/**
	Tipo de Midia
*/

	const TAXONOMY_TIPO_MIDIA = 'xtt-pa-midias';
	const ACTION_TIPO_MIDIA = 'post-types-for-midias';
	static function Register_Midia() {
		$labels = array(
			'name'                => __( 'Tipos de Midias', 'iasd'),
			'singular_name'       => __( 'Tipo de Midia', 'iasd'),
			'search_items'        => __( 'Buscar Tipo de Midia', 'iasd'),
			'all_items'           => __( 'Todas os Tipos de Midias', 'iasd'),
			'parent_item'         => __( 'Tipo Superior', 'iasd'),
			'parent_item_colon'   => __( 'Tipo Superior:', 'iasd'),
			'edit_item'           => __( 'Editar Tipo de Midia', 'iasd' ),
			'update_item'         => __( 'Atualizar Tipo de Midia', 'iasd'),
			'add_new_item'        => __( 'Adicionar Novo Tipo de Midia', 'iasd'),
			'new_item_name'       => __( 'Nome do Tipo de Midia', 'iasd'),
			'menu_name'           => __( 'Tipos de Midias', 'iasd')
		);

		$args = array(
			'hierarchical'        => true,
			'labels'              => $labels,
			'show_ui'             => true,
			'show_admin_column'   => false,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => __('tipo-midia', 'iasd') ),
			'public'              => false,
			'capabilities'        => self::TaxonomyPermissions()
		);

		$post_types = apply_filters(self::ACTION_TIPO_MIDIA, self::GetTaxonomyMap(self::TAXONOMY_TIPO_MIDIA));

		register_taxonomy( self::TAXONOMY_TIPO_MIDIA, $post_types, $args );

		TaxonomyImageController::AddToTaxonomy(self::TAXONOMY_TIPO_MIDIA);
	}
/**
	Tipo de Material
*/

	const TAXONOMY_TIPO_MATERIAL = 'xtt-pa-materiais';
	const ACTION_TIPO_MATERIAL = 'post-types-for-materiais';
	static function Register_Material() {
		$labels = array(
			'name'                => __( 'Tipos de Materiais', 'iasd'),
			'singular_name'       => __( 'Tipo de Material', 'iasd'),
			'search_items'        => __( 'Buscar Tipo de Material', 'iasd'),
			'all_items'           => __( 'Todos os Tipos de Materiais', 'iasd'),
			'parent_item'         => __( 'Tipo Superior', 'iasd'),
			'parent_item_colon'   => __( 'Tipo Superior:', 'iasd'),
			'edit_item'           => __( 'Editar Tipo de Material', 'iasd' ),
			'update_item'         => __( 'Atualizar Tipo de Material', 'iasd'),
			'add_new_item'        => __( 'Adicionar Novo Tipo de Material', 'iasd'),
			'new_item_name'       => __( 'Nome do Tipo de Material', 'iasd'),
			'menu_name'           => __( 'Tipos de Materiais', 'iasd')
		);

		$args = array(
			'hierarchical'        => true,
			'labels'              => $labels,
			'show_ui'             => true,
			'show_admin_column'   => false,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => __('tipo-material', 'iasd') ),
			'public'              => true,
			'capabilities'        => self::TaxonomyPermissions()
		);

		$post_types = apply_filters(self::ACTION_TIPO_MATERIAL, self::GetTaxonomyMap(self::TAXONOMY_TIPO_MATERIAL));

		register_taxonomy( self::TAXONOMY_TIPO_MATERIAL, $post_types, $args );

		TaxonomyImageController::AddToTaxonomy(self::TAXONOMY_TIPO_MATERIAL);
	}

/**
	Tipo de Evento
*/

	const TAXONOMY_EVENTOS = 'xtt-pa-eventos';
	const ACTION_EVENTOS = 'post-types-for-eventos';
	static function Register_Evento() {
		$labels = array(
			'name'                => __( 'Tipos de Eventos', 'iasd'),
			'singular_name'       => __( 'Tipo de Evento', 'iasd'),
			'search_items'        => __( 'Buscar Tipo de Evento', 'iasd'),
			'all_items'           => __( 'Todos os Tipos de Eventos', 'iasd'),
			'parent_item'         => __( 'Tipo Superior', 'iasd'),
			'parent_item_colon'   => __( 'Tipo Superior:', 'iasd'),
			'edit_item'           => __( 'Editar Tipo de Evento', 'iasd' ),
			'update_item'         => __( 'Atualizar Tipo de Evento', 'iasd'),
			'add_new_item'        => __( 'Adicionar Novo Tipo de Evento', 'iasd'),
			'new_item_name'       => __( 'Nome do Tipo de Evento', 'iasd'),
			'menu_name'           => __( 'Tipos de Eventos', 'iasd')
		);

		$args = array(
			'hierarchical'        => true,
			'labels'              => $labels,
			'show_ui'             => true,
			'show_admin_column'   => true,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => __('tipo-evento', 'iasd') ),
			'public'              => true,
			'capabilities'        => self::TaxonomyPermissions()
		);

		$map = self::GetTaxonomyMap(self::TAXONOMY_EVENTOS);
		$post_types = apply_filters(self::ACTION_EVENTOS, $map);

		register_taxonomy( self::TAXONOMY_EVENTOS, $post_types, $args );

		TaxonomyImageController::AddToTaxonomy(self::TAXONOMY_EVENTOS);
	}

/**
	Níveis de Destaque
*/

	const TAXONOMY_DESTAQUE = 'xtt-pa-destaque';
	const ACTION_DESTAQUE = 'post-types-for-destaque';
	static function Register_Destaque() {
		$labels = array(
			'name'                => __( 'Níveis de Destaque', 'iasd'),
			'singular_name'       => __( 'Nível de Destaque', 'iasd'),
			'search_items'        => __( 'Buscar níveis', 'iasd'),
			'all_items'           => __( 'Todos os níveis', 'iasd'),
			'parent_item'         => __( 'Grupo', 'iasd'),
			'parent_item_colon'   => __( 'Grupo:', 'iasd'),
			'edit_item'           => __( 'Editar nível de destaque', 'iasd' ),
			'update_item'         => __( 'Atualizar nível', 'iasd'),
			'add_new_item'        => __( 'Adicionar novo Nível de Destaque', 'iasd'),
			'new_item_name'       => __( 'Nome do nível', 'iasd'),
			'menu_name'           => __( 'Níveis de Destaque', 'iasd')
		);

		$args = array(
			'hierarchical'        => true,
			'labels'              => $labels,
			'show_ui'             => true,
			'show_admin_column'   => true,
			'query_var'           => false,
			'public'              => false,
			'capabilities'        => self::TaxonomyPermissions()
		);

		$post_types = apply_filters(self::ACTION_DESTAQUE, self::GetTaxonomyMap(self::TAXONOMY_DESTAQUE));

		register_taxonomy( self::TAXONOMY_DESTAQUE, $post_types, $args );
	}

/**
	Category
*/

	const TAXONOMY_CATEGORY = 'category';
	const ACTION_CATEGORY = 'post-types-for-category';



	static function Register_Category() {
		$post_types = self::GetTaxonomyMap(self::TAXONOMY_CATEGORY);
		if( ! is_array($post_types) ) return;
		foreach ($post_types as $post_type){
			register_taxonomy_for_object_type('category', $post_type);  
	 	}
	}

	


}

function get_editoria($post_id = null, $taxonomy = IASD_Taxonomias::TAXONOMY_EDITORIAS) {
	if(!$post_id)
		$post_id = get_the_ID();
	if(!is_int($post_id) && is_object($post_id))
		$post_id = $post_id->ID;

	$terms = wp_get_post_terms($post_id, $taxonomy, array('orderby' => 'slug'));

	if(count($terms)) {
		return reset($terms);
	}
	return '';
}

class PATaxonomias extends IASD_Taxonomias {

}

function cmp($a, $b) {
    return strcmp($a->parent, $b->parent);
}


