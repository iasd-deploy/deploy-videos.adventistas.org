<?php
	$field1 = 'orderby';
	$value1 = $this->widgetGet($field1, 'date');
	$field2 = 'order';
	$value2 = $this->widgetGet($field2, 'DESC');

	$post_type = $this->getCurrentPostType();
	$orderByOptions = ($post_type) ? $post_type['postmeta'] : $this->OrderByOptions();
?>

		<div class="iasdlistadeposts-form-spacer <?php echo $this->get_field_id($field1 . '_spacer'); ?>">
			<fieldset class="iasdlistadeposts iasd-widget-appearance-<?php echo $field1; ?>-container">
				<legend><?php _e('Ordenação', 'iasd'); ?></legend>
				<div class="alignleft">
					<select class="widefat iasd-widget mandatory iasd-widget-appearance-<?php echo $field1; ?>" id="<?php echo $this->get_field_id($field1); ?>"
						name="<?php echo $this->get_field_name($field1); ?>">
<?php

	foreach ($orderByOptions as $option => $name) {
		$selected = ($value1 == $option) ? ' selected="selected" ' : '';
		echo '					<option value="', $option, '"', $selected, '>', $name,'</option>', "\r\n";
	}
?>
					</select>
				</div>
				<div class="alignright">
					<select class="widefat iasd-widget mandatory iasd-widget-appearance-<?php echo $field2; ?>" 
						id="<?php echo $this->get_field_id($field2); ?>"
						name="<?php echo $this->get_field_name($field2); ?>">
<?php
		$orderOptions = array(
			'ASC'  => __('Crescente', 'iasd'),
			'DESC' => __('Decrescente', 'iasd'),
		);
		foreach ($orderOptions as $option => $name) {
			$selected = ($value2 == $option) ? ' selected="selected" ' : '';
			echo '					<option value="', $option, '"', $selected, '>', $name,'</option>', "\r\n";
		}
?>
					</select>
				</div>
				<br class="clear">
			</fieldset>
		</div>