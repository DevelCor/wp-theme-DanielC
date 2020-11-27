<?php

function dc_setup_theme(){
    $supports = [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption'
    ];

    add_theme_support( 'html5', $supports );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails');
    add_theme_support( 'automatic-feed-links' );

    $GLOBALS['content_width'] = 1130;
}

function dc_setup_widgets(){
    register_sidebar( 
    [
        'id'            => 'sidebar-widgets',
        'name'          => 'Sidebar Widgets',
        'description'   => 'Barra de widgets',
        'before_widget' => '<section id="%1$s class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>'
    ] );
}
add_action( 'widgets_init','dc_setup_widgets');
add_action( 'after_setup_theme', 'dc_setup_theme' );

require 'inc/scripts.php';

//  estilos admin
function admin_style() {
    wp_enqueue_style('admin-styles', get_stylesheet_directory_uri().'/admin.css');
  }
add_action('admin_enqueue_scripts', 'admin_style');