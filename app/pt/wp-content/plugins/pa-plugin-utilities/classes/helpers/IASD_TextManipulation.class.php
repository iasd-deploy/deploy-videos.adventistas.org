<?php

class IASD_TextManipulation {
	public static function TrimWords( $text, $char_length = 55, $more = '!!!') {
		return self::TrimChars($text, $char_length, $more, true);
	}

	public static function TrimChars( $text, $char_length = 55, $more = '!!!', $words = false) {
		if(strlen($text) <= $char_length)
			return $text;

		$text = wp_strip_all_tags( $text );
		$text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ' );
		//Remove 3 caracteres para poder colocar reticiencias (diminuir chance de quebra)
		$text = mb_substr($text, 0, $char_length - 3);

		if($words) {
			$last_space = strrpos($text, ' ');
			$last_dot   = strrpos($text, '.');
			$char_length = ($last_dot > $last_space) ? $last_dot : $last_space;
		}

		$output = mb_substr($text, 0, $char_length) . $more;
		return preg_replace('/<a[^>]+>Continue reading.*?<\/a>/i','',$output);
	}

	public static function ExcerptOrContent($excerpt = '') {
		if(!$excerpt) {
			$post = get_post();
			$excerpt = strip_tags(strip_shortcodes($post->post_content));
		}

		return $excerpt;
	}

    public static function sbt_auto_excerpt_more( $more ) {
		return '...';
	}

    public static function sbt_custom_excerpt_more( $output ) {
		return preg_replace('/<a[^>]+>Continue reading.*?<\/a>/i','',$output);
	}

    public static function Init() {
		add_filter( 'trim', array('IASD_TextManipulation', 'TrimChars'), 10, 2);
	}
}

// add_filter( 'trim', array('IASD_TextManipulation', 'TrimChars'), 10, 2);
// add_filter( 'trim_chars', array('IASD_TextManipulation', 'TrimChars'), 10, 3);
// add_filter( 'trim_words', array('IASD_TextManipulation', 'TrimWords'), 10, 3);
// add_filter( 'excerpt_more', array('IASD_TextManipulation', 'sbt_auto_excerpt_more'), 20 );
add_filter( 'get_the_excerpt', array('IASD_TextManipulation', 'sbt_custom_excerpt_more'), 20 );

// add_action('init', array('IASD_TextManipulation', 'Init'));
