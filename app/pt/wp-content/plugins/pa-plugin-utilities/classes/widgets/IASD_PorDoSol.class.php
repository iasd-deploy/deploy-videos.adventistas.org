<?php

add_action( 'wp_ajax_pordosol', array( 'IASD_PorDoSol', 'Ajax' ) );
add_action( 'wp_ajax_nopriv_pordosol', array( 'IASD_PorDoSol', 'Ajax' ) );
add_action( 'widgets_init', array( 'IASD_PorDoSol', 'init' ) );

add_action( 'init', 'set_xttsol_cookie' );
add_action( 'init', 'set_xtttimezone_cookie' );


function set_xttsol_cookie() {
	if ( ! isset( $_COOKIE['xttsol'] ) ) {
		setcookie( 'xttsol', '0', strtotime( '+1 year' ), '/' );
	}
}
function set_xtttimezone_cookie() {
	if ( ! isset($_COOKIE['xtttimezone']) ) {
		setcookie( 'xtttimezone', '0', strtotime( '+1 year' ), '/' );
	}
}

function xttsol_empty(){
	if ( '0' == $_COOKIE['xttsol'] || empty($_COOKIE['xttsol'])) {
		return true;
	} else {
		return false;
	}
}

function xtttimezone_empty(){
	if ( '0' == $_COOKIE['xtttimezone'] ) {
		return true;
	} else {
		return false;
	}
}


