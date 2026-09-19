<?php
/**
 * Meta boxes for Products, Videos, Projects and Locations.
 *
 * All keys are exposed to Elementor Pro's "Custom Field" dynamic tags and to the
 * BrickPoint dynamic tags (inc/elementor-dynamic-tags.php).
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Field definitions per post type.
 *
 * @return array<string,array<string,array{label:string,type:string,desc?:string}>>
 */
function brickpoint_meta_definitions() {
	return array(
		'bp_product'  => array(
			'_bp_short'        => array( 'label' => __( 'Short description', 'brickpoint' ), 'type' => 'textarea', 'desc' => __( 'Shown on product cards.', 'brickpoint' ) ),
			'_bp_price'        => array( 'label' => __( 'Price', 'brickpoint' ), 'type' => 'text', 'desc' => __( 'e.g. Rs. 14,500 or "Rs. Contact for Rate". Leave empty for "Price on request".', 'brickpoint' ) ),
			'_bp_price_label'  => array( 'label' => __( 'Price label', 'brickpoint' ), 'type' => 'text', 'desc' => __( 'e.g. Market-competitive bulk pricing', 'brickpoint' ) ),
			'_bp_unit'         => array( 'label' => __( 'Unit', 'brickpoint' ), 'type' => 'text', 'desc' => __( 'e.g. 1000 bricks, bag, trolley, kg', 'brickpoint' ) ),
			'_bp_availability' => array( 'label' => __( 'Availability', 'brickpoint' ), 'type' => 'text', 'desc' => __( 'e.g. In Stock', 'brickpoint' ) ),
			'_bp_badge'        => array( 'label' => __( 'Badge', 'brickpoint' ), 'type' => 'text', 'desc' => __( 'e.g. Best Seller, Popular, New', 'brickpoint' ) ),
			'_bp_sku'          => array( 'label' => __( 'SKU / Reference', 'brickpoint' ), 'type' => 'text' ),
			'_bp_gallery'      => array( 'label' => __( 'Gallery images', 'brickpoint' ), 'type' => 'gallery', 'desc' => __( 'Additional product images.', 'brickpoint' ) ),
			'_bp_specs'        => array( 'label' => __( 'Specifications', 'brickpoint' ), 'type' => 'textarea', 'desc' => __( 'One per line as Label: Value', 'brickpoint' ) ),
			'_bp_features'     => array( 'label' => __( 'Key features', 'brickpoint' ), 'type' => 'textarea', 'desc' => __( 'One per line', 'brickpoint' ) ),
			'_bp_video'        => array( 'label' => __( 'Product video URL', 'brickpoint' ), 'type' => 'url', 'desc' => __( 'MP4 / YouTube / Vimeo', 'brickpoint' ) ),
			'_bp_brochure'     => array( 'label' => __( 'Brochure / PDF URL', 'brickpoint' ), 'type' => 'url' ),
			'_bp_cta_text'     => array( 'label' => __( 'CTA button text', 'brickpoint' ), 'type' => 'text', 'desc' => __( 'Default: Request Quote', 'brickpoint' ) ),
			'_bp_cta_link'     => array( 'label' => __( 'CTA button link', 'brickpoint' ), 'type' => 'url', 'desc' => __( 'Default: Contact page', 'brickpoint' ) ),
			'_bp_whatsapp'     => array( 'label' => __( 'WhatsApp message override', 'brickpoint' ), 'type' => 'textarea', 'desc' => __( 'Leave empty to auto-generate the product inquiry message.', 'brickpoint' ) ),
			'_bp_whatsapp_url' => array( 'label' => __( 'WhatsApp link override', 'brickpoint' ), 'type' => 'url', 'desc' => __( 'Full wa.me URL. Leave empty to auto-generate.', 'brickpoint' ) ),
			'_bp_related'      => array( 'label' => __( 'Related product IDs', 'brickpoint' ), 'type' => 'text', 'desc' => __( 'Comma separated. Leave empty to use same-category products.', 'brickpoint' ) ),
			'_bp_featured'     => array( 'label' => __( 'Featured product', 'brickpoint' ), 'type' => 'checkbox' ),
		),
		'bp_video'    => array(
			'_bpv_url'        => array( 'label' => __( 'Video URL', 'brickpoint' ), 'type' => 'url', 'desc' => __( 'MP4 / YouTube / Vimeo. The thumbnail is the Featured Image.', 'brickpoint' ) ),
			'_bpv_source'     => array( 'label' => __( 'Source type', 'brickpoint' ), 'type' => 'text', 'desc' => __( 'mp4, youtube or vimeo (auto-detected when empty).', 'brickpoint' ) ),
			'_bpv_duration'   => array( 'label' => __( 'Duration', 'brickpoint' ), 'type' => 'text', 'desc' => __( 'e.g. 2:30', 'brickpoint' ) ),
			'_bpv_button_text' => array( 'label' => __( 'Button text', 'brickpoint' ), 'type' => 'text', 'desc' => __( 'Default: Related Products', 'brickpoint' ) ),
			'_bpv_button_link' => array( 'label' => __( 'Button link', 'brickpoint' ), 'type' => 'url', 'desc' => __( 'Default: Products archive', 'brickpoint' ) ),
			'_bpv_captions'   => array( 'label' => __( 'Captions (VTT) URL', 'brickpoint' ), 'type' => 'url' ),
			'_bpv_related_products' => array( 'label' => __( 'Related product IDs', 'brickpoint' ), 'type' => 'text' ),
			'_bpv_featured'   => array( 'label' => __( 'Featured video', 'brickpoint' ), 'type' => 'checkbox' ),
		),
		'bp_project'  => array(
			'_bpp_location'     => array( 'label' => __( 'Location', 'brickpoint' ), 'type' => 'text', 'desc' => __( 'e.g. DHA Lahore', 'brickpoint' ) ),
			'_bpp_status'       => array( 'label' => __( 'Status label', 'brickpoint' ), 'type' => 'text', 'desc' => __( 'Default: Illustrative construction reference', 'brickpoint' ) ),
			'_bpp_gallery'      => array( 'label' => __( 'Gallery', 'brickpoint' ), 'type' => 'gallery' ),
			'_bpp_video'        => array( 'label' => __( 'Project video URL', 'brickpoint' ), 'type' => 'url' ),
			'_bpp_link'         => array( 'label' => __( 'Project link', 'brickpoint' ), 'type' => 'url', 'desc' => __( 'Optional external link.', 'brickpoint' ) ),
			'_bpp_cta_text'     => array( 'label' => __( 'CTA text', 'brickpoint' ), 'type' => 'text', 'desc' => __( 'Default: Build Like This — Get Quote', 'brickpoint' ) ),
			'_bpp_cta_link'     => array( 'label' => __( 'CTA link', 'brickpoint' ), 'type' => 'url', 'desc' => __( 'Default: Contact page', 'brickpoint' ) ),
			'_bpp_related'      => array( 'label' => __( 'Related product IDs', 'brickpoint' ), 'type' => 'text' ),
			'_bpp_featured'     => array( 'label' => __( 'Featured project', 'brickpoint' ), 'type' => 'checkbox' ),
			'_bpp_illustrative' => array( 'label' => __( 'Illustrative / inspiration visual (not a claimed BrickPoint project)', 'brickpoint' ), 'type' => 'checkbox' ),
		),
		'bp_location' => array(
			'_bpl_address'  => array( 'label' => __( 'Address', 'brickpoint' ), 'type' => 'text' ),
			'_bpl_maps'     => array( 'label' => __( 'Google Maps URL', 'brickpoint' ), 'type' => 'url' ),
			'_bpl_phone'    => array( 'label' => __( 'Phone', 'brickpoint' ), 'type' => 'text' ),
			'_bpl_whatsapp' => array( 'label' => __( 'WhatsApp number', 'brickpoint' ), 'type' => 'text', 'desc' => __( 'International format without +. Leave empty to use the site number.', 'brickpoint' ) ),
			'_bpl_hours'    => array( 'label' => __( 'Opening hours', 'brickpoint' ), 'type' => 'text' ),
			'_bpl_lat'      => array( 'label' => __( 'Latitude', 'brickpoint' ), 'type' => 'text' ),
			'_bpl_lng'      => array( 'label' => __( 'Longitude', 'brickpoint' ), 'type' => 'text' ),
			'_bpl_video'    => array( 'label' => __( 'Location video URL', 'brickpoint' ), 'type' => 'url' ),
			'_bpl_badge'    => array( 'label' => __( 'Badge', 'brickpoint' ), 'type' => 'text', 'desc' => __( 'e.g. Unit 1, Head Office', 'brickpoint' ) ),
		),
	);
}

