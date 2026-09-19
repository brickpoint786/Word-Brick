<?php
/**
 * Template helper output functions (used by PHP fallback templates and widgets).
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dark page header (matches the original page hero band).
 *
 * @param string $eyebrow Eyebrow text.
 * @param string $title   Title.
 * @param string $text    Intro text.
 * @param string $image   Optional background image URL.
 * @param string $extra   Extra HTML (filters etc.).
 */
function bp_page_header( $eyebrow, $title, $text = '', $image = '', $extra = '' ) {
	echo '<section class="bp-pagehead' . ( $image ? ' has-image' : '' ) . '">';
	if ( $image ) {
		echo '<img class="bp-pagehead-bg" src="' . esc_url( $image ) . '" alt="" loading="eager" /><div class="bp-pagehead-shade"></div>';
	}
	echo '<div class="bp-container bp-rel">';
	if ( $eyebrow ) {
		echo '<p class="bp-eyebrow light">' . esc_html( $eyebrow ) . '</p>';
	}
	echo '<h1 class="bp-pagehead-title">' . wp_kses_post( $title ) . '</h1>';
	if ( $text ) {
		echo '<p class="bp-pagehead-text">' . wp_kses_post( $text ) . '</p>';
	}
	echo $extra; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- callers escape.
	echo '</div></section>';
}

/**
 * Section heading block.
 *
 * @param string $eyebrow Eyebrow.
 * @param string $title   Title.
 * @param string $text    Text.
 * @param bool   $light   Light (on dark).
 * @param string $align   center|left.
 */
function bp_section_head( $eyebrow, $title, $text = '', $light = false, $align = 'center' ) {
	echo '<div class="bp-section-head ' . esc_attr( $align ) . ( $light ? ' light' : '' ) . '">';
	if ( $eyebrow ) {
		echo '<p class="bp-eyebrow">' . esc_html( $eyebrow ) . '</p>';
	}
	echo '<h2 class="bp-h2">' . wp_kses_post( $title ) . '</h2>';
	if ( $text ) {
		echo '<p class="bp-lead">' . wp_kses_post( $text ) . '</p>';
	}
	echo '</div>';
}

/**
 * Breadcrumbs.
 *
 * @param array<int,array{0:string,1:string}> $items Label/URL pairs (last has empty URL).
 * @param bool                                 $dark  Dark variant.
 */
function bp_breadcrumbs( $items, $dark = false ) {
	echo '<nav class="bp-crumbs' . ( $dark ? ' dark' : '' ) . '" aria-label="' . esc_attr__( 'Breadcrumb', 'brickpoint' ) . '"><div class="bp-container">';
	$count = count( $items );
	foreach ( $items as $i => $item ) {
		if ( $i > 0 ) {
			bp_the_icon( 'chevron-right', 'bp-icon xs' );
		}
		if ( ! empty( $item[1] ) && $i < $count - 1 ) {
			echo '<a href="' . esc_url( $item[1] ) . '">' . esc_html( $item[0] ) . '</a>';
		} else {
			echo '<span class="current">' . esc_html( $item[0] ) . '</span>';
		}
	}
	echo '</div></nav>';
}

/**
 * Pagination.
 */
function brickpoint_pagination() {
	the_posts_pagination(
		array(
			'mid_size'  => 2,
			'prev_text' => __( '← Previous', 'brickpoint' ),
			'next_text' => __( 'Next →', 'brickpoint' ),
		)
	);
}

/**
 * Post meta line (author + date).
 */
