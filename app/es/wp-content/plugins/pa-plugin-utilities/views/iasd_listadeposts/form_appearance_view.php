<?php
	$field = 'view';
	$value = $this->widgetGet($field);
	$sidebar = $this->getSidebar();
?>
		<div class="iasdlistadeposts-form-spacer <?php echo $this->get_field_id($field . '_spacer'); ?>">
			<fieldset class="iasdlistadeposts iasd-widget-appearance-<?php echo $field; ?>-container">
				<legend><?php _e('Formato', 'iasd'); ?>:</legend>
<?php
		$output = '';
		if($sidebar && isset($sidebar['col_class'])) {
			$sidebarColClass = $sidebar['col_class'];
			
			$output = $this->widgetGetViewsOptions($value);
		}
?>
				<select class="widefat iasd-widget mandatory iasd-widget-appearance-view"
						id="<?php echo $this->get_field_id($field); ?>"
						name="<?php echo $this->get_field_name($field); ?>">
					<option value=""><?php _e('Selecione...', 'iasd'); ?></option>
<?php echo $output; ?>
				</select>
			</fieldset>
		</div>