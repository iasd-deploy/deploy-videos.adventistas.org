<?php

add_action('widgets_init', array('NT_Links', 'Init'));

class NT_Links extends WP_Widget {
	
	function __construct() {
		$widget_ops = array('classname' => __CLASS__, 'description' => __('Links para a TV e Rádio Novo Tempo.', 'iasd') );
		parent::__construct(__CLASS__, __('IASD: Novo Tempo', 'iasd'), $widget_ops);
	}

	function form($instance) { }

	static function Init() {
		register_widget(__CLASS__);
	}

	function update($new_instance, $old_instance) {
		if(!count($new_instance))
			$new_instance = $old_instance;

		return $new_instance;
	}
	
	function demoWidget() {
		$this->widget(false, false);
	}

	function widget($args, $instance){
		$lang = WPLANG;

		//Option da radio default definida pelo administrador
		$default_radio = get_option('xtt_default_radio');
		//Option da tv default definida pelo administrador
		$default_tv = get_option('xtt_default_tv');
			
		if (empty($default_radio))
		 	$default_radio = 'brasil';

		switch ($lang) {
			case "pt_BR":
				$radio = plugins_url() . "/pa-plugin-utilities/static/html/player_radiont.php?lang=pt_BR&radio=".$default_radio."&dir=" . get_stylesheet_directory_uri();
				$tv = plugins_url() . "/pa-plugin-utilities/static/html/player_tvnt.php?lang=pt_BR&tv=".$default_tv."&dir=" . get_stylesheet_directory_uri();
				break;
			case "es_ES":
				$radio = plugins_url() . "/pa-plugin-utilities/static/html/player_radiont.php?lang=es_ES&radio=".$default_radio."&dir=" . get_stylesheet_directory_uri();
				$tv = plugins_url() . "/pa-plugin-utilities/static/html/player_tvnt.php?lang=es_ES&tv=".$default_tv."&dir=" . get_stylesheet_directory_uri();
				break;
			default:
				$radio = plugins_url() . "/pa-plugin-utilities/static/html/player_radiont.php?lang=pt_BR&radio=".$default_radio."&dir=" . get_stylesheet_directory_uri();
				$tv = plugins_url() . "/pa-plugin-utilities/static/html/player_tvnt.php?lang=pt_BR&tv=".$default_tv."&dir=" . get_stylesheet_directory_uri();
				break;
		}
?>
<div class="iasd-widget iasd-widget-novo_tempo col-md-4">
	<h1><?php _e('Novo tempo', 'iasd');?></h1>
	<div class="well">
		<a href="javascript:void(window.open('<?php echo $tv; ?>','page_tv','toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=810,height=460'));" target="_blank" title="<?php _e('Assista a TV Novo Tempo', 'iasd');?>"><?php _e('Assista a TV Novo Tempo', 'iasd');?></a>
		<a href="javascript:void(window.open('<?php echo $radio; ?>','page_radio','toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no,width=420,height=359'));" title="<?php _e('Sintonize a Rádio Novo Tempo online', 'iasd');?>"><?php _e('Sintonize a Rádio Novo Tempo online', 'iasd');?></a>
	</div>
	<div class="alert alert-danger">
		<strong>Atenção!</strong> O tamanho do Widget selecionado é incompatível com esta Sidebar. Por favor, reveja suas configurações.
	</div>
</div>
<?php
	}
}
