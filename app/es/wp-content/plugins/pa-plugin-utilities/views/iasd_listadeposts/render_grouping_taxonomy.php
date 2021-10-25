<?php
	$taxonomy = get_taxonomy($this->widgetGet('grouping_taxonomy'));
	$terms = $this->widgetGetGroupingTerms();

	$isOpen = false;
	$isForced = $this->widgetGet('grouping_forced');
	$grouping_slug = $this->widgetGet('grouping_slug');

	if($grouping_slug == 'forced') {
		$isForced = true;
		$grouping_slug = 'default';
		$isOpen = true;
	}
?>
<!-- begin of grouping -->
<div class="config <?php if($isOpen) echo 'open'; ?>">
	<a href="#" title="Clique para escolher as informações que sua região" class="toggle-config-link"><?php _e('Escolher', 'iasd'); ?></a>
	<div class="well">
		<form role="form">
			<div class="form-group">
				<label for="selectRegion"><?php echo __('Escolher', 'iasd'), ' ', $taxonomy->labels->singular_name; ?>:</label>
				<div class="custom-select">
					<select id="selectRegion" name="grouping_slug">
						<optgroup label="<?php echo $taxonomy->labels->name, ' ', __('disponíveis', 'iasd'); ?>">
<?php
	if(!$isForced)
		echo '<option value="">' . __('Todos', 'iasd') . '</option>';

	foreach($terms as $slug => $term):
		$selected = ($slug == $grouping_slug) ? 'selected="selected"' : '';
?>
							<option value="<?php echo $slug; ?>" <?php echo $selected; ?>><?php echo $term->name;?></option>
<?php endforeach; ?>
						</optgroup>
					</select>
				</div>
			</div>
			<button type="submit" class="btn btn-default">Mostrar notícias</button>
			<input type="hidden" name="action" value="iasd-listadeposts-refresh" />
			<input type="hidden" name="widget" value="<?php echo $this->slug() ?>" />
		</form>
	</div>
</div>
<!-- end of grouping -->