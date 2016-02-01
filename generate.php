<?php

if ( ! defined( 'GENESIS_TRANSLATIONS_GENERATE' ) ) {
	return;
}

$contents = file_get_contents( '../../themes/genesis/lib/languages/genesis.pot' );

$contents = explode( 'msgid "', $contents );
unset( $contents[0] );

foreach ( $contents as $key => $content ) {
	$string = '"
msgstr ""';
	$content = explode( $string, $content );
	$content = $content[0];
	$content = str_replace( '"
"', '', $content );

	// NEED TO HANDLE _NX_noop TYPE STUFF HERE
	$string = '"
msgid_plural "';
	$content = explode( $string, $content );
//	echo $content[0] . ' : ' . $content[1];
	if ( isset( $content[1] ) ) {
		$string = '"
msgstr';
		$plaural = explode( $string, $content[1] );
		$plaural = $plaural[0];
		$content =
		$new_contents[] = array( $content[0], $plaural );
	} else {
		if ( '' != $content[0] ) {
			$new_contents[] = $content[0];
		}
	}
}

$file = '<?php

/**
 * Outputs all of the translations strings from the Genesis theme.
 */
function genesis_translations_strings() {
	return array(
';
$number = 0;
foreach ( $new_contents as $key => $value ) {
	$number++;

	if ( ! is_array( $value ) ) {

		if ( strpos( $value, '$' ) !== false ) {
			$file .= "		'$value' => __( '$value', 'genesis-translations' ),\n";
		} else {
			$file .= "		\"$value\" => __( \"$value\", 'genesis-translations' ),\n";
		}

	} else {
		$file .= "		'$value[0]-$value[1]' => _nx_noop( '$value[0]', '$value[1]', 'time difference', 'genesis-translations' ),\n";
	}

}
$file .= '
	);
}

/**
 * Translating items.
 *
 * @param  string  $text        The text
 * @param  string  $text_domain The original text
 * @param  string  $text_domain The text domain
 * @return string  The translated text
 */
function genesis_translations_gettext_filter( $text, $text_string, $text_domain ) {

	if ( \'genesis\' == $text_domain ) {
		$translations = genesis_translations_strings();
		if ( isset( $translations[$text_string] ) ) {
			return $translations[$text_string];
		}
	}

	return $text;
}
add_filter( \'gettext\', \'genesis_translations_gettext_filter\', 10, 3 );

/**
 * Translating plauralised items.
 *
 * @param  string  $text        The translated text
 * @param  string  $singular    The singular text
 * @param  string  $plaural     The plaural text
 * @param  int     $count       The number of objects
 * @param  string  $context     The translation context
 * @param  string  $text_domain The text domain
 * @return string  The translated text
 */
function genesis_translations_gettext_with_context_filter( $text, $singular, $plaural, $count, $context, $text_domain ) {

	if ( \'genesis\' == $text_domain ) {
		$translations = genesis_translations_strings();

		if ( 1 < $count && isset( $translations[$plaural] ) ) {
			return $translations[$plaural];
		} elseif ( 2 > $count && isset( $translations[$singular] ) ) {
			return $translations[$singular];
		}

	}

	return $text;
}
add_filter( \'ngettext_with_context\', \'genesis_translations_gettext_with_context_filter\', 10, 6 );

';


file_put_contents( 'translate2.php', $file );

echo "\n\n$number items have been processed.\n\nCOMPLETE!";
die;