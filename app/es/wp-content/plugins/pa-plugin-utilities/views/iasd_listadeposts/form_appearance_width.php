<?php
	$field = 'width';
	$width = $this->widgetGet($field);
	$availableCols = $this->getAvailableCols();
?>
		<div class="iasdlistadeposts-form-spacer <?php echo $this->get_field_id($field . '_spacer'); ?>">
			<fieldset class="iasdlistadeposts iasd-widget-appearance-<?php echo $field; ?>-container">
				<legend><?php _e('Disposição', 'iasd'); ?>:</legend>

				<select class="widefat iasd-widget mandatory iasd-widget-appearance-width" id="<?php echo $this->get_field_id($field); ?>"
						name="<?php echo $this->get_field_name($field); ?>" >
					<option value=""><?php _e('Selecione...', 'iasd'); ?></option>
<?php
		foreach($availableCols as $col_id => $desc)
			echo '<option value="', $col_id,'"', ($width == $col_id) ? ' selected="selected" ' : '', '>', $desc, '</option>';
?>
				</select>
			</fieldset>
		</div>