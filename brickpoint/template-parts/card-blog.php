<?php
/**
 * Blog post card.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$bp_compact = ! empty( $args['compact'] );
$bp_cat     = bp_first_term_name( get_the_ID(), 'category' );
$bp_reveal  = isset( $args['reveal'] ) && $args['reveal'] ? ' bp-reveal delay-' . ( (int) ( isset( $args['index'] ) ? $args['index'] : 0 ) % 3 ) : '';
?>
<article class="bp-card bp-blog-card card-hover<?php echo $bp_compact ? ' compact' : ''; ?><?php echo esc_attr( $bp_reveal ); ?>">
	<a class="bp-media r-16-9 img-zoom" href="<?php the_permalink(); ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail( 'bp-wide', array( 'loading' => 'lazy' ) ); ?>
		<?php endif; ?>
	</a>
	<div class="bp-card-body">
		<?php if ( $bp_cat && 'Uncategorized' !== $bp_cat ) : ?><p class="bp-card-cat"><?php echo esc_html( $bp_cat ); ?></p><?php endif; ?>
		<h3 class="bp-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php if ( ! $bp_compact ) : ?>
			<p class="bp-card-text line-clamp-2"><?php echo esc_html( bp_excerpt( 24 ) ); ?></p>
			<div class="bp-card-meta">
				<span><?php bp_the_icon( 'calendar', 'bp-icon sm' ); ?> <?php echo esc_html( get_the_date() ); ?></span>
				<span><?php bp_the_icon( 'user', 'bp-icon sm' ); ?> <?php the_author(); ?></span>
			</div>
		<?php else : ?>
			<p class="bp-card-text xs"><?php echo esc_html( get_the_date() ); ?></p>
		<?php endif; ?>
	</div>
</article>
