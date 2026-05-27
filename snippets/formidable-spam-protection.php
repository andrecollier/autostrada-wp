/**
 * Spam-beskyttelse for Formidable Forms
 *
 * Globalt (alle skjemaer):
 *  1. Honeypot: skjult felt "autostrada_website_url" må være tomt
 *  2. Time-trap: minimum 3 sekunder mellom render og submit
 *
 * Kun form ID 7 ("Vi kjøper bilen din"):
 *  3. Rate limit: maks 3 submissions per 10 min per IP
 *
 * Bakgrunn: 2026-05-27 fikk form 7 745+ bot-submissions fra én IP
 * (108.175.5.162, Leaseweb DC) på 15 min. Alle felter fylt med "20".
 * Honeypot + time-trap utvidet til alle skjemaer som proaktiv beskyttelse.
 */

define( 'AUTOSTRADA_SPAM_RATE_LIMIT_FORM_ID', 7 );
define( 'AUTOSTRADA_SPAM_RATE_LIMIT', 3 );
define( 'AUTOSTRADA_SPAM_RATE_WINDOW', 600 );  // 10 min
define( 'AUTOSTRADA_SPAM_MIN_FILL_TIME', 3 );  // sekunder

/**
 * Hent ekte client IP (siten kjører bak Cloudflare).
 */
function autostrada_spam_get_ip() {
	$candidates = array( 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );
	foreach ( $candidates as $key ) {
		if ( ! empty( $_SERVER[ $key ] ) ) {
			$ip = trim( explode( ',', $_SERVER[ $key ] )[0] );
			if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
				return $ip;
			}
		}
	}
	return '';
}

/**
 * Injiser honeypot + timestamp i ALLE Formidable-skjemaer.
 */
add_filter( 'frm_filter_final_form', function( $form_html ) {
	$ts = time();
	$honeypot = '<div style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden;" aria-hidden="true">'
		. '<label>Website (la stå tomt)</label>'
		. '<input type="text" name="autostrada_website_url" value="" tabindex="-1" autocomplete="off">'
		. '<input type="hidden" name="autostrada_form_render_ts" value="' . esc_attr( $ts ) . '">'
		. '</div>';

	return preg_replace( '/<\/form>/i', $honeypot . '</form>', $form_html, 1 );
} );

/**
 * Valider submission for ALLE skjemaer (honeypot + time-trap).
 * Rate limit kjøres kun for form ID 7.
 */
add_filter( 'frm_validate_entry', function( $errors, $values ) {
	$form_id = isset( $values['form_id'] ) ? (int) $values['form_id'] : 0;

	// 1. Honeypot (alle skjemaer)
	if ( ! empty( $_POST['autostrada_website_url'] ) ) {
		$errors['spam'] = 'Spam oppdaget.';
		return $errors;
	}

	// 2. Time-trap (alle skjemaer)
	$ts = isset( $_POST['autostrada_form_render_ts'] ) ? (int) $_POST['autostrada_form_render_ts'] : 0;
	if ( $ts === 0 || ( time() - $ts ) < AUTOSTRADA_SPAM_MIN_FILL_TIME ) {
		$errors['spam'] = 'Skjemaet ble sendt for raskt. Prøv igjen.';
		return $errors;
	}

	// 3. Rate limit (kun form 7)
	if ( $form_id === AUTOSTRADA_SPAM_RATE_LIMIT_FORM_ID ) {
		$ip = autostrada_spam_get_ip();
		if ( $ip !== '' ) {
			$key = 'autostrada_spam_rl_' . md5( $ip );
			$count = (int) get_transient( $key );
			if ( $count >= AUTOSTRADA_SPAM_RATE_LIMIT ) {
				$errors['spam'] = 'For mange innsendinger. Vent noen minutter og prøv igjen.';
				return $errors;
			}
			set_transient( $key, $count + 1, AUTOSTRADA_SPAM_RATE_WINDOW );
		}
	}

	return $errors;
}, 10, 2 );
