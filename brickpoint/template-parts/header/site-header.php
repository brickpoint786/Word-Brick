<?php
/**
 * PHP fallback header — same markup used by the BrickPoint Header Elementor widget.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$bp_wa = bp_default_whatsapp_url();
?>
<div class="bp-topbar">
	<div class="bp-container bp-topbar-inner">
		<p class="bp-topbar-left">
			<span><?php bp_the_icon( 'phone' ); ?> <?php echo esc_html( bp_phone_display() ); ?></span>
			<span class="bp-topbar-units"><?php echo esc_html( str_replace( array( 'Company,', 'Company' ), array( 'Co. •', 'Co.' ), bp_get( 'bp_companies' ) ) ); ?></span>
		</p>
		<div class="bp-topbar-right">
			<a href="<?php echo esc_url( bp_archive_url( 'bp_location' ) ); ?>"><?php esc_html_e( 'Our Bhattas', 'brickpoint' ); ?></a>
			<a href="<?php echo esc_url( bp_archive_url( 'bp_video' ) ); ?>"><?php esc_html_e( 'Videos', 'brickpoint' ); ?></a>
			<a class="bp-topbar-chip" href="<?php echo esc_url( bp_page_url( 'contact' ) ); ?>"><?php esc_html_e( 'Get Quote', 'brickpoint' ); ?></a>
		</div>
	</div>
</div>
<header class="bp-header" id="bpHeader">
	<div class="bp-container bp-header-inner">
		<?php get_template_part( 'template-parts/header/logo' ); ?>
		<nav class="bp-nav" aria-label="<?php esc_attr_e( 'Main navigation', 'brickpoint' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'bp-menu',
					'fallback_cb'    => 'bp_fallback_menu',
					'depth'          => 2,
				)
			);
			?>
		</nav>
		<div class="bp-header-cta">
			<a class="bp-btn btn-whatsapp" target="_blank" rel="noopener" href="<?php echo esc_url( $bp_wa ); ?>"><?php bp_the_icon( 'message-circle' ); ?> <?php esc_html_e( 'WhatsApp Us', 'brickpoint' ); ?></a>
			<a class="bp-btn btn-brick" href="<?php echo esc_url( bp_page_url( 'contact' ) ); ?>"><?php esc_html_e( 'Request Quote', 'brickpoint' ); ?></a>
		</div>
		<button class="bp-menu-toggle" data-bp-drawer-open="bpDrawer" aria-label="<?php esc_attr_e( 'Open menu', 'brickpoint' ); ?>" aria-controls="bpDrawer" aria-expanded="false"><?php bp_the_icon( 'menu' ); ?></button>
	</div>
</header>
<div class="bp-drawer" id="bpDrawer" aria-hidden="true">
	<div class="bp-drawer-backdrop" data-bp-drawer-close></div>
	<div class="bp-drawer-panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Mobile navigation', 'brickpoint' ); ?>">
		<div class="bp-drawer-head">
			<?php get_template_part( 'template-parts/header/logo' ); ?>
			<button class="bp-drawer-close" data-bp-drawer-close aria-label="<?php esc_attr_e( 'Close menu', 'brickpoint' ); ?>"><?php bp_the_icon( 'x' ); ?></button>
		</div>
		<nav class="bp-drawer-nav" aria-label="<?php esc_attr_e( 'Mobile navigation', 'brickpoint' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => has_nav_menu( 'mobile' ) ? 'mobile' : 'primary',
					'container'      => false,
					'menu_class'     => 'bp-menu-mobile',
					'fallback_cb'    => 'bp_fallback_menu',
					'depth'          => 2,
				)
			);
			?>
			<div class="bp-drawer-quick">
				<a href="<?php echo esc_url( bp_page_url( 'for-contractors' ) ); ?>"><?php esc_html_e( 'For Contractors', 'brickpoint' ); ?></a>
				<a href="<?php echo esc_url( bp_page_url( 'for-builders' ) ); ?>"><?php esc_html_e( 'For Builders', 'brickpoint' ); ?></a>
				<a href="<?php echo esc_url( bp_page_url( 'for-companies' ) ); ?>"><?php esc_html_e( 'For Companies', 'brickpoint' ); ?></a>
				<a href="<?php echo esc_url( bp_page_url( 'blog' ) ); ?>"><?php esc_html_e( 'Blog', 'brickpoint' ); ?></a>
			</div>
		</nav>
		<div class="bp-drawer-cta">
			<a class="bp-btn btn-whatsapp" target="_blank" rel="noopener" href="<?php echo esc_url( $bp_wa ); ?>"><?php bp_the_icon( 'message-circle' ); ?> <?php esc_html_e( 'WhatsApp:', 'brickpoint' ); ?> <?php echo esc_html( bp_phone_display() ); ?></a>
			<a class="bp-btn btn-brick" href="<?php echo esc_url( bp_page_url( 'contact' ) ); ?>"><?php esc_html_e( 'Request a Quote', 'brickpoint' ); ?></a>
		</div>
	</div>
</div>
