<?php
/**
 * Inline SVG icon set (Lucide-style, matching the original design) and
 * registration of the same set as an Elementor icon library ("BrickPoint Icons").
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Icon path data. Every icon is a 24x24 stroke icon unless marked as fill.
 *
 * @return array<string,array{d:string,fill?:bool}>
 */
function bp_icon_set() {
	return array(
		'arrow-right'  => array( 'd' => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>' ),
		'chevron-right' => array( 'd' => '<path d="m9 18 6-6-6-6"/>' ),
		'chevron-down' => array( 'd' => '<path d="m6 9 6 6 6-6"/>' ),
		'play'         => array( 'd' => '<polygon points="6 3 20 12 6 21 6 3" fill="currentColor" stroke="none"/>' ),
		'phone'        => array( 'd' => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>' ),
		'mail'         => array( 'd' => '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>' ),
		'map-pin'      => array( 'd' => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>' ),
		'navigation'   => array( 'd' => '<polygon points="3 11 22 2 13 21 11 13 3 11"/>' ),
		'clock'        => array( 'd' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>' ),
		'calendar'     => array( 'd' => '<rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>' ),
		'user'         => array( 'd' => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>' ),
		'tag'          => array( 'd' => '<path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"/><circle cx="7.5" cy="7.5" r=".5" fill="currentColor"/>' ),
		'message-circle' => array( 'd' => '<path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>' ),
		'whatsapp'     => array( 'd' => '<path d="M17.5 14.4c-.3-.1-1.8-.9-2-1-.3-.1-.5-.1-.7.1-.2.3-.8 1-.9 1.2-.2.2-.3.2-.6.1-.3-.1-1.3-.5-2.4-1.5-.9-.8-1.5-1.8-1.7-2.1-.2-.3 0-.5.1-.6l.4-.5c.1-.2.2-.3.3-.5.1-.2 0-.4 0-.5C10 9 9.4 7.5 9.1 6.9c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4C6.7 7.1 6 7.8 6 9.3s1.1 3 1.2 3.2c.2.2 2.1 3.2 5.1 4.5 2.5 1 3 .8 3.6.8.6-.1 1.8-.7 2-1.4.3-.7.3-1.3.2-1.4-.1-.2-.3-.3-.6-.4zM12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2z"/>' ),
		'send'         => array( 'd' => '<path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>' ),
		'check-circle' => array( 'd' => '<path d="M21.801 10A10 10 0 1 1 17 3.335"/><path d="m9 11 3 3L22 4"/>' ),
		'badge-check'  => array( 'd' => '<path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/>' ),
		'shield-check' => array( 'd' => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>' ),
		'shield'       => array( 'd' => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/>' ),
		'truck'        => array( 'd' => '<path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/>' ),
		'factory'      => array( 'd' => '<path d="M2 20a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8l-7 5V8l-7 5V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M17 18h1M12 18h1M7 18h1"/>' ),
		'award'        => array( 'd' => '<path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"/><circle cx="12" cy="8" r="6"/>' ),
		'boxes'        => array( 'd' => '<path d="M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z"/><path d="m7 16.5-4.74-2.85M7 16.5l5-3M7 16.5v5.17"/><path d="M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z"/><path d="m17 16.5-5-3M17 16.5l4.74-2.85M17 16.5v5.17"/><path d="M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z"/><path d="M12 8 7.26 5.15M12 8l4.74-2.85M12 13.5V8"/>' ),
		'layers'       => array( 'd' => '<path d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"/><path d="M2 12a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 12"/><path d="M2 17a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 17"/>' ),
		'package'      => array( 'd' => '<path d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z"/><path d="M12 22V12"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="m7.5 4.27 9 5.15"/>' ),
		'mountain'     => array( 'd' => '<path d="m8 3 4 8 5-5 5 15H2L8 3z"/>' ),
		'waves'        => array( 'd' => '<path d="M2 6c.6.5 1.2 1 2.5 1C7 7 7 5 9.5 5c2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/><path d="M2 12c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/><path d="M2 18c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/>' ),
		'anchor'       => array( 'd' => '<path d="M12 22V8"/><path d="M5 12H2a10 10 0 0 0 20 0h-3"/><circle cx="12" cy="5" r="3"/>' ),
		'zap'          => array( 'd' => '<path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/>' ),
		'droplets'     => array( 'd' => '<path d="M7 16.3c2.2 0 4-1.83 4-4.05 0-1.16-.57-2.26-1.71-3.19S7.29 6.75 7 5.3c-.29 1.45-1.14 2.84-2.29 3.76S3 11.1 3 12.25c0 2.22 1.8 4.05 4 4.05z"/><path d="M12.56 6.6A10.97 10.97 0 0 0 14 3.02c.5 2.5 2 4.9 4 6.5s3 3.5 3 5.5a6.98 6.98 0 0 1-11.91 4.97"/>' ),
		'flask'        => array( 'd' => '<path d="M10 2v7.527a2 2 0 0 1-.211.896L4.72 20.55a1 1 0 0 0 .9 1.45h12.76a1 1 0 0 0 .9-1.45l-5.069-10.127A2 2 0 0 1 14 9.527V2"/><path d="M8.5 2h7"/><path d="M7 16h10"/>' ),
		'plug'         => array( 'd' => '<path d="M12 22v-5"/><path d="M9 8V2"/><path d="M15 8V2"/><path d="M18 8v5a4 4 0 0 1-4 4h-4a4 4 0 0 1-4-4V8Z"/>' ),
		'paintbrush'   => array( 'd' => '<path d="m14.622 17.897-10.68-2.913"/><path d="M18.376 2.622a1 1 0 1 1 3.002 3.002L17.36 9.643a.5.5 0 0 0 0 .707l.944.944a2.41 2.41 0 0 1 0 3.408l-.944.944a.5.5 0 0 1-.707 0L8.354 7.348a.5.5 0 0 1 0-.707l.944-.944a2.41 2.41 0 0 1 3.408 0l.944.944a.5.5 0 0 0 .707 0z"/><path d="M9 8c-1.804 2.71-3.97 3.46-6.583 3.948a.507.507 0 0 0-.302.819l7.32 8.883a1 1 0 0 0 1.185.204C12.735 20.405 16 16.792 16 15"/>' ),
		'lightbulb'    => array( 'd' => '<path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/>' ),
		'toggle'       => array( 'd' => '<rect width="20" height="12" x="2" y="6" rx="6" ry="6"/><circle cx="16" cy="12" r="2"/>' ),
		'users'        => array( 'd' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>' ),
		'target'       => array( 'd' => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/>' ),
		'eye'          => array( 'd' => '<path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/>' ),
		'heart'        => array( 'd' => '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>' ),
		'ruler'        => array( 'd' => '<path d="M21.3 15.3a2.4 2.4 0 0 1 0 3.4l-2.6 2.6a2.4 2.4 0 0 1-3.4 0L2.7 8.7a2.41 2.41 0 0 1 0-3.4l2.6-2.6a2.41 2.41 0 0 1 3.4 0Z"/><path d="m14.5 12.5 2-2"/><path d="m11.5 9.5 2-2"/><path d="m8.5 6.5 2-2"/><path d="m17.5 15.5 2-2"/>' ),
		'palette'      => array( 'd' => '<circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/><circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/><circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/><circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"/>' ),
		'file-text'    => array( 'd' => '<path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/>' ),
		'share'        => array( 'd' => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.59 13.51 6.83 3.98M15.41 6.51l-6.82 3.98"/>' ),
		'menu'         => array( 'd' => '<path d="M4 12h16M4 6h16M4 18h16"/>' ),
		'x'            => array( 'd' => '<path d="M18 6 6 18M6 6l12 12"/>' ),
		'search'       => array( 'd' => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>' ),
		'brick-logo'   => array( 'd' => '<rect x="2" y="4" width="9" height="4" rx="1" fill="currentColor" stroke="none"/><rect x="13" y="4" width="9" height="4" rx="1" fill="currentColor" stroke="none"/><rect x="7.5" y="10" width="9" height="4" rx="1" fill="currentColor" stroke="none"/><rect x="2" y="16" width="9" height="4" rx="1" fill="currentColor" stroke="none"/><rect x="13" y="16" width="9" height="4" rx="1" fill="currentColor" stroke="none"/>' ),
		'facebook'     => array( 'd' => '<path d="M13.5 21v-7h2.4l.4-3h-2.8V9.1c0-.9.3-1.5 1.6-1.5h1.3V4.9c-.3 0-1.1-.1-2-.1-2 0-3.4 1.2-3.4 3.5V11H7.5v3H10v7h3.5Z" fill="currentColor" stroke="none"/>' ),
		'instagram'    => array( 'd' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r="1.2" fill="currentColor" stroke="none"/>' ),
		'x-twitter'    => array( 'd' => '<path d="M17.7 3H21l-7.1 8.2L22.2 21h-6.6l-5.1-6.1L4.6 21H1.3l7.6-8.7L1.8 3h6.7l4.6 5.6L17.7 3Zm-1.2 16h1.8L7.1 4.9H5.2L16.5 19Z" fill="currentColor" stroke="none"/>' ),
		'tiktok'       => array( 'd' => '<path d="M16.6 5.82A4.28 4.28 0 0 1 15.54 3h-3.09v12.4a2.59 2.59 0 0 1-2.59 2.5c-1.42 0-2.6-1.16-2.6-2.6 0-1.72 1.66-3.01 3.37-2.48V9.66c-3.45-.46-6.47 2.22-6.47 5.64 0 3.33 2.76 5.7 5.69 5.7 3.14 0 5.69-2.55 5.69-5.7V9.01a7.35 7.35 0 0 0 4.3 1.38V7.3s-1.88.09-3.24-1.48z" fill="currentColor" stroke="none"/>' ),
	);
}

/**
 * Inline SVG icon.
 *
 * @param string $name  Icon key.
 * @param string $class CSS class.
 * @return string
 */
function bp_icon( $name, $class = 'bp-icon' ) {
	$set = bp_icon_set();
	if ( empty( $set[ $name ] ) ) {
		/**
		 * Allow foreign icon libraries (Font Awesome / eicons from Elementor) to render.
		 *
		 * @param string $html  HTML.
		 * @param string $name  Icon name/class.
		 * @param string $class CSS class.
		 */
		return apply_filters( 'brickpoint_icon_html', '', (string) $name, $class );
	}
	return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $set[ $name ]['d'] . '</svg>';
}

/**
 * Echo an icon.
 *
 * @param string $name  Icon key.
 * @param string $class Class.
 */
function bp_the_icon( $name, $class = 'bp-icon' ) {
	echo bp_icon( $name, $class ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static trusted SVG.
}

/**
 * Write the icon set to /assets/icons as SVG files (used by the importer and
 * the Elementor icon library so icons stay editable/replaceable).
 *
 * @return string Directory path.
 */
function bp_icons_dir() {
	$dir = BRICKPOINT_DIR . '/assets/icons';
	if ( ! is_dir( $dir ) ) {
		wp_mkdir_p( $dir );
	}
	return $dir;
}

/**
 * Register the BrickPoint icon set as an Elementor icon library.
 *
 * @param array $tabs Icon tabs.
 * @return array
 */
function brickpoint_elementor_icon_library( $tabs ) {
	$names = array_keys( bp_icon_set() );
	$tabs['brickpoint'] = array(
		'name'          => 'brickpoint',
		'label'         => __( 'BrickPoint Icons', 'brickpoint' ),
		'url'           => BRICKPOINT_URI . '/assets/css/icons.css',
		'enqueue'       => array( BRICKPOINT_URI . '/assets/css/icons.css' ),
		'prefix'        => 'bpi-',
		'displayPrefix' => 'bpi',
		'labelIcon'     => 'bpi bpi-brick-logo',
		'ver'           => BRICKPOINT_VERSION,
		'fetchJson'     => BRICKPOINT_URI . '/assets/icons/brickpoint-icons.json',
		'native'        => false,
		'icons'         => $names,
	);
	return $tabs;
}
add_filter( 'elementor/icons_manager/additional_tabs', 'brickpoint_elementor_icon_library' );
