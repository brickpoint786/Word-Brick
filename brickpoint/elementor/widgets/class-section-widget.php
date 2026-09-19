<?php
/**
 * Schema-driven section widget.
 *
 * @package BrickPoint
 */

namespace BrickPoint\Elementor;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * One class serves every section widget; the schema id comes from default args.
 */
class Section_Widget extends Widget_Base {

	/**
	 * Schema id.
	 *
	 * @var string
	 */
	protected $bp_id = '';

	/**
	 * Schema.
	 *
	 * @var array
	 */
	protected $bp_schema = array();

	/**
	 * Constructor.
	 *
	 * @param array $data Data.
	 * @param array $args Args.
	 */
	public function __construct( $data = array(), $args = null ) {
		$args        = (array) $args;
		$this->bp_id = isset( $args['bp_schema_id'] ) ? $args['bp_schema_id'] : ( isset( $data['widgetType'] ) ? $data['widgetType'] : '' );
		$all         = $this->schema_source();
		$this->bp_schema = isset( $all[ $this->bp_id ] ) ? $all[ $this->bp_id ] : array( 'title' => $this->bp_id, 'icon' => 'eicon-apps', 'controls' => array() );
		parent::__construct( $data, $args );
	}

	/**
	 * Schema source.
	 *
	 * @return array
	 */
	protected function schema_source() {
		return bp_widget_schema();
	}

	/** @inheritDoc */
	public function get_name() {
		return $this->bp_id;
	}

	/** @inheritDoc */
	public function get_title() {
		return $this->bp_schema['title'];
	}

	/** @inheritDoc */
	public function get_icon() {
		return $this->bp_schema['icon'];
	}

	/** @inheritDoc */
	public function get_categories() {
		return array( 'brickpoint' );
	}

	/** @inheritDoc */
	public function get_keywords() {
		return array( 'brickpoint', 'brick', str_replace( array( 'bp_', '_' ), array( '', ' ' ), $this->bp_id ) );
	}

	/** @inheritDoc */
	public function get_style_depends() {
		return array( 'brickpoint-main' );
	}

	/** @inheritDoc */
	public function get_script_depends() {
		return array( 'brickpoint-main' );
	}

	/**
	 * Build controls from schema.
	 */
	protected function register_controls() {
		$controls = isset( $this->bp_schema['controls'] ) ? $this->bp_schema['controls'] : array();
		$groups   = array();
		foreach ( $controls as $key => $c ) {
			$g = isset( $c['group'] ) ? $c['group'] : 'content';
			$groups[ $g ]['label']          = isset( $c['group_label'] ) ? $c['group_label'] : ( isset( $groups[ $g ]['label'] ) ? $groups[ $g ]['label'] : __( 'Content', 'brickpoint' ) );
			$groups[ $g ]['controls'][ $key ] = $c;
		}
		foreach ( $groups as $gid => $group ) {
			$this->start_controls_section( 'bp_sec_' . $gid, array( 'label' => $group['label'], 'tab' => Controls_Manager::TAB_CONTENT ) );
			foreach ( $group['controls'] as $key => $c ) {
				$this->add_schema_control( $this, $key, $c );
			}
			$this->end_controls_section();
		}
		$this->start_controls_section( 'bp_sec_advanced_note', array( 'label' => __( 'Design', 'brickpoint' ), 'tab' => Controls_Manager::TAB_STYLE ) );
		$this->add_control( 'bp_note', array( 'type' => Controls_Manager::RAW_HTML, 'raw' => __( 'Colours, typography and radius follow the BrickPoint global settings (Appearance → Customize → BrickPoint). Use the Advanced tab for spacing or custom CSS.', 'brickpoint' ), 'content_classes' => 'elementor-descriptor' ) );
		$this->end_controls_section();
	}

	/**
	 * Add a control (to the widget or a repeater) from a schema definition.
	 *
	 * @param object $stack Controls stack.
	 * @param string $key   Key.
	 * @param array  $c     Definition.
	 */
	protected function add_schema_control( $stack, $key, $c ) {
		$base = array( 'label' => isset( $c['label'] ) ? $c['label'] : $key );
		if ( isset( $c['description'] ) ) {
			$base['description'] = $c['description'];
		}
		if ( isset( $c['default'] ) && 'media' !== $c['type'] && 'icon' !== $c['type'] && 'url' !== $c['type'] && 'repeater' !== $c['type'] ) {
			$base['default'] = $c['default'];
		}
		switch ( $c['type'] ) {
			case 'text':
				$stack->add_control( $key, $base + array( 'type' => Controls_Manager::TEXT, 'label_block' => true, 'dynamic' => array( 'active' => true ) ) );
				break;
			case 'textarea':
				$stack->add_control( $key, $base + array( 'type' => Controls_Manager::TEXTAREA, 'rows' => isset( $c['rows'] ) ? $c['rows'] : 4, 'dynamic' => array( 'active' => true ) ) );
				break;
			case 'wysiwyg':
				$stack->add_control( $key, $base + array( 'type' => Controls_Manager::WYSIWYG, 'dynamic' => array( 'active' => true ) ) );
				break;
			case 'number':
				$stack->add_control( $key, $base + array( 'type' => Controls_Manager::NUMBER, 'min' => 0 ) );
				break;
			case 'select':
				$stack->add_control( $key, $base + array( 'type' => Controls_Manager::SELECT, 'options' => $c['options'] ) );
				break;
			case 'switch':
				$stack->add_control( $key, $base + array( 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => isset( $c['default'] ) ? $c['default'] : '' ) );
				break;
			case 'media':
				$stack->add_control( $key, $base + array( 'type' => Controls_Manager::MEDIA, 'dynamic' => array( 'active' => true ) ) );
				break;
			case 'url':
				$stack->add_control( $key, $base + array( 'type' => Controls_Manager::URL, 'placeholder' => 'https:// or page:slug', 'options' => array( 'url', 'is_external' ), 'dynamic' => array( 'active' => true ) ) );
				break;
			case 'icon':
				$stack->add_control( $key, $base + array( 'type' => Controls_Manager::ICONS, 'default' => isset( $c['default'] ) ? array( 'value' => 'bpi bpi-' . $c['default'], 'library' => 'brickpoint' ) : array(), 'recommended' => array( 'brickpoint' => array_keys( bp_icon_set() ) ), 'skin' => 'inline', 'label_block' => false ) );
				break;
			case 'color':
				$stack->add_control( $key, $base + array( 'type' => Controls_Manager::COLOR ) );
				break;
			case 'repeater':
				$rep = new Repeater();
				foreach ( $c['fields'] as $fk => $fc ) {
					$this->add_schema_control( $rep, $fk, $fc );
				}
				$stack->add_control( $key, $base + array( 'type' => Controls_Manager::REPEATER, 'fields' => $rep->get_controls(), 'title_field' => ! empty( $c['title'] ) ? '{{{ ' . $c['title'] . ' }}}' : '' ) );
				break;
		}
	}

	/**
	 * Render on front end / editor.
	 */
	protected function render() {
		$settings = bp_widget_settings_to_section( $this->get_settings_for_display(), $this->bp_schema );
		echo '<div class="bp-widget bp-widget-' . esc_attr( $this->bp_id ) . '">';
		bp_render_section( $this->bp_schema['section'], $settings );
		echo '</div>';
	}

	/** No JS template; always server rendered. */
	protected function content_template() {}
}
