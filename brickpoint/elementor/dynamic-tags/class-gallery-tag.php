<?php
/**
 * Dynamic tag: BrickPoint gallery (product / project gallery attachment IDs).
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
 * Gallery tag.
 */
class Gallery_Tag extends Data_Tag {

	/** @inheritDoc */
	public function get_name() {
		return 'brickpoint-gallery';
	}

	/** @inheritDoc */
	public function get_title() {
		return __( 'BrickPoint Gallery', 'brickpoint' );
	}

	/** @inheritDoc */
	public function get_group() {
		return 'brickpoint';
	}

	/** @inheritDoc */
	public function get_categories() {
		return array( Module::GALLERY_CATEGORY );
	}

	/** @inheritDoc */
	protected function register_controls() {
		$this->add_control( 'field', array( 'label' => __( 'Field', 'brickpoint' ), 'type' => Controls_Manager::SELECT, 'options' => array( 'product_gallery' => __( 'Product gallery', 'brickpoint' ), 'project_gallery' => __( 'Project gallery', 'brickpoint' ) ), 'default' => 'product_gallery' ) );
		$this->add_control( 'include_featured', array( 'label' => __( 'Include featured image first', 'brickpoint' ), 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ) );
	}

	/** @inheritDoc */
	public function get_value( array $options = array() ) {
		$id   = get_the_ID();
		$meta = 'project_gallery' === $this->get_settings( 'field' ) ? '_bpp_gallery' : '_bp_gallery';
		$ids  = array_filter( array_map( 'absint', preg_split( '/[\s,]+/', (string) get_post_meta( $id, $meta, true ) ) ) );
		if ( 'yes' === $this->get_settings( 'include_featured' ) && has_post_thumbnail( $id ) ) {
			array_unshift( $ids, get_post_thumbnail_id( $id ) );
		}
		$out = array();
		foreach ( array_unique( $ids ) as $aid ) {
			$out[] = array( 'id' => $aid );
		}
		return $out;
	}
}
