<?php

class IASD_OutputFormat {
	const PARAMETER = 'iasd_output';
	public static function TemplateInclude($template) {
		if(isset($_GET[self::PARAMETER])) {
			switch ($_GET[self::PARAMETER]) {
				case 'json':
					$template = PAPU_VIEW . DIRECTORY_SEPARATOR . 'iasd_output_format_json.php';
					break;
			}
		}
		return $template;
	}

	public static function WPHeaders($headers) {
		if(isset($_GET[self::PARAMETER])) {
			switch ($_GET[self::PARAMETER]) {
				case 'json':
					$headers['Content-Type'] = 'application/json';
					break;
			}
		}

		return $headers;
	}
	
}

add_filter('template_include', array('IASD_OutputFormat', 'TemplateInclude'), 9999);
add_filter('wp_headers', array('IASD_OutputFormat', 'WPHeaders'));


