<?php
/**
 * Video card.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$bp_id       = get_the_ID();
$bp_cat      = bp_first_term_name( $bp_id, 'bp_video_category' );
$bp_duration = bp_meta( $bp_id, '_bpv_duration' );
$bp_featured = '1' === bp_meta( $bp_id, '_bpv_featured' );
$bp_reveal   = isset( $args['reveal'] ) && $args['reveal'] ? ' bp-reveal delay-' . ( (int) ( isset( $args['index'] ) ? $args['index'] : 0 ) % 3 ) : '';
?>
<a class="bp-card bp-video-card card-hover<?php echo esc_attr( $bp_reveal ); ?>" href="<?php the_permalink(); ?>">
	<div class="bp-media r-16-9 dark img-zoom dim">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'bp-video', array( 'loading' => 'lazy' ) ); ?>
		<?php endif; ?>
		<div class="bp-shade"></div>
		<span class="bp-play"><?php bp_the_icon( 'play' ); ?></span>
		<?php if ( $bp_duration ) : ?><span class="bp-badge dark bp-abs-br"><?php echo esc_html( $bp_duration ); ?></span><?php endif; ?>
		<?php if ( $bp_featured ) : ?><span class="bp-badge amber bp-abs-tl"><?php bp_the_icon( 'badge-check', 'bp-icon xs' ); ?> <?php esc_html_e( 'Featured', 'brickpoint' ); ?></span><?php endif; ?>
	</div>
	<div class="bp-card-body">
		<?php if ( $bp_cat ) : ?><p class="bp-card-cat"><?php echo esc_html( $bp_cat ); ?></p><?php endif; ?>
		<h3 class="bp-card-title hover"><?php the_title(); ?></h3>
		<?php if ( has_excerpt() ) : ?><p class="bp-card-text line-clamp-2"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
	</div>
</a>
