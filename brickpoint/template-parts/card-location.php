<?php
/**
 * Location card (Our Bhattas).
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$bp_id      = get_the_ID();
$bp_address = bp_meta( $bp_id, '_bpl_address' );
$bp_maps    = bp_meta( $bp_id, '_bpl_maps' );
$bp_phone   = bp_meta( $bp_id, '_bpl_phone', bp_phone_display() );
$bp_hours   = bp_meta( $bp_id, '_bpl_hours' );
$bp_video   = bp_meta( $bp_id, '_bpl_video' );
$bp_badge   = bp_meta( $bp_id, '_bpl_badge' );
$bp_wa_num  = bp_meta( $bp_id, '_bpl_whatsapp', '' );
$bp_full    = ! empty( $args['full'] );
$bp_reveal  = isset( $args['reveal'] ) && $args['reveal'] ? ' bp-reveal' : '';
/* translators: %s: location name */
$bp_wa_msg  = sprintf( __( "Assalam-o-Alaikum BrickPoint,\n\nI want to visit / order from: %s\n\nPlease share directions, availability and prices.\n\nThank you.", 'brickpoint' ), get_the_title() );
?>
<article class="bp-card bp-loc-card card-hover<?php echo $bp_full ? ' r-3xl' : ''; ?><?php echo esc_attr( $bp_reveal ); ?>">
	<div class="bp-media <?php echo $bp_full ? 'r-16-8' : 'r-16-9'; ?> img-zoom">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'bp-wide', array( 'loading' => 'lazy' ) ); ?>
		<?php else : ?>
			<span class="bp-media-caption"><?php bp_the_icon( 'map-pin', 'bp-icon xl' ); ?></span>
		<?php endif; ?>
		<?php if ( $bp_full ) : ?><div class="bp-shade"></div><?php endif; ?>
		<?php if ( $bp_badge ) : ?><span class="bp-badge bp-abs-tl lg"><?php echo esc_html( $bp_badge ); ?></span><?php endif; ?>
		<?php if ( $bp_full ) : ?><h3 class="bp-media-caption bp-h2 sm" style="font-size:1.5rem"><?php the_title(); ?></h3><?php endif; ?>
	</div>
	<div class="bp-card-body">
		<?php if ( ! $bp_full ) : ?><h3 class="bp-card-title"><?php the_title(); ?></h3><?php endif; ?>
		<?php if ( $bp_address ) : ?><p class="bp-loc-line"><?php bp_the_icon( 'map-pin' ); ?> <span><?php echo esc_html( $bp_address ); ?></span></p><?php endif; ?>
		<?php if ( has_excerpt() || get_the_content() ) : ?><p class="bp-loc-desc <?php echo $bp_full ? '' : 'line-clamp-3'; ?>"><?php echo esc_html( has_excerpt() ? get_the_excerpt() : wp_strip_all_tags( get_the_content() ) ); ?></p><?php endif; ?>
		<?php if ( $bp_full ) : ?>
			<div class="bp-loc-meta">
				<?php if ( $bp_phone ) : ?><span><?php bp_the_icon( 'phone' ); ?> <?php echo esc_html( $bp_phone ); ?></span><?php endif; ?>
				<?php if ( $bp_hours ) : ?><span><?php bp_the_icon( 'clock' ); ?> <?php echo esc_html( $bp_hours ); ?></span><?php endif; ?>
			</div>
			<?php if ( $bp_video ) { bp_video_player( $bp_video, bp_thumb_url( $bp_id, 'bp-video' ), array( 'class' => 'bp-video-player', 'title' => get_the_title() ) ); } ?>
			<div class="bp-btn-3">
				<?php if ( $bp_maps ) : ?><a class="bp-btn btn-dark" target="_blank" rel="noopener" href="<?php echo esc_url( $bp_maps ); ?>"><?php bp_the_icon( 'navigation' ); ?> <?php esc_html_e( 'Google Maps', 'brickpoint' ); ?></a><?php endif; ?>
				<?php echo bp_whatsapp_button( bp_whatsapp_url( $bp_wa_msg, $bp_wa_num ), __( 'WhatsApp', 'brickpoint' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<a class="bp-btn btn-outline" href="<?php echo esc_url( bp_page_url( 'contact' ) ); ?>"><?php esc_html_e( 'Contact', 'brickpoint' ); ?></a>
			</div>
		<?php else : ?>
			<div class="bp-btn-2">
				<?php if ( $bp_maps ) : ?><a class="bp-btn btn-dark sm" target="_blank" rel="noopener" href="<?php echo esc_url( $bp_maps ); ?>"><?php esc_html_e( 'Google Maps', 'brickpoint' ); ?> <?php bp_the_icon( 'arrow-right', 'bp-icon sm' ); ?></a><?php endif; ?>
				<a class="bp-btn btn-outline sm" href="<?php echo esc_url( bp_page_url( 'contact' ) ); ?>"><?php esc_html_e( 'Contact', 'brickpoint' ); ?></a>
			</div>
		<?php endif; ?>
	</div>
</article>
