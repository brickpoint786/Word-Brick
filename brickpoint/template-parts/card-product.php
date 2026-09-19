<?php
/**
 * Product card (used by archives, widgets and the Elementor loop fallback).
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$bp_id    = get_the_ID();
$bp_cat   = bp_first_term_name( $bp_id, 'bp_product_category' );
$bp_price = bp_meta( $bp_id, '_bp_price' );
$bp_unit  = bp_meta( $bp_id, '_bp_unit' );
$bp_label = bp_meta( $bp_id, '_bp_price_label' );
$bp_badge = bp_meta( $bp_id, '_bp_badge' );
$bp_avail = bp_meta( $bp_id, '_bp_availability' );
$bp_short = bp_meta( $bp_id, '_bp_short' );
$bp_video = bp_meta( $bp_id, '_bp_video' );
$bp_reveal = isset( $args['reveal'] ) && $args['reveal'] ? ' bp-reveal delay-' . ( (int) ( isset( $args['index'] ) ? $args['index'] : 0 ) % 4 ) : '';
?>
<article class="bp-card bp-product-card card-hover<?php echo esc_attr( $bp_reveal ); ?>" id="product-<?php echo esc_attr( $bp_id ); ?>">
	<a class="bp-media r-4-3 img-zoom" href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'bp-card', array( 'loading' => 'lazy' ) ); ?>
		<?php endif; ?>
		<span class="bp-badge-stack">
			<?php if ( $bp_badge ) : ?><span class="bp-badge"><?php echo esc_html( $bp_badge ); ?></span><?php endif; ?>
			<?php if ( $bp_avail ) : ?><span class="bp-badge <?php echo 'In Stock' === $bp_avail ? 'emerald' : 'dark'; ?>"><?php echo esc_html( $bp_avail ); ?></span><?php endif; ?>
		</span>
		<?php if ( $bp_video ) : ?><span class="bp-play sm"><?php bp_the_icon( 'play' ); ?></span><?php endif; ?>
	</a>
	<div class="bp-card-body">
		<?php if ( $bp_cat ) : ?><p class="bp-card-cat"><?php echo esc_html( $bp_cat ); ?></p><?php endif; ?>
		<h3 class="bp-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php if ( $bp_short ) : ?><p class="bp-card-text line-clamp-2"><?php echo esc_html( $bp_short ); ?></p><?php endif; ?>
		<div class="bp-price-row">
			<?php if ( $bp_price ) : ?>
				<span class="bp-price"><?php echo esc_html( $bp_price ); ?></span>
				<?php if ( $bp_unit ) : ?><span class="bp-unit">/ <?php echo esc_html( $bp_unit ); ?></span><?php endif; ?>
			<?php else : ?>
				<span class="bp-price-req"><?php esc_html_e( 'Price on request', 'brickpoint' ); ?></span>
			<?php endif; ?>
		</div>
		<?php if ( $bp_label ) : ?><p class="bp-price-label"><?php echo esc_html( $bp_label ); ?></p><?php endif; ?>
		<div class="bp-btn-2">
			<a class="bp-btn btn-outline sm" href="<?php the_permalink(); ?>"><?php esc_html_e( 'View Product', 'brickpoint' ); ?></a>
			<?php echo bp_whatsapp_button( bp_product_whatsapp_url( $bp_id ), __( 'WhatsApp', 'brickpoint' ), 'sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</div>
</article>
