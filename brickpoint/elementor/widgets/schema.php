<?php
/**
 * Widget schema: declares controls for every BrickPoint section widget.
 *
 * Control types: text, textarea, wysiwyg, number, select, switch, media, url,
 * icon, color, repeater (with fields).
 *
 * The keys are the exact setting keys consumed by inc/sections.php, so a widget's
 * settings array can be passed straight into bp_render_section().
 *
 * @package BrickPoint
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Button repeater definition (shared).
 *
 * @return array
 */
function bp_schema_buttons() {
	return array(
		'type'   => 'repeater',
		'label'  => __( 'Buttons', 'brickpoint' ),
		'title'  => 'text',
		'fields' => array(
			'text'  => array( 'type' => 'text', 'label' => __( 'Text', 'brickpoint' ), 'default' => __( 'Learn More', 'brickpoint' ) ),
			'url'   => array( 'type' => 'url', 'label' => __( 'Link', 'brickpoint' ), 'description' => __( 'Also accepts: page:slug, archive:bp_product, whatsapp, wa:Message text, tel, home', 'brickpoint' ) ),
			'style' => array( 'type' => 'select', 'label' => __( 'Style', 'brickpoint' ), 'default' => 'brick', 'options' => array( 'brick' => 'Brick (orange)', 'whatsapp' => 'WhatsApp (green)', 'dark' => 'Dark', 'ghost' => 'Ghost (light)', 'ghost-dark' => 'Ghost (dark)', 'outline' => 'Outline', 'light' => 'Light' ) ),
			'size'  => array( 'type' => 'select', 'label' => __( 'Size', 'brickpoint' ), 'default' => '', 'options' => array( '' => 'Default', 'sm' => 'Small', 'lg' => 'Large', 'xl' => 'Extra large' ) ),
			'icon'  => array( 'type' => 'icon', 'label' => __( 'Icon', 'brickpoint' ) ),
			'icon_pos' => array( 'type' => 'select', 'label' => __( 'Icon position', 'brickpoint' ), 'default' => 'right', 'options' => array( 'right' => 'Right', 'left' => 'Left' ) ),
		),
	);
}

/**
 * Section-head controls (eyebrow/title/text).
 *
 * @param string $title_default Default title.
 * @return array
 */
function bp_schema_head( $title_default = '' ) {
	return array(
		'eyebrow' => array( 'type' => 'text', 'label' => __( 'Eyebrow', 'brickpoint' ) ),
		'title'   => array( 'type' => 'textarea', 'label' => __( 'Title', 'brickpoint' ), 'default' => $title_default, 'rows' => 2 ),
		'text'    => array( 'type' => 'textarea', 'label' => __( 'Text', 'brickpoint' ), 'rows' => 3 ),
	);
}

/**
 * The full schema.
 *
 * @return array<string,array{title:string,icon:string,section:string,controls:array}>
 */
