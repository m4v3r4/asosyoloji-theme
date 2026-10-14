<?php
/**
 * Public contact and newsletter request forms.
 *
 * @package Asosyoloji
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the configured public-form recipient.
 *
 * @return string
 */
function asosyoloji_form_recipient() {
	$recipient = sanitize_email( get_theme_mod( 'aso_contact_recipient', get_option( 'admin_email' ) ) );
	return is_email( $recipient ) ? $recipient : sanitize_email( get_option( 'admin_email' ) );
}

/**
 * Build a privacy-safe rate-limit key.
 *
 * @param string $action Form action.
 * @return string
 */
function asosyoloji_form_rate_key( $action ) {
	$address = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	return 'aso_form_' . md5( $action . '|' . $address . '|' . wp_salt( 'nonce' ) );
}

/**
 * Redirect back to the originating page with a form status.
 *
 * @param string $key    Query key.
 * @param string $status Status value.
 * @return void
 */
function asosyoloji_form_redirect( $key, $status ) {
	$target = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$target = remove_query_arg( array( 'aso_contact', 'aso_newsletter' ), $target );
	wp_safe_redirect( add_query_arg( $key, $status, $target ) );
	exit;
}

/**
 * Render a form response message.
 *
 * @param string $key Form query key.
 * @return string
 */
function asosyoloji_form_notice( $key ) {
	$status = isset( $_GET[ $key ] ) ? sanitize_key( wp_unslash( $_GET[ $key ] ) ) : '';
	if ( ! $status ) {
		return '';
	}

	if ( 'success' === $status ) {
		$message = 'aso_contact' === $key
			? __( 'Mesajınız ulaştı. En kısa sürede size döneceğiz.', 'asosyoloji' )
			: __( 'Bülten abonelik talebiniz alındı.', 'asosyoloji' );
		$class = 'is-success';
	} else {
		$message = __( 'İşlem tamamlanamadı. Alanları kontrol edip yeniden deneyin.', 'asosyoloji' );
		$class = 'is-error';
	}

	return sprintf(
		'<div class="aso-form-notice %1$s" role="status">%2$s</div>',
		esc_attr( $class ),
		esc_html( $message )
	);
}

/**
 * Render the native contact form.
 *
 * @return string
 */
function asosyoloji_contact_form() {
	$privacy_url = get_privacy_policy_url();
	ob_start();
	?>
	<section class="aso-contact" aria-labelledby="aso-contact-title">
		<div class="aso-contact__intro">
			<div class="aso-contact__kicker"><?php esc_html_e( 'Bize ulaşın', 'asosyoloji' ); ?></div>
			<h2 id="aso-contact-title"><?php esc_html_e( 'Düşüncenizi paylaşın', 'asosyoloji' ); ?></h2>
			<p><?php esc_html_e( 'Yayın, etkinlik, işbirliği ve diğer konulardaki mesajlarınızı bu formdan iletebilirsiniz.', 'asosyoloji' ); ?></p>
		</div>
		<?php echo wp_kses_post( asosyoloji_form_notice( 'aso_contact' ) ); ?>
		<form class="aso-public-form aso-contact__form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
			<input type="hidden" name="action" value="asosyoloji_contact">
			<?php wp_nonce_field( 'asosyoloji_contact', 'asosyoloji_contact_nonce' ); ?>
			<div class="aso-form-trap" aria-hidden="true">
				<label><?php esc_html_e( 'Bu alanı boş bırakın', 'asosyoloji' ); ?><input type="text" name="website" tabindex="-1" autocomplete="off"></label>
			</div>
			<div class="aso-public-form__grid">
				<label><?php esc_html_e( 'Adınız', 'asosyoloji' ); ?><input type="text" name="name" autocomplete="name" required maxlength="120"></label>
				<label><?php esc_html_e( 'E-posta adresiniz', 'asosyoloji' ); ?><input type="email" name="email" autocomplete="email" required maxlength="190"></label>
			</div>
			<label><?php esc_html_e( 'Konu', 'asosyoloji' ); ?><input type="text" name="subject" required maxlength="180"></label>
			<label><?php esc_html_e( 'Mesajınız', 'asosyoloji' ); ?><textarea name="message" rows="8" required maxlength="5000"></textarea></label>
			<label class="aso-public-form__consent">
				<input type="checkbox" name="privacy" value="1" required>
				<span>
					<?php if ( $privacy_url ) : ?>
						<?php echo wp_kses_post( sprintf( __( '<a href="%s">Gizlilik politikasını</a> okudum ve mesajımın yanıtlanması için verilerimin işlenmesini kabul ediyorum.', 'asosyoloji' ), esc_url( $privacy_url ) ) ); ?>
					<?php else : ?>
						<?php esc_html_e( 'Mesajımın yanıtlanması için verdiğim bilgilerin işlenmesini kabul ediyorum.', 'asosyoloji' ); ?>
					<?php endif; ?>
				</span>
			</label>
			<button class="aso-button aso-public-form__submit" type="submit"><?php esc_html_e( 'Mesajı gönder', 'asosyoloji' ); ?></button>
		</form>
	</section>
	<?php
	return (string) ob_get_clean();
}

