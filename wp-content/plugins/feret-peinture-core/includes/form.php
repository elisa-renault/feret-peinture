<?php
/** Quote requests: WordPress mail API, explicit SMTP and no saved customer messages. */
defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/captcha.php';

function fp_mail_setting( $name, $default = '' ) {
	if ( defined( $name ) ) { return constant( $name ); }
	$value = getenv( $name );
	return false === $value ? $default : $value;
}

function fp_form_flag( $name ) {
	return filter_var( fp_mail_setting( $name, false ), FILTER_VALIDATE_BOOLEAN );
}

function fp_quote_ready() {
	$configured = is_email( fp_mail_setting( 'FP_QUOTE_TO' ) ) && is_email( fp_mail_setting( 'FP_MAIL_FROM' ) ) && '' !== fp_mail_setting( 'FP_SMTP_HOST' );
	if ( ! $configured ) { return false; }
	if ( fp_is_preview() ) {
		return in_array( fp_mail_setting( 'FP_SMTP_HOST' ), array( 'mailpit', 'localhost', '127.0.0.1', '::1' ), true ) && str_ends_with( fp_mail_setting( 'FP_QUOTE_TO' ), '.test' );
	}
	return fp_form_flag( 'FP_LAUNCH_APPROVED' ) && fp_form_flag( 'FP_CONTACT_APPROVED' ) && fp_form_flag( 'FP_PRIVACY_APPROVED' ) && fp_form_flag( 'FP_MAIL_DELIVERY_VERIFIED' ) && '' !== trim( fp_mail_setting( 'FP_PRIVACY_RETENTION' ) );
}

// Nonproduction must never fall back to the system mail transport or a real SMTP relay.
add_filter( 'pre_wp_mail', function ( $result ) {
	if ( fp_is_preview() && ! in_array( fp_mail_setting( 'FP_SMTP_HOST' ), array( 'mailpit', 'localhost', '127.0.0.1', '::1' ), true ) ) { return false; }
	return $result;
} );

// WordPress validates From before phpmailer_init; localhost has no valid default domain.
add_filter( 'wp_mail_from', function ( $email ) {
	return is_email( fp_mail_setting( 'FP_MAIL_FROM' ) ) ? fp_mail_setting( 'FP_MAIL_FROM' ) : $email;
} );
add_filter( 'wp_mail_from_name', function ( $name ) {
	return is_email( fp_mail_setting( 'FP_MAIL_FROM' ) ) ? 'Feret Peinture' : $name;
} );

add_action( 'phpmailer_init', function ( $mailer ) {
	$host = fp_mail_setting( 'FP_SMTP_HOST' );
	if ( '' === $host ) { return; }
	$mailer->isSMTP();
	$mailer->Host = $host;
	$mailer->Port = (int) fp_mail_setting( 'FP_SMTP_PORT', 587 );
	$mailer->SMTPAuth = '' !== fp_mail_setting( 'FP_SMTP_USER' );
	$mailer->Username = fp_mail_setting( 'FP_SMTP_USER' );
	$mailer->Password = fp_mail_setting( 'FP_SMTP_PASS' );
	$secure = fp_mail_setting( 'FP_SMTP_SECURE', 'tls' );
	$mailer->SMTPSecure = in_array( $secure, array( 'tls', 'ssl' ), true ) ? $secure : '';
	$mailer->SMTPAutoTLS = ! fp_is_preview() || '' !== $secure;
	$mailer->SMTPDebug = 0;
	$mailer->Timeout = 10;
	if ( is_email( fp_mail_setting( 'FP_MAIL_FROM' ) ) ) {
		$mailer->setFrom( fp_mail_setting( 'FP_MAIL_FROM' ), 'Feret Peinture' );
		$mailer->Sender = fp_mail_setting( 'FP_MAIL_FROM' );
	}
} );

function fp_form_install() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table = $wpdb->prefix . 'fp_quote_limits';
	$charset = $wpdb->get_charset_collate();
	dbDelta( "CREATE TABLE $table (
		counter_key varchar(64) NOT NULL,
		attempts bigint unsigned NOT NULL DEFAULT 1,
		expires bigint unsigned NOT NULL,
		PRIMARY KEY  (counter_key),
		KEY expires (expires)
	) $charset;" );
	update_option( 'fp_form_schema', '1', false );
	if ( ! wp_next_scheduled( 'fp_quote_cleanup' ) ) { wp_schedule_event( time() + HOUR_IN_SECONDS, 'hourly', 'fp_quote_cleanup' ); }
}
add_action( 'init', function () { if ( '1' !== get_option( 'fp_form_schema' ) ) { fp_form_install(); } } );
add_action( 'fp_quote_cleanup', function () {
	global $wpdb;
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}fp_quote_limits WHERE expires < %d", time() ) );
} );