function bp_widget_schema() {
	$b = bp_schema_buttons();
	return array(
		'bp_hero' => array(
			'title' => __( 'BrickPoint Hero', 'brickpoint' ), 'icon' => 'eicon-banner', 'section' => 'hero',
			'controls' => array_merge(
				array(
					'bg_image'   => array( 'type' => 'media', 'label' => __( 'Background image', 'brickpoint' ) ),
					'badge_icon' => array( 'type' => 'icon', 'label' => __( 'Badge icon', 'brickpoint' ), 'default' => 'factory' ),
					'badge_text' => array( 'type' => 'text', 'label' => __( 'Badge text', 'brickpoint' ) ),
				),
				bp_schema_head( 'Building Strength.<br><span class="hl">Delivering Quality.</span><br>Shaping Tomorrow.' ),
				array(
					'buttons' => $b,
					'trust'   => array( 'type' => 'repeater', 'label' => __( 'Trust badges', 'brickpoint' ), 'title' => 'text', 'fields' => array( 'icon' => array( 'type' => 'icon', 'label' => 'Icon' ), 'text' => array( 'type' => 'text', 'label' => 'Text' ) ) ),
					'video_url'     => array( 'type' => 'text', 'label' => __( 'Video URL (mp4 / YouTube / Vimeo)', 'brickpoint' ), 'group' => 'video', 'group_label' => __( 'Video Card', 'brickpoint' ) ),
					'video_poster'  => array( 'type' => 'media', 'label' => __( 'Video poster', 'brickpoint' ), 'group' => 'video' ),
					'video_eyebrow' => array( 'type' => 'text', 'label' => __( 'Video eyebrow', 'brickpoint' ), 'group' => 'video' ),
					'video_title'   => array( 'type' => 'text', 'label' => __( 'Video title', 'brickpoint' ), 'group' => 'video' ),
					'video_link'    => array( 'type' => 'url', 'label' => __( 'Video link', 'brickpoint' ), 'group' => 'video' ),
					'ss7_show'      => array( 'type' => 'switch', 'label' => __( 'Show SS7 floating card', 'brickpoint' ), 'default' => 'yes', 'group' => 'floats', 'group_label' => __( 'Floating cards', 'brickpoint' ) ),
					'ss7_image'     => array( 'type' => 'media', 'label' => __( 'SS7 image', 'brickpoint' ), 'group' => 'floats' ),
					'ss7_eyebrow'   => array( 'type' => 'text', 'label' => __( 'SS7 eyebrow', 'brickpoint' ), 'default' => 'Flagship', 'group' => 'floats' ),
					'ss7_title'     => array( 'type' => 'text', 'label' => __( 'SS7 title', 'brickpoint' ), 'default' => 'SS7 Bricks', 'group' => 'floats' ),
					'ss7_link_text' => array( 'type' => 'text', 'label' => __( 'SS7 link text', 'brickpoint' ), 'default' => 'View SS7 range →', 'group' => 'floats' ),
					'ss7_link'      => array( 'type' => 'url', 'label' => __( 'SS7 link', 'brickpoint' ), 'group' => 'floats' ),
					'stat_number'   => array( 'type' => 'text', 'label' => __( 'Stat number', 'brickpoint' ), 'default' => '3', 'group' => 'floats' ),
					'stat_suffix'   => array( 'type' => 'text', 'label' => __( 'Stat suffix', 'brickpoint' ), 'default' => '+', 'group' => 'floats' ),
					'stat_label'    => array( 'type' => 'text', 'label' => __( 'Stat label', 'brickpoint' ), 'default' => 'Production units', 'group' => 'floats' ),
					'marquee'       => array( 'type' => 'textarea', 'label' => __( 'Marquee items (one per line)', 'brickpoint' ), 'group' => 'marquee', 'group_label' => __( 'Marquee', 'brickpoint' ) ),
				)
			),
		),
		'bp_image_content' => array(
			'title' => __( 'BrickPoint Image + Content', 'brickpoint' ), 'icon' => 'eicon-image-box', 'section' => 'image_content',
			'controls' => array_merge(
				array(
					'image'        => array( 'type' => 'media', 'label' => __( 'Image', 'brickpoint' ) ),
					'image_alt'    => array( 'type' => 'text', 'label' => __( 'Image alt', 'brickpoint' ) ),
					'reverse'      => array( 'type' => 'switch', 'label' => __( 'Image on right', 'brickpoint' ) ),
					'float1_title' => array( 'type' => 'text', 'label' => __( 'Float card 1 title', 'brickpoint' ) ),
					'float1_text'  => array( 'type' => 'text', 'label' => __( 'Float card 1 text', 'brickpoint' ) ),
					'float2_title' => array( 'type' => 'text', 'label' => __( 'Float card 2 title', 'brickpoint' ) ),
					'float2_text'  => array( 'type' => 'text', 'label' => __( 'Float card 2 text', 'brickpoint' ) ),
				),
				bp_schema_head(),
				array(
					'checklist' => array( 'type' => 'textarea', 'label' => __( 'Checklist (one per line)', 'brickpoint' ), 'rows' => 5 ),
					'buttons'   => $b,
					'bg'        => array( 'type' => 'select', 'label' => __( 'Background', 'brickpoint' ), 'default' => '', 'options' => array( '' => 'White', 'bp-sand' => 'Sand', 'bp-dark' => 'Dark' ) ),
				)
			),
		),
		'bp_category_grid' => array(
			'title' => __( 'BrickPoint Category Grid', 'brickpoint' ), 'icon' => 'eicon-gallery-grid', 'section' => 'category_grid',
			'controls' => array_merge(
				bp_schema_head( 'One supplier for your complete material list' ),
				array(
					'style'       => array( 'type' => 'select', 'label' => __( 'Style', 'brickpoint' ), 'default' => 'dark', 'options' => array( 'dark' => 'Dark section (home)', 'light' => 'Light cards (categories page)' ) ),
					'count'       => array( 'type' => 'number', 'label' => __( 'Count (0 = all)', 'brickpoint' ), 'default' => 12 ),
					'button_text' => array( 'type' => 'text', 'label' => __( 'Button text', 'brickpoint' ) ),
					'button_url'  => array( 'type' => 'url', 'label' => __( 'Button link', 'brickpoint' ) ),
				)
			),
		),
		'bp_ss7_feature' => array(
			'title' => __( 'BrickPoint SS7 Feature', 'brickpoint' ), 'icon' => 'eicon-featured-image', 'section' => 'ss7_feature',
			'controls' => array_merge(
				bp_schema_head( 'The Strength Behind Every Structure' ),
				array(
					'specs'   => array( 'type' => 'repeater', 'label' => __( 'Spec boxes', 'brickpoint' ), 'title' => 'label', 'fields' => array( 'label' => array( 'type' => 'text', 'label' => 'Label' ), 'value' => array( 'type' => 'text', 'label' => 'Value' ) ) ),
					'buttons' => $b,
					'image'   => array( 'type' => 'media', 'label' => __( 'Main image', 'brickpoint' ) ),
					'thumbs'  => array( 'type' => 'repeater', 'label' => __( 'Thumbnails', 'brickpoint' ), 'title' => '', 'fields' => array( 'image' => array( 'type' => 'media', 'label' => 'Image' ) ) ),
				)
			),
		),
		'bp_product_grid' => array(
			'title' => __( 'BrickPoint Product Grid', 'brickpoint' ), 'icon' => 'eicon-products', 'section' => 'product_grid',
			'controls' => array_merge(
				bp_schema_head( 'Materials contractors ask for by name' ),
				array(
					'head_layout' => array( 'type' => 'select', 'label' => __( 'Heading layout', 'brickpoint' ), 'default' => '', 'options' => array( '' => 'Centered heading', 'row' => 'Title + link row' ) ),
					'featured'    => array( 'type' => 'switch', 'label' => __( 'Featured only', 'brickpoint' ), 'default' => 'yes' ),
					'category'    => array( 'type' => 'text', 'label' => __( 'Category slug(s)', 'brickpoint' ) ),
					'related'     => array( 'type' => 'text', 'label' => __( 'Specific product IDs (comma separated)', 'brickpoint' ) ),
					'count'       => array( 'type' => 'number', 'label' => __( 'Count', 'brickpoint' ), 'default' => 8 ),
					'columns'     => array( 'type' => 'select', 'label' => __( 'Columns', 'brickpoint' ), 'default' => '4', 'options' => array( '2' => '2', '3' => '3', '4' => '4' ) ),
					'plain'       => array( 'type' => 'switch', 'label' => __( 'Compact padding', 'brickpoint' ) ),
					'dark'        => array( 'type' => 'switch', 'label' => __( 'Dark background', 'brickpoint' ) ),
					'button_text' => array( 'type' => 'text', 'label' => __( 'Button text', 'brickpoint' ) ),
					'button_url'  => array( 'type' => 'url', 'label' => __( 'Button link', 'brickpoint' ) ),
				)
			),
		),
		'bp_video_showcase' => array(
			'title' => __( 'BrickPoint Video Showcase', 'brickpoint' ), 'icon' => 'eicon-video-playlist', 'section' => 'video_showcase',
			'controls' => array_merge(
				bp_schema_head( 'See the Strength Behind Every Brick' ),
				array(
					'main_video'    => array( 'type' => 'text', 'label' => __( 'Main video URL', 'brickpoint' ) ),
					'main_poster'   => array( 'type' => 'media', 'label' => __( 'Main poster', 'brickpoint' ) ),
					'main_badge'    => array( 'type' => 'text', 'label' => __( 'Main badge', 'brickpoint' ), 'default' => 'Featured' ),
					'mini1_video'   => array( 'type' => 'text', 'label' => __( 'Mini 1 video URL', 'brickpoint' ) ),
					'mini1_poster'  => array( 'type' => 'media', 'label' => __( 'Mini 1 poster', 'brickpoint' ) ),
					'mini1_caption' => array( 'type' => 'text', 'label' => __( 'Mini 1 caption', 'brickpoint' ) ),
					'mini1_text'    => array( 'type' => 'textarea', 'label' => __( 'Mini 1 text', 'brickpoint' ) ),
					'mini2_video'   => array( 'type' => 'text', 'label' => __( 'Mini 2 video URL', 'brickpoint' ) ),
					'mini2_poster'  => array( 'type' => 'media', 'label' => __( 'Mini 2 poster', 'brickpoint' ) ),
					'mini2_caption' => array( 'type' => 'text', 'label' => __( 'Mini 2 caption', 'brickpoint' ) ),
					'mini2_text'    => array( 'type' => 'textarea', 'label' => __( 'Mini 2 text', 'brickpoint' ) ),
					'count'         => array( 'type' => 'number', 'label' => __( 'Latest video cards', 'brickpoint' ), 'default' => 3 ),
					'button_text'   => array( 'type' => 'text', 'label' => __( 'Button text', 'brickpoint' ) ),
					'button_url'    => array( 'type' => 'url', 'label' => __( 'Button link', 'brickpoint' ) ),
				)
			),
		),
		'bp_video_grid'    => bp_schema_post_grid( 'bp_video', __( 'BrickPoint Video Grid', 'brickpoint' ), 'eicon-video-camera' ),
		'bp_project_grid'  => bp_schema_post_grid( 'bp_project', __( 'BrickPoint Project Grid', 'brickpoint' ), 'eicon-posts-grid' ),
		'bp_location_grid' => bp_schema_post_grid( 'bp_location', __( 'BrickPoint Location Cards', 'brickpoint' ), 'eicon-google-maps' ),
		'bp_blog_grid'     => bp_schema_post_grid( 'post', __( 'BrickPoint Blog Grid', 'brickpoint' ), 'eicon-post-list' ),
		'bp_audience' => array(
			'title' => __( 'BrickPoint Audience Cards', 'brickpoint' ), 'icon' => 'eicon-person', 'section' => 'audience',
			'controls' => array_merge(
				bp_schema_head( 'Built for the way you build' ),
				array( 'items' => array( 'type' => 'repeater', 'label' => __( 'Cards', 'brickpoint' ), 'title' => 'title', 'fields' => array(
					'image' => array( 'type' => 'media', 'label' => 'Image' ), 'title' => array( 'type' => 'text', 'label' => 'Title' ), 'text' => array( 'type' => 'textarea', 'label' => 'Text' ), 'url' => array( 'type' => 'url', 'label' => 'Link' ), 'link_text' => array( 'type' => 'text', 'label' => 'Link text', 'default' => 'Learn more' ),
				) ) )
			),
		),
		'bp_team' => array(
			'title' => __( 'BrickPoint Team', 'brickpoint' ), 'icon' => 'eicon-lock-user', 'section' => 'team',
			'controls' => array_merge(
				bp_schema_head( 'Leadership' ),
				array(
					'items' => array( 'type' => 'repeater', 'label' => __( 'Members', 'brickpoint' ), 'title' => 'name', 'fields' => array(
						'image' => array( 'type' => 'media', 'label' => 'Photo' ), 'name' => array( 'type' => 'text', 'label' => 'Name' ), 'role' => array( 'type' => 'text', 'label' => 'Role' ), 'text' => array( 'type' => 'text', 'label' => 'Meta line' ),
					) ),
					'buttons' => $b,
				)
			),
		),
		'bp_cta_band' => array(
			'title' => __( 'BrickPoint CTA Band', 'brickpoint' ), 'icon' => 'eicon-call-to-action', 'section' => 'cta_band',
			'controls' => array_merge(
				array( 'bg_image' => array( 'type' => 'media', 'label' => __( 'Background image', 'brickpoint' ) ) ),
				bp_schema_head( 'Send your material list.<br>We handle the rest.' ),
				array( 'meta' => array( 'type' => 'text', 'label' => __( 'Meta line', 'brickpoint' ) ), 'buttons' => $b )
			),
		),
		'bp_cta_box' => array(
			'title' => __( 'BrickPoint CTA Box', 'brickpoint' ), 'icon' => 'eicon-button', 'section' => 'cta_box',
			'controls' => array_merge(
				bp_schema_head( 'Need SS7 for your project?' ),
				array(
					'style'   => array( 'type' => 'select', 'label' => __( 'Style', 'brickpoint' ), 'default' => 'dark', 'options' => array( 'dark' => 'Dark', 'sand' => 'Sand', 'outline' => 'Outline', 'plain' => 'Plain (centered text)' ) ),
					'split'   => array( 'type' => 'switch', 'label' => __( 'Split layout (text left, buttons right)', 'brickpoint' ) ),
					'outer'   => array( 'type' => 'select', 'label' => __( 'Section padding', 'brickpoint' ), 'default' => 'bp-section-sm', 'options' => array( 'bp-section-xs' => 'Small', 'bp-section-sm' => 'Medium', 'bp-section' => 'Large', 'bp-section-sm bp-sand' => 'Medium on sand' ) ),
					'buttons' => $b,
				)
			),
		),
		'bp_page_header' => array(
			'title' => __( 'BrickPoint Page Header', 'brickpoint' ), 'icon' => 'eicon-header', 'section' => 'page_header',
			'controls' => array_merge(
				array(
					'dynamic'  => array( 'type' => 'switch', 'label' => __( 'Dynamic (archive/term/page title)', 'brickpoint' ) ),
					'bg_image' => array( 'type' => 'media', 'label' => __( 'Background image', 'brickpoint' ) ),
					'size'     => array( 'type' => 'select', 'label' => __( 'Size', 'brickpoint' ), 'default' => 'sm', 'options' => array( 'sm' => 'Compact', 'md' => 'Medium', 'lg' => 'Large (hero with side image)' ) ),
					'eyebrow_style' => array( 'type' => 'select', 'label' => __( 'Eyebrow style', 'brickpoint' ), 'default' => 'text', 'options' => array( 'text' => 'Text', 'badge' => 'Badge' ) ),
					'eyebrow_icon'  => array( 'type' => 'icon', 'label' => __( 'Eyebrow icon', 'brickpoint' ) ),
				),
				bp_schema_head(),
				array(
					'buttons'        => $b,
					'chips'          => array( 'type' => 'textarea', 'label' => __( 'Chips (one per line)', 'brickpoint' ) ),
					'pills_taxonomy' => array( 'type' => 'select', 'label' => __( 'Filter pills', 'brickpoint' ), 'default' => '', 'options' => array( '' => 'None', 'bp_product_category' => 'Product categories', 'bp_video_category' => 'Video categories', 'bp_project_category' => 'Project categories', 'category' => 'Blog categories' ) ),
					'search'         => array( 'type' => 'switch', 'label' => __( 'Show search form', 'brickpoint' ) ),
					'breadcrumbs'    => array( 'type' => 'repeater', 'label' => __( 'Breadcrumbs', 'brickpoint' ), 'title' => 'text', 'fields' => array( 'text' => array( 'type' => 'text', 'label' => 'Text' ), 'url' => array( 'type' => 'url', 'label' => 'Link' ) ) ),
					'side_image'     => array( 'type' => 'media', 'label' => __( 'Side image (large size)', 'brickpoint' ) ),
					'side_tags'      => array( 'type' => 'textarea', 'label' => __( 'Side image tags (one per line)', 'brickpoint' ) ),
				)
			),
		),
		'bp_icon_cards' => array(
			'title' => __( 'BrickPoint Icon Cards', 'brickpoint' ), 'icon' => 'eicon-icon-box', 'section' => 'icon_cards',
			'controls' => array_merge(
				bp_schema_head(),
				array(
					'style'   => array( 'type' => 'select', 'label' => __( 'Card style', 'brickpoint' ), 'default' => 'white-3xl', 'options' => array( 'white-3xl' => 'White cards', 'dark-3xl' => 'Dark cards', 'compact' => 'Compact' ) ),
					'columns' => array( 'type' => 'select', 'label' => __( 'Columns', 'brickpoint' ), 'default' => '3', 'options' => array( '2' => '2', '3' => '3', '4' => '4' ) ),
					'bg'      => array( 'type' => 'select', 'label' => __( 'Background', 'brickpoint' ), 'default' => '', 'options' => array( '' => 'White', 'bp-sand' => 'Sand', 'bp-dark' => 'Dark' ) ),
					'padding' => array( 'type' => 'select', 'label' => __( 'Padding', 'brickpoint' ), 'default' => 'bp-section', 'options' => array( 'bp-section-xs' => 'Small', 'bp-section-sm' => 'Medium', 'bp-section' => 'Large' ) ),
					'items'   => array( 'type' => 'repeater', 'label' => __( 'Cards', 'brickpoint' ), 'title' => 'title', 'fields' => array(
						'icon' => array( 'type' => 'icon', 'label' => 'Icon' ), 'icon_color' => array( 'type' => 'select', 'label' => 'Icon color', 'default' => '', 'options' => array( '' => 'Orange', 'green' => 'Green', 'amber' => 'Amber' ) ), 'title' => array( 'type' => 'text', 'label' => 'Title' ), 'text' => array( 'type' => 'textarea', 'label' => 'Text' ),
					) ),
					'after_html' => array( 'type' => 'wysiwyg', 'label' => __( 'Note below cards (amber box)', 'brickpoint' ) ),
				)
			),
		),
		'bp_notice' => array(
			'title' => __( 'BrickPoint Notice', 'brickpoint' ), 'icon' => 'eicon-alert', 'section' => 'notice',
			'controls' => array(
				'text'    => array( 'type' => 'wysiwyg', 'label' => __( 'Text', 'brickpoint' ) ),
				'style'   => array( 'type' => 'select', 'label' => __( 'Style', 'brickpoint' ), 'default' => 'amber', 'options' => array( 'amber' => 'Amber', 'dark' => 'Dark' ) ),
				'narrow'  => array( 'type' => 'switch', 'label' => __( 'Narrow container', 'brickpoint' ) ),
				'padding' => array( 'type' => 'select', 'label' => __( 'Padding', 'brickpoint' ), 'default' => 'bp-section-xs', 'options' => array( 'bp-section-xs' => 'Small', 'bp-section-sm' => 'Medium' ) ),
			),
		),
		'bp_video_content' => array(
			'title' => __( 'BrickPoint Video + Content', 'brickpoint' ), 'icon' => 'eicon-video-camera', 'section' => 'video_content',
			'controls' => array_merge(
				array(
					'dark'        => array( 'type' => 'switch', 'label' => __( 'Dark section', 'brickpoint' ), 'default' => 'yes' ),
					'video_first' => array( 'type' => 'switch', 'label' => __( 'Video on left', 'brickpoint' ) ),
					'video'       => array( 'type' => 'text', 'label' => __( 'Video URL', 'brickpoint' ) ),
					'poster'      => array( 'type' => 'media', 'label' => __( 'Poster', 'brickpoint' ) ),
					'caption'     => array( 'type' => 'text', 'label' => __( 'Caption under video', 'brickpoint' ) ),
				),
				bp_schema_head(),
				array(
					'list'    => array( 'type' => 'textarea', 'label' => __( 'Checklist (one per line)', 'brickpoint' ) ),
					'cards'   => array( 'type' => 'repeater', 'label' => __( 'Mini cards (Mission / Vision / Values)', 'brickpoint' ), 'title' => 'title', 'fields' => array( 'icon' => array( 'type' => 'icon', 'label' => 'Icon' ), 'title' => array( 'type' => 'text', 'label' => 'Title' ), 'text' => array( 'type' => 'textarea', 'label' => 'Text' ) ) ),
					'buttons' => $b,
				)
			),
		),
		'bp_material_groups' => array(
			'title' => __( 'BrickPoint Material Groups', 'brickpoint' ), 'icon' => 'eicon-flip-box', 'section' => 'material_groups',
			'controls' => array(
				'per_group' => array( 'type' => 'number', 'label' => __( 'Products per group', 'brickpoint' ), 'default' => 4 ),
				'groups'    => array( 'type' => 'repeater', 'label' => __( 'Groups', 'brickpoint' ), 'title' => 'title', 'fields' => array( 'title' => array( 'type' => 'text', 'label' => 'Title' ), 'categories' => array( 'type' => 'text', 'label' => 'Category slugs (comma separated)' ) ) ),
			),
		),
		'bp_contact' => array(
			'title' => __( 'BrickPoint Contact Section', 'brickpoint' ), 'icon' => 'eicon-form-horizontal', 'section' => 'contact',
			'controls' => array(
				'card_title'  => array( 'type' => 'text', 'label' => __( 'Info card title', 'brickpoint' ), 'default' => __( 'Talk to BrickPoint', 'brickpoint' ) ),
				'map_title'   => array( 'type' => 'text', 'label' => __( 'Map card title', 'brickpoint' ), 'default' => __( 'Office Map', 'brickpoint' ) ),
				'map_text'    => array( 'type' => 'text', 'label' => __( 'Map card text', 'brickpoint' ) ),
				'map_url'     => array( 'type' => 'url', 'label' => __( 'Map URL (blank = theme setting)', 'brickpoint' ) ),
				'form_title'  => array( 'type' => 'text', 'label' => __( 'Form title', 'brickpoint' ), 'default' => __( 'Request a Quotation', 'brickpoint' ) ),
				'form_intro'  => array( 'type' => 'text', 'label' => __( 'Form intro', 'brickpoint' ) ),
				'form_button' => array( 'type' => 'text', 'label' => __( 'Form button', 'brickpoint' ), 'default' => __( 'Send Request', 'brickpoint' ) ),
			),
		),
		'bp_prose' => array(
			'title' => __( 'BrickPoint Prose Page', 'brickpoint' ), 'icon' => 'eicon-document-file', 'section' => 'prose',
			'controls' => array(
				'eyebrow' => array( 'type' => 'text', 'label' => __( 'Eyebrow', 'brickpoint' ) ),
				'title'   => array( 'type' => 'text', 'label' => __( 'Title', 'brickpoint' ) ),
				'content' => array( 'type' => 'wysiwyg', 'label' => __( 'Content', 'brickpoint' ) ),
			),
		),
	);
}

