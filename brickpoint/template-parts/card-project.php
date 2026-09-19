<?php
/**
 * Project card.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$bp_id     = get_the_ID();
$bp_loc    = bp_meta( $bp_id, '_bpp_location' );
$bp_cat    = bp_first_term_name( $bp_id, 'bp_project_category' );
$bp_status = bp_meta( $bp_id, '_bpp_status', __( 'Illustrative construction reference', 'brickpoint' ) );
$bp_full   = ! empty( $args['full'] );
$bp_reveal = isset( $args['reveal'] ) && $args['reveal'] ? ' bp-reveal delay-' . ( (int) ( isset( $args['index'] ) ? $args['index'] : 0 ) % 3 ) : '';
?>
<a class="bp-card bp-project-card card-hover<?php echo esc_attr( $bp_reveal ); ?>" href="<?php the_permalink(); ?>">
	<div class="bp-media r-16-10 img-zoom">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'bp-wide', array( 'loading' => 'lazy' ) ); ?>
		<?php endif; ?>
		<?php if ( $bp_status ) : ?><span class="bp-badge dark bp-abs-tl"><?php echo esc_html( $bp_status ); ?></span><?php endif; ?>
		<?php if ( $bp_full && $bp_cat ) : ?><span class="bp-badge bp-abs-br"><?php echo esc_html( $bp_cat ); ?></span><?php endif; ?>
	</div>
	<div class="bp-card-body">
		<?php if ( $bp_loc ) : ?><p class="bp-card-cat"><?php echo esc_html( $bp_loc ); ?></p><?php endif; ?>
		<h3 class="bp-card-title hover<?php echo $bp_full ? '' : ' md'; ?>"><?php the_title(); ?></h3>
		<?php if ( $bp_full && has_excerpt() ) : ?><p class="bp-card-text line-clamp-2"><?php echo esc_html( get_the_excerpt() ); ?></p><?php endif; ?>
		<?php if ( $bp_full ) : ?><span class="bp-link bp-mt-xs"><?php esc_html_e( 'Ask about materials used', 'brickpoint' ); ?> <?php bp_the_icon( 'arrow-right' ); ?></span><?php endif; ?>
	</div>
</a>
