<?php
/**
 * Helper functions shared across templates, widgets and the importer.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default site-wide settings (mirrors the original BrickPoint SITE config).
 *
 * @return array<string,string>
 */
function bp_defaults() {
	return array(
		'bp_brand'           => 'BrickPoint',
		'bp_tagline'         => 'Bricks & Materials',
		'bp_phone_display'   => '0315 2850818',
		'bp_whatsapp_number' => '923152850818',
		'bp_email'           => 'info@brickpoint.pk',
		'bp_address'         => 'Lahore, Punjab, Pakistan',
		'bp_ceo'             => 'Syed Iftikhar Haider',
		'bp_sales'           => 'Qasim Iqbal',
		'bp_companies'       => 'Masha Allah Bricks Company, Fine Bricks Company, SS7 Bricks',
		'bp_default_wa'      => 'Assalam-o-Alaikum BrickPoint, I need a quotation for construction materials.',
		'bp_copyright'       => '© {year} BrickPoint. All rights reserved.',
		'bp_social_facebook' => 'https://www.facebook.com/brickpoint.pk/',
		'bp_social_instagram' => 'https://www.instagram.com/brickpoint.pk/',
		'bp_social_twitter'  => 'https://x.com/BrickPointPK',
		'bp_social_tiktok'   => 'https://www.tiktok.com/@brickpoint.pk/',
		'bp_office_maps'     => 'https://maps.app.goo.gl/GACXw15YxyV4bK5t8?g_st=awb',
	);
}

/**
 * Read a theme mod with the BrickPoint default.
 *
 * @param string $key     Setting key.
 * @param mixed  $default Fallback.
 * @return mixed
 */
function bp_get( $key, $default = null ) {
	$defaults = bp_defaults();
	if ( null === $default && isset( $defaults[ $key ] ) ) {
		$default = $defaults[ $key ];
	}
	$value = get_theme_mod( $key, $default );
	return ( '' === $value || null === $value ) ? $default : $value;
}

/** @return string */
function bp_phone_display() {
	return (string) bp_get( 'bp_phone_display' );
}

/** @return string */
function bp_phone_intl() {
	$p = preg_replace( '/\D/', '', (string) bp_get( 'bp_whatsapp_number' ) );
	return $p ? $p : '923152850818';
}

/** @return string */
function bp_email() {
	return sanitize_email( bp_get( 'bp_email' ) );
}

/** @return string */
function bp_address() {
	return (string) bp_get( 'bp_address' );
}

/**
 * Social URL for a network.
 *
 * @param string $network facebook|instagram|twitter|tiktok.
 * @return string
 */
function bp_social( $network ) {
	return esc_url( bp_get( 'bp_social_' . $network, '' ) );
}

/**
 * Copyright line with {year} replaced.
 *
 * @return string
 */
function bp_copyright() {
	return str_replace( '{year}', gmdate( 'Y' ), (string) bp_get( 'bp_copyright' ) );
}

/**
 * Post meta with default.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Meta key.
 * @param mixed  $default Default.
 * @return mixed
 */
function bp_meta( $post_id, $key, $default = '' ) {
	$v = get_post_meta( $post_id, $key, true );
	return ( '' === $v || null === $v ) ? $default : $v;
}

/**
 * Term image URL (stored as attachment ID or URL).
 *
 * @param int    $term_id Term.
 * @param string $key     Meta key.
 * @param string $size    Image size.
 * @return string
 */
function bp_term_image( $term_id, $key = 'bp_cat_image', $size = 'bp-card' ) {
	$v = get_term_meta( $term_id, $key, true );
	if ( is_numeric( $v ) ) {
		$src = wp_get_attachment_image_url( (int) $v, $size );
		return $src ? $src : '';
	}
	return $v ? esc_url( $v ) : '';
}

/**
 * Resolve an image value (attachment ID or URL) to a URL.
 *
 * @param mixed  $value Attachment ID or URL.
 * @param string $size  Size.
 * @return string
 */
function bp_image_url( $value, $size = 'large' ) {
	if ( is_array( $value ) ) {
		if ( ! empty( $value['id'] ) ) {
			$src = wp_get_attachment_image_url( (int) $value['id'], $size );
			if ( $src ) {
				return $src;
			}
		}
		return ! empty( $value['url'] ) ? esc_url( $value['url'] ) : '';
	}
	if ( is_numeric( $value ) ) {
		$src = wp_get_attachment_image_url( (int) $value, $size );
		return $src ? $src : '';
	}
	return $value ? esc_url( $value ) : '';
}

