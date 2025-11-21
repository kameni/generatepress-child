<?php
/**
 * GeneratePress Child Theme functions.
 *
 * @package GeneratePress_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Enqueue child theme stylesheet.
 *
 * Parent theme (GeneratePress) loads its own styles.
 * We just load the child style.css and make it depend on the parent.
 */
function generatepress_child_enqueue_styles() {
	$parent_handle = 'generatepress'; // This is the main GeneratePress style handle.

	wp_enqueue_style(
		'generatepress-child',
		get_stylesheet_uri(),
		array( $parent_handle ),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', 'generatepress_child_enqueue_styles', 20 );
/**
 * Load custom blocks.
 */
function generatepress_child_load_blocks() {
	require_once get_stylesheet_directory() . '/inc/blocks/product-card.php';
}
add_action( 'after_setup_theme', 'generatepress_child_load_blocks' );
