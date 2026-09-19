<?php
/**
 * Elementor template generator.
 *
 * Builds Elementor documents (pages, Theme Builder header/footer/single/archive/404,
 * card + CTA library templates) from the same content blueprints the PHP
 * fallback uses, using BrickPoint widgets. The generated documents are exported
 * as Elementor JSON (elementor/templates/*.json) and imported by the demo importer.
 *
 * Placeholders inside JSON (resolved by the importer at import time):
 *  - media url "demo:key"     → Media Library attachment (id + url)
 *  - text "demo:videokey"     → original video URL
 *  - link "page:slug", "archive:cpt", "home", "whatsapp", "wa:…" → resolved by bp_link() at render,
 *    and rewritten to real permalinks on import so they are editable in Elementor.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Template generator.
 */
class BrickPoint_Elementor_Templates {

	/**
	 * Random Elementor element id.
	 *
	 * @return string
	 */
	public static function el_id() {
		return substr( md5( wp_generate_uuid4() ), 0, 7 );
	}

	/**
	 * Section settings (content-defaults format) → Elementor widget settings.
	 *
	 * @param string $widget   Widget id.
	 * @param array  $settings Section settings.
	 * @return array
	 */
	public static function widget_settings( $widget, $settings ) {
		$all    = bp_widget_schema() + bp_template_widget_schema();
		$schema = isset( $all[ $widget ] ) ? $all[ $widget ] : array( 'controls' => array() );
		return self::convert( isset( $schema['controls'] ) ? $schema['controls'] : array(), $settings );
	}

	/**
	 * Convert settings per control schema.
	 *
	 * @param array $controls Controls.
	 * @param array $settings Values.
	 * @return array
	 */
	protected static function convert( $controls, $settings ) {
		$out = array();
		foreach ( $settings as $key => $v ) {
			if ( ! isset( $controls[ $key ] ) ) {
				continue;
			}
			$c = $controls[ $key ];
			switch ( $c['type'] ) {
				case 'media':
					$out[ $key ] = is_array( $v ) ? $v : array( 'url' => (string) $v, 'id' => is_numeric( $v ) ? (int) $v : 0 );
					break;
				case 'url':
					$out[ $key ] = is_array( $v ) ? $v : array( 'url' => (string) $v, 'is_external' => ( 0 === strpos( (string) $v, 'wa:' ) || 'whatsapp' === $v ) ? 'on' : '', 'nofollow' => '' );
					break;
				case 'icon':
					$out[ $key ] = is_array( $v ) ? $v : ( $v ? array( 'value' => 'bpi bpi-' . $v, 'library' => 'brickpoint' ) : array() );
					break;
				case 'switch':
					$out[ $key ] = ( 'yes' === $v || true === $v ) ? 'yes' : '';
					break;
				case 'select':
					$out[ $key ] = (string) $v;
					break;
				case 'number':
					$out[ $key ] = (int) $v;
					break;
				case 'repeater':
					$rows = array();
					foreach ( (array) $v as $row ) {
						$r        = self::convert( $c['fields'], (array) $row );
						$r['_id'] = self::el_id();
						$rows[]   = $r;
					}
					$out[ $key ] = $rows;
					break;
				default:
					$out[ $key ] = is_array( $v ) ? wp_json_encode( $v ) : (string) $v;
			}
		}
		return $out;
	}

	/**
	 * Widget element.
	 *
	 * @param string $widget   Widget id.
	 * @param array  $settings Section settings.
	 * @return array
	 */
	public static function widget( $widget, $settings = array() ) {
		return array(
			'id'         => self::el_id(),
			'elType'     => 'widget',
			'widgetType' => $widget,
			'settings'   => self::widget_settings( $widget, $settings ),
			'elements'   => array(),
		);
	}

	/**
	 * Full-width container wrapping widgets.
	 *
	 * @param array $children Elements.
	 * @param array $extra    Extra settings.
	 * @return array
	 */
	public static function container( $children, $extra = array() ) {
		$zero = array( 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true );
		return array(
			'id'       => self::el_id(),
			'elType'   => 'container',
			'settings' => array_merge(
				array(
					'content_width'  => 'full',
					'flex_direction' => 'column',
					'flex_gap'       => array( 'unit' => 'px', 'size' => 0, 'column' => '0', 'row' => '0', 'isLinked' => true ),
					'padding'        => $zero,
					'margin'         => $zero,
				),
				$extra
			),
			'elements' => $children,
			'isInner'  => false,
		);
	}

