<?php
/**
 * Front page. If the homepage is built with Elementor, the Elementor content is
 * shown (via page.php). Otherwise the PHP fallback renders the original home sections.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' ) && bp_is_elementor_post( (int) get_option( 'page_on_front' ) ) ) {
	include get_template_directory() . '/page.php';
	return;
}

get_header();
if ( ! brickpoint_do_location( 'single' ) ) {
	$bp_pages = bp_demo_pages();
	bp_render_sections( $bp_pages['home']['sections'] );
}
get_footer();
