<?php
/**
 * Section renderers.
 *
 * Every visual block of the original BrickPoint design is implemented once here
 * as a function that receives a settings array. The Elementor widgets
 * (elementor/widgets/*.php) pass their control values; the PHP fallback page
 * templates pass the defaults from inc/content-defaults.php. Both paths output
 * identical markup, so the front end looks the same with or without Elementor.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read a setting with default.
 *
 * @param array  $s   Settings.
 * @param string $key Key.
 * @param mixed  $d   Default.
 * @return mixed
 */
function bp_s( $s, $key, $d = '' ) {
	if ( ! isset( $s[ $key ] ) ) {
		return $d;
	}
	$v = $s[ $key ];
	if ( is_string( $v ) && '' === trim( $v ) ) {
		return $d;
	}
	return $v;
}

/**
 * Setting that holds an image (URL string, attachment ID or Elementor media array) → URL.
 *
 * @param array  $s    Settings.
 * @param string $key  Key.
 * @param string $size Size.
 * @return string
 */
function bp_s_img( $s, $key, $size = 'large' ) {
	$v = bp_s( $s, $key, '' );
	if ( empty( $v ) ) {
		return '';
	}
	if ( is_string( $v ) && 0 === strpos( $v, 'demo:' ) ) {
		return bp_demo_image_url( substr( $v, 5 ), $size );
	}
	return bp_image_url( $v, $size );
}

/**
 * Resolve a "demo:key" or plain URL setting for videos (kept remote).
 *
 * @param array  $s   Settings.
 * @param string $key Key.
 * @return string
 */
function bp_s_video( $s, $key ) {
	$v = bp_s( $s, $key, '' );
	if ( is_array( $v ) ) {
		$v = isset( $v['url'] ) ? $v['url'] : '';
	}
	if ( is_string( $v ) && 0 === strpos( $v, 'demo:' ) ) {
		return bp_demo_video_url( substr( $v, 5 ) );
	}
	return (string) $v;
}

/**
 * Resolve a link setting (string URL, "page:slug", "archive:type", "wa:message" or Elementor URL array).
 *
 * @param mixed $v Value.
 * @return array{url:string,blank:bool}
 */
function bp_link( $v ) {
	$blank = false;
	if ( is_array( $v ) ) {
		$blank = ! empty( $v['is_external'] );
		$v     = isset( $v['url'] ) ? $v['url'] : '';
	}
	$v = (string) $v;
	if ( 0 === strpos( $v, 'page:' ) ) {
		$v = bp_page_url( substr( $v, 5 ) );
	} elseif ( 0 === strpos( $v, 'archive:' ) ) {
		$v = bp_archive_url( substr( $v, 8 ) );
	} elseif ( 0 === strpos( $v, 'wa:' ) ) {
		$v     = bp_whatsapp_url( str_replace( '\n', "\n", substr( $v, 3 ) ) );
		$blank = true;
	} elseif ( 'home' === $v ) {
		$v = home_url( '/' );
	} elseif ( 'whatsapp' === $v ) {
		$v     = bp_default_whatsapp_url();
		$blank = true;
	} elseif ( 'tel' === $v ) {
		$v = 'tel:+' . bp_phone_intl();
	}
	if ( false !== strpos( $v, 'wa.me' ) ) {
		$blank = true;
	}
	return array( 'url' => $v, 'blank' => $blank );
}

/**
 * Render a list of buttons.
 *
 * @param array  $buttons Items: text,url,style,icon,icon_pos,new_tab.
 * @param string $size    sm|md|lg|xl or ''.
 * @param string $wrap    Wrapper class ('' for none).
 * @return string
 */
function bp_render_buttons( $buttons, $size = '', $wrap = 'bp-btn-row' ) {
	if ( empty( $buttons ) || ! is_array( $buttons ) ) {
		return '';
	}
	$html = '';
	foreach ( $buttons as $b ) {
		$text = bp_s( $b, 'text', '' );
		if ( '' === $text ) {
			continue;
		}
		$style = bp_s( $b, 'style', 'brick' );
		$link  = bp_link( bp_s( $b, 'url', '#' ) );
		if ( 'whatsapp' === $style && ( '#' === $link['url'] || '' === $link['url'] ) ) {
			$link = array( 'url' => bp_default_whatsapp_url(), 'blank' => true );
		}
		$icon   = bp_s( $b, 'icon', '' );
		if ( 'whatsapp' === $style && '' === $icon && empty( $b['no_icon'] ) ) {
			$icon = 'message-circle';
		}
		$pos    = bp_s( $b, 'icon_pos', 'whatsapp' === $style ? 'left' : 'right' );
		$blank  = $link['blank'] || ! empty( $b['new_tab'] );
		$size_c = bp_s( $b, 'size', $size );
		$class  = 'bp-btn btn-' . sanitize_html_class( $style ) . ( $size_c ? ' ' . sanitize_html_class( $size_c ) : '' ) . ( ! empty( $b['full'] ) ? ' full' : '' );
		$svg    = $icon ? bp_icon( $icon ) : '';
		$html  .= sprintf(
			'<a class="%1$s" href="%2$s"%3$s>%4$s<span>%5$s</span>%6$s</a>',
			esc_attr( $class ),
			esc_url( $link['url'] ),
			$blank ? ' target="_blank" rel="noopener"' : '',
			'left' === $pos ? $svg : '',
			esc_html( $text ),
			'right' === $pos ? $svg : ''
		);
	}
	if ( '' === $html ) {
		return '';
	}
	return $wrap ? '<div class="' . esc_attr( $wrap ) . '">' . $html . '</div>' : $html;
}

/**
 * Normalise repeater/textarea list settings: accepts array of arrays, array of strings or newline text.
 *
 * @param mixed  $v   Value.
 * @param string $key Field to pluck when items are arrays.
 * @return array
 */
function bp_list( $v, $key = 'text' ) {
	if ( is_string( $v ) ) {
		return bp_lines( $v );
	}
	if ( ! is_array( $v ) ) {
		return array();
	}
	$out = array();
	foreach ( $v as $item ) {
		if ( is_array( $item ) ) {
			if ( isset( $item[ $key ] ) && '' !== $item[ $key ] ) {
				$out[] = $item[ $key ];
			}
		} elseif ( '' !== trim( (string) $item ) ) {
			$out[] = (string) $item;
		}
	}
	return $out;
}

/**
 * Section wrapper open.
 *
 * @param string $classes Classes.
 * @param string $id      ID.
 */
function bp_section_open( $classes, $id = '' ) {
	echo '<section class="' . esc_attr( $classes ) . '"' . ( $id ? ' id="' . esc_attr( $id ) . '"' : '' ) . '>';
}

/* ------------------------------------------------------------------------- */
/* HERO                                                                       */
/* ------------------------------------------------------------------------- */

/**
 * Home hero.
 *
 * @param array $s Settings.
 */
