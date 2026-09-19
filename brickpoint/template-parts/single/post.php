<?php
/**
 * Single post body. Shared by single template and the Elementor "Post Details" widget.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$bp_id  = get_the_ID();
$bp_cat = get_the_category();
$bp_cat = $bp_cat ? $bp_cat[0] : null;
bp_breadcrumbs( array( array( __( 'Home', 'brickpoint' ), home_url( '/' ) ), array( __( 'Blog', 'brickpoint' ), bp_page_url( 'blog' ) ), array( get_the_title(), '' ) ) );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'bp-single-post' ); ?>>
	<section class="bp-section-sm">
		<div class="bp-container narrow-3">
			<?php if ( $bp_cat && 'uncategorized' !== $bp_cat->slug ) : ?><a class="bp-badge" href="<?php echo esc_url( get_category_link( $bp_cat ) ); ?>"><?php echo esc_html( $bp_cat->name ); ?></a><?php endif; ?>
			<h1 class="bp-h1 sm bp-mt-xs"><?php the_title(); ?></h1>
			<?php brickpoint_posted_meta(); ?>
			<?php if ( has_post_thumbnail() ) : ?><div class="bp-media r-16-9 bp-img-main bp-mt-sm"><?php the_post_thumbnail( 'bp-hero' ); ?></div><?php endif; ?>
			<div class="bp-prose-card prose-bp lg">
				<?php if ( has_excerpt() ) : ?><p class="lead"><strong><?php echo esc_html( get_the_excerpt() ); ?></strong></p><?php endif; ?>
				<?php the_content(); ?>
				<?php wp_link_pages(); ?>
			</div>
			<div class="bp-btn-row mt-sm">
				<a class="bp-btn btn-outline" href="<?php echo esc_url( bp_page_url( 'blog' ) ); ?>"><?php esc_html_e( '← All Articles', 'brickpoint' ); ?></a>
				<a class="bp-btn btn-brick" href="<?php echo esc_url( bp_archive_url( 'bp_product' ) ); ?>"><?php esc_html_e( 'Shop Materials', 'brickpoint' ); ?></a>
				<?php echo bp_whatsapp_button( bp_whatsapp_url( sprintf( /* translators: %s: article */ __( "Assalam-o-Alaikum BrickPoint,\n\nI read your article \"%s\" and have a question.\n\nThank you.", 'brickpoint' ), get_the_title() ) ), __( 'Ask on WhatsApp', 'brickpoint' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<?php if ( comments_open() || get_comments_number() ) { comments_template(); } ?>
		</div>
	</section>
	<?php bp_section_post_grid( array( 'plain' => 'yes', 'head_layout' => 'row', 'title' => __( 'Related Articles', 'brickpoint' ), 'count' => 3, 'columns' => 3, 'exclude' => $bp_id, 'button_text' => __( 'View all', 'brickpoint' ), 'button_url' => 'page:blog' ), 'post' ); ?>
</article>
<?php
