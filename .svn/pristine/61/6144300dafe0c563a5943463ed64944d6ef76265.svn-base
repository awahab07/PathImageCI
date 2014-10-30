<?php
/******************************
	Code for GetText
******************************/

define('LOCALE_DIR', APPPATH .'language/locales');
define('DEFAULT_LOCALE', 'en_US');

require_once(APPPATH.'libraries/php-gettext/gettext.inc');

setup_gettext_helper();

function setup_gettext_helper() {
	// Determine currently set locale
	$CI =& get_instance();
	$CI->load->library('session');
	$locale = $CI->input->get('language'); 

	if ($locale == false) {
		// Retry getting lang set in session
		$locale = $CI->session->userdata('language');
	
		if ($locale == false) {
			// Fallback
			$locale = $CI->config->item('pathimageci_default_locale');
		}
	}

	$supported_locales = $CI->config->item('pathimageci_locales');
	$encoding = 'UTF-8';

	// gettext setup
	T_setlocale(LC_MESSAGES, $locale);
	// Set the text domain as 'messages'
	$domain = 'messages';
	T_bindtextdomain($domain, LOCALE_DIR);
	T_bind_textdomain_codeset($domain, $encoding);
	T_textdomain($domain);
}