function bp_section_hero( $s ) {
	$bg      = bp_s_img( $s, 'bg_image', 'bp-hero' );
	$video   = bp_s_video( $s, 'video_url' );
	$poster  = bp_s_img( $s, 'video_poster', 'bp-video' );
	$ss7_img = bp_s_img( $s, 'ss7_image', 'medium' );
	$vlink   = bp_link( bp_s( $s, 'video_link', 'archive:bp_video' ) );
	$ss7link = bp_link( bp_s( $s, 'ss7_link', 'page:ss7-bricks' ) );
	$trust   = bp_s( $s, 'trust', array() );
	$marquee = bp_list( bp_s( $s, 'marquee', '' ) );
	?>
	<section class="bp-hero">
		<div class="bp-hero-bg">
			<?php if ( $bg ) : ?><img src="<?php echo esc_url( $bg ); ?>" alt="<?php echo esc_attr( bp_s( $s, 'bg_alt', __( 'Bricklayers building a wall', 'brickpoint' ) ) ); ?>" fetchpriority="high" /><?php endif; ?>
			<div class="bp-hero-grad"></div>
			<div class="brick-lines"></div>
		</div>
		<div class="bp-container bp-hero-inner">
			<div>
				<?php if ( bp_s( $s, 'badge_text' ) ) : ?>
					<p class="bp-badge-outline bp-fade-up"><?php bp_the_icon( bp_s( $s, 'badge_icon', 'factory' ), 'bp-icon sm' ); ?> <?php echo esc_html( bp_s( $s, 'badge_text' ) ); ?></p>
				<?php endif; ?>
				<h1 class="bp-hero-title bp-fade-up"><?php echo wp_kses_post( bp_s( $s, 'title' ) ); ?></h1>
				<?php if ( bp_s( $s, 'text' ) ) : ?><p class="bp-hero-text bp-fade-up"><?php echo wp_kses_post( bp_s( $s, 'text' ) ); ?></p><?php endif; ?>
				<?php echo bp_render_buttons( bp_s( $s, 'buttons', array() ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php if ( ! empty( $trust ) ) : ?>
					<div class="bp-trust">
						<?php foreach ( $trust as $t ) : ?>
							<span><?php bp_the_icon( bp_s( $t, 'icon', 'check-circle' ) ); ?> <?php echo esc_html( bp_s( $t, 'text' ) ); ?></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
			<div class="bp-hero-media">
				<?php if ( $video || $poster ) : ?>
					<div class="bp-hero-video hero-video-frame">
						<?php if ( $video ) : ?>
							<?php bp_video_player( $video, $poster, array( 'autoplay' => true, 'controls' => false, 'class' => 'bp-hero-video-el', 'title' => __( 'BrickPoint brick construction video', 'brickpoint' ) ) ); ?>
						<?php else : ?>
							<img src="<?php echo esc_url( $poster ); ?>" alt="" />
						<?php endif; ?>
						<div class="bp-shade"></div>
						<div class="bp-hero-caption">
							<div>
								<?php if ( bp_s( $s, 'video_eyebrow' ) ) : ?><p class="bp-eyebrow"><?php echo esc_html( bp_s( $s, 'video_eyebrow' ) ); ?></p><?php endif; ?>
								<?php if ( bp_s( $s, 'video_title' ) ) : ?><strong><?php echo esc_html( bp_s( $s, 'video_title' ) ); ?></strong><?php endif; ?>
							</div>
							<a class="bp-hero-play" href="<?php echo esc_url( $vlink['url'] ); ?>" aria-label="<?php esc_attr_e( 'Watch all videos', 'brickpoint' ); ?>"><?php bp_the_icon( 'play' ); ?></a>
						</div>
					</div>
				<?php endif; ?>
				<?php if ( 'yes' === bp_s( $s, 'ss7_show', 'yes' ) ) : ?>
					<div class="bp-ss7-float">
						<div class="bp-ss7-card">
							<div class="bp-ss7-inner">
								<?php if ( $ss7_img ) : ?><img src="<?php echo esc_url( $ss7_img ); ?>" alt="<?php esc_attr_e( 'SS7 brick close-up', 'brickpoint' ); ?>" loading="lazy" /><?php endif; ?>
								<div>
									<p class="bp-eyebrow"><?php bp_the_icon( 'award' ); ?> <?php echo esc_html( bp_s( $s, 'ss7_eyebrow', __( 'Flagship', 'brickpoint' ) ) ); ?></p>
									<strong><?php echo esc_html( bp_s( $s, 'ss7_title', __( 'SS7 Bricks', 'brickpoint' ) ) ); ?></strong>
									<a href="<?php echo esc_url( $ss7link['url'] ); ?>"><?php echo esc_html( bp_s( $s, 'ss7_link_text', __( 'View SS7 range →', 'brickpoint' ) ) ); ?></a>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>
				<?php if ( bp_s( $s, 'stat_number' ) ) : ?>
					<div class="bp-stat-float">
						<strong><?php echo esc_html( bp_s( $s, 'stat_number' ) ); ?><span><?php echo esc_html( bp_s( $s, 'stat_suffix', '+' ) ); ?></span></strong>
						<small><?php echo esc_html( bp_s( $s, 'stat_label' ) ); ?></small>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php if ( ! empty( $marquee ) ) : ?>
			<div class="bp-marquee">
				<div class="marquee-track" aria-hidden="true">
					<?php for ( $k = 0; $k < 2; $k++ ) : ?>
						<div>
							<?php foreach ( $marquee as $m ) : ?><span><?php echo esc_html( $m ); ?></span><?php endforeach; ?>
						</div>
					<?php endfor; ?>
				</div>
			</div>
		<?php endif; ?>
	</section>
	<?php
}

/* ------------------------------------------------------------------------- */
/* IMAGE + CONTENT (Why BrickPoint)                                           */
/* ------------------------------------------------------------------------- */

/**
 * Image with floating cards + heading + checklist + buttons.
 *
 * @param array $s Settings.
 */
function bp_section_image_content( $s ) {
	$img   = bp_s_img( $s, 'image', 'bp-hero' );
	$items = bp_list( bp_s( $s, 'checklist', '' ) );
	$rev   = 'yes' === bp_s( $s, 'reverse', '' );
	?>
	<section class="bp-section <?php echo esc_attr( bp_s( $s, 'bg', '' ) ); ?>">
		<div class="bp-container bp-2col">
			<div class="bp-reveal<?php echo $rev ? ' bp-order-2' : ''; ?>" <?php echo $rev ? 'style="order:2"' : ''; ?>>
				<div class="bp-img-float img-zoom">
					<?php if ( $img ) : ?><img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( bp_s( $s, 'image_alt', __( 'Brick kiln production', 'brickpoint' ) ) ); ?>" loading="lazy" /><?php endif; ?>
					<?php if ( bp_s( $s, 'float1_title' ) || bp_s( $s, 'float2_title' ) ) : ?>
						<div class="bp-img-float-cards">
							<?php if ( bp_s( $s, 'float1_title' ) ) : ?>
								<div class="bp-float-card"><strong><?php echo esc_html( bp_s( $s, 'float1_title' ) ); ?></strong><small><?php echo esc_html( bp_s( $s, 'float1_text' ) ); ?></small></div>
							<?php endif; ?>
							<?php if ( bp_s( $s, 'float2_title' ) ) : ?>
								<div class="bp-float-card dark"><strong><?php echo esc_html( bp_s( $s, 'float2_title' ) ); ?></strong><small><?php echo esc_html( bp_s( $s, 'float2_text' ) ); ?></small></div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<div>
				<div class="bp-reveal"><?php bp_section_head( bp_s( $s, 'eyebrow' ), bp_s( $s, 'title' ), bp_s( $s, 'text' ), false, 'left' ); ?></div>
				<?php if ( $items ) : ?>
					<ul class="bp-checklist bp-reveal delay-1">
						<?php foreach ( $items as $i ) : ?><li><?php bp_the_icon( 'check-circle' ); ?> <span><?php echo esc_html( $i ); ?></span></li><?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<?php echo bp_render_buttons( bp_s( $s, 'buttons', array() ), '', 'bp-btn-row mt' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	</section>
	<?php
}

/* ------------------------------------------------------------------------- */
/* CATEGORY GRID                                                              */
/* ------------------------------------------------------------------------- */

/**
 * Product category grid (dark home style or light categories-page style).
 *
 * @param array $s Settings.
 */
function bp_section_category_grid( $s ) {
	$style = bp_s( $s, 'style', 'dark' );
	$count = (int) bp_s( $s, 'count', 12 );
	$terms = bp_get_product_categories();
	if ( $count > 0 ) {
		$terms = array_slice( $terms, 0, $count );
	}
	$light = 'light' === $style;
	?>
	<section class="<?php echo $light ? 'bp-section-xs' : 'bp-section bp-dark'; ?>">
		<div class="bp-container">
			<?php if ( bp_s( $s, 'title' ) ) : ?>
				<div class="bp-reveal"><?php bp_section_head( bp_s( $s, 'eyebrow' ), bp_s( $s, 'title' ), bp_s( $s, 'text' ), ! $light ); ?></div>
			<?php endif; ?>
			<?php if ( $terms ) : ?>
				<div class="bp-grid <?php echo $light ? 'cols-3 gap-lg' : 'cols-4 gap-sm keep-2'; ?><?php echo bp_s( $s, 'title' ) ? ' bp-mt' : ''; ?>">
					<?php foreach ( $terms as $i => $t ) : ?>
						<?php if ( $light ) : ?>
							<?php bp_category_card_light( $t, $i ); ?>
						<?php else : ?>
							<?php get_template_part( 'template-parts/card', 'category', array( 'term' => $t, 'style' => 'dark', 'reveal' => true, 'index' => $i ) ); ?>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<p class="bp-empty"><?php esc_html_e( 'No product categories yet. Run the BrickPoint demo import or add categories under Products → Product Categories.', 'brickpoint' ); ?></p>
			<?php endif; ?>
			<?php if ( bp_s( $s, 'button_text' ) ) : ?>
				<div class="bp-btn-row center mt bp-reveal">
					<?php $l = bp_link( bp_s( $s, 'button_url', 'page:categories' ) ); ?>
					<a class="bp-btn <?php echo $light ? 'btn-outline' : 'btn-ghost plain'; ?>" href="<?php echo esc_url( $l['url'] ); ?>"><?php echo esc_html( bp_s( $s, 'button_text' ) ); ?> <?php bp_the_icon( 'arrow-right' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

/**
 * Light category card exactly like the /categories page (Explore + Quote buttons).
 *
 * @param WP_Term $t Term.
 * @param int     $i Index.
 */
function bp_category_card_light( $t, $i = 0 ) {
	$img = bp_term_image( $t->term_id, 'bp_cat_image', 'bp-wide' );
	?>
	<article class="bp-card r-3xl card-hover bp-reveal delay-<?php echo (int) ( $i % 3 ); ?>">
		<a class="bp-media r-16-9 img-zoom" href="<?php echo esc_url( bp_category_url( $t ) ); ?>">
			<?php if ( $img ) : ?><img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $t->name ); ?>" loading="lazy" /><?php endif; ?>
			<span class="bp-badge dark bp-abs-tl lg"><?php echo esc_html( sprintf( _n( '%d product', '%d products', (int) $t->count, 'brickpoint' ), (int) $t->count ) ); ?></span>
		</a>
		<div class="bp-card-body lg">
			<h3 class="bp-card-title lg" style="margin-top:0"><a href="<?php echo esc_url( bp_category_url( $t ) ); ?>"><?php echo esc_html( $t->name ); ?></a></h3>
			<?php if ( $t->description ) : ?><p class="bp-card-text line-clamp-2"><?php echo esc_html( $t->description ); ?></p><?php endif; ?>
			<div class="bp-btn-2">
				<a class="bp-btn btn-dark sm" href="<?php echo esc_url( bp_category_url( $t ) ); ?>"><?php esc_html_e( 'Explore', 'brickpoint' ); ?> <?php bp_the_icon( 'arrow-right', 'bp-icon sm' ); ?></a>
				<?php echo bp_whatsapp_button( bp_category_whatsapp_url( $t ), __( 'Quote', 'brickpoint' ), 'sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	</article>
	<?php
}

/* ------------------------------------------------------------------------- */
/* SS7 FEATURE                                                                */
/* ------------------------------------------------------------------------- */

/**
 * Flagship SS7 section.
 *
 * @param array $s Settings.
 */
function bp_section_ss7_feature( $s ) {
	$main   = bp_s_img( $s, 'image', 'bp-hero' );
	$thumbs = bp_s( $s, 'thumbs', array() );
	$specs  = bp_s( $s, 'specs', array() );
	?>
	<section class="bp-section lg bp-ss7-section">
		<div class="bp-container bp-2col">
			<div>
				<div class="bp-reveal"><?php bp_section_head( bp_s( $s, 'eyebrow' ), bp_s( $s, 'title' ), bp_s( $s, 'text' ), false, 'left' ); ?></div>
				<?php if ( $specs ) : ?>
					<div class="bp-spec-grid bp-reveal delay-1">
						<?php foreach ( $specs as $sp ) : ?>
							<div class="bp-spec-box"><span class="bp-card-cat"><?php echo esc_html( bp_s( $sp, 'label' ) ); ?></span><p><?php echo esc_html( bp_s( $sp, 'value' ) ); ?></p></div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
				<div class="bp-reveal delay-2"><?php echo bp_render_buttons( bp_s( $s, 'buttons', array() ), '', 'bp-btn-row mt-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			</div>
			<div class="bp-reveal delay-1">
				<div class="bp-ss7-gallery-wrap">
					<div class="bp-img-main img-zoom"><?php if ( $main ) : ?><img src="<?php echo esc_url( $main ); ?>" alt="<?php esc_attr_e( 'SS7 red bricks stacked', 'brickpoint' ); ?>" loading="lazy" /><?php endif; ?></div>
					<?php if ( $thumbs ) : ?>
						<div class="bp-ss7-thumbs">
							<?php foreach ( array_slice( (array) $thumbs, 0, 3 ) as $th ) : $u = bp_s_img( array( 'i' => is_array( $th ) && isset( $th['image'] ) ? $th['image'] : $th ), 'i', 'bp-card' ); if ( $u ) : ?>
								<img src="<?php echo esc_url( $u ); ?>" alt="<?php esc_attr_e( 'SS7 brick gallery', 'brickpoint' ); ?>" loading="lazy" />
							<?php endif; endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
	<?php
}

/* ------------------------------------------------------------------------- */
/* PRODUCT GRID                                                               */
/* ------------------------------------------------------------------------- */

/**
 * Product grid.
 *
 * @param array $s Settings.
 */
function bp_section_product_grid( $s ) {
	$qa = array(
		'posts_per_page' => (int) bp_s( $s, 'count', 8 ),
		'featured'       => 'yes' === bp_s( $s, 'featured', 'yes' ),
		'category'       => bp_s( $s, 'category', '' ),
		'post__not_in'   => array_filter( array( (int) bp_s( $s, 'exclude', 0 ) ) ), // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
	);
	$related = array_filter( array_map( 'absint', preg_split( '/[\s,]+/', (string) bp_s( $s, 'related', '' ) ) ) );
	if ( $related ) {
		$qa['post__in'] = $related;
		$qa['orderby']  = 'post__in';
		$qa['featured'] = false;
		$qa['category'] = '';
	}
	$q = bp_query_products( $qa );
	if ( ! $q->have_posts() && ! $related && $qa['category'] ) {
		// Fallback: any products when the category has no other items.
		$qa['category'] = '';
		$q = bp_query_products( $qa );
	}
	$cols  = (int) bp_s( $s, 'columns', 4 );
	$plain = 'yes' === bp_s( $s, 'plain', '' );
	$dark  = 'yes' === bp_s( $s, 'dark', '' );
	?>
	<section class="<?php echo $plain ? 'bp-section-xs' : 'bp-section'; ?> <?php echo esc_attr( bp_s( $s, 'bg', $dark ? 'bp-dark' : '' ) ); ?>">
		<div class="bp-container">
			<?php if ( bp_s( $s, 'title' ) && 'row' === bp_s( $s, 'head_layout', '' ) ) : ?>
				<div class="bp-section-head-row<?php echo $dark ? ' light' : ''; ?>">
					<h2 class="bp-h2 sm"><?php echo wp_kses_post( bp_s( $s, 'title' ) ); ?></h2>
					<?php if ( bp_s( $s, 'button_text' ) ) : $l = bp_link( bp_s( $s, 'button_url', 'archive:bp_product' ) ); ?>
						<a class="bp-link" href="<?php echo esc_url( $l['url'] ); ?>"><?php echo esc_html( bp_s( $s, 'button_text' ) ); ?> <?php bp_the_icon( 'arrow-right' ); ?></a>
					<?php endif; ?>
				</div>
			<?php elseif ( bp_s( $s, 'title' ) ) : ?>
				<div class="bp-reveal"><?php bp_section_head( bp_s( $s, 'eyebrow' ), bp_s( $s, 'title' ), bp_s( $s, 'text' ), $dark ); ?></div>
			<?php endif; ?>
			<?php if ( $q->have_posts() ) : ?>
				<div class="bp-grid cols-<?php echo (int) $cols; ?><?php echo bp_s( $s, 'title' ) ? ' bp-mt' : ''; ?>">
					<?php $i = 0; while ( $q->have_posts() ) : $q->the_post(); get_template_part( 'template-parts/card', 'product', array( 'reveal' => true, 'index' => $i++ ) ); endwhile; wp_reset_postdata(); ?>
				</div>
			<?php else : ?>
				<p class="bp-empty"><?php esc_html_e( 'No products found yet. Add products under Products → Add New or run the demo import.', 'brickpoint' ); ?></p>
			<?php endif; ?>
			<?php if ( bp_s( $s, 'button_text' ) && 'row' !== bp_s( $s, 'head_layout', '' ) ) : $l = bp_link( bp_s( $s, 'button_url', 'archive:bp_product' ) ); ?>
				<div class="bp-btn-row center mt"><a class="bp-btn btn-brick" href="<?php echo esc_url( $l['url'] ); ?>"><?php echo esc_html( bp_s( $s, 'button_text' ) ); ?> <?php bp_the_icon( 'arrow-right' ); ?></a></div>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

/* ------------------------------------------------------------------------- */
/* VIDEO SHOWCASE                                                             */
/* ------------------------------------------------------------------------- */

/**
 * Video showcase (main + two minis + latest video cards).
 *
 * @param array $s Settings.
 */
function bp_section_video_showcase( $s ) {
	$main   = bp_s_video( $s, 'main_video' );
	$mainp  = bp_s_img( $s, 'main_poster', 'bp-hero' );
	$minis  = array();
	foreach ( array( 1, 2 ) as $n ) {
		$v = bp_s_video( $s, 'mini' . $n . '_video' );
		if ( $v || bp_s_img( $s, 'mini' . $n . '_poster' ) ) {
			$minis[] = array(
				'video'   => $v,
				'poster'  => bp_s_img( $s, 'mini' . $n . '_poster', 'bp-video' ),
				'caption' => bp_s( $s, 'mini' . $n . '_caption' ),
				'text'    => bp_s( $s, 'mini' . $n . '_text' ),
			);
		}
	}
	$count = (int) bp_s( $s, 'count', 3 );
	$btn   = bp_link( bp_s( $s, 'button_url', 'archive:bp_video' ) );
	?>
	<section class="bp-section bp-charcoal">
		<div class="bp-container">
			<div class="bp-section-head-row">
				<div class="bp-reveal"><?php bp_section_head( bp_s( $s, 'eyebrow' ), bp_s( $s, 'title' ), bp_s( $s, 'text' ), true, 'left' ); ?></div>
				<?php if ( bp_s( $s, 'button_text' ) ) : ?><div class="bp-reveal delay-1"><a class="bp-btn btn-ghost plain" href="<?php echo esc_url( $btn['url'] ); ?>"><?php echo esc_html( bp_s( $s, 'button_text' ) ); ?> <?php bp_the_icon( 'arrow-right' ); ?></a></div><?php endif; ?>
			</div>
			<?php if ( $main || $minis ) : ?>
				<div class="bp-video-showcase">
					<?php if ( $main ) : ?>
						<div class="bp-video-main hero-video-frame bp-reveal">
							<?php bp_video_player( $main, $mainp, array( 'title' => bp_s( $s, 'main_badge', __( 'Featured', 'brickpoint' ) ) ) ); ?>
							<?php if ( bp_s( $s, 'main_badge' ) ) : ?><span class="bp-badge bp-abs-tl lg"><?php echo esc_html( bp_s( $s, 'main_badge' ) ); ?></span><?php endif; ?>
						</div>
					<?php endif; ?>
					<?php if ( $minis ) : ?>
						<div class="bp-video-side">
							<?php foreach ( $minis as $k => $m ) : ?>
								<div class="bp-video-mini bp-reveal delay-<?php echo (int) $k + 1; ?>">
									<div class="bp-media r-16-9">
										<?php if ( $m['video'] ) : ?>
											<?php bp_video_player( $m['video'], $m['poster'], array( 'autoplay' => true, 'controls' => false, 'title' => $m['caption'] ) ); ?>
										<?php elseif ( $m['poster'] ) : ?>
											<img src="<?php echo esc_url( $m['poster'] ); ?>" alt="" loading="lazy" />
										<?php endif; ?>
										<?php if ( $m['caption'] ) : ?><span class="bp-badge dark bp-abs-bl"><?php echo esc_html( $m['caption'] ); ?></span><?php endif; ?>
									</div>
									<?php if ( $m['text'] ) : ?><p><?php echo esc_html( $m['text'] ); ?></p><?php endif; ?>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php
			if ( $count > 0 ) {
				$q = new WP_Query( array( 'post_type' => 'bp_video', 'posts_per_page' => $count, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'DESC' ) ) );
				if ( $q->have_posts() ) {
					echo '<div class="bp-grid cols-3 bp-mt-sm" style="margin-top:2rem">';
					$i = 0;
					while ( $q->have_posts() ) {
						$q->the_post();
						get_template_part( 'template-parts/card', 'video', array( 'reveal' => true, 'index' => $i++ ) );
					}
					echo '</div>';
				}
				wp_reset_postdata();
			}
			?>
		</div>
	</section>
	<?php
}

/* ------------------------------------------------------------------------- */
/* VIDEO GRID / PROJECT GRID / LOCATION GRID / BLOG GRID                      */
/* ------------------------------------------------------------------------- */

/**
 * Generic CPT grid section.
 *
 * @param array  $s    Settings.
 * @param string $type bp_video|bp_project|bp_location|post.
 */
function bp_section_post_grid( $s, $type ) {
	$args = array(
		'post_type'      => $type,
		'posts_per_page' => (int) bp_s( $s, 'count', 6 ),
		'orderby'        => 'post' === $type ? 'date' : array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
		'post__not_in'   => array_filter( array( (int) bp_s( $s, 'exclude', 0 ) ) ), // phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in
	);
	$dark = 'yes' === bp_s( $s, 'dark', '' ) || false !== strpos( bp_s( $s, 'bg', '' ), 'dark' );
	if ( 'yes' === bp_s( $s, 'plain', '' ) ) {
		$s['padding'] = 'bp-section-xs';
	}
	if ( $dark && ! bp_s( $s, 'bg' ) ) {
		$s['bg'] = 'bp-dark';
	}
	$featured_keys = array( 'bp_video' => '_bpv_featured', 'bp_project' => '_bpp_featured' );
	if ( 'yes' === bp_s( $s, 'featured', '' ) && isset( $featured_keys[ $type ] ) ) {
		$args['meta_key']   = $featured_keys[ $type ]; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		$args['meta_value'] = '1'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
	}
	$q = new WP_Query( $args );
	if ( ! $q->have_posts() && isset( $args['meta_key'] ) ) {
		unset( $args['meta_key'], $args['meta_value'] );
		$q = new WP_Query( $args );
	}
	$card = array( 'bp_video' => 'video', 'bp_project' => 'project', 'bp_location' => 'location', 'post' => 'blog' );
	$cols = (int) bp_s( $s, 'columns', 'bp_location' === $type ? 2 : 3 );
	$full = 'yes' === bp_s( $s, 'full', '' );
	?>
	<section class="<?php echo esc_attr( bp_s( $s, 'padding', 'bp-section' ) ); ?> <?php echo esc_attr( bp_s( $s, 'bg', '' ) ); ?>">
		<div class="bp-container">
			<?php if ( bp_s( $s, 'title' ) && 'row' === bp_s( $s, 'head_layout', '' ) ) : ?>
				<div class="bp-section-head-row<?php echo $dark ? ' light' : ''; ?>">
					<h2 class="bp-h2 sm"><?php echo wp_kses_post( bp_s( $s, 'title' ) ); ?></h2>
					<?php if ( bp_s( $s, 'button_text' ) ) : $l = bp_link( bp_s( $s, 'button_url', 'archive:' . $type ) ); ?>
						<a class="bp-link" href="<?php echo esc_url( $l['url'] ); ?>"><?php echo esc_html( bp_s( $s, 'button_text' ) ); ?> <?php bp_the_icon( 'arrow-right' ); ?></a>
					<?php endif; ?>
				</div>
			<?php elseif ( bp_s( $s, 'title' ) ) : ?><div class="bp-reveal"><?php bp_section_head( bp_s( $s, 'eyebrow' ), bp_s( $s, 'title' ), bp_s( $s, 'text' ), $dark ); ?></div><?php endif; ?>
			<?php if ( bp_s( $s, 'notice' ) ) : ?><div class="bp-notice xs<?php echo bp_s( $s, 'title' ) ? ' bp-mt-sm' : ''; ?>"><?php echo wp_kses_post( bp_s( $s, 'notice' ) ); ?></div><?php endif; ?>
			<?php if ( $q->have_posts() ) : ?>
				<div class="bp-grid cols-<?php echo (int) $cols; ?> gap-lg<?php echo ( bp_s( $s, 'title' ) || bp_s( $s, 'notice' ) ) ? ' bp-mt' : ''; ?>">
					<?php $i = 0; while ( $q->have_posts() ) : $q->the_post(); get_template_part( 'template-parts/card', $card[ $type ], array( 'reveal' => true, 'index' => $i++, 'full' => $full ) ); endwhile; wp_reset_postdata(); ?>
				</div>
			<?php else : ?>
				<p class="bp-empty"><?php echo esc_html( bp_s( $s, 'empty', __( 'Nothing here yet.', 'brickpoint' ) ) ); ?></p>
			<?php endif; ?>
			<?php if ( bp_s( $s, 'button_text' ) && 'row' !== bp_s( $s, 'head_layout', '' ) ) : $l = bp_link( bp_s( $s, 'button_url', 'archive:' . $type ) ); ?>
				<div class="bp-btn-row center mt"><a class="bp-btn <?php echo esc_attr( bp_s( $s, 'button_style', 'btn-outline' ) ); ?>" href="<?php echo esc_url( $l['url'] ); ?>"><?php echo esc_html( bp_s( $s, 'button_text' ) ); ?> <?php bp_the_icon( 'arrow-right' ); ?></a></div>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

/* ------------------------------------------------------------------------- */
/* AUDIENCE CARDS / TEAM CARDS                                                */
/* ------------------------------------------------------------------------- */

/**
 * Who We Serve cards.
 *
 * @param array $s Settings.
 */
function bp_section_audience( $s ) {
	$items = bp_s( $s, 'items', array() );
	?>
	<section class="bp-section bp-sand">
		<div class="bp-container">
			<?php if ( bp_s( $s, 'title' ) ) : ?><div class="bp-reveal"><?php bp_section_head( bp_s( $s, 'eyebrow' ), bp_s( $s, 'title' ), bp_s( $s, 'text' ) ); ?></div><?php endif; ?>
			<div class="bp-grid cols-3 bp-mt">
				<?php foreach ( (array) $items as $i => $it ) : $l = bp_link( bp_s( $it, 'url', '#' ) ); $img = bp_s_img( $it, 'image', 'bp-wide' ); ?>
					<a class="bp-card bp-aud-card card-hover bp-reveal delay-<?php echo (int) ( $i % 3 ); ?>" href="<?php echo esc_url( $l['url'] ); ?>">
						<div class="bp-media r-16-9 img-zoom dim-8">
							<?php if ( $img ) : ?><img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( bp_s( $it, 'title' ) ); ?>" loading="lazy" /><?php endif; ?>
							<div class="bp-shade ink"></div>
						</div>
						<div class="bp-card-body">
							<h3 class="bp-card-title"><?php echo esc_html( bp_s( $it, 'title' ) ); ?></h3>
							<p class="bp-card-text"><?php echo esc_html( bp_s( $it, 'text' ) ); ?></p>
							<span class="bp-link"><?php echo esc_html( bp_s( $it, 'link_text', __( 'Learn more', 'brickpoint' ) ) ); ?> <?php bp_the_icon( 'arrow-right' ); ?></span>
						</div>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Leadership cards.
 *
 * @param array $s Settings.
 */
function bp_section_team( $s ) {
	$items = bp_s( $s, 'items', array() );
	?>
	<section class="bp-section-sm">
		<div class="bp-container">
			<?php if ( bp_s( $s, 'title' ) ) : ?><div class="bp-reveal"><?php bp_section_head( bp_s( $s, 'eyebrow' ), bp_s( $s, 'title' ), bp_s( $s, 'text' ) ); ?></div><?php endif; ?>
			<div class="bp-grid cols-2 gap-lg" style="max-width:48rem;margin:2rem auto 0">
				<?php foreach ( (array) $items as $i => $m ) : $img = bp_s_img( $m, 'image', 'bp-wide' ); ?>
					<div class="bp-card bp-team-card card-hover bp-reveal delay-<?php echo (int) ( $i % 2 ); ?>">
						<?php if ( $img ) : ?><img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( bp_s( $m, 'name' ) ); ?>" loading="lazy" /><?php endif; ?>
						<div class="bp-card-body">
							<h3 class="bp-card-title"><?php echo esc_html( bp_s( $m, 'name' ) ); ?></h3>
							<p class="bp-role"><?php echo esc_html( bp_s( $m, 'role' ) ); ?></p>
							<p class="bp-card-text"><?php echo esc_html( bp_s( $m, 'text', 'BrickPoint • ' . bp_phone_display() ) ); ?></p>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<?php echo bp_render_buttons( bp_s( $s, 'buttons', array() ), '', 'bp-btn-row center mt' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	</section>
	<?php
}

/* ------------------------------------------------------------------------- */
/* CTA BAND / CTA BOX                                                         */
/* ------------------------------------------------------------------------- */

/**
 * Full-width dark CTA band with background image.
 *
 * @param array $s Settings.
 */
function bp_section_cta_band( $s ) {
	$bg = bp_s_img( $s, 'bg_image', 'bp-hero' );
	?>
	<section class="bp-cta">
		<div class="bp-cta-bg"><?php if ( $bg ) : ?><img src="<?php echo esc_url( $bg ); ?>" alt="" loading="lazy" /><?php endif; ?></div>
		<div class="bp-container bp-cta-inner">
			<div class="bp-reveal">
				<?php if ( bp_s( $s, 'eyebrow' ) ) : ?><p class="bp-eyebrow light"><?php echo esc_html( bp_s( $s, 'eyebrow' ) ); ?></p><?php endif; ?>
				<h2 class="bp-cta-title"><?php echo wp_kses_post( bp_s( $s, 'title' ) ); ?></h2>
				<?php if ( bp_s( $s, 'meta' ) ) : ?><p class="bp-cta-meta"><?php bp_the_icon( 'phone' ); ?> <span><?php echo esc_html( bp_s( $s, 'meta' ) ); ?></span></p><?php endif; ?>
			</div>
			<div class="bp-reveal delay-1"><?php echo bp_render_buttons( bp_s( $s, 'buttons', array() ), 'xl', 'bp-cta-actions' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		</div>
	</section>
	<?php
}

/**
 * Rounded CTA box (dark / sand / outline; centred or split).
 *
 * @param array $s Settings.
 */
function bp_section_cta_box( $s ) {
	$style = bp_s( $s, 'style', 'dark' );
	$left  = 'yes' === bp_s( $s, 'split', '' );
	$outer = bp_s( $s, 'outer', 'bp-section-xs' );
	?>
	<section class="<?php echo esc_attr( $outer ); ?>">
		<div class="bp-container">
			<div class="bp-cta-box <?php echo esc_attr( $style ); ?><?php echo $left ? ' left' : ''; ?>">
				<div>
					<?php if ( bp_s( $s, 'eyebrow' ) ) : ?><p class="bp-eyebrow"><?php echo esc_html( bp_s( $s, 'eyebrow' ) ); ?></p><?php endif; ?>
					<h2 class="bp-h2 sm"><?php echo wp_kses_post( bp_s( $s, 'title' ) ); ?></h2>
					<?php if ( bp_s( $s, 'text' ) ) : ?><p><?php echo wp_kses_post( bp_s( $s, 'text' ) ); ?></p><?php endif; ?>
				</div>
				<?php echo bp_render_buttons( bp_s( $s, 'buttons', array() ), 'lg', 'bp-btn-row' . ( $left ? '' : ' center' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	</section>
	<?php
}

/* ------------------------------------------------------------------------- */
/* PAGE HEADER                                                                */
/* ------------------------------------------------------------------------- */

/**
 * Dark page header. Supports: bg image, buttons, chips, filter pills, search form,
 * breadcrumbs, split layout with side image + tags (SS7 page).
 *
 * @param array $s Settings.
 */
function bp_section_page_header( $s ) {
	$bg     = bp_s_img( $s, 'bg_image', 'bp-hero' );
	$size   = bp_s( $s, 'size', '' );
	$side   = bp_s_img( $s, 'side_image', 'bp-hero' );
	$tags   = bp_list( bp_s( $s, 'side_tags', '' ) );
	$chips  = bp_list( bp_s( $s, 'chips', '' ) );
	$crumbs = bp_s( $s, 'breadcrumbs', array() );
	$title  = bp_s( $s, 'title' );
	$text   = bp_s( $s, 'text' );
	$eyebrow = bp_s( $s, 'eyebrow' );
	// Dynamic title/description for archives and terms.
	if ( 'yes' === bp_s( $s, 'dynamic', '' ) ) {
		$dyn = bp_archive_header_data();
		$title   = $title ? $title : $dyn['title'];
		$text    = $text ? $text : $dyn['text'];
		$eyebrow = $eyebrow ? $eyebrow : $dyn['eyebrow'];
		if ( ! $bg && $dyn['image'] ) {
			$bg = $dyn['image'];
		}
		if ( empty( $crumbs ) && $dyn['crumbs'] ) {
			$crumbs = $dyn['crumbs'];
		}
	}
	?>
	<section class="bp-pagehead <?php echo esc_attr( $size ); ?><?php echo $bg ? ' has-image' : ''; ?>">
		<?php if ( $bg ) : ?><img class="bp-pagehead-bg" src="<?php echo esc_url( $bg ); ?>" alt="" /><div class="bp-pagehead-shade<?php echo $side ? ' strong' : ''; ?>"></div><?php endif; ?>
		<div class="bp-container bp-rel<?php echo $side ? ' bp-2col' : ''; ?>">
			<div>
				<?php if ( $crumbs ) : ?>
					<nav class="bp-crumbs inline" aria-label="<?php esc_attr_e( 'Breadcrumb', 'brickpoint' ); ?>"><div class="bp-container">
						<?php $n = count( $crumbs ); foreach ( array_values( $crumbs ) as $i => $c ) : $cl = bp_link( bp_s( $c, 'url', '' ) ); ?>
							<?php if ( $i > 0 ) { bp_the_icon( 'chevron-right', 'bp-icon xs' ); } ?>
							<?php if ( $i < $n - 1 && $cl['url'] ) : ?><a href="<?php echo esc_url( $cl['url'] ); ?>"><?php echo esc_html( bp_s( $c, 'text' ) ); ?></a><?php else : ?><span class="current"><?php echo esc_html( bp_s( $c, 'text' ) ); ?></span><?php endif; ?>
						<?php endforeach; ?>
					</div></nav>
				<?php endif; ?>
				<?php if ( $eyebrow ) : ?>
					<?php if ( 'badge' === bp_s( $s, 'eyebrow_style', '' ) ) : ?>
						<p class="bp-badge-outline amber"><?php bp_the_icon( bp_s( $s, 'eyebrow_icon', 'award' ), 'bp-icon sm' ); ?> <?php echo esc_html( $eyebrow ); ?></p>
					<?php else : ?>
						<p class="bp-eyebrow light"><?php echo esc_html( $eyebrow ); ?></p>
					<?php endif; ?>
				<?php endif; ?>
				<h1 class="bp-pagehead-title"><?php echo wp_kses_post( $title ); ?></h1>
				<?php if ( $text ) : ?><p class="bp-pagehead-text"><?php echo wp_kses_post( $text ); ?></p><?php endif; ?>
				<?php echo bp_render_buttons( bp_s( $s, 'buttons', array() ), bp_s( $s, 'button_size', '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php if ( $chips ) : ?>
					<div class="bp-chips"><?php foreach ( $chips as $c ) : ?><span class="bp-chip"><?php bp_the_icon( 'map-pin' ); ?> <?php echo esc_html( $c ); ?></span><?php endforeach; ?></div>
				<?php endif; ?>
				<?php if ( bp_s( $s, 'pills_taxonomy' ) ) : ?>
					<?php $tax = bp_s( $s, 'pills_taxonomy' ); $base = bp_archive_url( 'bp_video_category' === $tax ? 'bp_video' : ( 'bp_project_category' === $tax ? 'bp_project' : 'bp_product' ) ); $cur = is_tax( $tax ) ? get_queried_object()->slug : ( ! empty( $_GET['featured'] ) ? 'featured' : '' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>
					<?php echo bp_filter_pills( $tax, $base, $cur ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php endif; ?>
				<?php if ( 'yes' === bp_s( $s, 'search', '' ) ) : ?>
					<form class="bp-pagehead-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						<input type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search articles…', 'brickpoint' ); ?>" aria-label="<?php esc_attr_e( 'Search', 'brickpoint' ); ?>" />
						<input type="hidden" name="post_type" value="post" />
						<button class="bp-btn btn-brick" type="submit"><?php esc_html_e( 'Search', 'brickpoint' ); ?></button>
					</form>
				<?php endif; ?>
			</div>
			<?php if ( $side ) : ?>
				<div class="bp-reveal delay-1">
					<div class="bp-ss7-hero-img">
						<div class="bp-img-main"><img src="<?php echo esc_url( $side ); ?>" alt="<?php echo esc_attr( bp_s( $s, 'side_alt', __( 'SS7 brick close-up, premium red bricks', 'brickpoint' ) ) ); ?>" /></div>
						<?php if ( $tags ) : ?><div class="bp-ss7-tags"><?php foreach ( array_slice( $tags, 0, 3 ) as $t ) : ?><span><?php echo esc_html( $t ); ?></span><?php endforeach; ?></div><?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
}

/**
 * Data for dynamic archive headers.
 *
 * @return array{title:string,text:string,eyebrow:string,image:string,crumbs:array}
 */
function bp_archive_header_data() {
	$d = array( 'title' => '', 'text' => '', 'eyebrow' => '', 'image' => '', 'crumbs' => array() );
	if ( is_tax() || is_category() || is_tag() ) {
		$term = get_queried_object();
		$d['title'] = $term->name;
		$d['text']  = $term->description;
		$d['image'] = bp_term_image( $term->term_id, 'bp_cat_banner', 'bp-hero' );
		if ( ! $d['image'] ) {
			$d['image'] = bp_term_image( $term->term_id, 'bp_cat_image', 'bp-hero' );
		}
		$parent = array( 'text' => __( 'Categories', 'brickpoint' ), 'url' => 'page:categories' );
		if ( 'bp_video_category' === $term->taxonomy ) {
			$parent = array( 'text' => __( 'Videos', 'brickpoint' ), 'url' => 'archive:bp_video' );
		} elseif ( 'bp_project_category' === $term->taxonomy ) {
			$parent = array( 'text' => __( 'Projects', 'brickpoint' ), 'url' => 'archive:bp_project' );
		} elseif ( 'category' === $term->taxonomy || 'post_tag' === $term->taxonomy ) {
			$parent = array( 'text' => __( 'Blog', 'brickpoint' ), 'url' => 'page:blog' );
		}
		$d['crumbs'] = array( array( 'text' => __( 'Home', 'brickpoint' ), 'url' => 'home' ), $parent, array( 'text' => $term->name ) );
	} elseif ( is_post_type_archive() ) {
		$obj = get_queried_object();
		$d['title'] = $obj ? $obj->labels->name : post_type_archive_title( '', false );
	} elseif ( is_home() ) {
		$d['title'] = get_the_title( (int) get_option( 'page_for_posts' ) );
		$d['title'] = $d['title'] ? $d['title'] : __( 'Blog', 'brickpoint' );
	} elseif ( is_search() ) {
		/* translators: %s: search query */
		$d['title']   = sprintf( __( 'Results for “%s”', 'brickpoint' ), get_search_query() );
		$d['eyebrow'] = __( 'Search', 'brickpoint' );
	} elseif ( is_author() ) {
		$d['title'] = get_the_author();
		$d['eyebrow'] = __( 'Author', 'brickpoint' );
	} elseif ( is_archive() ) {
		$d['title'] = get_the_archive_title();
		$d['text']  = wp_strip_all_tags( get_the_archive_description() );
	}
	return $d;
}

/* ------------------------------------------------------------------------- */
/* ICON CARDS / NOTICE / VIDEO CONTENT / MATERIAL GROUPS                      */
/* ------------------------------------------------------------------------- */

/**
 * Icon cards grid.
 *
 * @param array $s Settings.
 */
function bp_section_icon_cards( $s ) {
	$items = bp_s( $s, 'items', array() );
	$style = bp_s( $s, 'style', 'white' ); // white | dark | white-3xl | dark-3xl | compact.
	$cols  = (int) bp_s( $s, 'columns', 3 );
	$cls   = 'bp-icon-card';
	if ( false !== strpos( $style, 'dark' ) ) {
		$cls .= ' dark';
	}
	if ( false !== strpos( $style, '3xl' ) ) {
		$cls .= ' r-3xl';
	}
	if ( 'compact' === $style ) {
		$cls .= ' compact';
	}
	$after = bp_s( $s, 'after_html', '' );
	?>
	<section class="<?php echo esc_attr( bp_s( $s, 'padding', 'bp-section-sm' ) ); ?> <?php echo esc_attr( bp_s( $s, 'bg', '' ) ); ?>">
		<div class="bp-container">
			<?php if ( bp_s( $s, 'title' ) ) : ?><div class="bp-reveal"><?php bp_section_head( bp_s( $s, 'eyebrow' ), bp_s( $s, 'title' ), bp_s( $s, 'text' ) ); ?></div><?php endif; ?>
			<div class="bp-grid cols-<?php echo (int) $cols; ?> gap-sm<?php echo bp_s( $s, 'title' ) ? ' bp-mt-sm' : ''; ?>" style="margin-top:<?php echo bp_s( $s, 'title' ) ? '2rem' : '0'; ?>">
				<?php foreach ( (array) $items as $i => $it ) : ?>
					<div class="<?php echo esc_attr( $cls ); ?> bp-reveal delay-<?php echo (int) ( $i % $cols ); ?>">
						<?php bp_the_icon( bp_s( $it, 'icon', 'check-circle' ), 'bp-icon ' . esc_attr( bp_s( $it, 'icon_color', '' ) ) ); ?>
						<h3><?php echo esc_html( bp_s( $it, 'title' ) ); ?></h3>
						<p><?php echo esc_html( bp_s( $it, 'text' ) ); ?></p>
					</div>
				<?php endforeach; ?>
			</div>
			<?php if ( $after ) : ?><div class="bp-notice bp-mt-sm bp-reveal"><?php echo wp_kses_post( $after ); ?></div><?php endif; ?>
		</div>
	</section>
	<?php
}

/**
 * Amber notice block.
 *
 * @param array $s Settings.
 */
function bp_section_notice( $s ) {
	?>
	<section class="<?php echo esc_attr( bp_s( $s, 'padding', 'bp-section-xs' ) ); ?>" style="padding-bottom:0">
		<div class="bp-container<?php echo bp_s( $s, 'narrow' ) ? ' narrow-3' : ''; ?>"><div class="bp-notice <?php echo esc_attr( bp_s( $s, 'style', 'xs' ) ); ?>"><?php echo wp_kses_post( bp_s( $s, 'text' ) ); ?></div></div>
	</section>
	<?php
}

/**
 * Text + checklist beside a video (SS7 "Watch up close" / About video).
 *
 * @param array $s Settings.
 */
function bp_section_video_content( $s ) {
	$video  = bp_s_video( $s, 'video' );
	$poster = bp_s_img( $s, 'poster', 'bp-hero' );
	$items  = bp_list( bp_s( $s, 'list', '' ) );
	$dark   = 'yes' === bp_s( $s, 'dark', 'yes' );
	$rev    = 'yes' === bp_s( $s, 'video_first', '' );
	$content = bp_s( $s, 'content', '' );
	?>
	<section class="bp-section-sm <?php echo $dark ? 'bp-charcoal' : ''; ?>">
		<div class="bp-container bp-2col">
			<div class="bp-reveal" <?php echo $rev ? 'style="order:2"' : ''; ?>>
				<?php bp_section_head( bp_s( $s, 'eyebrow' ), bp_s( $s, 'title' ), bp_s( $s, 'text' ), $dark, 'left' ); ?>
				<?php if ( $items ) : ?><ul class="bp-checklist plain"><?php foreach ( $items as $i ) : ?><li><?php bp_the_icon( 'check-circle' ); ?> <span><?php echo esc_html( $i ); ?></span></li><?php endforeach; ?></ul><?php endif; ?>
				<?php if ( $content ) : ?><div class="bp-mt-sm"><?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- nested render. ?></div><?php endif; ?>
				<?php echo bp_render_buttons( bp_s( $s, 'buttons', array() ), '', 'bp-btn-row mt-sm' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
			<div class="bp-reveal delay-1">
				<div class="bp-video-frame hero-video-frame<?php echo $dark ? '' : ' light'; ?>"><?php bp_video_player( $video, $poster, array( 'title' => wp_strip_all_tags( bp_s( $s, 'title' ) ) ) ); ?></div>
				<?php if ( bp_s( $s, 'caption' ) ) : ?><p class="bp-muted sm bp-center" style="margin-top:.75rem;font-size:.75rem"><?php echo esc_html( bp_s( $s, 'caption' ) ); ?></p><?php endif; ?>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Construction Materials page: category groups with 4 products each.
 *
 * @param array $s Settings.
 */
function bp_section_material_groups( $s ) {
	$groups = bp_s( $s, 'groups', array() );
	$per    = (int) bp_s( $s, 'per_group', 4 );
	foreach ( (array) $groups as $g ) :
		$slugs = array_map( 'trim', explode( ',', bp_s( $g, 'categories', '' ) ) );
		$slugs = array_filter( $slugs );
		if ( ! $slugs ) {
			continue;
		}
		$q = bp_query_products( array( 'category' => $slugs, 'posts_per_page' => $per, 'featured' => false ) );
		?>
		<section class="bp-section-xs bp-border-b">
			<div class="bp-container">
				<div class="bp-section-head-row">
					<div>
						<h2 class="bp-h2 sm"><?php echo esc_html( bp_s( $g, 'title' ) ); ?></h2>
						<div class="bp-pills light" style="margin-top:.75rem">
							<?php foreach ( $slugs as $slug ) : $t = get_term_by( 'slug', $slug, 'bp_product_category' ); if ( $t ) : ?>
								<a class="bp-pill" href="<?php echo esc_url( bp_category_url( $t ) ); ?>"><?php echo esc_html( $t->name ); ?></a>
							<?php endif; endforeach; ?>
						</div>
					</div>
					<a class="bp-link" href="<?php echo esc_url( bp_page_url( 'categories' ) ); ?>"><?php esc_html_e( 'All categories', 'brickpoint' ); ?> <?php bp_the_icon( 'arrow-right' ); ?></a>
				</div>
				<?php if ( $q->have_posts() ) : ?>
					<div class="bp-grid cols-4 bp-mt-sm">
						<?php $i = 0; while ( $q->have_posts() ) : $q->the_post(); get_template_part( 'template-parts/card', 'product', array( 'reveal' => true, 'index' => $i++ ) ); endwhile; wp_reset_postdata(); ?>
					</div>
				<?php endif; ?>
			</div>
		</section>
		<?php
	endforeach;
}

/* ------------------------------------------------------------------------- */
/* CONTACT                                                                    */
/* ------------------------------------------------------------------------- */

/**
 * Contact layout: direct contact card + map card + quotation form.
 *
 * @param array $s Settings.
 */
function bp_section_contact( $s ) {
	?>
	<section class="bp-section-xs">
		<div class="bp-container bp-contact-layout">
			<div>
				<div class="bp-contact-card">
					<h2><?php echo esc_html( bp_s( $s, 'card_title', __( 'Direct Contact', 'brickpoint' ) ) ); ?></h2>
					<div class="bp-contact-lines">
						<p><?php bp_the_icon( 'phone' ); ?> <a href="tel:+<?php echo esc_attr( bp_phone_intl() ); ?>"><?php echo esc_html( bp_phone_display() ); ?></a></p>
						<p><?php bp_the_icon( 'mail' ); ?> <a href="mailto:<?php echo esc_attr( bp_email() ); ?>"><?php echo esc_html( bp_email() ); ?></a></p>
						<p><?php bp_the_icon( 'map-pin' ); ?> <span><?php echo esc_html( bp_address() ); ?></span></p>
						<p class="soft"><?php esc_html_e( 'CEO:', 'brickpoint' ); ?> <?php echo esc_html( bp_get( 'bp_ceo' ) ); ?><br /><?php esc_html_e( 'Sales Manager:', 'brickpoint' ); ?> <?php echo esc_html( bp_get( 'bp_sales' ) ); ?></p>
					</div>
					<?php echo bp_whatsapp_button( bp_default_whatsapp_url(), __( 'Chat on WhatsApp', 'brickpoint' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<div class="bp-social-grid">
						<?php foreach ( array( 'facebook' => 'Facebook', 'instagram' => 'Instagram', 'twitter' => 'X / Twitter', 'tiktok' => 'TikTok' ) as $k => $label ) : if ( bp_social( $k ) ) : ?>
							<a href="<?php echo esc_url( bp_social( $k ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $label ); ?></a>
						<?php endif; endforeach; ?>
					</div>
				</div>
				<div class="bp-map-card">
					<h3><?php echo esc_html( bp_s( $s, 'map_title', __( 'Office Map', 'brickpoint' ) ) ); ?></h3>
					<p><?php echo esc_html( bp_s( $s, 'map_text', __( 'Open the verified office location in Google Maps.', 'brickpoint' ) ) ); ?></p>
					<a class="bp-btn btn-dark" target="_blank" rel="noopener" href="<?php echo esc_url( bp_s( $s, 'map_url', bp_get( 'bp_office_maps' ) ) ); ?>"><?php bp_the_icon( 'map-pin' ); ?> <?php esc_html_e( 'Open Google Maps', 'brickpoint' ); ?></a>
					<a class="bp-btn btn-outline" href="<?php echo esc_url( bp_archive_url( 'bp_location' ) ); ?>"><?php esc_html_e( 'All Locations', 'brickpoint' ); ?></a>
				</div>
			</div>
			<div>
				<?php echo bp_contact_form_shortcode( array( 'title' => bp_s( $s, 'form_title', __( 'Request a Quotation', 'brickpoint' ) ), 'intro' => bp_s( $s, 'form_intro', __( 'Fields: name, phone, email, company, required material, quantity, location, message.', 'brickpoint' ) ), 'button' => bp_s( $s, 'form_button', __( 'Send Quotation Request', 'brickpoint' ) ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	</section>
	<?php
}

/* ------------------------------------------------------------------------- */
/* LEGAL / PROSE PAGE                                                         */
/* ------------------------------------------------------------------------- */

/**
 * Narrow prose page (Privacy / Terms).
 *
 * @param array $s Settings: eyebrow, title, content (HTML).
 */
function bp_section_prose( $s ) {
	?>
	<section class="bp-page-content">
		<div class="bp-container narrow-3">
			<?php if ( bp_s( $s, 'eyebrow' ) ) : ?><p class="bp-eyebrow"><?php echo esc_html( bp_s( $s, 'eyebrow' ) ); ?></p><?php endif; ?>
			<?php if ( bp_s( $s, 'title' ) ) : ?><h1 class="bp-h1"><?php echo esc_html( bp_s( $s, 'title' ) ); ?></h1><?php endif; ?>
			<div class="bp-prose-card lg prose-bp"><?php echo wp_kses_post( bp_s( $s, 'content' ) ); ?></div>
		</div>
	</section>
	<?php
}
