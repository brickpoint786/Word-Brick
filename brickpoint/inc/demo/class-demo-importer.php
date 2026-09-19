<?php
/**
 * BrickPoint one-click demo importer.
 *
 * Step-based (AJAX) and idempotent: every imported object carries a
 * `_brickpoint_demo_id` marker (posts/terms/attachments/templates) so re-running
 * updates in place and never duplicates.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Importer.
 */
class BrickPoint_Demo_Importer {

	const MARK = '_brickpoint_demo_id';

	/**
	 * Steps in order: id => label.
	 *
	 * @return array<string,string>
	 */
	public static function steps() {
		return array(
			'prepare'    => __( 'Preparing & checking requirements', 'brickpoint' ),
			'settings'   => __( 'Theme options (WhatsApp, contact, social)', 'brickpoint' ),
			'media'      => __( 'Downloading images to Media Library', 'brickpoint' ),
			'taxonomies' => __( 'Product, video & project categories', 'brickpoint' ),
			'products'   => __( 'Products (SKU, prices, specs, WhatsApp)', 'brickpoint' ),
			'videos'     => __( 'Videos', 'brickpoint' ),
			'projects'   => __( 'Project references', 'brickpoint' ),
			'locations'  => __( 'Locations (bhattas & office)', 'brickpoint' ),
			'posts'      => __( 'Blog articles', 'brickpoint' ),
			'pages'      => __( 'Pages', 'brickpoint' ),
			'elementor'  => __( 'Elementor templates (header, footer, singles, archives, cards)', 'brickpoint' ),
			'menus'      => __( 'Menus (Primary, Footer, Mobile)', 'brickpoint' ),
			'finalize'   => __( 'Homepage, permalinks & cleanup', 'brickpoint' ),
		);
	}

	/**
	 * Success links.
	 *
	 * @return array<string,string>
	 */
	public static function links() {
		$home   = (int) get_option( 'page_on_front' );
		$header = (int) get_option( 'brickpoint_tpl_header' );
		$footer = (int) get_option( 'brickpoint_tpl_footer' );
		$el     = bp_has_elementor();
		$edit   = function ( $id ) use ( $el ) {
			if ( ! $id ) {
				return admin_url( 'edit.php?post_type=page' );
			}
			return $el ? admin_url( 'post.php?post=' . $id . '&action=elementor' ) : get_edit_post_link( $id, 'raw' );
		};
		return array(
			'view'      => home_url( '/' ),
			'home'      => $edit( $home ),
			'header'    => $header ? $edit( $header ) : admin_url( 'edit.php?post_type=elementor_library&tabs_group=theme' ),
			'footer'    => $footer ? $edit( $footer ) : admin_url( 'edit.php?post_type=elementor_library&tabs_group=theme' ),
			'elementor' => $el ? admin_url( 'edit.php?post_type=elementor_library&tabs_group=theme' ) : admin_url( 'plugin-install.php?s=elementor&tab=search&type=term' ),
			'customize' => admin_url( 'customize.php?autofocus[panel]=brickpoint' ),
		);
	}

	/**
	 * Boot.
	 */
	public static function init() {
		add_action( 'wp_ajax_brickpoint_demo_step', array( __CLASS__, 'ajax_step' ) );
	}

	/**
	 * AJAX step runner.
	 */
	public static function ajax_step() {
		check_ajax_referer( 'brickpoint_demo_import', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Not allowed.', 'brickpoint' ) ) );
		}
		$step   = isset( $_POST['step'] ) ? sanitize_key( wp_unslash( $_POST['step'] ) ) : '';
		$offset = isset( $_POST['offset'] ) ? absint( $_POST['offset'] ) : 0;
		if ( ! isset( self::steps()[ $step ] ) ) {
			wp_send_json_error( array( 'message' => __( 'Unknown step.', 'brickpoint' ) ) );
		}
		if ( function_exists( 'set_time_limit' ) ) {
			@set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		}
		wp_raise_memory_limit( 'admin' );
		try {
			$result = call_user_func( array( __CLASS__, 'step_' . $step ), $offset );
		} catch ( \Throwable $e ) {
			wp_send_json_error( array( 'message' => $e->getMessage() ) );
		}
		wp_send_json_success( $result );
	}

	/**
	 * Run every step synchronously (WP-CLI / programmatic).
	 *
	 * @return array<string,string> Step messages.
	 */
	public static function run_all() {
		$out = array();
		foreach ( array_keys( self::steps() ) as $step ) {
			$offset = 0;
			do {
				$r = call_user_func( array( __CLASS__, 'step_' . $step ), $offset );
				$offset = isset( $r['offset'] ) ? (int) $r['offset'] : 0;
			} while ( ! empty( $r['more'] ) );
			$out[ $step ] = isset( $r['message'] ) ? $r['message'] : 'ok';
		}
		return $out;
	}

