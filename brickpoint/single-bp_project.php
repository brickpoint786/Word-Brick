<?php
/**
 * Single project reference.
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
	get_template_part( 'template-parts/single/project' );
endwhile;
get_footer();
