<?php
/**
 * Footer.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</main>
<?php
if ( ! bp_elementor_footer() ) {
	get_template_part( 'template-parts/footer/site-footer' );
}
?>
</div><!-- .bp-site -->
<?php wp_footer(); ?>
</body>
</html>