	/* ---------------------------------------------------------------------- */
	/* Helpers                                                                 */
	/* ---------------------------------------------------------------------- */

	/**
	 * Find a post by demo marker.
	 *
	 * @param string $type    Post type.
	 * @param string $demo_id Marker.
	 * @return int
	 */
	protected static function find_post( $type, $demo_id ) {
		$q = get_posts(
			array(
				'post_type'      => $type,
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::MARK, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => $demo_id, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);
		return $q ? (int) $q[0] : 0;
	}

	/**
	 * Create or update a post.
	 *
	 * @param string $type    Post type.
	 * @param string $demo_id Marker (unique).
	 * @param array  $data    wp_insert_post data.
	 * @param array  $meta    Meta.
	 * @return int Post ID.
	 */
	protected static function upsert_post( $type, $demo_id, $data, $meta = array() ) {
		$id = self::find_post( $type, $demo_id );
		if ( ! $id && ! empty( $data['post_name'] ) ) {
			// Adopt an existing post with the same slug (e.g. created by the user) instead of duplicating.
			$existing = get_page_by_path( $data['post_name'], OBJECT, $type );
			if ( $existing ) {
				$id = (int) $existing->ID;
			}
		}
		$data['post_type']   = $type;
		$data['post_status'] = isset( $data['post_status'] ) ? $data['post_status'] : 'publish';
		if ( $id ) {
			$data['ID'] = $id;
			// Never overwrite content the site owner edited in Elementor/pages: only refresh empty fields.
			$current = get_post( $id );
			if ( $current && bp_is_elementor_post( $id ) && isset( $data['post_content'] ) ) {
				unset( $data['post_content'] );
			}
			$id = wp_update_post( wp_slash( $data ), true );
		} else {
			$id = wp_insert_post( wp_slash( $data ), true );
		}
		if ( is_wp_error( $id ) ) {
			throw new \RuntimeException( $id->get_error_message() );
		}
		update_post_meta( $id, self::MARK, $demo_id );
		foreach ( $meta as $k => $v ) {
			if ( null === $v ) {
				continue;
			}
			update_post_meta( $id, $k, $v );
		}
		return (int) $id;
	}

	/**
	 * Attachment ID for a demo image key (imports lazily if the media step was skipped).
	 *
	 * @param string $key Key.
	 * @return int
	 */
	public static function media_id( $key ) {
		$map = get_option( 'brickpoint_demo_media', array() );
		if ( ! empty( $map[ $key ] ) && get_post( (int) $map[ $key ] ) ) {
			return (int) $map[ $key ];
		}
		return self::import_image( $key );
	}

	/**
	 * [id,url] for JSON placeholder resolution.
	 *
	 * @param string $key Key.
	 * @return array{0:int,1:string}
	 */
	public static function media_pair( $key ) {
		$id = self::media_id( $key );
		if ( $id ) {
			$u = wp_get_attachment_url( $id );
			return array( $id, $u ? $u : bp_demo_image_url( $key ) );
		}
		return array( 0, bp_demo_image_url( $key ) );
	}

	/**
	 * Download one source image into the Media Library (idempotent).
	 *
	 * @param string $key Key.
	 * @return int Attachment ID (0 when download not possible; source URL is used instead).
	 */
	public static function import_image( $key ) {
		$imgs = bp_demo_images();
		if ( ! isset( $imgs[ $key ] ) ) {
			return 0;
		}
		$existing = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => self::MARK, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'img:' . $key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);
		if ( $existing ) {
			self::remember_media( $key, (int) $existing[0] );
			return (int) $existing[0];
		}
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$src = $imgs[ $key ]['url'];
		$tmp = download_url( $src, 60 );
		if ( is_wp_error( $tmp ) ) {
			// Bundled fallback copy (theme ships assets/images/demo/<key>.jpg when available).
			$local = BRICKPOINT_DIR . '/assets/images/demo/' . $key . '.jpg';
			if ( ! file_exists( $local ) ) {
				return 0;
			}
			$tmp = wp_tempnam( $key . '.jpg' );
			copy( $local, $tmp ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_copy
		}
		$file = array(
			'name'     => sanitize_file_name( 'brickpoint-' . strtolower( preg_replace( '/([a-z])([A-Z])/', '$1-$2', $key ) ) . '.jpg' ),
			'tmp_name' => $tmp,
		);
		$id = media_handle_sideload( $file, 0, $imgs[ $key ]['title'] );
		if ( is_wp_error( $id ) ) {
			@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.unlink_unlink
			return 0;
		}
		update_post_meta( $id, self::MARK, 'img:' . $key );
		update_post_meta( $id, '_wp_attachment_image_alt', $imgs[ $key ]['alt'] );
		update_post_meta( $id, '_brickpoint_source_url', $src );
		self::remember_media( $key, (int) $id );
		return (int) $id;
	}

	/**
	 * Store key → attachment map.
	 *
	 * @param string $key Key.
	 * @param int    $id  ID.
	 */
	protected static function remember_media( $key, $id ) {
		$map         = get_option( 'brickpoint_demo_media', array() );
		$map[ $key ] = $id;
		update_option( 'brickpoint_demo_media', $map, false );
	}

	/**
	 * Term upsert.
	 *
	 * @param string $tax  Taxonomy.
	 * @param array  $data name/slug/description/order/image/banner/icon/video/whatsapp.
	 * @return int term_id
	 */
	protected static function upsert_term( $tax, $data ) {
		$term = get_term_by( 'slug', $data['slug'], $tax );
		$args = array( 'slug' => $data['slug'], 'description' => isset( $data['description'] ) ? $data['description'] : '' );
		if ( $term ) {
			$r = wp_update_term( $term->term_id, $tax, array_merge( $args, array( 'name' => $data['name'] ) ) );
		} else {
			$r = wp_insert_term( $data['name'], $tax, $args );
		}
		if ( is_wp_error( $r ) ) {
			throw new \RuntimeException( $r->get_error_message() );
		}
		$id = (int) $r['term_id'];
		update_term_meta( $id, self::MARK, $tax . ':' . $data['slug'] );
		if ( isset( $data['order'] ) ) {
			update_term_meta( $id, 'bp_cat_order', (int) $data['order'] );
		}
		if ( 'bp_product_category' === $tax ) {
			if ( ! empty( $data['image'] ) ) {
				$mid = self::media_id( $data['image'] );
				update_term_meta( $id, 'bp_cat_image', $mid ? $mid : bp_demo_image_url( $data['image'] ) );
				update_term_meta( $id, 'bp_cat_banner', $mid ? $mid : bp_demo_image_url( $data['image'] ) );
			}
			if ( ! empty( $data['icon'] ) ) {
				update_term_meta( $id, 'bp_cat_icon', $data['icon'] );
			}
			if ( ! empty( $data['video'] ) ) {
				update_term_meta( $id, 'bp_cat_video', bp_demo_video_url( $data['video'] ) );
			}
		}
		return $id;
	}

	/**
	 * Gallery CSV from image keys.
	 *
	 * @param array $keys Keys.
	 * @return string
	 */
	protected static function gallery_csv( $keys ) {
		$ids = array();
		foreach ( (array) $keys as $k ) {
			$id = self::media_id( $k );
			if ( $id ) {
				$ids[] = $id;
			} else {
				$ids[] = bp_demo_image_url( $k );
			}
		}
		return implode( ',', $ids );
	}

	/**
	 * Set featured image by key.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     Image key.
	 */
	protected static function set_thumb( $post_id, $key ) {
		$id = self::media_id( $key );
		if ( $id ) {
			set_post_thumbnail( $post_id, $id );
			update_post_meta( $post_id, '_brickpoint_image_url', '' );
		} else {
			// No local copy possible: keep the exact source URL for the card renderers.
			update_post_meta( $post_id, '_brickpoint_image_url', bp_demo_image_url( $key ) );
		}
	}

	/* ---------------------------------------------------------------------- */
	/* Steps                                                                   */
	/* ---------------------------------------------------------------------- */

	/** Prepare. */
	public static function step_prepare( $offset = 0 ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			throw new \RuntimeException( __( 'Insufficient permissions.', 'brickpoint' ) );
		}
		update_option( 'brickpoint_demo_started', time(), false );
		$notes = array();
		$notes[] = bp_has_elementor() ? __( 'Elementor detected', 'brickpoint' ) : __( 'Elementor not active (PHP templates will render the design)', 'brickpoint' );
		$notes[] = bp_has_elementor_pro() ? __( 'Elementor Pro detected', 'brickpoint' ) : __( 'Elementor Pro not active (Theme Builder JSON still imported)', 'brickpoint' );
		flush_rewrite_rules( false );
		return array( 'message' => implode( ' • ', $notes ) );
	}

