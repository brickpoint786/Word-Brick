<?php
/**
 * Template-part widgets (header, footer, single bodies, cards, archive loops,
 * WhatsApp button, contact form, breadcrumbs).
 *
 * @package BrickPoint
 */

namespace BrickPoint\Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders theme template parts inside Elementor with the current queried post.
 */
class Template_Widget extends Section_Widget {

	/** @inheritDoc */
	protected function schema_source() {
		return bp_template_widget_schema();
	}

	/** @inheritDoc */
	protected function register_controls() {
		$controls = isset( $this->bp_schema['controls'] ) ? $this->bp_schema['controls'] : array();
		$this->start_controls_section( 'bp_sec_content', array( 'label' => __( 'Content', 'brickpoint' ) ) );
		if ( ! empty( $this->bp_schema['post_type'] ) ) {
			$this->add_control( 'preview_id', array(
				'label'       => __( 'Preview item (editor only)', 'brickpoint' ),
				'type'        => \Elementor\Controls_Manager::NUMBER,
				'description' => __( 'Post ID used when editing outside a single template. Front end always uses the current post.', 'brickpoint' ),
			) );
		}
		if ( empty( $controls ) ) {
			$this->add_control( 'bp_info', array( 'type' => \Elementor\Controls_Manager::RAW_HTML, 'raw' => __( 'Dynamic block: content comes from the current post / theme settings and is editable in WordPress.', 'brickpoint' ), 'content_classes' => 'elementor-descriptor' ) );
		}
		foreach ( $controls as $key => $c ) {
			$this->add_schema_control( $this, $key, $c );
		}
		$this->end_controls_section();
	}

	/** @inheritDoc */
	protected function render() {
		global $post;
		$settings = $this->get_settings_for_display();
		$part     = $this->bp_schema['part'];
		$type     = isset( $this->bp_schema['post_type'] ) ? $this->bp_schema['post_type'] : '';
		$switched = false;

		if ( $type && ( ! $post || get_post_type( $post ) !== $type ) ) {
			$pid = ! empty( $settings['preview_id'] ) ? (int) $settings['preview_id'] : 0;
			if ( ! $pid ) {
				$found = get_posts( array( 'post_type' => $type, 'posts_per_page' => 1, 'fields' => 'ids' ) );
				$pid   = $found ? (int) $found[0] : 0;
			}
			if ( $pid ) {
				$post = get_post( $pid ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				setup_postdata( $post );
				$switched = true;
			}
		}

		echo '<div class="bp-widget bp-widget-' . esc_attr( $this->bp_id ) . '">';
		switch ( $this->bp_id ) {
			case 'bp_whatsapp_button':
				$msg = ! empty( $settings['message'] ) ? $settings['message'] : '';
				if ( ! $msg && $post && 'bp_product' === get_post_type( $post ) ) {
					$url = bp_product_whatsapp_url( $post->ID );
				} else {
					$url = $msg ? bp_whatsapp_url( $msg ) : bp_default_whatsapp_url();
				}
				echo bp_whatsapp_button( $url, $settings['text'], $settings['size'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;
			case 'bp_contact_form':
				echo bp_contact_form_shortcode( array( 'title' => $settings['title'], 'intro' => $settings['intro'], 'button' => $settings['button'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				break;
			case 'bp_breadcrumbs':
				bp_breadcrumbs( bp_auto_breadcrumbs(), 'yes' === $settings['dark'] );
				break;
			default:
				if ( $part ) {
					$args = isset( $this->bp_schema['args'] ) ? $this->bp_schema['args'] : array();
					get_template_part( $part, null, $args );
				}
		}
		echo '</div>';

		if ( $switched ) {
			wp_reset_postdata();
		}
	}
}
