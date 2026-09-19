<?php
/**
 * Elementor dynamic tags: BrickPoint post fields (product price, SKU, WhatsApp
 * URL, video URL, location address …) and site settings (phone, email, socials).
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Field catalogue for the dynamic tags.
 *
 * @return array<string,array{label:string,type:string,meta?:string,cb?:string}>
 */
function bp_dynamic_fields() {
	return array(
		// Product.
		'product_price'      => array( 'label' => __( 'Product: Price', 'brickpoint' ), 'type' => 'text', 'meta' => '_bp_price' ),
		'product_unit'       => array( 'label' => __( 'Product: Unit', 'brickpoint' ), 'type' => 'text', 'meta' => '_bp_unit' ),
		'product_price_label' => array( 'label' => __( 'Product: Price label', 'brickpoint' ), 'type' => 'text', 'meta' => '_bp_price_label' ),
		'product_short'      => array( 'label' => __( 'Product: Short description', 'brickpoint' ), 'type' => 'text', 'meta' => '_bp_short' ),
		'product_sku'        => array( 'label' => __( 'Product: SKU', 'brickpoint' ), 'type' => 'text', 'meta' => '_bp_sku' ),
		'product_badge'      => array( 'label' => __( 'Product: Badge', 'brickpoint' ), 'type' => 'text', 'meta' => '_bp_badge' ),
		'product_availability' => array( 'label' => __( 'Product: Availability', 'brickpoint' ), 'type' => 'text', 'meta' => '_bp_availability' ),
		'product_specs'      => array( 'label' => __( 'Product: Specifications (table)', 'brickpoint' ), 'type' => 'text', 'cb' => 'bp_dyn_specs_html' ),
		'product_features'   => array( 'label' => __( 'Product: Features (list)', 'brickpoint' ), 'type' => 'text', 'cb' => 'bp_dyn_features_html' ),
		'product_whatsapp'   => array( 'label' => __( 'Product: WhatsApp order URL', 'brickpoint' ), 'type' => 'url', 'cb' => 'bp_dyn_product_wa' ),
		'product_video'      => array( 'label' => __( 'Product: Video URL', 'brickpoint' ), 'type' => 'url', 'meta' => '_bp_video' ),
		'product_brochure'   => array( 'label' => __( 'Product: Brochure URL', 'brickpoint' ), 'type' => 'url', 'meta' => '_bp_brochure' ),
		'product_gallery'    => array( 'label' => __( 'Product: Gallery', 'brickpoint' ), 'type' => 'gallery', 'meta' => '_bp_gallery' ),
		// Video.
		'video_url'          => array( 'label' => __( 'Video: URL', 'brickpoint' ), 'type' => 'url', 'meta' => '_bpv_url' ),
		'video_duration'     => array( 'label' => __( 'Video: Duration', 'brickpoint' ), 'type' => 'text', 'meta' => '_bpv_duration' ),
		'video_button_text'  => array( 'label' => __( 'Video: Button text', 'brickpoint' ), 'type' => 'text', 'meta' => '_bpv_button_text' ),
		'video_button_link'  => array( 'label' => __( 'Video: Button link', 'brickpoint' ), 'type' => 'url', 'meta' => '_bpv_button_link' ),
		// Project.
		'project_location'   => array( 'label' => __( 'Project: Location', 'brickpoint' ), 'type' => 'text', 'meta' => '_bpp_location' ),
		'project_status'     => array( 'label' => __( 'Project: Status', 'brickpoint' ), 'type' => 'text', 'meta' => '_bpp_status' ),
		'project_video'      => array( 'label' => __( 'Project: Video URL', 'brickpoint' ), 'type' => 'url', 'meta' => '_bpp_video' ),
		'project_gallery'    => array( 'label' => __( 'Project: Gallery', 'brickpoint' ), 'type' => 'gallery', 'meta' => '_bpp_gallery' ),
		// Location.
		'location_address'   => array( 'label' => __( 'Location: Address', 'brickpoint' ), 'type' => 'text', 'meta' => '_bpl_address' ),
		'location_phone'     => array( 'label' => __( 'Location: Phone', 'brickpoint' ), 'type' => 'text', 'meta' => '_bpl_phone' ),
		'location_hours'     => array( 'label' => __( 'Location: Hours', 'brickpoint' ), 'type' => 'text', 'meta' => '_bpl_hours' ),
		'location_badge'     => array( 'label' => __( 'Location: Badge', 'brickpoint' ), 'type' => 'text', 'meta' => '_bpl_badge' ),
		'location_maps'      => array( 'label' => __( 'Location: Google Maps URL', 'brickpoint' ), 'type' => 'url', 'meta' => '_bpl_maps' ),
		'location_video'     => array( 'label' => __( 'Location: Video URL', 'brickpoint' ), 'type' => 'url', 'meta' => '_bpl_video' ),
		// Site.
		'site_phone'         => array( 'label' => __( 'Site: Phone (display)', 'brickpoint' ), 'type' => 'text', 'cb' => 'bp_phone_display' ),
		'site_phone_link'    => array( 'label' => __( 'Site: Phone (tel: link)', 'brickpoint' ), 'type' => 'url', 'cb' => 'bp_dyn_tel' ),
		'site_email'         => array( 'label' => __( 'Site: Email', 'brickpoint' ), 'type' => 'text', 'cb' => 'bp_email' ),
		'site_address'       => array( 'label' => __( 'Site: Address', 'brickpoint' ), 'type' => 'text', 'cb' => 'bp_address' ),
		'site_whatsapp'      => array( 'label' => __( 'Site: WhatsApp URL', 'brickpoint' ), 'type' => 'url', 'cb' => 'bp_default_whatsapp_url' ),
		'site_ceo'           => array( 'label' => __( 'Site: CEO', 'brickpoint' ), 'type' => 'text', 'cb' => 'bp_dyn_ceo' ),
		'site_sales'         => array( 'label' => __( 'Site: Sales manager', 'brickpoint' ), 'type' => 'text', 'cb' => 'bp_dyn_sales' ),
		'site_facebook'      => array( 'label' => __( 'Site: Facebook URL', 'brickpoint' ), 'type' => 'url', 'cb' => 'bp_dyn_facebook' ),
		'site_instagram'     => array( 'label' => __( 'Site: Instagram URL', 'brickpoint' ), 'type' => 'url', 'cb' => 'bp_dyn_instagram' ),
		'site_twitter'       => array( 'label' => __( 'Site: X / Twitter URL', 'brickpoint' ), 'type' => 'url', 'cb' => 'bp_dyn_twitter' ),
		'site_tiktok'        => array( 'label' => __( 'Site: TikTok URL', 'brickpoint' ), 'type' => 'url', 'cb' => 'bp_dyn_tiktok' ),
		'site_maps'          => array( 'label' => __( 'Site: Office map URL', 'brickpoint' ), 'type' => 'url', 'cb' => 'bp_dyn_maps' ),
	);
}