	/** Theme options. */
	public static function step_settings( $offset = 0 ) {
		foreach ( bp_defaults() as $k => $v ) {
			if ( '' === get_theme_mod( $k, '' ) ) {
				set_theme_mod( $k, $v );
			}
		}
		set_theme_mod( 'bp_float_wa', true );
		set_theme_mod( 'bp_contact_to', get_option( 'admin_email' ) );
		set_theme_mod( 'bp_hero_video', bp_demo_video_url( 'hero' ) );
		set_theme_mod( 'bp_hero_poster', bp_demo_image_url( 'heroPoster' ) );
		update_option( 'blogdescription', 'Bricks & Construction Materials — Lahore' );
		if ( '' === get_option( 'blogname' ) || 'My WordPress Website' === get_option( 'blogname' ) || false !== stripos( get_option( 'blogname' ), 'playground' ) ) {
			update_option( 'blogname', 'BrickPoint' );
		}
		update_option( 'thumbnail_size_w', 400 );
		update_option( 'thumbnail_size_h', 300 );
		update_option( 'thumbnail_crop', 1 );
		if ( bp_has_elementor() ) {
			brickpoint_elementor_defaults();
			self::elementor_kit();
		}
		return array( 'message' => __( 'WhatsApp 0315 2850818, contact & social links set', 'brickpoint' ) );
	}

