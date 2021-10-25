<?php 
	
	function wp_cron_banner_f7(){
		get_values_api_f7(false);
	}
	 
	//Função que busca o arquivo json na pagina passada por paramentro
	function fetchData_f7($url){

		$result = wp_remote_retrieve_body(wp_remote_get($url));
		return $result;
  }

    //Chama a funcão que traz o json e decodifica os dados, tambem adualiza a variavel options banner_json_f7.
    function get_values_api_f7($opt){

    	//Identifica a linguagen do site, para que seja feito a consulta no site de tax correto.
    	$lang = substr(get_locale(), 0, 2);
    	if($lang == "pt"){
    			$result = fetchData_f7("https://api.feliz7play.com/v3/pt/CategoryVideos/-1?page=1");
      	} else {
      		$result = fetchData_f7("https://api.feliz7play.com/v3/es/CategoryVideos/-1?page=1");
      	}
				$result = json_decode($result);

				$itens = array();

				foreach($result->Medias as $value){
					$ThumbUrl = $value->ThumbUrl;
					$Title = $value->Title;
					$VideoUrl = $value->VideoUrl;

					$item = ['Title'=> $Title, 'ThumbUrl' =>	$ThumbUrl, 'VideoUrl'=>	$VideoUrl ];

					array_push($itens, $item);
				}

			//	pconsole($itens);
			

				update_option('banner_json_f7', $itens);

      	if($opt){
      		
      		return $itens;
      		
      	} else {
      		
      		return;
      	}
        
    }

    //Chama a funcão que traz o json e decodifica os dados.
    function get_values_f7(){

			get_values_api_f7(true);
			
			$json = get_option('banner_json_f7');

    	if(!$json){
    		$json = get_values_api_f7(true);
    	} 
    	
    	return $json;
    	
		}
		
