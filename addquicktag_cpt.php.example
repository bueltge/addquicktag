<?php
/**
 * Plugin Name: AddQuicktag Example for CPT
 * Plugin URI:  http://bueltge.de/
 * Description: Add custom post type 'my_custom_post_type' to AddQuicktag plugin
 * Author:      Frank Bültge
 * Version:     0.0.1
 * Licence:     GPLv3
 * Author URI:  http://bueltge.de
 */

 // This file is not called from WordPress. We don't like that.
if ( ! function_exists( 'add_filter' ) ) {
	echo "Hi there! I'm just a part of plugin, not much I can do when called directly.";
	exit;
}

if ( ! function_exists( 'my_addquicktag_post_types' ) ) {

	// add custom function to filter hook 'addquicktag_post_types'
	add_filter( 'addquicktag_post_types', 'my_addquicktag_post_types' );
	
	/**
	 * Return array $post_types with custom post types strings
	 * 
	 * @param   $post_type Array
	 * @return  $post_type Array
	 */
	function my_addquicktag_post_types( $post_types ) {
		
		$post_types[] = 'snippet';
		
		return $post_types;
	}
	
}