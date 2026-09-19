<?php
/**
 * BrickPoint content source.
 *
 * All original site content (media, page sections, catalogue) lives here.
 * - The demo importer reads it to create posts, terms, media and Elementor templates.
 * - The PHP fallback templates read the section defaults so the front end is complete
 *   even before Elementor is installed.
 *
 * Media keys ("demo:redStack") resolve to Media Library attachments after import
 * (option brickpoint_demo_media), otherwise to the original source URL.
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Source images (original site asset URLs). Downloaded into the Media Library on import.
 *
 * @return array<string,array{url:string,title:string,alt:string}>
 */
function bp_demo_images() {
	$base = 'https://images.pexels.com/photos/';
	$q    = '?auto=compress&cs=tinysrgb&fit=crop&h=627&w=1200';
	$list = array(
		'brickMason' => array( '19688828/pexels-photo-19688828.jpeg', 'Bricklayers building a wall' ),
		'bricklayer' => array( '33603170/pexels-photo-33603170.jpeg', 'Bricklayer at work' ),
		'wall'       => array( '8961703/pexels-photo-8961703.jpeg', 'Brick wall construction' ),
		'site'       => array( '38221540/pexels-photo-38221540.jpeg', 'Construction site materials' ),
		'cables'     => array( '8961695/pexels-photo-8961695.jpeg', 'Electrical cables and conduit' ),
		'align'      => array( '33194812/pexels-photo-33194812.jpeg', 'Aligning bricks with sand mortar' ),
		'kiln'       => array( '35460203/pexels-photo-35460203.jpeg', 'Brick kiln production' ),
		'kilnWomen'  => array( '8280818/pexels-photo-8280818.jpeg', 'Brick kiln workers' ),
		'pile'       => array( '15325569/pexels-photo-15325569.jpeg', 'Pile of bricks and aggregate' ),
		'stacked'    => array( '33160441/pexels-photo-33160441.jpeg', 'Stacked burnt-clay bricks' ),
		'redStack'   => array( '34713630/pexels-photo-34713630.jpeg', 'SS7 red bricks stacked' ),
		'worker'     => array( '34752732/pexels-photo-34752732.jpeg', 'Brick worker' ),
		'villa1'     => array( '7031412/pexels-photo-7031412.jpeg', 'Villa construction reference' ),
		'villa2'     => array( '8134847/pexels-photo-8134847.jpeg', 'Housing construction reference' ),
		'villa3'     => array( '18894990/pexels-photo-18894990.jpeg', 'Block development reference' ),
		'apt'        => array( '33414231/pexels-photo-33414231.jpeg', 'Apartment development reference' ),
	);
	$out = array();
	foreach ( $list as $k => $d ) {
		$out[ $k ] = array( 'url' => $base . $d[0] . $q, 'title' => $d[1], 'alt' => $d[1] );
	}
	// Video posters (thumbnails stored locally, videos stay remote).
	$vbase = 'https://images.pexels.com/videos/';
	$vq    = '?auto=compress&cs=tinysrgb&fit=crop&h=630&w=1200';
	$out['heroPoster']   = array( 'url' => $vbase . '35411576/pexels-photo-35411576.jpeg' . $vq, 'title' => 'SS7 Bricks in action — video poster', 'alt' => 'SS7 Bricks in action' );
	$out['sitePoster']   = array( 'url' => $vbase . '11355903/pexels-photo-11355903.jpeg' . $vq, 'title' => 'Site work — video poster', 'alt' => 'Brick site work' );
	$out['dronePoster']  = array( 'url' => $vbase . '27758012/aerial-bricks-clip-construction-27758012.jpeg' . $vq, 'title' => 'Aerial bricks — video poster', 'alt' => 'Aerial view of brick construction' );
	$out['aerialPoster'] = array( 'url' => $vbase . '20731372/aerial-build-building-buildings-20731372.jpeg' . $vq, 'title' => 'Aerial buildings — video poster', 'alt' => 'Aerial view of buildings' );
	return $out;
}

/**
 * Source videos (kept as remote URLs, as in the original site).
 *
 * @return array<string,string>
 */
function bp_demo_videos() {
	return array(
		'hero'   => 'https://videos.pexels.com/video-files/35411576/15003649_3840_2160_24fps.mp4',
		'site'   => 'https://videos.pexels.com/video-files/11355903/11355903-uhd_3840_2160_25fps.mp4',
		'drone'  => 'https://videos.pexels.com/video-files/27758012/12218981_3840_2160_25fps.mp4',
		'aerial' => 'https://videos.pexels.com/video-files/20731372/20731372-uhd_3840_2160_30fps.mp4',
	);
}

/**
 * Attachment ID for an imported demo image key (0 if not imported).
 *
 * @param string $key Key.
 * @return int
 */
function bp_demo_image_id( $key ) {
	$map = get_option( 'brickpoint_demo_media', array() );
	if ( ! empty( $map[ $key ] ) && 'attachment' === get_post_type( (int) $map[ $key ] ) ) {
		return (int) $map[ $key ];
	}
	return 0;
}

/**
 * URL for a demo image key: local attachment when imported, else source URL.
 *
 * @param string $key  Key.
 * @param string $size Size.
 * @return string
 */
function bp_demo_image_url( $key, $size = 'large' ) {
	$id = bp_demo_image_id( $key );
	if ( $id ) {
		$u = wp_get_attachment_image_url( $id, $size );
		if ( $u ) {
			return $u;
		}
	}
	$imgs = bp_demo_images();
	return isset( $imgs[ $key ] ) ? $imgs[ $key ]['url'] : '';
}

/**
 * Video URL by key.
 *
 * @param string $key Key.
 * @return string
 */
function bp_demo_video_url( $key ) {
	$v = bp_demo_videos();
	return isset( $v[ $key ] ) ? $v[ $key ] : '';
}

/**
 * Product categories.
 *
 * @return array<int,array<string,mixed>>
 */
function bp_demo_product_categories() {
	return array(
		array( 'name' => 'Bricks', 'slug' => 'bricks', 'description' => 'Premium burnt-clay bricks for strong, durable masonry.', 'image' => 'stacked', 'icon' => 'layers', 'order' => 1 ),
		array( 'name' => 'SS7 Bricks', 'slug' => 'ss7-bricks', 'description' => 'Our flagship high-strength SS7 brick range.', 'image' => 'redStack', 'icon' => 'award', 'order' => 2, 'video' => 'hero' ),
		array( 'name' => 'Cement', 'slug' => 'cement', 'description' => 'Fresh, quality cement for every structural need.', 'image' => 'site', 'icon' => 'package', 'order' => 3 ),
		array( 'name' => 'Bajri / Crush', 'slug' => 'bajri-crush', 'description' => 'Graded crush and bajri for concrete and foundations.', 'image' => 'pile', 'icon' => 'mountain', 'order' => 4 ),
		array( 'name' => 'Sand / Rait', 'slug' => 'sand-rait', 'description' => 'Clean sand for masonry, plaster and concrete.', 'image' => 'align', 'icon' => 'waves', 'order' => 5 ),
		array( 'name' => 'Steel', 'slug' => 'steel', 'description' => 'Structural steel for reinforcement and framing.', 'image' => 'site', 'icon' => 'anchor', 'order' => 6 ),
		array( 'name' => 'Electric Conduit Pipes', 'slug' => 'electric-conduit-pipes', 'description' => 'Durable conduit pipes for safe electrical work.', 'image' => 'cables', 'icon' => 'zap', 'order' => 7 ),
		array( 'name' => 'Plumbing Pipes and Fittings', 'slug' => 'plumbing-pipes', 'description' => 'Reliable plumbing pipes and fittings.', 'image' => 'wall', 'icon' => 'droplets', 'order' => 8 ),
		array( 'name' => 'Construction Chemicals', 'slug' => 'construction-chemicals', 'description' => 'Waterproofing, bonding and repair chemicals.', 'image' => 'brickMason', 'icon' => 'flask', 'order' => 9 ),
		array( 'name' => 'Insulation and Membrane', 'slug' => 'insulation-membrane', 'description' => 'Heat and waterproofing insulation solutions.', 'image' => 'villa1', 'icon' => 'shield', 'order' => 10 ),
		array( 'name' => 'Cables and Wires', 'slug' => 'cables-wires', 'description' => 'Quality cables and wires for safe wiring.', 'image' => 'cables', 'icon' => 'plug', 'order' => 11 ),
		array( 'name' => 'Paints', 'slug' => 'paints', 'description' => 'Interior and exterior paints with lasting finish.', 'image' => 'villa2', 'icon' => 'paintbrush', 'order' => 12 ),
		array( 'name' => 'Lights', 'slug' => 'lights', 'description' => 'Modern lighting for homes and projects.', 'image' => 'villa3', 'icon' => 'lightbulb', 'order' => 13 ),
		array( 'name' => 'Switches and Sockets', 'slug' => 'switches-sockets', 'description' => 'Safe, premium switches and sockets.', 'image' => 'cables', 'icon' => 'toggle', 'order' => 14 ),
		array( 'name' => 'Other Construction Materials', 'slug' => 'other-materials', 'description' => 'Everything else your project needs.', 'image' => 'bricklayer', 'icon' => 'boxes', 'order' => 15 ),
	);
}

