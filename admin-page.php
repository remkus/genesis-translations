<?php

add_action( 'admin_menu', 'thememix_genesis_translations_add_admin_page' );
/**
 * Adds admin page under the "Genesis" menu.
 * Explains how to implement new and improved translations.
 * 
 * @author Remkus de Vries, Ryan Hellyer
 * @since 2.1
 */
function thememix_genesis_translations_add_admin_page() {

	add_submenu_page(
		'genesis',
		__ ( 'ThemeMix Genesis Translations', 'genesis-translations' ), // Page title
		__ ( 'Translations', 'plugin-slug' ),                           // Menu title
		'manage_options',                                               // Capability required
		'thememix-translations',                                        // The URL slug
		'thememix_genesis_translations_admin_page'                      // Displays the admin page
	);
}


/**
 * Outputs the admin page.
 * 
 * @author Remkus de Vries, Ryan Hellyer
 * @since 2.1
 */
function thememix_genesis_translations_admin_page() {
	?>
	<div class="wrap">
		<h1><?php _e( 'ThemeMix Genesis Translations', 'genesis-translations' ); ?></h1>
		<p><?php _e( 'Here will be instructions on how to add translations.', 'genesis-translations' ); ?></p>
	</div><?php
}
