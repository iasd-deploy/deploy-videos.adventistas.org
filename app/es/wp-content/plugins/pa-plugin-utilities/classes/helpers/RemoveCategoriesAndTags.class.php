<?php

class RemoveCategoriesAndTags {

	public static function RemoveCategoriesAndTagsFromMenu() {
		if ( !current_user_can('manage_options') ) {
			remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=category');
			remove_submenu_page('edit.php', 'edit-tags.php?taxonomy=post_tag');
		}
	}

	public static function RemoveCategoriesAndTagsMetaBox() {
		if ( !current_user_can('manage_options') ) {
			remove_meta_box( 'categorydiv' , 'post' , 'normal' ); 
			remove_meta_box( 'tagsdiv-post_tag' , 'post' , 'normal' ); 
		}
	}
}

add_action('admin_menu', array('RemoveCategoriesAndTags', 'RemoveCategoriesAndTagsFromMenu'));
add_action('admin_menu', array('RemoveCategoriesAndTags', 'RemoveCategoriesAndTagsMetaBox'));
