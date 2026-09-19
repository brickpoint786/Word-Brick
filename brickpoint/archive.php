<?php
/**
 * Archive dispatcher: CPT archives, taxonomy archives, categories/tags.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
if ( brickpoint_do_location( 'archive' ) ) {
	get_footer();
	return;
}

get_template_part( 'template-parts/archive/loop' );
get_footer();