	/**
	 * Map of section type → widget id.
	 *
	 * @return array
	 */
	public static function section_widget_map() {
		return array(
			'hero' => 'bp_hero', 'image_content' => 'bp_image_content', 'category_grid' => 'bp_category_grid', 'ss7_feature' => 'bp_ss7_feature',
			'product_grid' => 'bp_product_grid', 'video_showcase' => 'bp_video_showcase', 'video_grid' => 'bp_video_grid', 'project_grid' => 'bp_project_grid',
			'location_grid' => 'bp_location_grid', 'blog_grid' => 'bp_blog_grid', 'audience' => 'bp_audience', 'team' => 'bp_team', 'cta_band' => 'bp_cta_band',
			'cta_box' => 'bp_cta_box', 'page_header' => 'bp_page_header', 'icon_cards' => 'bp_icon_cards', 'notice' => 'bp_notice', 'video_content' => 'bp_video_content',
			'material_groups' => 'bp_material_groups', 'contact' => 'bp_contact', 'prose' => 'bp_prose',
		);
	}

	/**
	 * Page content from a section blueprint.
	 *
	 * @param array $sections Sections.
	 * @return array
	 */
	public static function content_from_sections( $sections ) {
		$map = self::section_widget_map();
		$out = array();
		foreach ( $sections as $sec ) {
			list( $type, $settings ) = $sec;
			if ( ! isset( $map[ $type ] ) ) {
				continue;
			}
			$out[] = self::container( array( self::widget( $map[ $type ], $settings ) ) );
		}
		return $out;
	}

