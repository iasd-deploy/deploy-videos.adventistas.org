<?php
		$field = 'grouping_taxonomy';
		$value = $this->widgetGet($field, '');


		$visible = $this->mayHaveConfig();
		//$availableTaxonomies = $this->getAvailableTaxonomies();
		$availableTerms = array();
		$globalTaxonomies = IASD_Taxonomias::GetAllTaxonomies();

		$is_pending = true;

		$field1 = 'taxonomy_query';
		$value1 = $this->widgetGet($field1);

		if($value1) {
			foreach($value1 as $taxonomy_slug => $tax_query) {
				if(count($tax_query['terms']) > 1) {
					$availableTaxonomies[] = $taxonomy_slug;
					$is_pending = false;
					if($value == $taxonomy_slug) {
						$availableTerms = $tax_query['terms'];
					}
				}
			}
		}

		$field2 = 'grouping_slug';
		$value2 = $this->widgetGet($field2);

		$field3 = 'grouping_forced';
		$value3 = $this->widgetGet($field3);
?>
		<div class="iasdlistadeposts-form-spacer <?php echo $this->get_field_id($field . '_spacer'); ?>">
			<fieldset class="iasdlistadeposts fieldsetborder iasd-widget-appearance-<?php echo $field; ?>-container" <?php if(!$visible) echo 'disabled="disabled"'; ?>>
				<legend><?php _e('Opção "Escolher"', 'iasd'); ?></legend>
				<p><?php _e('Defina marcadores no <b>Filtro De Marcadores</b> antes de configurar.', 'iasd'); ?></p>

					<fieldset class="fieldset-grouping_taxonomy" <?php if(!$value) echo 'disabled="disabled"'; ?>>
						<legend><?php _e('Tipo de Marcador:','iasd'); ?></legend>
						<select class="widefat iasd-widget mandatory iasd-widget-appearance-<?php echo $field; ?>" id="<?php echo $this->get_field_id($field); ?>"
							name="<?php echo $this->get_field_name($field); ?>">
							<option value=""><?php _e('Não permitir', 'iasd'); ?></option>
<?php 

	if ( is_array($availableTaxonomies) ) {
		foreach($availableTaxonomies as $slug) {
			if(!in_array($slug, $globalTaxonomies))
				continue;

			$taxonomy = get_taxonomy($slug);

			$selected = ($value == $slug) ? '" selected="selected' : '';
			$label = (isset($taxonomy->label)) ? $taxonomy->label : $taxonomy->labels->name;
			echo '					<option value="', $slug, $selected, '">', $label,'</option>',"\r\n";
		}
	}
		
?>
						</select>
					</fieldset>
					<fieldset class="fieldset-grouping_slug" <?php if(!$value) echo 'disabled="disabled"'; ?>>
						<legend><?php _e('Padrão:', 'iasd'); ?></legend>

<?php $selected = ($value2 == 'default') ? 'selected="selected"' : ''; ?>
						<select class="widefat iasd-widget mandatory iasd-widget-appearance-<?php echo $field2; ?>" id="<?php echo $this->get_field_id($field2); ?>"
							name="<?php echo $this->get_field_name($field2); ?>">
							<option value="forced"><?php _e('Forçar Escolha', 'iasd'); ?></option>
							<option value="default" <?php echo $selected; ?>><?php _e('Mais Recentes', 'iasd'); ?></option>

<?php
	if($value) {
		echo '							<optgroup label="' . __('Marcadores', 'iasd') . '">';
		foreach ($availableTerms as $term_slug) {
			$label = get_term_by('slug', $term_slug, $value);
			$selected = ($value2 == $term_slug) ? '" selected="selected' : '';
			echo '						<option value="', $term_slug, $selected, '">', $label->name,'</option>',"\r\n";
		}
		echo '							</optgroup>';
	}
?>

						</select>
					</fieldset>
					<!--p class="forced" <?php if(!$value) echo 'disabled="disabled"'; ?>>
						<label>
							<input
								class="iasd-widget-appearance-<?php echo $field3; ?>"
								id="<?php echo $this->get_field_id($field3); ?>"
								name="<?php echo $this->get_field_name($field3); ?>"
								type="checkbox" value="1" 
								<?php if($value3) echo 'checked="checked"'; ?> />
								<?php _e('Forçar a escolha de uma opção antes de mostrar conteúdo', 'iasd'); ?>
						</label>
					</p-->
			</fieldset>
		</div>
