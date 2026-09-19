<?php
/**
 * Admin: Appearance → BrickPoint Demo page (one-click importer UI) and
 * theme-settings shortcuts.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Menu.
 */
function brickpoint_admin_menu() {
	add_theme_page(
		__( 'BrickPoint Demo', 'brickpoint' ),
		__( 'BrickPoint Demo', 'brickpoint' ),
		'manage_options',
		'brickpoint-demo',
		'brickpoint_demo_page'
	);
}
add_action( 'admin_menu', 'brickpoint_admin_menu' );

/**
 * Environment checks shown on the demo page.
 *
 * @return array<int,array{label:string,value:string,state:string}>
 */
function brickpoint_demo_requirements() {
	$pro = class_exists( '\ElementorPro\Plugin' );
	$el  = did_action( 'elementor/loaded' ) || class_exists( '\Elementor\Plugin' );
	$req = array(
		array(
			'label' => __( 'PHP', 'brickpoint' ),
			'value' => PHP_VERSION,
			'state' => version_compare( PHP_VERSION, '8.0', '>=' ) ? 'ok' : 'bad',
		),
		array(
			'label' => __( 'WordPress', 'brickpoint' ),
			'value' => get_bloginfo( 'version' ),
			'state' => version_compare( get_bloginfo( 'version' ), '6.0', '>=' ) ? 'ok' : 'warn',
		),
		array(
			'label' => __( 'Elementor', 'brickpoint' ),
			'value' => $el ? ( defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : __( 'Active', 'brickpoint' ) ) : __( 'Not active — pages fall back to theme templates', 'brickpoint' ),
			'state' => $el ? 'ok' : 'warn',
		),
		array(
			'label' => __( 'Elementor Pro', 'brickpoint' ),
			'value' => $pro ? ( defined( 'ELEMENTOR_PRO_VERSION' ) ? ELEMENTOR_PRO_VERSION : __( 'Active', 'brickpoint' ) ) : __( 'Not active — header/footer/archives use theme templates (Theme Builder JSON still imported)', 'brickpoint' ),
			'state' => $pro ? 'ok' : 'warn',
		),
		array(
			'label' => __( 'Remote downloads', 'brickpoint' ),
			'value' => function_exists( 'download_url' ) && ! ( defined( 'WP_HTTP_BLOCK_EXTERNAL' ) && WP_HTTP_BLOCK_EXTERNAL ) ? __( 'Allowed (images will be stored in Media Library)', 'brickpoint' ) : __( 'Blocked — original image URLs will be used', 'brickpoint' ),
			'state' => ( defined( 'WP_HTTP_BLOCK_EXTERNAL' ) && WP_HTTP_BLOCK_EXTERNAL ) ? 'warn' : 'ok',
		),
		array(
			'label' => __( 'Max execution time', 'brickpoint' ),
			'value' => (string) ini_get( 'max_execution_time' ) . 's',
			'state' => 'ok',
		),
	);
	return $req;
}

/**
 * Demo page.
 */
