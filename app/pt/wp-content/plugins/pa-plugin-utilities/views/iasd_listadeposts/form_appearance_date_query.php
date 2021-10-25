<?php
	$field = 'date_query';
	$default = $this->widgetView($field);
	$value = $this->widgetGet($field);
?>
		<div class="iasdlistadeposts-form-spacer <?php echo $this->get_field_id($field . '_spacer'); ?>">
			<fieldset class="iasdlistadeposts iasd-widget-appearance-<?php echo $field; ?>-container">
				<legend><?php _e('Data de Publicação', 'iasd'); ?>:</legend>

				<select class="widefat iasd-widget iasd-widget-appearance-<?php echo $field; ?>"
						id="<?php echo $this->get_field_id($field); ?>"
						name="<?php echo $this->get_field_name($field); ?>">
					<option value=""><?php _e('Não filtrar', 'iasd'); ?></option>
					<option value="-1 week"  <?php if ($value == '-1 week') echo 'selected="selected" ';?>><?php _e('Última semana', 'iasd'); ?></option>
					<option value="-15 days" <?php if ($value == '-15 days') echo 'selected="selected" ';?>><?php _e('Última quinzena', 'iasd'); ?></option>
					<option value="-1 month" <?php if ($value == '-1 month') echo 'selected="selected" ';?>><?php _e('Último mês', 'iasd'); ?></option>
					<option value="-3 month" <?php if ($value == '-3 month') echo 'selected="selected" ';?>><?php _e('Últimos 3 meses', 'iasd'); ?></option>
					<option value="-6 month" <?php if ($value == '-6 month') echo 'selected="selected" ';?>><?php _e('Últimos 6 meses', 'iasd'); ?></option>
					<option value="-1 year"  <?php if ($value == '-1 year') echo 'selected="selected" ';?>><?php _e('Último ano', 'iasd'); ?></option>
				</select>
			</fieldset>
		</div>