function brickpoint_posted_meta() {
	echo '<div class="bp-post-meta">';
	echo '<span>' . bp_icon( 'user', 'bp-icon sm' ) . esc_html( get_the_author() ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo '<span>' . bp_icon( 'calendar', 'bp-icon sm' ) . esc_html( get_the_date() ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$tags = get_the_tags();
	if ( $tags && ! is_wp_error( $tags ) ) {
		echo '<span>' . bp_icon( 'tag', 'bp-icon sm' ) . esc_html( implode( ', ', wp_list_pluck( $tags, 'name' ) ) ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	echo '</div>';
}

/**
 * Related blog posts.
 *
 * @param int $count Count.
 */
function brickpoint_related_posts( $count = 3 ) {
	$q = new WP_Query(
		array(
			'posts_per_page'      => absint( $count ),
			'post__not_in'        => array( get_the_ID() ), // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
			'ignore_sticky_posts' => true,
		)
	);
	if ( $q->have_posts() ) {
		echo '<section class="bp-related bp-section-sm"><div class="bp-container narrow-5"><h2 class="bp-h2 sm">' . esc_html__( 'Related Articles', 'brickpoint' ) . '</h2><div class="bp-grid cols-3 bp-mt">';
		while ( $q->have_posts() ) {
			$q->the_post();
			get_template_part( 'template-parts/card', 'blog', array( 'compact' => true ) );
		}
		echo '</div></div></section>';
	}
	wp_reset_postdata();
}

/**
 * Filter pills for a taxonomy (used on archive headers).
 *
 * @param string $taxonomy Taxonomy.
 * @param string $base_url Archive URL.
 * @param string $current  Current term slug.
 * @return string
 */
function bp_filter_pills( $taxonomy, $base_url, $current = '' ) {
	$terms = 'bp_product_category' === $taxonomy ? bp_get_product_categories() : get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
	if ( is_wp_error( $terms ) ) {
		return '';
	}
	$html  = '<div class="bp-pills">';
	$html .= '<a class="bp-pill' . ( '' === $current ? ' active' : '' ) . '" href="' . esc_url( $base_url ) . '">' . esc_html__( 'All', 'brickpoint' ) . '</a>';
	$html .= '<a class="bp-pill' . ( 'featured' === $current ? ' active' : '' ) . '" href="' . esc_url( add_query_arg( 'featured', '1', $base_url ) ) . '">' . esc_html__( 'Featured', 'brickpoint' ) . '</a>';
	foreach ( $terms as $t ) {
		$link  = get_term_link( $t );
		$html .= '<a class="bp-pill' . ( $current === $t->slug ? ' active' : '' ) . '" href="' . esc_url( is_wp_error( $link ) ? $base_url : $link ) . '">' . esc_html( $t->name ) . '</a>';
	}
	return $html . '</div>';
}

/**
 * Featured filter support (?featured=1) on CPT archives.
 *
 * @param WP_Query $query Query.
 */
function brickpoint_featured_filter( $query ) {
	if ( is_admin() || ! $query->is_main_query() || empty( $_GET['featured'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	$map = array( 'bp_product' => '_bp_featured', 'bp_video' => '_bpv_featured', 'bp_project' => '_bpp_featured' );
	foreach ( $map as $type => $key ) {
		if ( $query->is_post_type_archive( $type ) ) {
			$query->set( 'meta_key', $key ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			$query->set( 'meta_value', '1' ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		}
	}
}
add_action( 'pre_get_posts', 'brickpoint_featured_filter' );

/**
 * Query products with optional filters.
 *
 * @param array $args Overrides.
 * @return WP_Query
 */
function bp_query_products( $args = array() ) {
	$defaults = array(
		'post_type'      => 'bp_product',
		'posts_per_page' => 8,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
	);
	$args = wp_parse_args( $args, $defaults );
	if ( ! empty( $args['featured'] ) ) {
		$args['meta_key']   = '_bp_featured'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		$args['meta_value'] = '1'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
	}
	if ( ! empty( $args['category'] ) ) {
		$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'bp_product_category',
				'field'    => 'slug',
				'terms'    => array_map( 'sanitize_title', (array) $args['category'] ),
			),
		);
	}
	unset( $args['featured'], $args['category'] );
	$q = new WP_Query( $args );
	if ( ! $q->have_posts() && isset( $args['meta_key'] ) ) {
		unset( $args['meta_key'], $args['meta_value'] );
		$q = new WP_Query( $args );
	}
	return $q;
}

/**
 * Floating WhatsApp button (site-wide).
 */
function brickpoint_floating_whatsapp() {
	if ( 'hide' === bp_get( 'bp_float_wa', 'show' ) ) {
		return;
	}
	echo '<a class="bp-float-wa" target="_blank" rel="noopener" aria-label="' . esc_attr__( 'Chat on WhatsApp', 'brickpoint' ) . '" href="' . esc_url( bp_default_whatsapp_url() ) . '">' . bp_icon( 'message-circle', 'bp-icon lg' ) . '<span class="bp-ping"></span></a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_footer', 'brickpoint_floating_whatsapp', 5 );

/**
 * Organization JSON-LD (from the original layout).
 */
function brickpoint_schema() {
	$data = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Organization',
		'name'        => 'BrickPoint',
		'description' => 'Construction-materials supplier providing bricks and building materials.',
		'telephone'   => '+' . bp_phone_intl(),
		'url'         => home_url( '/' ),
		'sameAs'      => array_values( array_filter( array( bp_social( 'facebook' ), bp_social( 'instagram' ), bp_social( 'twitter' ), bp_social( 'tiktok' ) ) ) ),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $data ) . '</script>' . "\n";
}
add_action( 'wp_head', 'brickpoint_schema' );

/**
 * Fallback menu (mirrors NAV_LINKS from the original site) when no menu is assigned.
 *
 * @param array $args wp_nav_menu args.
 */
function bp_fallback_menu( $args ) {
	$items = array(
		array( __( 'Home', 'brickpoint' ), home_url( '/' ) ),
		array(
			__( 'Products', 'brickpoint' ),
			bp_archive_url( 'bp_product' ),
			array(
				array( __( 'All Products', 'brickpoint' ), bp_archive_url( 'bp_product' ) ),
				array( __( 'Categories', 'brickpoint' ), bp_page_url( 'categories' ) ),
				array( __( 'SS7 Bricks', 'brickpoint' ), bp_page_url( 'ss7-bricks' ) ),
				array( __( 'Construction Materials', 'brickpoint' ), bp_page_url( 'construction-materials' ) ),
				array( __( 'For Contractors →', 'brickpoint' ), bp_page_url( 'for-contractors' ), 'bp-menu-secondary' ),
			),
		),
		array( __( 'SS7 Bricks', 'brickpoint' ), bp_page_url( 'ss7-bricks' ) ),
		array( __( 'Projects', 'brickpoint' ), bp_archive_url( 'bp_project' ) ),
		array( __( 'Videos', 'brickpoint' ), bp_archive_url( 'bp_video' ) ),
		array( __( 'Locations', 'brickpoint' ), bp_archive_url( 'bp_location' ) ),
		array( __( 'About', 'brickpoint' ), bp_page_url( 'about' ) ),
		array( __( 'Blog', 'brickpoint' ), bp_page_url( 'blog' ) ),
		array( __( 'Contact', 'brickpoint' ), bp_page_url( 'contact' ) ),
	);
	$class = isset( $args['menu_class'] ) ? $args['menu_class'] : 'bp-menu';
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $items as $item ) {
		$has = ! empty( $item[2] );
		echo '<li class="menu-item' . ( $has ? ' menu-item-has-children' : '' ) . '"><a href="' . esc_url( $item[1] ) . '">' . esc_html( $item[0] ) . '</a>';
		if ( $has ) {
			echo '<ul class="sub-menu">';
			foreach ( $item[2] as $child ) {
				echo '<li class="menu-item ' . esc_attr( isset( $child[2] ) ? $child[2] : '' ) . '"><a href="' . esc_url( $child[1] ) . '">' . esc_html( $child[0] ) . '</a></li>';
			}
			echo '</ul>';
		}
		echo '</li>';
	}
	echo '</ul>';
}

/**
 * Automatic breadcrumb trail for the current request.
 *
 * @return array<int,array{0:string,1:string}>
 */
function bp_auto_breadcrumbs() {
	$items = array( array( __( 'Home', 'brickpoint' ), home_url( '/' ) ) );
	$labels = array(
		'bp_product'  => array( __( 'Products', 'brickpoint' ), 'bp_product_category' ),
		'bp_video'    => array( __( 'Videos', 'brickpoint' ), 'bp_video_category' ),
		'bp_project'  => array( __( 'Projects', 'brickpoint' ), 'bp_project_category' ),
		'bp_location' => array( __( 'Locations', 'brickpoint' ), '' ),
	);
	if ( is_singular() ) {
		$type = get_post_type();
		if ( isset( $labels[ $type ] ) ) {
			$items[] = array( $labels[ $type ][0], bp_archive_url( $type ) );
			if ( $labels[ $type ][1] ) {
				$term = bp_first_term( get_the_ID(), $labels[ $type ][1] );
				if ( $term ) {
					$items[] = array( $term->name, 'bp_product_category' === $term->taxonomy ? bp_category_url( $term ) : get_term_link( $term ) );
				}
			}
		} elseif ( 'post' === $type ) {
			$items[] = array( __( 'Blog', 'brickpoint' ), bp_page_url( 'blog' ) );
		}
		$items[] = array( get_the_title(), '' );
	} elseif ( is_tax() || is_category() ) {
		$term = get_queried_object();
		foreach ( $labels as $type => $l ) {
			if ( $l[1] === $term->taxonomy ) {
				$items[] = array( $l[0], bp_archive_url( $type ) );
			}
		}
		$items[] = array( $term->name, '' );
	} elseif ( is_post_type_archive() ) {
		$type    = get_query_var( 'post_type' );
		$type    = is_array( $type ) ? reset( $type ) : $type;
		$items[] = array( isset( $labels[ $type ] ) ? $labels[ $type ][0] : post_type_archive_title( '', false ), '' );
	} elseif ( is_home() ) {
		$items[] = array( __( 'Blog', 'brickpoint' ), '' );
	} elseif ( is_search() ) {
		$items[] = array( __( 'Search', 'brickpoint' ), '' );
	}
	return $items;
}
