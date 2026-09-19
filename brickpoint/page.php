<?php
/**
 * Page template.
 * - Elementor Pro "single" location wins if defined for pages.
 * - Elementor-built pages render their content.
 * - Known BrickPoint pages (by slug) that have no Elementor content render the
 *   original design sections from inc/content-defaults.php.
 * - Any other page renders classic content in the BrickPoint prose layout.
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
	$bp_slug  = get_post_field( 'post_name', get_the_ID() );
	$bp_pages = bp_demo_pages();
	if ( is_front_page() && isset( $bp_pages['home'] ) ) {
		$bp_slug = 'home';
	}
	if ( bp_is_elementor_post( get_the_ID() ) || '' !== trim( wp_strip_all_tags( get_the_content() ) ) ) {
		if ( bp_is_elementor_post( get_the_ID() ) ) {
			the_content();
		} else {
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'bp-page' ); ?>>
				<?php bp_page_header( '', get_the_title(), has_excerpt() ? get_the_excerpt() : '' ); ?>
				<div class="bp-container narrow-3 bp-page-content">
					<div class="bp-prose-card prose-bp" style="margin-top:0"><?php the_content(); ?></div>
					<?php wp_link_pages(); ?>
				</div>
				<?php if ( comments_open() || get_comments_number() ) { comments_template(); } ?>
			</article>
			<?php
		}
	} elseif ( isset( $bp_pages[ $bp_slug ] ) && ! empty( $bp_pages[ $bp_slug ]['sections'] ) ) {
		bp_render_sections( $bp_pages[ $bp_slug ]['sections'] );
	} else {
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'bp-page' ); ?>>
			<?php bp_page_header( '', get_the_title(), has_excerpt() ? get_the_excerpt() : '' ); ?>
			<div class="bp-container narrow-3 bp-page-content"><div class="bp-prose-card prose-bp" style="margin-top:0"><?php the_content(); ?></div></div>
		</article>
		<?php
	}
endwhile;

get_footer();