/**
 * Post grid schema factory.
 *
 * @param string $type  Post type.
 * @param string $title Widget title.
 * @param string $icon  Icon.
 * @return array
 */
function bp_schema_post_grid( $type, $title, $icon ) {
	$controls = array_merge(
		bp_schema_head(),
		array(
			'head_layout' => array( 'type' => 'select', 'label' => __( 'Heading layout', 'brickpoint' ), 'default' => '', 'options' => array( '' => 'Centered heading', 'row' => 'Title + link row' ) ),
			'count'       => array( 'type' => 'number', 'label' => __( 'Count', 'brickpoint' ), 'default' => 'bp_location' === $type ? 4 : 3 ),
			'columns'     => array( 'type' => 'select', 'label' => __( 'Columns', 'brickpoint' ), 'default' => 'bp_location' === $type ? '2' : '3', 'options' => array( '2' => '2', '3' => '3', '4' => '4' ) ),
			'notice'      => array( 'type' => 'wysiwyg', 'label' => __( 'Notice (amber box)', 'brickpoint' ) ),
			'padding'     => array( 'type' => 'select', 'label' => __( 'Padding', 'brickpoint' ), 'default' => 'bp-section', 'options' => array( 'bp-section-xs' => 'Small', 'bp-section-sm' => 'Medium', 'bp-section' => 'Large' ) ),
			'bg'          => array( 'type' => 'select', 'label' => __( 'Background', 'brickpoint' ), 'default' => '', 'options' => array( '' => 'White', 'bp-sand' => 'Sand', 'bp-dark' => 'Dark', 'bp-charcoal' => 'Charcoal' ) ),
			'button_text' => array( 'type' => 'text', 'label' => __( 'Button text', 'brickpoint' ) ),
			'button_url'  => array( 'type' => 'url', 'label' => __( 'Button link', 'brickpoint' ) ),
			'button_style' => array( 'type' => 'select', 'label' => __( 'Button style', 'brickpoint' ), 'default' => 'btn-outline', 'options' => array( 'btn-outline' => 'Outline', 'btn-brick' => 'Brick', 'btn-ghost' => 'Ghost (dark bg)' ) ),
		)
	);
	if ( in_array( $type, array( 'bp_video', 'bp_project' ), true ) ) {
		$controls['featured'] = array( 'type' => 'switch', 'label' => __( 'Featured only', 'brickpoint' ) );
	}
	if ( in_array( $type, array( 'bp_location', 'bp_project' ), true ) ) {
		$controls['full'] = array( 'type' => 'switch', 'label' => __( 'Full card (all details)', 'brickpoint' ), 'default' => 'yes' );
	}
	$sections = array( 'bp_video' => 'video_grid', 'bp_project' => 'project_grid', 'bp_location' => 'location_grid', 'post' => 'blog_grid' );
	return array( 'title' => $title, 'icon' => $icon, 'section' => $sections[ $type ], 'controls' => $controls );
}