/**
 * Video categories.
 *
 * @return array<int,array<string,mixed>>
 */
function bp_demo_video_categories() {
	$names = array(
		'ss7-bricks' => 'SS7 Bricks', 'brick-manufacturing' => 'Brick Manufacturing', 'our-bhattas' => 'Our Bhattas', 'brick-quality' => 'Brick Quality',
		'construction-projects' => 'Construction Projects', 'construction-materials' => 'Construction Materials', 'company-brand' => 'Company / Brand',
		'promotional' => 'Promotional Videos', 'product-videos' => 'Product Videos', 'behind-the-scenes' => 'Behind the Scenes', 'other-videos' => 'Other Videos',
	);
	$out = array();
	$i   = 1;
	foreach ( $names as $slug => $name ) {
		$out[] = array( 'name' => $name, 'slug' => $slug, 'order' => $i++ );
	}
	return $out;
}

/**
 * Project categories.
 *
 * @return array<int,array<string,mixed>>
 */
function bp_demo_project_categories() {
	return array(
		array( 'name' => 'Residential', 'slug' => 'residential', 'order' => 1 ),
		array( 'name' => 'Commercial', 'slug' => 'commercial', 'order' => 2 ),
		array( 'name' => 'Development', 'slug' => 'development', 'order' => 3 ),
	);
}

/**
 * Products.
 *
 * @return array<int,array<string,mixed>>
 */
function bp_demo_products() {
	$rate = 'Rs. Contact for Rate';
	return array(
		array(
			'title' => 'SS7 Premium Bricks', 'slug' => 'ss7-premium-bricks', 'category' => 'ss7-bricks', 'image' => 'redStack', 'gallery' => array( 'stacked', 'pile', 'worker' ),
			'short' => 'Flagship SS7 burnt-clay bricks — high strength, sharp edges, consistent firing.',
			'content' => "SS7 Premium Bricks are BrickPoint's flagship range. Manufactured at our trusted bhatta units with controlled firing for strength and dimensional consistency. Ideal for homes, commercial buildings and boundary walls. Contact us on WhatsApp for current pricing, availability and delivery scheduling.",
			'price' => $rate, 'price_label' => 'Market-competitive bulk pricing', 'unit' => '1000 bricks', 'availability' => 'In Stock', 'badge' => 'Best Seller',
			'specs' => "Type: Burnt-clay SS7\nUsage: Residential, commercial, boundary walls\nAvailability: Bulk & retail\nDelivery Area: Confirm on WhatsApp",
			'features' => "High crushing strength\nUniform size & sharp edges\nConsistent kiln firing\nBulk order support",
			'sku' => 'BP-SS7-001', 'featured' => 1, 'video' => 'hero', 'order' => 1,
		),
		array(
			'title' => 'Awwal Burnt-Clay Bricks', 'slug' => 'awwal-bricks', 'category' => 'bricks', 'image' => 'stacked', 'gallery' => array( 'pile', 'redStack' ),
			'short' => 'Quality awwal-grade bricks for reliable masonry work.',
			'content' => 'Awwal-grade burnt-clay bricks suitable for load-bearing and partition walls. Sorted for quality with dependable supply for projects of any size.',
			'price' => $rate, 'price_label' => 'Grade-wise pricing available', 'unit' => '1000 bricks', 'availability' => 'In Stock', 'badge' => 'Popular',
			'specs' => "Grade: Awwal\nUsage: Walls, partitions, structures", 'features' => "Grade-sorted batches\nReliable supply", 'sku' => 'BP-BR-002', 'featured' => 1, 'order' => 2,
		),
		array(
			'title' => 'Portland Cement (OPC)', 'slug' => 'portland-cement-opc', 'category' => 'cement', 'image' => 'site',
			'short' => 'Fresh stock cement for concrete, masonry and plaster.',
			'content' => 'Quality Ordinary Portland Cement sourced fresh for strength and workability. Available for retail and bulk project supply.',
			'price' => $rate, 'price_label' => 'Brand options on request', 'unit' => 'bag', 'availability' => 'In Stock',
			'specs' => "Type: OPC\nPacking: Standard bag", 'features' => "Fresh stock\nBulk rates", 'sku' => 'BP-CM-003', 'featured' => 1, 'order' => 3,
		),
		array(
			'title' => 'Crush / Bajri (Graded)', 'slug' => 'crush-bajri-graded', 'category' => 'bajri-crush', 'image' => 'pile',
			'short' => 'Graded crush for foundations, concrete and road base.',
			'content' => 'Machine-crushed graded bajri in multiple sizes for RCC, PCC and foundation work. Trolley and bulk supply available.',
			'price' => $rate, 'price_label' => 'Per trolley / per brass options', 'unit' => 'trolley', 'availability' => 'In Stock',
			'specs' => 'Sizes: Confirm on quote', 'features' => "Graded sizes\nClean material", 'sku' => 'BP-CR-004', 'featured' => 1, 'order' => 4,
		),
		array(
			'title' => 'Clean Sand / Rait', 'slug' => 'clean-sand-rait', 'category' => 'sand-rait', 'image' => 'align',
			'short' => 'Clean sand for masonry, plaster and concrete mixes.',
			'content' => 'Washed and screened sand suitable for all construction mixes. Consistent quality with bulk delivery options.',
			'price' => $rate, 'price_label' => 'Trolley pricing', 'unit' => 'trolley', 'availability' => 'In Stock',
			'specs' => 'Type: Construction sand', 'features' => "Screened\nBulk supply", 'sku' => 'BP-SD-005', 'featured' => 0, 'order' => 5,
		),
		array(
			'title' => 'Deformed Steel Bars', 'slug' => 'deformed-steel-bars', 'category' => 'steel', 'image' => 'site',
			'short' => 'Structural reinforcement steel for RCC work.',
			'content' => 'High-strength deformed bars for beams, columns, slabs and foundations. Cut-to-length coordination available for projects.',
			'price' => $rate, 'price_label' => 'Per kg / per ton', 'unit' => 'kg', 'availability' => 'In Stock',
			'specs' => 'Grades: Confirm on quote', 'features' => "Project quantities\nReliable sourcing", 'sku' => 'BP-ST-006', 'featured' => 1, 'order' => 6,
		),
		array(
			'title' => 'Electric Conduit Pipe Pack', 'slug' => 'electric-conduit-pipe', 'category' => 'electric-conduit-pipes', 'image' => 'cables',
			'short' => 'Durable PVC conduit pipes for concealed wiring.',
			'content' => 'Flame-retardant conduit pipes in standard diameters for residential and commercial electrical work.',
			'price' => $rate, 'price_label' => 'Per bundle options', 'unit' => 'bundle', 'availability' => 'In Stock',
			'features' => 'Multiple diameters', 'sku' => 'BP-EL-007', 'featured' => 0, 'order' => 7,
		),
		array(
			'title' => 'Plumbing Pipe & Fittings Set', 'slug' => 'plumbing-pipe-fittings', 'category' => 'plumbing-pipes', 'image' => 'wall',
			'short' => 'Pressure-rated pipes with complete fitting range.',
			'content' => 'Complete plumbing range — pipes, elbows, tees, sockets and valves for water supply and drainage.',
			'price' => $rate, 'price_label' => 'Complete range', 'unit' => 'set', 'availability' => 'In Stock',
			'features' => 'Full fitting range', 'sku' => 'BP-PL-008', 'featured' => 0, 'order' => 8,
		),
		array(
			'title' => 'Waterproofing Chemical Kit', 'slug' => 'waterproofing-chemical-kit', 'category' => 'construction-chemicals', 'image' => 'brickMason',
			'short' => 'Waterproofing and bonding chemicals for lasting protection.',
			'content' => 'Construction chemicals for waterproofing roofs, basements, tanks and bathrooms. Application guidance available.',
			'price' => $rate, 'price_label' => 'Kit pricing', 'unit' => 'kit', 'availability' => 'In Stock', 'badge' => 'New',
			'features' => 'Roof & basement use', 'sku' => 'BP-CH-009', 'featured' => 1, 'order' => 9,
		),
		array(
			'title' => 'Insulation Membrane Roll', 'slug' => 'insulation-membrane-roll', 'category' => 'insulation-membrane', 'image' => 'villa1',
			'short' => 'Heat and water insulation membranes for roofs.',
			'content' => 'Roof insulation and waterproofing membranes for energy efficiency and leak protection.',
			'price' => $rate, 'price_label' => 'Per roll', 'unit' => 'roll', 'availability' => 'In Stock',
			'features' => 'Roof application', 'sku' => 'BP-IN-010', 'featured' => 0, 'order' => 10,
		),
		array(
			'title' => 'Copper Wiring Cable Coil', 'slug' => 'copper-wiring-cable', 'category' => 'cables-wires', 'image' => 'cables',
			'short' => 'Pure copper cables for safe, lasting wiring.',
			'content' => 'Quality copper conductor cables in standard gauges for house wiring and commercial circuits.',
			'price' => $rate, 'price_label' => 'Per coil', 'unit' => 'coil', 'availability' => 'In Stock',
			'features' => 'Multiple gauges', 'sku' => 'BP-CB-011', 'featured' => 0, 'order' => 11,
		),
		array(
			'title' => 'Weather-Shield Exterior Paint', 'slug' => 'weather-shield-paint', 'category' => 'paints', 'image' => 'villa2',
			'short' => 'Long-life exterior paint with weather protection.',
			'content' => 'Premium exterior emulsion with UV and rain resistance. Shade cards available on request.',
			'price' => $rate, 'price_label' => 'Per gallon', 'unit' => 'gallon', 'availability' => 'In Stock',
			'features' => 'Shade options', 'sku' => 'BP-PT-012', 'featured' => 0, 'order' => 12,
		),
	);
}