	/** Media (batched, 4 per request). */
	public static function step_media( $offset = 0 ) {
		$keys  = array_keys( bp_demo_images() );
		$batch = array_slice( $keys, $offset, 4 );
		$ok    = 0;
		foreach ( $batch as $k ) {
			if ( self::media_id( $k ) ) {
				$ok++;
			}
		}
		$next = $offset + count( $batch );
		$map  = get_option( 'brickpoint_demo_media', array() );
		if ( $next < count( $keys ) ) {
			return array(
				'more'    => true,
				'offset'  => $next,
				/* translators: 1: done, 2: total */
				'message' => sprintf( __( '%1$d / %2$d images', 'brickpoint' ), $next, count( $keys ) ),
			);
		}
		$local = count( array_filter( $map ) );
		if ( $local < count( $keys ) ) {
			/* translators: 1: local count, 2: total */
			return array( 'message' => sprintf( __( '%1$d of %2$d images stored locally; the rest use their original source URLs', 'brickpoint' ), $local, count( $keys ) ) );
		}
		/* translators: %d: count */
		return array( 'message' => sprintf( __( '%d images in Media Library (videos stay on source URLs)', 'brickpoint' ), $local ) );
	}

	/** Taxonomies. */
	public static function step_taxonomies( $offset = 0 ) {
		$n = 0;
		foreach ( bp_demo_product_categories() as $c ) {
			self::upsert_term( 'bp_product_category', $c );
			$n++;
		}
		foreach ( bp_demo_video_categories() as $c ) {
			self::upsert_term( 'bp_video_category', $c );
			$n++;
		}
		foreach ( bp_demo_project_categories() as $c ) {
			self::upsert_term( 'bp_project_category', $c );
			$n++;
		}
		// Blog categories.
		foreach ( bp_demo_posts() as $p ) {
			if ( ! term_exists( $p['category'], 'category' ) ) {
				wp_insert_term( $p['category'], 'category' );
			}
		}
		/* translators: %d: count */
		return array( 'message' => sprintf( __( '%d categories (15 product, 11 video, 3 project)', 'brickpoint' ), $n ) );
	}

	/** Products. */
	public static function step_products( $offset = 0 ) {
		$n = 0;
		foreach ( bp_demo_products() as $p ) {
			$id = self::upsert_post(
				'bp_product',
				'product:' . $p['slug'],
				array(
					'post_title'   => $p['title'],
					'post_name'    => $p['slug'],
					'post_content' => $p['content'],
					'post_excerpt' => $p['short'],
					'menu_order'   => $p['order'],
				),
				array(
					'_bp_short'        => $p['short'],
					'_bp_price'        => $p['price'],
					'_bp_price_label'  => isset( $p['price_label'] ) ? $p['price_label'] : '',
					'_bp_unit'         => $p['unit'],
					'_bp_availability' => $p['availability'],
					'_bp_badge'        => isset( $p['badge'] ) ? $p['badge'] : '',
					'_bp_sku'          => $p['sku'],
					'_bp_specs'        => isset( $p['specs'] ) ? $p['specs'] : '',
					'_bp_features'     => isset( $p['features'] ) ? $p['features'] : '',
					'_bp_video'        => ! empty( $p['video'] ) ? bp_demo_video_url( $p['video'] ) : '',
					'_bp_featured'     => (string) (int) $p['featured'],
					'_bp_gallery'      => ! empty( $p['gallery'] ) ? self::gallery_csv( $p['gallery'] ) : '',
					'_bp_cta_text'     => '',
					'_bp_cta_link'     => '',
					'_bp_brochure'     => '',
				)
			);
			wp_set_object_terms( $id, $p['category'], 'bp_product_category' );
			self::set_thumb( $id, $p['image'] );
			$n++;
		}
		/* translators: %d: count */
		return array( 'message' => sprintf( __( '%d products with WhatsApp ordering', 'brickpoint' ), $n ) );
	}

	/** Videos. */
	public static function step_videos( $offset = 0 ) {
		$n = 0;
		foreach ( bp_demo_video_posts() as $v ) {
			$id = self::upsert_post(
				'bp_video',
				'video:' . $v['slug'],
				array( 'post_title' => $v['title'], 'post_name' => $v['slug'], 'post_content' => $v['excerpt'], 'post_excerpt' => $v['excerpt'], 'menu_order' => $v['order'] ),
				array(
					'_bpv_url'      => bp_demo_video_url( $v['video'] ),
					'_bpv_source'   => $v['source'],
					'_bpv_duration' => $v['duration'],
					'_bpv_featured' => (string) (int) $v['featured'],
					'_bpv_button_text' => '',
					'_bpv_button_link' => '',
				)
			);
			wp_set_object_terms( $id, $v['category'], 'bp_video_category' );
			self::set_thumb( $id, $v['thumb'] );
			$n++;
		}
		/* translators: %d: count */
		return array( 'message' => sprintf( __( '%d videos (thumbnails local, video files remote)', 'brickpoint' ), $n ) );
	}

