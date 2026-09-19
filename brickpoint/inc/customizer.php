<?php
/**
 * Customizer: BrickPoint contact / social / design settings.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register settings.
 *
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function brickpoint_customize( $wp_customize ) {
	$defaults = bp_defaults();

	$wp_customize->add_panel( 'brickpoint', array( 'title' => __( 'BrickPoint Settings', 'brickpoint' ), 'priority' => 25 ) );

	// Contact.
	$wp_customize->add_section( 'bp_contact', array( 'title' => __( 'Contact & WhatsApp', 'brickpoint' ), 'panel' => 'brickpoint' ) );
	$fields = array(
		'bp_phone_display'   => array( __( 'Phone (display)', 'brickpoint' ), 'text' ),
		'bp_whatsapp_number' => array( __( 'WhatsApp number (international, no +)', 'brickpoint' ), 'text' ),
		'bp_email'           => array( __( 'Email', 'brickpoint' ), 'email' ),
		'bp_address'         => array( __( 'Address', 'brickpoint' ), 'text' ),
		'bp_office_maps'     => array( __( 'Head office Google Maps URL', 'brickpoint' ), 'url' ),
		'bp_ceo'             => array( __( 'CEO name', 'brickpoint' ), 'text' ),
		'bp_sales'           => array( __( 'Sales Manager name', 'brickpoint' ), 'text' ),
		'bp_companies'       => array( __( 'Company units (comma separated)', 'brickpoint' ), 'text' ),
		'bp_default_wa'      => array( __( 'Default WhatsApp message', 'brickpoint' ), 'textarea' ),
		'bp_copyright'       => array( __( 'Footer copyright ({year} is replaced)', 'brickpoint' ), 'text' ),
		'bp_contact_to'      => array( __( 'Quotation form recipient email', 'brickpoint' ), 'email' ),
	);
	foreach ( $fields as $key => $def ) {
		list( $label, $type ) = $def;
		$sanitize = 'sanitize_text_field';
		if ( 'email' === $type ) {
			$sanitize = 'sanitize_email';
		} elseif ( 'textarea' === $type ) {
			$sanitize = 'sanitize_textarea_field';
		} elseif ( 'url' === $type ) {
			$sanitize = 'esc_url_raw';
		}
		$wp_customize->add_setting( $key, array( 'default' => isset( $defaults[ $key ] ) ? $defaults[ $key ] : '', 'sanitize_callback' => $sanitize ) );
		$wp_customize->add_control( $key, array( 'label' => $label, 'section' => 'bp_contact', 'type' => $type ) );
	}
	$wp_customize->add_setting( 'bp_float_wa', array( 'default' => 'show', 'sanitize_callback' => 'sanitize_key' ) );
	$wp_customize->add_control( 'bp_float_wa', array( 'label' => __( 'Floating WhatsApp button', 'brickpoint' ), 'section' => 'bp_contact', 'type' => 'select', 'choices' => array( 'show' => __( 'Show', 'brickpoint' ), 'hide' => __( 'Hide', 'brickpoint' ) ) ) );

	// Social.
	$wp_customize->add_section( 'bp_social', array( 'title' => __( 'Social Links', 'brickpoint' ), 'panel' => 'brickpoint' ) );
	foreach ( array( 'facebook' => __( 'Facebook URL', 'brickpoint' ), 'instagram' => __( 'Instagram URL', 'brickpoint' ), 'twitter' => __( 'X / Twitter URL', 'brickpoint' ), 'tiktok' => __( 'TikTok URL', 'brickpoint' ) ) as $net => $label ) {
		$wp_customize->add_setting( 'bp_social_' . $net, array( 'default' => $defaults[ 'bp_social_' . $net ], 'sanitize_callback' => 'esc_url_raw' ) );
		$wp_customize->add_control( 'bp_social_' . $net, array( 'label' => $label, 'section' => 'bp_social', 'type' => 'url' ) );
	}

	// Design tokens.
	$wp_customize->add_section( 'bp_design', array( 'title' => __( 'Colors & Design', 'brickpoint' ), 'panel' => 'brickpoint' ) );
	foreach ( array(
		'bp_primary'   => array( __( 'Brick (primary)', 'brickpoint' ), '#c2410c' ),
		'bp_accent'    => array( __( 'Ember (accent)', 'brickpoint' ), '#ea580c' ),
		'bp_secondary' => array( __( 'Ink (dark)', 'brickpoint' ), '#141210' ),
		'bp_whatsapp_color' => array( __( 'WhatsApp green', 'brickpoint' ), '#128c4b' ),
	) as $key => $def ) {
		$wp_customize->add_setting( $key, array( 'default' => $def[1], 'sanitize_callback' => 'sanitize_hex_color' ) );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $key, array( 'label' => $def[0], 'section' => 'bp_design' ) ) );
	}
	$wp_customize->add_setting( 'bp_radius', array( 'default' => 18, 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'bp_radius', array( 'label' => __( 'Card radius (px)', 'brickpoint' ), 'section' => 'bp_design', 'type' => 'number' ) );
	$wp_customize->add_setting( 'bp_container', array( 'default' => 1200, 'sanitize_callback' => 'absint' ) );
	$wp_customize->add_control( 'bp_container', array( 'label' => __( 'Container width (px)', 'brickpoint' ), 'section' => 'bp_design', 'type' => 'number' ) );
}
add_action( 'customize_register', 'brickpoint_customize' );

/**
 * Output CSS variables from Customizer values.
 */
function brickpoint_css_vars() {
	$css = sprintf(
		':root{--bp-brick:%1$s;--bp-ember:%2$s;--bp-ink:%3$s;--bp-wa:%4$s;--bp-radius:%5$dpx;--bp-container:%6$dpx;}',
		esc_attr( get_theme_mod( 'bp_primary', '#c2410c' ) ),
		esc_attr( get_theme_mod( 'bp_accent', '#ea580c' ) ),
		esc_attr( get_theme_mod( 'bp_secondary', '#141210' ) ),
		esc_attr( get_theme_mod( 'bp_whatsapp_color', '#128c4b' ) ),
		absint( get_theme_mod( 'bp_radius', 18 ) ),
		absint( get_theme_mod( 'bp_container', 1200 ) )
	);
	wp_add_inline_style( 'brickpoint-main', $css );
}
add_action( 'wp_enqueue_scripts', 'brickpoint_css_vars', 20 );
