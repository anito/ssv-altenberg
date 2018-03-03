<?php
// Declare SportsPress support.
add_theme_support( 'sportspress' );

// Declare Mega Slider support.
add_theme_support( 'mega-slider' );

add_action('wp_enqueue_scripts', 'add_styles', 0);
function add_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}

add_filter( 'upload_mimes', 'allow_svg_upload' );
function allow_svg_upload( $m ) {
    $m['svg'] = 'image/svg+xml';
    $m['svgz'] = 'image/svg+xml';
    return $m;
}

