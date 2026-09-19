<?php
/**
 * Taxonomies + term meta (image, icon, banner, video, WhatsApp message).
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register taxonomies.
 */
function brickpoint_register_taxonomies() {
	register_taxonomy(
		'bp_product_category',
		'bp_product',
		array(
			'labels'            => array(
				'name'          => __( 'Product Categories', 'brickpoint' ),
				'singular_name' => __( 'Product Category', 'brickpoint' ),
				'add_new_item'  => __( 'Add New Product Category', 'brickpoint' ),
				'edit_item'     => __( 'Edit Product Category', 'brickpoint' ),
				'all_items'     => __( 'All Product Categories', 'brickpoint' ),
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'categories', 'with_front' => false ),
		)
	);
	register_taxonomy(
		'bp_video_category',
		'bp_video',
		array(
			'labels'            => array(
				'name'          => __( 'Video Categories', 'brickpoint' ),
				'singular_name' => __( 'Video Category', 'brickpoint' ),
				'add_new_item'  => __( 'Add New Video Category', 'brickpoint' ),
				'edit_item'     => __( 'Edit Video Category', 'brickpoint' ),
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'video-category', 'with_front' => false ),
		)
	);
	register_taxonomy(
		'bp_project_category',
		'bp_project',
		array(
			'labels'            => array(
				'name'          => __( 'Project Categories', 'brickpoint' ),
				'singular_name' => __( 'Project Category', 'brickpoint' ),
				'add_new_item'  => __( 'Add New Project Category', 'brickpoint' ),
				'edit_item'     => __( 'Edit Project Category', 'brickpoint' ),
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'project-category', 'with_front' => false ),
		)
	);
}
add_action( 'init', 'brickpoint_register_taxonomies', 4 );

/**
 * Term meta field definitions.
 *
 * @return array<string,array{label:string,type:string,desc:string}>
 */
function brickpoint_term_meta_fields() {
	return array(
		'bp_cat_image'    => array(
			'label' => __( 'Category image', 'brickpoint' ),
			'type'  => 'image',
			'desc'  => __( 'Shown on category cards (16:10). Select from the Media Library.', 'brickpoint' ),
		),
		'bp_cat_banner'   => array(
			'label' => __( 'Category banner', 'brickpoint' ),
			'type'  => 'image',
			'desc'  => __( 'Optional wide banner used behind the category page title.', 'brickpoint' ),
		),
		'bp_cat_icon'     => array(
			'label' => __( 'Category icon', 'brickpoint' ),
			'type'  => 'text',
			'desc'  => __( 'Icon key (layers, award, package, mountain, waves, anchor, zap, droplets, flask, shield, plug, paintbrush, lightbulb, toggle, boxes).', 'brickpoint' ),
		),
		'bp_cat_video'    => array(
			'label' => __( 'Featured video URL', 'brickpoint' ),
			'type'  => 'url',
			'desc'  => __( 'Optional MP4 / YouTube / Vimeo URL shown on the category page.', 'brickpoint' ),
		),
		'bp_cat_whatsapp' => array(
			'label' => __( 'WhatsApp message override', 'brickpoint' ),
			'type'  => 'textarea',
			'desc'  => __( 'Optional prefilled WhatsApp message for this category.', 'brickpoint' ),
		),
		'bp_cat_order'    => array(
			'label' => __( 'Sort order', 'brickpoint' ),
			'type'  => 'number',
			'desc'  => '',
		),
	);
}

/**
 * Render term meta fields (add + edit forms).
 *
 * @param WP_Term|string $term Term object or taxonomy slug on the add form.
 */
