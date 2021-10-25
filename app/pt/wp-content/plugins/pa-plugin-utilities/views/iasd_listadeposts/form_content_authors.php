<?php
		$currentSource = $this->getCurrentSource();
		$authors = isset($currentSource['authors']) ? $currentSource['authors'] : array();

		$field = 'authors';
		$authors_query    = $this->widgetGet($field, array());
		if(!is_array($authors_query))
			$authors_query = array($authors_query);
		$authors_norepeat = $this->widgetGet('authors_norepeat', false);
		$field_name = $this->get_field_id('authors');

		$field3 = 'author_contextual';
		$value3 = $this->widgetGet($field3);
?>
		<div class="iasdlistadeposts-form-spacer <?php echo $this->get_field_id($field . '-outer_spacer'); ?>">
			<fieldset class="iasdlistadeposts fieldsetborder iasd-widget-content-<?php echo $field; ?>-outer-container">
				<legend><b><?php _e('Filtro de Autores', 'iasd'); ?></b></legend>

				<p class="context">
					<label>
						<input
							class="iasd-widget-appearance-<?php echo $field3; ?>"
							id="<?php echo $this->get_field_id($field3); ?>"
							name="<?php echo $this->get_field_name($field3); ?>"
							type="checkbox" value="1" 
							<?php if($value3) echo 'checked="checked"'; ?> />
							<?php _e('Usar filtro contextual', 'iasd'); ?>
					</label>
				</p>
				<div class="iasdlistadeposts-form-spacer-sub <?php echo $this->get_field_id('authors_spacer'); ?>">
					<fieldset class="iasd-widget-content-<?php echo $field; ?>-container fieldsetborder iasd-ldp-wide-legend" <?php if($value3) echo 'disabled="disabled"'; ?>>
						<legend>
							<label class="alignright">
								<input id="<?php echo $this->get_field_id('authors_norepeat'); ?>"
									name="<?php echo $this->get_field_name('authors_norepeat'); ?>"
									type="checkbox" value="1" <?php if($authors_norepeat) echo 'checked="checked"'; ?> />
									<b><?php _e('Não repetir', 'iasd'); ?></b>
							</label>
							<span><?php _e('Filtro de Autores', 'iasd'); ?></span>
							<a id="<?php echo $field_name; ?>-hide" class="author-hide" style="display: none;" href="">[-]</a>
							<a id="<?php echo $field_name; ?>-show" class="author-show" href="">[+]</a>
						</legend>

						<ul id="<?php echo $field_name; ?>-list" style="display: none" 
							class="authors-checkbox-list iasdlistadeposts-content-authors" 
							data-values="<?php echo implode(',', $authors_query); ?>">
<?php 		foreach($authors as $id => $info) { ?>
							<li>
								<label class="selectit">
									<input value="<?php echo $id; ?>" type="checkbox" name="<?php echo $this->get_field_name('authors]['); ?>" 
										id="<?php echo $this->get_field_id('authors-'.$id); ?>" <?php if(in_array($id, $authors_query)) echo 'checked="checked"'; ?> />
										<?php echo $info['name'], "\t\n"; ?>
								</label>
							</li>
<?php 		} ?>
						</ul>
					</fieldset>
				</div>
			</fieldset>
		</div>