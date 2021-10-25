<?php
/** 
	PARA EXECUTAR O SCRIPT, RODE NO TERMINAL (com wpcli instalado): 
 	wp eval-file processaVideo.php
*/

// dp_video_url
processaVideo();

function processaVideo() {
	ob_start();

	$log = fopen('log.csv', 'a');
	fwrite($log, "Count;PostID;FileUrl;FileSize;FileType\n");

	$args = array(
		"posts_per_page"    => "-1",
		"post_status"       => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'),
		// "include"			=> 39166,
		'meta_query' => array(
			'relation' => 'OR',
			array( 
				'key'=> 'post_processed',
				'value' => false
			),
			array( 
				'key'=> 'post_processed',
				'compare' => 'NOT EXISTS'
			)
		),
	);
	$posts = get_posts($args);
	
	echo "\n\n";
	echo "POSTS A PROCESSAR: ". count($posts);
	echo "\n\n";
	
	foreach ($posts as &$post){
		sleep(0.5);
		$count++;

		add_post_meta($post->ID, "post_processed", false, true);

		$videoUrl = get_post_meta( $post->ID, 'dp_video_url', true );
		// $videoUrl = "https://www.youtube.com/watch?v=2An-wqZNoPM&list=PLtT7fGpN_s4InR0dL3AW9fM8xyLbi7yI3";

		$videoID = "";

		switch (true) {
			case ( parse_url($videoUrl, PHP_URL_HOST) == "www.youtube.com" ):
				parse_str( parse_url( $videoUrl, PHP_URL_QUERY ), $vars );
				$videoID = $vars['v'];
				$videoLength = getVideoInfo('youtube', $videoID, $post->ID);
				$msg = "\e[39m". $count ." - ". $post->ID ." - ". $videoUrl ." - ". $videoID ." - ". $videoLength ."\n";
				update_field( 'video_url', $videoUrl, $post->ID );
				update_field( 'video_length', $videoLength, $post->ID );
				break;

			case ( parse_url($videoUrl, PHP_URL_HOST) == "youtu.be" ):
				$videoID = explode("/", parse_url($videoUrl, PHP_URL_PATH))[1];
				$videoLength = getVideoInfo('youtube', $videoID, $post->ID);
				$msg = "\e[33m". $count ." - ". $post->ID ." - ". $videoUrl ." - ". $videoID ." - ". $videoLength ."\n";
				update_field( 'video_url', $videoUrl, $post->ID );
				update_field( 'video_length', $videoLength, $post->ID );
				break;

			case ( parse_url($videoUrl, PHP_URL_HOST) == "vimeo.com" ):
				$videoID = explode("/", parse_url($videoUrl, PHP_URL_PATH))[1];
				$videoLength = getVideoInfo('vimeo', $videoID, $post->ID);
				$msg = "\e[31m". $count ." - ". $post->ID ." - ". $videoUrl ." - ". $videoID ." - ". $videoLength ."\n";
				update_field( 'video_url', $videoUrl, $post->ID );
				update_field( 'video_length', $videoLength, $post->ID );
				break;
		}
		ob_flush();
		flush();
		
		update_post_meta($post->ID, "post_processed", true);

		fwrite($log, str_replace(' - ', ';', $msg));
		echo $msg;
	}
}

function getVideoInfo($provider, $video_id, $postID){
	$json = file_get_contents("https://api.feliz7play.com/v4/". $provider ."info?video_id=". $video_id);
	$obj = json_decode($json);

	$videoLength = $obj->time;

	return $videoLength;

}