	/** Projects. */
	public static function step_projects( $offset = 0 ) {
		$n = 0;
		foreach ( bp_demo_projects() as $p ) {
			$id = self::upsert_post(
				'bp_project',
				'project:' . $p['slug'],
				array( 'post_title' => $p['title'], 'post_name' => $p['slug'], 'post_content' => $p['excerpt'], 'post_excerpt' => $p['excerpt'], 'menu_order' => $p['order'] ),
				array(
					'_bpp_location'     => $p['location'],
					'_bpp_status'       => $p['status'],
					'_bpp_featured'     => (string) (int) $p['featured'],
					'_bpp_illustrative' => '1',
				)
			);
			wp_set_object_terms( $id, $p['category'], 'bp_project_category' );
			self::set_thumb( $id, $p['image'] );
			$n++;
		}
		/* translators: %d: count */
		return array( 'message' => sprintf( __( '%d illustrative project references', 'brickpoint' ), $n ) );
	}

	/** Locations. */
	public static function step_locations( $offset = 0 ) {
		$n = 0;
		foreach ( bp_demo_locations() as $l ) {
			$id = self::upsert_post(
				'bp_location',
				'location:' . $l['slug'],
				array( 'post_title' => $l['title'], 'post_name' => $l['slug'], 'post_content' => $l['content'], 'post_excerpt' => $l['content'], 'menu_order' => $l['order'] ),
				array(
					'_bpl_address' => $l['address'],
					'_bpl_maps'    => $l['maps'],
					'_bpl_phone'   => $l['phone'],
					'_bpl_hours'   => $l['hours'],
					'_bpl_badge'   => $l['badge'],
					'_bpl_lat'     => isset( $l['lat'] ) ? $l['lat'] : '',
					'_bpl_lng'     => isset( $l['lng'] ) ? $l['lng'] : '',
				)
			);
			self::set_thumb( $id, $l['image'] );
			$n++;
		}
		/* translators: %d: count */
		return array( 'message' => sprintf( __( '%d locations with Google Maps links', 'brickpoint' ), $n ) );
	}

	/** Blog posts. */
	public static function step_posts( $offset = 0 ) {
		$n = 0;
		foreach ( bp_demo_posts() as $i => $p ) {
			$id = self::upsert_post(
				'post',
				'post:' . $p['slug'],
				array(
					'post_title'   => $p['title'],
					'post_name'    => $p['slug'],
					'post_content' => '<!-- wp:paragraph --><p>' . $p['content'] . '</p><!-- /wp:paragraph -->',
					'post_excerpt' => $p['excerpt'],
					'post_date'    => gmdate( 'Y-m-d H:i:s', strtotime( '-' . ( $i * 9 + 3 ) . ' days' ) ),
				)
			);
			$cat = get_term_by( 'name', $p['category'], 'category' );
			if ( $cat ) {
				wp_set_object_terms( $id, array( (int) $cat->term_id ), 'category' );
			}
			wp_set_object_terms( $id, $p['tags'], 'post_tag' );
			self::set_thumb( $id, $p['image'] );
			$n++;
		}
		// Remove the default "Hello world!" only if untouched.
		$hello = get_page_by_path( 'hello-world', OBJECT, 'post' );
		if ( $hello && false !== strpos( $hello->post_content, 'Welcome to WordPress' ) ) {
			wp_trash_post( $hello->ID );
		}
		/* translators: %d: count */
		return array( 'message' => sprintf( __( '%d articles', 'brickpoint' ), $n ) );
	}

	/** Pages. */
	public static function step_pages( $offset = 0 ) {
		$n = 0;
		foreach ( bp_demo_pages() as $slug => $p ) {
			$content = '';
			if ( in_array( $slug, array( 'privacy-policy', 'terms-and-conditions' ), true ) ) {
				$content = bp_demo_legal_html( 'privacy-policy' === $slug ? 'privacy' : 'terms' );
			}
			$id = self::upsert_post(
				'page',
				'page:' . $slug,
				array( 'post_title' => $p['title'], 'post_name' => $slug, 'post_content' => $content, 'menu_order' => $n ),
				array( '_brickpoint_page' => $slug )
			);
			if ( ! empty( $p['sections'] ) ) {
				update_post_meta( $id, '_wp_page_template', 'default' );
			}
			$n++;
		}
		// "Sample Page" cleanup (untouched default only).
		$sample = get_page_by_path( 'sample-page' );
		if ( $sample && false !== strpos( $sample->post_content, 'This is an example page' ) ) {
			wp_trash_post( $sample->ID );
		}
		$privacy = get_page_by_path( 'privacy-policy' );
		if ( $privacy ) {
			update_option( 'wp_page_for_privacy_policy', $privacy->ID );
		}
		/* translators: %d: count */
		return array( 'message' => sprintf( __( '%d pages', 'brickpoint' ), $n ) );
	}

