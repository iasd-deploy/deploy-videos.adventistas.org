<?php

// PARA EXECUTAR O SCRIPT, RODE NO TERMINAL (com wpcli instalado): 
// wp eval-file clearPost.php --skip-themes --skip-plugins --allow-root

clearImages();

function clearImages()
{

    ob_start();

    $log = fopen('log.txt', 'a');

    $args = array(
        "posts_per_page"    => "-1",
        "post_status"       => "publish",
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => 'img_processed',
                'value' => false
            ),
            array(
                'key' => 'img_processed',
                'compare' => 'NOT EXISTS'
            )
        ),
    );
    $posts = get_posts($args);

    echo "\n\n";
    echo "POSTS A PROCESSAR: " . count($posts);
    fwrite($log, "\n\nPOSTS A PROCESSAR: " . count($posts) . "\n");
    echo "\n\n";

    $count = count($posts);

    foreach ($posts as &$post) {
        sleep(0.5);
        $count--;

        add_post_meta($post->ID, "img_processed", false, true);

        //pega todas as midias anexadas ao post
        $medias = get_attached_media('', $post->ID);

        foreach ($medias as &$media) {

            switch (true) {
                case (strpos($post->post_content, strval($media->ID))): // Essa midia esta no corpo do post?
                    $msg = "\e[39m P - " . $count . " - " . $post->ID . " - " . $media->ID . " - " . $media->guid . "\n";
                    echo $msg;
                    fwrite($log, $msg);
                    break;
                case (get_post_thumbnail_id($post->ID) == $media->ID): // Essa midia esta na imagem destaque?
                    $msg = "\e[33m T - " . $count . " - " . $post->ID . " - " . $media->ID . " - " . $media->guid . "\n";
                    echo $msg;
                    fwrite($log, $msg);
                    break;
                default: // Se a imagem esta marcada para o post mas não está no corpo do post nem na imagem destaque então ela não esta sendo usada e deve ser enviada para a lixeira.
                    $msg = "\e[31m X - " . $count . " - " . $post->ID . " - " . $media->ID . " - " . $media->guid . "\n";
                    echo $msg;
                    fwrite($log, $msg);
                    wp_delete_attachment($media->ID, false);
                    // semudar para wp_delete_attachment($media->ID, true); a imagem será excluida por definitivo, sem passar pela lixeira.
            }
            ob_flush();
            flush();
        }

        update_post_meta($post->ID, "img_processed", true);
    }
    fclose($log);
    echo "\n";
    ob_end_flush();
}
