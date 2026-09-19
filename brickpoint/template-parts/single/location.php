<?php
/**
 * Single location body. Shared by single template and the Elementor "Location Details" widget.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$bp_id    = get_the_ID();
$bp_addr  = bp_meta( $bp_id, '_bpl_address' );
$bp_maps  = bp_meta( $bp_id, '_bpl_maps' );
$bp_phone = bp_meta( $bp_id, '_bpl_phone', bp_phone_display() );
$bp_wa    = bp_meta( $bp_id, '_bpl_whatsapp' );
$bp_hours = bp_meta( $bp_id, '_bpl_hours' );
$bp_vid   = bp_meta( $bp_id, '_bpl_video' );
$bp_badge = bp_meta( $bp_id, '_bpl_badge' );
$bp_lat   = bp_meta( $bp_id, '_bpl_lat' );
$bp_lng   = bp_meta( $bp_id, '_bpl_lng' );
bp_breadcrumbs( array( array( __( 'Home', 'brickpoint' ), home_url( '/' ) ), array( __( 'Locations', 'brickpoint' ), bp_archive_url( 'bp_location' ) ), array( get_the_title(), '' ) ) );
?>
<article id="location-<?php echo esc_attr( $bp_id ); ?>" <?php post_class( 'bp-single-location' ); ?>>
	<section class="bp-section-sm">
		<div class="bp-container">
			<div class="bp-2col gap-lg align-start">
				<div>
					<div class="bp-media r-4-3 bp-img-main">
						<?php the_post_thumbnail( 'bp-wide' ); ?>
						<?php if ( $bp_badge ) : ?><span class="bp-badge-stack"><span class="bp-badge"><?php echo esc_html( $bp_badge ); ?></span></span><?php endif; ?>
					</div>
					<?php if ( $bp_vid ) : ?><div class="bp-video-frame bp-mt-sm"><?php bp_video_player( $bp_vid, bp_thumb_url( $bp_id, 'bp-video' ), array( 'title' => get_the_title() ) ); ?></div><?php endif; ?>
					<?php if ( $bp_lat && $bp_lng ) : ?>
						<div class="bp-map-card bp-mt-sm"><iframe title="<?php the_title_attribute(); ?>" loading="lazy" src="<?php echo esc_url( 'https://maps.google.com/maps?q=' . $bp_lat . ',' . $bp_lng . '&z=15&output=embed' ); ?>"></iframe></div>
					<?php endif; ?>
				</div>
				<div>
					<p class="bp-eyebrow"><?php esc_html_e( 'Location', 'brickpoint' ); ?></p>
					<h1 class="bp-h1 sm"><?php the_title(); ?></h1>
					<?php if ( $bp_addr ) : ?><p class="bp-lead bp-icon-line"><?php bp_the_icon( 'map-pin', 'bp-icon orange' ); ?><span><?php echo esc_html( $bp_addr ); ?></span></p><?php endif; ?>
					<div class="prose-bp bp-mt-sm"><?php the_content(); ?></div>
					<ul class="bp-contact-lines bp-mt-sm">
						<?php if ( $bp_phone ) : ?><li><?php bp_the_icon( 'phone' ); ?><a href="tel:<?php echo esc_attr( preg_replace( '/\D+/', '', $bp_phone ) ); ?>"><?php echo esc_html( $bp_phone ); ?></a></li><?php endif; ?>
						<?php if ( $bp_hours ) : ?><li><?php bp_the_icon( 'clock' ); ?><span><?php echo esc_html( $bp_hours ); ?></span></li><?php endif; ?>
					</ul>
					<div class="bp-btn-row mt-sm">
						<?php if ( $bp_maps ) : ?><a class="bp-btn btn-brick" target="_blank" rel="noopener" href="<?php echo esc_url( $bp_maps ); ?>"><?php bp_the_icon( 'navigation' ); ?><?php esc_html_e( 'Directions', 'brickpoint' ); ?></a><?php endif; ?>
						<?php echo bp_whatsapp_button( bp_whatsapp_url( sprintf( /* translators: %s: location */ __( "Assalam-o-Alaikum BrickPoint,\n\nI want to inquire about: %s\n\nThank you.", 'brickpoint' ), get_the_title() ), $bp_wa ), __( 'WhatsApp', 'brickpoint' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php if ( $bp_phone ) : ?><a class="bp-btn btn-outline" href="tel:<?php echo esc_attr( preg_replace( '/\D+/', '', $bp_phone ) ); ?>"><?php bp_the_icon( 'phone' ); ?><?php esc_html_e( 'Call', 'brickpoint' ); ?></a><?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php bp_section_post_grid( array( 'plain' => 'yes', 'head_layout' => 'row', 'title' => __( 'Other Locations', 'brickpoint' ), 'count' => 3, 'columns' => 3, 'exclude' => $bp_id, 'button_text' => __( 'View all', 'brickpoint' ), 'button_url' => 'archive:bp_location' ), 'bp_location' ); ?>
</article>
<?php
