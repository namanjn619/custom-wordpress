<?php

if (!function_exists('elumine_child_enqueue_styles')) {
    /*
    *   Function to load parent and child theme CSS
    *
    */
    function elumine_child_enqueue_styles() {
        wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
        wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css' );
    }
}

if (!function_exists('elumine_child_theme_slug_setup')) {
    /*
    *   Function to load child theme textdomain
    *
    */
    function elumine_child_theme_slug_setup() {
        load_child_theme_textdomain( 'elumine-child', get_stylesheet_directory() . '/languages' );
    }
}
/*
* Load Child Theme and parent theme CSS
*
*/
add_action( 'wp_enqueue_scripts', 'elumine_child_enqueue_styles' );

/*
* Load Child theme text-domain
*
*/
add_action( 'after_setup_theme', 'elumine_child_theme_slug_setup' );
