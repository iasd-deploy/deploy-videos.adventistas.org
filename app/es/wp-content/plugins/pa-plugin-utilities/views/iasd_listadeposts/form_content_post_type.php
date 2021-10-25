<?php
	$field = 'post_type';
	$value = $this->widgetGet($field);
	$posts_types = $this->getAvailablePostTypes();
?>
		<div class="iasdlistadeposts-form-spacer <?php echo $this->get_field_id($field . '_spacer'); ?>">
			<fieldset class="iasdlistadeposts iasd-widget-content-<?php echo $field; ?>-container">
				<legend><?php _e('Tipo de Conteúdo', 'iasd'); ?>:</legend>

				<select class="widefat iasd-widget mandatory iasd-widget-content-<?php echo $field; ?>" id="<?php echo $this->get_field_id($field); ?>"
						name="<?php echo $this->get_field_name($field); ?>" >
					<!--option value=""><?php _e('Selecione...', 'iasd'); ?></option-->
<?php
	foreach ($posts_types as $post_type => $info) {
		$selected = ($value == $post_type) ? ' selected="selected" ' : '';
		echo '<option value="', $post_type, '"', $selected, '>', $info['name'],'</option>', "\r\n";
	}
?>
				</select>
			</fieldset>
		</div>
