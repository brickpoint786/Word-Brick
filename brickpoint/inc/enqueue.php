<?php
/**
 * Scripts & styles.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Front-end assets.
 */
function brickpoint_enqueue_assets() {
	wp_enqueue_style(
		'brickpoint-fonts',
		'https://fonts.googleapis.com/css2?family=Archivo:wght@600;700;800;900&family=Inter:wght@400;500;600;700&display=swap',
		array(),
		null // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	);
	wp_enqueue_style( 'brickpoint-main', BRICKPOINT_URI . '/assets/css/main.css', array( 'brickpoint-fonts' ), BRICKPOINT_VERSION );
	wp_enqueue_style( 'brickpoint-responsive', BRICKPOINT_URI . '/assets/css/responsive.css', array( 'brickpoint-main' ), BRICKPOINT_VERSION );
	wp_enqueue_style( 'brickpoint-animations', BRICKPOINT_URI . '/assets/css/animations.css', array( 'brickpoint-main' ), BRICKPOINT_VERSION );
	wp_enqueue_style( 'brickpoint-style', get_stylesheet_uri(), array( 'brickpoint-main' ), BRICKPOINT_VERSION );

	wp_enqueue_script( 'brickpoint-main', BRICKPOINT_URI . '/assets/js/main.js', array(), BRICKPOINT_VERSION, true );
	wp_localize_script(
		'brickpoint-main',
		'brickpointData',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'brickpoint_front' ),
		)
	);

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'brickpoint_enqueue_assets' );

/**
 * Make the theme CSS available inside the Elementor editor so widgets look identical.
 */
function brickpoint_elementor_editor_styles() {
	wp_enqueue_style( 'brickpoint-fonts-editor', 'https://fonts.googleapis.com/css2?family=Archivo:wght@600;700;800;900&family=Inter:wght@400;500;600;700&display=swap', array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
	wp_enqueue_style( 'brickpoint-main', BRICKPOINT_URI . '/assets/css/main.css', array(), BRICKPOINT_VERSION );
	wp_enqueue_style( 'brickpoint-responsive', BRICKPOINT_URI . '/assets/css/responsive.css', array( 'brickpoint-main' ), BRICKPOINT_VERSION );
	wp_enqueue_style( 'brickpoint-animations', BRICKPOINT_URI . '/assets/css/animations.css', array( 'brickpoint-main' ), BRICKPOINT_VERSION );
}
add_action( 'elementor/preview/enqueue_styles', 'brickpoint_elementor_editor_styles' );

/**
 * Admin assets for the demo importer page and meta boxes.
 *
 * @param string $hook Current admin page.
 */
function brickpoint_admin_assets( $hook ) {
	wp_enqueue_style( 'brickpoint-admin', BRICKPOINT_URI . '/assets/css/admin.css', array(), BRICKPOINT_VERSION );
	if ( 'appearance_page_brickpoint-demo' === $hook ) {
		wp_enqueue_script( 'brickpoint-demo', BRICKPOINT_URI . '/assets/js/demo-import.js', array( 'jquery' ), BRICKPOINT_VERSION, true );
		wp_localize_script(
			'brickpoint-demo',
			'brickpointDemo',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'brickpoint_demo_import' ),
				'i18n'    => array(
					'running' => __( 'Importing…', 'brickpoint' ),
					'done'    => __( 'BrickPoint Demo Imported Successfully', 'brickpoint' ),
					'error'   => __( 'Import stopped with an error. You can safely click Import again to resume.', 'brickpoint' ),
				),
			)
		);
	}
	if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		wp_enqueue_media();
		wp_enqueue_script( 'brickpoint-admin', BRICKPOINT_URI . '/assets/js/admin.js', array( 'jquery' ), BRICKPOINT_VERSION, true );
	}
}
add_action( 'admin_enqueue_scripts', 'brickpoint_admin_assets' );