/** Atomic counters: only a rotating keyed hash, never the IP or form contents. */
function fp_quote_rate_allowed( $ip ) {
	global $wpdb;
	$window = (int) floor( time() / ( 15 * MINUTE_IN_SECONDS ) );
	$key = hash_hmac( 'sha256', 'rate:' . $window . ':' . $ip, wp_salt( 'nonce' ) );
	$table = $wpdb->prefix . 'fp_quote_limits';
	$result = $wpdb->query( $wpdb->prepare( "INSERT INTO $table (counter_key, attempts, expires) VALUES (%s, 1, %d) ON DUPLICATE KEY UPDATE attempts = attempts + 1", $key, time() + HOUR_IN_SECONDS ) );
	if ( false === $result ) { return false; }
	return (int) $wpdb->get_var( $wpdb->prepare( "SELECT attempts FROM $table WHERE counter_key = %s", $key ) ) <= 5;
}

function fp_quote_token() {
	$body = time() . '.' . wp_generate_password( 24, false, false );
	return $body . '.' . hash_hmac( 'sha256', $body, wp_salt( 'nonce' ) );
}

function fp_quote_token_valid( $token ) {
	if ( ! is_string( $token ) || ! preg_match( '/^(\d{10})\.([a-zA-Z0-9]{24})\.([a-f0-9]{64})$/D', $token, $parts ) ) { return false; }
	$age = time() - (int) $parts[1];
	return $age >= 2 && $age < 4 * HOUR_IN_SECONDS && hash_equals( hash_hmac( 'sha256', $parts[1] . '.' . $parts[2], wp_salt( 'nonce' ) ), $parts[3] );
}

function fp_quote_success_token() {
	$body = time() . '.' . wp_generate_password( 24, false, false );
	return $body . '.' . hash_hmac( 'sha256', 'success:' . $body, wp_salt( 'nonce' ) );
}

function fp_quote_success_verified() {
	$token = isset( $_GET['sent'] ) && is_string( $_GET['sent'] ) ? wp_unslash( $_GET['sent'] ) : '';
	if ( ! preg_match( '/^(\d{10})\.([a-zA-Z0-9]{24})\.([a-f0-9]{64})$/D', $token, $parts ) ) { return false; }
	$age = time() - (int) $parts[1];
	return $age >= 0 && $age < 15 * MINUTE_IN_SECONDS && hash_equals( hash_hmac( 'sha256', 'success:' . $parts[1] . '.' . $parts[2], wp_salt( 'nonce' ) ), $parts[3] );
}

function fp_quote_types() {
	$types = array();
	foreach ( fp_services() as $service ) { $types[ $service->post_name ] = $service->post_title; }
	$types['a-preciser'] = 'À préciser ensemble';
	return $types;
}

function fp_quote_values( $input ) {
	$values = array();
	foreach ( array( 'name', 'town', 'type', 'description', 'phone', 'email', 'period', 'website', 'fp_token', 'fp_nonce', 'captcha' ) as $key ) {
		$value = isset( $input[ $key ] ) && is_scalar( $input[ $key ] ) ? (string) $input[ $key ] : '';
		$values[ $key ] = 'description' === $key ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
	}
	if ( '' === $values['type'] ) { $values['type'] = 'a-preciser'; }
	return $values;
}