/**
 * Videos.
 *
 * @return array<int,array<string,mixed>>
 */
function bp_demo_video_posts() {
	return array(
		array( 'title' => 'SS7 Bricks — Strength You Can See', 'slug' => 'ss7-strength', 'excerpt' => 'A close look at SS7 brick quality, firing and finish.', 'category' => 'ss7-bricks', 'thumb' => 'heroPoster', 'video' => 'hero', 'source' => 'mp4', 'duration' => '0:14', 'featured' => 1, 'order' => 1 ),
		array( 'title' => 'Inside Our Bhatta — Brick Making Process', 'slug' => 'bhatta-process', 'excerpt' => 'From clay preparation to firing and stacking.', 'category' => 'brick-manufacturing', 'thumb' => 'kiln', 'video' => 'site', 'source' => 'mp4', 'duration' => '0:16', 'featured' => 1, 'order' => 2 ),
		array( 'title' => 'Bricks in Action — Site Work', 'slug' => 'site-work', 'excerpt' => 'Masonry work and material handling on site.', 'category' => 'construction-projects', 'thumb' => 'sitePoster', 'video' => 'site', 'source' => 'mp4', 'duration' => '0:16', 'featured' => 0, 'order' => 3 ),
		array( 'title' => 'Aerial View — Developments We Supply', 'slug' => 'aerial-developments', 'excerpt' => 'Illustrative aerial construction reference.', 'category' => 'construction-projects', 'thumb' => 'aerialPoster', 'video' => 'aerial', 'source' => 'mp4', 'duration' => '0:14', 'featured' => 1, 'order' => 4 ),
		array( 'title' => 'Brick Quality Check', 'slug' => 'brick-quality-check', 'excerpt' => 'How we check strength, shape and sound of bricks.', 'category' => 'brick-quality', 'thumb' => 'stacked', 'video' => 'drone', 'source' => 'mp4', 'duration' => '2:30', 'featured' => 0, 'order' => 5 ),
		array( 'title' => 'BrickPoint Brand Story', 'slug' => 'brand-story', 'excerpt' => 'Who we are and how we support builders.', 'category' => 'company-brand', 'thumb' => 'bricklayer', 'video' => 'drone', 'source' => 'mp4', 'duration' => '1:20', 'featured' => 1, 'order' => 6 ),
	);
}

/**
 * Projects.
 *
 * @return array<int,array<string,mixed>>
 */
function bp_demo_projects() {
	$st = 'Illustrative construction reference';
	return array(
		array( 'title' => 'DHA Lahore — Villa Construction Reference', 'slug' => 'dha-lahore-villa', 'category' => 'residential', 'location' => 'DHA Lahore', 'excerpt' => 'Illustrative construction reference showing villa-scale masonry and finishing work typical of DHA Lahore developments.', 'image' => 'villa1', 'status' => $st, 'featured' => 1, 'order' => 1 ),
		array( 'title' => 'Bahria Town — Housing Reference', 'slug' => 'bahria-town-housing', 'category' => 'residential', 'location' => 'Bahria Town Lahore', 'excerpt' => 'Illustrative construction reference for housing-scale brickwork and material usage.', 'image' => 'villa2', 'status' => $st, 'featured' => 1, 'order' => 2 ),
		array( 'title' => 'Lake City — Development Reference', 'slug' => 'lake-city-dev', 'category' => 'development', 'location' => 'Lake City Lahore', 'excerpt' => 'Illustrative construction reference for large development blocks and apartment construction.', 'image' => 'apt', 'status' => $st, 'featured' => 1, 'order' => 3 ),
		array( 'title' => 'Etihad Town — Block Reference', 'slug' => 'etihad-town-block', 'category' => 'residential', 'location' => 'Etihad Town Lahore', 'excerpt' => 'Illustrative construction reference for block development and boundary structures.', 'image' => 'villa3', 'status' => $st, 'featured' => 0, 'order' => 4 ),
		array( 'title' => 'Al-Kabir Town — Commercial Reference', 'slug' => 'alkabir-commercial', 'category' => 'commercial', 'location' => 'Al-Kabir Town', 'excerpt' => 'Illustrative construction reference for commercial masonry and finishing.', 'image' => 'site', 'status' => $st, 'featured' => 0, 'order' => 5 ),
		array( 'title' => 'Paragon City — Villa Reference', 'slug' => 'paragon-city-villa', 'category' => 'residential', 'location' => 'Paragon City', 'excerpt' => 'Illustrative construction reference showing premium villa construction stages.', 'image' => 'brickMason', 'status' => $st, 'featured' => 0, 'order' => 6 ),
	);
}

/**
 * Locations.
 *
 * @return array<int,array<string,mixed>>
 */
