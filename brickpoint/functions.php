<?php
/**
 * BrickPoint theme bootstrap.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BRICKPOINT_VERSION', '2.0.0' );
define( 'BRICKPOINT_DIR', get_template_directory() );
define( 'BRICKPOINT_URI', get_template_directory_uri() );

$brickpoint_includes = array(
	'inc/helpers.php',
	'inc/setup.php',
	'inc/enqueue.php',
	'inc/post-types.php',
	'inc/taxonomies.php',
	'inc/meta-fields.php',
	'inc/whatsapp.php',
	'inc/icons.php',
	'inc/template-functions.php',
	'inc/customizer.php',
	'inc/contact-form.php',
	'inc/sections.php',
	'inc/content-defaults.php',
	'inc/elementor.php',
	'inc/elementor-widgets.php',
	'inc/elementor-dynamic-tags.php',
	'inc/admin.php',
	'inc/demo/class-elementor-templates.php',
	'inc/demo/class-demo-importer.php',
);

foreach ( $brickpoint_includes as $brickpoint_file ) {
	require BRICKPOINT_DIR . '/' . $brickpoint_file;
}
unset( $brickpoint_includes, $brickpoint_file );
