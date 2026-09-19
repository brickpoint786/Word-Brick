<?php
/**
 * Single product (PHP fallback; Elementor Pro single location takes precedence).
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
if ( brickpoint_do_location( 'single' ) ) {
	get_footer();
	return;
}

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/single/product' );
endwhile;
get_footer();
