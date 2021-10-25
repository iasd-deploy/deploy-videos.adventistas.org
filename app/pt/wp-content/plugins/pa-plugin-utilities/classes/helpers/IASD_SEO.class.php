<?php

class IASD_SEO {
	static function Router() {
		echo "\t\t".'<meta property="og:locale" content="'.WPLANG.'" />'."\n";
		$name = get_bloginfo('name');
		echo "\t\t".'<meta property="og:site_name" content="'.$name.'">'."\n";
		self::Head();
//		if(is_single())
//			self::Single();
	}

	static function Single() {
		//IMAGE
		$post_thumbnail_id = get_post_thumbnail_id(null);
		if($post_thumbnail_id) {
			$image = wp_get_attachment_image_src( $post_thumbnail_id, 'full');

			if(is_array($image) && count($image) == 4) {
				if($mime = get_post_mime_type( $post_thumbnail_id ))
					echo "\t\t".'<meta property="og:image:type" content="'.$mime.'">'."\n";

				echo "\t\t".'<meta property="og:image" content="'.$image[0].'">'."\n";
				echo "\t\t".'<meta property="og:image:width" content="'.$image[1].'">'."\n";
				echo "\t\t".'<meta property="og:image:height" content="'.$image[2].'">'."\n";
			}
		}

		echo "\t\t".'<meta property="og:title" content="'.get_the_title().'">'."\n";
		echo "\t\t".'<meta property="og:description" content="'.get_the_excerpt().'">'."\n";

		echo "\t\t".'<meta property="og:type" content="article" />'."\n";
		$permalink = get_permalink();
		echo "\t\t".'<link rel="canonical" href="'.$permalink.'" />'."\n";
		echo "\t\t".'<meta property="og:url" content="'.$permalink.'" />'."\n";
	}

	static function Head() {
/*		$wp_title = $title = wp_title('', false);
        var_dump($title);
		if($title)
			$title .= ' - ';
		$title .= get_bloginfo('name');

		if(is_home()) {
            $description = get_bloginfo('description');
            if(!$description)
                $description = 'home';
            if(strpos($title, $description) === false)
                $title .= ' - ' . $description;
        }
*/

        if ( is_front_page() ) {
            $title = get_bloginfo('name');
            if(get_bloginfo('description'))
                $title .= ' - ' . get_bloginfo('description');
        }
        if ( !is_front_page() ) {
            $title = wp_title('', false);
        }

		echo "\t\t<title>$title</title>"."\n";

		if(is_home())
			echo '<meta property="og:title" content="'.$title.'">'."\n";
	}
}

if(!is_admin())
	add_action('wp_head', array('IASD_SEO', 'Router'));


/*

<link rel="canonical" href="http://192.168.1.111/noticias/pt/noticia/gente/ator-filme-tropa-elite-aceita-jesus/" />
-<meta property="og:locale" content="pt_BR" />
<meta property="og:type" content="article" />
-<meta property="og:title" content="Ator do filme Tropa de Elite aceita a Jesus - Noticias - Adventistas" />
-<meta property="og:description" content="Sua jornada na televisão é grande, já atuou em vários filmes e novelas, em destaque o Tropa de Elite, onde interpretou o policial Mathias, do Bope. André Ramiro foi batizado neste sábado, 16 de novembro, na Igreja Adventista de Botafogo, no Rio de Janeiro. Carioca da Vila Kennedy, o ator de 32 anos já foi &hellip;" />
-<meta property="og:url" content="http://192.168.1.111/noticias/pt/noticia/gente/ator-filme-tropa-elite-aceita-jesus/" />
-<meta property="og:site_name" content="Noticias - Adventistas" />
<meta property="article:section" content="Adra" />
<meta property="article:section" content="Notícias" />
<meta property="article:published_time" content="2013-11-19T17:24:27+00:00" />
<meta property="article:modified_time" content="2013-11-20T14:35:19+00:00" />
-<meta property="og:image" content="http://noticias.adventistas.org/pt/noticias/pt/wp-content/uploads/2013/11/Bird_by_Magnus.jpg" />
-<meta property="og:image" content="http://noticias.adventistas.org/pt/wp-content/uploads/2013/11/andrebatismo.site_-300x225.jpg" />
<meta name="twitter:card" content="summary"/>
<meta name="twitter:site" content="@iasd"/>
<meta name="twitter:domain" content="Noticias - Adventistas"/>
<meta name="twitter:creator" content="@iasd"/>

*/
