<?php

add_action('get_footer', array('ReferralPortalWidget', 'Init'));
add_action('wp_ajax_parsereferer', array( 'ReferralPortalWidget', 'parseReferer'));
add_action('wp_ajax_nopriv_parsereferer', array( 'ReferralPortalWidget', 'parseReferer'));

add_action('wp_ajax_get_title', array( 'ReferralPortalWidget', 'GetTitle'));
add_action('wp_ajax_nopriv_get_title', array( 'ReferralPortalWidget', 'GetTitle'));

add_action('wp_head', array( 'ReferralPortalWidget', 'GetTitleOnly'));

class ReferralPortalWidget extends WP_Widget
{
	static function Init($forceReferer = false) {
		$referer = (isset($_SERVER["HTTP_REFERER"])) ? $_SERVER["HTTP_REFERER"] : $forceReferer;

		if(!$referer)
			return false;
		$url_referer = parse_url($referer,PHP_URL_HOST);
		$url_request = parse_url(get_bloginfo( 'url' ),PHP_URL_HOST);

		$domain_referer = ReferralPortalWidget::getDomain($url_referer);
		$domain_request = ReferralPortalWidget::getDomain($url_request);

		if((!empty($url_referer) && $url_referer != $url_request) || $forceReferer){
			if( ($domain_referer==$domain_request) || $forceReferer ){
				wp_enqueue_script('portal_sticker', get_stylesheet_directory_uri().'/static/js/portal-sticker.js', '', false, true);
				$js_variables = array(
					'referer' => base64_encode($referer),
					'baseurl' => get_bloginfo( 'url' )
				);
				wp_localize_script( 'portal_sticker', 'url', $js_variables );
			?>
				<div id="sticker-portal">
					<a href="javascript:;"><span class="icon"></span></a>
					<div>
						<a href="<?php echo $referer; ?>" title="VOLTAR PARA: <?php echo $url_request; ?>">VOLTAR PARA: <br>
							<span><?php echo $url_request ?></span>
						</a>
					</div>
				</div>
			<?php
			}
		}
	}

	static function GetTitleOnly() {
		if(isset($_REQUEST['iasd_title_only']))
			die;
	}
	static function GetTitle() {
		echo json_encode(array('title' => get_bloginfo( 'blogname', 'display' )));
	}

	static function getDomain($host)
	{
		if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $host, $regs)) {
			return $regs['domain'];
		}
		return false;
	}

	static function parseReferer(){
		$url = base64_decode($_GET['url']);
		if(!empty($url)){
			if(substr($url, -1) != '/')
				$url .= '/';

			//$url .= 'wp-admin/admin-ajax.php?action=get_title';
			$url .= '?iasd_title_only';
			$requestResult = wp_remote_get($url);
			if(substr($requestResult['body'], -1) == '0')
				$requestResult['body'] = substr($requestResult['body'], 0, -1);
			$requestBody = $requestResult['body'];

			/*$encodedData = substr($requestBody, 0, -1);

			if(strlen($requestBody)) {
				$decodedData = json_decode($requestBody);
				$decodedData = (array) $decodedData;
				if(isset($decodedData['title'])) {
					$title = $decodedData['title'];
					$trimmedTitle = apply_filters('trim', $title, 45, '...');			
					echo json_encode(array('title'=>$trimmedTitle, 'fullTitle' => $title));
				}
			}*/
			
			preg_match("/<title>([^<]+)<\/title>/", $requestBody, $matches);
			if(count($matches) == 2) {
				$title = trim($matches[1]);
				$trimmedTitle = apply_filters('trim', $title, 45);			
				echo json_encode(array('title'=>$trimmedTitle, 'fullTitle' => $title));
			}
			
			die;
		}
	}
}