/**
 * Page URL by slug with a fallback path.
 *
 * @param string $slug Page slug.
 * @return string
 */
function bp_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	if ( $page instanceof WP_Post ) {
		return get_permalink( $page );
	}
	return home_url( '/' . trim( $slug, '/' ) . '/' );
}

/**
 * Archive URL helper for custom post types.
 *
 * @param string $type Post type.
 * @return string
 */
function bp_archive_url( $type ) {
	$url = get_post_type_archive_link( $type );
	return $url ? $url : home_url( '/' );
}

/**
 * Split a "one per line" textarea into a clean array.
 *
 * @param string $text Raw text.
 * @return string[]
 */
function bp_lines( $text ) {
	$lines = preg_split( '/\r\n|\r|\n/', (string) $text );
	$lines = array_map( 'trim', (array) $lines );
	return array_values( array_filter( $lines, 'strlen' ) );
}

/**
 * Parse "Label: Value" lines into pairs.
 *
 * @param string $text Raw text.
 * @return array<int,array{label:string,value:string}>
 */
function bp_specs( $text ) {
	$out = array();
	foreach ( bp_lines( $text ) as $line ) {
		$parts = explode( ':', $line, 2 );
		if ( 2 === count( $parts ) ) {
			$out[] = array(
				'label' => trim( $parts[0] ),
				'value' => trim( $parts[1] ),
			);
		}
	}
	return $out;
}

/**
 * Gallery attachment IDs / URLs for a post (comma-separated IDs or line URLs).
 *
 * @param int    $post_id Post ID.
 * @param string $key     Meta key.
 * @return string[] Image URLs.
 */
function bp_gallery_urls( $post_id, $key = '_bp_gallery', $size = 'large' ) {
	$raw = bp_meta( $post_id, $key, '' );
	$out = array();
	foreach ( preg_split( '/[\r\n,]+/', (string) $raw ) as $item ) {
		$item = trim( $item );
		if ( '' === $item ) {
			continue;
		}
		$url = bp_image_url( $item, $size );
		if ( $url ) {
			$out[] = $url;
		}
	}
	return $out;
}

/**
 * Trimmed excerpt for cards.
 *
 * @param int $length Words.
 * @return string
 */
function bp_excerpt( $length = 24 ) {
	$text = has_excerpt() ? get_the_excerpt() : get_the_content();
	return wp_trim_words( wp_strip_all_tags( $text ), absint( $length ), '…' );
}

/**
 * First term name for a post.
 *
 * @param int    $post_id  Post ID.
 * @param string $taxonomy Taxonomy.
 * @return string
 */
function bp_first_term_name( $post_id, $taxonomy ) {
	$terms = get_the_terms( $post_id, $taxonomy );
	if ( $terms && ! is_wp_error( $terms ) ) {
		return $terms[0]->name;
	}
	return '';
}

/**
 * First term object for a post.
 *
 * @param int    $post_id  Post ID.
 * @param string $taxonomy Taxonomy.
 * @return WP_Term|null
 */
function bp_first_term( $post_id, $taxonomy ) {
	$terms = get_the_terms( $post_id, $taxonomy );
	if ( $terms && ! is_wp_error( $terms ) ) {
		return $terms[0];
	}
	return null;
}

/**
 * Detect whether a URL is an embeddable (YouTube/Vimeo) player.
 *
 * @param string $url Video URL.
 * @return bool
 */
function bp_is_embed_url( $url ) {
	return (bool) preg_match( '#(youtube\.com|youtu\.be|vimeo\.com)#i', (string) $url );
}

/**
 * Normalise a YouTube/Vimeo URL into an embed src.
 *
 * @param string $url URL.
 * @return string
 */
function bp_embed_src( $url ) {
	$url = trim( (string) $url );
	if ( preg_match( '#youtu\.be/([A-Za-z0-9_-]{6,})#', $url, $m ) ) {
		return 'https://www.youtube.com/embed/' . $m[1];
	}
	if ( preg_match( '#youtube\.com/watch\?.*v=([A-Za-z0-9_-]{6,})#', $url, $m ) ) {
		return 'https://www.youtube.com/embed/' . $m[1];
	}
	if ( preg_match( '#vimeo\.com/(?:video/)?(\d+)#', $url, $m ) ) {
		return 'https://player.vimeo.com/video/' . $m[1];
	}
	return $url;
}

