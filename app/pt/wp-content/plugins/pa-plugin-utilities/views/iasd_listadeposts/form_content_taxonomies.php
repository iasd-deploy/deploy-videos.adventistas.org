<?php
	$availableTaxonomies = $this->getAvailableTaxonomies();
	$taxonomy_query = $this->widgetGet('taxonomy_query', array());
	$taxonomy_norepeat = $this->widgetGet('taxonomy_norepeat', array());
	$field = 'taxonomies';

	$field3 = 'grouping_contextual';
	$value3 = $this->widgetGet($field3);
?>
		<div class="iasdlistadeposts-form-spacer <?php echo $this->get_field_id($field . '_spacer'); ?>">
			<fieldset class="iasdlistadeposts fieldsetborder iasd-widget-content-<?php echo $field; ?>-container">
				<legend><b><?php _e('Filtro de Marcadores', 'iasd'); ?></b></legend>

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
<?php 
		
		// $taxonomies = get_taxonomies(array('_builtin' => false), 'objects');

		$taxonomies = get_taxonomies(null, 'objects');

		// if(!count($availableTaxonomies))
		// 	$availableTaxonomies = array_keys($taxonomies);

		$remoteTaxonomies = IASD_Taxonomias::GetAllTaxonomies();

		foreach($taxonomies as $taxonomy_slug => $taxonomy):
			$field_name = $this->get_field_id($taxonomy_slug);
			$field = $taxonomy_slug;

			$enabled = in_array($taxonomy_slug, $availableTaxonomies);
			$class = '';

			if(!isset($taxonomy_query[$taxonomy_slug]))
				$taxonomy_query[$taxonomy_slug] = array(); 

			if(!isset($taxonomy_query[$taxonomy_slug]['terms']))
				$taxonomy_query[$taxonomy_slug]['terms'] = array();

			if(!isset($taxonomy_query['current_term']))
				$taxonomy_query['current_term'] = '';

			if(!in_array($taxonomy_slug, $remoteTaxonomies)) {
				$class = ' iasd-ldp-localonly ';
				if($this->widgetGet('source_id') != 'local')
					$enabled = false;
			}

			if($value3){
				$enabled = false;
			}


?>

				<div class="iasdlistadeposts-form-spacer-sub <?php echo $class; echo $this->get_field_id('taxonomy_query_'.$taxonomy_slug . '_spacer'); ?> <?php echo $taxonomy_slug;?>">
					<fieldset class="iasd-widget-content-<?php echo $field; ?>-container fieldsetborder iasd-ldp-wide-legend" <?php if(!$enabled) echo 'disabled="disabled"'; ?>>
						<legend class="title inline-edit-categories-label inline-edit-categories-label-<?php echo $taxonomy_slug; ?>">
							<label class="alignright">
								<input id="<?php echo $this->get_field_id('taxonomy_norepeat'); ?>"
									name="<?php echo $this->get_field_name('taxonomy_norepeat]['); ?>"
									type="checkbox" value="<?php echo $taxonomy_slug; ?>" 
									<?php if(in_array($taxonomy_slug, $taxonomy_norepeat)) echo 'checked="checked"'; ?> />
									<b><?php _e('Não repetir', 'iasd'); ?></b>
							</label>
							<?php
								echo $taxonomy->label; 
								$count = count($taxonomy_query[$taxonomy_slug]['terms']);
								$count_hidden = (!$count) ? 'style="display:none;"' : '';
								echo ' <span class="count"'.$count_hidden.'>('.$count.')</span>'; 
								if(isset($instance['current_term']) && $instance['current_term'] == $taxonomy_slug) echo ' <b>*</b>'; 
							?>
							<a id="<?php echo $field_name; ?>-hide" class="taxonomy-hide" style="display: none;" href="">[-]</a>
							<a id="<?php echo $field_name; ?>-show" class="taxonomy-show" href="">[+]</a>
						</legend>
						<ul id="<?php echo $field_name; ?>-list" data-taxonomy="<?php echo $taxonomy_slug; ?>" style="display: none;"
							class="taxonomy-checkbox-list iasdlistadeposts-content-taxonomy-<?php echo $taxonomy_slug;?>" 
							data-values="<?php echo implode(',', $taxonomy_query[$taxonomy_slug]['terms']); ?>">
<?php
			global $wp_version;

			if ( floatval($wp_version) >= 4.4 ){
				$walker = new IASD_Checklist_Walker($this->get_field_name('taxonomy_query['.$taxonomy_slug.'][terms]'), 
				$this->get_field_id('taxonomy_query-'.$taxonomy_slug.'-terms'));
	
			} else {
				$walker = new IASD_Checklist_Walker($this->get_field_name('taxonomy_query]['.$taxonomy_slug.'][terms'), 
				$this->get_field_id('taxonomy_query-'.$taxonomy_slug.'-terms'));
			}	

			wp_terms_checklist(null, 
				array('taxonomy' => $taxonomy_slug, 'selected_cats' => $taxonomy_query[$taxonomy_slug]['terms'], 
					'checked_ontop' => false,
					'walker' => $walker));
?>
						</ul>
					</fieldset>
				</div>
<?php 	endforeach; ?>
			</fieldset>
		</div>