/**
 * Register meta so it is visible to the REST API / Elementor dynamic "Custom Field" tag.
 */
function brickpoint_register_meta() {
	foreach ( brickpoint_meta_definitions() as $type => $fields ) {
		foreach ( $fields as $key => $f ) {
			register_post_meta(
				$type,
				$key,
				array(
					'show_in_rest'  => true,
					'single'        => true,
					'type'          => 'string',
					'auth_callback' => function () {
						return current_user_can( 'edit_posts' );
					},
					'sanitize_callback' => 'sanitize_textarea_field',
				)
			);
		}
	}
}
add_action( 'init', 'brickpoint_register_meta' );

/**
 * Add the meta boxes.
 */
function brickpoint_add_meta_boxes() {
	$titles = array(
		'bp_product'  => __( 'Product Details', 'brickpoint' ),
		'bp_video'    => __( 'Video Details', 'brickpoint' ),
		'bp_project'  => __( 'Project Details', 'brickpoint' ),
		'bp_location' => __( 'Location Details', 'brickpoint' ),
	);
	foreach ( $titles as $type => $title ) {
		add_meta_box( 'bp_' . $type . '_details', $title, 'brickpoint_render_meta_box', $type, 'normal', 'high' );
	}
}
add_action( 'add_meta_boxes', 'brickpoint_add_meta_boxes' );

