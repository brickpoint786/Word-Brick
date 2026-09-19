<?php
/**
 * Social icons.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$bp_socials = array(
	'facebook'  => array( __( 'Facebook', 'brickpoint' ), 'facebook' ),
	'instagram' => array( __( 'Instagram', 'brickpoint' ), 'instagram' ),
	'twitter'   => array( __( 'X Twitter', 'brickpoint' ), 'x-twitter' ),
	'tiktok'    => array( __( 'TikTok', 'brickpoint' ), 'tiktok' ),
);
?>
<div class="bp-social">
	<?php foreach ( $bp_socials as $bp_key => $bp_def ) : $bp_url = bp_social( $bp_key ); if ( ! $bp_url ) { continue; } ?>
		<a href="<?php echo esc_url( $bp_url ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $bp_def[0] ); ?>"><?php bp_the_icon( $bp_def[1] ); ?></a>
	<?php endforeach; ?>
</div>