function brickpoint_tax_meta_fields( $term = null ) {
	$is_edit = $term instanceof WP_Term;
	$id      = $is_edit ? $term->term_id : 0;
	wp_nonce_field( 'bp_term_meta', 'bp_term_meta_nonce' );
	foreach ( brickpoint_term_meta_fields() as $key => $field ) {
		$value = $id ? get_term_meta( $id, $key, true ) : '';
		$input = '';
		switch ( $field['type'] ) {
			case 'image':
				$preview = $value ? bp_image_url( $value, 'thumbnail' ) : '';
				$input   = '<div class="bp-media-field">'
					. '<input type="hidden" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" />'
					. '<img src="' . esc_url( $preview ) . '" alt="" class="bp-media-preview"' . ( $preview ? '' : ' style="display:none"' ) . ' />'
					. '<button type="button" class="button bp-media-select">' . esc_html__( 'Select image', 'brickpoint' ) . '</button> '
					. '<button type="button" class="button-link-delete bp-media-remove"' . ( $value ? '' : ' style="display:none"' ) . '>' . esc_html__( 'Remove', 'brickpoint' ) . '</button>'
					. '</div>';
				break;
			case 'textarea':
				$input = '<textarea name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" rows="3" class="large-text">' . esc_textarea( $value ) . '</textarea>';
				break;
			case 'number':
				$input = '<input type="number" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" class="small-text" />';
				break;
			default:
				$input = '<input type="' . esc_attr( $field['type'] ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" class="regular-text" />';
		}
		$desc = $field['desc'] ? '<p class="description">' . esc_html( $field['desc'] ) . '</p>' : '';
		if ( $is_edit ) {
			echo '<tr class="form-field"><th scope="row"><label for="' . esc_attr( $key ) . '">' . esc_html( $field['label'] ) . '</label></th><td>' . $input . $desc . '</td></tr>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
		} else {
			echo '<div class="form-field"><label for="' . esc_attr( $key ) . '">' . esc_html( $field['label'] ) . '</label>' . $input . $desc . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

/**
 * Save term meta.
 *
 * @param int $term_id Term ID.
 */
function brickpoint_tax_meta_save( $term_id ) {
	if ( ! isset( $_POST['bp_term_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bp_term_meta_nonce'] ) ), 'bp_term_meta' ) ) {
		return;
	}
	if ( ! current_user_can( 'manage_categories' ) ) {
		return;
	}
	foreach ( brickpoint_term_meta_fields() as $key => $field ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			continue;
		}
		$raw = wp_unslash( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized below per type.
		switch ( $field['type'] ) {
			case 'image':
				$val = is_numeric( $raw ) ? absint( $raw ) : esc_url_raw( $raw );
				break;
			case 'url':
				$val = esc_url_raw( $raw );
				break;
			case 'number':
				$val = (int) $raw;
				break;
			case 'textarea':
				$val = sanitize_textarea_field( $raw );
				break;
			default:
				$val = sanitize_text_field( $raw );
		}
		update_term_meta( $term_id, $key, $val );
	}
}

foreach ( array( 'bp_product_category', 'bp_video_category', 'bp_project_category' ) as $bp_tax ) {
	add_action( $bp_tax . '_add_form_fields', 'brickpoint_tax_meta_fields' );
	add_action( $bp_tax . '_edit_form_fields', 'brickpoint_tax_meta_fields' );
	add_action( 'created_' . $bp_tax, 'brickpoint_tax_meta_save' );
	add_action( 'edited_' . $bp_tax, 'brickpoint_tax_meta_save' );
}
unset( $bp_tax );

/**
 * Get product categories ordered by bp_cat_order.
 *
 * @param array $args get_terms args.
 * @return WP_Term[]
 */
function bp_get_product_categories( $args = array() ) {
	$terms = get_terms(
		wp_parse_args(
			$args,
			array(
				'taxonomy'   => 'bp_product_category',
				'hide_empty' => false,
				'meta_key'   => 'bp_cat_order', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'orderby'    => 'meta_value_num',
				'order'      => 'ASC',
			)
		)
	);
	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		$terms = get_terms( array( 'taxonomy' => 'bp_product_category', 'hide_empty' => false ) );
	}
	return is_wp_error( $terms ) ? array() : $terms;
}

/**
 * Category permalink — the SS7 category links to the SS7 landing page like the original site.
 *
 * @param WP_Term $term Term.
 * @return string
 */
function bp_category_url( $term ) {
	if ( 'ss7-bricks' === $term->slug && 'bp_product_category' === $term->taxonomy ) {
		return bp_page_url( 'ss7-bricks' );
	}
	$link = get_term_link( $term );
	return is_wp_error( $link ) ? home_url( '/' ) : $link;
}
