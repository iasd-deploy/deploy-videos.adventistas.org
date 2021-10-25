<?php

add_action('admin_menu', array('PAGoogleAnalytics', 'AdminMenu'));
add_action('wp_footer', array('PAGoogleAnalytics', 'Render'));
add_action('admin_init', array('PAGoogleAnalytics', 'AdminInit'));
add_action('admin_init', array('PAGoogleAnalytics', 'CreateACFField'));

class PAGoogleAnalytics {
	static function AdminInit() {
		register_setting('google_analytics_option_group','google_analytics_gas');
	}

	static function AdminMenu() {
		add_options_page('Google Analytics', 'Google Analytics', 'manage_options','google-analytics-settings-page', array('PAGoogleAnalytics', 'Settings'));
	}

	static function Settings(){
		if(!current_user_can('manage_options')) {
			wp_die( __('You do not have sufficient permissions to access this page.'));
		}

		if (isset($_POST['google-analytics-settings-ga'])){
			update_option('google_analytics_option_ga',$_POST['google-analytics-settings-ga']);
		}

		echo '<div class="wrap">';
		screen_icon();
		echo '<h2>Google Analytics Settings</h2><form method="post">';
		echo '<table class="form-table"><tr><th>Google Analytic Ids (GA)</th><td><input type="text" name="google-analytics-settings-ga" value="'  . get_option('google_analytics_option_ga') . '"/><p>Separe GAs por ponto e virgula (;)</p></td></tr></table>';
		submit_button();
		echo '</form></div>';
	}

	static function Render() {
		$gas = preg_replace('/\s+/', '', get_option('google_analytics_option_ga'));
		$gas = explode(";", $gas);

		//Apenas se tiver GAs ele renderiza o resto
		//if (count($gas) > 0) {
			echo "
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga'); \n\n";

			echo "\tga('create', 'UA-51240501-1', {'name': 'adventistasGlobal'}); \n";
			echo "\tga('adventistasGlobal.send', 'pageview'); \n\n";

			echo "\tga('create', 'UA-33684424-20', {'name': 'adventistasGeral'}); \n";
			echo "\tga('adventistasGeral.send', 'pageview'); \n\n";

			if (WPLANG == 'pt_BR') {
				echo "\tga('create', 'UA-33684424-24', {'name': 'adventistasRootPT'}); \n";
				echo "\tga('adventistasRootPT.send', 'pageview'); \n\n";
			}

			if (WPLANG == 'es_ES') {
				echo "\tga('create', 'UA-33684424-25', {'name': 'adventistasRootES'}); \n";
				echo "\tga('adventistasRootES.send', 'pageview'); \n\n";
			}

			if (is_single()){
				$terms = get_the_terms( get_the_ID(), 'xtt-pa-owner');

				if( !empty($terms) ) {
		
					$term = array_pop($terms);

					$id_ga = get_field('id_ga', $term );

					echo "\tga('create', '". $id_ga ."', {'name': 'SedeProprietaria'}); \n";
					echo "\tga('SedeProprietaria.send', 'pageview'); \n\n";
				}
			}

			if (count($gas) >= 1) {
				for ($i = 0; $i < count($gas); $i++) {
					echo "\tga('create', '" . $gas[$i] . "', {name:'tracker" . $i . "'});\n";
					echo "\tga('tracker" . $i . ".send', 'pageview');\n\n";
				}
			}

			echo "</script>\n";
		//}
	}

	static function CreateACFField() {
		// var_dump(SITE);
		// die;

		if (SITE == 'noticias' || SITE == 'videos' || SITE == 'downloads' || SITE == 'dev') {
			if( function_exists('acf_add_local_field_group') ):
				acf_add_local_field_group(array (
					'key' => 'group_56f3dd8f8362c',
					'title' => 'Google Analytics',
					'fields' => array (
						array (
							'key' => 'field_56f3dd9392598',
							'label' => 'Google Analytics ID code:',
							'name' => 'id_ga',
							'type' => 'text',
							'instructions' => 'Insert the Google Analytics code here.',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array (
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'default_value' => '',
							'placeholder' => '',
							'prepend' => '',
							'append' => '',
							'maxlength' => '',
							'readonly' => 0,
							'disabled' => 0,
						),
					),
					'location' => array (
						array (
							array (
								'param' => 'taxonomy',
								'operator' => '==',
								'value' => 'xtt-pa-owner',
							),
						),
					),
					'menu_order' => 0,
					'position' => 'normal',
					'style' => 'default',
					'label_placement' => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen' => '',
					'active' => 1,
					'description' => '',
				));
			endif;
		}
	}
}

