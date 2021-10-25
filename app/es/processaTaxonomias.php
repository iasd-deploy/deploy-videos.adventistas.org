<?php
/** 
	PARA EXECUTAR O SCRIPT, RODE NO TERMINAL (com wpcli instalado): 
 	wp eval-file processaTaxonomias.php
*/

processaTaxonomias();

function processaTaxonomias() {
	ob_start();

    $apiEndpoint = 'https://tax.adventistas.org/'. LANG .'/wp-json/wp/v2';
    $taxonomies = ['xtt-pa-colecoes', 'xtt-pa-editorias', 'xtt-pa-departamentos', 'xtt-pa-projetos', 'xtt-pa-sedes', 'xtt-pa-owner'];

    $count_A = 0;
    $count_B = 0;

	foreach ($taxonomies as &$tax){
		sleep(0.5);

        // if($count_A == 1){
        //     break;
        // }

        echo "\n\n";
        echo "\e[33mINICIANDO O PROCESSAMENTO DA TAXONOMIA \e[31m". $tax ."\n";
		
        $terms = get_terms( array( 
            'taxonomy' => $tax,
            'meta_query' => array(
                array( 
                    'key'=> 'pa_tax_id_remote',
                    'compare' => 'NOT EXISTS'
                )
            ),
        ) );

        foreach ($terms as $term){
            // if($count_B == 10){
            //     break;
            // }
            // echo "\e[39m". $term->name ."\n";
            $url = $apiEndpoint."/". $tax ."/?slug=". $term->slug ."&_fields=id,slug,name" ;

            $json = json_decode(file_get_contents($url))[0];
            echo "\e[39mTax: ". $tax ." - RemoteID: ". $json->id ." - LocalID: ". $term->term_id ." - Slug: ". $term->slug ."\n";

            update_term_meta($term->term_id, 'pa_tax_id_remote', $json->id, );
            

            $count_B++;
            ob_flush();
		    flush();
        }

        $count_A++;
        ob_flush();
		flush();
	}
}


// array(1) {
//     [0]=>
//     object(stdClass)#13375 (3) {
//       ["id"]=>
//       int(257)
//       ["name"]=>
//       string(27) "10 Dias de Oração e Jejum"
//       ["slug"]=>
//       string(25) "10-dias-de-oracao-e-jejum"
//     }
//   }