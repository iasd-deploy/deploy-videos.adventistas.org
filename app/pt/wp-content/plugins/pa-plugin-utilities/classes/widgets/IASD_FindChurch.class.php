<?php

add_action('widgets_init', array('IASD_FindChurch', 'Init'));

class IASD_FindChurch extends WP_Widget
{
	function __construct()
	{
		$widget_ops = array('classname' => __CLASS__, 'description' => __('Formulário e Link para o site Encontre uma igreja.', 'iasd') );
		parent::__construct(__CLASS__, __('IASD: Encontre uma Igreja', 'iasd'), $widget_ops);
	}

	function form($instance)
	{
		self::UpdateChurchCount();
		$instance = wp_parse_args($instance, array( 'width' => 'col-md-4') );
?>
		<div>
			<p class="widget-singlegallerywidget">
				<?php
					$defaultTermId = isset($instance['term_id']) ? $instance['term_id'] : get_option('paheader_sede');
					wp_dropdown_categories(array('taxonomy' => IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS,
							'selected' => (!empty($defaultTermId)) ? $defaultTermId : 0,
							'hide_empty' => 0,
							'id' => $this->get_field_id('term_id'),
							'name' => $this->get_field_name('term_id')));
				?>

				<div>
					<label for="<?php echo $this->get_field_id('width'); ?>"><?php _e( 'Disposição:' ); ?></label>
					<select name="<?php echo $this->get_field_name('width'); ?>" id="<?php echo $this->get_field_id('orderby'); ?>" class="widefat">
						<option value="col-md-4"<?php selected( $instance['width'], 'col-md-4' ); ?>><?php _e( '1/3 de coluna' ); ?></option>
						<option value="col-md-12"<?php selected( $instance['width'], 'col-md-12' ); ?>><?php _e( 'Largura total' ); ?></option>
					</select>
				</div>

			</p>
		</div>
<?php
	}
	static function Init() {
		register_widget(__CLASS__);
	}
	function update($new_instance, $old_instance)
	{
		if(!count($new_instance))
			$new_instance = $old_instance;
		self::UpdateChurchCount();
		return $new_instance;
	}
	function demoWidget() {
		$this->widget(false, false);
	}
	function UpdateChurchCount() {
		$instances = get_option('widget_' . strtolower(__CLASS__), array());

		foreach($instances as $k => $v) {
			$instance = $instances[$k];
			if(!is_array($instance))
				continue;
			if(!isset($instance['term_id']))
				continue;

			$term = get_term($instance['term_id'], IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS);

			$response = wp_remote_get('https://igrejas.adventistas.org/api/qtd/' . strtoupper($term->slug));
			if ( !is_wp_error($response) && isset($response['body']) && 200 == $response['response']['code']) {
				$numeroDeIgrejas = intval($response['body']);
				if($numeroDeIgrejas) {
					$instances[$k]['count'] = $numeroDeIgrejas;
				}
			}
		}

		update_option('widget_' . strtolower(__CLASS__), $instances);
	}

    function widgetWidthClass() {
        $widgets = get_option('widget_iasd_findchurch');
        return $widgets[$this->number]['width'];
    }

	function widget($args, $instance)
	{
		
		// Identifica qual o idioma do site afim de direcionar as consultas de encontre uma igreja para o site do respectivo idioma.
		$idioma = explode("_",get_locale());

		if ($idioma[0] == "es")
		{
			$actionUrl = "https://iglesias.adventistas.org/es/Mapa";
		}
		else 
		{
			$actionUrl = "https://igrejas.adventistas.org/pt/Mapa";
		}

		$count = (isset($instance['count'])) ? $instance['count'] : false;

		$sede = get_term($instance['term_id'], IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS);

		if (empty($sede)) $sede = get_option('paheader_sede');

		if(!empty($sede)) {
			$sede = get_term($sede, IASD_Taxonomias::TAXONOMY_SEDES_REGIONAIS);

			if ($sede) $sede = $sede->name;

		} else {
			$sede = __('Divisão Sul Americana', 'iasd');
		}


		if ($count == 0){
			$estaoLocalizadas = __('Nenhuma Igreja Adventista localizada', 'iasd');
		} else if ($count == 1){
			$estaoLocalizadas = __('Igreja Adventista está localizada', 'iasd');
		} else {
			$estaoLocalizadas = __('Igrejas Adventistas estão localizadas', 'iasd');
		}



?>
		<div class="iasd-widget iasd-widget-find_church <?php if($instance['width'] == 'col-md-12'){ echo 'col-md-12'; }else{ echo 'col-md-4'; }; ?>">
			<h1><?php _e('Encontre uma igreja', 'iasd');?></h1>
			<div class="well">
				<div class="info">
					<h2><?php printf('<span>%s</span><br />%s '. __('na', 'iasd') .' %s</h2>', $count, $estaoLocalizadas, $sede); ?></h2>
				</div>
				<form action="<?php echo $actionUrl;?>" method="GET" target="_blank">
					<label><?php _e('Encontre a mais próxima de você:', 'iasd'); ?></label>
					<div class="input-group">
						<input type="text" class="form-control" name="q" placeholder="<?php _e('Ex: cidade, CEP, bairro', 'iasd'); ?>" >
						<span class="input-group-btn">
							<input type="submit" class="btn btn-default" type="button" value="<?php _e('Encontre', 'iasd'); ?>" />
						</span>
					</div>
				</form>
			</div>
			<div class="alert alert-danger">
				<strong>Atenção!</strong> O tamanho do Widget selecionado é incompatível com esta Sidebar. Por favor, reveja suas configurações.
			</div>
		</div>
<?php
	}
}


if ( ! wp_next_scheduled('IASD_FindChurch::UpdateChurchCount')) {
	wp_schedule_event( time(), 'daily', 'IASD_FindChurch::UpdateChurchCount');
}
add_action( 'IASD_FindChurch::UpdateChurchCount', array('IASD_FindChurch', 'UpdateChurchCount') );
