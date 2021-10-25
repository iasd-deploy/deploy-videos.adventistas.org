<?php

if(!class_exists('IASD_Checklist_Walker')) {
	class IASD_Checklist_Walker extends Walker_Category {
		var $enabled = true;
		var $base_id = '';

		function start_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat("\t", $depth);
			$output .= "$indent<ul class='children depth_$depth'>\n";
		}

		function end_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat("\t", $depth);
			$output .= "$indent</ul>\n";
		}

		function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
			extract($args);
			if ( empty($taxonomy) )
				$taxonomy = 'category';

			if ( $taxonomy == 'category' )
				$name = 'post_category';
			else
				$name = 'tax_input['.$taxonomy.']';

			if($this->field_name)
				$name = $this->field_name;

			$class = in_array( $category->slug, $popular_cats ) ? ' class="popular-category"' : '';
			//
			$output .= "\n<li id='{$taxonomy}-{$category->slug}'$class>\n\t" .
				'<label class="selectit">'."\n\t\t".'<input value="' . $category->slug .
				'" type="checkbox" name="'.$name.'[]" id="'.$this->base_id.'-' . $category->slug . '"' . checked( in_array( $category->slug, $selected_cats ), true, false ) .
				disabled( $this->enabled, false, false ) . ' title="' . esc_attr(apply_filters('the_category', $category->name )) . '"/> ' .
				esc_html( apply_filters('the_category', $category->name )) .
				"\n\t".'</label>' . "\n";
		}

		function end_el( &$output, $category, $depth = 0, $args = array() ) {
			$output .= "</li>\n";
		}

		function IASD_Checklist_Walker($field_name, $base_id, $enabled = true) {
			$this->field_name = $field_name;
			$this->enabled = $enabled;
			$this->base_id = $base_id;
		}
	}
}