function getIP() {

    if ($_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if($_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if($_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if($_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if($_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = '';

    $ipaddress = explode(",", $ipaddress);

    return $ipaddress[0];
}

class IASD_PorDoSol extends WP_Widget {

	function __construct(){
		$widget_ops = array( 'classname' => __CLASS__, 'description' => __( 'Apresenta o horário de entrada e saída do Sábado', 'iasd' ) );
		parent::__construct( __CLASS__, __( 'IASD: Pôr-do-sol', 'iasd' ), $widget_ops );
	}

	function formz($instance){
	}

	static function init() {
		register_widget( __CLASS__ );
		self::db_create();
	}

	function update($new_instance, $old_instance) {

		if ( ! count( $new_instance ) ) {
			$new_instance = $old_instance;
		}

		return $new_instance;
	}
	static function Ajax() {
		
		if (isset($_REQUEST['pds_la']) && isset($_REQUEST['pds_la'])) {
			unset($_COOKIE['xtttimezone']);
		}
		
		// $pds_cache = get_option('pds_cache', array());
		$pds_cache = json_decode( IASD_PorDoSol::db_get( getIP() ), true );
		$isset_pds_cache = empty($pds_cache);

		$cache_key = $_REQUEST['pds_la'].'x'.$_REQUEST['pds_lo'];

        if($cache_key == 'x'){

        	if(is_array($pds_cache)){
        		foreach ($pds_cache as $key => $value) {
					$cache_key = $key;
					break;
        		}
        	} else {
				$cache_key = '-23.5505199x-46.63330939999997';
        	}
        }
        list($pds_la, $pds_lo) = explode('x', $cache_key);

        $is_xttsol_empty = xttsol_empty();

        if (!isset($_REQUEST['pds_la']) && !isset($_REQUEST['pds_la'])) {
	        if($is_xttsol_empty) {
			   	$xml_string = file_get_contents('http://freegeoip.net/json/' . getIP());
				$resp = json_decode($xml_string);
				$pds_la = $resp->latitude;
				$pds_lo = $resp->longitude;
				$cache_key = $resp->latitude . 'x' . $resp->longitude; 
				setcookie ('xttsol', $xml_string, strtotime( '+1 month' ), '/');
			} else {
				$xttsol = $_COOKIE['xttsol'];
				$resp = json_decode(preg_replace('/\\\\/', '', $xttsol));
				$pds_la = $resp->latitude;
				$pds_lo = $resp->longitude;
				$cache_key = $resp->latitude . 'x' . $resp->longitude; 
			}
		}

		if(!isset($pds_cache[$cache_key]))
			$pds_cache[$cache_key] = array('timezone' => array(), 'sunset' => array());

		$timezone_key = date('Ymd');
		$sunset_key = $_REQUEST['pds_d'].'/'.$_REQUEST['pds_m'];

		//Requisição ao Google para ver a timezone da região e horário de verão	

		if ($pds_la == 0)
			$pds_la = '-23.5505199';
		if ($pds_lo == 0 )
			$pds_lo = '-46.63330939999997';				
		
		if(!isset($pds_cache[$cache_key]['timezone'][$timezone_key]) && strpos($pds_cache[$cache_key]['timezone'][$timezone_key],'ZERO_RESULTS') == false ||  (isset($_REQUEST['pds_la']) && isset($_REQUEST['pds_la']) ) ){
			$serialized = json_encode($_REQUEST);
			setcookie ('pordosol', $serialized, strtotime( '+1 year' ), '/');
			// $_COOKIE['pordosol'] = $serialized;
			$url_tz = 'https://maps.googleapis.com/maps/api/timezone/json?location='.$pds_la.','.$pds_lo.'&sensor=false&timestamp='.time();
			$tz_string = file_get_contents($url_tz);
			$pds_cache[$cache_key]['timezone'][$timezone_key] = $tz_string;
		} else {
			$tz_string = $pds_cache[$cache_key]['timezone'][$timezone_key];
		}

        $json_data = @$pds_cache[$cache_key]['sunset'][$sunset_key];
        if($json_data)
            if(!$json_data['friday'] || !$json_data['friday'])
                $json_data = false;

		//Pega o horário para sexta e sábado
		$tz_object = json_decode($tz_string);


		if(!$json_data || xttsol_empty()) {
			$tz = 0;

			if ($pds_la == 0)
				$pds_la = '-23.5505199';
			if ($pds_lo == 0 )
				$pds_lo = '-46.63330939999997';

			if($tz_object->status == 'OK') {
				$tz = ($tz_object->rawOffset + $tz_object->dstOffset)/3600;
				setcookie ('xtttimezone', $tz_string, strtotime( '+1 year' ), '/');

			} else {
				$tz_object = json_decode('{"dstOffset" : 3600,"rawOffset" : -10800,"status" : "OK","timeZoneId" : "America/Sao_Paulo","timeZoneName" : "Brasilia Summer Time" }');
				$tz = ($tz_object->rawOffset + $tz_object->dstOffset)/3600;
				$tz = '-3';
				setcookie ('xtttimezone', $tz_string, strtotime( '+1 year' ), '/');
			}

			$pds_dt = new DateTime();
			$pds_dt->setDate($_REQUEST['pds_y'], $_REQUEST['pds_m'], $_REQUEST['pds_d']);
			$url = 'http://www.earthtools.org/sun/'.$pds_la.'/'.$pds_lo.'/'.$pds_dt->format('d').'/'.$pds_dt->format('m').'/'.$tz.'/0';

			$xml_string = file_get_contents($url);
			$data_fri = json_decode(json_encode(simplexml_load_string($xml_string)));

			$pds_dt->modify('+1 day');
			$url = 'http://www.earthtools.org/sun/'.$pds_la.'/'.$pds_lo.'/'.$pds_dt->format('d').'/'.$pds_dt->format('m').'/'.$tz.'/0';
			$xml_string = file_get_contents($url);
			$data_sab = json_decode(json_encode(simplexml_load_string($xml_string)));

			$json_data = array('friday' => $data_fri, 'sabbath' => $data_sab);

			$pds_cache[$cache_key]['sunset'][$sunset_key] = $json_data;
			
			if (!isset($_REQUEST['pds_la']) && !isset($_REQUEST['pds_la'])) {
				$is_xttsol_empty = xttsol_empty();
			}

		}

        $json_data['widget_id'] = $_GET['widget_id'];

		if($json_data) {


			// update_option('pds_cache', $pds_cache);

			if ( !empty( $isset_pds_cache ) ){
				IASD_PorDoSol::db_set( getIP(), json_encode( $pds_cache ) ) ;
			}


			header('Content-type: application/json');
			echo json_encode($json_data);
		}

		die;
	}

	function demoWidget() {
		$this->widget(array(), array());
	}
	function widget($args, $instance) {

		$tdy = new DateTime();
		$tdy->setTime(0, 0, 0);
		$frd = clone $tdy; $frd->modify('next friday');
		if(($frd->format('U') - $tdy->format('U')) == 604800) // 7 dias, quer dizer que hoje é sexta
			$frd = $tdy;

		$sbt = clone $tdy; $sbt->modify('next saturday');
		if(($sbt->format('U') - $tdy->format('U')) == 604800) // 7 dias, quer dizer que hoje é Sábado
			$sbt = $tdy;

		$dts = ($sbt->format('U') > $frd->format('U')) ? $frd : $sbt ;
		if($dts == $sbt)
			$dts->modify('-1 day');
		// Se for Sábado, mostra a sexta-feira "ontem"

		$pds_data = (isset($_COOKIE['pordosol'])) ? $_COOKIE['pordosol'] : false;
		if($pds_data)
			$pds_data = json_decode($pds_data);
		
		$is_xttsol_empty = xttsol_empty();

		if($is_xttsol_empty) {
			$xml_string = file_get_contents('http://freegeoip.net/json/' . getIP());
			$resp = json_decode($xml_string);
			// setcookie ('xttsol', $xml_string, strtotime( '+1 month' ), '/');
			// $_COOKIE['xttsol'] = $xml_string;
		} else {
			$xttsol = $_COOKIE['xttsol'];
			$resp = json_decode(preg_replace('/\\\\/', '', $xttsol));
		}


		if (!empty($resp) && is_object($resp)){
			$pds_data = array('pds_nm' => $resp->city . ', '. $resp->region_code . ', ' . $resp->country_code, 'pds_d' => $dts->format('d'),
                                'pds_la' => $resp->latitude, 'pds_m' => $dts->format('m'),
                                'pds_lo' => $resp->longitude, 'pds_y' => $dts->format('Y'));
		}

		if( empty($pds_data) || empty($resp->city) ) {
			$pds_data = array('pds_nm' => 'São Paulo, SP, BR', 'pds_d' => $dts->format('d'),
                                'pds_la' => '-23.5489433', 'pds_m' => $dts->format('m'),
                                'pds_lo' => '-46.6388182', 'pds_y' => $dts->format('Y'));
		}

		wp_enqueue_script('moment_js', PAPURL_STTC . '/js/moment/min/moment.min.js', '', false, true);
		wp_enqueue_script('moment_lang_js', PAPURL_STTC . '/js/moment/min/langs.min.js', 'moment_js', false, true);
		wp_enqueue_script('pordosol_gmaps',  "http://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&key=".get_option('pordosol_gmaps_key', 'AIzaSyCadpOqVvah1-fa3XCfW2Vtdj1hN1PQnrw'), array(), false, true);
        wp_enqueue_script('pordosol_js', PAPURL_STTC . '/js/iasd_pordosol.js', 'pordosol_js', false, true);
?>
        <div id="<?php echo $this->get_field_id('widget'); ?>" class="iasd-widget iasd-widget-sunset iasd-widget-config col-md-4" data-day="<?php echo $pds_data['pds_d']; ?>" data-month="<?php echo $pds_data['pds_m'] - 1; ?>" data-year="<?php echo $pds_data['pds_y']; ?>">
            <h1><?php _e('Pôr-do-sol', 'iasd'); ?></h1>
            <div class="well">
                <time class="friday">
                    <span class="hour">17:45</span>
                    <span class="month"><?php echo $frd->format("d/m"); ?></span>
                    <span class="day"><?php echo ucfirst(__('Friday')); ?></span>
                </time>
                <time class="sabbath">
                    <span class="hour">17:45</span>
                    <span class="month"><?php echo $sbt->format("d/m"); ?></span>
                    <span class="day"><?php echo ucfirst(__('Saturday')); ?></span>
                </time>
                <div class="navigation">
                    <a href="#" class="nav-prev-link" title="<?php _e('Clique para ver datas anteriores', 'iasd'); ?>"><?php _e('Datas anteriores', 'iasd'); ?></a>
                    <a href="#" class="nav-next-link" title="<?php _e('Clique para ver datas próximas', 'iasd'); ?>"><?php _e('Datas próximas', 'iasd'); ?></a>
                </div>
            </div>
            <p class="location"><?php _e('Horário válido para', 'iasd'); ?> <span><?php echo $pds_data['pds_nm']; ?></span></p>
            <div class="config">
                <a href="#" title="<?php _e('Clique para trocar a cidade', 'iasd'); ?>" class="toggle-config-link"><?php _e('Trocar cidade', 'iasd'); ?></a>
                <div class="well">
                    <form role="form">
                        <div class="form-group">
                            <label for="find-city"><?php _e('Procurar cidade', 'iasd'); ?></label>
                            <input type="text" class="form-control iasd-pds-find" autocomplete="off" id="find-city" placeholder="<?php _e('Digite aqui o nome da cidade', 'iasd'); ?>">
                        </div>
                        <button type="submit" class="btn btn-default"><?php _e('Atualizar cidade', 'iasd'); ?></button>
                    </form>
                </div>
            </div>
        </div>

<?php
	}

	function db_create() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		if ( $wpdb->get_var( "SHOW TABLES LIKE 'wp_xtt_por_do_sol'" ) != 'wp_xtt_por_do_sol' ) {

			$sql = "CREATE TABLE wp_xtt_por_do_sol (
				id VARCHAR(30) NOT NULL,
				post_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				json text NOT NULL,
				UNIQUE KEY id (id)
			);";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

			dbDelta( $sql ) ;
		}
	}

	function db_get($ip) {

		global $wpdb;

		$res = wp_cache_get( 'location_' . $ip );

		if ( false === $res ) {

			$res = $wpdb->get_var( $wpdb->prepare( 'SELECT json FROM wp_xtt_por_do_sol WHERE id = %s', $ip ) );

			if ( empty( $res ) ){
				$res = false;
			}

			wp_cache_set( 'location_' . $ip , $res );
		}

		return $res;
	}

	function db_set($ip, $json) {

		global $wpdb;

		$wpdb->insert(
			'wp_xtt_por_do_sol',
			array(
				'id' => $ip,
				'json' => $json,
				'post_date' => date( 'Y-m-d H:i:s' )
			)
		);
	}

}


