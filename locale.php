<?php if( ! defined( "BASE_PATH" ) ){ echo 'Not allow to execute directly.'; exit(); }
/* vi: set noet ft=php ff=unix ts=2 sw=1 fenc=utf8 */

require_once 'locales/' . W2_LOCALE . '.php';

/**
 * Get translated word
 *
 * String	$label		Key for locale word
 * String	$alt_word	Alternative word
 * return	String
 */
function __( $label, $alt_word = null ){
	global $w2_word_set;

	if( empty($w2_word_set[$label]) ) {
		return is_null($alt_word) ? $label : $alt_word;
	}
	return htmlspecialchars($w2_word_set[$label], ENT_QUOTES);
}

/**
 * Echo translated word
 *
 * String	$label		Key for locale word
 * String	$alt_word	Alternative word
 */
function _e( $label, $alt_word = null ){
	echo __($label, $alt_word);
}
