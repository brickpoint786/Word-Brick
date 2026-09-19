<?php
/**
 * Elementor widgets bootstrap.
 *
 * Every BrickPoint section has a matching Elementor widget. Widgets are
 * schema-driven: the schema (elementor/widgets/schema.php) declares controls,
 * the widget maps Elementor settings onto the same setting keys the PHP section
 * renderers (inc/sections.php) use, so Elementor output == PHP fallback output.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register widgets.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager Manager.
 */
require_once BRICKPOINT_DIR . '/elementor/widgets/schema.php';

function brickpoint_register_widgets( $widgets_manager ) {
	require_once BRICKPOINT_DIR . '/elementor/widgets/class-section-widget.php';
	require_once BRICKPOINT_DIR . '/elementor/widgets/class-template-widget.php';

	foreach ( bp_widget_schema() as $id => $schema ) {
		$widgets_manager->register( new \BrickPoint\Elementor\Section_Widget( array(), array( 'bp_schema_id' => $id ) ) );
	}
	foreach ( bp_template_widget_schema() as $id => $schema ) {
		$widgets_manager->register( new \BrickPoint\Elementor\Template_Widget( array(), array( 'bp_schema_id' => $id ) ) );
	}
}
add_action( 'elementor/widgets/register', 'brickpoint_register_widgets' );

/**
 * Editor panel styles (widget icons etc.) — preview styles are handled in inc/enqueue.php.
 */
function brickpoint_elementor_panel_styles() {
	wp_enqueue_style( 'brickpoint-editor', BRICKPOINT_URI . '/assets/css/editor.css', array(), BRICKPOINT_VERSION );
	wp_enqueue_style( 'brickpoint-icons', BRICKPOINT_URI . '/assets/css/icons.css', array(), BRICKPOINT_VERSION );
}
add_action( 'elementor/editor/after_enqueue_styles', 'brickpoint_elementor_panel_styles' );

/**
 * Theme JS inside the preview iframe (reveal animations, thumbs, marquee).
 */
function brickpoint_elementor_preview_scripts() {
	wp_enqueue_script( 'brickpoint-main', BRICKPOINT_URI . '/assets/js/main.js', array(), BRICKPOINT_VERSION, true );
}
add_action( 'elementor/preview/enqueue_scripts', 'brickpoint_elementor_preview_scripts' );

/**
 * Widget-friendly list of Elementor-style settings → section settings.
 * Converts repeater rows, media arrays and URL arrays into the plain values
 * bp_section_* functions expect.
 *
 * @param array $settings Elementor settings.
 * @param array $schema   Widget schema.
 * @return array
 */
function bp_widget_settings_to_section( $settings, $schema ) {
	$out = array();
	foreach ( $schema['controls'] as $key => $c ) {
		if ( ! isset( $settings[ $key ] ) ) {
			continue;
		}
		$v = $settings[ $key ];
		switch ( $c['type'] ) {
			case 'media':
				$out[ $key ] = is_array( $v ) ? ( ! empty( $v['id'] ) ? (int) $v['id'] : ( isset( $v['url'] ) ? $v['url'] : '' ) ) : $v;
				break;
			case 'url':
				$out[ $key ] = is_array( $v ) ? ( isset( $v['url'] ) ? $v['url'] : '' ) : $v;
				break;
			case 'icon':
				$out[ $key ] = bp_widget_icon_name( $v );
				break;
			case 'switch':
				$out[ $key ] = ( 'yes' === $v || true === $v || 1 === $v ) ? 'yes' : 'no';
				break;
			case 'repeater':
				$rows = array();
				foreach ( (array) $v as $row ) {
					$r = array();
					foreach ( $c['fields'] as $fk => $fc ) {
						if ( ! isset( $row[ $fk ] ) ) {
							continue;
						}
						$fv = $row[ $fk ];
						if ( 'media' === $fc['type'] ) {
							$fv = is_array( $fv ) ? ( ! empty( $fv['id'] ) ? (int) $fv['id'] : ( isset( $fv['url'] ) ? $fv['url'] : '' ) ) : $fv;
						} elseif ( 'url' === $fc['type'] ) {
							$new_tab = is_array( $fv ) && ! empty( $fv['is_external'] );
							$fv      = is_array( $fv ) ? ( isset( $fv['url'] ) ? $fv['url'] : '' ) : $fv;
							if ( $new_tab ) {
								$r['new_tab'] = 'yes';
							}
						} elseif ( 'icon' === $fc['type'] ) {
							$fv = bp_widget_icon_name( $fv );
						}
						$r[ $fk ] = $fv;
					}
					$rows[] = $r;
				}
				$out[ $key ] = $rows;
				break;
			default:
				$out[ $key ] = $v;
		}
	}
	return $out;
}

/**
 * Elementor ICONS control value → BrickPoint icon name (or raw class if from another library).
 *
 * @param mixed $v Value.
 * @return string
 */
function bp_widget_icon_name( $v ) {
	if ( is_string( $v ) ) {
		return $v;
	}
	if ( is_array( $v ) && ! empty( $v['value'] ) && is_string( $v['value'] ) ) {
		if ( preg_match( '/bpi-([a-z0-9-]+)/', $v['value'], $m ) ) {
			return $m[1];
		}
		return $v['value'];
	}
	return '';
}

/**
 * Icon HTML that accepts BrickPoint names or foreign icon classes (fa-*, eicon-*).
 * Used by bp_icon() indirectly through a filter.
 *
 * @param string $html  Existing HTML.
 * @param string $name  Icon name.
 * @param string $class Class.
 * @return string
 */
function brickpoint_foreign_icon( $html, $name, $class ) {
	if ( '' === $html && $name && preg_match( '/^(fa[srb]?|fab|eicon)[ -]/', $name ) ) {
		return '<i class="' . esc_attr( $name . ' ' . $class ) . '" aria-hidden="true"></i>';
	}
	return $html;
}
add_filter( 'brickpoint_icon_html', 'brickpoint_foreign_icon', 10, 3 );
