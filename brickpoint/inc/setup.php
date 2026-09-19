<?php
/**
 * Theme setup.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme supports, image sizes and menus.
 */
function brickpoint_setup() {
	load_theme_textdomain( 'brickpoint', BRICKPOINT_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' )
	);
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/editor.css' );

	// Elementor.
	add_theme_support( 'elementor' );
	add_theme_support( 'elementor-theme-builder' );
	add_theme_support( 'elementor-pro-locations' );

	add_image_size( 'bp-card', 800, 600, true );     // 4:3 product cards.
	add_image_size( 'bp-wide', 1200, 750, true );    // 16:10 project / category cards.
	add_image_size( 'bp-video', 1280, 720, true );   // 16:9 video thumbnails.
	add_image_size( 'bp-hero', 1600, 900, true );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'brickpoint' ),
			'footer'  => __( 'Footer Menu', 'brickpoint' ),
			'mobile'  => __( 'Mobile Menu', 'brickpoint' ),
		)
	);
}
add_action( 'after_setup_theme', 'brickpoint_setup' );

/**
 * Content width.
 */
function brickpoint_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'brickpoint_content_width', 1200 ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
}
add_action( 'after_setup_theme', 'brickpoint_content_width', 0 );

/**
 * Widget areas.
 */
function brickpoint_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Blog Sidebar', 'brickpoint' ),
			'id'            => 'sidebar-1',
			'before_widget' => '<section id="%1$s" class="widget bp-widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		)
	);
}
add_action( 'widgets_init', 'brickpoint_widgets_init' );

/**
 * Body classes.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function brickpoint_body_classes( $classes ) {
	$classes[] = 'brickpoint';
	if ( bp_has_elementor_pro() ) {
		$classes[] = 'bp-has-elementor-pro';
	}
	return $classes;
}
add_filter( 'body_class', 'brickpoint_body_classes' );

/**
 * Register a flush of rewrite rules on theme switch (post types need it).
 */
function brickpoint_after_switch() {
	brickpoint_register_post_types();
	brickpoint_register_taxonomies();
	flush_rewrite_rules();
	if ( ! get_option( 'brickpoint_activation_redirect_done' ) ) {
		update_option( 'brickpoint_activation_redirect', 1 );
	}
}
add_action( 'after_switch_theme', 'brickpoint_after_switch' );

/**
 * Redirect to the demo importer once after activation.
 */
function brickpoint_activation_redirect() {
	if ( ! get_option( 'brickpoint_activation_redirect' ) || wp_doing_ajax() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	delete_option( 'brickpoint_activation_redirect' );
	update_option( 'brickpoint_activation_redirect_done', 1 );
	if ( isset( $_GET['activated'] ) && 'themes.php' === $GLOBALS['pagenow'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_safe_redirect( admin_url( 'themes.php?page=brickpoint-demo' ) );
		exit;
	}
}
add_action( 'admin_init', 'brickpoint_activation_redirect' );