/**
 * Contact form shortcode.
 *
 * @return string
 */
function asosyoloji_contact_form_shortcode() {
	return asosyoloji_contact_form();
}
add_shortcode( 'asosyoloji_iletisim', 'asosyoloji_contact_form_shortcode' );

/**
 * Replace an unrendered Contact Form 7 shortcode with the native form.
 *
 * @param string $content Page content.
 * @return string
 */
function asosyoloji_contact_form_fallback( $content ) {
	if ( is_admin() || ! is_singular( 'page' ) ) {
		return $content;
	}

	if ( preg_match( '/\[contact-form-7[^\]]*\]/iu', $content ) ) {
		return (string) preg_replace_callback(
			'/\[contact-form-7[^\]]*\]/iu',
			function () {
				return asosyoloji_contact_form();
			},
			$content,
			1
		);
	}

	return $content;
}
add_filter( 'the_content', 'asosyoloji_contact_form_fallback', 20 );

/**
 * Process a public contact form submission.
 *
 * @return void
 */
function asosyoloji_handle_contact_form() {
	$nonce = isset( $_POST['asosyoloji_contact_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['asosyoloji_contact_nonce'] ) ) : '';
	$trap  = isset( $_POST['website'] ) ? trim( (string) wp_unslash( $_POST['website'] ) ) : '';
	$key   = asosyoloji_form_rate_key( 'contact' );

	if ( ! wp_verify_nonce( $nonce, 'asosyoloji_contact' ) || $trap || get_transient( $key ) ) {
		asosyoloji_form_redirect( 'aso_contact', 'error' );
	}

	$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$privacy = ! empty( $_POST['privacy'] );

	if ( ! $name || ! is_email( $email ) || ! $subject || ! $message || ! $privacy ) {
		asosyoloji_form_redirect( 'aso_contact', 'error' );
	}

	set_transient( $key, 1, MINUTE_IN_SECONDS );
	$mail_subject = sprintf( '[%s] %s', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $subject );
	$mail_body    = sprintf( "Ad: %s\nE-posta: %s\n\n%s", $name, $email, $message );
	$headers      = array( sprintf( 'Reply-To: %s <%s>', $name, $email ) );
	$sent         = wp_mail( asosyoloji_form_recipient(), $mail_subject, $mail_body, $headers );

	asosyoloji_form_redirect( 'aso_contact', $sent ? 'success' : 'error' );
}
add_action( 'admin_post_asosyoloji_contact', 'asosyoloji_handle_contact_form' );
add_action( 'admin_post_nopriv_asosyoloji_contact', 'asosyoloji_handle_contact_form' );

/**
 * Render the footer newsletter request form.
 *
 * @return string
 */
function asosyoloji_newsletter_form() {
	$privacy_url = get_privacy_policy_url();
	ob_start();
	?>
	<section class="site-footer__newsletter" aria-labelledby="aso-newsletter-title">
		<div>
			<h2 id="aso-newsletter-title"><?php echo esc_html( get_theme_mod( 'aso_footer_newsletter_title', __( 'Yeni yazıları kaçırmayın', 'asosyoloji' ) ) ); ?></h2>
			<p><?php echo esc_html( get_theme_mod( 'aso_footer_newsletter_text', __( 'Yeni sayılar, yazılar ve etkinliklerden haberdar olmak için bültene katılın.', 'asosyoloji' ) ) ); ?></p>
		</div>
		<div>
			<?php echo wp_kses_post( asosyoloji_form_notice( 'aso_newsletter' ) ); ?>
			<form class="aso-newsletter-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
				<input type="hidden" name="action" value="asosyoloji_newsletter">
				<?php wp_nonce_field( 'asosyoloji_newsletter', 'asosyoloji_newsletter_nonce' ); ?>
				<input class="aso-newsletter-form__trap" type="text" name="company" tabindex="-1" autocomplete="off" aria-hidden="true">
				<label class="screen-reader-text" for="aso-newsletter-email"><?php esc_html_e( 'E-posta adresi', 'asosyoloji' ); ?></label>
				<div class="aso-newsletter-form__row">
					<input id="aso-newsletter-email" type="email" name="email" autocomplete="email" placeholder="<?php esc_attr_e( 'E-posta adresiniz', 'asosyoloji' ); ?>" required maxlength="190">
					<button type="submit"><?php esc_html_e( 'Katıl', 'asosyoloji' ); ?></button>
				</div>
				<label class="aso-newsletter-form__consent">
					<input type="checkbox" name="privacy" value="1" required>
					<span>
						<?php if ( $privacy_url ) : ?>
							<?php echo wp_kses_post( sprintf( __( '<a href="%s">Gizlilik politikasını</a> okudum ve bülten için iletişim kurulmasını kabul ediyorum.', 'asosyoloji' ), esc_url( $privacy_url ) ) ); ?>
						<?php else : ?>
							<?php esc_html_e( 'Bülten için iletişim kurulmasını kabul ediyorum.', 'asosyoloji' ); ?>
						<?php endif; ?>
					</span>
				</label>
			</form>
		</div>
	</section>
	<?php
	return (string) ob_get_clean();
}

/**
 * Process a newsletter subscription request without retaining visitor data.
 *
 * @return void
 */
function asosyoloji_handle_newsletter_form() {
	$nonce   = isset( $_POST['asosyoloji_newsletter_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['asosyoloji_newsletter_nonce'] ) ) : '';
	$trap    = isset( $_POST['company'] ) ? trim( (string) wp_unslash( $_POST['company'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$privacy = ! empty( $_POST['privacy'] );
	$key     = asosyoloji_form_rate_key( 'newsletter' );

	if ( ! wp_verify_nonce( $nonce, 'asosyoloji_newsletter' ) || $trap || ! is_email( $email ) || ! $privacy || get_transient( $key ) ) {
		asosyoloji_form_redirect( 'aso_newsletter', 'error' );
	}

	set_transient( $key, 1, 5 * MINUTE_IN_SECONDS );
	$subject = sprintf( '[%s] Bülten abonelik talebi', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
	$body    = sprintf( "Bülten abonelik talebi: %s\n\nAdres, site veritabanında saklanmadı; kullandığınız bülten sistemine ekleyip onay sürecini başlatın.", $email );
	$sent    = wp_mail( asosyoloji_form_recipient(), $subject, $body, array( 'Reply-To: ' . $email ) );

	asosyoloji_form_redirect( 'aso_newsletter', $sent ? 'success' : 'error' );
}
add_action( 'admin_post_asosyoloji_newsletter', 'asosyoloji_handle_newsletter_form' );
add_action( 'admin_post_nopriv_asosyoloji_newsletter', 'asosyoloji_handle_newsletter_form' );