/** Callback helpers (no-arg or post-id). */
function bp_dyn_specs_html( $id ) {
	$rows = bp_specs( bp_meta( $id, '_bp_specs' ) );
	if ( ! $rows ) {
		return '';
	}
	$h = '<dl class="bp-spec-table">';
	foreach ( $rows as $r ) {
		$h .= '<div><dt>' . esc_html( $r['label'] ) . '</dt><dd>' . esc_html( $r['value'] ) . '</dd></div>';
	}
	return $h . '</dl>';
}
function bp_dyn_features_html( $id ) {
	$rows = bp_lines( bp_meta( $id, '_bp_features' ) );
	if ( ! $rows ) {
		return '';
	}
	$h = '<ul class="bp-checklist">';
	foreach ( $rows as $r ) {
		$h .= '<li>' . bp_icon( 'check-circle', 'bp-icon green' ) . '<span>' . esc_html( $r ) . '</span></li>';
	}
	return $h . '</ul>';
}
function bp_dyn_product_wa( $id ) {
	return bp_product_whatsapp_url( $id );
}
function bp_dyn_tel() {
	return 'tel:+' . bp_phone_intl();
}
function bp_dyn_ceo() {
	return bp_get( 'bp_ceo' );
}
function bp_dyn_sales() {
	return bp_get( 'bp_sales' );
}
function bp_dyn_facebook() {
	return bp_social( 'facebook' );
}
function bp_dyn_instagram() {
	return bp_social( 'instagram' );
}
function bp_dyn_twitter() {
	return bp_social( 'twitter' );
}
function bp_dyn_tiktok() {
	return bp_social( 'tiktok' );
}
function bp_dyn_maps() {
	return bp_get( 'bp_office_maps' );
}

/**
 * Resolve a field value for a post.
 *
 * @param string $field Field key.
 * @param int    $post_id Post ID.
 * @return string
 */
function bp_dynamic_value( $field, $post_id = 0 ) {
	$fields = bp_dynamic_fields();
	if ( ! isset( $fields[ $field ] ) ) {
		return '';
	}
	$f = $fields[ $field ];
	if ( ! empty( $f['cb'] ) && function_exists( $f['cb'] ) ) {
		$rf = new ReflectionFunction( $f['cb'] );
		return (string) ( $rf->getNumberOfParameters() ? call_user_func( $f['cb'], $post_id ) : call_user_func( $f['cb'] ) );
	}
	if ( ! empty( $f['meta'] ) && $post_id ) {
		return (string) get_post_meta( $post_id, $f['meta'], true );
	}
	return '';
}

/**
 * Register tags.
 *
 * @param \Elementor\Core\DynamicTags\Manager $manager Manager.
 */
function brickpoint_register_dynamic_tags( $manager ) {
	require_once BRICKPOINT_DIR . '/elementor/dynamic-tags/class-field-tag.php';
	require_once BRICKPOINT_DIR . '/elementor/dynamic-tags/class-url-tag.php';
	require_once BRICKPOINT_DIR . '/elementor/dynamic-tags/class-gallery-tag.php';

	$manager->register_group( 'brickpoint', array( 'title' => __( 'BrickPoint', 'brickpoint' ) ) );
	$manager->register( new \BrickPoint\Elementor\Field_Tag() );
	$manager->register( new \BrickPoint\Elementor\Url_Tag() );
	$manager->register( new \BrickPoint\Elementor\Gallery_Tag() );
}
add_action( 'elementor/dynamic_tags/register', 'brickpoint_register_dynamic_tags' );
