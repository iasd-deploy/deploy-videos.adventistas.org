<?php


define('PAPU_LDPW', PAPU_VIEW . '/iasd_listadeposts');


/**
	HOOKS
*/
add_action( 'init',                                     array('IASD_ListaDePosts', 'Init'), 0);
/**
 * On an early action hook, check if the hook is scheduled - if not, schedule it.
 */

add_filter( 'cron_schedules', 'cron_add' );

function cron_add( $schedules ) {  $schedules['five_minutes'] = array( 'interval' => 5 * 60, 'display' => 'Five Minutes' ); return $schedules; }

class IASD_ListaDePosts extends WP_Widget {
    static function Init() {
        if(apply_filters('enable_listadeposts', get_option('enable_listadeposts', false))) {
            add_action( 'widgets_init',                             array('IASD_ListaDePosts', 'RegisterWidget'));
            add_action( 'admin_head',                               array('IASD_ListaDePosts', 'EnqueueAdmin'));
            add_filter( 'local_post_types-postmeta',                array('IASD_ListaDePosts', 'OrderByOptions'), 0, 2);

            add_action( 'wp_ajax_iasd-listadeposts-refresh',        array('IASD_ListaDePosts', 'Refresh'), 10, 0);
            add_action( 'wp_ajax_nopriv_iasd-listadeposts-refresh', array('IASD_ListaDePosts', 'Refresh'), 10, 0);

            add_action( 'wp_ajax_iasd-listadeposts-check-contents', array('IASD_ListaDePosts', 'CheckContentsAjax'), 10, 0);

            add_action( 'sidebar_admin_page', 						array('IASD_ListaDePosts', 'CheckContentsFooterHtml'), 10, 0);

            add_action( 'save_post',                                array('IASD_ListaDePosts', 'CacheUpdateSavePost'), 99, 1);
            add_action( 'edit_user_profile_update',                 array('IASD_ListaDePosts', 'CacheUpdateLocal'), 99);

            add_action( 'iasd_ldp_reset_cache',                     array('IASD_ListaDePosts', 'CacheUpdate'), 99);

            // Actions para abrir modal de edição de lista de post no contexto do site
            add_action( 'wp_footer', 								array('IASD_ListaDePosts', 'addDummyContent'), 1000);
            add_action( 'wp_ajax_nopriv_get_search_content', 		array('IASD_ListaDePosts', 'getSearchContent'), 10, 0);
            add_action( 'wp_ajax_get_search_content', 				array('IASD_ListaDePosts', 'getSearchContent'), 10, 0);

            IASD_ListaDePosts::SetupCron();
        }

        // Metodos para consumir conteudo via chamada POST
        add_action( 'wp_ajax_nopriv_post_json_content', 		array('IASD_ListaDePosts', 'getJsonContentPOST'), 10, 0);
        add_action( 'wp_ajax_post_json_content', 				array('IASD_ListaDePosts', 'getJsonContentPOST'), 10, 0);

        // Metodos para consumir conteudo via chamada GET. Retorna x primeiras noticias passando o parametro 'limit'.
    	add_action( 'wp_ajax_nopriv_get_json_content', 			array('IASD_ListaDePosts', 'getJsonContentGET'), 10, 0);
        add_action( 'wp_ajax_get_json_content', 				array('IASD_ListaDePosts', 'getJsonContentGET'), 10, 0);

    }
/**
		ATRIBUTES
*/
	const base_id   = 'iasd_ldp';
	const option_id = 'widget_iasd_ldp';
	const form_id   = 'widget-iasd_ldp';
	const custom_id = 'custom-iasd_ldp';

	private $instance = null;
	private $widget_args = null;
	private $view_config = null;
	private $availableSources = null;
	private $availablePostTypes = null;
	private $availableViews = null;

	static $rules = array();

	static function OrderByOptions() {
		return array(
			'date' 	=> __('Data', 'iasd'),
			'title' => __('Título', 'iasd'),
			'rand' =>  __('Aleatório', 'iasd'),
		);
	}


/**
		Constructor and Admin
*/

	function __construct() {
		add_action( "update_option_" . IASD_ListaDePosts::option_id, array(__CLASS__, 'AfterUpdate'), 10, 2);
		// Instantiate the parent object
		$widget_ops = array('description' => __( 'Widget de listagem de posts da IASD. Incluindo filtros avançados e consulta à sites externos compativeis.' , 'iasd') );
		parent::__construct(self::base_id, __('IASD: Lista de Posts', 'iasd', $widget_ops));
	}

	static function RegisterWidget() {
		register_widget(__CLASS__);
	}
	static function UnregisterWidget() {
		unregister_widget(__CLASS__);
	}
	static function EnqueueAdmin() {

		echo "<script type='text/javascript'>var iasd_rules_action = '".IASD_ListaDePosts_Ajax::RULES."';</script>\n";

		wp_enqueue_style('iasd-ldp-admin', PAPURL_STTC . '/css/iasdlistadeposts_admin.css');

		wp_enqueue_script('iasd-ldp-admin', PAPURL_STTC . '/js/iasdlistadeposts_admin.js', array('jquery'));
		wp_enqueue_script('jquery-validate', PAPURL_STTC . '/js/jquery.validate/jquery.validate.js', array('jquery'));
		wp_enqueue_script('jquery-validate-additional-methods', PAPURL_STTC . '/js/jquery.validate/additional-methods.js', array('jquery', 'jquery-validate'));
		wp_enqueue_script('jquery-validate-l10n', PAPURL_STTC . '/js/jquery.validate/localization/messages_'.WPLANG.'.js', array('jquery', 'jquery-validate'));

		add_thickbox();

	}

	static function SetupCron() {
		if (!wp_next_scheduled( 'iasd_ldp_reset_cache' ) )
			wp_schedule_event( time(), 'five_minutes', 'iasd_ldp_reset_cache');
	}

	static function GenerateSecret() {
		return self::base_id . '_' . sha1(md5(rand()));
	}

/**
		STATIC - SOURCES
*/

	static function BasicSources() {
		$language_code = strtolower((defined('WPLANG')) ? substr(WPLANG, 0, 2) : 'pt');
		if($language_code != 'es')
			$language_code = 'pt';

		$language_code .= '/';

		$site_url = site_url('/', 'https');

		$sources = array();
		$sources['local']     = array('name' => get_bloginfo('name') . ' (Local)',			'url' => $site_url);
/*		if($site_url != 'https://noticias.adventistas.org/'.$language_code)
			$sources['asn']       = array('name' => __('Portal ASN', 'iasd'),		'url' => 'https://noticias.adventistas.org/'.$language_code);

		if($site_url != 'https://videos.adventistas.org/'.$language_code)
			$sources['videos']    = array('name' => __('Portal Videos', 'iasd'),		'url' => 'https://videos.adventistas.org/'.$language_code);

		if($site_url != 'https://downloads.adventistas.org/'.$language_code)
			$sources['downloads'] = array('name' => __('Portal Materiais', 'iasd'), 	'url' => 'https://downloads.adventistas.org/'.$language_code);

		if($site_url != 'https://eventos.adventistas.org/'.$language_code)
			$sources['eventos']   = array('name' => __('Portal Eventos', 'iasd'), 		'url' => 'https://eventos.adventistas.org/'.$language_code);*/

        $tax_server = get_option('xtt_tax_server_url');

        $server_url = str_replace('tax', 'noticias', $tax_server);
		if($server_url != $site_url)
            $sources['asn']       = array('name' => __('Portal ASN', 'iasd'),			'url' => $server_url);

        $server_url = str_replace('tax', 'videos', $tax_server);
        if($server_url != $site_url)
			$sources['videos']    = array('name' => __('Portal Videos', 'iasd'),		'url' => $server_url);

        $server_url = str_replace('tax', 'downloads', $tax_server);
        if($server_url != $site_url)
			$sources['downloads'] = array('name' => __('Portal Materiais', 'iasd'), 	'url' => $server_url);

        $server_url = str_replace('tax', 'eventos', $tax_server);
        if($server_url != $site_url)
			$sources['eventos']   = array('name' => __('Portal Eventos', 'iasd'), 		'url' => $server_url);

		$sources['outra']         = array('name' => __('Outra', 'iasd'), 		'url' => '');

		return $sources;
	}

	static function OtherSources() {
		$sources = array();
		$instances = get_option(self::option_id, array());
		foreach($instances as $number => $instance) {
			if(isset($instance['source_id']) && $instance['source_id'] == 'outra') {
				$sources[self::base_id . '_' . $number] = array('url' => $instance['source_extra']);
			}
		}
		return $sources;
	}

	static function LoadRules($ignoreCache = false) {
		if(!self::$rules || $ignoreCache) {
			self::$rules = IASD_ListaDePosts_Ajax::Rules(false, $ignoreCache);
		}

		return self::$rules;
	}

	static function LoadSourceRules($source_id = 'local', $ignoreCache = false) {
		$rules = self::LoadRules($ignoreCache);

		if($rules && isset($rules['sources'][$source_id]))
			return $rules['sources'][$source_id];

		return null;
	}
	static function LoadPostTypeRules($post_type = 'post', $source_id = 'local', $ignoreCache = false) {
		$source = self::LoadSourceRules($source_id, $ignoreCache);

		if($source && isset($source['post_type'][$post_type]))
			return $source['post_type'][$post_type];

		return null;
	}
	static function AfterUpdate($old_instances, $new_instances) {
		unset($old_instances['_multiwidget']);
		unset($new_instances['_multiwidget']);
		$old_instances_numbers = array_keys($old_instances);
		$new_instances_numbers = array_keys($new_instances);

		//Remove caches de antigos
		foreach($old_instances_numbers as $number)
			if(!in_array($number, $new_instances_numbers))
				delete_option('kche_' . self::base_id . '_' . $number);

		$revalidateSources = false;
		foreach($new_instances as $number => $instance) {
			if($instance['source_id'] == 'outra') {
				if(!isset($old_instances[$number])) {
					$revalidateSources = true;
				} else if($old_instances[$number]['source_id'] != 'outra') {
					$revalidateSources = true;
				} else if($old_instances[$number]['source_extra'] != $instance['source_extra']) {
					$revalidateSources = true;
				}
			}
		}

		self::LoadRules($revalidateSources);
	}


/**
		STATIC - CONTENT CHECK & REFRESH
*/

	static function CheckContentsFooterHtml() {
?>
		<div id="iasd-ldp-cc-container-dummy" style="display:none;">
			<div class="iasd-ldp-cc">
				<div class="iasd-ldp-cc-spinner spinner"></div>
				<div class="iasd-ldp-cc-container">
					<div class="iasd-ldp-cc-container-list"></div>
					<div class="iasd-ldp-cc-container-save">
<!-- 						<div class="alignright">
							<a href="javascript:void(0);" class="button iasd-widget iasd-ldp-cc-refresh"><?php _e('Recarregar', 'iasd'); ?></a>
						</div> -->
					</div>
				</div>
			</div>
		</div>
<?php
	}

