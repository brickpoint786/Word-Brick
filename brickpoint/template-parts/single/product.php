<?php
/**
 * Single product body. Shared by single template and the Elementor "Product Details" widget.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$bp_id      = get_the_ID();
$bp_term    = bp_first_term( $bp_id, 'bp_product_category' );
$bp_gallery = bp_gallery_urls( $bp_id, '_bp_gallery', 'bp-wide' );
$bp_main    = has_post_thumbnail() ? bp_thumb_url( $bp_id, 'bp-wide' ) : ( $bp_gallery ? $bp_gallery[0] : '' );
$bp_price   = bp_meta( $bp_id, '_bp_price' );
$bp_unit    = bp_meta( $bp_id, '_bp_unit' );
$bp_label   = bp_meta( $bp_id, '_bp_price_label' );
$bp_badge   = bp_meta( $bp_id, '_bp_badge' );
$bp_avail   = bp_meta( $bp_id, '_bp_availability' );
$bp_sku     = bp_meta( $bp_id, '_bp_sku' );
$bp_short   = bp_meta( $bp_id, '_bp_short' );
$bp_video   = bp_meta( $bp_id, '_bp_video' );
$bp_specs   = bp_specs( bp_meta( $bp_id, '_bp_specs' ) );
$bp_feats   = bp_lines( bp_meta( $bp_id, '_bp_features' ) );
$bp_broch   = bp_meta( $bp_id, '_bp_brochure' );
$bp_cta_t   = bp_meta( $bp_id, '_bp_cta_text' );
$bp_cta_l   = bp_meta( $bp_id, '_bp_cta_link' );
$bp_crumbs  = array( array( __( 'Home', 'brickpoint' ), home_url( '/' ) ), array( __( 'Products', 'brickpoint' ), bp_archive_url( 'bp_product' ) ) );
if ( $bp_term ) {
	$bp_crumbs[] = array( $bp_term->name, bp_category_url( $bp_term ) );
}
$bp_crumbs[] = array( get_the_title(), '' );
bp_breadcrumbs( $bp_crumbs );
?>
<article id="product-<?php echo esc_attr( $bp_id ); ?>" <?php post_class( 'bp-single-product' ); ?>>
	<section class="bp-section-sm">
		<div class="bp-container">
			<div class="bp-2col gap-lg align-start">
				<div class="bp-product-gallery">
					<div class="bp-media r-4-3 bp-img-main">
						<?php if ( $bp_main ) : ?><img id="bp-main-image" src="<?php echo esc_url( $bp_main ); ?>" alt="<?php the_title_attribute(); ?>" /><?php endif; ?>
						<?php if ( $bp_badge ) : ?><span class="bp-badge-stack"><span class="bp-badge"><?php echo esc_html( $bp_badge ); ?></span></span><?php endif; ?>
					</div>
					<?php if ( count( $bp_gallery ) > 0 ) : ?>
						<div class="bp-thumbs">
							<?php foreach ( array_slice( array_values( array_unique( array_merge( array( $bp_main ), $bp_gallery ) ) ), 0, 5 ) as $bp_i => $bp_g ) : ?>
								<button type="button" class="bp-thumb<?php echo 0 === $bp_i ? ' active' : ''; ?>" data-src="<?php echo esc_url( $bp_g ); ?>"><img src="<?php echo esc_url( $bp_g ); ?>" alt="" loading="lazy" /></button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<?php if ( $bp_video ) : ?>
						<div class="bp-video-frame bp-mt-sm"><?php bp_video_player( $bp_video, $bp_main, array( 'title' => get_the_title() ) ); ?></div>
					<?php endif; ?>
				</div>
				<div class="bp-product-info">
					<?php if ( $bp_term ) : ?><p class="bp-eyebrow"><?php echo esc_html( $bp_term->name ); ?></p><?php endif; ?>
					<h1 class="bp-h1 sm"><?php the_title(); ?></h1>
					<?php if ( $bp_short ) : ?><p class="bp-lead"><?php echo esc_html( $bp_short ); ?></p><?php endif; ?>
					<div class="bp-price-block">
						<?php if ( $bp_price ) : ?>
							<span class="bp-price lg"><?php echo esc_html( $bp_price ); ?></span>
							<?php if ( $bp_unit ) : ?><span class="bp-unit">/ <?php echo esc_html( $bp_unit ); ?></span><?php endif; ?>
						<?php else : ?>
							<span class="bp-price lg"><?php esc_html_e( 'Price on request', 'brickpoint' ); ?></span>
						<?php endif; ?>
						<?php if ( $bp_avail ) : ?><span class="bp-badge <?php echo 'In Stock' === $bp_avail ? 'emerald' : 'dark'; ?>"><?php echo esc_html( $bp_avail ); ?></span><?php endif; ?>
					</div>
					<?php if ( $bp_label ) : ?><p class="bp-price-label"><?php echo esc_html( $bp_label ); ?></p><?php endif; ?>
					<?php if ( $bp_sku ) : ?><p class="bp-sku"><?php esc_html_e( 'SKU:', 'brickpoint' ); ?> <strong><?php echo esc_html( $bp_sku ); ?></strong></p><?php endif; ?>
					<div class="bp-btn-row mt-sm">
						<?php echo bp_whatsapp_button( bp_product_whatsapp_url( $bp_id ), __( 'WhatsApp Inquiry', 'brickpoint' ), 'lg' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<a class="bp-btn btn-dark lg" href="tel:<?php echo esc_attr( bp_phone_intl() ); ?>"><?php bp_the_icon( 'phone' ); ?><?php esc_html_e( 'Call Now', 'brickpoint' ); ?></a>
						<a class="bp-btn btn-outline lg" href="<?php echo esc_url( add_query_arg( 'product', get_post_field( 'post_name', $bp_id ), bp_page_url( 'contact' ) ) ); ?>"><?php esc_html_e( 'Request Quote', 'brickpoint' ); ?></a>
						<?php if ( $bp_cta_t && $bp_cta_l ) : ?><a class="bp-btn btn-brick lg" href="<?php echo esc_url( bp_link( $bp_cta_l )['url'] ); ?>"><?php echo esc_html( $bp_cta_t ); ?></a><?php endif; ?>
						<?php if ( $bp_broch ) : ?><a class="bp-btn btn-outline lg" href="<?php echo esc_url( $bp_broch ); ?>" target="_blank" rel="noopener"><?php bp_the_icon( 'download' ); ?><?php esc_html_e( 'Brochure', 'brickpoint' ); ?></a><?php endif; ?>
					</div>
					<?php if ( $bp_feats ) : ?>
						<div class="bp-mt">
							<h2 class="bp-h3"><?php esc_html_e( 'Key Features', 'brickpoint' ); ?></h2>
							<ul class="bp-checklist">
								<?php foreach ( $bp_feats as $bp_f ) : ?><li><?php bp_the_icon( 'check-circle', 'bp-icon green' ); ?><span><?php echo esc_html( $bp_f ); ?></span></li><?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
					<?php if ( $bp_specs ) : ?>
						<div class="bp-mt">
							<h2 class="bp-h3"><?php esc_html_e( 'Specifications', 'brickpoint' ); ?></h2>
							<dl class="bp-spec-table">
								<?php foreach ( $bp_specs as $bp_s ) : ?><div><dt><?php echo esc_html( $bp_s['label'] ); ?></dt><dd><?php echo esc_html( $bp_s['value'] ); ?></dd></div><?php endforeach; ?>
							</dl>
						</div>
					<?php endif; ?>
					<?php if ( get_the_content() ) : ?>
						<div class="bp-mt">
							<h2 class="bp-h3"><?php esc_html_e( 'Product Description', 'brickpoint' ); ?></h2>
							<div class="prose-bp"><?php the_content(); ?></div>
						</div>
					<?php endif; ?>
					<div class="bp-share bp-mt">
						<span><?php esc_html_e( 'Share:', 'brickpoint' ); ?></span>
						<?php $bp_url = rawurlencode( get_permalink() ); $bp_t = rawurlencode( get_the_title() ); ?>
						<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_attr( $bp_url ); ?>" target="_blank" rel="noopener" aria-label="Facebook"><?php bp_the_icon( 'facebook' ); ?></a>
						<a href="https://wa.me/?text=<?php echo esc_attr( $bp_t . '%20' . $bp_url ); ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><?php bp_the_icon( 'whatsapp' ); ?></a>
						<a href="https://twitter.com/intent/tweet?url=<?php echo esc_attr( $bp_url ); ?>&text=<?php echo esc_attr( $bp_t ); ?>" target="_blank" rel="noopener" aria-label="X"><?php bp_the_icon( 'x' ); ?></a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php
	bp_section_product_grid(
		array(
			'plain'       => 'yes',
			'head_layout' => 'row',
			'title'       => __( 'Related Products', 'brickpoint' ),
			'category'    => $bp_term ? $bp_term->slug : '',
			'exclude'     => $bp_id,
			'count'       => 4,
			'columns'     => 4,
			'button_text' => __( 'View all', 'brickpoint' ),
			'button_url'  => 'archive:bp_product',
			'related'     => bp_meta( $bp_id, '_bp_related' ),
		)
	);
	?>
</article>
<?php
