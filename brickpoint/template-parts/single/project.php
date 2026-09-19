<?php
/**
 * Single project body. Shared by single template and the Elementor "Project Details" widget.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$bp_id   = get_the_ID();
$bp_term = bp_first_term( $bp_id, 'bp_project_category' );
$bp_loc  = bp_meta( $bp_id, '_bpp_location' );
$bp_st   = bp_meta( $bp_id, '_bpp_status' );
$bp_vid  = bp_meta( $bp_id, '_bpp_video' );
$bp_gal  = bp_gallery_urls( $bp_id, '_bpp_gallery', 'bp-wide' );
$bp_link = bp_meta( $bp_id, '_bpp_link' );
$bp_ct   = bp_meta( $bp_id, '_bpp_cta_text' );
$bp_cl   = bp_meta( $bp_id, '_bpp_cta_link' );
bp_breadcrumbs( array( array( __( 'Home', 'brickpoint' ), home_url( '/' ) ), array( __( 'Projects', 'brickpoint' ), bp_archive_url( 'bp_project' ) ), array( get_the_title(), '' ) ) );
?>
<article id="project-<?php echo esc_attr( $bp_id ); ?>" <?php post_class( 'bp-single-project' ); ?>>
	<section class="bp-section-sm">
		<div class="bp-container narrow-4">
			<div class="bp-media r-16-9 bp-img-main">
				<?php the_post_thumbnail( 'bp-hero' ); ?>
				<?php if ( $bp_st ) : ?><span class="bp-badge-stack"><span class="bp-badge dark"><?php echo esc_html( $bp_st ); ?></span></span><?php endif; ?>
			</div>
			<div class="bp-mt-sm">
				<p class="bp-eyebrow"><?php echo esc_html( trim( $bp_loc . ( $bp_term ? ' • ' . $bp_term->name : '' ), ' •' ) ); ?></p>
				<h1 class="bp-h1 sm"><?php the_title(); ?></h1>
				<?php if ( bp_meta( $bp_id, '_bpp_illustrative', '1' ) ) : ?>
					<div class="bp-notice xs bp-mt-sm"><strong><?php esc_html_e( 'Content notice:', 'brickpoint' ); ?></strong> <?php esc_html_e( 'This is an illustrative construction reference / project inspiration visual. BrickPoint does not claim supply to this project unless verified by management.', 'brickpoint' ); ?></div>
				<?php endif; ?>
				<div class="prose-bp bp-mt-sm"><?php the_content(); ?></div>
				<?php if ( $bp_vid ) : ?><div class="bp-video-frame bp-mt-sm"><?php bp_video_player( $bp_vid, bp_thumb_url( $bp_id, 'bp-video' ), array( 'title' => get_the_title() ) ); ?></div><?php endif; ?>
				<?php if ( $bp_gal ) : ?>
					<div class="bp-grid cols-3 gap-sm bp-mt-sm">
						<?php foreach ( $bp_gal as $bp_g ) : ?><div class="bp-media r-4-3"><img src="<?php echo esc_url( $bp_g ); ?>" alt="" loading="lazy" /></div><?php endforeach; ?>
					</div>
				<?php endif; ?>
				<div class="bp-btn-row mt-sm">
					<?php echo bp_whatsapp_button( bp_whatsapp_url( sprintf( /* translators: %s: project */ __( "Assalam-o-Alaikum BrickPoint,\n\nI want to build like this project: %s\n\nPlease share a materials quotation.\n\nThank you.", 'brickpoint' ), get_the_title() ) ), $bp_ct ? $bp_ct : __( 'Build Like This — Get Quote', 'brickpoint' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<a class="bp-btn btn-outline" href="<?php echo esc_url( $bp_cl ? bp_link( $bp_cl )['url'] : bp_archive_url( 'bp_product' ) ); ?>"><?php esc_html_e( 'Materials', 'brickpoint' ); ?></a>
					<?php if ( $bp_link ) : ?><a class="bp-btn btn-ghost-dark" href="<?php echo esc_url( $bp_link ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'External Link', 'brickpoint' ); ?></a><?php endif; ?>
					<a class="bp-btn btn-ghost-dark" href="<?php echo esc_url( bp_archive_url( 'bp_project' ) ); ?>"><?php esc_html_e( '← All Projects', 'brickpoint' ); ?></a>
				</div>
			</div>
		</div>
	</section>
	<?php bp_section_post_grid( array( 'plain' => 'yes', 'head_layout' => 'row', 'title' => __( 'More References', 'brickpoint' ), 'count' => 3, 'columns' => 3, 'exclude' => $bp_id, 'button_text' => __( 'View all', 'brickpoint' ), 'button_url' => 'archive:bp_project' ), 'bp_project' ); ?>
</article>
<?php