function bp_demo_locations() {
	return array(
		array( 'title' => 'Masha Allah Bricks Company — Ram Thaman', 'slug' => 'masha-allah-ram-thaman', 'address' => 'Ram Thaman, Punjab, Pakistan', 'content' => 'Main brick manufacturing unit producing quality burnt-clay bricks with consistent firing and strength.', 'image' => 'kiln', 'maps' => 'https://maps.app.goo.gl/6Pi5BNTsxPRkn1cn7?g_st=awb', 'phone' => '0315 2850818', 'hours' => 'Mon – Sat: 8:00 AM – 6:00 PM', 'badge' => 'Unit 1', 'order' => 1 ),
		array( 'title' => 'Fine Bricks Company — Raja Jang', 'slug' => 'fine-bricks-raja-jang', 'address' => 'Raja Jang, Punjab, Pakistan', 'content' => 'Fine Bricks production facility focused on dimensional accuracy and premium finish bricks.', 'image' => 'kilnWomen', 'maps' => 'https://maps.app.goo.gl/SDaxqVM55qXt66wW8?g_st=awb', 'phone' => '0315 2850818', 'hours' => 'Mon – Sat: 8:00 AM – 6:00 PM', 'badge' => 'Unit 2', 'order' => 2 ),
		array( 'title' => 'Masha Allah Bricks Company — Sattoki', 'slug' => 'masha-allah-sattoki', 'address' => 'Sattoki, Punjab, Pakistan', 'content' => 'High-capacity bhatta supporting bulk orders for contractors and large developments.', 'image' => 'pile', 'maps' => 'https://google.com/maps?q=31.2328,74.3169424&z=17&hl=en', 'phone' => '0315 2850818', 'hours' => 'Mon – Sat: 8:00 AM – 6:00 PM', 'badge' => 'Unit 3', 'lat' => '31.2328', 'lng' => '74.3169424', 'order' => 3 ),
		array( 'title' => 'BrickPoint — Head Office', 'slug' => 'brickpoint-office', 'address' => 'Lahore, Punjab, Pakistan', 'content' => 'Sales office for quotations, coordination and customer support. Contact us for bulk supply and project planning.', 'image' => 'villa1', 'maps' => 'https://maps.app.goo.gl/GACXw15YxyV4bK5t8?g_st=awb', 'phone' => '0315 2850818', 'hours' => 'Mon – Sat: 9:00 AM – 7:00 PM', 'badge' => 'Unit 4', 'order' => 4 ),
	);
}

/**
 * Blog posts.
 *
 * @return array<int,array<string,mixed>>
 */
function bp_demo_posts() {
	return array(
		array( 'title' => 'How to Choose the Right Bricks for Your House', 'slug' => 'choose-right-bricks', 'excerpt' => 'Awwal vs SS7, strength checks, and what to ask your supplier before ordering.', 'content' => 'Choosing bricks is the most important material decision for your house. In this guide we cover brick grades, simple field tests (shape, sound, water absorption), and the questions to ask your supplier — including firing consistency, batch sorting and delivery planning. For project pricing, share your covered area and wall schedule on WhatsApp and we will estimate quantities.', 'image' => 'stacked', 'category' => 'Brick Selection', 'tags' => array( 'bricks', 'guide' ) ),
		array( 'title' => 'Cement, Sand and Crush: Correct Ratios Explained', 'slug' => 'cement-sand-crush-ratios', 'excerpt' => 'Simple ratios for PCC, RCC and masonry mortar — and mistakes to avoid.', 'content' => 'Correct mix ratios decide the strength of your structure. We explain standard PCC (1:4:8), RCC (1:2:4) and mortar (1:4 / 1:6) mixes, water-cement discipline, curing time, and the common mistakes that weaken concrete. Always confirm structural mixes with your engineer.', 'image' => 'site', 'category' => 'Material Guides', 'tags' => array( 'cement', 'concrete' ) ),
		array( 'title' => 'Brick Manufacturing: From Bhatta to Building', 'slug' => 'brick-manufacturing-bhatta', 'excerpt' => 'Clay preparation, moulding, drying, firing and quality sorting.', 'content' => 'Every brick passes through clay preparation, moulding, sun drying, kiln firing and sorting. Consistent firing temperature and proper stacking decide final strength and colour. This article walks through each stage so buyers understand what quality firing looks like.', 'image' => 'kiln', 'category' => 'Manufacturing', 'tags' => array( 'bhatta', 'quality' ) ),
		array( 'title' => 'Material Planning Checklist for a 5-Marla House', 'slug' => '5-marla-checklist', 'excerpt' => 'Bricks, cement, steel, sand and crush — how to plan stage-wise.', 'content' => 'A stage-wise material plan prevents over-ordering and site delays. We share a practical checklist for foundation, grey structure, masonry, plaster and finishing stages — with tips on storage, wastage allowance and delivery scheduling around Lahore.', 'image' => 'bricklayer', 'category' => 'Planning', 'tags' => array( 'planning', 'estimate' ) ),
	);
}

/**
 * Lahore societies (Projects page chips).
 *
 * @return string[]
 */
function bp_demo_societies() {
	return array( 'DHA Lahore', 'Bahria Town', 'Lake City', 'Etihad Town', 'Al-Kabir Town', 'Paragon City', 'Park View City', 'Central Park', 'LDA City' );
}

/**
 * WhatsApp message presets used by pages.
 *
 * @return array<string,string>
 */
function bp_demo_wa() {
	return array(
		'ss7'        => "Assalam-o-Alaikum BrickPoint,\n\nI want a quotation for: SS7 Bricks\n\nPlease share price, availability and delivery details.\n\nThank you.",
		'list'       => "Assalam-o-Alaikum BrickPoint,\n\nPlease share a quotation for my construction materials.\n\nThank you.",
		'contractor' => "Assalam-o-Alaikum BrickPoint,\n\nI am a contractor and need bulk rates for my project.\n\nPlease share your contractor pricing process.\n\nThank you.",
		'boq'        => "Assalam-o-Alaikum BrickPoint,\n\nI am sharing my project BOQ for quotation.\n\nThank you.",
		'builder'    => "Assalam-o-Alaikum BrickPoint,\n\nI am a builder and need materials for an upcoming house/building project.\n\nPlease guide me on sourcing.\n\nThank you.",
		'company'    => "Assalam-o-Alaikum BrickPoint,\n\nWe are a construction company interested in large-scale material supply.\n\nPlease share your corporate inquiry process.\n\nThank you.",
	);
}

/**
 * Page section blueprints. Each page = list of [section_type, settings].
 * Used by the PHP fallback (page-*.php) and converted into Elementor JSON by the importer.
 *
 * @return array<string,array{title:string,sections:array}>
 */
