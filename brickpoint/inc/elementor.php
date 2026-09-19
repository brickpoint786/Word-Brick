<?php
/**
 * Elementor / Elementor Pro integration.
 *
 * - Registers Theme Builder locations (header, footer, single, archive).
 * - Registers the "BrickPoint" widget category.
 * - Makes sure Elementor Pro templates win over PHP fallbacks (no conflicts).
 * - Provides bp_render_template() to render saved Elementor templates by slug/ID
 *   when Pro is not present (header/footer fallback to Elementor free).
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Elementor Pro theme locations.
 *
 * @param \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $manager Manager.
 */
function brickpoint_register_elementor_locations( $manager ) {
	$manager->register_all_core_location();
}
add_action( 'elementor/theme/register_locations', 'brickpoint_register_elementor_locations' );

/**
 * Widget category.
 *
 * @param \Elementor\Elements_Manager $elements_manager Manager.
 */
function brickpoint_elementor_category( $elements_manager ) {
	$elements_manager->add_category(
		'brickpoint',
		array(
			'title' => __( 'BrickPoint', 'brickpoint' ),
			'icon'  => 'eicon-apps',
		)
	);
}
add_action( 'elementor/elements/categories_registered', 'brickpoint_elementor_category' );

/**
 * Elementor Pro location wrapper.
 *
 * @param string $location header|footer|single|archive.
 * @return bool True when Elementor Pro rendered the location.
 */
function brickpoint_do_location( $location ) {
	if ( function_exists( 'elementor_theme_do_location' ) ) {
		return (bool) elementor_theme_do_location( $location );
	}
	return false;
}

/**
 * Render a saved Elementor template (library) by its stored ID.
 * Used so header/footer stay Elementor-editable even with Elementor Free.
 *
 * @param string $key   Option key (brickpoint_tpl_header / brickpoint_tpl_footer ...).
 * @return bool
 */
function bp_render_saved_template( $key ) {
	if ( ! bp_has_elementor() ) {
		return false;
	}
	$id = (int) get_option( $key );
	if ( ! $id || 'publish' !== get_post_status( $id ) ) {
		return false;
	}
	$content = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $id, true );
	if ( ! $content ) {
		return false;
	}
	echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor output.
	return true;
}

/**
 * Header: Elementor Pro location → saved Elementor template → PHP fallback.
 *
 * @return bool True if rendered by Elementor.
 */
function bp_elementor_header() {
	if ( brickpoint_do_location( 'header' ) ) {
		return true;
	}
	return bp_render_saved_template( 'brickpoint_tpl_header' );
}

/**
 * Footer.
 *
 * @return bool
 */
function bp_elementor_footer() {
	if ( brickpoint_do_location( 'footer' ) ) {
		return true;
	}
	return bp_render_saved_template( 'brickpoint_tpl_footer' );
}

/**
 * Elementor "Canvas"/"Full width" documents render their own header/footer, so
 * make sure our theme header uses the same wrapper Elementor expects.
 */
function brickpoint_elementor_page_templates_support() {
	if ( bp_has_elementor() ) {
		add_post_type_support( 'bp_product', 'elementor' );
		add_post_type_support( 'bp_video', 'elementor' );
		add_post_type_support( 'bp_project', 'elementor' );
		add_post_type_support( 'bp_location', 'elementor' );
	}
}
add_action( 'init', 'brickpoint_elementor_page_templates_support', 20 );

/**
 * Sensible Elementor defaults so pages look like the design (container width etc.).
 * Applied by the importer, also applied on first activation when Elementor is present.
 */
function brickpoint_elementor_defaults() {
	if ( get_option( 'elementor_container_width' ) === false ) {
		update_option( 'elementor_container_width', 1200 );
	}
	if ( get_option( 'elementor_disable_color_schemes' ) === false ) {
		update_option( 'elementor_disable_color_schemes', 'yes' );
	}
	if ( get_option( 'elementor_disable_typography_schemes' ) === false ) {
		update_option( 'elementor_disable_typography_schemes', 'yes' );
	}
	update_option( 'elementor_unfiltered_files_upload', '1' );
	update_option( 'elementor_experiment-container', 'active' );
	update_option( 'elementor_experiment-e_optimized_css_loading', 'inactive' );
}
add_action( 'elementor/loaded', 'brickpoint_elementor_defaults' );

/**
 * Disable Elementor's default colors/fonts globally so the BrickPoint CSS applies.
 * (Elementor "Kit" defaults are also written by the importer.)
 *
 * @param array $settings Kit settings.
 * @return array
 */
function brickpoint_elementor_kit_defaults( $settings ) {
	return $settings;
}

/**
 * Body class helper when Elementor Pro header/footer is active.
 *
 * @param string[] $classes Classes.
 * @return string[]
 */
function brickpoint_elementor_body_class( $classes ) {
	if ( function_exists( 'elementor_location_exits' ) && elementor_location_exits( 'header', true ) ) {
		$classes[] = 'bp-elementor-header';
	}
	return $classes;
}
add_filter( 'body_class', 'brickpoint_elementor_body_class' );

/**
 * Register Elementor Pro Theme Builder display conditions for the templates
 * created by the importer. Called by the importer after templates are created.
 *
 * @param int    $template_id Template ID.
 * @param array  $conditions  Conditions e.g. ['include/general'].
 */
function bp_set_elementor_conditions( $template_id, $conditions ) {
	update_post_meta( $template_id, '_elementor_conditions', $conditions );
	if ( ! class_exists( '\ElementorPro\Plugin' ) ) {
		return;
	}
	try {
		$module = \ElementorPro\Modules\ThemeBuilder\Module::instance();
		$cm     = $module->get_conditions_manager();
		$doc    = \Elementor\Plugin::instance()->documents->get( $template_id );
		if ( $doc && method_exists( $cm, 'save_conditions' ) ) {
			$cm->save_conditions( $template_id, $conditions );
		} elseif ( $doc ) {
			$cm->get_cache()->regenerate();
		}
	} catch ( \Throwable $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
		// Conditions stored as post meta; Pro will pick them up on cache regeneration.
	}
}
