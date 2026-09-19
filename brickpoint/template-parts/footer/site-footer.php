<?php
/**
 * PHP fallback footer — same markup used by the BrickPoint Footer Elementor widget.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$bp_wa = bp_default_whatsapp_url();
?>
<footer class="bp-footer">
	<div class="brick-lines"></div>
	<div class="bp-container bp-footer-grid">
		<div>
			<?php get_template_part( 'template-parts/header/logo', null, array( 'tagline' => false ) ); ?>
			<p class="bp-footer-about"><?php echo esc_html( bp_get( 'bp_footer_about', __( 'Premium bricks and reliable construction materials for homes, commercial developments, and large-scale building projects.', 'brickpoint' ) ) ); ?></p>
			<div class="bp-footer-contact">
				<p><?php bp_the_icon( 'phone' ); ?> <a href="tel:+<?php echo esc_attr( bp_phone_intl() ); ?>"><?php echo esc_html( bp_phone_display() ); ?></a></p>
				<p><?php bp_the_icon( 'mail' ); ?> <a href="mailto:<?php echo esc_attr( bp_email() ); ?>"><?php echo esc_html( bp_email() ); ?></a></p>
				<p><?php bp_the_icon( 'map-pin' ); ?> <?php echo esc_html( bp_address() ); ?></p>
			</div>
			<?php get_template_part( 'template-parts/footer/social-links' ); ?>
		</div>
		<div>
			<h4 class="bp-footer-h"><?php esc_html_e( 'Products', 'brickpoint' ); ?></h4>
			<ul class="bp-footer-list">
				<li><a href="<?php echo esc_url( bp_page_url( 'ss7-bricks' ) ); ?>"><?php esc_html_e( 'SS7 Bricks', 'brickpoint' ); ?></a></li>
				<li><a href="<?php echo esc_url( bp_archive_url( 'bp_product' ) ); ?>"><?php esc_html_e( 'All Products', 'brickpoint' ); ?></a></li>
				<li><a href="<?php echo esc_url( bp_page_url( 'categories' ) ); ?>"><?php esc_html_e( 'Product Categories', 'brickpoint' ); ?></a></li>
				<li><a href="<?php echo esc_url( bp_page_url( 'construction-materials' ) ); ?>"><?php esc_html_e( 'Construction Materials', 'brickpoint' ); ?></a></li>
				<li><a href="<?php echo esc_url( bp_archive_url( 'bp_video' ) ); ?>"><?php esc_html_e( 'Product Videos', 'brickpoint' ); ?></a></li>
			</ul>
			<h4 class="bp-footer-h"><?php esc_html_e( 'Company', 'brickpoint' ); ?></h4>
			<?php
			if ( has_nav_menu( 'footer' ) ) {
				wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'menu_class' => 'bp-footer-list', 'depth' => 1 ) );
			} else {
				?>
				<ul class="bp-footer-list">
					<li><a href="<?php echo esc_url( bp_page_url( 'about' ) ); ?>"><?php esc_html_e( 'About Us', 'brickpoint' ); ?></a></li>
					<li><a href="<?php echo esc_url( bp_archive_url( 'bp_project' ) ); ?>"><?php esc_html_e( 'Projects', 'brickpoint' ); ?></a></li>
					<li><a href="<?php echo esc_url( bp_page_url( 'blog' ) ); ?>"><?php esc_html_e( 'Blog', 'brickpoint' ); ?></a></li>
					<li><a href="<?php echo esc_url( bp_archive_url( 'bp_location' ) ); ?>"><?php esc_html_e( 'Locations', 'brickpoint' ); ?></a></li>
				</ul>
			<?php } ?>
		</div>
		<div>
			<h4 class="bp-footer-h"><?php esc_html_e( 'Who We Serve', 'brickpoint' ); ?></h4>
			<ul class="bp-footer-list">
				<li><a href="<?php echo esc_url( bp_page_url( 'for-contractors' ) ); ?>"><?php esc_html_e( 'For Contractors', 'brickpoint' ); ?></a></li>
				<li><a href="<?php echo esc_url( bp_page_url( 'for-builders' ) ); ?>"><?php esc_html_e( 'For Builders', 'brickpoint' ); ?></a></li>
				<li><a href="<?php echo esc_url( bp_page_url( 'for-companies' ) ); ?>"><?php esc_html_e( 'For Construction Companies', 'brickpoint' ); ?></a></li>
				<li><a href="<?php echo esc_url( bp_page_url( 'contact' ) ); ?>"><?php esc_html_e( 'Request Quotation', 'brickpoint' ); ?></a></li>
			</ul>
			<h4 class="bp-footer-h"><?php esc_html_e( 'Our Units', 'brickpoint' ); ?></h4>
			<ul class="bp-footer-list">
				<?php foreach ( array_map( 'trim', explode( ',', bp_get( 'bp_companies' ) ) ) as $bp_unit ) : ?>
					<li><?php echo esc_html( $bp_unit ); ?></li>
				<?php endforeach; ?>
				<li class="soft"><?php esc_html_e( 'CEO:', 'brickpoint' ); ?> <?php echo esc_html( bp_get( 'bp_ceo' ) ); ?></li>
			</ul>
		</div>
		<div>
			<h4 class="bp-footer-h"><?php esc_html_e( 'Get a Quotation', 'brickpoint' ); ?></h4>
			<p class="bp-footer-quote"><?php esc_html_e( 'Send your material list on WhatsApp and get availability, delivery details and final quotation.', 'brickpoint' ); ?></p>
			<div class="bp-btn-row stack">
				<a class="bp-btn btn-whatsapp full" target="_blank" rel="noopener" href="<?php echo esc_url( $bp_wa ); ?>"><?php bp_the_icon( 'message-circle', 'bp-icon md' ); ?> <?php esc_html_e( 'Chat on WhatsApp', 'brickpoint' ); ?></a>
				<a class="bp-btn btn-soft full" href="<?php echo esc_url( bp_page_url( 'contact' ) ); ?>"><?php esc_html_e( 'Contact Form', 'brickpoint' ); ?></a>
			</div>
		</div>
	</div>
	<div class="bp-footer-bottom">
		<div class="bp-container bp-footer-bottom-inner">
			<p><?php echo esc_html( bp_copyright() ); ?></p>
			<div class="bp-footer-legal">
				<a href="<?php echo esc_url( bp_page_url( 'privacy-policy' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'brickpoint' ); ?></a>
				<a href="<?php echo esc_url( bp_page_url( 'terms-and-conditions' ) ); ?>"><?php esc_html_e( 'Terms & Conditions', 'brickpoint' ); ?></a>
			</div>
		</div>
	</div>
</footer>