function bp_demo_pages() {
	$wa   = bp_demo_wa();
	$defs = bp_defaults();
	$ceo  = $defs['bp_ceo'];
	$sales = $defs['bp_sales'];
	$phone = $defs['bp_phone_display'];

	$home = array(
		array( 'hero', array(
			'bg_image' => 'demo:brickMason', 'badge_icon' => 'factory', 'badge_text' => 'Masha Allah • Fine Bricks • SS7',
			'title' => 'Building Strength.<br><span class="hl">Delivering Quality.</span><br>Shaping Tomorrow.',
			'text' => 'Premium bricks and reliable construction materials for homes, commercial developments, and large-scale building projects.',
			'buttons' => array(
				array( 'text' => 'Explore Products', 'url' => 'archive:bp_product', 'style' => 'brick', 'icon' => 'arrow-right' ),
				array( 'text' => 'Request a Quote', 'url' => 'page:contact', 'style' => 'ghost' ),
				array( 'text' => 'WhatsApp Us', 'url' => 'whatsapp', 'style' => 'whatsapp' ),
			),
			'trust' => array( array( 'icon' => 'shield-check', 'text' => 'Quality-focused supply' ), array( 'icon' => 'truck', 'text' => 'Reliable delivery' ), array( 'icon' => 'factory', 'text' => 'Multiple production locations' ) ),
			'video_url' => 'demo:hero', 'video_poster' => 'demo:heroPoster', 'video_eyebrow' => 'SS7 Bricks • In Action', 'video_title' => 'See the strength behind every brick', 'video_link' => 'archive:bp_video',
			'ss7_show' => 'yes', 'ss7_image' => 'demo:redStack', 'ss7_eyebrow' => 'Flagship', 'ss7_title' => 'SS7 Bricks', 'ss7_link_text' => 'View SS7 range →', 'ss7_link' => 'page:ss7-bricks',
			'stat_number' => '3', 'stat_suffix' => '+', 'stat_label' => 'Production units',
			'marquee' => "SS7 Bricks\nCement\nBajri / Crush\nSand / Rait\nSteel\nPipes\nChemicals\nCables\nPaints\nLights",
		) ),
		array( 'image_content', array(
			'image' => 'demo:kiln', 'float1_title' => 'Trusted Supply', 'float1_text' => 'Consistent quality for every order size', 'float2_title' => 'Bulk Ready', 'float2_text' => 'Contractors & companies welcome',
			'eyebrow' => 'Why BrickPoint', 'title' => 'A construction-materials partner you can build on',
			'text' => 'BrickPoint brings together trusted brick manufacturing units and a complete construction-materials range — so contractors, builders and developers can source with confidence.',
			'checklist' => "Quality-focused brick manufacturing at multiple bhatta locations\nFull construction-materials catalogue — one supplier, one quotation\nWhatsApp-first ordering with fast, direct responses\nBulk supply for contractors, builders and construction companies",
			'buttons' => array( array( 'text' => 'About BrickPoint', 'url' => 'page:about', 'style' => 'dark', 'icon' => 'arrow-right' ), array( 'text' => 'Our Locations', 'url' => 'archive:bp_location', 'style' => 'outline' ) ),
		) ),
		array( 'category_grid', array( 'style' => 'dark', 'count' => 12, 'eyebrow' => 'Product Categories', 'title' => 'One supplier for your complete material list', 'text' => 'From flagship SS7 bricks to cement, aggregates, steel, pipes, electricals and finishes.', 'button_text' => 'View All Categories', 'button_url' => 'page:categories' ) ),
		array( 'ss7_feature', array(
			'eyebrow' => 'Flagship Product', 'title' => 'The Strength Behind Every Structure',
			'text' => 'SS7 Bricks — our signature range engineered for strength, shape and lasting performance. Ask for specifications, availability and project pricing on WhatsApp.',
			'specs' => array( array( 'label' => 'Size', 'value' => 'Standard chamber size (confirm on quote)' ), array( 'label' => 'Type', 'value' => 'Burnt-clay SS7' ), array( 'label' => 'Usage', 'value' => 'Homes • Commercial • Boundary' ), array( 'label' => 'Availability', 'value' => 'Bulk & retail orders' ) ),
			'buttons' => array( array( 'text' => 'Request SS7 Quote', 'url' => 'wa:' . $wa['ss7'], 'style' => 'whatsapp' ), array( 'text' => 'View SS7 Page', 'url' => 'page:ss7-bricks', 'style' => 'dark', 'icon' => 'arrow-right' ) ),
			'image' => 'demo:redStack', 'thumbs' => array( array( 'image' => 'demo:stacked' ), array( 'image' => 'demo:pile' ), array( 'image' => 'demo:worker' ) ),
		) ),
		array( 'product_grid', array( 'eyebrow' => 'Featured Products', 'title' => 'Materials contractors ask for by name', 'text' => 'Live from the product catalogue — prices, units and WhatsApp ordering on every card.', 'count' => 8, 'featured' => 'yes', 'columns' => 4, 'button_text' => 'Browse All Products', 'button_url' => 'archive:bp_product' ) ),
		array( 'video_showcase', array(
			'eyebrow' => 'Inside BrickPoint', 'title' => 'See the Strength Behind Every Brick', 'text' => 'Manufacturing, bhattas, quality checks, materials and project references — on video.', 'button_text' => 'View All Videos', 'button_url' => 'archive:bp_video',
			'main_video' => 'demo:drone', 'main_poster' => 'demo:dronePoster', 'main_badge' => 'Featured',
			'mini1_video' => 'demo:site', 'mini1_poster' => 'demo:sitePoster', 'mini1_caption' => 'From the Bhatta to Your Building', 'mini1_text' => 'Brick preparation, firing, stacking, loading and quality — the journey of every batch.',
			'mini2_video' => 'demo:aerial', 'mini2_poster' => 'demo:aerialPoster', 'mini2_caption' => 'Materials That Become Landmarks', 'mini2_text' => 'Illustrative construction references from housing developments and building work.',
			'count' => 3,
		) ),
		array( 'project_grid', array( 'eyebrow' => 'Project References', 'title' => 'Materials that become landmarks', 'text' => 'Illustrative construction references from Lahore housing societies and building work.', 'count' => 3, 'columns' => 3 ) ),
		array( 'audience', array(
			'eyebrow' => 'Who We Serve', 'title' => 'Built for the way you build', 'text' => 'Bulk supply, project quotations and coordinated materials — for every scale of builder.',
			'items' => array(
				array( 'title' => 'For Contractors', 'text' => 'Bulk material supply, project-based quotations and delivery coordination.', 'url' => 'page:for-contractors', 'image' => 'demo:site' ),
				array( 'title' => 'For Builders', 'text' => 'Consistent quality across every batch, with multi-category sourcing.', 'url' => 'page:for-builders', 'image' => 'demo:bricklayer' ),
				array( 'title' => 'For Construction Companies', 'text' => 'Large-scale supply, documentation and dedicated contact.', 'url' => 'page:for-companies', 'image' => 'demo:apt' ),
			),
		) ),
		array( 'cta_band', array(
			'bg_image' => 'demo:bricklayer', 'eyebrow' => 'Get a fast quotation', 'title' => 'Send your material list.<br>We handle the rest.', 'meta' => $phone . ' • CEO: ' . $ceo . ' • Sales: ' . $sales,
			'buttons' => array( array( 'text' => 'WhatsApp Your List', 'url' => 'wa:' . $wa['list'], 'style' => 'whatsapp', 'icon' => 'message-circle' ), array( 'text' => 'Request Quote Form', 'url' => 'page:contact', 'style' => 'brick' ) ),
		) ),
	);

	$about = array(
		array( 'page_header', array( 'bg_image' => 'demo:bricklayer', 'size' => 'md', 'eyebrow' => 'Our Story', 'title' => 'BrickPoint — strength you can build on', 'text' => 'A construction-materials supplier bringing together trusted brick manufacturing units and a complete building-materials range for contractors, builders, developers and individual customers.' ) ),
		array( 'video_content', array(
			'dark' => 'no', 'video_first' => 'yes', 'video' => 'demo:site', 'poster' => 'demo:sitePoster', 'caption' => 'Company video — manufacturing and site work (replaceable from theme settings)',
			'eyebrow' => 'Brand Story', 'title' => 'From bhatta kilns to landmark buildings',
			'text' => 'BrickPoint unites Masha Allah Bricks Company, Fine Bricks Company and the SS7 Bricks range with a full construction-materials catalogue — so every customer, from a single-home builder to a large developer, can source reliably from one supplier.',
			'cards' => array( array( 'icon' => 'target', 'title' => 'Mission', 'text' => 'Reliable, quality materials with honest quotations.' ), array( 'icon' => 'eye', 'title' => 'Vision', 'text' => 'The trusted materials partner for every project scale.' ), array( 'icon' => 'heart', 'title' => 'Values', 'text' => 'Quality, consistency and responsive service.' ) ),
		) ),
		array( 'icon_cards', array( 'bg' => 'bp-sand', 'style' => 'dark-3xl', 'columns' => 4, 'eyebrow' => 'Capabilities', 'title' => 'What we supply', 'items' => array(
			array( 'icon' => 'factory', 'title' => 'Brick Manufacturing', 'text' => 'Multiple bhatta locations producing burnt-clay and SS7 bricks.' ),
			array( 'icon' => 'shield-check', 'title' => 'Quality Commitment', 'text' => 'Sorted batches, consistent firing and honest grading.' ),
			array( 'icon' => 'users', 'title' => 'All Customer Sizes', 'text' => 'Individual home builders to contractors and companies.' ),
			array( 'icon' => 'target', 'title' => 'Project Support', 'text' => 'Stage-wise quotations and delivery coordination.' ),
		) ) ),
		array( 'team', array( 'eyebrow' => 'Management', 'title' => 'Leadership', 'text' => 'Direct access to decision-makers — no layers between you and your quotation.', 'items' => array(
			array( 'name' => $ceo, 'role' => 'Chief Executive Officer', 'image' => 'demo:worker', 'text' => 'BrickPoint • ' . $phone ),
			array( 'name' => $sales, 'role' => 'Sales Manager', 'image' => 'demo:brickMason', 'text' => 'BrickPoint • ' . $phone ),
		), 'buttons' => array( array( 'text' => 'View Products', 'url' => 'archive:bp_product', 'style' => 'brick', 'icon' => 'arrow-right' ), array( 'text' => 'Contact Us', 'url' => 'page:contact', 'style' => 'outline' ), array( 'text' => 'Our Locations', 'url' => 'archive:bp_location', 'style' => 'outline' ) ) ) ),
	);

	$ss7 = array(
		array( 'page_header', array( 'bg_image' => 'demo:redStack', 'size' => 'lg', 'eyebrow' => 'Flagship Range', 'eyebrow_style' => 'badge', 'eyebrow_icon' => 'award', 'title' => 'SS7 <span class="hl">Bricks</span>', 'text' => 'Our signature high-strength brick — consistent firing, sharp edges and dependable supply for homes, commercial work and boundary structures.',
			'buttons' => array( array( 'text' => 'Request SS7 Quotation', 'url' => 'wa:' . $wa['ss7'], 'style' => 'whatsapp', 'size' => 'lg' ), array( 'text' => 'Browse Products', 'url' => 'archive:bp_product', 'style' => 'ghost', 'size' => 'lg' ) ),
			'side_image' => 'demo:redStack', 'side_tags' => "High Strength\nSharp Edges\nEven Firing" ) ),
		array( 'icon_cards', array( 'style' => 'white-3xl', 'columns' => 4, 'eyebrow' => 'Editable Specifications', 'title' => 'SS7 at a glance', 'text' => 'Specification fields are editable from the product record — update size, colour, type, strength, usage, availability and delivery area any time.', 'items' => array(
			array( 'icon' => 'ruler', 'title' => 'Size', 'text' => 'Standard chamber size — confirm current batch on quote' ),
			array( 'icon' => 'palette', 'title' => 'Colour', 'text' => 'Classic kiln-fired red with natural variation' ),
			array( 'icon' => 'layers', 'title' => 'Type & Strength', 'text' => 'Burnt-clay SS7 — high crushing strength grade' ),
			array( 'icon' => 'truck', 'title' => 'Usage & Delivery', 'text' => 'Homes • Commercial • Boundary — delivery on schedule' ),
		), 'after_html' => '<strong>Quality highlights:</strong> uniform size &amp; sharp edges • consistent kiln firing • sorted batches • bulk order support. For lab-tested strength figures of the current batch, message us on WhatsApp.' ) ),
		array( 'video_content', array( 'dark' => 'yes', 'eyebrow' => 'Product Video', 'title' => 'Watch SS7 bricks up close', 'list' => "Manufacturing walkthrough\nQuality & firing checks\nUsage on real sites", 'buttons' => array( array( 'text' => 'All Videos', 'url' => 'archive:bp_video', 'style' => 'ghost', 'icon' => 'play', 'icon_pos' => 'left' ) ), 'video' => 'demo:hero', 'poster' => 'demo:heroPoster' ) ),
		array( 'product_grid', array( 'plain' => 'yes', 'head_layout' => 'row', 'title' => 'SS7 Products', 'category' => 'ss7-bricks', 'featured' => 'no', 'count' => 8, 'columns' => 4, 'button_text' => 'View all', 'button_url' => 'archive:bp_product' ) ),
		array( 'cta_box', array( 'outer' => 'bp-section-sm bp-sand', 'style' => 'dark', 'split' => 'yes', 'title' => 'Need SS7 for your project?', 'text' => 'Share quantity + site location for availability, delivery details and final quotation.', 'buttons' => array( array( 'text' => 'WhatsApp SS7 Inquiry', 'url' => 'wa:' . $wa['ss7'], 'style' => 'whatsapp' ), array( 'text' => 'Request Quote Form', 'url' => 'page:contact', 'style' => 'brick' ) ) ) ),
	);

	$materials = array(
		array( 'page_header', array( 'eyebrow' => 'Complete Range', 'title' => 'Construction Materials', 'text' => 'Cement to finishes — one quotation, coordinated supply, WhatsApp-fast response.' ) ),
		array( 'material_groups', array( 'per_group' => 4, 'groups' => array(
			array( 'title' => 'Structure & Masonry', 'categories' => 'bricks,ss7-bricks,cement,bajri-crush,sand-rait,steel' ),
			array( 'title' => 'Pipes & Services', 'categories' => 'electric-conduit-pipes,plumbing-pipes,cables-wires,switches-sockets' ),
			array( 'title' => 'Protection & Finish', 'categories' => 'construction-chemicals,insulation-membrane,paints,lights,other-materials' ),
		) ) ),
		array( 'cta_box', array( 'outer' => 'bp-section-xs', 'style' => 'plain', 'title' => 'One list. One quotation. Coordinated delivery.', 'text' => 'Send your BOQ or material list on WhatsApp and get a consolidated project quotation.', 'eyebrow' => 'Bulk Orders', 'buttons' => array( array( 'text' => 'Request Project Quotation', 'url' => 'page:contact', 'style' => 'brick', 'icon' => 'arrow-right', 'size' => 'xl' ) ) ) ),
	);

	$contractors = array(
		array( 'page_header', array( 'bg_image' => 'demo:site', 'size' => 'md', 'eyebrow' => 'For Contractors', 'title' => 'Bulk supply that keeps your sites moving', 'text' => 'Project-based quotations, reliable availability and delivery coordination across brick and material categories.', 'buttons' => array( array( 'text' => 'Get Contractor Rates', 'url' => 'wa:' . $wa['contractor'], 'style' => 'whatsapp', 'size' => 'lg' ), array( 'text' => 'Browse Products', 'url' => 'archive:bp_product', 'style' => 'ghost', 'size' => 'lg' ) ) ) ),
		array( 'icon_cards', array( 'style' => 'white-3xl', 'columns' => 3, 'eyebrow' => 'Contractor Benefits', 'title' => 'Why contractors choose BrickPoint', 'items' => array(
			array( 'icon' => 'check-circle', 'icon_color' => 'green', 'title' => 'Bulk material supply', 'text' => 'Trolley, thousand-brick and tonnage quantities with sorted batches.' ),
			array( 'icon' => 'check-circle', 'icon_color' => 'green', 'title' => 'Project-based quotations', 'text' => 'Send your BOQ or stage list — get one consolidated quote.' ),
			array( 'icon' => 'check-circle', 'icon_color' => 'green', 'title' => 'Delivery coordination', 'text' => 'Schedule deliveries stage-wise with site-location planning.' ),
			array( 'icon' => 'check-circle', 'icon_color' => 'green', 'title' => 'Reliable availability', 'text' => 'Multiple bhatta units back consistent brick supply.' ),
			array( 'icon' => 'check-circle', 'icon_color' => 'green', 'title' => 'Full category range', 'text' => 'Bricks, cement, aggregates, steel, pipes, electricals, finishes.' ),
			array( 'icon' => 'check-circle', 'icon_color' => 'green', 'title' => 'Direct WhatsApp line', 'text' => 'Fast answers on rates, stock and delivery slots.' ),
		) ) ),
		array( 'cta_box', array( 'style' => 'dark', 'title' => 'Share your BOQ today', 'text' => 'Attach your material list on WhatsApp for a project quotation.', 'buttons' => array( array( 'text' => 'Send BOQ on WhatsApp', 'url' => 'wa:' . $wa['boq'], 'style' => 'whatsapp' ), array( 'text' => 'Use Quote Form', 'url' => 'page:contact', 'style' => 'brick', 'icon' => 'arrow-right' ) ) ) ),
	);

	$builders = array(
		array( 'page_header', array( 'bg_image' => 'demo:bricklayer', 'size' => 'md', 'eyebrow' => 'For Builders', 'title' => 'Consistent quality, house after house', 'text' => 'Source complete material sets with consistent batches — from foundation to finishing.', 'buttons' => array( array( 'text' => 'Discuss Your Build', 'url' => 'wa:' . $wa['builder'], 'style' => 'whatsapp', 'size' => 'lg' ), array( 'text' => 'SS7 Bricks', 'url' => 'page:ss7-bricks', 'style' => 'ghost', 'size' => 'lg' ) ) ) ),
		array( 'icon_cards', array( 'style' => 'white-3xl', 'columns' => 3, 'eyebrow' => 'Builder Benefits', 'title' => 'Sourcing made simple', 'items' => array(
			array( 'icon' => 'check-circle', 'icon_color' => 'green', 'title' => 'Multi-category sourcing', 'text' => 'One supplier for structure, services and finishes.' ),
			array( 'icon' => 'check-circle', 'icon_color' => 'green', 'title' => 'Consistent quality', 'text' => 'Sorted batches keep every house uniform.' ),
			array( 'icon' => 'check-circle', 'icon_color' => 'green', 'title' => 'Quantity planning', 'text' => 'Stage-wise estimates reduce waste and delays.' ),
			array( 'icon' => 'check-circle', 'icon_color' => 'green', 'title' => 'Bulk requirements', 'text' => 'House-builder rates on repeat orders.' ),
			array( 'icon' => 'check-circle', 'icon_color' => 'green', 'title' => 'Project planning support', 'text' => 'Material sequencing advice from our team.' ),
			array( 'icon' => 'check-circle', 'icon_color' => 'green', 'title' => 'Fast quotation', 'text' => 'WhatsApp your covered area + list for pricing.' ),
		) ) ),
		array( 'cta_box', array( 'style' => 'sand', 'title' => 'Planning your next build?', 'text' => 'Get a complete material quotation before you break ground.', 'buttons' => array( array( 'text' => 'Request Quotation', 'url' => 'page:contact', 'style' => 'brick', 'icon' => 'arrow-right' ), array( 'text' => 'Full Range', 'url' => 'page:construction-materials', 'style' => 'outline' ) ) ) ),
	);

	$companies = array(
		array( 'page_header', array( 'bg_image' => 'demo:apt', 'size' => 'md', 'eyebrow' => 'For Construction Companies', 'title' => 'Large-scale supply, coordinated professionally', 'text' => 'Multi-location coordination, documentation support and a dedicated contact for corporate accounts.', 'buttons' => array( array( 'text' => 'Corporate Inquiry', 'url' => 'wa:' . $wa['company'], 'style' => 'whatsapp', 'size' => 'lg' ), array( 'text' => 'Contact Form', 'url' => 'page:contact', 'style' => 'ghost', 'size' => 'lg' ) ) ) ),
		array( 'icon_cards', array( 'style' => 'dark-3xl', 'columns' => 3, 'eyebrow' => 'Corporate Benefits', 'title' => 'Built for scale', 'items' => array(
			array( 'icon' => 'check-circle', 'icon_color' => 'green', 'title' => 'Large-scale supply', 'text' => 'High-volume brick and material orders with planning.' ),
			array( 'icon' => 'check-circle', 'icon_color' => 'green', 'title' => 'Material coordination', 'text' => 'Stage-wise scheduling across your sites.' ),
			array( 'icon' => 'check-circle', 'icon_color' => 'green', 'title' => 'Multiple locations', 'text' => 'Bhatta + office network for flexible fulfilment.' ),
			array( 'icon' => 'check-circle', 'icon_color' => 'green', 'title' => 'Product documentation', 'text' => 'Quotations, specs and delivery records on request.' ),
			array( 'icon' => 'check-circle', 'icon_color' => 'green', 'title' => 'Dedicated contact', 'text' => 'CEO ' . $ceo . ' • Sales ' . $sales . ' • ' . $phone ),
			array( 'icon' => 'check-circle', 'icon_color' => 'green', 'title' => 'Full-range sourcing', 'text' => 'Structure to finishes under one relationship.' ),
		) ) ),
		array( 'cta_box', array( 'style' => 'outline', 'title' => 'Start a corporate conversation', 'text' => 'Share your company profile and upcoming requirement schedule.', 'buttons' => array( array( 'text' => 'Corporate Inquiry Form', 'url' => 'page:contact', 'style' => 'brick', 'icon' => 'arrow-right' ), array( 'text' => 'View References', 'url' => 'archive:bp_project', 'style' => 'outline' ) ) ) ),
	);

	$contact = array(
		array( 'page_header', array( 'eyebrow' => 'Get In Touch', 'title' => 'Contact BrickPoint', 'text' => 'Call, WhatsApp or send the quotation form — we respond fast on working hours.' ) ),
		array( 'contact', array() ),
	);

	$categories = array(
		array( 'page_header', array( 'eyebrow' => 'Browse by category', 'title' => 'Product Categories', 'text' => 'Fifteen editable categories — from SS7 bricks to finishing materials.' ) ),
		array( 'category_grid', array( 'style' => 'light', 'count' => 0 ) ),
	);

	$privacy = array(
		array( 'prose', array( 'eyebrow' => 'Legal', 'title' => 'Privacy Policy', 'content' => bp_demo_legal_html( 'privacy' ) ) ),
	);
	$terms = array(
		array( 'prose', array( 'eyebrow' => 'Legal', 'title' => 'Terms & Conditions', 'content' => bp_demo_legal_html( 'terms' ) ) ),
	);

	return array(
		'home'                   => array( 'title' => 'Home', 'sections' => $home ),
		'about'                  => array( 'title' => 'About Us', 'sections' => $about ),
		'ss7-bricks'             => array( 'title' => 'SS7 Bricks', 'sections' => $ss7 ),
		'construction-materials' => array( 'title' => 'Construction Materials', 'sections' => $materials ),
		'categories'             => array( 'title' => 'Product Categories', 'sections' => $categories ),
		'for-contractors'        => array( 'title' => 'For Contractors', 'sections' => $contractors ),
		'for-builders'           => array( 'title' => 'For Builders', 'sections' => $builders ),
		'for-companies'          => array( 'title' => 'For Construction Companies', 'sections' => $companies ),
		'contact'                => array( 'title' => 'Contact', 'sections' => $contact ),
		'privacy-policy'         => array( 'title' => 'Privacy Policy', 'sections' => $privacy ),
		'terms-and-conditions'   => array( 'title' => 'Terms and Conditions', 'sections' => $terms ),
		'blog'                   => array( 'title' => 'Blog', 'sections' => array() ), // Posts page (archive template).
	);
}

