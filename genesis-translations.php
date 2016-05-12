<?php
/**
 * This plugin translates the Genesis Framework into one of the available languages.
 *
 * @package Genesis
 * @author Remkus de Vries
 *
 * Plugin Name: Genesis Translations
 * Plugin URI: https://thememix.com/plugins/genesis-translations/
 * Description: This plugin translates the Genesis Framework into one of the available languages.
 * Author: ThemeMix, Remkus de Vries
 * Version: 2.2.0
 * Author URI: https://thememix.com/
 * License: GPLv2
 * Text Domain: genesis-translations
 * Domain Path: /languages/
 */

/**
 * Defining Genesis Translation constants
 *
 */
define( 'GENTRANS_VERSION', '2.2.0' );

/**
 * The text domain for the plugin
 *
 * @since 1.0
 */
define( 'GTRANS_DOMAIN' , 'genesis-translations' );

/**
 * Load the text domain for translation of the plugin
 *
 * @since 1.0
 */
load_plugin_textdomain( 'genesis-translations', false, 'genesis-translations/genesis20' );

register_activation_hook( __FILE__, 'thememix_genesis_translations_activation_check' );
/**
 * Checks for activated Genesis Framework and its minimum version before allowing plugin to activate
 *
 * @author Nathan Rice, Remkus de Vries
 * @uses genesis_translations_activation_check()
 * @since 1.0
 * @version 2.0.2
 */
function thememix_genesis_translations_activation_check() {

    // Find Genesis Theme Data
    $theme = wp_get_theme( 'genesis' );

    // Get the version
    $version = $theme->get( 'Version' );

    // Set what we consider the minimum Genesis version
    $minimum_genesis_version = '1.9';

    // Restrict activation to only when the Genesis Framework is activated
    if ( basename( get_template_directory() ) != 'genesis' ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );  // Deactivate ourself
        wp_die( sprintf( __( 'Whoa.. the translation this plugin only works, really, when you have installed the %1$sGenesis Framework%2$s', GTRANS_DOMAIN ), '<a href="http://forsitemedia.net/go/genesis/" target="_new">', '</a>' ) );
    }

    // Set a minimum version of the Genesis Framework to be activated on
    if ( version_compare( $version, $minimum_genesis_version, '<' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );  // Deactivate ourself
        wp_die( sprintf( __( 'Uhm, the thing of it is, you kinda need the %1$sGenesis Framework %2$s%3$s or greater for these translations to make any sense.', GTRANS_DOMAIN ), '<a href="http://forsitemedia.net/go/genesis/" target="_new">', $latest, '</a>' ) );
    }
}

add_action( 'genesis_init', 'thememix_genesis_translation_init', 9 );
/**
 * Loads the Genesis text strings and filters them.
 * Alternatively, sets the GENESIS_LANGUAGES_DIR for older versions.
 *
 * @author Remkus de Vries, Daan Kortenbach
 * @since  1.0
 * @version  2.0.4
 */
function thememix_genesis_translation_init() {

    // Find Genesis Theme Data
    $theme = wp_get_theme( 'genesis' );

    // Get the version
    $version = $theme->get( 'Version' );

    // Set what we consider the old translation version
    $old_translations = '1.9.1';

    // Get root path to translations
    $fstlang = WP_CONTENT_DIR.'/plugins/' .str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) );

    // Compare Genesis version with what is set as old translation
    if ( version_compare( $version, $old_translations, '=<' ) ) {

        define( 'GENESIS_LANGUAGES_DIR', $fstlang . 'genesis-translations/' );

    }

    else {

        define( 'GENESIS_LANGUAGES_DIR', $fstlang . 'genesis20/' );

        if ( is_admin() ) {
            require( 'admin-page.php' );
            require( 'i18n-module.php' );
            new ThemeMix_Genesis_Translations_i18n(
                array(
                'textdomain'     => 'genesis-translations',
                'project_slug'   => 'genesis-translations',
                'plugin_name'    => 'Genesis Translations',
                'hook'           => 'thememix_genesis_translations_admin_footer',
                'glotpress_url'  => 'https://translate.wordpress.org/',
                'glotpress_name' => 'Genesis Translations',
                'glotpress_logo' => 'https://s.w.org/style/images/wp-header-logo.png',
                'register_url '  => 'https://wordpress.org/support/register.php',
                )
           );
        }

    }
}

require( 'translate.php' );
