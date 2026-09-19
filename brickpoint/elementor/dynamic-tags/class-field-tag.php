<?php
/**
 * Dynamic tag: BrickPoint text field.
 *
 * @package BrickPoint
 */

namespace BrickPoint\Elementor;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Text field tag.
 */
class Field_Tag extends Tag {

	/** @inheritDoc */
	public function get_name() {
		return 'brickpoint-field';
	}

	/** @inheritDoc */
	public function get_title() {
		return __( 'BrickPoint Field', 'brickpoint' );
	}

	/** @inheritDoc */
	public function get_group() {
		return 'brickpoint';
	}

	/** @inheritDoc */
	public function get_categories() {
		return array( Module::TEXT_CATEGORY, Module::NUMBER_CATEGORY, Module::POST_META_CATEGORY );
	}

	/** Options list. */
	protected function options( $type ) {
		$out = array();
		foreach ( bp_dynamic_fields() as $k => $f ) {
			if ( $f['type'] === $type ) {
				$out[ $k ] = $f['label'];
			}
		}
		return $out;
	}

	/** @inheritDoc */
	protected function register_controls() {
		$this->add_control( 'field', array( 'label' => __( 'Field', 'brickpoint' ), 'type' => Controls_Manager::SELECT, 'options' => $this->options( 'text' ), 'default' => 'product_price' ) );
		$this->add_control( 'bp_fallback', array( 'label' => __( 'Fallback', 'brickpoint' ), 'type' => Controls_Manager::TEXT ) );
	}

	/** @inheritDoc */
	public function render() {
		$v = bp_dynamic_value( $this->get_settings( 'field' ), get_the_ID() );
		if ( '' === $v ) {
			$v = (string) $this->get_settings( 'bp_fallback' );
		}
		echo wp_kses_post( $v );
	}
}