/**
 * Archive header presets (used by archive templates + Elementor archive templates).
 *
 * @return array<string,array<string,mixed>>
 */
function bp_demo_archive_headers() {
	return array(
		'bp_product'  => array( 'eyebrow' => 'Catalogue', 'title' => 'Products', 'text' => 'Every product with WhatsApp ordering — no cart, no checkout, just fast quotations.', 'pills_taxonomy' => 'bp_product_category' ),
		'bp_video'    => array( 'eyebrow' => 'Video Library', 'title' => 'Inside BrickPoint', 'text' => 'Explore our products, production process, construction materials, projects, and company updates through video.', 'pills_taxonomy' => 'bp_video_category' ),
		'bp_project'  => array( 'eyebrow' => 'References & Inspiration', 'title' => 'Projects', 'text' => 'Construction references and project inspiration visuals from Lahore housing developments. Visuals are illustrative unless a project is verified by BrickPoint.', 'chips' => implode( "\n", bp_demo_societies() ) ),
		'bp_location' => array( 'eyebrow' => 'Bhattas & Office', 'title' => 'Our Locations', 'text' => 'Three production units plus head office — tap any card for real Google Maps directions.' ),
		'post'        => array( 'eyebrow' => 'Guides & Updates', 'title' => 'Blog', 'text' => 'Brick selection, material guides, planning tips and industry updates.', 'search' => 'yes' ),
	);
}

