<?php

add_filter('iasd-header-inner-menu', array('IasdNavEntreCampos', 'callRenderFunc'));

class IasdNavEntreCampos {

	const UPDATE_ACTION = 'nav-entre-campos-cron-action';

	public static function Init() {
		add_action( 'refresh_blog_details', array(__CLASS__, 'UpdateTaxSiteList'), 100, 2 );
		//add_action( 'refresh_blog_details', array(__CLASS__, 'TriggerMultiSiteUpdate'), 100, 2 );
		add_action( 'IASD_Taxonomias::UpdateFinish', array(__CLASS__, 'TriggerMultiSiteUpdate') );

		add_action( 'wp_ajax_TriggerMultiSiteUpdate', array(__CLASS__, 'TriggerMultiSiteUpdate') );
		add_action( 'wp_ajax_nopriv_TriggerMultiSiteUpdate', array(__CLASS__, 'TriggerMultiSiteUpdate') );

		# Cron
		add_filter(self::UPDATE_ACTION, array(__CLASS__, 'TriggerMultiSiteUpdate') );

		if(!wp_get_schedule( self::UPDATE_ACTION ) )
			wp_schedule_event(time(), 'daily', self::UPDATE_ACTION );

		$tax_url = self::GetServerUrl();
		$main_url = str_replace('tax.', '', $tax_url);
		update_option('pa-multisite-main-url', $main_url );

		# deploy trigger
		$current_deploy_id = get_option('pa-multisite-setup-deploy-id');
		$new_deploy_id = 201310311138; //Atualizar quando quiser forçar
		if($new_deploy_id > $current_deploy_id){
			update_option('pa-multisite-setup-deploy-id', $new_deploy_id);
			self::TriggerMultiSiteUpdate();
		}

	}

	public static function CallRenderFunc(){
		return array('IasdNavEntreCampos','RenderMultiSiteMenu');
	}

	public static function GetServerUrl() {
		$url = get_site_option('xtt_tax_server_url');
		if(empty($url)){
			if(function_exists('get_blog_option')) {
				$url = get_blog_option( 1, 'xtt_tax_server_url' );
			} else {
				$url = get_option('xtt_tax_server_url');
			}
		}

		return $url;
	}

	public static function ShowUpdateTaxSiteListErrorMessage(){
		echo '<div class="error">Não foi possível sincronizar lista atualizada de sites com o portal Adventista. Faça a sincronização manualmente.</div>';
	}

	public static function GetCurrentSiteDomain() {
		if(function_exists('get_current_site')) {
			$current_site = get_current_site();
			return $current_site->domain;
		} else {
			return parse_url(site_url(), PHP_URL_HOST);
		}
	}

	public static function UpdateTaxSiteList($blog_id) {
		global $http, $wpdb;
		if (empty($http)) $http = new WP_Http();
		$server_url = self::GetServerUrl();

		$blogs = $wpdb->get_results("SELECT blog_id, domain, path FROM $wpdb->blogs  WHERE public = '1' AND archived = '0' AND deleted = '0' ORDER BY blog_id DESC", ARRAY_A );
		$blogs_arr = array();

		foreach ($blogs as $blog) {
			$blog['path'] = str_replace('/pt', '', $blog['path']);
			if($blog['path']=='/'){
				$blog['path'] = 'portal_home';
			}
			$blogs_arr[] = 'sites[]='.$blog['path'];
		}

		if(isset($_GET['debug'])){
			echo "<pre>"; var_dump($blogs_arr);
		}

		$site_name = get_site_option('site_name');

		$blog_domain = self::GetCurrentSiteDomain();

		$host = explode('.', $blog_domain);
		$subdomain = $host[0];

		$params = array('method' => 'POST', 'body' => 'install='.$subdomain.'&'.implode('&', $blogs_arr), 'timeout'=>30);

		$result = $http->request($server_url . 'wp-admin/admin-ajax.php?action=updateMultiSiteList', $params);

		if (is_wp_error($result)) {
			add_action('admin_notices', array(__CLASS__, 'ShowUpdateTaxSiteListErrorMessage'));
		}
	}

	public static function TriggerMultiSiteUpdate(){
		$server_url = self::GetServerUrl();

		#current blog details

		if( isset($_GET['rendermenu'])){		
			$menuraw = get_option('pa-multisite-menu');
			echo "$menuraw <br>";
			self::RenderMultiSiteMenu();
			die;
		}


		$blog_info = parse_url(get_site_url());
		if(!isset($blog_info['path']))
			$blog_info['path'] = '/';
	
		preg_match("/^\/(?:pt|es)?\/?([^\/]+)?\/?/", $blog_info['path'], $matches);
		$blog_path = (!empty($matches[1])) ? $matches[1] : 'portal_home';

		$install_url = $blog_info['host'];

		// Call tax webservice
		global $http;
		if (empty($http)) $http = new WP_Http();

		$params = array('method' => 'POST', 'body' => 'slug='.$blog_path, 'timeout'=>30);
		

		// TESTES COMENTAR \/
		// $server_url = 'http://tax.adventistas.org/pt/';
		// $server_url = 'http://tax.adventistas.org/es/';


		$result = $http->request($server_url . 'wp-admin/admin-ajax.php?action=getMultiSiteList', $params);
		if (is_wp_error($result)) {
			add_action('admin_notices', array(__CLASS__, 'ShowUpdateTaxSiteListErrorMessage'));
		} else {
			update_option('pa-multisite-menu', $result['body'] );
			update_option('pa-multisite-menu-compilado', '');
		}

	}

