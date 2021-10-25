<?php
add_action( 'wp_enqueue_scripts', 'wpdocs_my_enqueue_scripts' );
function wpdocs_my_enqueue_scripts(){

    wp_enqueue_script( 'wpdocs-iasd-lgpd-script', 'https://files.adventistas.org/iasd_lgpd/dist/iasd_lgpd.min.js', array(), null, true );   
}
