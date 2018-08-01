<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

// END ENQUEUE PARENT ACTION
function custom_style_sheet() {
    wp_enqueue_style( 'custom-styling', get_stylesheet_directory_uri() . '/style.css' );
}
add_action('wp_enqueue_scripts', 'custom_style_sheet');

?>