function brickpoint_demo_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$steps    = BrickPoint_Demo_Importer::steps();
	$done     = get_option( 'brickpoint_demo_imported' );
	$links    = BrickPoint_Demo_Importer::links();
	$req      = brickpoint_demo_requirements();
	$preview  = BRICKPOINT_URI . '/screenshot.png';
	?>
	<div class="wrap bp-demo-wrap">
		<div class="bp-demo-hero">
			<div>
				<span class="bp-demo-badge"><?php esc_html_e( 'BrickPoint • Elementor Pro Theme', 'brickpoint' ); ?></span>
				<h1><?php esc_html_e( 'One-Click Demo Import', 'brickpoint' ); ?></h1>
				<p><?php esc_html_e( 'Imports the complete BrickPoint website: products, categories, videos, projects, locations, blog posts, media, menus, pages, Elementor Pro header/footer/archive/single templates, theme options and WhatsApp settings.', 'brickpoint' ); ?></p>
				<p><?php esc_html_e( 'Safe to run again: existing demo items are updated, never duplicated.', 'brickpoint' ); ?></p>
				<div class="bp-demo-actions">
					<button id="bp-demo-import" class="button button-primary button-hero" data-retry="<?php esc_attr_e( 'Retry Import', 'brickpoint' ); ?>" data-again="<?php esc_attr_e( 'Run Import Again', 'brickpoint' ); ?>">
						<?php echo $done ? esc_html__( 'Re-import Demo', 'brickpoint' ) : esc_html__( 'Import Demo', 'brickpoint' ); ?>
					</button>
					<?php if ( $done ) : ?>
						<span style="color:#a7f3d0;font-size:13px;">
							<?php
							/* translators: %s: date */
							printf( esc_html__( 'Last import: %s', 'brickpoint' ), esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), (int) $done ) ) );
							?>
						</span>
					<?php endif; ?>
				</div>
			</div>
			<img src="<?php echo esc_url( $preview ); ?>" alt="<?php esc_attr_e( 'BrickPoint preview', 'brickpoint' ); ?>" />
		</div>

		<div class="bp-demo-card">
			<h2><?php esc_html_e( 'Environment', 'brickpoint' ); ?></h2>
			<div class="bp-demo-req">
				<?php foreach ( $req as $r ) : ?>
					<div><strong><?php echo esc_html( $r['label'] ); ?></strong><span class="<?php echo esc_attr( $r['state'] ); ?>"><?php echo esc_html( $r['value'] ); ?></span></div>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="bp-demo-card">
			<h2><?php esc_html_e( 'Import progress', 'brickpoint' ); ?></h2>
			<ol id="bp-demo-steps" class="bp-demo-steps">
				<?php foreach ( $steps as $id => $label ) : ?>
					<li data-step="<?php echo esc_attr( $id ); ?>"><span class="bp-step-icon">✓</span><span class="bp-step-label"><?php echo esc_html( $label ); ?></span><span class="bp-step-detail"></span></li>
				<?php endforeach; ?>
			</ol>
			<div id="bp-demo-progress" class="bp-demo-progress"><span></span></div>
			<pre id="bp-demo-log" class="bp-demo-log" aria-live="polite"></pre>
			<div id="bp-demo-error" class="bp-demo-error"></div>
		</div>

		<div id="bp-demo-success" class="bp-demo-success<?php echo $done ? ' show' : ''; ?>">
			<h2><?php esc_html_e( 'BrickPoint Demo Imported Successfully', 'brickpoint' ); ?></h2>
			<p><?php esc_html_e( 'Your website is ready. Edit any page with Elementor, or update contact details under Appearance → Customize → BrickPoint.', 'brickpoint' ); ?></p>
			<div class="bp-demo-success-links">
				<a class="button button-primary" data-link="view" href="<?php echo esc_url( $links['view'] ); ?>" target="_blank"><?php esc_html_e( 'View Website', 'brickpoint' ); ?></a>
				<a class="button" data-link="home" href="<?php echo esc_url( $links['home'] ); ?>"><?php esc_html_e( 'Edit Homepage', 'brickpoint' ); ?></a>
				<a class="button" data-link="header" href="<?php echo esc_url( $links['header'] ); ?>"><?php esc_html_e( 'Edit Header', 'brickpoint' ); ?></a>
				<a class="button" data-link="footer" href="<?php echo esc_url( $links['footer'] ); ?>"><?php esc_html_e( 'Edit Footer', 'brickpoint' ); ?></a>
				<a class="button" data-link="elementor" href="<?php echo esc_url( $links['elementor'] ); ?>"><?php esc_html_e( 'Open Elementor', 'brickpoint' ); ?></a>
				<a class="button" data-link="customize" href="<?php echo esc_url( $links['customize'] ); ?>"><?php esc_html_e( 'Theme Settings', 'brickpoint' ); ?></a>
			</div>
		</div>

		<div class="bp-demo-card">
			<h2><?php esc_html_e( 'What gets imported', 'brickpoint' ); ?></h2>
			<ul class="bp-demo-list">
				<li><?php esc_html_e( '15 product categories, 12 products (SKU, price, unit, specs, features, gallery, WhatsApp), 11 video categories, 6 videos, 6 project references, 4 locations, 4 blog articles.', 'brickpoint' ); ?></li>
				<li><?php esc_html_e( 'All images downloaded into the Media Library (video files stay on their original URLs, thumbnails stored locally).', 'brickpoint' ); ?></li>
				<li><?php esc_html_e( 'Pages: Home, About, Products, Categories, SS7 Bricks, Construction Materials, For Contractors, For Builders, For Construction Companies, Projects, Videos, Locations, Blog, Contact, Privacy Policy, Terms & Conditions.', 'brickpoint' ); ?></li>
				<li><?php esc_html_e( 'Elementor Pro Theme Builder: Header, Footer, Single Product/Video/Project/Location/Post, Archives, 404 — with display conditions; Homepage and all pages as Elementor documents; card & CTA templates in the Elementor library.', 'brickpoint' ); ?></li>
				<li><?php esc_html_e( 'Menus (Primary, Footer, Mobile), homepage & blog page, permalinks, WhatsApp/contact/social settings, Elementor kit colours & fonts.', 'brickpoint' ); ?></li>
			</ul>
		</div>
	</div>
	<?php
}

/**
 * Admin notice pointing to the importer until it has been run.
 */
function brickpoint_demo_notice() {
	$screen = get_current_screen();
	if ( ! current_user_can( 'manage_options' ) || get_option( 'brickpoint_demo_imported' ) || ( $screen && 'appearance_page_brickpoint-demo' === $screen->id ) ) {
		return;
	}
	if ( get_user_meta( get_current_user_id(), 'brickpoint_demo_notice_dismissed', true ) ) {
		return;
	}
	?>
	<div class="notice notice-info is-dismissible bp-demo-notice">
		<p>
			<strong><?php esc_html_e( 'BrickPoint:', 'brickpoint' ); ?></strong>
			<?php esc_html_e( 'Import the complete demo website (pages, products, Elementor templates, menus) in one click.', 'brickpoint' ); ?>
			<a class="button button-primary" style="margin-left:8px" href="<?php echo esc_url( admin_url( 'themes.php?page=brickpoint-demo' ) ); ?>"><?php esc_html_e( 'Import Demo', 'brickpoint' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'brickpoint_demo_notice' );

/**
 * Add "Demo Import" link in the admin bar for quick access.
 *
 * @param WP_Admin_Bar $bar Admin bar.
 */
function brickpoint_admin_bar( $bar ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$bar->add_node(
		array(
			'id'     => 'brickpoint-demo',
			'title'  => __( 'BrickPoint Demo', 'brickpoint' ),
			'href'   => admin_url( 'themes.php?page=brickpoint-demo' ),
			'parent' => 'appearance',
		)
	);
}
add_action( 'admin_bar_menu', 'brickpoint_admin_bar', 90 );
