<?php
/**
 * Dynamic tag: BrickPoint URL field.
 *
 * @package BrickPoint
 */

namespace BrickPoint\Elementor;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * URL tag (links, video URLs, WhatsApp URLs).
 */
class Url_Tag extends Data_Tag {

	/** @inheritDoc */
	public function get_name() {
		return 'brickpoint-url';
	}

	/** @inheritDoc */
	public function get_title() {
		return __( 'BrickPoint URL', 'brickpoint' );
	}

	/** @inheritDoc */
	public function get_group() {
		return 'brickpoint';
	}

	/** @inheritDoc */
	public function get_categories() {
		return array( Module::URL_CATEGORY );
	}

	/** @inheritDoc */
	protected function register_controls() {
		$opts = array();
		foreach ( bp_dynamic_fields() as $k => $f ) {
			if ( 'url' === $f['type'] ) {
				$opts[ $k ] = $f['label'];
			}
		}
		$this->add_control( 'field', array( 'label' => __( 'Field', 'brickpoint' ), 'type' => Controls_Manager::SELECT, 'options' => $opts, 'default' => 'product_whatsapp' ) );
	}

	/** @inheritDoc */
	public function get_value( array $options = array() ) {
		return bp_dynamic_value( $this->get_settings( 'field' ), get_the_ID() );
	}
}
