<?php

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
 * This is a test file, for testing future versions of the plugin.
 * This will be used for providing translations through translate.wordpress.org.
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


add_filter( \'gettext\', \'genesis_translations_gettext_filter\', 10, 3 );
function genesis_translations_gettext_filter( $text, $text_string, $text_domain ) {
	if ( \'genesis\' == $text_domain ) {
		$translations = genesis_translations_strings();
		if ( isset( $translations[$text_string] ) ) {
			return $translations[$text_string];
		}
	}

	return $text;
}

';


file_put_contents( 'genesis-framework.php', $file );

echo "\n\n$number items have been processed.\n\nCOMPLETE!";
die;