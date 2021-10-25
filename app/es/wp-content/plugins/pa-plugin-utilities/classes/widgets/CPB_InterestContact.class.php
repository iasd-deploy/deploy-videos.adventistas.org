<?php

add_action('widgets_init', array('CPB_InterestContact', 'Init'));

class CPB_InterestContact extends WP_Widget
{
	function __construct()
	{
		$widget_ops = array('classname' => __CLASS__, 'description' => __('Formulário para contato de interessados na Educação Adventista.', 'iasd') );
		parent::__construct(__CLASS__, __('CPB: Interessados em matrícula', 'iasd'), $widget_ops);
	}

	function form($instance) {
		$instance = wp_parse_args($instance, array( 'width' => 'col-md-8') );

?>	
		<div>
			<label for="<?php echo $this->get_field_id('width'); ?>"><?php _e( 'Disposição:' ); ?></label>
			<select name="<?php echo $this->get_field_name('width'); ?>" id="<?php echo $this->get_field_id('orderby'); ?>" class="widefat">
				<option value="col-md-4"<?php selected( $instance['width'], 'col-md-4' ); ?>><?php _e( '1/3 de coluna' ); ?></option>
				<option value="col-md-8"<?php selected( $instance['width'], 'col-md-8' ); ?>><?php _e( '2/3 de coluna' ); ?></option>
			</select>
		</div>
<?php

	}

	static function Init() {
		register_widget(__CLASS__);
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		if(isset($new_instance['sidebar']))
			$instance['sidebar'] = $new_instance['sidebar'];
		if(isset($new_instance['width']))
			$instance['width'] = $new_instance['width'];

		return $new_instance;
	}
	
	function demoWidget() {
		$this->widget(false, false);
	}

	function widget($args, $instance)
	{
		wp_enqueue_style('cpb_interestcontact', PAPURL_STTC . '/css/cpb_interestcontact.css');
		wp_enqueue_script('cpb_interestcontact', PAPURL_STTC . '/js/cpb_interestcontact.js', '', false, true);
		wp_enqueue_script('jquery-validate', PAPURL_STTC . '/js/jquery.validate/jquery.validate.js', array('jquery'));
?>
		<div class="iasd-widget iasd-widget-interest_contact <?= ($instance['width'] == 'col-md-8') ? 'col-md-8' : 'col-md-4'; ?>">
			<h1><?php _e('Estude conosco', 'iasd');?></h1>
			<div class="interest-contact">
				<form id="frm-matric" data-origem="widget-dsa" data-tipo="2">
					<div class="sct-matric custom-sct" id="sct-estado">
						<span class="sct-seta"></span>
						<select name="estado" id="bx-estado">
							<option value="0"><?php _e('Estado', 'iasd');?></option>
						</select>
					</div>

					<div class="sct-matric custom-sct" id="sct-cidade">
						<span class="sct-seta"></span>
						<select name="cidade" id="bx-cidade">
							<option value="0"><?php _e('Cidade', 'iasd');?></option>
						</select>
					</div>

					<div class="sct-matric custom-sct" id="sct-entidade">
						<span class="sct-seta"></span>
						<select name="id_escola" id="bx-entidade">
							<option value="0"><?php _e('Escola', 'iasd');?></option>
						</select>
					</div>

					<div class="sct-matric custom-sct" id="sct-serie">
						<span class="sct-seta"></span>
						<select name="serie" id="bx-serie">
							<option value="0"><?php _e('Ano', 'iasd');?></option>
						</select>
					</div>

					<input class="form-control inp-matric" type="text" name="nomealuno" placeholder=<?php _e('Nome', 'iasd');?>>
					<input class="form-control inp-matric" type="text" name="nomeresp" placeholder=<?php _e('Responsável', 'iasd');?>>
					<input class="form-control inp-matric" type="email" name="email" placeholder=<?php _e('Email', 'iasd');?>>
					<input class="form-control inp-matric" type="tel" name="telefone" placeholder=<?php _e('Telefone', 'iasd');?> maxlength="15" autocomplete="off">
					<div class="txt-matric">
						<textarea class="form-control" name="observacao" cols="30" rows="5" placeholder=<?php _e('Mensagem', 'iasd');?>></textarea>
					</div>

						<button type="submit" class="btn btn-default btn-matric"><?php _e('Enviar', 'iasd');?></button>
					<div class="result-matric"></div>
				</form>
			</div>
			<div class="alert alert-danger">
				<strong>Atenção!</strong> O tamanho do Widget selecionado é incompatível com esta Sidebar. Por favor, reveja suas configurações.
			</div>
		</div>
<?php
	}
}
