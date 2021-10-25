<?php

$items = array();
if(have_posts()) {

	while ( have_posts() ) {
		the_post();
		global $post;
		$item = array();
		$item['title'] = get_the_title();
		$item['excerpt'] = get_the_excerpt();
		$item['permalink'] = get_permalink();
		$item['date'] = get_the_date();

		$image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
		$item['thumbnail'] = (count($image)) ? $image[0] : '';

		$item['taxonomy'] = array();
		$taxonomies = get_taxonomies(array('public' => true));
		foreach($taxonomies as $taxonomy_slug) {
			$terms = wp_get_object_terms(get_the_ID(), $taxonomy_slug);
			if(!count($terms))
				continue;
			$item['taxonomy'][$taxonomy_slug] = array();
			$taxonomy = get_taxonomy($taxonomy_slug);
			$item['taxonomy'][$taxonomy_slug]['name'] = $taxonomy->label;
			$item['taxonomy'][$taxonomy_slug]['items'] = array();
			foreach($terms as $term)
				$item['taxonomy'][$taxonomy_slug]['items'][] = array('name' => $term->name, 'slug' => $term->slug);
		}

		$post_metas = apply_filters('IASD_ListaDePosts::AvailablePostTypes::PostMeta', array(), $post->post_type);

		$item['meta'] = array();
		foreach($post_metas as $post_meta) {
			$value = get_post_meta(get_the_ID(), $post_meta);
			if($value)
				$item['meta'][$post_meta] = $value;
		}

		$items[] = $item;
	}
}
echo json_encode($items);