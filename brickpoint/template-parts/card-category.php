<?php
/**
 * Product category card. $args['term'] (WP_Term), $args['style'] = dark|light.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$bp_term = isset( $args['term'] ) ? $args['term'] : null;
if ( ! $bp_term instanceof WP_Term ) {
	return;
}
$bp_light = isset( $args['style'] ) && 'light' === $args['style'];
$bp_img   = bp_term_image( $bp_term->term_id, 'bp_cat_image', 'bp-wide' );
$bp_icon  = get_term_meta( $bp_term->term_id, 'bp_cat_icon', true );
$bp_count = (int) $bp_term->count;
$bp_reveal = isset( $args['reveal'] ) && $args['reveal'] ? ' bp-reveal delay-' . ( (int) ( isset( $args['index'] ) ? $args['index'] : 0 ) % 4 ) : '';
?>
<?php if ( $bp_light ) : ?>
<a class="bp-card bp-cat-card-light card-hover<?php echo esc_attr( $bp_reveal ); ?>" href="<?php echo esc_url( bp_category_url( $bp_term ) ); ?>">
	<div class="bp-media r-16-10 img-zoom">
		<?php if ( $bp_img ) : ?><img src="<?php echo esc_url( $bp_img ); ?>" alt="<?php echo esc_attr( $bp_term->name ); ?>" loading="lazy" /><?php endif; ?>
		<div class="bp-shade"></div>
		<?php if ( $bp_icon ) : ?><span class="bp-abs-tl" style="display:grid;place-items:center;width:2.5rem;height:2.5rem;border-radius:.75rem;background:rgba(255,255,255,.9);color:#c2410c"><?php bp_the_icon( $bp_icon, 'bp-icon md' ); ?></span><?php endif; ?>
		<span class="bp-badge dark bp-abs-br"><?php echo esc_html( sprintf( _n( '%d product', '%d products', $bp_count, 'brickpoint' ), $bp_count ) ); ?></span>
	</div>
	<div class="bp-card-body">
		<h3 class="bp-card-title hover"><?php echo esc_html( $bp_term->name ); ?></h3>
		<?php if ( $bp_term->description ) : ?><p class="bp-card-text line-clamp-2"><?php echo esc_html( $bp_term->description ); ?></p><?php endif; ?>
		<span class="bp-link bp-mt-xs"><?php esc_html_e( 'Browse category', 'brickpoint' ); ?> <?php bp_the_icon( 'arrow-right' ); ?></span>
	</div>
</a>
<?php else : ?>
<a class="bp-card bp-cat-card dark card-hover<?php echo esc_attr( $bp_reveal ); ?>" href="<?php echo esc_url( bp_category_url( $bp_term ) ); ?>">
	<div class="bp-media r-16-10 img-zoom">
		<?php if ( $bp_img ) : ?><img src="<?php echo esc_url( $bp_img ); ?>" alt="<?php echo esc_attr( $bp_term->name ); ?>" loading="lazy" /><?php endif; ?>
		<div class="bp-shade"></div>
		<p class="bp-media-caption"><?php echo esc_html( $bp_term->name ); ?></p>
	</div>
	<div class="bp-card-row">
		<span class="bp-card-text xs line-clamp-2"><?php echo esc_html( $bp_term->description ); ?></span>
		<span class="bp-card-arrow"><?php bp_the_icon( 'arrow-right' ); ?></span>
	</div>
</a>
<?php endif; ?>