/**
 * Template widgets: render theme template parts (single/archive bodies, header, footer,
 * cards) so Elementor Pro Theme Builder templates can use them with dynamic content.
 *
 * @return array<string,array{title:string,icon:string,part:string,args?:array,controls?:array}>
 */
function bp_template_widget_schema() {
	return array(
		'bp_site_header'     => array( 'title' => __( 'BrickPoint Site Header', 'brickpoint' ), 'icon' => 'eicon-header', 'part' => 'template-parts/header/site-header' ),
		'bp_site_footer'     => array( 'title' => __( 'BrickPoint Site Footer', 'brickpoint' ), 'icon' => 'eicon-footer', 'part' => 'template-parts/footer/site-footer' ),
		'bp_product_details' => array( 'title' => __( 'BrickPoint Product Details', 'brickpoint' ), 'icon' => 'eicon-product-info', 'part' => 'template-parts/single/product', 'post_type' => 'bp_product' ),
		'bp_video_details'   => array( 'title' => __( 'BrickPoint Video Details', 'brickpoint' ), 'icon' => 'eicon-video-camera', 'part' => 'template-parts/single/video', 'post_type' => 'bp_video' ),
		'bp_project_details' => array( 'title' => __( 'BrickPoint Project Details', 'brickpoint' ), 'icon' => 'eicon-post', 'part' => 'template-parts/single/project', 'post_type' => 'bp_project' ),
		'bp_location_details' => array( 'title' => __( 'BrickPoint Location Details', 'brickpoint' ), 'icon' => 'eicon-map-pin', 'part' => 'template-parts/single/location', 'post_type' => 'bp_location' ),
		'bp_post_details'    => array( 'title' => __( 'BrickPoint Blog Post', 'brickpoint' ), 'icon' => 'eicon-post-content', 'part' => 'template-parts/single/post', 'post_type' => 'post' ),
		'bp_archive_grid'    => array( 'title' => __( 'BrickPoint Archive (header + grid)', 'brickpoint' ), 'icon' => 'eicon-archive', 'part' => 'template-parts/archive/loop' ),
		'bp_blog_archive'    => array( 'title' => __( 'BrickPoint Blog Archive', 'brickpoint' ), 'icon' => 'eicon-archive-posts', 'part' => 'template-parts/archive/blog' ),
		'bp_product_card'    => array( 'title' => __( 'BrickPoint Product Card', 'brickpoint' ), 'icon' => 'eicon-product-images', 'part' => 'template-parts/card-product', 'post_type' => 'bp_product' ),
		'bp_video_card'      => array( 'title' => __( 'BrickPoint Video Card', 'brickpoint' ), 'icon' => 'eicon-play', 'part' => 'template-parts/card-video', 'post_type' => 'bp_video' ),
		'bp_project_card'    => array( 'title' => __( 'BrickPoint Project Card', 'brickpoint' ), 'icon' => 'eicon-image-rollover', 'part' => 'template-parts/card-project', 'post_type' => 'bp_project', 'args' => array( 'full' => true ) ),
		'bp_location_card'   => array( 'title' => __( 'BrickPoint Location Card', 'brickpoint' ), 'icon' => 'eicon-google-maps', 'part' => 'template-parts/card-location', 'post_type' => 'bp_location', 'args' => array( 'full' => true ) ),
		'bp_blog_card'       => array( 'title' => __( 'BrickPoint Blog Card', 'brickpoint' ), 'icon' => 'eicon-post-excerpt', 'part' => 'template-parts/card-blog', 'post_type' => 'post' ),
		'bp_whatsapp_button' => array( 'title' => __( 'BrickPoint WhatsApp Button', 'brickpoint' ), 'icon' => 'eicon-button', 'part' => '', 'controls' => array(
			'text'    => array( 'type' => 'text', 'label' => __( 'Text', 'brickpoint' ), 'default' => __( 'Order on WhatsApp', 'brickpoint' ) ),
			'message' => array( 'type' => 'textarea', 'label' => __( 'Pre-filled message (blank = current product / default)', 'brickpoint' ) ),
			'size'    => array( 'type' => 'select', 'label' => __( 'Size', 'brickpoint' ), 'default' => '', 'options' => array( '' => 'Default', 'sm' => 'Small', 'lg' => 'Large' ) ),
		) ),
		'bp_social_links'    => array( 'title' => __( 'BrickPoint Social Links', 'brickpoint' ), 'icon' => 'eicon-social-icons', 'part' => 'template-parts/footer/social-links' ),
		'bp_contact_form'    => array( 'title' => __( 'BrickPoint Quotation Form', 'brickpoint' ), 'icon' => 'eicon-form-horizontal', 'part' => '', 'controls' => array(
			'title'  => array( 'type' => 'text', 'label' => __( 'Title', 'brickpoint' ), 'default' => __( 'Request a Quotation', 'brickpoint' ) ),
			'intro'  => array( 'type' => 'text', 'label' => __( 'Intro', 'brickpoint' ) ),
			'button' => array( 'type' => 'text', 'label' => __( 'Button', 'brickpoint' ), 'default' => __( 'Send Request', 'brickpoint' ) ),
		) ),
		'bp_breadcrumbs'     => array( 'title' => __( 'BrickPoint Breadcrumbs', 'brickpoint' ), 'icon' => 'eicon-navigator', 'part' => '', 'controls' => array( 'dark' => array( 'type' => 'switch', 'label' => __( 'Dark', 'brickpoint' ) ) ) ),
	);
}
