<?php 

	//Verifica se a tarrefa ja esta agendada, se ñ estiver entao faz a agenda. 
	if (!wp_next_scheduled( 'wp_update_banner_nav' ) ) {
	  wp_schedule_event( time(), 'hourly', 'wp_update_banner_nav');
	}
	// Action que é executada de hora em hora. 
	add_action( 'wp_update_banner_nav', 'wp_cron_banner' );


	function wp_cron_banner(){
		get_values_api(false);
	}
	 
	//Função que busca o arquivo json na pagina passada por paramentro
	function fetchData($url){

		$result = wp_remote_retrieve_body(wp_remote_get($url));
		return $result;
    }

    //Chama a funcão que traz o json e decodifica os dados, tambem adualiza a variavel options banner_json.
    function get_values_api($opt){

    	//Identifica a linguagen do site, para que seja feito a consulta no site de tax correto.
    	$lang = substr(get_locale(), 0, 2);
    	if($lang == "pt"){
    		$result = fetchData("http://tax.adventistas.org/pt/wp-admin/admin-ajax.php?action=banner");
      	} else {
      		$result = fetchData("http://tax.adventistas.org/es/wp-admin/admin-ajax.php?action=banner");
      	}
      	$result = json_decode($result);

      	update_option('banner_json', $result);

      	if($opt){
      		
      		return $result;
      		
      	} else {
      		
      		return;
      	}
        
    }

    //Chama a funcão que traz o json e decodifica os dados.
    function get_values(){

    	$json = get_option('banner_json');
    	
    	if (!$json){
    		$json = get_values_api(true);
    	} 
    	
    	return $json;
    	
    }


//Mostra o html acima do heder no thema
function banner_heder(){

	$json = get_values();

	if($json):

		$url = substr(get_site_url(), 7, -3);
		$link = str_replace('*SITE*', $url, $json->link);
			
	?>

		<div class="banner" style=" display: block; background-color: <?php echo $json->color; ?>;">
			<div class="container">
				<div class="row"> 
					<div class="col-md-12 text-center visible-md visible-lg">
						<a href="<?php echo $link; ?>" target="_blank" onClick="ga('adventistasGeral.send', 'event', 'Banner - <?php echo $json->alt; ?>', 'click', 'Banner - <?php echo $json->utm_sources; ?>');">
							<img src="<?php echo $json->imagem_large; ?>">
						</a>
					</div>

					<div class="col-md-12 text-center visible-sm">
						<a href="<?php echo $json->link; ?>" target="_blank" onClick="ga('adventistasGeral.send', 'event', 'Banner - <?php echo $json->alt; ?>', 'click', 'Banner - <?php echo $json->utm_sources; ?>');">
							<img src="<?php echo $json->imagem_medium; ?>">
						</a>
					</div>

					<div class="col-md-12 text-center visible-xs">
						<a href="<?php echo $json->link; ?>" target="_blank" onClick="ga('adventistasGeral.send', 'event', 'Banner - <?php echo $json->alt; ?>', 'click', 'Banner - <?php echo $json->utm_sources; ?>');">
							<img src="<?php echo $json->imagem_small; ?>" >
						</a>
					</div>

				</div>
			</div>
		</div>
	
	<?php

	endif;
}

