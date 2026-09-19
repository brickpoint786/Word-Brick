<?php
/**
 * 404.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
?>
<section class="bp-404">
	<div class="bp-container narrow-3">
		<p class="bp-eyebrow"><?php esc_html_e( 'Page not found', 'brickpoint' ); ?></p>
		<h1 class="bp-h1">404</h1>
		<p class="bp-lead bp-mt-xs"><?php esc_html_e( 'The page you are looking for has moved or does not exist. Try a search or head back to the catalogue.', 'brickpoint' ); ?></p>
		<form class="bp-search-form bp-mt-sm" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<input type="search" name="s" placeholder="<?php esc_attr_e( 'Search…', 'brickpoint' ); ?>" aria-label="<?php esc_attr_e( 'Search', 'brickpoint' ); ?>" />
			<button class="bp-btn btn-brick" type="submit"><?php esc_html_e( 'Search', 'brickpoint' ); ?></button>
		</form>
		<div class="bp-btn-row center mt">
			<a class="bp-btn btn-dark" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to Home', 'brickpoint' ); ?></a>
			<a class="bp-btn btn-outline" href="<?php echo esc_url( bp_archive_url( 'bp_product' ) ); ?>"><?php esc_html_e( 'Browse Products', 'brickpoint' ); ?></a>
		</div>
	</div>
</section>
<?php
get_footer();
