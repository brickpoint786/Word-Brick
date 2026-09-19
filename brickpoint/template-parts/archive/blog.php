<?php
/**
 * Blog listing body. Shared by index/home/search and the Elementor "Blog Archive" widget.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bp_headers = bp_demo_archive_headers();
$bp_head    = $bp_headers['post'];
$bp_head['dynamic'] = 'yes';
if ( is_home() ) {
	$bp_head['title'] = get_option( 'page_for_posts' ) ? get_the_title( (int) get_option( 'page_for_posts' ) ) : $bp_head['title'];
}
bp_section_page_header( $bp_head );
?>
<section class="bp-section-xs">
	<div class="bp-container">
		<?php
		$bp_cats = get_categories( array( 'hide_empty' => true ) );
		$bp_cats = array_filter( $bp_cats, function ( $c ) { return 'uncategorized' !== $c->slug; } );
		if ( $bp_cats ) :
			?>
			<div class="bp-pills light" style="margin-top:0">
				<a class="bp-pill<?php echo is_category() ? '' : ' active'; ?>" href="<?php echo esc_url( bp_page_url( 'blog' ) ); ?>"><?php esc_html_e( 'All', 'brickpoint' ); ?></a>
				<?php foreach ( $bp_cats as $bp_c ) : ?>
					<a class="bp-pill<?php echo is_category( $bp_c->term_id ) ? ' active' : ''; ?>" href="<?php echo esc_url( get_category_link( $bp_c ) ); ?>"><?php echo esc_html( $bp_c->name ); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<?php if ( have_posts() ) : ?>
			<div class="bp-grid cols-3 gap-lg bp-mt-sm" style="margin-top:2rem">
				<?php $bp_i = 0; while ( have_posts() ) : the_post(); get_template_part( 'template-parts/card', 'blog', array( 'reveal' => true, 'index' => $bp_i++ ) ); endwhile; ?>
			</div>
			<div class="bp-pagination"><?php brickpoint_pagination(); ?></div>
		<?php else : ?>
			<div class="bp-empty"><?php esc_html_e( 'No articles found.', 'brickpoint' ); ?></div>
		<?php endif; ?>
	</div>
</section>
<?php