	function checkContents($params) {
		

		global $wp_query;

		$query = $wp_query->query;
		$wp_query = $this->query();

		// source 
		$source_id = $params['source_id'];


		switch ($source_id):
			case 'local':
		        $source_title = __('Local', 'iasd') ;
		        break;
		    case 'asn':
		        $source_title = __('Portal ASN', 'iasd') ;
		        break;
		    case 'videos':
		        $source_title = __('Portal Videos', 'iasd');
		        break;
		    case 'downloads':
		        $source_title = __('Portal Materiais', 'iasd');
		        break;
		    case 'eventos':
		        $source_title = __('Portal Eventos', 'iasd');
		        break;  
		    default:
		       	$source_title = 'Outra';
		endswitch;

		// date query

		$date_query_id = $params['date_query'];

		switch ($date_query_id):
		    case '-1 week':
		        $date_query_title = __('Última semana', 'iasd') ;
		        break;
		    case '-15 days':
		        $date_query_title = __('Última quinzena', 'iasd');
		        break;
		    case '-1 month':
		        $date_query_title = __('Último mês', 'iasd');
		        break;
		    case '-3 month':
		        $date_query_title = __('Últimos 3 meses', 'iasd');
		        break;
		    case '-6 month':
		        $date_query_title = __('Últimos 6 meses', 'iasd');
		        break;  
		    case '-1 year':
		        $date_query_title = __('Último ano', 'iasd');
		        break;    
		endswitch;
			
		// order by

		$order = $wp_query->query['order'];
		if ($order = 'ASC') {
			$orderby = __('Crescente');
		} else {
			$orderby = __('Decrescente');
		}
		
		// quantity
		$quant = 0;

		//post type
		$post_type = ucfirst($wp_query->query['post_type']);
		$tax_query = $wp_query->query['tax_query'];

		$tax_query_json = json_encode($params);

		?>

			<h2> <?php echo _e('Organizar conteúdo', 'iasd'); ?> </h2> <div id="btn-help-dummy"> </div>

			<fieldset id="regrasfield">
				<legend><?php echo _e('Regras de filtragem', 'iasd'); ?> <a id='bt1' href='javascript:void(0);'><?php echo _e('(ocultar)', 'iasd'); ?></a></legend>
				<table id="regras">
					<tbody>
						<tr>
						  <td>Fonte: <b><?php echo $source_title; ?></b></td>
						  <td>Quantidade itens: <b id="qnt"></b></td>
						</tr>
						</tr>
						  <td>Tipo: <b><?php echo $post_type; ?></b></td>
						  <?php if (!empty($orderby)) { ?>
						  	<td>Ordenação: <b><?php echo $orderby; ?></b></td>
						  <?php } ?>	
						</tr>
						</tr>
						  <td>Marcadores</td>
						  <?php if (!empty($date_query_title)) { ?>
						 	 <td>Data de publicação: <b><?php echo $date_query_title; ?></b></td>
						  <?php } ?>	
						</tr>
						</tr>
							<td>
					  	  		<ul id="marcadores">
									<?php

										// Marcadores 
										$tax_query = $wp_query->query['tax_query'];
					
										foreach ($tax_query as $tax_block) {
										
											if ( isset($tax_block['taxonomy']) ) { 
												$tax_slug = $tax_block['taxonomy'];
												$tax_obj = get_taxonomy($tax_slug);
												$tax_obj_labels = $tax_obj->labels;
												$tax_title = $tax_obj_labels->name;

												if (empty($tax_title))
													continue;

												echo '<li>';

												echo $tax_title . ': ';
	 
												// Iterate terms
												$terms = $tax_block['terms'];

												$i = 0;
												$len = count($terms);
												foreach ($terms as $term) {

													$term_obj = get_term_by('slug', $term, $tax_slug);
													$term_name = $term_obj->name;

													echo '<b>' . $term_name . '</b>';

													if ($i < $len - 1){
														echo ', ';
													}
													$i++;

												}
												echo '</li>';
											}
										}

										$authors_array = $params['authors'];

										if (!empty($authors_array)) {
											echo '<li>Autores: ';
											
											$lenj = count($authors_array);
											foreach ($authors_array as $author) {
												$obj = get_user_by( 'slug', $author ); 
												echo '<b>' . $obj->data->display_name . '</b>';

												if ($j < $lenj - 1){
													echo ', ';
												}
												$j++;

											}
											echo '</li>';

										}

										

									?>
					  	  		</ul>
					  		</td>	
						</tr>
					</tbody>
				</table>
			</fieldset>
			
			<div id="tabs-container">
			    <ul class="tabs-menu">
			        <li id="li-tab-1" class="current"><a href="#tab-1">Conteúdo</a></li>
			        <li id="li-tab-2" ><a href="#tab-2">Buscar e adicionar</a></li>
			        <li id="li-tab-3" ><a href="#tab-3">Adicionar manualmente</a></li>
			    </ul>
			    <div class="tab">
			       
			       <!-- ============ ABA CONTEUDO ============ -->

			        <div id="tab-1" class="tab-content">	

			        <div class="help-icon-dummy"><?php echo _e('Ajuda ', 'iasd'); ?></div> 
				    
				    <div class="alert alert-info alert-dismissable" id="help-text-tab1">
	                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
	                  <span class="font-awesome-alerts"></span>
	                 <?php echo __('Este é o conteúdo atual do post. Nesta aba você pode fazer duas ações: fixar e ordenar posts.<br/><br/>
Para fixar um post, basta clicar no pin (<img src="wp-content/plugins/pa-plugin-utilities/static/img/pin.png" class="pin" width="15px" height="15px" style="padding:0px;">). Ao fixar um post você impede que a inclusão de outros posts façam com que ele seja removido da visualização. <br/><br/>Posts fixos tem prioridade de exibição
Para ordenar um post, basta arrastá-lo e soltá-lo na ordem desejada. Apenas posts fixos podem ser ordenados.'); ?>
	                </div>

	               <script type="text/javascript">
				   	jQuery( ".help-icon-dummy" ).click(function() {
				   		var status = jQuery('#help-text-tab1').css('display')
				   		if(status == 'none' || status == undefined){ 
							jQuery("#help-text-tab1").show();
						} else {
							jQuery("#help-text-tab1").hide();
						}
				   	});
				   </script>

					<?php
						echo '<ol style="list-style-position: outside;">';
						$i = 0;
						if (!$wp_query->have_posts()) {
							echo '<center>' . __("Nenhum resultado encontrado.") .'</center>';
						}

						while($wp_query->have_posts()) {
							$wp_query->the_post();
							global $post;
							$i++;

							$manual = (isset($post->isManual) && $post->isManual) ? true : false;

							$checkedClass = ($this->isPostFixed() || $manual) ? ' class="iasd-ldp-fixed"' : '';

							$post_object = new stdClass();

							if ($manual) {

								$option 			= get_option($this->slug());
								$custom_posts 		= $option['custom_posts'];
								$custom_post 		= $custom_posts[get_the_ID()];

								$post_object->ID 				= $custom_post->ID;
								$post_object->title 			= $custom_post->post_title;
								$post_object->link 				= $custom_post->guid;
								$post_object->excerpt 			= $custom_post->post_excerpt;
								$post_object->thumb_url 		= $custom_post->thumbs['full'];
								$post_object->thumb_url_small 	= $custom_post->thumbs['thumb_80x80']; 
								$post_object->thumb_url_big 	= $custom_post->thumbs['thumb_124x124'];

								$post->ID 			= $custom_post->ID;
								$post->post_title 	= $custom_post->post_title;
								$post->guid 		= $custom_post->guid;
								$post->post_excerpt = $custom_post->post_excerpt;
								$post->thumbs 		= $custom_post->thumbs;
								
								
							} else {
								$post_object->ID = get_the_ID();
								$post_object->title = get_the_title();
								$post_object->link = get_permalink();
								$post_object->excerpt = strip_tags(get_the_excerpt());
								$post_object->thumb_url = $this->getThumbnail('full');
								$post_object->thumb_url_small = $this->getThumbnail('thumb_80x80');
								$post_object->thumb_url_big = $this->getThumbnail('thumb_124x124');
							}
										
							?>

							<li <?php echo $checkedClass; ?> data-id="<?php echo $post_object->ID; ?>" style="backgound-color:rgba(239, 239, 239, 0.32); padding-bottom:8px; display:block; <?php if(($this->isPostFixed() && !$manual) || $manual): ?> background-color: snow; border-color: #999999; border-style: dashed;display: block;<?php endif; ?>">
								
								<!-- ==================== Short item container ====================== -->

								<div id="short<?php echo $post_object->ID; ?>" class="item_block">

									<div class="alignleft"><img src='<?php echo $post_object->thumb_url_small;?>' width="80px" height="80px"></img></div>
										<div style="margin-left: 95px; width: 460px;">
											<p><?php echo '<b>'. $post_object->title. '</b>'; ?></p>
										</div>
									<br class="clear" />
									
									<!-- Botão fixar / desafixar -->
									<div class="div_fixar">
										<?php if(!$this->isPostFixed() && !$manual): ?> <a href="javascript:void(0);" class="iasd-ldp-set-fixed"><img src="wp-content/plugins/pa-plugin-utilities/static/img/pin.png" class="pin not_fixed"></img></a><?php endif; ?>
										<?php if($this->isPostFixed() && !$manual): ?> <a href="javascript:void(0);" class="iasd-ldp-set-fixed"><img src="wp-content/plugins/pa-plugin-utilities/static/img/pin.png" class="pin"></a><?php endif; ?>

									</div>

									<!-- Link mostrar mais/ ocultar -->
									<div class="div_link">
										<?php if($manual): ?>
										<a href="javascript:void(0);" class="edit_post"
											data-id="<?php echo $post_object->ID; ?>"
											data-title="<?php echo esc_attr($post_object->title); ?>"
											data-link="<?php echo $post_object->link; ?>"
											data-excerpt="<?php echo esc_attr($post_object->excerpt); ?>"
											data-thumb_url="<?php echo $post_object->thumb_url; ?>" style="margin-right:10px;"><?php _e('Editar Post', 'iasd');?></a>
										<a href="javascript:void(0);" class="iasd-ldp-remove" style="margin-right:10px;"><?php _e('Remover Post', 'iasd');?></a>
										<?php endif; ?>
										<a id='bt2_<?php echo $post_object->ID; ?>' href='javascript:void(0);'><?php echo _e('mais detalhes', 'iasd'); ?></a>
									</div>

								</div>								

								<!-- ==================== Extended item container ====================== -->

								<div id="extended<?php echo $post_object->ID; ?>" style="display:none;">

									<div class="alignleft" style="padding: 10px"><img src='<?php echo $post_object->thumb_url_big;?>' width="124px" height="124px"></div>
										<div style="margin-left: 150px; width: 460px;">
											<p><?php echo __('Title') . ': <b>', $post_object->title, '</b>'; ?></p>
											<p><?php echo __('Link'), ': <b>', $post_object->link, '</b>'; ?></p>
											<p><?php echo __('Excerpt'), ': <b>', $post_object->excerpt, '</b>'; ?></p>
									<br class="clear" />
									
									<!-- Botão fixar / desafixar  -->
									<div class="div_fixar">
										<?php if(!$this->isPostFixed() && !$manual): ?> <a href="javascript:void(0);" class="iasd-ldp-set-fixed"><img src="wp-content/plugins/pa-plugin-utilities/static/img/pin.png" class="pin not_fixed"></img></a><?php endif; ?>
										<?php if($this->isPostFixed() && !$manual): ?> <a href="javascript:void(0);" class="iasd-ldp-set-fixed"><img src="wp-content/plugins/pa-plugin-utilities/static/img/pin.png" class="pin"></a><?php endif; ?>
									</div>

								</div>
																	
								<!-- Link mostrar mais/ ocultar  -->
								<div class="div_link">
									<?php if($manual): ?>
									<a href="javascript:void(0);" class="edit_post"
										data-id="<?php echo $post_object->ID; ?>"
										data-title="<?php echo esc_attr($post_object->title); ?>"
										data-link="<?php echo $post_object->link; ?>"
										data-excerpt="<?php echo esc_attr($post_object->excerpt); ?>"
										data-thumb_url="<?php echo $post_object->thumb_url; ?>" style="margin-right:10px;"><?php _e('Editar Post', 'iasd');?></a>
									<a href="javascript:void(0);" class="iasd-ldp-remove" style="margin-right:10px;"><?php _e('Remover Post', 'iasd');?></a>
									<?php endif; ?>
									<a id='bt2_ocultar_<?php echo $post_object->ID; ?>' href='javascript:void(0);'><?php echo _e('ocultar', 'iasd'); ?></a>
								</div>
							
							</li>

							<!-- Script item <?php echo $post_object->ID; ?> -->
							<script>
								function swap_<?php echo $post_object->ID; ?>() {
									if (document.getElementById('short<?php echo $post_object->ID; ?>').style.display == 'block' ||
										document.getElementById('short<?php echo $post_object->ID; ?>').style.display == '' ) {
									   		document.getElementById('extended<?php echo $post_object->ID; ?>').style.display = 'block';
									   		document.getElementById('short<?php echo $post_object->ID; ?>').style.display = 'none';
									} else {
										document.getElementById('extended<?php echo $post_object->ID; ?>').style.display = 'none';
								   		document.getElementById('short<?php echo $post_object->ID; ?>').style.display = 'block';									}
									}
								
									document.getElementById('bt2_<?php echo $post_object->ID; ?>').addEventListener('click',function(e){
										swap_<?php echo $post_object->ID; ?>();
									});
									document.getElementById('bt2_ocultar_<?php echo $post_object->ID; ?>').addEventListener('click',function(e){
										swap_<?php echo $post_object->ID; ?>();
									});
							</script>

						<?php

							}
							wp_reset_query();
							$stri = strval($i);

							echo '</ol>';
							
						?>

			        </div>



			        <div id="tab-2" class="tab-content">			        	 
					        <div class="help-icon-dummy"><?php echo _e('Ajuda ', 'iasd'); ?></div> 
						    
						    <div class="alert alert-info alert-dismissable" id="help-text-tab2">
			                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
			                  <span class="font-awesome-alerts"></span>
			                 <?php echo __('Você pode pesquisar por qualquer post que se enquadre nas regras de filtragem<br/><br/>
		Ao clicar no pin (<img src="wp-content/plugins/pa-plugin-utilities/static/img/pin.png" class="pin" width="15px" height="15px" style="padding:0px;">). o post aparecerá na aba conteúdo como estatus "fixo", ou seja, novos posts não farão com que ele suma da visualização.<br/><br/>Para remover da visualização um post pesquisado, basta desafixá-lo na aba Conteúdo.'); ?>
			                </div>

					        <script type="text/javascript">
							   	jQuery( ".help-icon-dummy" ).click(function() {
							   		var status = jQuery('#help-text-tab2').css('display')
							   		if(status == 'none' || status == undefined){ 
										jQuery("#help-text-tab2").show();
									} else {
										jQuery("#help-text-tab2").hide();
									}
							   	});
						    </script>

						    <fieldset id="search_fildset"> 
						    	<legend>
						    		<?php echo _e('Busca por conteúdo', 'iasd'); ?>
						    	</legend>
								<div id="search_post_container">
			        			<input name="search_post_tag" id="search_post_title" type="text" value=""  class="widefat">
								<div class="alignright"><a href="javascript:void(0);" class="button" id="submit_search_post_title"><?php _e('Buscar', 'iasd'); ?></a></div>

			        		</div>
						    </fieldset>
			        

			        		<div id="html_search_response">

			        		</div>			        	


			        	<script>
			        		jQuery(document).ready(function(jQuery) {
			        			
								jQuery( "#submit_search_post_title" ).click(function() {
									
									var count = jQuery("#search_post_title").val().length;

									if (count >= 3) {
										jQuery('#html_search_response').html('<center><p><?php echo _e('Buscando..', 'iasd'); ?></p></center>');

										var text = jQuery("#search_post_title").val();
										var data = {
											'action': 'get_search_content',
											'title': text.replace(/\s/g,"+"),
											'query': <?php echo $tax_query_json; ?>,
											
										};
										// We can also pass the url value separately from ajaxurl for front end AJAX implementations
										jQuery.post(ajaxurl, data, function(response) {
											jQuery('#html_search_response').html(response);
										});
									} else {
		        						alert('<?php echo _e('Por favor usar pelo menos 3 caracteres na sua pesquisa.', 'iasd'); ?>');
		        					}
								});
							});

							jQuery("#search_post_title").keyup(function(event){
							    event.preventDefault();
							    if(event.keyCode == 13){
							        jQuery("#submit_search_post_title").click();
							    }
							});
			        	</script>
			        </div>

			        <div id="tab-3" class="tab-content">
				        <div class="help-icon-dummy"><?php echo _e('Ajuda ', 'iasd'); ?></div> 
					    
					    <div class="alert alert-info alert-dismissable" id="help-text-tab3">
		                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
		                  <span class="font-awesome-alerts"></span>
		                  <?php echo _e('Você pode adicionar manualmente um ítem ao conteúdo inserindo diretamente os dados do ítem no formulário abaixo. Todos os campos são obrigatórios.'); ?>
		                </div>

		               <script type="text/javascript">
					   	jQuery( ".help-icon-dummy" ).click(function() {
					   		var status = jQuery('#help-text-tab3').css('display')
					   		if(status == 'none' || status == undefined){ 
								jQuery("#help-text-tab3").show();
							} else {
								jQuery("#help-text-tab3").hide();
							}
					   	});
					   </script>
						<div class="iasd-ldp-ac">
							<form method="POST" action="">
								<div id="">
									<div id="post-body" class="metabox-holder">
										<div id="post-body-content">
											<div class="">
												<div class="inside">
													<h2><?php _e('Post Personalizado', 'iasd'); ?></h2>
													<input name="iasd-ldp-ac-form[id]" id="iasd-ldp-ac-form-id" type="hidden" value="" class="form-id widefat">
													<table class="form-table">
														<tbody>
															<tr>
																<td>
																	<label for="iasd-ldp-ac-form-title"><?php _e('Title'); ?>*</label>
																	<input name="iasd-ldp-ac-form[title]" id="iasd-ldp-ac-form-title" type="text" value="" class="form-title widefat" required>
																</td>
															</tr>
															<tr>
																<td>
																	<label for="iasd-ldp-ac-form-excerpt"><?php _e('Excerpt'); ?>*</label>
																	<textarea name="iasd-ldp-ac-form[excerpt]" id="iasd-ldp-ac-form-excerpt" class="form-excerpt widefat" required></textarea>
																</td>
															</tr>
															<tr>
																<td>
																	<label for="iasd-ldp-ac-form-thumbnail"><?php _e('Thumbnail'); ?>*</label>
																	<input name="iasd-ldp-ac-form[thumbnail]" id="iasd-ldp-ac-form-thumbnail" type="" value="" class="form-thumbnail widefat" required>
																</td>
															</tr>
															<tr>
																<td>
																	<label for="iasd-ldp-ac-form-link"><?php _e('Link'); ?>*</label>
																	<input name="iasd-ldp-ac-form[link]" id="iasd-ldp-ac-form-link" type="" value="" class="form-link widefat" required>
																</td>
															</tr>
															<tr>
																<td>
																	<div class="alignleft"><a href="javascript:void(0);" class="cancel_post button"><?php _e('Cancelar', 'iasd'); ?></a></div>
																	<div class="alignright"><a href="javascript:void(0);" class="save_post button"><?php _e('Salvar', 'iasd'); ?></a></div>
																</td>
															</tr>
														</tbody>
													</table>
												</div>
											</div>
										</div>
									</div><!-- /post-body-->
								</div>
							</form>
						</div>
			        </div>
			    </div>
			</div>
	
		
			<script>

				// Funcao para troca de abas dentro modal dummy
				jQuery(document).ready(function() {
				    jQuery(".tabs-menu a").click(function(event) {
				        event.preventDefault();
				        jQuery(this).parent().addClass("current");
				        jQuery(this).parent().siblings().removeClass("current");
				        var tab = jQuery(this).attr("href");
				        jQuery(".tab-content").not(tab).css("display", "none");
				        jQuery(tab).fadeIn();
				    });

				    document.getElementById("qnt").innerHTML = "<?php echo $stri; ?>";
				});

				//Funçao para mostrar/ocultar mais conteudo dentro modal dummy
				function swapRules() {

					if (document.getElementById('regras').style.display == 'none') {
				   		document.getElementById('regras').style.display = 'block';
				   		document.getElementById('bt1').innerHTML = '<?php echo _e('(ocultar)', 'iasd'); ?>';
					} else {
				    	document.getElementById('regras').style.display = 'none';
				    	document.getElementById('bt1').innerHTML = '<?php echo _e('(mostrar)', 'iasd'); ?>';
					}
				}
				
				document.getElementById('bt1').addEventListener('click',function(e){
					swapRules();
				});

				//Desabilita ajuda no modal dummy dentro do wp-admin
				var pathURL = window.location.pathname;
				if (pathURL.indexOf("widget") > -1){
					jQuery(".close").hide();
					jQuery(".alert-info").addClass('help_modal_wp_admin');
					jQuery(".help-icon-dummy").addClass('help-icon-dummy-wp-admin');
					jQuery(".help-icon-dummy").removeClass('help-icon-dummy');

				}

			</script>
	<?php 
	}

	static function CheckContentsAjax() {
		
		$params = $_REQUEST;

		if(isset($params['multi_number']) && isset($params['widget_number']) && isset($params[self::form_id])) {
			$widget_number = ($params['multi_number']) ? $params['multi_number'] : $params['widget_number'];
			$params = $params[self::form_id][$widget_number];
			$params['number'] = $widget_number;

			if (isset($params['taxonomy_query'][IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS])){
				//Nega os posts filhos da sede regional
			 	$params['taxonomy_query'][IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS]['include_children'] = false;
			}					
			$widget = new IASD_ListaDePosts();

			$custom_posts = (isset($params['custom_post'])) ? $params['custom_post'] : false;
			unset($params['custom_post']);
			$widget->_setInstance($params);

			if($custom_posts) {
				$jsonStr = stripslashes($custom_posts);
				if($jsonCommands = json_decode($jsonStr)) {
					if($jsonCommands->action == 'add') {
						$post_obj = $widget->addCustomPost($jsonCommands->parameters);
						$params['custom_post_obj'] = $post_obj;
					}
					if($jsonCommands->action == 'del')
						$widget->delCustomPost($jsonCommands->parameters);

					$widget->cleanCustomPosts();
				}
			}
			$widget->checkContents($params);
		}
	}


	static function getJsonContentPOST() {

		$body = file_get_contents('php://input');
		$query_args = json_decode($body, true);

		$iasdObj = new IASD_ListaDePosts();
		$iasdObj->_setInstance(self::DefaultInstance());
		$iasdObj->jsonContentPOST($query_args);	
	}
	

	function jsonContentPOST($query_args) {

		global $wp_query;
		$wp_query = $this->query($query_args);

		echo json_encode($wp_query);
		die();
	}


	static function getJsonContentGET() {

		$limit = $_GET["limit"];
		$paged = $_GET["paged"];

		$body = '{"sidebar":"front-page","source_id":"local","post_type":"post","posts_per_page":"'. $limit .'","orderby":"date","order":"DESC","date_query":"","paged":"'. $paged .'"}';
		$query_args = json_decode($body, true);

		$iasdObj = new IASD_ListaDePosts();
		$iasdObj->_setInstance(self::DefaultInstance());
		$iasdObj->jsonContentGET($query_args);	
	}
	

	static function getSearchContent() {

		$query_args = $_POST["query"];
		$title 		= $_POST["title"];

		$query_args['slug_search'] 		= $title;
		$query_args['posts_per_page'] 	= 10;
		$query_args['fixed_ids'] 		= '';

		$iasdObj = new IASD_ListaDePosts();
		$iasdObj->_setInstance(self::DefaultInstance());
		$iasdObj->jsonSearchGET($query_args);	

	}


	function jsonContentGET($query_args) {

		global $wp_query;

		$wp_query = $this->query($query_args);

		$posts_array = array();

		while($wp_query->have_posts()) : $wp_query->the_post();

			$tax_array = get_post_taxonomies(get_the_ID());

			$post_array = get_post( get_the_ID() );
			$post_array->terms = array();
			$thumb_id = get_post_thumbnail_id(get_the_ID());
			$post_array->thumbnail = wp_get_attachment_image_src( $thumb_id , 'thumb_150x100') ;
			$post_array->full_image = wp_get_attachment_image_src( $thumb_id , 'full') ;

			$post_array->author_nickname = get_the_author_meta( 'nickname' , $post_array->post_author );
			$post_array->author_first_name = get_the_author_meta( 'first_name' , $post_array->post_author );
			$post_array->author_last_name = get_the_author_meta( 'last_name' , $post_array->post_author );

			if (SITE == "videos") {
				$post_array->video_url = get_post_meta( get_the_ID(), 'dp_video_url', true );;
			}

			ob_start();
			the_content();
			$output = ob_get_contents();
			ob_end_clean();

			$post_array->content = $output;


			if (!empty($tax_array)){
				foreach ( $tax_array as $taxonomy) {	
					$post_array->$taxonomy = wp_get_post_terms( get_the_ID(), $taxonomy);
				}
			}

			array_push($posts_array,$post_array);

		endwhile;
		header('Content-Type: application/json');
		echo json_encode($posts_array);

		die();
	}

	function addCustomPost($post_object) {
		$instances = get_option($this->slug());
		if(!isset($instances['custom_posts']))
			$instances['custom_posts'] = array();

		if(!isset($post_object->thumb_url))
			return false;

		if($post_object->ID == null)
			$post_object->ID = md5(serialize($post_object));

		if(isset($instances['custom_posts'][$post_object->ID])) {
			$base_post = $instances['custom_posts'][$post_object->ID];

			if($base_post->thumb_url == $post_object->thumb_url) {
				$post_object->thumb_id = $base_post->thumb_id;
				$post_object->thumbs = $base_post->thumbs;
			} else {
				wp_delete_attachment($base_post->thumb_id, true );
			}
		}

		$thumb_url = $post_object->thumb_url;

		$thumb_id = (isset($post_object->thumb_id)) ? $post_object->thumb_id : null;
		$thumbs   = (isset($post_object->thumbs)) ? $post_object->thumbs : null;

		if (!empty($thumb_url) && !$thumb_id && !$thumbs) {
			$tmp = download_url( $thumb_url );

			preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $thumb_url, $matches );
			$file_array['name'] = basename($matches[0]);
			$file_array['tmp_name'] = $tmp;

			if ( is_wp_error( $tmp ) ) {
				@unlink($file_array['tmp_name']);
				$file_array['tmp_name'] = '';
			}

			$thumb_id = media_handle_sideload( $file_array, null, $thumb_url);
			if ( is_wp_error($thumb_id) ) {
				@unlink($file_array['tmp_name']);
				return false;
			}
			$post_object->thumb_id = $thumb_id;

			$media_meta = wp_get_attachment_metadata($thumb_id);
			$media_sizes = $media_meta['sizes'];

			list($u1, $u2, $media_file) = explode('/', $media_meta['file'], 3);

			$media_url = wp_get_attachment_url($thumb_id);

			$thumbs = array('full' => $media_url);
			foreach($media_sizes as $size => $size_info) {
				$size_url = str_replace($media_file, $size_info['file'], $media_url);
				$thumbs[$size] = $size_url;
			}
			if(!count($thumbs))
				return false;

			$post_object->thumbs = $thumbs;
			$post_object->thumb_url = $media_url;
		}

		$post_object->filter = 'raw';


		$instances['custom_posts'][$post_object->ID] = $post_object;

		delete_option($this->slug());
		add_option($this->slug(), $instances, false, 'no');

		if($post_object->ID) {
			$fixed_ids = $this->widgetGet('fixed_ids', '');
			if(strpos($fixed_ids, $post_object->ID) === false) {
				if($fixed_ids)
					$fixed_ids .= ',';
				$fixed_ids .= $post_object->ID;
			}
			$this->instance['fixed_ids'] = $fixed_ids;
		}

		return $post_object;
	}

	function delCustomPost($post_id) {
		$instances = get_option($this->slug());
		if(!isset($instances['custom_posts']))
			$instances['custom_posts'] = array();

		if(isset($instances['custom_posts'][$post_id])) {
			if(isset($instances['custom_posts'][$post_id]->thumb_id)) {
				$thumb_id = $instances['custom_posts'][$post_id]->thumb_id;
				wp_delete_attachment( $thumb_id, true );
			}
			unset($instances['custom_posts'][$post_id]);
		}

		delete_option($this->slug());
		add_option($this->slug(), $instances, false, 'no');
	}

	function cleanCustomPosts() {
		$fixed_ids = $this->widgetGet('fixed_ids', '');

		$instances = get_option($this->slug());
		if(!isset($instances['custom_posts']))
			$instances['custom_posts'] = array();

		foreach($instances['custom_posts'] as $id => $custom_post)
			if(strpos($fixed_ids, $id) === false)
				$instances['custom_posts'][$id] = false;

		$instances['custom_posts'] = array_filter($instances['custom_posts']);

		delete_option($this->slug());
		add_option($this->slug(), $instances, false, 'no');
	}

	static function Refresh() {
		$params = $_REQUEST;
		global $wp_filter;
		$old_filter = $wp_filter;
		$wp_filter = array('trim' => $old_filter['trim']);

		if(isset($params['widget'])) {
			IASD_DefaultImage::Init();

			$widget_slug = $params['widget'];
			$widget_number = substr($widget_slug, strrpos($widget_slug, '-') + 1);

			$instances = get_option(self::option_id);

			if(isset($instances[$widget_number])) {
				unset($params['widget']);

				$instance = array_merge($instances[$widget_number], $params);
				$widget = new IASD_ListaDePosts();
				$widget->_setInstance($instance);

				$widget->widget(array(), $instance);
			}
		}

		global $wp_filter;
		$wp_filter = $old_filter;
	}

/**
		ACCESSORS
*/

	function slug() {
		return self::base_id . '-' . $this->number;
	}

	function _set($number) {
		parent::_set($number);

		$this->view_config = null;
		$this->availableViews = null;
		$this->availablePostTypes = null;
	}

	public function getInstance() {
		return $this->instance;
	}

	function _setInstance($instance) {
		$this->instance = $instance;

		if(isset($instance['number']))
			$this->_set($instance['number']);

		$this->view_config = null;
		$this->availableViews = null;
		$this->availablePostTypes = null;
	}

	function _setInnerVariables($instance = null, $view_config = null, $availableViews = null, $availablePostTypes = null) {
		$this->instance = $instance;
		$this->view_config = $view_config;
		$this->availableViews = $availableViews;
		$this->availablePostTypes = $availablePostTypes;
	}

	function _getInnerVariables() {
		return array(
				'instance'           => $this->instance,
				'view_config'        => $this->view_config,
				'availableViews'     => $this->availableViews,
				'availablePostTypes' => $this->availablePostTypes,
			);
	}

	function widgetGet($field, $default = null) {
		if(!is_array($this->instance))
			$this->instance = array();
		return isset($this->instance[$field])
					? $this->instance[$field]
					: $default;
	}

/**
		RENDERING ASSESSORS
*/

	function _setArgs($args) {
		$this->widget_args = $args;
	}

	function widgetArg($field, $default = null) {
		return isset($this->widget_args[$field])
					? $this->widget_args[$field]
					: $default;
	}

	function widgetArgs() {
		return $this->widget_args;
	}

/**
		SOURCE ASSESSORS
*/

	function getBasicSources() {
		$sources = array();
		$base_sources = self::BasicSources();
		$rules = self::LoadRules();

		foreach($base_sources as $source => $details)
			if(isset($rules['sources'][$source]) || in_array($source, array('outra', 'local')))
				$sources[$source] = $details;

		return $sources;
	}

	function getCurrentSourceId() {
		$source_id = $this->widgetGet('source_id');
		if($source_id == 'outra')
			$source_id = self::base_id . '_' . $this->number;

		return $source_id;
	}

	function getCurrentSource() {
		return self::LoadSourceRules($this->getCurrentSourceId());
	}

	function getCurrentPostType() {
		return self::LoadPostTypeRules($this->widgetGet('post_type'), $this->getCurrentSourceId());
	}

	function getAvailablePostTypes() {
		$source = $this->getCurrentSource();

		return ($source) ? $source['post_type'] : array();
	}

	function getAvailableTaxonomies() {
		$current_post_type = $this->getCurrentPostType();

		return $current_post_type['taxonomy'];
	}

/**
		VIEW ACESSORS
*/

	function findSidebar() {
		$sidebars_widgets = wp_get_sidebars_widgets();
		$slug = $this->slug();
		$current_sidebar_slug = null;

		foreach ($sidebars_widgets as $sidebar_slug => $widgets ) {
			if(!$widgets)
				continue;

			if(in_array($slug, $widgets))
				$current_sidebar_slug = $sidebar_slug;
		}

		if(!$current_sidebar_slug)
			if(isset($_REQUEST[self::form_id]) && $form = $_REQUEST[self::form_id])
				if(isset($form[$this->number]) && $params = $form[$this->number])
					if(isset($params['sidebar']) && $params['sidebar'])
						$current_sidebar_slug = $params['sidebar'];

		return $current_sidebar_slug;
	}

	function getSidebar() {
		$sidebar = $this->findSidebar();

		global $wp_registered_sidebars;

		if(isset($wp_registered_sidebars[$sidebar]))
			return $wp_registered_sidebars[$sidebar];

		return null;
	}

	function getSelectedView() {
		$viewName = $this->widgetGet('view');

		$view = IASD_ListaDePosts_Views::GetView($viewName);

		$this->view_config = $view;
		return $view;
	}

	function mayHaveConfig() {
		return $this->widgetView('allow_grouping', false);
	}

	function mayHaveSeeMore() {
		return $this->widgetView('allow_see_more', false);
	}

	function widgetView($field, $default = null) {
		if(!$this->view_config)
			$this->getSelectedView();
		return isset($this->view_config[$field])
					? $this->view_config[$field]
					: $default;
	}

	function getAvailableViews() {
		if(!$this->availableViews) {
			$availableViews = array();
			$sidebarColClass = false;

			$sidebar = $this->getSidebar();
			if($sidebar)
				if(isset($sidebar['col_class']))
					$sidebarColClass = $sidebar['col_class'];

			$postType = $this->widgetGet('post_type');

			if($sidebarColClass) {
				$views = IASD_ListaDePosts_Views::GetViews();
				foreach ($views as $viewName => $info) {
					if($sidebarColClass == 'col-md-8') {
						if(!in_array('col-md-8', $info['cols']) && !in_array('col-md-4', $info['cols']))
							continue;
					} else if($sidebarColClass == 'col-md-4') {
						if(!in_array('col-md-4', $info['cols']))
							continue;
					}

					if(count($info['post_type'])) {
						if(!in_array($postType, $info['post_type']))
							continue;
					}

					$availableViews[$viewName] = $info;

					$availableCols = array();

					if(in_array($sidebarColClass, array('col-md-12', 'col-md-8', 'col-md-4')) && in_array('col-md-4', $info['cols']))
						$availableCols['col-md-4'] = __('1/3 da Coluna', 'iasd');
					if(in_array($sidebarColClass, array('col-md-12', 'col-md-8')) && in_array('col-md-8', $info['cols']))
						$availableCols['col-md-8'] = __('2/3 da Coluna', 'iasd');
					if(in_array($sidebarColClass, array('col-md-12')) && in_array('col-md-12', $info['cols']))
						$availableCols['col-md-12'] = __('Coluna Inteira', 'iasd');

					$availableViews[$viewName]['cols'] = $availableCols;
				}
			}

			$this->availableViews = $availableViews;
		}

		return $this->availableViews;
	}

	function getAvailableCols() {
		$views = $this->getAvailableViews();
		$viewName = $this->widgetGet('view');
		$availableCols = array();

		if(isset($views[$viewName]))
			$availableCols = $views[$viewName]['cols'];

		return $availableCols;
	}

/**
		COOKIE ASSESSORS
*/

	function setCookie($name, $value, $duration = false, $domain = '/', $secure = false, $httponly = false) {
		$cookie_name = $this->slug() . '::' . $name;
		if(!$duration)
			$duration = strtotime( '+1 year' );

		$setcookie = (!headers_sent()) ? setcookie($cookie_name, $value, $duration, $domain, $secure, $httponly) : false;

		return $setcookie;
	}

	function getCookie($name) {
		$cookie_name = $this->slug() . '::' . $name;

		return (isset($_COOKIE[$cookie_name]))
				? $_COOKIE[$cookie_name]
				: null;
	}

	function setCookieGroupingSlug() {
		$value = $this->widgetGet('grouping_slug');
		return $this->setCookie('grouping_slug', $value);
	}

	function getCookieGroupingSlug() {
		return $this->getCookie('grouping_slug');
	}

/**
		POST ASSESSORS
*/

	function isPostFixed() {
		$id = get_the_ID();

		return in_array($id, $this->widgetGetFixedIds());
	}

	function getPostTerms($taxonomy = IASD_Taxonomias::TAXONOMY_EDITORIAS) {
		$terms = array();

		$source_id = $this->widgetGet('source_id');
		if($source_id == 'local') {
			$base_terms = wp_get_post_terms(get_the_ID(), $taxonomy);
			if(!is_wp_error($base_terms))
				foreach($base_terms as $base_term)
					$terms[] = $base_term;
		} else {
			global $post;
			if(isset($post->taxonomies[$taxonomy]))
				$terms = $post->taxonomies[$taxonomy];
		}
		return $terms;
	}

	function getPostTerm($taxonomy = IASD_Taxonomias::TAXONOMY_EDITORIAS) {
		$term = null;
		$terms = $this->getPostTerms($taxonomy);

		if($terms && count($terms)) {
			$term = current($terms);
		}

		if($author = $this->getPostAuthor())
			if(isset($author['taxonomy']))
				if($author['taxonomy'])
					$term = $author['taxonomy'];

		return $term;
	}

	function getPostAuthor() {
		global $post;
		$author = array();

		$source_id = $this->widgetGet('source_id');
		if($source_id == 'local') {
			$post_author = get_user_by('id', $post->post_author);

			if($post_author)
				$author = $post_author = apply_filters('iasd_query_author', array('id' => $post->post_author, 'slug' => $post_author->user_nicename, 'name' => $post_author->display_name));
		} else {
			if(isset($post->author))
				$author = $post->author;
		}

		return $author;
	}

	function getPostMeta($meta_key) {
		$meta_value = '';

		$source_id = $this->widgetGet('source_id');
		if($source_id == 'local') {
			$meta_value = get_post_meta(get_the_ID(), $meta_key, true);

		} else {
			global $post;

			$metas = (array) $post->meta;
			if(isset($metas[$meta_key]))
				$meta_value = $metas[$meta_key];
		}
		if(is_array($meta_value) && count($meta_value) == 1)
			$meta_value = current($meta_value);

		return $meta_value;
	}

	function getThumbnail($size) {
		return IASD_DefaultImage::PostThumbnailUrl($size);
	}

	function getThumbnailName() {
		return IASD_DefaultImage::PostThumbnailName();
	}

/**
		VALIDATION & UPDATE
*/

	static function DefaultInstance() {


		$base = array(
			'authors'             => array(),
			'authors_norepeat'    => false,
			'author_contextual'   => 0,
			'date_query'          => '',
			'grouping_contextual' => 0,
			'grouping_taxonomy'   => null,
			'grouping_forced'     => false,
			'grouping_slug'       => false,
			'meta_query'          => array(),
			'number'              => 0,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'posts_per_page'      => 4,
			'post_status'         => 'publish',
			'post_type'           => 'post',
			'fixed_ids'           => '',
			'saved'               => 0,
			'secret'              => self::GenerateSecret(),
			'seemore'             => 1,
			'seemore_text'        => __('Veja mais', 'iasd'),
			'seemore_title'       => '',
			'sidebar'             => '',
			'source_id'           => 'local',
			'taxonomy_query'      => array(),
			'taxonomy_norepeat'   => array(),
			'title'               => '',
			'view'                => ''
		);

		//$base = self::Validate($base, $base);
		return $base;
	}


	static function Validate($new_instance, $old_instance) {
		if(!count($old_instance)) {
			$old_instance = self::DefaultInstance();
		}
		//Proteção contra perda de dados em situações de debug
		if(count($new_instance) <= 1) {
			if(isset($new_instance['number']))
				$old_instance['number'] = $new_instance['number'];
			$new_instance = $old_instance;
		}

		//Disabled Fields
		if(isset($old_instance['saved']) && $old_instance['saved'] && count($new_instance) > 2) {

			if(!isset($new_instance['seemore']))
				$new_instance['seemore'] = 0;

			if(!isset($new_instance['grouping_forced']))
				$new_instance['grouping_forced'] = 0;
			if(!isset($new_instance['grouping_taxonomy']) || !$new_instance['grouping_taxonomy'])
				$new_instance['grouping_forced'] = 0;

			if(!isset($new_instance['grouping_contextual']))
				$new_instance['grouping_contextual'] = 0;
			if(!isset($new_instance['author_contextual']))
				$new_instance['author_contextual'] = 0;

			if(!isset($new_instance['authors_norepeat']))
				$new_instance['authors_norepeat'] = 0;
			if(!isset($new_instance['taxonomy_norepeat']))
				$new_instance['taxonomy_norepeat'] = array();
			if(!isset($new_instance['taxonomy_query']))
				$new_instance['taxonomy_query'] = array();
		}

		//Missing post_type
		if(!isset($new_instance['post_type']))
			if(isset($old_instance['post_type']))
				$new_instance['post_type'] = $old_instance['post_type'];
		if(!isset($new_instance['post_type']))
			$new_instance['post_type'] = 'post';

		//Missing source_id
		if(!isset($new_instance['source_id']))
			if(isset($old_instance['source_id']))
				$new_instance['source_id'] = $old_instance['source_id'];

		//Missing source_id
		if(!isset($new_instance['view']))
			$new_instance['view'] = '';

		//Missing title
		if(!isset($new_instance['title']) || strlen($new_instance['title']) < 4)
			if(isset($old_instance['title']))
				$new_instance['title'] = $old_instance['title'];

		//Missing secret
		if(!isset($new_instance['secret']))
			$new_instance['secret'] = (isset($old_instance['secret'])) ? $old_instance['secret'] : self::GenerateSecret();

		//Se não tiver sede, adiciona a sede DSA - removido a pedido da DSA
		// $new_instance = self::ValidateSedeRegional($new_instance);
		if (isset($new_instance['taxonomy_query'][IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS])){
			//Nega os posts filhos da sede regional
			 $new_instance['taxonomy_query'][IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS]['include_children'] = false;
		}

		return $new_instance;
	}

	static function ValidateSedeRegional($new_instance) {

		$post_type = $new_instance['post_type'];
		$source_id = $new_instance['source_id'];
		if($source_id == 'outra')
			$source_id = self::base_id . '_' . $new_instance['number'];

		$rules = self::LoadPostTypeRules($post_type, $source_id);

		$taxonomies = array();
		if($rules && isset($rules['taxonomy']))
			$taxonomies = $rules['taxonomy'];

		//ADD IF POSSIBLE
		if(in_array(IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS, $taxonomies)) {
			if(!isset($new_instance['taxonomy_query'][IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS])) {
				$dsa_term = get_term_by('slug', 'dsa', IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS);
				if($dsa_term) {
					$new_instance['taxonomy_query'][IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS] = array();
					$new_instance['taxonomy_query'][IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS] = array('field' => 'slug', 'terms' => array($dsa_term->slug), 'include_children' => false);
				}
			}
            $new_instance['taxonomy_query'][IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS]['include_children'] = false;
		}


		return $new_instance;
	}

	function update( $new_instance, $old_instance ) {
		$new_instance['number'] = $this->number;

		$new_instance = self::Validate($new_instance, $old_instance);

		$new_instance['saved'] = time();
		$sidebar = $this->findSidebar();
		if($sidebar)
			$new_instance['sidebar'] = $sidebar;

		if(!isset($old_instance['post_type']) || $new_instance['post_type'] != $old_instance['post_type'])
			$this->availablePostTypes = false;
		if(!isset($old_instance['sidebar']) || $new_instance['sidebar'] != $old_instance['sidebar'])
			$this->availableViews = false;
		if(!isset($old_instance['view']) || $new_instance['view'] != $old_instance['view'])
			$this->view_config = false;

		// @codeCoverageIgnoreStart
		if(!defined('UNIT_TESTING')) {
			$forced_query = array_merge($new_instance, array('non-cacheable' => 1));
			$this->query($forced_query);

			$this->_setInstance($new_instance);
			$this->widgetCacheReset();

		}
		// @codeCoverageIgnoreEnd

		if(isset($new_instance['custom_post']))
			unset($new_instance['custom_post']);

		return $new_instance;
	}

/**
		FORMS
*/

	function includeView($formView) {
		require PAPU_LDPW . '/' . $formView;
	}

	function form( $instance ) {
		$this->_setInstance($instance);
		$sidebar = $this->getSidebar();
		if($sidebar)
			if(isset($sidebar['class']))
				if(strpos($sidebar['class'], 'inactive-sidebar') !== FALSE)
					return false;
?>
		<input id="<?php echo $this->get_field_id('sidebar'); ?>"
				class="iasd-ldp sidebar"
				name="<?php echo $this->get_field_name('sidebar'); ?>"
				type="hidden" value="<?php echo esc_attr($this->widgetGet('sidebar')); ?>" />
<?php
		$saved = isset($instance['saved']) ? $instance['saved'] : 0;
		if(!$saved || !$this->widgetGet('sidebar')) {
			echo '<p class="no-options-widget">' . __('Clique em "Salvar" para mostrar as opções iniciais', 'iasd') . '</p>';
		} else {
			echo '<div class="iasd-listadeposts-widget-form-container">';
			$this->formSecret();
			$this->formTitle();
			$this->formContent();
			$this->formAppearance();
			$this->formContentsAndPreview();
			echo '</div>';
		}

		return ($saved > 0);
	}

	public function formSecret() {
?>
		<input id="<?php echo $this->get_field_id('secret'); ?>"
				name="<?php echo $this->get_field_name('secret'); ?>"
				type="hidden" value="<?php echo esc_attr($this->widgetGet('secret')); ?>" />
<?php
	}

	public function formTitle() {
		$this->includeView('form_title.php');
	}

	public function formContent() {
?>
		<fieldset class="iasdlistadeposts iasdlistadeposts-content fieldsetborder">
			<legend><?php _e('Conteúdo', 'iasd'); ?></legend>
<?php
		$this->formContentSourceId();
		$this->formContentSourceExtra();
		$this->formContentPostType();
		$this->formContentTaxonomies();
		$this->formContentAuthors();
?>
		</fieldset>
<?php
	}

/**
		FORM CONTENT
*/

	public function formContentSourceId() {
		$this->includeView('form_content_source_id.php');
	}

	public function formContentSourceExtra() {
		$this->includeView('form_content_source_extra.php');
	}

	public function formContentPostType() {
		$this->includeView('form_content_post_type.php');
	}

	public function formContentTaxonomies() {
		$this->includeView('form_content_taxonomies.php');
	}

	public function formContentAuthors() {
		$this->includeView('form_content_authors.php');
	}

/**
		FORM APPEARANCE
*/

	public function formAppearance() {
?>
				<fieldset class="iasdlistadeposts iasdlistadeposts-appearance fieldsetborder">
					<legend><?php _e('Aparência', 'iasd'); ?></legend>
<?php
		$this->formAppearanceView();
		$this->formAppearanceWidth();
		$this->formAppearancePostsPerPage();
		$this->formAppearanceOrderBy();
		$this->formAppearanceDateQuery();

		$this->formAppearanceGroupingTaxonomy();
		$this->formAppearanceSeeMore();
?>
				</fieldset>
<?php
	}

	public function formAppearanceView() {
		$this->includeView('form_appearance_view.php');
	}

	public function formAppearanceWidth() {
		$this->includeView('form_appearance_width.php');
	}

	public function formAppearancePostsPerPage() {
		$this->includeView('form_appearance_post_per_page.php');
	}

	public function formAppearanceOrderBy() {
		$this->includeView('form_appearance_orderby.php');
	}

	public function formAppearanceDateQuery() {
		$this->includeView('form_appearance_date_query.php');
	}

	public function formAppearanceGroupingTaxonomy() {
		$this->includeView('form_appearance_grouping_taxonomy.php');
	}

	public function formAppearanceSeeMore() {
		$this->includeView('form_appearance_seemore.php');
	}

/**
		FORM CHECK CONTENTS
*/

	public function formContentsAndPreview() {
		$field = 'fixed_ids';
		$value = $this->widgetGet($field);

		$field1 = 'contents';
		$field2 = 'custom_post';
?>
		<input id="<?php echo $this->get_field_id($field); ?>"
				name="<?php echo $this->get_field_name($field); ?>"
				class="iasd-ldp-fixed-ids"
				type="hidden" value="<?php echo esc_attr($value); ?>" />
		<div class="iasdlistadeposts-form-spacer <?php echo $this->get_field_id($field1 . '_spacer'); ?>">
			<div class="iasdlistadeposts iasd-widget-<?php echo $field1; ?>-container">
				<div class="alignright">
					<a href="#TB_inline?inlineId=iasd-ldp-cc-container-dummy" class="button iasd-widget iasd-widget-<?php echo $field1; ?>" >
						<?php _e('Ver Conteúdos', 'iasd') ?>
					</a>
				</div>
				<br class="clear" />
			</div>
		</div>
		<input id="<?php echo $this->get_field_id($field2); ?>"
				name="<?php echo $this->get_field_name($field2); ?>"
				class="iasd-ldp-custom-post"
				type="hidden" value="" />
<?php
	}

/**
		QUERY
*/

	function query($query_args = false ) {
		if(!$query_args)
			$query_args = $this->getInstance();

		$custom_information = get_option(self::base_id . '-' . $query_args['number']);

		if(isset($custom_information['custom_posts']))
			$query_args['custom_posts'] = $custom_information['custom_posts'];

		if(!isset($query_args['grouping_contextual']))
			$query_args['grouping_contextual'] = false;
		if(!isset($query_args['author_contextual']))
			$query_args['author_contextual'] = false;

		if(!isset($query_args['grouping_taxonomy']))
			$query_args['grouping_taxonomy'] = false;

		if($query_args['grouping_contextual'] || $grouping_taxonomy = $query_args['grouping_taxonomy']){
			if(!$query_args['grouping_contextual']) {
				if(isset($query_args['grouping_slug']) && !in_array($query_args['grouping_slug'], array('default', 'forced'))) {
					$this->setCookieGroupingSlug($query_args['grouping_slug']);
				} else {
					$this->instance['grouping_slug_original'] = (isset($this->instance['grouping_slug'])) ? $this->instance['grouping_slug'] : 'default';
					$grouping_slug = $this->getCookieGroupingSlug();
					$terms = $this->instance['taxonomy_query'][$grouping_taxonomy]['terms'];
					if(!in_array($grouping_slug, $terms))
						$grouping_slug = $this->instance['grouping_slug_original'];
					$query_args['grouping_slug'] = $this->instance['grouping_slug'] = $grouping_slug;
				}
			}

			$fixedArgs = self::BuildQuery($query_args);
			$grouping_query = IASD_Query::GroupingQuery($fixedArgs);

			return $grouping_query;

		} else {

			$query_args = self::BuildQuery($query_args);
			$obj = new IASD_Query($query_args);

			return $obj;
		}
	}

	public static function BuildQueryFieldsToClean() {
		return array('number', 'saved', 'secret', 'sidebar', 'taxonomy_query', 'title', 'view', 'width', 'seemore_text','seemore', 'source_id', 'source_extra', );

	}

	public static function BuildQuery($args = array(), $defaultsFilter = 'IASDListaDePosts::BuildQuery-defaults') {
		
		$base_query_args = self::DefaultInstance();
	
		$json_output = null;
		$wp_query_args = array_merge($base_query_args, $args);
		$wp_query_args = apply_filters($defaultsFilter, $wp_query_args, $args);

		foreach($wp_query_args as $k => $v) {
			if(isset($args[$k])) {
				if($args[$k]) {
					if(is_array($v)) { // validação para tax_query e meta_query
						if(is_array($args[$k]))
							if(count($args[$k]))
								$wp_query_args[$k] = $args[$k];
					} else { //demais parametros
						$wp_query_args[$k] = $args[$k];
					}
				}
				unset($args[$k]);
			}
		}

		if($wp_query_args['source_id'] != 'local') {
			$source_id = $wp_query_args['source_id'];
			if($source_id == 'outra') {
				$wp_query_args['source'] = $wp_query_args['source_extra'];
			} else {
				$sources = self::BasicSources();
				$wp_query_args['source'] = $sources[$source_id]['url'];
			}
		}

		if(!$wp_query_args['grouping_contextual'])
			$wp_query_args = self::BuildQueryTax($wp_query_args);

		$fieldsToClean = self::BuildQueryFieldsToClean();
		foreach($fieldsToClean as $fieldToClean)
			if(isset($wp_query_args[$fieldToClean]))
				unset($wp_query_args[$fieldToClean]);

		return $wp_query_args;
	}

	static function BuildQueryTax($wp_query_args = array()) {
		$wp_query_args['tax_query'] = array();

		//Garante que Sedes Regionais não incluam children
		foreach($wp_query_args['taxonomy_query'] as $k => $tax_query) {
			$i = count($wp_query_args['tax_query']);

			$wp_query_args['tax_query'][$i] = $tax_query;
			$wp_query_args['tax_query'][$i]['operator'] = 'IN';
			$wp_query_args['tax_query'][$i]['field'] = 'slug';
			$wp_query_args['tax_query'][$i]['taxonomy'] = $k;

		}
		if(count($wp_query_args['tax_query']) > 1)
			$wp_query_args['tax_query']['relation'] = 'AND';

		unset($wp_query_args['taxonomy_query']);
		

		return $wp_query_args;
	}

	static function BuildQueryUrl($instance) {
		$grouping_slug = (isset($instance['grouping_slug'])) ? $instance['grouping_slug'] : false;
		if($grouping_slug && !in_array($instance['grouping_slug'], array('default', 'forced')))
			if(isset($instance['grouping_taxonomy']) && $grouping_taxonomy = $instance['grouping_taxonomy'])
				if(isset($instance['taxonomy_query'][$grouping_taxonomy]) && is_array($instance['taxonomy_query'][$grouping_taxonomy]))
					$instance['taxonomy_query'][$grouping_taxonomy]['terms'] = array($instance['grouping_slug']);

		$query = self::BuildQuery($instance);

		$unsets = array('posts_per_page', 'taxonomy_norepeat', 'authors_norepeat', 'source', 'grouping_forced', 'grouping_taxonomy', 'grouping_contextual', 'grouping_slug');
		foreach ($unsets as $unset)
			if(isset($query[$unset]))
				unset($query[$unset]);

		if(isset($query['tax_query']) && $query['tax_query']) {
			foreach($query['tax_query'] as $tax_query)
				if(is_array($tax_query))
					$query[$tax_query['taxonomy']] = implode(',', $tax_query['terms']);

			unset($query['tax_query']);
		}

		$query = array_filter($query);

		return http_build_query($query);
	}

/**
		RENDERING
*/

	function widgetCacheMasterKey() {
		$cache_key = 'kche_' . $this->slug();

		return $cache_key;
	}

	function widgetCacheKey() {
		$cache_key = $this->widgetCacheMasterKey();

		if($this->widgetGet('grouping_contextual') || $this->widgetGet('author_contextual')) {
			$cache_key .= md5(json_encode(array($this->widgetGet('tax_query'), $this->widgetGet('author'))));
		} else if($this->widgetGet('grouping_taxonomy')) {
			$grouping_slug = $this->widgetGet('grouping_slug');
			if(!$grouping_slug || $grouping_slug == 'fixed')
				$grouping_slug = 'default';
			$cache_key .= '_' . $grouping_slug;
		}

		return $cache_key;
	}

	function widgetCacheGet() {
		$cache_key = $this->widgetCacheKey();

		return get_option($cache_key);
	}

	function widgetCacheSet($rendered) {
		$cache_key = $this->widgetCacheKey();

		if($this->widgetGet('grouping_taxonomy') || $this->widgetGet('grouping_contextual') || $this->widgetGet('author_contextual')) {
			$master_key = $this->widgetCacheMasterKey();
			$variations = get_option($master_key);
			if(!is_array($variations))
				$variations = array();
			if(!in_array($cache_key, $variations))
				$variations[] = $cache_key;

			delete_option($master_key);
			add_option($master_key, $variations, null, 'no');
		}

		delete_option($cache_key);
		add_option($cache_key, $rendered, null, 'no');
	}

	function widgetCacheClear() {
		$cache_key = $this->widgetCacheMasterKey();

		if($this->widgetGet('grouping_taxonomy') || $this->widgetGet('grouping_contextual') || $this->widgetGet('author_contextual')) {
			$variations = get_option($cache_key, array());

			foreach($variations as $variation_key) {
				delete_option($variation_key);
			}
		}
		delete_option($cache_key);
	}

	function widgetCacheReset() {
		$this->widgetCacheClear();
		$this->widgetCacheRender();
	}

	function widgetCacheRender() {

		$hasContentToCache = false;
		$view = $this->getSelectedView();

		if($view) {
			global $wp_query, $_HAS_COUNTS;
			$_HAS_COUNTS = true;
			$wp_query = $this->query();

			$hasContentToCache = $wp_query->have_posts();
			ob_start();
//			echo $this->widgetArg('before_widget'); IASD Lista de Posts não usa estes parametros.
			require $view['path'];
//			echo $this->widgetArg('after_widget'); IASD Lista de Posts não usa estes parametros.
			$rendered = ob_get_contents();
			ob_end_clean();
			wp_reset_query();
			$_HAS_COUNTS = false;
		} else {
			$rendered = '<!-- VIEW NOT DEFINED -->';
		}

		//Will not cache if it has no posts
		$this->widgetCacheSet( ($hasContentToCache) ? $rendered : '');

		return $rendered;
	}

	static function CacheUpdateSavePost($post_id) {
		global $sidebars_widgets;
		if ($post_id && wp_is_post_revision( $post_id ) )
			return;

		self::CacheUpdateLocal();
	}
	static function CacheUpdateLocal() {
		global $sidebars_widgets;
		$instances = get_option(self::option_id, array());
		$sidebars_widgets = wp_get_sidebars_widgets();
		foreach($instances as $number => $instance) {
			if($instance['source_id'] == 'local') {
				if(in_array(self::base_id . '-' . $number, $sidebars_widgets['wp_inactive_widgets']))
					continue;
				$widget = new IASD_ListaDePosts();
				$widget->_setInstance($instance);
				$widget->widgetCacheClear();
			}
		}
	}

	static function CacheUpdate() {
		global $sidebars_widgets;
		$instances = get_option(self::option_id, array());
		unset($instances['_multiwidget']);

		$instances_keys = get_option(self::option_id . '_cron');

		if(!is_array($instances_keys))
			$instances_keys = array();

		if(!count($instances_keys)) {
			foreach($instances as $number => $instance)
				if(in_array(self::base_id . '-' . $number, $sidebars_widgets['wp_inactive_widgets']))
					$instances[$number] = false;

			$instances_keys = array_keys(array_filter($instances));
		}
		shuffle($instances_keys);

		$resets = 3;

		foreach($instances_keys as $k => $number) {
			$widget = new IASD_ListaDePosts();
			$widget->_setInstance($instances[$number]);
			$widget->widgetCacheReset();
			$instances_keys[$k] = false;
			$resets--;
			if(!$resets)
				break;
		}

		$instances_keys = array_filter($instances_keys);
		delete_option(self::option_id . '_cron');
		if(count($instances_keys))
			add_option(self::option_id . '_cron', $instances_keys, null, 'no');
	}

	function widget($widget_args, $instance) {
		if(isset($instance['grouping_contextual']) && $instance['grouping_contextual']) {
			global $wp_query;
			unset($instance['taxonomy_query']);
			unset($instance['tax_query']);

			if(isset($wp_query->tax_query->queries)) {
				$instance['tax_query'] = iasdDecodeToArray($wp_query->tax_query->queries);
				$instance['grouping_taxonomy'] = $instance['tax_query'][0]['taxonomy'];
			}
		}

		if(isset($instance['author_contextual']) && $instance['author_contextual']) {
			global $wp_query;

			if(isset($wp_query->query_vars['author']))
				$instance['author'] = $wp_query->query_vars['author'];
		}

		if(!isset($instance['number']))
			$instance['number'] = $this->number;

		$this->_setInstance($instance);
		$this->_setArgs($widget_args);

		if(get_taxonomy($this->widgetGet('grouping_taxonomy')) && !$this->widgetGet('grouping_contextual'))
			wp_enqueue_script('iasd-listadeposts', PAPURL_STTC . '/js/iasdlistadeposts.js', array('jquery'), false, true);

		$rendered = $this->widgetCacheGet();

		if(!$rendered || defined('NO_WIDGET_CACHE'))
			$rendered = $this->widgetCacheRender();

		echo $rendered;
	}

	function widgetTitle() {
		$title = $this->widgetGet('title');
		if($grouping_slug = $this->widgetGet('grouping_slug')) {
			$term = get_term_by('slug', $grouping_slug, $this->widgetGet('grouping_taxonomy'));
			if($term)
				$title = $term->name;
		}
		return $this->widgetArg('before_title', '<h1>') . $title . $this->widgetArg('after_title', '</h1>');
	}

	function widgetAddClasses() {
		$params = func_get_args();
		if(!is_array($params))
			$params = array();
		$params[] = $this->widgetView('widget_class');
		$params[] = $this->widgetGroupingTaxonomyClass();
		$params[] = $this->widgetWidthClass();

		return trim(implode(' ', array_filter($params)));
	}

	function widgetWidthClass() {
		$sidebar = $this->getSidebar();
		$class = '';

		if($sidebar) {
			if(isset($sidebar['col_class'])) {
				$sidebar_class = $sidebar['col_class'];
				$view_cols = $this->widgetView('cols');
				$class = $class_selected = $this->widgetGet('width');

/*				if($sidebar_class == 'col-md-4'){
					$class = 'col-md-12'; //Se for 4 colunas dentro de 4 colunas tira-se a Classes
				} else */
				if($sidebar_class == 'col-md-8') {
					$class = ($class_selected == 'col-md-4') ? 'col-md-6' : 'col-md-12'; //Faz proporcional quando em 8
				}
			}
		}

		return $class;
	}

	function widgetSeeMoreHtml($text = '') {
		$output = '';
		if($this->widgetGet('seemore')) {
			$text = $this->widgetGet('seemore_text', $text);
			$source = $this->getCurrentSource();
			$link = $source['url'] .'?'. self::BuildQueryUrl($this->getInstance());

			$output = '<a href="'.$link.'" title="'.$text.'" class="more-link">'.$text.' »</a>';
		}
		return $output;
	}

	function widgetGroupingTaxonomyClass() {
		return ($this->widgetGet('grouping_taxonomy') && !$this->widgetGet('grouping_contextual'))
			? 'iasd-widget-config'
			: '';
	}

	function widgetGetFixedIds() {
		$items = $this->widgetGet('fixed_ids', '');

		return explode(',', $items);
	}

	function widgetGetViewsOptions($current_view = false) {
		$output = '';
		$groups = IASD_ListaDePosts_Views::GetGroups();
		$availableViews = $this->getAvailableViews();

		foreach($groups as $group => $views) {
			$pre_output = '';
			foreach($views as $viewName) {
				if(isset($availableViews[$viewName])) {
					$info = $availableViews[$viewName];
					$selected = ($current_view == $viewName) ? ' selected="selected" ' : '';
					$pre_output .= "\t".'<option value="' . $viewName . '"' . $selected . '>' . $info['description'] . '</option>' . "\r\n";
				}
			}
			if($pre_output && $group != 'all')
				$pre_output = "<optgroup label=\"".$group."\">\r\n".$pre_output."\r\n</optgroup>\r\n";
			$output .= $pre_output;
		}
		return $output;
	}

	function widgetGetGroupingTerms() {
		$taxonomy = $this->widgetGet('grouping_taxonomy');
		$tax_queries = $this->widgetGet('taxonomy_query');
		$tax_query = $tax_queries[$taxonomy];
		$terms = array();
		foreach($tax_query['terms'] as $slug)
			$terms[$slug] = get_term_by('slug', $slug, $taxonomy);

		return $terms;
	}

	function widgetAddGroupingTaxonomyHtml() {
		if(get_taxonomy($this->widgetGet('grouping_taxonomy')) && !$this->widgetGet('grouping_contextual'))
			$this->includeView('render_grouping_taxonomy.php');
	}


	static function addDummyContent() {

		if (self::checkAuthRoles()) {
		?>
			<!-- Estrutura do modal dummy-->			
			<div id="iasd-ldp-cc-container-dummy" style="display:none;">
				<div class="iasd-ldp-cc">
					<div class="iasd-ldp-cc-spinner spinner"></div>
					<div class="iasd-ldp-cc-container">
						<div class="iasd-ldp-cc-container-list"></div>
						<div class="iasd-ldp-cc-container-save">
							<div class="alignright">
								<a href="javascript:void(0);" class="button iasd-widget" id="iasd-save-widget-dummy"><?php _e('Salvar', 'iasd'); ?></a>
							<div id="div-btn-save-widget"></div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Trigger para salvar widget quando clicar no botão salvar dentro do modal dummy -->			
			<div class="overlay_save_widget"><p>Salvando widget..</p></div>
			<div id="widget_number_dummy"></div>

			<!-- Trigger para salvar widget quando clicar no botão salvar dentro do modal dummy -->			
			<script type="text/javascript">
				jQuery( "#iasd-save-widget-dummy" ).click(function() {
					var widgetNumber = jQuery("#widget_number_dummy").html();
					jQuery( "#widget-iasd_ldp-" + widgetNumber + '-savewidget').click();
					jQuery( ".tb-close-icon").click();
					jQuery(document.body).css({ 'cursor': 'wait' });
					jQuery( ".overlay_save_widget" ).show();
				});

			</script>

		<?php
		}
	}

	static function checkAuthRoles() {


		if ( self::availableOnlyToAdmin() ) {
			if (is_user_logged_in() && current_user_can('administrator')) {
				return true;
			} else {
				return false;
			}		

		} else {

			if (is_user_logged_in() && ( current_user_can('editor') || current_user_can('administrator') ) ) {
				return true;
			} else {
				return false;
			}
		}

	}

	static function availableOnlyToAdmin() {

		$res = false;
		// Lista de sites onde botão de edição é registro a admins
		$sites = array( 'downloads', 'noticias', 'videos' );
		$current_site_url = get_site_url('home');

		foreach ($sites as $site) {

			if ( ( strpos($current_site_url,$site) !== false ) ) {
				$res = true;
				break;
			}
		}

		return $res;
	}

	function addEditButton() {

		if(self::checkAuthRoles()){


			$widget_number = $this->number;
			
			$widget_iasd_ldp 	= get_option('widget_iasd_ldp');
			$widget_object 		= $widget_iasd_ldp[$widget_number]; 

			?>

			<form>

				<input type="hidden" value="<?php echo $widget_object['sidebar']; ?>" name="widget-iasd_ldp[<?php echo $widget_number;?>][sidebar]">
				<input type="hidden" value="<?php echo $widget_object['secret']; ?>" name="widget-iasd_ldp[<?php echo $widget_number;?>][secret]">
				<input type="hidden" value="<?php echo $widget_object['title']; ?>" name="widget-iasd_ldp[<?php echo $widget_number;?>][title]">
				<input type="hidden" value="<?php echo $widget_object['source_id']; ?>" name="widget-iasd_ldp[<?php echo $widget_number;?>][source_id]">
				<input type="hidden" value="<?php echo $widget_object['post_type']; ?>" name="widget-iasd_ldp[<?php echo $widget_number;?>][post_type]">
				
				<?php 

					if (is_array($widget_object['taxonomy_query'])){
						foreach ($widget_object['taxonomy_query'] as $tax => $terms_array) {
	 						foreach ($terms_array['terms'] as $term) {				
				?>

				<input type="hidden" value="<?php echo $term;?>" name="widget-iasd_ldp[<?php echo $widget_number;?>][taxonomy_query][<?php echo $tax; ?>][terms][]">
				
				<?php
					 		} 
						}
					}
				?>

				<input type="hidden" value="<?php echo $widget_object['view']; ?>" name="widget-iasd_ldp[<?php echo $widget_number;?>][view]">
				<input type="hidden" value="<?php echo $widget_object['width']; ?>" name="widget-iasd_ldp[<?php echo $widget_number;?>][width]">
				<input type="hidden" value="<?php echo $widget_object['posts_per_page']; ?>" name="widget-iasd_ldp[<?php echo $widget_number;?>][posts_per_page]">
				<input type="hidden" value="<?php echo $widget_object['orderby']; ?>" name="widget-iasd_ldp[<?php echo $widget_number;?>][orderby]">
				<input type="hidden" value="<?php echo $widget_object['order']; ?>" name="widget-iasd_ldp[<?php echo $widget_number;?>][order]">
				<input type="hidden" value="<?php echo $widget_object['date_query']; ?>" name="widget-iasd_ldp[<?php echo $widget_number;?>][date_query]">
				<input type="hidden" value="<?php echo $widget_object['seemore_text']; ?>" name="widget-iasd_ldp[<?php echo $widget_number;?>][seemore_text]">
				<input type="hidden" value="<?php echo $widget_object['seemore_title']; ?>" name="widget-iasd_ldp[<?php echo $widget_number;?>][seemore_title]">
				<input type="hidden" value="<?php echo $widget_object['fixed_ids']; ?>" name="widget-iasd_ldp[<?php echo $widget_number;?>][fixed_ids]" class="iasd-ldp-fixed-ids">
				<input type="hidden" value="<?php echo $widget_object['custom_post']; ?>" name="widget-iasd_ldp[<?php echo $widget_number;?>][custom_post]" class="iasd-ldp-custom-post">
				
				<input type="hidden" value="iasd_ldp-<?php echo $widget_number;?>" name="widget-id">
				<input type="hidden" value="iasd_ldp" name="id_base">
				<input type="hidden" value="250" name="widget-width">
				<input type="hidden" value="200" name="widget-height">
				<input type="hidden" value="<?php echo $widget_number;?>" name="widget_number" id="widget_number">
				<input type="hidden" value="" name="multi_number">
				<input type="hidden" value="" name="add_new">
				<input type="hidden" value="<?php echo $widget_object['sidebar']; ?>" name="sidebar">
				<?php wp_nonce_field( 'save-sidebar-widgets', 'savewidgets', false ); ?>
			
				<div class="widget-control-actions">
					<input type="button" id="widget-iasd_ldp-<?php echo $widget_number;?>-savewidget" class="save_widget_external" style="display:none;">
				</div> 

				<div>
					<a href="#TB_inline?width=600&height=400&inlineId=iasd-ldp-cc-container-dummy" id="btn-open-dummy-<?php echo $widget_number;?>" class='iasd-widget-contents-externo'> </a>
				</div>
			</form>	

			<script type="text/javascript">
				jQuery( "#btn-open-dummy-<?php echo $widget_number;?>" ).click(function() {
					jQuery("#widget_number_dummy").html("<?php echo $widget_number;?>");
				});
			</script>

			<?php
		}
	}

	function jsonSearchGET($query_args) {
		
		$wp_query = $this->query($query_args);
		
		echo '<ol style="list-style-position: outside; padding:0px; margin-left: 0;">';

		$i = 0;
		while($wp_query->have_posts()) {
			$wp_query->the_post();
			global $post;
			$i++;

			$manual = (isset($post->isManual) && $post->isManual) ? true : false;

			$checkedClass = ($this->isPostFixed() || $manual) ? ' class="iasd-ldp-fixed"' : '';

			$post_object = new stdClass();

			if ($manual) {

				$option 		= get_option($this->slug());
				$custom_posts 	= $option['custom_posts'];
				$custom_post 	= $custom_posts[get_the_ID()];

				$post_object->ID 				= $custom_post->ID;
				$post_object->title 			= $custom_post->post_title;
				$post_object->link 				= $custom_post->guid;
				$post_object->excerpt 			= $custom_post->post_excerpt;
				$post_object->thumb_url 		= $custom_post->thumbs['full'];
				$post_object->thumb_url_small 	= $custom_post->thumbs['thumb_80x80']; 
				$post_object->thumb_url_big 	= $custom_post->thumbs['thumb_124x124'];

				$post->ID 						= $custom_post->ID;
				$post->post_title 				= $custom_post->post_title;
				$post->guid 					= $custom_post->guid;
				$post->post_excerpt 			= $custom_post->post_excerpt;
				$post->thumbs 					= $custom_post->thumbs;
				
				
			} else {
				$post_object->ID 				= get_the_ID();
				$post_object->title 			= get_the_title();
				$post_object->link 				= get_permalink();
				$post_object->excerpt 			= strip_tags(get_the_excerpt());
				$post_object->thumb_url 		= $this->getThumbnail('full');
				$post_object->thumb_url_small   = $this->getThumbnail('thumb_80x80');
				$post_object->thumb_url_big     = $this->getThumbnail('thumb_124x124');
			}
										
			?>

			<li <?php echo $checkedClass; ?> data-id="<?php echo $post_object->ID; ?>" style="backgound-color:rgba(239, 239, 239, 0.32); padding-bottom:8px; display:block; <?php if(($this->isPostFixed() && !$manual) || $manual): ?> background-color: snow; border-color: #999999; border-style: dashed;display: block;<?php endif; ?>">
				
				<!-- ==================== Short item container ====================== -->

				<div id="short<?php echo $post_object->ID; ?>_2" class="item_block">

					<div class="alignleft"><img src='<?php echo $post_object->thumb_url_small;?>' width="80px" height="80px"></img></div>
						<div style="margin-left: 95px; width: 460px;">
							<p><?php echo '<b>'. $post_object->title. '</b>'; ?></p>
						</div>
					<br class="clear" />
					
					<!-- Botão fixar / desafixar -->
					<div class="div_fixar">
						<?php if(!$this->isPostFixed() && !$manual): ?> <a href="javascript:void(0);" class="iasd-ldp-set-fixed"><img src="wp-content/plugins/pa-plugin-utilities/static/img/pin.png" class="pin not_fixed"></img></a><?php endif; ?>
						<?php if($this->isPostFixed() && !$manual): ?> <a href="javascript:void(0);" class="iasd-ldp-set-fixed"><img src="wp-content/plugins/pa-plugin-utilities/static/img/pin.png" class="pin"></a><?php endif; ?>

					</div>

					<!-- Link mostrar mais/ ocultar -->
					<div class="div_link">
						<?php if($manual): ?>
						<a href="javascript:void(0);" class="edit_post"
							data-id="<?php echo $post_object->ID; ?>"
							data-title="<?php echo esc_attr($post_object->title); ?>"
							data-link="<?php echo $post_object->link; ?>"
							data-excerpt="<?php echo esc_attr($post_object->excerpt); ?>"
							data-thumb_url="<?php echo $post_object->thumb_url; ?>" style="margin-right:10px;"><?php _e('Editar Post', 'iasd');?></a>
						<a href="javascript:void(0);" class="iasd-ldp-remove" style="margin-right:10px;"><?php _e('Remover Post', 'iasd');?></a>
						<?php endif; ?>
						<a id='bt3_<?php echo $post_object->ID; ?>' href='javascript:void(0);'><?php echo _e('mais detalhes', 'iasd'); ?></a>
					</div>

				</div>								


				<!-- ==================== Extended item container ====================== -->


				<div id="extended<?php echo $post_object->ID; ?>_2" style="display:none;">

					<div class="alignleft" style="padding: 10px"><img src='<?php echo $post_object->thumb_url_big;?>' width="124px" height="124px"></div>
						<div style="margin-left: 150px; width: 460px;">
							<p><?php echo __('Title') . ': <b>', $post_object->title, '</b>'; ?></p>
							<p><?php echo __('Link'), ': <b>', $post_object->link, '</b>'; ?></p>
							<p><?php echo __('Excerpt'), ': <b>', $post_object->excerpt, '</b>'; ?></p>
					<br class="clear" />
					
					<!-- Botão fixar / desafixar  -->
					<div class="div_fixar">
						<?php if(!$this->isPostFixed() && !$manual): ?> <a href="javascript:void(0);" class="iasd-ldp-set-fixed"><img src="wp-content/plugins/pa-plugin-utilities/static/img/pin.png" class="pin not_fixed"></img></a><?php endif; ?>
						<?php if($this->isPostFixed() && !$manual): ?> <a href="javascript:void(0);" class="iasd-ldp-set-fixed"><img src="wp-content/plugins/pa-plugin-utilities/static/img/pin.png" class="pin"></a><?php endif; ?>
					</div>

				</div>
													
				<!-- Link mostrar mais/ ocultar  -->
				<div class="div_link">
					<?php if($manual): ?>
					<a href="javascript:void(0);" class="edit_post"
						data-id="<?php echo $post_object->ID; ?>"
						data-title="<?php echo esc_attr($post_object->title); ?>"
						data-link="<?php echo $post_object->link; ?>"
						data-excerpt="<?php echo esc_attr($post_object->excerpt); ?>"
						data-thumb_url="<?php echo $post_object->thumb_url; ?>" style="margin-right:10px;"><?php _e('Editar Post', 'iasd');?></a>
					<a href="javascript:void(0);" class="iasd-ldp-remove" style="margin-right:10px;"><?php _e('Remover Post', 'iasd');?></a>
					<?php endif; ?>
					<a id='bt3_ocultar_<?php echo $post_object->ID; ?>' href='javascript:void(0);'><?php echo _e('ocultar', 'iasd'); ?></a>
				</div>
			
			</li>

			<script>
				function swap_<?php echo $post_object->ID; ?>_2() {
					
					if (document.getElementById('short<?php echo $post_object->ID; ?>_2').style.display == 'block' ||
						document.getElementById('short<?php echo $post_object->ID; ?>_2').style.display == '' ) {
					   		document.getElementById('extended<?php echo $post_object->ID; ?>_2').style.display = 'block';
					   		document.getElementById('short<?php echo $post_object->ID; ?>_2').style.display = 'none';

					} else {
						document.getElementById('extended<?php echo $post_object->ID; ?>_2').style.display = 'none';
				   		document.getElementById('short<?php echo $post_object->ID; ?>_2').style.display = 'block';									}
					}
				
					document.getElementById('bt3_<?php echo $post_object->ID; ?>').addEventListener('click',function(e){
						swap_<?php echo $post_object->ID; ?>_2();
					});
					document.getElementById('bt3_ocultar_<?php echo $post_object->ID; ?>').addEventListener('click',function(e){
						swap_<?php echo $post_object->ID; ?>_2();
					});
			</script>

	<?php
			
	}
		wp_reset_query();
		$stri = strval($i);

		echo '</ol>';

		if ($i == 0) {
			echo "<center><div><p>" .  __('Nenhum resultado encontrado.') . "</p></div></center>";
		} else if($i > 1) {
			echo "<div><p>" . __('Por uma questão de performace, apresentamos os '.$i.' resultados mais relevantes, se você não encontrou o post que procura, adicione mais termos à sua busca.') . "</p></div>";
		} 

 		die();
	}

}