	/** Elementor templates (batched). */
	public static function step_elementor( $offset = 0 ) {
		if ( ! bp_has_elementor() ) {
			return array( 'message' => __( 'Elementor not active — skipped (pages render with the theme templates; run again after installing Elementor)', 'brickpoint' ) );
		}
		$keys  = BrickPoint_Elementor_Templates::keys();
		$batch = array_slice( $keys, $offset, 4 );
		foreach ( $batch as $key ) {
			self::import_template( $key );
		}
		$next = $offset + count( $batch );
		if ( $next < count( $keys ) ) {
			/* translators: 1: done, 2: total */
			return array( 'more' => true, 'offset' => $next, 'message' => sprintf( __( '%1$d / %2$d templates', 'brickpoint' ), $next, count( $keys ) ) );
		}
		self::regenerate_elementor();
		/* translators: %d: count */
		return array( 'message' => sprintf( __( '%d Elementor documents (header, footer, singles, archives, 404, pages, cards)', 'brickpoint' ), count( $keys ) ) );
	}

	/**
	 * Import one template JSON as an Elementor document.
	 *
	 * @param string $key Key.
	 * @return int Post ID.
	 */
	public static function import_template( $key ) {
		$tpl = BrickPoint_Elementor_Templates::load( $key );
		if ( ! $tpl ) {
			return 0;
		}
		$content = BrickPoint_Elementor_Templates::resolve( $tpl['content'], array( __CLASS__, 'media_pair' ) );
		$meta    = isset( $tpl['brickpoint'] ) ? $tpl['brickpoint'] : array();
		$type    = $tpl['type'];

		if ( 'page' === $type ) {
			$slug = ! empty( $meta['page_slug'] ) ? $meta['page_slug'] : $key;
			$page = get_page_by_path( $slug );
			if ( ! $page ) {
				$id = self::upsert_post( 'page', 'page:' . $slug, array( 'post_title' => $tpl['title'], 'post_name' => $slug ) );
			} else {
				$id = (int) $page->ID;
			}
			// Don't clobber a page the owner already customised in Elementor.
			if ( bp_is_elementor_post( $id ) && get_post_meta( $id, '_brickpoint_tpl_hash', true ) !== md5( wp_json_encode( $tpl['content'] ) ) && get_post_meta( $id, '_brickpoint_tpl_hash', true ) ) {
				$owner_edited = (int) get_post_meta( $id, '_elementor_edit_count', true ) > 0;
				if ( $owner_edited ) {
					return $id;
				}
			}
			self::write_elementor_document( $id, $content, 'wp-page', isset( $tpl['page_settings'] ) ? $tpl['page_settings'] : array( 'template' => 'elementor_header_footer' ) );
			update_post_meta( $id, '_brickpoint_tpl_hash', md5( wp_json_encode( $tpl['content'] ) ) );
			update_post_meta( $id, '_wp_page_template', 'elementor_header_footer' );
			return $id;
		}

		// Library / theme builder document.
		$id = self::upsert_post(
			'elementor_library',
			'tpl:' . $key,
			array( 'post_title' => $tpl['title'], 'post_name' => sanitize_title( $tpl['title'] ) ),
			array( '_elementor_template_type' => $type )
		);
		wp_set_object_terms( $id, $type, 'elementor_library_type' );
		if ( ! empty( $meta['conditions'] ) ) {
			wp_set_object_terms( $id, 'theme', 'elementor_library_category' );
		}
		self::write_elementor_document( $id, $content, $type, isset( $tpl['page_settings'] ) ? $tpl['page_settings'] : array() );
		if ( ! empty( $meta['conditions'] ) ) {
			bp_set_elementor_conditions( $id, $meta['conditions'] );
		}
		if ( ! empty( $meta['option'] ) ) {
			update_option( $meta['option'], $id, false );
		}
		return $id;
	}

