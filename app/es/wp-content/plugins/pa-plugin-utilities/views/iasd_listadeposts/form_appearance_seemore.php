<?php

		$field1 = 'seemore';
		$value1 = $this->widgetGet($field1, 1);
		$field2 = 'seemore_text';
		$value2 = $this->widgetGet($field2, __('Veja Mais', 'iasd'));
		$field3 = 'seemore_title';
		$value3 = $this->widgetGet($field3, '');

		$visible = $this->mayHaveSeeMore();
?>
		<div class="iasdlistadeposts-form-spacer <?php echo $this->get_field_id($field1 . '_spacer'); ?>">
			<fieldset class="iasdlistadeposts fieldsetborder iasd-widget-appearance-<?php echo $field1; ?>-container" <?php if(!$visible) echo 'disabled="disabled"'; ?>>
				<legend>
					<label>
						<input
							class="iasd-widget-appearance-seemore"
							id="<?php echo $this->get_field_id($field1); ?>"
							name="<?php echo $this->get_field_name($field1); ?>"
							type="checkbox" value="1" 
							<?php if($value1) echo 'checked="checked"'; ?> />
						<?php _e('Veja mais', 'iasd'); ?>
					</label>
				</legend>
				
				<p>
					<?php _e('Texto do link', 'iasd'); ?>
					<br />
					<input id="<?php echo $this->get_field_id($field2); ?>"
						name="<?php echo $this->get_field_name($field2); ?>"
						type="text" value="<?php echo $value2; ?>" class="iasd-widget-appearance-<?php echo $field2; ?> mandatory widefat" <?php if(!$value1) echo 'readonly="readonly" '; ?>/>
				</p>
				<p>
					<?php _e('Titulo da página destino', 'iasd'); ?>
					<br />
					<input id="<?php echo $this->get_field_id($field3); ?>"
						name="<?php echo $this->get_field_name($field3); ?>"
						type="text" value="<?php echo $value3; ?>" class="iasd-widget-appearance-<?php echo $field3; ?> mandatory widefat" <?php if(!$value1) echo 'readonly="readonly" '; ?>/>
				</p>
			</fieldset>
		</div>