/**
 * Legal page HTML.
 *
 * @param string $which privacy|terms.
 * @return string
 */
function bp_demo_legal_html( $which ) {
	$d     = bp_defaults();
	$phone = $d['bp_phone_display'];
	$email = $d['bp_email'];
	$year  = gmdate( 'Y' );
	if ( 'privacy' === $which ) {
		return '<p>BrickPoint respects your privacy. This policy explains what information we collect through quotation forms, WhatsApp inquiries and website usage — and how we use it.</p>'
			. '<h2>Information We Collect</h2><ul><li>Contact details you provide (name, phone, email, company, site location).</li><li>Quotation details (materials, quantities, messages).</li><li>Basic website analytics (pages visited, device type).</li></ul>'
			. '<h2>How We Use It</h2><ul><li>To prepare quotations and coordinate deliveries.</li><li>To respond to inquiries via phone, WhatsApp or email.</li><li>To improve our catalogue and customer experience.</li></ul>'
			. '<h2>Sharing</h2><p>We do not sell your personal data. Information is shared only with staff and delivery partners as needed to fulfil your request, or when required by law.</p>'
			. '<h2>WhatsApp Communication</h2><p>By contacting ' . esc_html( $phone ) . ' on WhatsApp you consent to receiving quotation and order-related messages from BrickPoint.</p>'
			. '<h2>Contact</h2><p>For privacy questions contact ' . esc_html( $email ) . ' or ' . esc_html( $phone ) . '.</p>'
			. '<p class="bp-muted sm">Last updated: ' . $year . '. Editable from the WordPress page editor.</p>';
	}
	return '<p>By requesting quotations or purchasing materials from BrickPoint, you agree to the following terms.</p>'
		. '<h2>Quotations &amp; Pricing</h2><ul><li>All prices shared on WhatsApp, phone or this website are quotations valid for the stated period only.</li><li>Material rates may change with market conditions; final rates are confirmed before dispatch.</li><li>Delivery charges (if any) are quoted separately based on site location and quantity.</li></ul>'
		. '<h2>Orders &amp; Payment</h2><ul><li>Orders are confirmed after mutual agreement on rate, quantity and delivery schedule.</li><li>Payment terms are agreed per order. This website has no online checkout or payment gateway.</li></ul>'
		. '<h2>Delivery</h2><ul><li>Delivery timelines are estimated and coordinated per order; they are not guaranteed unless expressly confirmed in writing.</li><li>Customers should ensure site access and unloading arrangements.</li></ul>'
		. '<h2>Quality</h2><ul><li>Natural variation in kiln-fired brick colour is normal.</li><li>Grade and sorting are as described in the quotation; please inspect on delivery and report issues promptly.</li></ul>'
		. '<h2>Content Notice</h2><p>Project visuals labelled “Illustrative construction reference” are inspiration references, not claims of completed supply, unless verified by BrickPoint management.</p>'
		. '<h2>Contact</h2><p>Questions: ' . esc_html( $email ) . ' • ' . esc_html( $phone ) . '.</p>'
		. '<p class="bp-muted sm">Last updated: ' . $year . '. Editable from the WordPress page editor.</p>';
}

