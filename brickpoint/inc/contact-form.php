<?php
/**
 * Quotation request form (shortcode + AJAX handler).
 * Replaces the original /api/contact endpoint. Stores inquiries as a private CPT
 * and emails the site owner. Works inside Elementor via the shortcode widget or
 * the "BP Quotation Form" widget.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the private inquiries post type.
 */
function brickpoint_register_inquiries() {
	register_post_type(
		'bp_inquiry',
		array(
			'labels'       => array(
				'name'          => __( 'Inquiries', 'brickpoint' ),
				'singular_name' => __( 'Inquiry', 'brickpoint' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-email-alt',
			'supports'     => array( 'title', 'editor' ),
			'capability_type' => 'post',
			'capabilities' => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap' => true,
		)
	);
}
add_action( 'init', 'brickpoint_register_inquiries' );

/**
 * Material options (from the original form).
 *
 * @return string[]
 */
function bp_material_options() {
	return array( 'SS7 Bricks', 'Bricks', 'Cement', 'Bajri / Crush', 'Sand / Rait', 'Steel', 'Pipes', 'Chemicals', 'Insulation', 'Cables', 'Paints', 'Lights', 'Switches', 'Multiple / Full List' );
}

/**
 * Render the form.
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function bp_contact_form_shortcode( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'title'  => __( 'Request a Quotation', 'brickpoint' ),
			'intro'  => __( 'Fields: name, phone, email, company, required material, quantity, location, message.', 'brickpoint' ),
			'button' => __( 'Send Quotation Request', 'brickpoint' ),
		),
		$atts,
		'brickpoint_contact_form'
	);
	$product = isset( $_GET['product'] ) ? sanitize_text_field( wp_unslash( $_GET['product'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	ob_start();
	?>
	<div class="bp-quote-form-wrap">
		<?php if ( $atts['title'] ) : ?><h2 class="bp-h2 sm"><?php echo esc_html( $atts['title'] ); ?></h2><?php endif; ?>
		<?php if ( $atts['intro'] ) : ?><p class="bp-muted sm"><?php echo esc_html( $atts['intro'] ); ?></p><?php endif; ?>
		<form class="bp-quote-form" method="post" novalidate>
			<?php wp_nonce_field( 'brickpoint_contact', 'bp_contact_nonce' ); ?>
			<input type="hidden" name="action" value="brickpoint_contact" />
			<input type="hidden" name="product" value="<?php echo esc_attr( $product ); ?>" />
			<input type="text" name="website" class="bp-hp" tabindex="-1" autocomplete="off" aria-hidden="true" />
			<div class="bp-field"><label for="bp_name"><?php esc_html_e( 'Full Name *', 'brickpoint' ); ?></label><input id="bp_name" name="name" required placeholder="<?php esc_attr_e( 'Your name', 'brickpoint' ); ?>" /></div>
			<div class="bp-field"><label for="bp_phone"><?php esc_html_e( 'Phone *', 'brickpoint' ); ?></label><input id="bp_phone" name="phone" required placeholder="03xx xxxxxxx" /></div>
			<div class="bp-field"><label for="bp_email"><?php esc_html_e( 'Email', 'brickpoint' ); ?></label><input id="bp_email" type="email" name="email" placeholder="you@email.com" /></div>
			<div class="bp-field"><label for="bp_company"><?php esc_html_e( 'Company', 'brickpoint' ); ?></label><input id="bp_company" name="company" placeholder="<?php esc_attr_e( 'Company / contractor name', 'brickpoint' ); ?>" /></div>
			<div class="bp-field"><label for="bp_material"><?php esc_html_e( 'Required Material', 'brickpoint' ); ?></label>
				<select id="bp_material" name="material"><option value=""><?php esc_html_e( 'Select material…', 'brickpoint' ); ?></option>
				<?php foreach ( bp_material_options() as $m ) : ?><option value="<?php echo esc_attr( $m ); ?>"><?php echo esc_html( $m ); ?></option><?php endforeach; ?>
				</select></div>
			<div class="bp-field"><label for="bp_quantity"><?php esc_html_e( 'Quantity', 'brickpoint' ); ?></label><input id="bp_quantity" name="quantity" placeholder="<?php esc_attr_e( 'e.g. 50,000 bricks / 200 bags', 'brickpoint' ); ?>" /></div>
			<div class="bp-field full"><label for="bp_location"><?php esc_html_e( 'Site / Delivery Location', 'brickpoint' ); ?></label><input id="bp_location" name="location" placeholder="<?php esc_attr_e( 'e.g. DHA Phase 6, Lahore', 'brickpoint' ); ?>" /></div>
			<div class="bp-field full"><label for="bp_message"><?php esc_html_e( 'Message', 'brickpoint' ); ?></label><textarea id="bp_message" name="message" rows="4" placeholder="<?php esc_attr_e( 'Share your full material list, sizes and timeline…', 'brickpoint' ); ?>"></textarea></div>
			<div class="bp-field full">
				<button type="submit" class="bp-btn btn-brick full lg"><?php bp_the_icon( 'send', 'bp-icon sm' ); ?> <span><?php echo esc_html( $atts['button'] ); ?></span></button>
				<p class="bp-form-error" hidden><?php esc_html_e( 'Something went wrong. Please try WhatsApp instead.', 'brickpoint' ); ?></p>
			</div>
		</form>
		<div class="bp-form-success" hidden>
			<?php bp_the_icon( 'check-circle', 'bp-icon xl green' ); ?>
			<h3 class="bp-h3"><?php esc_html_e( 'Inquiry received!', 'brickpoint' ); ?></h3>
			<p><?php esc_html_e( 'Thank you — our sales team will contact you shortly. For an instant response, message us on WhatsApp.', 'brickpoint' ); ?></p>
			<?php echo bp_whatsapp_button( bp_default_whatsapp_url(), __( 'Continue on WhatsApp', 'brickpoint' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'brickpoint_contact_form', 'bp_contact_form_shortcode' );

/**
 * AJAX handler.
 */
function brickpoint_contact_handler() {
	check_ajax_referer( 'brickpoint_contact', 'bp_contact_nonce' );
	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success( array( 'ok' => true ) ); // Honeypot: silently accept.
	}
	$fields = array( 'name', 'phone', 'email', 'company', 'material', 'quantity', 'location', 'message', 'product' );
	$data   = array();
	foreach ( $fields as $f ) {
		$raw        = isset( $_POST[ $f ] ) ? wp_unslash( $_POST[ $f ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$data[ $f ] = 'message' === $f ? sanitize_textarea_field( $raw ) : ( 'email' === $f ? sanitize_email( $raw ) : sanitize_text_field( $raw ) );
	}
	if ( '' === $data['name'] || '' === $data['phone'] ) {
		wp_send_json_error( array( 'message' => __( 'Name and phone are required.', 'brickpoint' ) ), 400 );
	}
	$body = '';
	foreach ( $data as $k => $v ) {
		if ( '' !== $v ) {
			$body .= ucfirst( $k ) . ': ' . $v . "\n";
		}
	}
	$post_id = wp_insert_post(
		array(
			'post_type'    => 'bp_inquiry',
			'post_status'  => 'private',
			/* translators: 1: name, 2: material */
			'post_title'   => sprintf( __( 'Quotation: %1$s — %2$s', 'brickpoint' ), $data['name'], $data['material'] ? $data['material'] : $data['product'] ),
			'post_content' => $body,
		),
		true
	);
	if ( ! is_wp_error( $post_id ) ) {
		foreach ( $data as $k => $v ) {
			update_post_meta( $post_id, '_bpi_' . $k, $v );
		}
	}
	$to = bp_get( 'bp_contact_to', get_option( 'admin_email' ) );
	wp_mail( $to, sprintf( '[%s] %s', get_bloginfo( 'name' ), __( 'New quotation request', 'brickpoint' ) ), $body );
	wp_send_json_success( array( 'ok' => true ) );
}
add_action( 'wp_ajax_brickpoint_contact', 'brickpoint_contact_handler' );
add_action( 'wp_ajax_nopriv_brickpoint_contact', 'brickpoint_contact_handler' );