function fp_quote_validate( $input ) {
	$values = fp_quote_values( $input );
	$errors = array();
	$len = function ( $value ) { return mb_strlen( $value, 'UTF-8' ); };
	if ( $len( $values['name'] ) < 2 || $len( $values['name'] ) > 100 ) { $errors['name'] = 'Indiquez votre nom (2 à 100 caractères).'; }
	if ( $len( $values['town'] ) < 2 || $len( $values['town'] ) > 100 ) { $errors['town'] = 'Indiquez la commune ou le code postal du chantier.'; }
	if ( ! isset( fp_quote_types()[ $values['type'] ] ) ) { $errors['type'] = 'Choisissez le type de travaux.'; }
	if ( $len( $values['description'] ) < 10 || $len( $values['description'] ) > 4000 ) { $errors['description'] = 'Décrivez les travaux en 10 à 4 000 caractères.'; }
	if ( '' === $values['email'] && '' === $values['phone'] ) { $errors['phone'] = 'Renseignez un téléphone ou un email.'; }
	// Validate original email/phone values too: do not turn a malformed or injected address into a valid one.
	$raw_email = isset( $input['email'] ) && is_string( $input['email'] ) ? trim( $input['email'] ) : '';
	if ( '' !== $values['email'] && ( $raw_email !== $values['email'] || ! is_email( $raw_email ) || strlen( $raw_email ) > 254 ) ) { $errors['email'] = 'Vérifiez votre adresse email.'; }
	$raw_phone = isset( $input['phone'] ) && is_string( $input['phone'] ) ? trim( $input['phone'] ) : '';
	$digits = preg_replace( '/[\s.()\-]/u', '', $raw_phone );
	if ( '' !== $values['phone'] && ( ! preg_match( '/^\+?[0-9]{8,15}$/D', $digits ) || strlen( $raw_phone ) > 40 || preg_match( '/[\r\n]/', $raw_phone ) ) ) { $errors['phone'] = 'Vérifiez votre numéro de téléphone.'; }
	if ( $len( $values['period'] ) > 100 ) { $errors['period'] = 'Précisez la période en 100 caractères maximum.'; }
	return array( 'values' => $values, 'errors' => $errors );
}

/** Shared server path, invoked by the actual HTTP controller and integration tests. */
function fp_quote_submit( $input, $ip ) {
	$result = fp_quote_validate( $input );
	$result['success'] = false;
	if ( ! fp_quote_ready() ) { $result['errors'] = array( 'form' => 'Le formulaire est momentanément indisponible. Réessayez ultérieurement.' ); return $result; }
	if ( ! fp_quote_rate_allowed( $ip ) ) { $result['errors'] = array( 'form' => 'Trop de tentatives récentes. Réessayez dans 15 minutes.' ); return $result; }
	$v = $result['values'];
	if ( ! wp_verify_nonce( $v['fp_nonce'], 'fp_quote' ) || ! fp_quote_token_valid( $v['fp_token'] ) || '' !== $v['website'] ) {
		$result['errors'] = array( 'form' => 'Le formulaire a expiré ou a été envoyé trop rapidement. Vérifiez votre saisie puis réessayez.' );
		return $result;
	}
	if ( $result['errors'] ) { return $result; }
	$proof = fp_captcha_verify( $v['captcha'] );
	if ( ! $proof || ! fp_captcha_consume( $proof ) ) {
		$result['errors']['captcha'] = 'Relancez la vérification anti-spam.';
		return $result;
	}
	global $wpdb;
	$table = $wpdb->prefix . 'fp_quote_limits';
	$key = hash_hmac( 'sha256', 'sent:' . $v['fp_token'], wp_salt( 'nonce' ) );
	$reserved = $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO $table (counter_key, attempts, expires) VALUES (%s, 1, %d)", $key, time() + DAY_IN_SECONDS ) );
	if ( 1 !== $reserved ) { $result['errors'] = array( 'form' => 'Cette demande a déjà été traitée ou est en cours. Ne la renvoyez pas immédiatement.' ); return $result; }
	$type = fp_quote_types()[ $v['type'] ];
	$subject = 'Demande de rendez-vous : ' . $v['town'] . ' - ' . $type;
	$body = "Nouvelle demande de rendez-vous\n\nNom : {$v['name']}\nCommune / code postal : {$v['town']}\nTravaux : $type\nTéléphone : {$v['phone']}\nEmail : {$v['email']}\nPériode souhaitée : {$v['period']}\n\nDescription :\n{$v['description']}\n";
	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( '' !== $v['email'] ) { $headers[] = 'Reply-To: ' . $v['email']; }
	$sent = wp_mail( fp_mail_setting( 'FP_QUOTE_TO' ), $subject, $body, $headers );
	if ( ! $sent ) {
		$wpdb->delete( $table, array( 'counter_key' => $key ), array( '%s' ) );
		$result['errors'] = array( 'form' => 'L’envoi n’a pas abouti. Votre saisie est conservée ci-dessous : réessayez ultérieurement.' );
		return $result;
	}
	$result['success'] = true;
	return $result;
}

