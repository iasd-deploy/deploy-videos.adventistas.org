<?php
	$field = 'source_extra';
	$value = $this->widgetGet($field, '');
	$source = $this->widgetGet('source_id', 'local');
?>
		<div class="iasdlistadeposts-form-spacer <?php echo $this->get_field_id('source_extra_spacer'); ?>">
			<fieldset class="iasdlistadeposts iasd-widget-content-<?php echo $field; ?>-container" <?php if($source != 'outra') echo 'disabled="disabled"'; ?>>
				<input class="widefat iasd-widget mandatory iasd-widget-content-<?php echo $field; ?>-field" id="<?php echo $this->get_field_id('source_extra'); ?>"
						name="<?php echo $this->get_field_name('source_extra'); ?>"
						placeholder="ex: http://noticias.adventistas.org/pt/"
						type="text" value="<?php echo esc_attr($value); ?>" />
				<div class="alignright">
					<span class="spinner" style="float: left; display: none;"></span>
					<input class="button iasd-widget iasd-widget-content-<?php echo $field; ?>-check"
						data-action="<?php echo IASD_ListaDePosts_Ajax::CHECKSOURCE;?>"
					 	id="<?php echo $this->get_field_id($field); ?>-check"
						type="button" value="<?php _e('Verificar', 'iasd') ?>" />
				</div>
			</fieldset>
		</div>
