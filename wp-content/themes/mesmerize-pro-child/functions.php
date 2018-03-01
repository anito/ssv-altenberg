<?php
add_action('wp_enqueue_scripts', 'add_styles', PHP_INT_MAX);
function add_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}