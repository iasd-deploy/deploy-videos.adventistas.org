<?php
	$field = 'title';
	$value = $this->widgetGet($field);
?>
		<div class="iasdlistadeposts-form-spacer <?php echo $this->get_field_id($field . '_spacer'); ?>">
			<fieldset class="iasdlistadeposts iasd-widget-appearance-<?php echo $field; ?>-container">
				<legend><?php _e('Titulo', 'iasd'); ?>:</legend>
				
				<input class="widefat iasd-widget mandatory iasd-widget-<?php echo $field; ?>" id="<?php echo $this->get_field_id($field); ?>"
					   name="<?php echo $this->get_field_name($field); ?>"
					   type="text" value="<?php echo esc_attr($value); ?>" />

			</fieldset>
		</div>