/**
 * Menus.
 *
 * @return array<string,array{name:string,items:array}>
 */
function bp_demo_menus() {
	$primary = array(
		array( 'title' => 'Home', 'url' => 'home' ),
		array( 'title' => 'Products', 'url' => 'archive:bp_product', 'children' => array(
			array( 'title' => 'All Products', 'url' => 'archive:bp_product' ),
			array( 'title' => 'Categories', 'url' => 'page:categories' ),
			array( 'title' => 'SS7 Bricks', 'url' => 'page:ss7-bricks' ),
			array( 'title' => 'Construction Materials', 'url' => 'page:construction-materials' ),
			array( 'title' => 'For Contractors →', 'url' => 'page:for-contractors', 'classes' => 'bp-menu-secondary' ),
		) ),
		array( 'title' => 'SS7 Bricks', 'url' => 'page:ss7-bricks' ),
		array( 'title' => 'Projects', 'url' => 'archive:bp_project' ),
		array( 'title' => 'Videos', 'url' => 'archive:bp_video' ),
		array( 'title' => 'Locations', 'url' => 'archive:bp_location' ),
		array( 'title' => 'About', 'url' => 'page:about' ),
		array( 'title' => 'Blog', 'url' => 'page:blog' ),
		array( 'title' => 'Contact', 'url' => 'page:contact' ),
	);
	$footer = array(
		array( 'title' => 'About Us', 'url' => 'page:about' ),
		array( 'title' => 'Projects', 'url' => 'archive:bp_project' ),
		array( 'title' => 'Blog', 'url' => 'page:blog' ),
		array( 'title' => 'Locations', 'url' => 'archive:bp_location' ),
	);
	$mobile = $primary;
	return array(
		'primary' => array( 'name' => 'BrickPoint Primary', 'items' => $primary ),
		'footer'  => array( 'name' => 'BrickPoint Footer', 'items' => $footer ),
		'mobile'  => array( 'name' => 'BrickPoint Mobile', 'items' => $mobile ),
	);
}

/**
 * Render a list of [type, settings] sections.
 *
 * @param array $sections Sections.
 */
function bp_render_sections( $sections ) {
	foreach ( (array) $sections as $sec ) {
		bp_render_section( $sec[0], $sec[1] );
	}
}

/**
 * Render one section by type.
 *
 * @param string $type     Section type.
 * @param array  $settings Settings.
 */
function bp_render_section( $type, $settings ) {
	switch ( $type ) {
		case 'hero':
			bp_section_hero( $settings );
			break;
		case 'image_content':
			bp_section_image_content( $settings );
			break;
		case 'category_grid':
			bp_section_category_grid( $settings );
			break;
		case 'ss7_feature':
			bp_section_ss7_feature( $settings );
			break;
		case 'product_grid':
			bp_section_product_grid( $settings );
			break;
		case 'video_showcase':
			bp_section_video_showcase( $settings );
			break;
		case 'video_grid':
			bp_section_post_grid( $settings, 'bp_video' );
			break;
		case 'project_grid':
			bp_section_post_grid( $settings, 'bp_project' );
			break;
		case 'location_grid':
			bp_section_post_grid( $settings, 'bp_location' );
			break;
		case 'blog_grid':
			bp_section_post_grid( $settings, 'post' );
			break;
		case 'audience':
			bp_section_audience( $settings );
			break;
		case 'team':
			bp_section_team( $settings );
			break;
		case 'cta_band':
			bp_section_cta_band( $settings );
			break;
		case 'cta_box':
			bp_section_cta_box( $settings );
			break;
		case 'page_header':
			bp_section_page_header( $settings );
			break;
		case 'icon_cards':
			bp_section_icon_cards( $settings );
			break;
		case 'notice':
			bp_section_notice( $settings );
			break;
		case 'video_content':
			if ( ! empty( $settings['cards'] ) ) {
				ob_start();
				echo '<div class="bp-grid cols-3 gap-sm">';
				foreach ( $settings['cards'] as $i => $c ) {
					echo '<div class="bp-icon-card compact bp-reveal delay-' . (int) $i . '">' . bp_icon( $c['icon'] ) . '<h3>' . esc_html( $c['title'] ) . '</h3><p>' . esc_html( $c['text'] ) . '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				echo '</div>';
				$settings['content'] = ob_get_clean();
			}
			bp_section_video_content( $settings );
			break;
		case 'material_groups':
			bp_section_material_groups( $settings );
			break;
		case 'contact':
			bp_section_contact( $settings );
			break;
		case 'prose':
			bp_section_prose( $settings );
			break;
	}
}
