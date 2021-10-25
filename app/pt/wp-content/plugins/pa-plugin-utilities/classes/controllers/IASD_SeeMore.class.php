<?php

class IASD_SeeMore {
	static function Title($title, $sep = '', $seplocation = '') {
		if(isset($_GET['seemore_title']) && $_GET['seemore_title']) {
			$title = $_GET['seemore_title'];
		}

		return $title;
	}

	static function WPRefArray($wp) {
		global $wp_query;

		if(isset($_GET['seemore_title']) && $_GET['seemore_title']) {
			$title = $_GET['seemore_title'];
			$wp_query->is_tax = false;
		}
	}
}

add_filter('wp_title', array('IASD_SeeMore', 'Title'), 10);
add_filter('archive_title', array('IASD_SeeMore', 'Title'), 10, 3);

add_action('wp', array('IASD_SeeMore', 'WPRefArray'), 10);