add_action( 'template_redirect', function () {
	if ( ! is_page( 'contact' ) ) { return; }
	nocache_headers();
	if ( ! defined( 'DONOTCACHEPAGE' ) ) { define( 'DONOTCACHEPAGE', true ); }
	if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) ) { return; }
	if ( (int) ( $_SERVER['CONTENT_LENGTH'] ?? 0 ) > 20000 || ! empty( $_FILES ) ) {
		$GLOBALS['fp_quote_result'] = array( 'values' => array(), 'errors' => array( 'form' => 'Cette demande est trop volumineuse. Le formulaire n’accepte pas de pièces jointes.' ) );
		status_header( 413 );
		return;
	}
	$ip = filter_var( $_SERVER['REMOTE_ADDR'] ?? '', FILTER_VALIDATE_IP ) ?: 'unknown';
	// REMOTE_ADDR only. Forwarded IP headers are untrusted; normalize at your reverse proxy.
	// Prefix HTML fields: WordPress reserves public query variables such as `name`.
	$posted = wp_unslash( $_POST );
	$input = array( 'fp_nonce' => $posted['fp_nonce'] ?? '', 'fp_token' => $posted['fp_token'] ?? '' );
	foreach ( array( 'name', 'town', 'type', 'description', 'phone', 'email', 'period', 'website', 'captcha' ) as $key ) {
		$input[ $key ] = $posted[ 'fp_' . $key ] ?? '';
	}
	$result = fp_quote_submit( $input, $ip );
	if ( $result['success'] ) {
		wp_safe_redirect( add_query_arg( 'sent', fp_quote_success_token(), home_url( '/merci/' ) ), 303 );
		exit;
	}
	$GLOBALS['fp_quote_result'] = $result;
	status_header( 422 );
}, 20 );

/** Render a labelled field and connect its instructions and server errors. */
function fp_render_quote_field( $key, $field, $values, $errors ) {
	$contact = in_array( $key, array( 'phone', 'email' ), true );
	$described_by = array();
	if ( $contact && isset( $errors['contact'] ) ) { $described_by[] = 'error-contact'; }
	if ( isset( $errors[ $key ] ) ) { $described_by[] = 'error-' . $key; }
	$invalid = isset( $errors[ $key ] ) || ( $contact && isset( $errors['contact'] ) );
	?>
	<div class="form-field"><label for="quote-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field[0] ); ?></label>
	<input id="quote-<?php echo esc_attr( $key ); ?>" name="fp_<?php echo esc_attr( $key ); ?>" type="<?php echo esc_attr( $field[1] ); ?>" autocomplete="<?php echo esc_attr( $field[2] ); ?>" maxlength="<?php echo (int) $field[3]; ?>" value="<?php echo esc_attr( $values[ $key ] ); ?>" <?php echo $field[4] ? 'required' : ''; ?> <?php echo $invalid ? 'aria-invalid="true"' : ''; ?> <?php echo $described_by ? 'aria-describedby="' . esc_attr( implode( ' ', $described_by ) ) . '"' : ''; ?> <?php echo 'email' === $key ? 'spellcheck="false" autocapitalize="none"' : ''; ?>>
	<?php if ( isset( $errors[ $key ] ) ) : ?><p class="form-error" id="error-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $errors[ $key ] ); ?></p><?php endif; ?></div>
	<?php
}

