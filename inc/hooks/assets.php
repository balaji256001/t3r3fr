<?php

namespace Waboot\inc\hooks;

use function Waboot\inc\core\AssetsManager;

/**
 * Loads assets
 */
function assets(){
    $assets = [];
    $assets['main-js'] = [
        'uri' => defined('WP_DEBUG') && WP_DEBUG ? get_template_directory_uri() . '/assets/dist/js/main.pkg.js' : get_template_directory_uri() . '/assets/dist/js/main.min.js',
        'path' => defined('WP_DEBUG') && WP_DEBUG ? get_template_directory() . '/assets/dist/js/main.pkg.js' : get_template_directory() . '/assets/dist/js/main.min.js',
        'type' => 'js',
        'deps' => ['jquery','owlcarousel-js','swipebox-js']
    ];
    $assets['owlcarousel-js'] = [
        'uri' => get_template_directory_uri() . '/assets/vendor/owlcarousel/owl.carousel.min.js',
        'type' => 'js',
        'deps' => ['jquery']
    ];
    $assets['swipebox-js'] = [
        'uri' => get_template_directory_uri() . '/assets/vendor/swipebox/js/jquery.swipebox.min.js',
        'type' => 'js',
        'deps' => ['jquery']
    ];
    $assets['main-style'] = [
        'uri' => get_template_directory_uri() . '/assets/dist/css/main.min.css',
        'path' => get_template_directory() . '/assets/dist/css/main.min.css',
        'type' => 'css',
        'deps' => ['google-font']
    ];
    $assets['google-font'] = [
        //'uri' => 'https://fonts.googleapis.com/css?family=Montserrat:400,400i,700,700i&display=swap',
        'uri' => 'https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,400;0,700;1,400;1,700&family=Lato:ital,wght@0,400;0,700;1,400;1,700&display=swap',
        'type' => 'css'
    ];
    $assets['owlcarousel-css'] = [
        'uri' => get_template_directory_uri() . '/assets/vendor/owlcarousel/owl.carousel.min.css',
        'type' => 'css'
    ];
    $assets['swipebox-css'] = [
        'uri' => get_template_directory_uri() . '/assets/vendor/swipebox/css/swipebox.min.css',
        'type' => 'css',
    ];

    AssetsManager()->addAssets($assets);
    try{
        AssetsManager()->enqueue();
    }catch (\Exception $e){
        trigger_error($e->getMessage(),E_USER_WARNING);
    }
}
add_action('wp_enqueue_scripts', __NAMESPACE__.'\\assets');

/**
 * Loads Admin Assets
 */
function editorStyle() {
    add_editor_style( 'assets/dist/css/gutenberg.min.css' );
}
add_action('init', __NAMESPACE__.'\\editorStyle');
