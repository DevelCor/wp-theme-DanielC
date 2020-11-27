<?php
function dc_enqueue_scripts(){
    // wp_enqueue_style( 'dc_style',get_stylesheet_uri(  ));
}
add_action( 'wp_enqueue_scripts','dc_enqueue_scripts' );