	/**
	 * All templates.
	 *
	 * @return array<string,array{title:string,type:string,content:array,conditions?:array,option?:string,page_settings?:array}>
	 */
	public static function all() {
		$t     = array();
		$pages = bp_demo_pages();
		$ah    = bp_demo_archive_headers();

		// Theme builder.
		$t['header'] = array( 'title' => 'BrickPoint Header', 'type' => 'header', 'conditions' => array( 'include/general' ), 'option' => 'brickpoint_tpl_header', 'content' => array( self::container( array( self::widget( 'bp_site_header' ) ) ) ) );
		$t['footer'] = array( 'title' => 'BrickPoint Footer', 'type' => 'footer', 'conditions' => array( 'include/general' ), 'option' => 'brickpoint_tpl_footer', 'content' => array( self::container( array( self::widget( 'bp_site_footer' ) ) ) ) );

		$singles = array(
			'single-product'  => array( 'BrickPoint Single Product', 'bp_product_details', 'include/singular/bp_product' ),
			'single-video'    => array( 'BrickPoint Single Video', 'bp_video_details', 'include/singular/bp_video' ),
			'single-project'  => array( 'BrickPoint Single Project', 'bp_project_details', 'include/singular/bp_project' ),
			'single-location' => array( 'BrickPoint Single Location', 'bp_location_details', 'include/singular/bp_location' ),
			'single-post'     => array( 'BrickPoint Single Post', 'bp_post_details', 'include/singular/post' ),
		);
		foreach ( $singles as $k => $d ) {
			$t[ $k ] = array( 'title' => $d[0], 'type' => 'single', 'conditions' => array( $d[2] ), 'option' => 'brickpoint_tpl_' . str_replace( '-', '_', $k ), 'content' => array( self::container( array( self::widget( $d[1] ) ) ) ) );
		}

		$archives = array(
			'archive-product'  => array( 'BrickPoint Products Archive', array( 'include/archive/bp_product_archive', 'include/archive/bp_product_category' ) ),
			'archive-video'    => array( 'BrickPoint Videos Archive', array( 'include/archive/bp_video_archive', 'include/archive/bp_video_category' ) ),
			'archive-project'  => array( 'BrickPoint Projects Archive', array( 'include/archive/bp_project_archive', 'include/archive/bp_project_category' ) ),
			'archive-location' => array( 'BrickPoint Locations Archive', array( 'include/archive/bp_location_archive' ) ),
		);
		foreach ( $archives as $k => $d ) {
			$t[ $k ] = array( 'title' => $d[0], 'type' => 'archive', 'conditions' => $d[1], 'option' => 'brickpoint_tpl_' . str_replace( '-', '_', $k ), 'content' => array( self::container( array( self::widget( 'bp_archive_grid' ) ) ) ) );
		}
		$t['archive-blog'] = array( 'title' => 'BrickPoint Blog Archive', 'type' => 'archive', 'conditions' => array( 'include/archive/recent_posts', 'include/archive/category', 'include/archive/post_tag', 'include/archive/search' ), 'option' => 'brickpoint_tpl_archive_blog', 'content' => array( self::container( array( self::widget( 'bp_blog_archive' ) ) ) ) );

		$t['error-404'] = array(
			'title'      => 'BrickPoint 404',
			'type'       => 'error-404',
			'conditions' => array( 'include/singular/not_found404' ),
			'option'     => 'brickpoint_tpl_404',
			'content'    => self::content_from_sections(
				array(
					array( 'page_header', array( 'eyebrow' => 'Page not found', 'title' => '404', 'text' => 'The page you are looking for has moved or does not exist. Try a search or head back to the catalogue.', 'search' => 'yes', 'buttons' => array( array( 'text' => 'Back to Home', 'url' => 'home', 'style' => 'brick' ), array( 'text' => 'Browse Products', 'url' => 'archive:bp_product', 'style' => 'ghost' ) ) ) ),
					array( 'product_grid', array( 'title' => 'Popular products', 'head_layout' => 'row', 'featured' => 'yes', 'count' => 4, 'columns' => 4, 'plain' => 'yes', 'button_text' => 'View all', 'button_url' => 'archive:bp_product' ) ),
				)
			),
		);

		// Pages.
		foreach ( $pages as $slug => $p ) {
			if ( empty( $p['sections'] ) ) {
				continue;
			}
			$t[ 'page-' . $slug ] = array( 'title' => $p['title'], 'type' => 'page', 'page_slug' => $slug, 'content' => self::content_from_sections( $p['sections'] ) );
		}
		// Archive pages that are CPT archives get "page" docs too (Products/Projects/Videos/Locations menu entries are archives, no page needed).

		// Library: cards + CTA + reusable sections.
		$library = array(
			'card-product'  => array( 'BrickPoint Card — Product', 'bp_product_card' ),
			'card-video'    => array( 'BrickPoint Card — Video', 'bp_video_card' ),
			'card-project'  => array( 'BrickPoint Card — Project', 'bp_project_card' ),
			'card-location' => array( 'BrickPoint Card — Location', 'bp_location_card' ),
			'card-blog'     => array( 'BrickPoint Card — Blog', 'bp_blog_card' ),
			'whatsapp-button' => array( 'BrickPoint — WhatsApp Button', 'bp_whatsapp_button' ),
			'quotation-form' => array( 'BrickPoint — Quotation Form', 'bp_contact_form' ),
		);
		foreach ( $library as $k => $d ) {
			$t[ 'lib-' . $k ] = array( 'title' => $d[0], 'type' => 'section', 'content' => array( self::container( array( self::widget( $d[1] ) ) ) ) );
		}
		$home = $pages['home']['sections'];
		foreach ( $home as $sec ) {
			if ( 'cta_band' === $sec[0] ) {
				$t['lib-cta-band'] = array( 'title' => 'BrickPoint — CTA Band (Send your material list)', 'type' => 'section', 'content' => self::content_from_sections( array( $sec ) ) );
			}
			if ( 'ss7_feature' === $sec[0] ) {
				$t['lib-ss7-feature'] = array( 'title' => 'BrickPoint — SS7 Feature', 'type' => 'section', 'content' => self::content_from_sections( array( $sec ) ) );
			}
		}
		$t['lib-cta-box'] = array( 'title' => 'BrickPoint — CTA Box (Quote)', 'type' => 'section', 'content' => self::content_from_sections( array( $pages['ss7-bricks']['sections'][4] ) ) );

		return $t;
	}

