<?php
/**
 * Logo (custom logo or the BrickPoint brick mark).
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$bp_show_tag = ! isset( $args['tagline'] ) || $args['tagline'];
?>
<a class="bp-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'BrickPoint home', 'brickpoint' ); ?>">
	<?php if ( has_custom_logo() ) : ?>
		<?php echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'medium', false, array( 'class' => 'custom-logo', 'alt' => get_bloginfo( 'name' ) ) ); ?>
	<?php else : ?>
		<span class="bp-logo-mark"><?php bp_the_icon( 'brick-logo' ); ?></span>
		<span class="leading-none">
			<span class="bp-logo-text">Brick<span>Point</span></span>
			<?php if ( $bp_show_tag ) : ?><span class="bp-logo-tag"><?php echo esc_html( bp_get( 'bp_tagline' ) ); ?></span><?php endif; ?>
		</span>
	<?php endif; ?>
</a>
