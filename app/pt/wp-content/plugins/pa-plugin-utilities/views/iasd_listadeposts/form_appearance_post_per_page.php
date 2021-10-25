<?php
	$field = 'posts_per_page';
	$default = $this->widgetView('posts_per_page');
	$value = ($this->widgetView('posts_per_page_forced')) ? $default : $this->widgetGet($field, $default);
	$displayNone = ($this->widgetView('posts_per_page_forced')) ? ' style="display: none;"' : '';
?>
		<div class="iasdlistadeposts-form-spacer <?php echo $this->get_field_id($field . '_spacer'); ?>">
			<fieldset class="iasdlistadeposts iasd-widget-appearance-<?php echo $field; ?>-container"<?php echo $displayNone; ?>>
				<legend><?php _e('Quantidade de itens', 'iasd'); ?>:</legend>
				
				<input class="widefat iasd-widget mandatory iasd-widget-appearance-<?php echo $field; ?>" id="<?php echo $this->get_field_id($field); ?>"
					   name="<?php echo $this->get_field_name($field); ?>"
					   type="text" value="<?php echo esc_attr($value); ?>" />

			</fieldset>
		</div>