	/**
	 * Write Elementor data/meta to a post.
	 *
	 * @param int    $id            Post ID.
	 * @param array  $content       Elements.
	 * @param string $doc_type      Document type.
	 * @param array  $page_settings Page settings.
	 */
	protected static function write_elementor_document( $id, $content, $doc_type, $page_settings = array() ) {
		$json = wp_json_encode( $content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
		update_post_meta( $id, '_elementor_data', wp_slash( $json ) );
		update_post_meta( $id, '_elementor_edit_mode', 'builder' );
		update_post_meta( $id, '_elementor_template_type', $doc_type );
		update_post_meta( $id, '_elementor_version', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '3.0.0' );
		if ( $page_settings ) {
			update_post_meta( $id, '_elementor_page_settings', $page_settings );
		}
		delete_post_meta( $id, '_elementor_css' );
		if ( class_exists( '\Elementor\Plugin' ) ) {
			try {
				$doc = \Elementor\Plugin::instance()->documents->get( $id, false );
				if ( $doc ) {
					$doc->save( array( 'elements' => $content, 'settings' => $page_settings ) );
				}
			} catch ( \Throwable $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
				// Meta already written; Elementor will regenerate CSS on first render.
			}
		}
		// Elementor Free has no header/footer/single/archive document classes; its save()
		// falls back to a generic type. Re-assert the intended Theme Builder type so
		// Elementor Pro picks the templates up (with conditions) as soon as it is active.
		if ( 'wp-page' !== $doc_type ) {
			update_post_meta( $id, '_elementor_template_type', $doc_type );
			if ( taxonomy_exists( 'elementor_library_type' ) ) {
				wp_set_object_terms( $id, $doc_type, 'elementor_library_type' );
			}
		}
	}

	/**
	 * Elementor kit: global colours + fonts matching the design.
	 */
	protected static function elementor_kit() {
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return;
		}
		try {
			$kit = \Elementor\Plugin::instance()->kits_manager->get_active_kit_for_frontend();
			if ( ! $kit || ! $kit->get_id() ) {
				return;
			}
			$colors = array(
				array( '_id' => 'primary', 'title' => 'Brick', 'color' => '#c2410c' ),
				array( '_id' => 'secondary', 'title' => 'Ink', 'color' => '#141210' ),
				array( '_id' => 'text', 'title' => 'Text', 'color' => '#1c1a17' ),
				array( '_id' => 'accent', 'title' => 'Orange', 'color' => '#ea580c' ),
			);
			$custom = array(
				array( '_id' => 'bpsand', 'title' => 'Sand', 'color' => '#f6f1ea' ),
				array( '_id' => 'bpcream', 'title' => 'Cream', 'color' => '#faf8f5' ),
				array( '_id' => 'bpamber', 'title' => 'Amber', 'color' => '#d9a441' ),
				array( '_id' => 'bpmuted', 'title' => 'Muted', 'color' => '#6b6560' ),
				array( '_id' => 'bpwa', 'title' => 'WhatsApp', 'color' => '#128c4b' ),
			);
			$typo = array(
				array( '_id' => 'primary', 'title' => 'Headings', 'typography_typography' => 'custom', 'typography_font_family' => 'Archivo', 'typography_font_weight' => '800' ),
				array( '_id' => 'secondary', 'title' => 'Sub-headings', 'typography_typography' => 'custom', 'typography_font_family' => 'Archivo', 'typography_font_weight' => '700' ),
				array( '_id' => 'text', 'title' => 'Body', 'typography_typography' => 'custom', 'typography_font_family' => 'Inter', 'typography_font_weight' => '400' ),
				array( '_id' => 'accent', 'title' => 'Eyebrow', 'typography_typography' => 'custom', 'typography_font_family' => 'Inter', 'typography_font_weight' => '700', 'typography_text_transform' => 'uppercase' ),
			);
			$kit->update_settings(
				array(
					'system_colors'     => $colors,
					'custom_colors'     => $custom,
					'system_typography' => $typo,
					'container_width'   => array( 'unit' => 'px', 'size' => 1200 ),
					'container_padding' => array( 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true ),
					'space_between_widgets' => array( 'unit' => 'px', 'size' => 0, 'column' => '0', 'row' => '0', 'isLinked' => true ),
					'body_typography_typography' => 'custom',
					'body_typography_font_family' => 'Inter',
					'body_color'        => '#1c1a17',
					'link_normal_color' => '#c2410c',
					'h1_typography_typography' => 'custom',
					'h1_typography_font_family' => 'Archivo',
					'h1_typography_font_weight' => '800',
					'h2_typography_typography' => 'custom',
					'h2_typography_font_family' => 'Archivo',
					'h2_typography_font_weight' => '800',
					'h3_typography_typography' => 'custom',
					'h3_typography_font_family' => 'Archivo',
					'h3_typography_font_weight' => '700',
					'button_border_radius' => array( 'unit' => 'px', 'top' => '999', 'right' => '999', 'bottom' => '999', 'left' => '999', 'isLinked' => true ),
				)
			);
		} catch ( \Throwable $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// Kit optional.
		}
	}

	/**
	 * Regenerate Elementor CSS + Pro conditions cache.
	 */
	protected static function regenerate_elementor() {
		if ( ! class_exists( '\Elementor\Plugin' ) ) {
			return;
		}
		try {
			\Elementor\Plugin::instance()->files_manager->clear_cache();
		} catch ( \Throwable $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// Ignore.
		}
		if ( class_exists( '\ElementorPro\Modules\ThemeBuilder\Module' ) ) {
			try {
				\ElementorPro\Modules\ThemeBuilder\Module::instance()->get_conditions_manager()->get_cache()->regenerate();
			} catch ( \Throwable $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
				// Ignore.
			}
		}
	}

