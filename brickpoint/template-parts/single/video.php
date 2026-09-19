<?php
/**
 * Single video body. Shared by single template and the Elementor "Video Details" widget.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$bp_id   = get_the_ID();
$bp_term = bp_first_term( $bp_id, 'bp_video_category' );
$bp_url  = bp_meta( $bp_id, '_bpv_url' );
$bp_dur  = bp_meta( $bp_id, '_bpv_duration' );
$bp_bt   = bp_meta( $bp_id, '_bpv_button_text' );
$bp_bl   = bp_meta( $bp_id, '_bpv_button_link' );
$bp_feat = bp_meta( $bp_id, '_bpv_featured' );
$bp_rel  = bp_meta( $bp_id, '_bpv_related_products' );
$bp_poster = has_post_thumbnail() ? bp_thumb_url( $bp_id, 'bp-video' ) : '';
?>
<article id="video-<?php echo esc_attr( $bp_id ); ?>" <?php post_class( 'bp-single-video bp-dark-page' ); ?>>
	<?php bp_breadcrumbs( array( array( __( 'Home', 'brickpoint' ), home_url( '/' ) ), array( __( 'Videos', 'brickpoint' ), bp_archive_url( 'bp_video' ) ), array( get_the_title(), '' ) ), true ); ?>
	<section class="bp-section-sm">
		<div class="bp-container narrow-4">
			<div class="bp-video-frame"><?php bp_video_player( $bp_url, $bp_poster, array( 'title' => get_the_title() ) ); ?></div>
			<div class="bp-mt-sm">
				<?php if ( $bp_term ) : ?><p class="bp-eyebrow light"><?php echo esc_html( $bp_term->name ); ?></p><?php endif; ?>
				<h1 class="bp-h1 sm" style="color:#fff"><?php the_title(); ?></h1>
				<div class="bp-post-meta light">
					<?php if ( $bp_dur ) : ?><span><?php bp_the_icon( 'clock', 'bp-icon sm' ); ?><?php echo esc_html( $bp_dur ); ?></span><?php endif; ?>
					<span><?php bp_the_icon( 'calendar', 'bp-icon sm' ); ?><?php echo esc_html( get_the_date() ); ?></span>
					<?php if ( $bp_feat ) : ?><span class="bp-badge"><?php esc_html_e( 'Featured', 'brickpoint' ); ?></span><?php endif; ?>
				</div>
				<?php if ( get_the_content() || has_excerpt() ) : ?><div class="prose-bp light bp-mt-sm"><?php the_content(); ?></div><?php endif; ?>
				<div class="bp-btn-row mt-sm">
					<a class="bp-btn btn-ghost" href="<?php echo esc_url( bp_archive_url( 'bp_video' ) ); ?>"><?php esc_html_e( '← All Videos', 'brickpoint' ); ?></a>
					<a class="bp-btn btn-brick" href="<?php echo esc_url( $bp_bl ? bp_link( $bp_bl )['url'] : bp_archive_url( 'bp_product' ) ); ?>"><?php echo esc_html( $bp_bt ? $bp_bt : __( 'Related Products', 'brickpoint' ) ); ?></a>
				</div>
			</div>
		</div>
	</section>
	<?php
	bp_section_post_grid(
		array(
			'dark'        => 'yes',
			'plain'       => 'yes',
			'head_layout' => 'row',
			'title'       => __( 'More Videos', 'brickpoint' ),
			'count'       => 3,
			'columns'     => 3,
			'exclude'     => $bp_id,
			'button_text' => __( 'View all', 'brickpoint' ),
			'button_url'  => 'archive:bp_video',
		),
		'bp_video'
	);
	if ( $bp_rel ) {
		bp_section_product_grid( array( 'plain' => 'yes', 'dark' => 'yes', 'head_layout' => 'row', 'title' => __( 'Related Products', 'brickpoint' ), 'related' => $bp_rel, 'count' => 4, 'columns' => 4 ) );
	}
	?>
</article>
<?php
