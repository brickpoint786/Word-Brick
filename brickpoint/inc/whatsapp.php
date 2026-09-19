<?php
/**
 * WhatsApp ordering helpers (no WooCommerce).
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Build a wa.me link.
 *
 * @param string      $message Prefilled text.
 * @param string|null $phone   International number (digits only).
 * @return string
 */
function bp_whatsapp_url( $message = '', $phone = null ) {
	if ( null === $phone || '' === $phone ) {
		$phone = bp_phone_intl();
	}
	$url = 'https://wa.me/' . preg_replace( '/\D/', '', (string) $phone );
	if ( '' !== $message ) {
		$url .= '?text=' . rawurlencode( $message );
	}
	return $url;
}

/**
 * Default site WhatsApp link.
 *
 * @return string
 */
function bp_default_whatsapp_url() {
	return bp_whatsapp_url( (string) bp_get( 'bp_default_wa' ) );
}

/**
 * Product inquiry message (mirrors the original productInquiryMessage()).
 *
 * @param int $post_id Product ID.
 * @return string
 */
function bp_product_whatsapp_message( $post_id ) {
	$custom = bp_meta( $post_id, '_bp_whatsapp', '' );
	if ( $custom ) {
		return $custom;
	}
	$cat   = bp_first_term_name( $post_id, 'bp_product_category' );
	$price = trim( bp_meta( $post_id, '_bp_price', '' ) . ' ' . bp_meta( $post_id, '_bp_price_label', '' ) );
	$unit  = bp_meta( $post_id, '_bp_unit', '' );
	$lines = array(
		__( 'Assalam-o-Alaikum BrickPoint,', 'brickpoint' ),
		'',
		__( 'I am interested in the following product:', 'brickpoint' ),
		'',
		/* translators: %s: product name */
		sprintf( __( 'Product: %s', 'brickpoint' ), get_the_title( $post_id ) ),
		/* translators: %s: category */
		sprintf( __( 'Category: %s', 'brickpoint' ), $cat ? $cat : '-' ),
		/* translators: %s: price */
		sprintf( __( 'Price: %s', 'brickpoint' ), $price ? $price : __( 'Please quote', 'brickpoint' ) ),
		/* translators: %s: unit */
		sprintf( __( 'Unit: %s', 'brickpoint' ), $unit ? $unit : '-' ),
		'',
		__( 'Please share availability, delivery details, and final quotation.', 'brickpoint' ),
		'',
		__( 'Thank you.', 'brickpoint' ),
	);
	return implode( "\n", $lines );
}

/**
 * Product WhatsApp URL (respects the per-product override).
 *
 * @param int $post_id Product ID.
 * @return string
 */
function bp_product_whatsapp_url( $post_id ) {
	$override = bp_meta( $post_id, '_bp_whatsapp_url', '' );
	if ( $override ) {
		return esc_url( $override );
	}
	return bp_whatsapp_url( bp_product_whatsapp_message( $post_id ) );
}

/**
 * Category inquiry message.
 *
 * @param string $cat_name Category name.
 * @return string
 */
function bp_category_whatsapp_message( $cat_name ) {
	return implode(
		"\n",
		array(
			__( 'Assalam-o-Alaikum BrickPoint,', 'brickpoint' ),
			'',
			/* translators: %s: category */
			sprintf( __( 'I want a quotation for: %s', 'brickpoint' ), $cat_name ),
			'',
			__( 'Please share price, availability and delivery details.', 'brickpoint' ),
			'',
			__( 'Thank you.', 'brickpoint' ),
		)
	);
}

/**
 * Category WhatsApp URL (respects term override).
 *
 * @param WP_Term $term Term.
 * @return string
 */
function bp_category_whatsapp_url( $term ) {
	$custom = get_term_meta( $term->term_id, 'bp_cat_whatsapp', true );
	return bp_whatsapp_url( $custom ? $custom : bp_category_whatsapp_message( $term->name ) );
}

/**
 * Render a WhatsApp button.
 *
 * @param string $url   Link.
 * @param string $label Label.
 * @param string $class Extra classes.
 * @param bool   $icon  Show icon.
 * @return string
 */
function bp_whatsapp_button( $url, $label = '', $class = '', $icon = true ) {
	if ( '' === $label ) {
		$label = __( 'Order on WhatsApp', 'brickpoint' );
	}
	return sprintf(
		'<a class="bp-btn btn-whatsapp %1$s" target="_blank" rel="noopener" href="%2$s">%3$s<span>%4$s</span></a>',
		esc_attr( $class ),
		esc_url( $url ),
		$icon ? bp_icon( 'whatsapp' ) : '', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG from bp_icon().
		esc_html( $label )
	);
}
