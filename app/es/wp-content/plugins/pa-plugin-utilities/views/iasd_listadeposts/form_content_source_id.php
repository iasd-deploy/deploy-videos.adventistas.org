<?php
	$field = 'source_id';
	$value = $this->widgetGet($field, 'local');
?>
		<div class="iasdlistadeposts-form-spacer <?php echo $this->get_field_id($field . '_spacer'); ?>">
			<fieldset class="iasdlistadeposts iasd-widget-content-<?php echo $field; ?>-container">
				<legend><?php _e('Fonte de conteúdo', 'iasd'); ?>:</legend>

				<select class="widefat iasd-widget mandatory iasd-widget-content-<?php echo $field; ?>" id="<?php echo $this->get_field_id($field); ?>"
						name="<?php echo $this->get_field_name($field); ?>">
<?php
	$sources = $this->getBasicSources();
	foreach ($sources as $source => $info) {
		$selected = ($value == $source) ? ' selected="selected" ' : '';
		echo '<option value="', $source, '"', $selected, '>', $info['name'],'</option>', "\r\n";
	}
?>
				</select>

			</fieldset>
		</div>