	/** Menus. */
	public static function step_menus( $offset = 0 ) {
		$locations = get_theme_mod( 'nav_menu_locations', array() );
		foreach ( bp_demo_menus() as $loc => $menu ) {
			$obj = wp_get_nav_menu_object( $menu['name'] );
			if ( ! $obj ) {
				$menu_id = wp_create_nav_menu( $menu['name'] );
				if ( is_wp_error( $menu_id ) ) {
					throw new \RuntimeException( $menu_id->get_error_message() );
				}
			} else {
				$menu_id = (int) $obj->term_id;
				// Rebuild items (idempotent: menu itself reused, items refreshed).
				foreach ( wp_get_nav_menu_items( $menu_id, array( 'post_status' => 'any' ) ) ?: array() as $item ) {
					wp_delete_post( $item->ID, true );
				}
			}
			self::add_menu_items( $menu_id, $menu['items'], 0 );
			$locations[ $loc ] = $menu_id;
		}
		set_theme_mod( 'nav_menu_locations', $locations );
		return array( 'message' => __( 'Primary, Footer and Mobile menus assigned', 'brickpoint' ) );
	}

	/**
	 * Add menu items recursively.
	 *
	 * @param int   $menu_id Menu ID.
	 * @param array $items   Items.
	 * @param int   $parent  Parent item ID.
	 */
	protected static function add_menu_items( $menu_id, $items, $parent ) {
		foreach ( $items as $it ) {
			$args = array(
				'menu-item-title'     => $it['title'],
				'menu-item-status'    => 'publish',
				'menu-item-parent-id' => $parent,
				'menu-item-classes'   => isset( $it['classes'] ) ? $it['classes'] : '',
			);
			$url = $it['url'];
			if ( 0 === strpos( $url, 'page:' ) && ( $page = get_page_by_path( substr( $url, 5 ) ) ) ) { // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.FoundInControlStructure
				$args += array( 'menu-item-type' => 'post_type', 'menu-item-object' => 'page', 'menu-item-object-id' => $page->ID );
			} elseif ( 0 === strpos( $url, 'archive:' ) ) {
				$args += array( 'menu-item-type' => 'post_type_archive', 'menu-item-object' => substr( $url, 8 ) );
			} elseif ( 'home' === $url ) {
				$front = (int) get_option( 'page_on_front' );
				$front = $front ? $front : ( ( $p = get_page_by_path( 'home' ) ) ? $p->ID : 0 ); // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.FoundInControlStructure
				if ( $front ) {
					$args += array( 'menu-item-type' => 'post_type', 'menu-item-object' => 'page', 'menu-item-object-id' => $front );
				} else {
					$args += array( 'menu-item-type' => 'custom', 'menu-item-url' => home_url( '/' ) );
				}
			} else {
				$args += array( 'menu-item-type' => 'custom', 'menu-item-url' => bp_link( $url )['url'] );
			}
			$id = wp_update_nav_menu_item( $menu_id, 0, $args );
			if ( ! is_wp_error( $id ) && ! empty( $it['children'] ) ) {
				self::add_menu_items( $menu_id, $it['children'], (int) $id );
			}
		}
	}

	/** Finalize. */
	public static function step_finalize( $offset = 0 ) {
		$home = get_page_by_path( 'home' );
		$blog = get_page_by_path( 'blog' );
		update_option( 'show_on_front', 'page' );
		if ( $home ) {
			update_option( 'page_on_front', $home->ID );
		}
		if ( $blog ) {
			update_option( 'page_for_posts', $blog->ID );
		}
		update_option( 'posts_per_page', 9 );
		$structure = (string) get_option( 'permalink_structure' );
		if ( '' === $structure || false !== strpos( $structure, '%year%' ) ) {
			global $wp_rewrite;
			$wp_rewrite->set_permalink_structure( '/%postname%/' );
			$wp_rewrite->init();
		}
		// Re-point "Home" menu items to the front page (created before page_on_front was set).
		self::step_menus();
		flush_rewrite_rules( false );
		self::regenerate_elementor();
		update_option( 'brickpoint_demo_imported', time(), false );
		delete_option( 'brickpoint_activation_redirect' );
		return array(
			'message' => __( 'Homepage set to “Home”, blog page set to “Blog”, permalinks flushed', 'brickpoint' ),
			'links'   => self::links(),
		);
	}
}
BrickPoint_Demo_Importer::init();

/**
 * WP-CLI: `wp brickpoint import-demo`.
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command(
		'brickpoint import-demo',
		function () {
			foreach ( BrickPoint_Demo_Importer::run_all() as $step => $msg ) {
				WP_CLI::log( $step . ': ' . $msg );
			}
			WP_CLI::success( 'BrickPoint demo imported.' );
		}
	);
}