/**
 * Output a responsive video player (MP4 or embed).
 *
 * @param string $url        Video URL.
 * @param string $poster     Poster image URL.
 * @param array  $args       autoplay|controls|class.
 */
function bp_video_player( $url, $poster = '', $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'autoplay' => false,
			'controls' => true,
			'class'    => 'bp-video-player',
			'title'    => '',
		)
	);
	if ( ! $url ) {
		return;
	}
	if ( bp_is_embed_url( $url ) ) {
		printf(
			'<iframe class="%1$s" src="%2$s" title="%3$s" loading="lazy" allow="accelerometer; autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>',
			esc_attr( $args['class'] ),
			esc_url( bp_embed_src( $url ) ),
			esc_attr( $args['title'] )
		);
		return;
	}
	$attrs = $args['autoplay'] ? ' autoplay muted loop playsinline' : ' controls playsinline';
	if ( $args['autoplay'] && $args['controls'] ) {
		$attrs .= ' controls';
	}
	printf(
		'<video class="%1$s"%2$s preload="metadata"%3$s aria-label="%4$s"><source src="%5$s" type="video/mp4" /></video>',
		esc_attr( $args['class'] ),
		$attrs, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attribute list.
		$poster ? ' poster="' . esc_url( $poster ) . '"' : '',
		esc_attr( $args['title'] ),
		esc_url( $url )
	);
}

/**
 * Whether Elementor (free) is active.
 *
 * @return bool
 */
function bp_has_elementor() {
	return did_action( 'elementor/loaded' ) > 0 || class_exists( '\Elementor\Plugin' );
}

/**
 * Whether Elementor Pro is active.
 *
 * @return bool
 */
function bp_has_elementor_pro() {
	return function_exists( 'elementor_pro_load_plugin' ) || class_exists( '\ElementorPro\Plugin' );
}

/**
 * Whether a post is built with Elementor.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function bp_is_elementor_post( $post_id ) {
	return 'builder' === get_post_meta( $post_id, '_elementor_edit_mode', true );
}

/**
 * Source-URL image fallback: when the importer could not store an image locally
 * it keeps the exact original URL in `_brickpoint_image_url`; these filters make
 * the normal thumbnail API use it transparently.
 */
function brickpoint_fallback_has_thumbnail( $has, $post, $thumbnail_id ) {
	if ( $has ) {
		return $has;
	}
	$post = get_post( $post );
	return $post && '' !== (string) get_post_meta( $post->ID, '_brickpoint_image_url', true );
}
add_filter( 'has_post_thumbnail', 'brickpoint_fallback_has_thumbnail', 10, 3 );

function brickpoint_fallback_thumbnail_html( $html, $post_id, $thumbnail_id, $size, $attr ) {
	if ( '' !== $html ) {
		return $html;
	}
	$url = (string) get_post_meta( $post_id, '_brickpoint_image_url', true );
	if ( '' === $url ) {
		return $html;
	}
	$attr  = wp_parse_args( $attr, array( 'class' => 'attachment-' . ( is_array( $size ) ? implode( 'x', $size ) : $size ) . ' wp-post-image', 'alt' => get_the_title( $post_id ) ) );
	$attrs = '';
	foreach ( $attr as $k => $v ) {
		$attrs .= ' ' . $k . '="' . esc_attr( $v ) . '"';
	}
	return '<img src="' . esc_url( $url ) . '"' . $attrs . ' />';
}
add_filter( 'post_thumbnail_html', 'brickpoint_fallback_thumbnail_html', 10, 5 );

function brickpoint_fallback_thumbnail_url( $url, $post, $size ) {
	if ( $url ) {
		return $url;
	}
	$post = get_post( $post );
	$fb   = $post ? (string) get_post_meta( $post->ID, '_brickpoint_image_url', true ) : '';
	return $fb ? $fb : $url;
}
add_filter( 'post_thumbnail_url', 'brickpoint_fallback_thumbnail_url', 10, 3 );

/**
 * Featured image URL with source-URL fallback (see brickpoint_fallback_* filters).
 *
 * @param int    $post_id Post ID.
 * @param string $size    Size.
 * @return string
 */
function bp_thumb_url( $post_id, $size = 'large' ) {
	$u = get_the_post_thumbnail_url( $post_id, $size );
	if ( $u ) {
		return $u;
	}
	return (string) get_post_meta( $post_id, '_brickpoint_image_url', true );
}