/**
 * Render a meta box.
 *
 * @param WP_Post $post Post.
 */
function brickpoint_render_meta_box( $post ) {
	$defs = brickpoint_meta_definitions();
	if ( empty( $defs[ $post->post_type ] ) ) {
		return;
	}
	wp_nonce_field( 'bp_meta_' . $post->post_type, 'bp_meta_nonce' );
	echo '<div class="bp-meta-grid">';
	foreach ( $defs[ $post->post_type ] as $key => $f ) {
		$val = get_post_meta( $post->ID, $key, true );
		echo '<div class="bp-meta-field bp-meta-' . esc_attr( $f['type'] ) . '">';
		if ( 'checkbox' === $f['type'] ) {
			echo '<label><input type="checkbox" name="' . esc_attr( $key ) . '" value="1"' . checked( $val, '1', false ) . ' /> <strong>' . esc_html( $f['label'] ) . '</strong></label>';
		} else {
			echo '<label for="' . esc_attr( $key ) . '"><strong>' . esc_html( $f['label'] ) . '</strong></label>';
			if ( 'textarea' === $f['type'] ) {
				echo '<textarea id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" rows="3" class="large-text">' . esc_textarea( $val ) . '</textarea>';
			} elseif ( 'gallery' === $f['type'] ) {
				$ids = array_filter( array_map( 'absint', explode( ',', (string) $val ) ) );
				echo '<div class="bp-gallery-field"><input type="hidden" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( implode( ',', $ids ) ) . '" /><div class="bp-gallery-preview">';
				foreach ( $ids as $id ) {
					echo wp_get_attachment_image( $id, 'thumbnail' );
				}
				echo '</div><button type="button" class="button bp-gallery-select">' . esc_html__( 'Select images', 'brickpoint' ) . '</button> <button type="button" class="button-link-delete bp-gallery-clear">' . esc_html__( 'Clear', 'brickpoint' ) . '</button></div>';
			} else {
				echo '<input type="' . esc_attr( 'url' === $f['type'] ? 'url' : 'text' ) . '" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" class="large-text" />';
			}
		}
		if ( ! empty( $f['desc'] ) ) {
			echo '<p class="description">' . esc_html( $f['desc'] ) . '</p>';
		}
		echo '</div>';
	}
	echo '</div>';
	echo '<p class="description">' . esc_html__( 'Use the main editor for the full description and the Featured Image panel for the main image / video thumbnail.', 'brickpoint' ) . '</p>';
}

/**
 * Save meta.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post.
 */
function brickpoint_save_meta( $post_id, $post ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	$defs = brickpoint_meta_definitions();
	if ( empty( $defs[ $post->post_type ] ) ) {
		return;
	}
	if ( ! isset( $_POST['bp_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bp_meta_nonce'] ) ), 'bp_meta_' . $post->post_type ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( $defs[ $post->post_type ] as $key => $f ) {
		if ( 'checkbox' === $f['type'] ) {
			update_post_meta( $post_id, $key, isset( $_POST[ $key ] ) ? '1' : '0' );
			continue;
		}
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}
		$raw = wp_unslash( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below.
		switch ( $f['type'] ) {
			case 'url':
				$val = esc_url_raw( $raw );
				break;
			case 'textarea':
				$val = sanitize_textarea_field( $raw );
				break;
			case 'gallery':
				$val = implode( ',', array_filter( array_map( 'absint', explode( ',', $raw ) ) ) );
				break;
			default:
				$val = sanitize_text_field( $raw );
		}
		update_post_meta( $post_id, $key, $val );
	}
}
add_action( 'save_post', 'brickpoint_save_meta', 10, 2 );
