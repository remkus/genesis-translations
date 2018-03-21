<?php

add_action( 'admin_menu', 'thememix_genesis_translations_add_admin_page' );
/**
 * Adds admin page under the "Genesis" menu.
 * Explains how to implement new and improved translations.
 *
 * @author Remkus de Vries
 * @since 2.1.0
 */
function thememix_genesis_translations_add_admin_page() {

	add_submenu_page(
		'genesis',
		__ ( 'Genesis Translations by ThemeMix', 'genesis-translations' ), // Page title
		__ ( 'Translations', 'plugin-slug' ),                           // Menu title
		'manage_options',                                               // Capability required
		'genesis-translations',                                         // The URL slug
		'thememix_genesis_translations_admin_page'                      // Displays the admin page
	);
}


/**
 * Outputs the admin page.
 *
 * @author Remkus de Vries
 * @since 2.1.0
 */
function thememix_genesis_translations_admin_page() {
	?>
	<div class="wrap">
		<h1><?php _e( 'Genesis Translations', 'genesis-translations' ); ?></h1>
		<div style="margin-top: 20px;border:1px solid #ccc;background-color:#fff;padding:10px;max-width:650px;">
			<h2>Help translate the Genesis Framework in your locale</h2>
			<p><?php _e( 'Is the translation of Genesis in your language not 100% yet? <strong>You can help!</strong> Please take the time to translate this plugin which includes all the strings for the Genesis Framework. We have a tutorial available for anyone wanting to contribute translating the Genesis Framework.', 'genesis-translations' ); ?></p>
			<p><?php _e( 'Please visit <a target="_blank" href="https://thmmx.link/gtpage/">our Knowledge Base</a> for instruction on how to help.', 'genesis-translations' ) ?>
	</div></div><?php

	do_action( 'thememix_genesis_translations_admin_footer' );

}