	/**
	 * Elementor library JSON structure for one template.
	 *
	 * @param string $key Key.
	 * @param array  $tpl Template def.
	 * @return array
	 */
	public static function to_json( $key, $tpl ) {
		return array(
			'title'         => $tpl['title'],
			'type'          => $tpl['type'],
			'version'       => '0.4',
			'brickpoint'    => array(
				'key'        => $key,
				'conditions' => isset( $tpl['conditions'] ) ? $tpl['conditions'] : array(),
				'option'     => isset( $tpl['option'] ) ? $tpl['option'] : '',
				'page_slug'  => isset( $tpl['page_slug'] ) ? $tpl['page_slug'] : '',
			),
			'page_settings' => isset( $tpl['page_settings'] ) ? $tpl['page_settings'] : ( 'page' === $tpl['type'] ? array( 'template' => 'elementor_header_footer' ) : array() ),
			'content'       => $tpl['content'],
		);
	}

	/**
	 * Write all templates to elementor/templates/*.json (used by the build and
	 * regenerated on import if missing).
	 *
	 * @param string $dir Directory.
	 * @return string[] Written files.
	 */
	public static function export_all( $dir ) {
		if ( ! is_dir( $dir ) ) {
			wp_mkdir_p( $dir );
		}
		$files = array();
		foreach ( self::all() as $key => $tpl ) {
			$file = trailingslashit( $dir ) . $key . '.json';
			file_put_contents( $file, wp_json_encode( self::to_json( $key, $tpl ), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
			$files[] = $file;
		}
		return $files;
	}

	/**
	 * Load a template from JSON (falls back to generating it).
	 *
	 * @param string $key Key.
	 * @return array|null
	 */
	public static function load( $key ) {
		$file = BRICKPOINT_DIR . '/elementor/templates/' . $key . '.json';
		if ( file_exists( $file ) ) {
			$data = json_decode( file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			if ( is_array( $data ) && isset( $data['content'] ) ) {
				return $data;
			}
		}
		$all = self::all();
		return isset( $all[ $key ] ) ? self::to_json( $key, $all[ $key ] ) : null;
	}

	/**
	 * Keys of all templates (from JSON dir or generator).
	 *
	 * @return string[]
	 */
	public static function keys() {
		$files = glob( BRICKPOINT_DIR . '/elementor/templates/*.json' );
		if ( $files ) {
			return array_map( function ( $f ) { return basename( $f, '.json' ); }, $files );
		}
		return array_keys( self::all() );
	}

	/**
	 * Resolve placeholders recursively.
	 *
	 * @param mixed    $data     Data.
	 * @param callable $media_cb fn(key,size) → [id,url].
	 * @return mixed
	 */
	public static function resolve( $data, $media_cb ) {
		if ( is_array( $data ) ) {
			// Media array.
			if ( isset( $data['url'] ) && is_string( $data['url'] ) && array_key_exists( 'id', $data ) && 0 === strpos( $data['url'], 'demo:' ) ) {
				$r = call_user_func( $media_cb, substr( $data['url'], 5 ) );
				$data['id']  = (int) $r[0];
				$data['url'] = $r[1];
				return $data;
			}
			// Link array.
			if ( isset( $data['url'] ) && is_string( $data['url'] ) && array_key_exists( 'is_external', $data ) ) {
				$l = bp_link( $data['url'] );
				if ( $l['url'] && 0 !== strpos( $data['url'], 'wa:' ) ) {
					$data['url'] = $l['url'];
				} elseif ( 0 === strpos( $data['url'], 'wa:' ) ) {
					$data['url'] = $l['url'];
				}
				if ( $l['blank'] ) {
					$data['is_external'] = 'on';
				}
				return $data;
			}
			foreach ( $data as $k => $v ) {
				$data[ $k ] = self::resolve( $v, $media_cb );
			}
			return $data;
		}
		if ( is_string( $data ) && 0 === strpos( $data, 'demo:' ) ) {
			$key = substr( $data, 5 );
			$vid = bp_demo_video_url( $key );
			if ( $vid ) {
				return $vid;
			}
			$r = call_user_func( $media_cb, $key );
			return $r[1] ? $r[1] : $data;
		}
		return $data;
	}
}