function fp_render_quote_form() {
	if ( ! fp_quote_ready() ) {
		echo '<div class="form-notice"><h2>Parlons de votre projet</h2><p>Le formulaire est en cours de préparation. L’envoi des demandes sera disponible à l’ouverture du site.</p>';
		if ( fp_phone_uri() ) { echo '<a class="button button--primary" data-event="click_phone" href="' . esc_url( fp_phone_uri() ) . '">' . esc_html( fp_phone_display() ) . '</a>'; }
		echo '</div>';
		return;
	}
	$result = $GLOBALS['fp_quote_result'] ?? array( 'values' => array(), 'errors' => array() );
	$values = wp_parse_args( $result['values'], array_fill_keys( array( 'name', 'town', 'type', 'description', 'phone', 'email', 'period' ), '' ) );
	$errors = $result['errors'];
	// A missing contact method concerns the pair, not the telephone alone.
	if ( isset( $errors['phone'] ) && '' === $values['phone'] && '' === $values['email'] ) {
		$errors['contact'] = $errors['phone'];
		unset( $errors['phone'] );
	}
	$token = fp_quote_token();
	?>
	<form class="fp-form" action="<?php echo esc_url( home_url( '/contact/' ) ); ?>" method="post" data-quote-form data-has-errors="<?php echo $errors ? '1' : '0'; ?>">
		<?php if ( fp_is_preview() ) : ?><p class="form-note preview-notice">Formulaire de test : utilisez des coordonnées fictives. Aucun message ne sera transmis à Christophe.</p><?php endif; ?>
		<?php if ( isset( $errors['form'] ) ) : ?>
		<div class="form-error-summary" id="form-errors" role="alert" tabindex="-1"><p><?php echo esc_html( $errors['form'] ); ?></p></div>
		<?php endif; ?>
		<p class="form-note" id="quote-required">* Champs obligatoires.</p>
		<div class="form-grid">
		<?php
		$fields = array(
			'name' => array( 'Votre nom *', 'text', 'name', 100, true ),
			'town' => array( 'Commune ou code postal *', 'text', 'off', 100, true ),
		);
		foreach ( $fields as $key => $field ) { fp_render_quote_field( $key, $field, $values, $errors ); }
		?>
		</div>
		<fieldset class="contact-methods" id="quote-contact" tabindex="-1" aria-label="Coordonnées" <?php echo isset( $errors['contact'] ) ? 'aria-describedby="error-contact"' : ''; ?>>
		<div class="form-grid"><?php
		fp_render_quote_field( 'phone', array( 'Téléphone', 'tel', 'tel', 40, false ), $values, $errors );
		fp_render_quote_field( 'email', array( 'Email', 'email', 'email', 254, false ), $values, $errors );
		?></div>
		<?php if ( isset( $errors['contact'] ) ) : ?><p class="form-error" id="error-contact"><?php echo esc_html( $errors['contact'] ); ?></p><?php endif; ?>
		</fieldset>
		<div class="form-field"><label for="quote-description">Vos travaux en quelques mots *</label><textarea id="quote-description" name="fp_description" rows="3" minlength="10" maxlength="4000" required <?php echo isset( $errors['description'] ) ? 'aria-describedby="error-description"' : ''; ?> <?php echo isset( $errors['description'] ) ? 'aria-invalid="true"' : ''; ?>><?php echo esc_textarea( $values['description'] ); ?></textarea><?php if ( isset( $errors['description'] ) ) : ?><p class="form-error" id="error-description"><?php echo esc_html( $errors['description'] ); ?></p><?php endif; ?></div>
		<div hidden aria-hidden="true"><label for="quote-website">Ne pas remplir</label><input id="quote-website" name="fp_website" tabindex="-1" autocomplete="off" value=""></div>
		<?php wp_nonce_field( 'fp_quote', 'fp_nonce', false ); ?>
		<input type="hidden" name="fp_token" value="<?php echo esc_attr( $token ); ?>">
		<div class="form-field" id="quote-captcha" tabindex="-1" <?php echo isset( $errors['captcha'] ) ? 'aria-invalid="true" aria-describedby="error-captcha"' : ''; ?>>
        <altcha-widget name="fp_captcha" language="fr" auto="onload" challenge="<?php echo esc_url( add_query_arg( 'fp_captcha', '1', home_url( '/' ) ) ); ?>" configuration='{"humanInteractionSignature":false,"hideFooter":true}'></altcha-widget>
        <?php if ( isset( $errors['captcha'] ) ) : ?><p class="form-error" id="error-captcha"><?php echo esc_html( $errors['captcha'] ); ?></p><?php endif; ?>
        <noscript><p>Activez JavaScript pour la vérification anti-spam, ou contactez Christophe par téléphone.</p></noscript>
        </div>
		<p class="form-note form-privacy">L’EURL CHRISTOPHE FERET utilise ces informations pour organiser votre rendez-vous, sur la base des mesures précontractuelles demandées. Christophe traite votre demande ; Aliant peut accéder aux données pour la maintenance. Les demandes sans suite sont conservées au maximum trois ans après le dernier échange. Pour connaître vos droits d’accès, de rectification et d’effacement et les exercer, consultez la <a href="<?php echo esc_url( home_url( '/confidentialite/' ) ); ?>">politique de confidentialité</a>.</p>
		<button type="submit" class="button button--primary">Envoyer ma demande</button>
		<p class="form-note submit-status" role="status" aria-live="polite" aria-atomic="true" data-submit-status></p>
	</form>
	<?php
}

// Preserve existing bookmarks and POST bodies from forms opened before the move.
add_action( 'template_redirect', function () {
    global $wp;
    if ( 'devis' !== trim( $wp->request ?? '', '/' ) ) { return; }
    $url = home_url( '/contact/' );
    if ( isset( $_GET['prestation'] ) && is_string( $_GET['prestation'] ) ) {
        $url = add_query_arg( 'prestation', sanitize_key( wp_unslash( $_GET['prestation'] ) ), $url );
    }
    wp_safe_redirect( $url, 'POST' === ( $_SERVER['REQUEST_METHOD'] ?? '' ) ? 308 : 301 );
    exit;
}, 0 );
