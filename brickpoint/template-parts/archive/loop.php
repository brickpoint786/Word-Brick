<?php
/**
 * Archive body (header + filters + grid). Shared by archive templates and the Elementor "Archive Grid" widget.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bp_type = get_post_type() ? get_post_type() : ( isset( get_queried_object()->name ) ? get_queried_object()->name : 'post' );
if ( is_tax( 'bp_product_category' ) ) {
	$bp_type = 'bp_product';
} elseif ( is_tax( 'bp_video_category' ) ) {
	$bp_type = 'bp_video';
} elseif ( is_tax( 'bp_project_category' ) ) {
	$bp_type = 'bp_project';
}
$bp_headers = bp_demo_archive_headers();
$bp_head    = isset( $bp_headers[ $bp_type ] ) ? $bp_headers[ $bp_type ] : $bp_headers['post'];

if ( is_tax() || is_category() || is_tag() ) {
	$bp_term = get_queried_object();
	$bp_head = array(
		'dynamic'  => 'yes',
		'eyebrow'  => '',
		'buttons'  => 'bp_product_category' === $bp_term->taxonomy ? array(
			/* translators: %s: category name */
			array( 'text' => sprintf( __( 'Get %s Quote', 'brickpoint' ), $bp_term->name ), 'url' => bp_category_whatsapp_url( $bp_term ), 'style' => 'whatsapp' ),
			array( 'text' => __( 'All Products', 'brickpoint' ), 'url' => 'archive:bp_product', 'style' => 'ghost' ),
		) : array(),
	);
	if ( in_array( $bp_term->taxonomy, array( 'bp_video_category', 'bp_project_category' ), true ) ) {
		$bp_head['pills_taxonomy'] = $bp_term->taxonomy;
	}
}
bp_section_page_header( $bp_head );

// Category featured video.
if ( is_tax( 'bp_product_category' ) ) {
	$bp_cat_video = get_term_meta( get_queried_object_id(), 'bp_cat_video', true );
	if ( $bp_cat_video ) {
		echo '<section class="bp-section-xs bp-border-b" style="background:#000"><div class="bp-container narrow-3">';
		echo '<p style="display:flex;align-items:center;gap:.5rem;margin-bottom:.75rem;font-size:.875rem;font-weight:700;color:#fff">' . bp_icon( 'play', 'bp-icon orange' ) . ' ' . esc_html( sprintf( /* translators: %s: category */ __( 'Featured %s Video', 'brickpoint' ), get_queried_object()->name ) ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="bp-video-frame">';
		bp_video_player( $bp_cat_video, bp_term_image( get_queried_object_id(), 'bp_cat_image', 'bp-hero' ), array( 'title' => get_queried_object()->name ) );
		echo '</div></div></section>';
	}
}

$bp_cards = array( 'bp_product' => 'product', 'bp_video' => 'video', 'bp_project' => 'project', 'bp_location' => 'location', 'post' => 'blog' );
$bp_card  = isset( $bp_cards[ $bp_type ] ) ? $bp_cards[ $bp_type ] : 'blog';
$bp_cols  = 'bp_location' === $bp_type ? 2 : ( 'bp_product' === $bp_type ? 4 : 3 );
$bp_full  = in_array( $bp_type, array( 'bp_location', 'bp_project' ), true );
?>
<section class="bp-section-xs">
	<div class="bp-container">
		<?php if ( 'bp_project' === $bp_type && ! is_tax() ) : ?>
			<div class="bp-notice xs"><strong><?php esc_html_e( 'Content notice:', 'brickpoint' ); ?></strong> <?php esc_html_e( 'Project visuals on this page are labelled “Illustrative construction reference” or “Project inspiration visual”. BrickPoint does not claim supply to any named society, developer or project unless verified by management.', 'brickpoint' ); ?></div>
		<?php endif; ?>
		<?php if ( in_array( $bp_type, array( 'bp_product', 'bp_video' ), true ) ) : global $wp_query; ?>
			<p class="bp-count">
				<?php
				printf(
					/* translators: 1: count, 2: label */
					esc_html__( 'Showing %1$s %2$s', 'brickpoint' ),
					'<strong>' . (int) $wp_query->found_posts . '</strong>',
					'bp_product' === $bp_type ? esc_html__( 'products', 'brickpoint' ) : esc_html__( 'videos', 'brickpoint' )
				);
				if ( is_tax() ) {
					echo ' ' . esc_html__( 'in', 'brickpoint' ) . ' <strong>' . esc_html( get_queried_object()->name ) . '</strong>';
				}
				?>
			</p>
		<?php endif; ?>
		<?php if ( have_posts() ) : ?>
			<div class="bp-grid cols-<?php echo (int) $bp_cols; ?> gap-lg" style="margin-top:1.5rem">
				<?php $bp_i = 0; while ( have_posts() ) : the_post(); get_template_part( 'template-parts/card', $bp_card, array( 'reveal' => true, 'index' => $bp_i++, 'full' => $bp_full ) ); endwhile; ?>
			</div>
			<div class="bp-pagination"><?php brickpoint_pagination(); ?></div>
		<?php else : ?>
			<div class="bp-empty">
				<?php if ( is_tax( 'bp_product_category' ) ) : ?>
					<?php esc_html_e( 'Products in this category are being added.', 'brickpoint' ); ?> <a class="bp-link" target="_blank" rel="noopener" href="<?php echo esc_url( bp_category_whatsapp_url( get_queried_object() ) ); ?>"><?php esc_html_e( 'Ask on WhatsApp →', 'brickpoint' ); ?></a>
				<?php else : ?>
					<?php esc_html_e( 'No items found in this filter yet.', 'brickpoint' ); ?> <a class="bp-link" href="<?php echo esc_url( bp_archive_url( $bp_type ) ); ?>"><?php esc_html_e( 'Clear filters', 'brickpoint' ); ?></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php