	public static function RenderMultiSiteMenu(){
		
		if ($_GET['update'] == 'true'){
			self::TriggerMultiSiteUpdate();
		} else {
			$menucompilado = get_option('pa-multisite-menu-compilado');

			if($menucompilado)
				return $menucompilado;
		}

		# get menu json
		$menuraw = get_option('pa-multisite-menu');

		$menuraw = substr($menuraw, 0, -1);

		$menudata = json_decode($menuraw, true);

		$main_url = get_option('pa-multisite-main-url');

		$blog_info = parse_url(get_site_url());

		if(!isset($blog_info['path']))
			$blog_info['path'] = '/';

		preg_match("/^\/(?:pt|es)?\/?([^\/]+)?\/?/", $blog_info['path'], $matches);
		$blog_slug = (!empty($matches[1])) ? $matches[1] : '';

		$blog_domain = self::GetCurrentSiteDomain();

		$host = explode('.', $blog_domain);
		$subdomain = $host[0];
		$subdomain_up = strtoupper($subdomain);

		

		if ($_GET['nav'] == 'true'){
			echo '<script> jQuery( document ).ready(function() {
			jQuery( ".iasd-dropdown-navigation" ).show();
			}); </script>';

		}

		$ret = '

<div class="dropdown iasd-dropdown-navigation visible-desktop" style="display:none;">
	<button class="dropdown-toggle" data-toggle="dropdown" href="#">
		Navegue entre os campos
	</button>
	<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">
		<ul>
			<li class="heading"><h1>Uniões</h1></li>';
			
		# varre unioes
		$unioes = get_terms( 'xtt-pa-sedes', array( 'hide_empty' => 0 , 'parent' => 0 ) );
		foreach ($unioes as $u) {
			$filhos = get_terms( 'xtt-pa-sedes', array( 'hide_empty' => 0 , 'parent' => $u->term_id ) );

			$f_has_link = array();
			foreach($filhos as $f){
	
				if(isset($menudata[$f->slug]) && !empty($menudata[$f->slug])){
					$f_has_link[] = $f->slug;
				}
			}
			$link_u = false;

			if(isset($menudata[$u->slug]) && !empty($menudata[$u->slug])){
				$link_u = $menudata[$u->slug];
				$u_has_link = true;
			} else {
				$u_has_link = false;
			}
			if(!isset($menudata[$u->slug]))
				continue;
			$url_match = str_replace('http://', '', $menudata[$u->slug]);

			$blog_url_assembled = $blog_domain.'/'.$blog_slug;

			if($blog_url_assembled==$url_match){
				$classAtive_u = ' class="active"';
			} else {
				$classAtive_u = '';
				foreach($f_has_link as $h) {
					if(isset($h)) {

						$h_tail = str_replace('http://', '', $main_url);

						// $h_tail = str_replace('/pt', '', $h_tail);
						// $h_tail = str_replace('/es', '', $h_tail);
						// $h_tail = str_replace('/', '', $h_tail);	
						// $children_url_match = $h_tail.'/'.$blog_slug;
						
						$h_tail = str_replace('pt/', '', $h_tail);
						$h_tail = str_replace('es/', '', $h_tail);

                        $children_url_match = $h.'.'.$h_tail.$blog_slug;

						if($children_url_match == $blog_url_assembled) 
						{
							$classAtive_u = ' class="active"';
						}
					}
				}
			}

			if($u_has_link || $f_has_link){

				$upslug = strtoupper($u->slug);
				$title_u = "{$u->name} (".$upslug.")";
				//'.$link_u.'  -> Tirando o link para testes...
				$ret .= '
			<li>
				<a href="" title="'.$title_u.'"><h2>'.$title_u.'</h2></a>
				<ul'.$classAtive_u.'>
					<li class="heading"><h3>'.$title_u.'</h3></li>
					<li><a href="'.$link_u.'" title="Portal '.$upslug.'">Portal '.$upslug.'</a></li>';
				# varre associacoes
				if($f_has_link){
					foreach($filhos as $f){
						if(!in_array($f->slug, $f_has_link))
							continue;
						#get term details
						$term_data = get_option("xtt_cat_info_{$f->term_id}");
						$site_url = '';
						if(isset($term_data["site_url"])){
							$site_url = $term_data["site_url"];
						} else {
							$current_main_url = str_replace('http://', 'http://'.$f->slug.'.', $main_url);
							$site_url = $current_main_url.$blog_slug;
							// $current_main_url = $main_url;
							// $site_url = $current_main_url.'/'.$blog_slug;
						}

						$title_f = "{$f->name} (".strtoupper($f->slug).")";

						$url_match = str_replace('http://', '', $site_url);
						$url_match = str_replace('pt/', '', $url_match);
						$url_match = str_replace('es/', '', $url_match);


						$site_url = str_replace('pt/', '', $site_url);
						$site_url = str_replace('es/', '', $site_url);


						$blog_url_assembled = $blog_domain.'/'.$blog_slug;


						$is_active ='';	
						// $is_active = ($blog_url_assembled==$url_match) ? ' class="active"' : '';	
						if ($blog_url_assembled== $url_match){
							$is_active = ' class="active"';
						}

						$ret .= '
					<li'.$is_active.'><a href="'.$site_url.'" title="'.$title_f.'">'.$title_f.'</a></li>';
					}
				}

				$ret .= '
					<li class="back"><a href="#" title="Clique para ver todas as Uniões" class="back-link">Todas as Uniões</a></li>
				</ul>
			</li>';
			}
		}

		$ret .= '
			<li class="dsa-link"><a href="'.$main_url.'" title="Clique para ver o Portal Adventista" class="btn btn-default">Divisão Sul-Americana (DSA)</a></li>
		</ul>
	</div>
</div>';

		update_option('pa-multisite-menu-compilado', $ret);
		echo $ret;

	}

}

add_action('init', array('IasdNavEntreCampos', 'Init'));
