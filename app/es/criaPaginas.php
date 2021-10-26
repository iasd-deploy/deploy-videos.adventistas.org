<?php
/** 
	PARA EXECUTAR O SCRIPT, RODE NO TERMINAL (com wpcli instalado): 
 	wp eval-file criaPaginas.php
*/

criaPaginas();

function criaPaginas() {
	ob_start();

	$args = array(
		'taxonomy' => 'xtt-pa-sedes'
	);
	$sedes = get_terms($args);
	$user = get_user_by( 'email', 'suporte@internetdsa.com' );
	
	echo "\n\n";
	echo "POSTS A PROCESSAR: ". count($sedes);
	echo "\n\n";

	foreach ($sedes as &$sede){
		sleep(0.5);


		$post_content = '
			<!-- wp:acf/p-a-row {"id":"block_61780c101f9e5","name":"acf/p-a-row","data":{},"align":"","mode":"preview","wpClassName":"wp-block-acf-p-a-row"} -->
			<!-- wp:acf/p-a-feature-post {"id":"block_61780c141f9e6","name":"acf/p-a-feature-post","data":{"field_89b432e1":"Destacados","field_60e46c07":{"manual":"","sticky":"","taxonomies":"[\u0022xtt-pa-sedes\u0022]","terms":"[[\u0022'. $sede->slug .'\u0022]]"}},"align":"","mode":"edit","wpClassName":"wp-block-acf-p-a-feature-post"} /-->
			
			<!-- wp:acf/p-a-list-videos-column {"id":"block_61780c251f9e7","name":"acf/p-a-list-videos-column","data":{"field_afd65ba9":"Mas visto","field_979f2532":"popular","field_85304d4d":{"manual":"","sticky":"","limit":"4","taxonomies":"[\u0022xtt-pa-sedes\u0022]","terms":"[[\u0022'. $sede->slug .'\u0022]]"},"field_54273ae7":"0"},"align":"","mode":"edit","wpClassName":"wp-block-acf-p-a-list-videos-column"} /-->
			
			<!-- wp:acf/p-a-carousel-videos {"id":"block_61780c451f9e9","name":"acf/p-a-carousel-videos","data":{"field_c8122831":"Últimos vidéos","field_d01208ea":"latest","field_3ea2c3f3":{"manual":"","sticky":"","limit":"10","taxonomies":"[\u0022xtt-pa-sedes\u0022]","terms":"[[\u0022'. $sede->slug .'\u0022]]"}},"align":"","mode":"edit","wpClassName":"wp-block-acf-p-a-carousel-videos"} /-->
			
			<!-- wp:acf/p-a-feliz7-play {"id":"block_61780c3a1f9e8","name":"acf/p-a-feliz7-play","data":{"field_b12d91ad":{"data":"[{\u0022id\u0022:7558,\u0022date\u0022:\u0022\u0022,\u0022title\u0022:{\u0022rendered\u0022:\u0022Como o mundo vai acabar?\u0022},\u0022excerpt\u0022:{\u0022rendered\u0022:\u0022Como ser\\u00e1 que o mundo vai acabar? A b\\u00edblia fala sobre isso? Veremos nesse epis\\u00f3dio!\u0022},\u0022link\u0022:{\u0022title\u0022:\u0022Como o mundo vai acabar?\u0022,\u0022url\u0022:\u0022https:\\/\\/feliz7play.com\\/pt\\/c\\/camelo-na-agulha?target=como-o-mundo-vai-acabar\u0022,\u0022target\u0022:\u0022_blank\u0022},\u0022featured_media_url\u0022:{\u0022pa_block_render\u0022:\u0022https:\\/\\/files.adventistas.org\\/feliz7play\\/v2\\/sites\\/2\\/2021\\/10\\/o-fim-do-mundo-camelo-na-agulha.jpg\u0022}},{\u0022id\u0022:7499,\u0022date\u0022:\u0022\u0022,\u0022title\u0022:{\u0022rendered\u0022:\u0022O d\\u00edzimo que voltou - Libras\u0022},\u0022excerpt\u0022:{\u0022rendered\u0022:\u0022Uma s\\u00e9rie de testemunhos e relatos de milagres de f\\u00e9, com a finalidade de inspirar e motivar os adoradores em suas igrejas a cada s\\u00e1bado.\u0022},\u0022link\u0022:{\u0022title\u0022:\u0022O d\\u00edzimo que voltou - Libras\u0022,\u0022url\u0022:\u0022https:\\/\\/feliz7play.com\\/pt\\/c\\/provai-e-vede-libras\\/provai-e-vede-2021-libras?target=o-dizimo-que-voltou-libras\u0022,\u0022target\u0022:\u0022_blank\u0022},\u0022featured_media_url\u0022:{\u0022pa_block_render\u0022:\u0022https:\\/\\/files.adventistas.org\\/feliz7play\\/v2\\/sites\\/2\\/2021\\/10\\/Miniatura_ProvaieVede2021_Libras_Eps43.jpg\u0022}},{\u0022id\u0022:7489,\u0022date\u0022:\u0022\u0022,\u0022title\u0022:{\u0022rendered\u0022:\u0022O d\\u00edzimo que voltou\u0022},\u0022excerpt\u0022:{\u0022rendered\u0022:\u0022Assista essa hist\\u00f3ria e saiba mais do amor e cuidado de Deus por seus filhos.\u0022},\u0022link\u0022:{\u0022title\u0022:\u0022O d\\u00edzimo que voltou\u0022,\u0022url\u0022:\u0022https:\\/\\/feliz7play.com\\/pt\\/c\\/provai-e-vede\\/provai-e-vede-2021?target=o-dizimo-que-voltou\u0022,\u0022target\u0022:\u0022_blank\u0022},\u0022featured_media_url\u0022:{\u0022pa_block_render\u0022:\u0022https:\\/\\/files.adventistas.org\\/feliz7play\\/v2\\/sites\\/2\\/2021\\/10\\/Miniatura_ProvaieVede2021_Eps43.jpg\u0022}},{\u0022id\u0022:7543,\u0022date\u0022:\u0022\u0022,\u0022title\u0022:{\u0022rendered\u0022:\u0022TEASER\u0022},\u0022excerpt\u0022:{\u0022rendered\u0022:\u0022O tempo n\\u00e3o para. As profecias b\\u00edblicas est\\u00e3o se cumprindo. Os eventos finais est\\u00e3o cada vez mais vis\\u00edveis.\\r\\nThiago, Bela, Lucas, Amanda e muitos outros s\\u00e3o provados e suas vidas correm risco. Confrontados por um novo entendimento mundial, eles v\\u00e3o ser levados a responder: vale a pena ser fiel? O rel\\u00f3gio est\\u00e1 pr\\u00f3ximo de bater meia noite e precisam decidir se \\u00e9 tempo de desanimar ou de crer.\u0022},\u0022link\u0022:{\u0022title\u0022:\u0022TEASER\u0022,\u0022url\u0022:\u0022https:\\/\\/feliz7play.com\\/pt\\/c\\/2359-ate-o-ultimo-minuto\\/2359-ate-o-ultimo-minuto-extras?target=teaser\u0022,\u0022target\u0022:\u0022_blank\u0022},\u0022featured_media_url\u0022:{\u0022pa_block_render\u0022:\u0022https:\\/\\/files.adventistas.org\\/feliz7play\\/v2\\/sites\\/2\\/2021\\/10\\/Thumb_TeaserTemp3.jpg\u0022}},{\u0022id\u0022:7540,\u0022date\u0022:\u0022\u0022,\u0022title\u0022:{\u0022rendered\u0022:\u0022O ENIGMA DOS 7\u0022},\u0022excerpt\u0022:{\u0022rendered\u0022:\u0022H\\u00e1 quem diga que 7 seja o n\\u00famero da perfei\\u00e7\\u00e3o. Bem, neste epis\\u00f3dio, nossos amigos Nick e Gabi v\\u00e3o a um interessante parque e ali, em meio \\u00e0 natureza, s\\u00e3o desafiados pelo pai, o Heitor, a resolverem um enigma. Ser\\u00e1 que conseguir\\u00e3o? Enquanto tentam essa proeza, eles leem o livro O Libertador, de Ellen White, e a B\\u00edblia, relembram v\\u00e1rios milagres realizados por Jesus e descobrem coisas muito legais ligadas ao n\\u00famero 7. E... ser\\u00e1 que voc\\u00ea adivinha em que dia da semana eles fazem tudo isso?\u0022},\u0022link\u0022:{\u0022title\u0022:\u0022O ENIGMA DOS 7\u0022,\u0022url\u0022:\u0022https:\\/\\/feliz7play.com\\/pt\\/c\\/o-presente-de-nick\\/o-presente-de-nick-4?target=o-enigma-dos-7\u0022,\u0022target\u0022:\u0022_blank\u0022},\u0022featured_media_url\u0022:{\u0022pa_block_render\u0022:\u0022https:\\/\\/files.adventistas.org\\/feliz7play\\/v2\\/sites\\/2\\/2021\\/10\\/enigma-dos-sete-presente-de-nick.jpg\u0022}},{\u0022id\u0022:7538,\u0022date\u0022:\u0022\u0022,\u0022title\u0022:{\u0022rendered\u0022:\u0022Decidi ficar junto\u0022},\u0022excerpt\u0022:{\u0022rendered\u0022:\u0022Bruno e Ju decidem criar um pequeno grupo, mas cada um com objetivos diferentes. Quando ambos percebem que seu plano desabaria, a busca por uma solu\\u00e7\\u00e3o acaba conduzindo a um caminho diferente.\u0022},\u0022link\u0022:{\u0022title\u0022:\u0022Decidi ficar junto\u0022,\u0022url\u0022:\u0022https:\\/\\/feliz7play.com\\/pt\\/c\\/decidi-ser?target=decidi-ficar-junto\u0022,\u0022target\u0022:\u0022_blank\u0022},\u0022featured_media_url\u0022:{\u0022pa_block_render\u0022:\u0022https:\\/\\/files.adventistas.org\\/feliz7play\\/v2\\/sites\\/2\\/2021\\/10\\/thumb-decidi-ficar-junto-pt1.jpg\u0022}},{\u0022id\u0022:7536,\u0022date\u0022:\u0022\u0022,\u0022title\u0022:{\u0022rendered\u0022:\u00225 Jogadores de futebol crist\\u00e3os\u0022},\u0022excerpt\u0022:{\u0022rendered\u0022:\u0022Conhe\\u00e7a a hist\\u00f3ria de 5 (entre muitos) atletas que jogaram na sele\\u00e7\\u00e3o brasileira que usaram do seu talento no futebol como forma de mostrar sua f\\u00e9 para todo o mundo.\u0022},\u0022link\u0022:{\u0022title\u0022:\u00225 Jogadores de futebol crist\\u00e3os\u0022,\u0022url\u0022:\u0022https:\\/\\/feliz7play.com\\/pt\\/c\\/missao-atleta?target=5-jogadores-de-futebol-cristaos\u0022,\u0022target\u0022:\u0022_blank\u0022},\u0022featured_media_url\u0022:{\u0022pa_block_render\u0022:\u0022https:\\/\\/files.adventistas.org\\/feliz7play\\/v2\\/sites\\/2\\/2021\\/10\\/Thumb_MissaoAtletaEp5.jpg\u0022}},{\u0022id\u0022:7519,\u0022date\u0022:\u0022\u0022,\u0022title\u0022:{\u0022rendered\u0022:\u0022Deixa Comigo!\u0022},\u0022excerpt\u0022:{\u0022rendered\u0022:\u0022E a\\u00ed, com voc\\u00ea \\u00e9 miss\\u00e3o dada, miss\\u00e3o cumprida? No segundo v\\u00eddeo da nossa s\\u00e9rie sobre a LEI, vamos mostrar que \\\u0022Cumprir fielmente a parte que me corresponde\\\u0022 vai mais longe do que voc\\u00ea imagina!\u0022},\u0022link\u0022:{\u0022title\u0022:\u0022Deixa Comigo!\u0022,\u0022url\u0022:\u0022https:\\/\\/feliz7play.com\\/pt\\/c\\/dbv-mania?target=deixa-comigo\u0022,\u0022target\u0022:\u0022_blank\u0022},\u0022featured_media_url\u0022:{\u0022pa_block_render\u0022:\u0022https:\\/\\/files.adventistas.org\\/feliz7play\\/v2\\/sites\\/2\\/2021\\/10\\/deixa-comigo-dbv-mania.jpg\u0022}},{\u0022id\u0022:7516,\u0022date\u0022:\u0022\u0022,\u0022title\u0022:{\u0022rendered\u0022:\u0022Maratona Apocalipse Kids\u0022},\u0022excerpt\u0022:{\u0022rendered\u0022:\u0022Colet\\u00e2nea completa com 60 minutos para voc\\u00ea aprender mais do apocalipse com o pessoal de Apocalipse Kids.\\r\\n\\r\\nConte\\u00fado crist\\u00e3o e educativo para as crian\\u00e7as conhecerem sobre Jesus! #ApocalispeKids\u0022},\u0022link\u0022:{\u0022title\u0022:\u0022Maratona Apocalipse Kids\u0022,\u0022url\u0022:\u0022https:\\/\\/feliz7play.com\\/pt\\/maratona-apocalipse-kids\u0022,\u0022target\u0022:\u0022_blank\u0022},\u0022featured_media_url\u0022:{\u0022pa_block_render\u0022:\u0022https:\\/\\/files.adventistas.org\\/feliz7play\\/v2\\/sites\\/2\\/2021\\/10\\/maratona-apocalipse-kids.jpg\u0022}},{\u0022id\u0022:7512,\u0022date\u0022:\u0022\u0022,\u0022title\u0022:{\u0022rendered\u0022:\u0022E voc\\u00ea que foi adotado?\u0022},\u0022excerpt\u0022:{\u0022rendered\u0022:\u0022Mais lembran\\u00e7as revelam segredos entre Chris e Erik, enquanto Felipe parece saber muito mais do que aparenta.\u0022},\u0022link\u0022:{\u0022title\u0022:\u0022E voc\\u00ea que foi adotado?\u0022,\u0022url\u0022:\u0022https:\\/\\/feliz7play.com\\/pt\\/c\\/7camp?target=e-voce-que-foi-adotado\u0022,\u0022target\u0022:\u0022_blank\u0022},\u0022featured_media_url\u0022:{\u0022pa_block_render\u0022:\u0022https:\\/\\/files.adventistas.org\\/feliz7play\\/v2\\/sites\\/2\\/2021\\/10\\/Thumb_7CampEP3-2.jpg\u0022}}]","manual":"","sticky":""}},"align":"","mode":"preview","wpClassName":"wp-block-acf-p-a-feliz7-play"} /-->
			<!-- /wp:acf/p-a-row -->
			';
		
		// if ($count == 1){
		// 	break;
		// }

		// $args = new stdClass();
		// $args->post_title = $sede->name
        // $args->post_content = $updatedContent;
		$count++;
		echo $count ." - ". $sede->slug ." - ". $sede->name ."\n";

		$post_data = array(
			'post_title'    => $sede->name,
			'post_content'  => wp_slash($post_content),
			'post_name'		=> $sede->slug,
			'post_status'   => 'publish',
			'post_author'   => $user->ID,
			'post_type'     => 'page',
			'page_template'	=> 'page-front-page.blade.php',
		);
		
		wp_insert_post( $post_data, $error_obj );

	}
}
