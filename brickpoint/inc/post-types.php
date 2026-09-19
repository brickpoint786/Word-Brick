<?php
/**
 * Custom post types: bp_product, bp_video, bp_project, bp_location.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register post types.
 */
function brickpoint_register_post_types() {
	$common = array(
		'public'       => true,
		'has_archive'  => true,
		'show_in_rest' => true,
		'menu_position' => 20,
	);

	register_post_type(
		'bp_product',
		array_merge(
			$common,
			array(
				'labels'    => array(
					'name'               => __( 'Products', 'brickpoint' ),
					'singular_name'      => __( 'Product', 'brickpoint' ),
					'add_new'            => __( 'Add New', 'brickpoint' ),
					'add_new_item'       => __( 'Add New Product', 'brickpoint' ),
					'edit_item'          => __( 'Edit Product', 'brickpoint' ),
					'new_item'           => __( 'New Product', 'brickpoint' ),
					'view_item'          => __( 'View Product', 'brickpoint' ),
					'search_items'       => __( 'Search Products', 'brickpoint' ),
					'not_found'          => __( 'No products found', 'brickpoint' ),
					'all_items'          => __( 'All Products', 'brickpoint' ),
					'archives'           => __( 'Product Archives', 'brickpoint' ),
				),
				'rewrite'   => array( 'slug' => 'products', 'with_front' => false ),
				'menu_icon' => 'dashicons-building',
				'supports'  => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes', 'custom-fields' ),
				'taxonomies' => array( 'bp_product_category' ),
			)
		)
	);

	register_post_type(
		'bp_video',
		array_merge(
			$common,
			array(
				'labels'    => array(
					'name'          => __( 'Videos', 'brickpoint' ),
					'singular_name' => __( 'Video', 'brickpoint' ),
					'add_new_item'  => __( 'Add New Video', 'brickpoint' ),
					'edit_item'     => __( 'Edit Video', 'brickpoint' ),
					'all_items'     => __( 'All Videos', 'brickpoint' ),
					'search_items'  => __( 'Search Videos', 'brickpoint' ),
					'not_found'     => __( 'No videos found', 'brickpoint' ),
				),
				'rewrite'   => array( 'slug' => 'videos', 'with_front' => false ),
				'menu_icon' => 'dashicons-video-alt3',
				'supports'  => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes', 'custom-fields' ),
				'taxonomies' => array( 'bp_video_category' ),
			)
		)
	);

	register_post_type(
		'bp_project',
		array_merge(
			$common,
			array(
				'labels'    => array(
					'name'          => __( 'Projects', 'brickpoint' ),
					'singular_name' => __( 'Project', 'brickpoint' ),
					'add_new_item'  => __( 'Add New Project', 'brickpoint' ),
					'edit_item'     => __( 'Edit Project', 'brickpoint' ),
					'all_items'     => __( 'All Projects', 'brickpoint' ),
					'search_items'  => __( 'Search Projects', 'brickpoint' ),
					'not_found'     => __( 'No projects found', 'brickpoint' ),
				),
				'rewrite'   => array( 'slug' => 'projects', 'with_front' => false ),
				'menu_icon' => 'dashicons-portfolio',
				'supports'  => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes', 'custom-fields' ),
				'taxonomies' => array( 'bp_project_category' ),
			)
		)
	);

	register_post_type(
		'bp_location',
		array_merge(
			$common,
			array(
				'labels'    => array(
					'name'          => __( 'Locations', 'brickpoint' ),
					'singular_name' => __( 'Location', 'brickpoint' ),
					'add_new_item'  => __( 'Add New Location', 'brickpoint' ),
					'edit_item'     => __( 'Edit Location', 'brickpoint' ),
					'all_items'     => __( 'All Locations', 'brickpoint' ),
					'search_items'  => __( 'Search Locations', 'brickpoint' ),
					'not_found'     => __( 'No locations found', 'brickpoint' ),
				),
				'rewrite'   => array( 'slug' => 'locations', 'with_front' => false ),
				'menu_icon' => 'dashicons-location-alt',
				'supports'  => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes', 'custom-fields' ),
			)
		)
	);
}
add_action( 'init', 'brickpoint_register_post_types', 5 );

/**
 * Order location/product archives by menu_order.
 *
 * @param WP_Query $query Query.
 */
function brickpoint_archive_ordering( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( $query->is_post_type_archive( array( 'bp_location', 'bp_product', 'bp_project', 'bp_video' ) ) ) {
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
		$query->set( 'posts_per_page', 24 );
	}
	if ( $query->is_tax( array( 'bp_product_category', 'bp_video_category', 'bp_project_category' ) ) ) {
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'date' => 'DESC' ) );
		$query->set( 'posts_per_page', 24 );
	}
}
add_action( 'pre_get_posts', 'brickpoint_archive_ordering' );

/**
 * Let Elementor edit our custom post types by default.
 *
 * @param array $types Supported types.
 * @return array
 */
function brickpoint_elementor_cpt_support( $types ) {
	foreach ( array( 'post', 'page', 'bp_product', 'bp_video', 'bp_project', 'bp_location' ) as $t ) {
		if ( ! in_array( $t, (array) $types, true ) ) {
			$types[] = $t;
		}
	}
	return $types;
}
add_filter( 'option_elementor_cpt_support', 'brickpoint_elementor_cpt_support' );
add_filter( 'default_option_elementor_cpt_support', 'brickpoint_elementor_cpt_support' );
