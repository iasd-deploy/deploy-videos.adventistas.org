<?php

class RemoveColumns {

	public static function ResetColumnsFilter( $columns ) {
		if (SITE != 'videos') {
			unset($columns['categories']);
		}

		unset($columns['comments']);
		unset($columns['tags']);
		unset($columns['likes']);

		if ( function_exists('wpseo_init') ) {
			unset($columns['wpseo-score']);
			unset($columns['wpseo-title']);
			unset($columns['wpseo-metadesc']);
			unset($columns['wpseo-focuskw']);
		}
		
		return $columns;
	}
}

add_filter('manage_edit-page_columns', array('RemoveColumns', 'ResetColumnsFilter'));
add_filter('manage_edit-post_columns', array('RemoveColumns', 'ResetColumnsFilter'));
add_filter('manage_edit-qa_faqs_columns', array('RemoveColumns', 'ResetColumnsFilter'));
add_filter('manage_edit-lideres_columns', array('RemoveColumns', 'ResetColumnsFilter'));
add_filter('manage_edit-contatos_columns', array('RemoveColumns', 'ResetColumnsFilter'));
add_filter('manage_edit-projetos_columns', array('RemoveColumns', 'ResetColumnsFilter'));
add_filter('manage_edit-testemunhos_columns', array('RemoveColumns', 'ResetColumnsFilter'));
add_filter('manage_edit-colunas_columns', array('RemoveColumns', 'ResetColumnsFilter'));
add_filter('manage_edit-pa_image_gallery_columns', array('RemoveColumns', 'ResetColumnsFilter'));
add_filter('manage_edit-pa_video_gallery_columns', array('RemoveColumns', 'ResetColumnsFilter'));
add_filter('manage_edit-release_columns', array('RemoveColumns', 'ResetColumnsFilter'));
add_filter('manage_edit-revista-adventista_columns', array('RemoveColumns', 'ResetColumnsFilter'));