<?php

/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Elements_Ext_Section' ) ) {

	/**
	 * Define Jet_Elements_Ext_Section class
	 */
	class Jet_Elements_Ext_Section {

		/**
		 * [$parallax_sections description]
		 * @var array
		 */
		public $parallax_sections = array();

		/**
		 * A reference to an instance of this class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    object
		 */
		private static $instance = null;

		/**
		 * Init Handler
		 */
		public function init() {

			$avaliable_extensions = jet_elements_settings()->get( 'avaliable_extensions', jet_elements_settings()->default_avaliable_extensions );

			if ( ! filter_var( $avaliable_extensions['section_parallax'], FILTER_VALIDATE_BOOLEAN ) ) {
				return false;
			}

			add_action( 'elementor/element/section/section_layout/after_section_end', array( $this, 'after_section_end' ), 10, 2 );

			add_action( 'elementor/element/container/section_layout/after_section_end', array( $this, 'after_section_end' ), 10, 2 );

			add_action( 'elementor/frontend/element/before_render', array( $this, 'section_before_render' ) );

			add_action( 'elementor/frontend/section/before_render', array( $this, 'section_before_render' ) );

			add_action( 'elementor/frontend/container/before_render', array( $this, 'section_before_render' ), 10, 1 );

			add_action( 'elementor/frontend/before_enqueue_scripts', array( $this, 'enqueue_scripts' ), 9 );
		}

		/**
		 * After section_layout callback
		 *
		 * @param  object $obj
		 * @param  array $args
		 * @return void
		 */
		public function after_section_end( $obj, $args ) {

			if ( class_exists( 'Jet_Parallax' ) ) {
				return false;
			}

			$obj->start_controls_section(
				'section_parallax',
				array(
					'label' => esc_html__( 'Section Parallax', 'jet-elements' ),
					'tab'   => Elementor\Controls_Manager::TAB_LAYOUT,
				)
			);

			if ( \Elementor\Plugin::$instance->breakpoints && method_exists( \Elementor\Plugin::$instance->breakpoints, 'get_active_breakpoints') ) {
				$active_breakpoints = \Elementor\Plugin::$instance->breakpoints->get_active_breakpoints();
				$breakpoints_list   = array();

				foreach ($active_breakpoints as $key => $value) {
					$breakpoints_list[$key] = $value->get_label();
				}

				$breakpoints_list['desktop'] = 'Desktop';
				$breakpoints_list            = array_reverse($breakpoints_list);
			} else {
				$breakpoints_list = array(
					'desktop' => 'Desktop',
					'tablet'  => 'Tablet',
					'mobile'  => 'Mobile'
				);
			}

			$repeater = new Elementor\Repeater();

			$repeater->add_responsive_control(
				'jet_parallax_layout_image',
				array(
					'label'   => esc_html__( 'Image', 'jet-elements' ),
					'type'    => Elementor\Controls_Manager::MEDIA,
					'dynamic' => array( 'active' => true ),
					'selectors' => array(
						'{{WRAPPER}} {{CURRENT_ITEM}}.jet-parallax-section__layout .jet-parallax-section__image' => 'background-image: url("{{URL}}");'
					),
					'frontend_available' => true,
					'render_type'        => 'template',
				)
			);

			$repeater->add_control(
				'jet_parallax_layout_speed',
				array(
					'label'      => esc_html__( 'Parallax Speed(%)', 'jet-elements' ),
					'type'       => Elementor\Controls_Manager::SLIDER,
					'size_units' => array( '%', 'custom' ),
					'range'      => array(
						'%' => array(
							'min'  => 1,
							'max'  => 100,
						),
					),
					'default' => array(
						'size' => 50,
						'unit' => '%',
					),
				)
			);

			$repeater->add_control(
				'jet_parallax_layout_type',
				array(
					'label'   => esc_html__( 'Parallax Type', 'jet-elements' ),
					'type'    => Elementor\Controls_Manager::SELECT,
					'default' => 'scroll',
					'options' => array(
						'none'     => esc_html__( 'None', 'jet-elements' ),
						'scroll'   => esc_html__( 'Vertical Scroll', 'jet-elements' ),
						'h-scroll' => esc_html__( 'Horizontal Scroll', 'jet-elements' ),
						'mouse'    => esc_html__( 'Mouse Move', 'jet-elements' ),
						'zoom'     => esc_html__( 'Scrolling Zoom', 'jet-elements' ),
						'rotate'   => esc_html__( 'Scrolling Rotate', 'jet-elements' ),
						'blur'     => esc_html__( 'Scrolling Blur', 'jet-elements' ),
						'opacity'  => esc_html__( 'Scrolling Transparency', 'jet-elements' ),
					),
				)
			);

			$repeater->add_control(
				'jet_parallax_layout_direction',
				array(
					'label'   => esc_html__( 'Direction', 'jet-elements' ),
					'type'    => Elementor\Controls_Manager::SELECT,
					'default' => '1',
					'options' => array(
						'1'  => esc_html__( 'Down / To Right', 'jet-elements' ),
						'-1' => esc_html__( 'Up / To Left', 'jet-elements' ),
					),
					'condition' => array(
						'jet_parallax_layout_type' => array( 'scroll', 'h-scroll', 'rotate' ),
					),
				)
			);

			$repeater->add_control(
				'jet_parallax_layout_fx_direction',
				array(
					'label'   => esc_html__( 'Direction', 'jet-elements' ),
					'type'    => Elementor\Controls_Manager::SELECT,
					'default' => 'fade-in',
					'options' => array(
						'fade-in'  => esc_html__( 'Fade In', 'jet-elements' ),
						'fade-out' => esc_html__( 'Fade Out', 'jet-elements' ),
					),
					'condition' => array(
						'jet_parallax_layout_type' => array( 'blur', 'opacity' ),
					),
				)
			);

			$repeater->add_control(
				'jet_parallax_layout_z_index',
				array(
					'label'    => esc_html__( 'Z-Index', 'jet-elements' ),
					'type'     => Elementor\Controls_Manager::NUMBER,
					'min'      => 0,
					'max'      => 99,
					'step'     => 1,
				)
			);

			$repeater->add_responsive_control(
				'jet_parallax_layout_bg_x',
				array(
					'label'   => esc_html__( 'Background X Position(%)', 'jet-elements' ),
					'type'    => Elementor\Controls_Manager::NUMBER,
					'default' => 50,
					'min'     => -200,
					'max'     => 200,
					'step'    => 1,
					'frontend_available' => true,
					'render_type'        => 'template',
				)
			);

			$repeater->add_responsive_control(
				'jet_parallax_layout_bg_y',
				array(
					'label'   => esc_html__( 'Background Y Position(%)', 'jet-elements' ),
					'type'    => Elementor\Controls_Manager::NUMBER,
					'default' => 50,
					'min'     => -200,
					'max'     => 200,
					'step'    => 1,
					'frontend_available' => true,
					'render_type'        => 'template',
				)
			);

			$repeater->add_responsive_control(
				'jet_parallax_layout_bg_size',
				array(
					'label'   => esc_html__( 'Background Size', 'jet-elements' ),
					'type'    => Elementor\Controls_Manager::SELECT,
					'default' => 'auto',
					'options' => array(
						'auto'    => esc_html__( 'Auto', 'jet-elements' ),
						'cover'   => esc_html__( 'Cover', 'jet-elements' ),
						'contain' => esc_html__( 'Contain', 'jet-elements' ),
					),
					'selectors' => array(
						'{{WRAPPER}} {{CURRENT_ITEM}}.jet-parallax-section__layout .jet-parallax-section__image' => 'background-size: {{VALUE}};'
					),
					'frontend_available' => true,
					'render_type'        => 'template',
				)
			);

			$repeater->add_control(
				'jet_parallax_layout_animation_prop',
				array(
					'label'   => esc_html__( 'Animation Property', 'jet-elements' ),
					'type'    => Elementor\Controls_Manager::SELECT,
					'default' => 'transform',
					'options' => array(
						'bgposition'  => esc_html__( 'Background Position', 'jet-elements' ),
						'transform'   => esc_html__( 'Transform', 'jet-elements' ),
						'transform3d' => esc_html__( 'Transform 3D', 'jet-elements' ),
					),
					'condition' => array(
						'jet_parallax_layout_type' => array( 'scroll', 'h-scroll', 'mouse' ),
					),
				)
			);

			$repeater->add_control(
				'jet_parallax_layout_on',
				array(
					'label'       => __( 'Enable On Device', 'jet-elements' ),
					'type'        => Elementor\Controls_Manager::SELECT2,
					'multiple'    => true,
					'label_block' => 'true',
					'default'     => array(
						'desktop',
						'tablet',
					),
					'options' => $breakpoints_list,
				)
			);

			$obj->add_control(
				'jet_parallax_layout_list',
				array(
					'label'              => '<b>' . esc_html__( 'Layouts', 'jet-elements' ) . '</b>',
					'type'               => 'jet-repeater',
					'fields'             => $repeater->get_controls(),
					'default'            => array(),
					'prevent_empty'      => false,
					'frontend_available' => true,
					'style_transfer'     => false,
				)
			);

			$obj->end_controls_section();
		}

		/**
		 * Elementor before section render callback
		 *
		 * @param  object $obj
		 * @return void
		 */
		public function section_before_render( $obj ) {
			$data     = $obj->get_data();
			$type     = isset( $data['elType'] ) ? $data['elType'] : 'section';
			$settings = $data['settings'];

			if ( 'section' === $type || 'container' === $type ) {

				if ( isset( $settings['jet_parallax_layout_list'] ) ) {
					$parallax_layout_list = method_exists( $obj, 'get_settings_for_display' ) ? $obj->get_settings_for_display( 'jet_parallax_layout_list' ) : $settings['jet_parallax_layout_list'];

					if ( is_array( $parallax_layout_list ) && ! empty( $parallax_layout_list ) ) {

						foreach ( $parallax_layout_list as $key => $layout ) {
							if ( empty( $layout['jet_parallax_layout_image']['url'] )
								&& empty( $layout['jet_parallax_layout_image_tablet']['url'] )
								&& empty( $layout['jet_parallax_layout_image_mobile']['url'] )
							) {
								continue;
							}

							if ( ! in_array( $data['id'], $this->parallax_sections ) ) {
								$this->parallax_sections[ $data['id'] ] = $parallax_layout_list;
							}
						}
					}

				}
			}
		}

		/**
		 * [enqueue_scripts description]
		 *
		 * @return void
		 */
		public function enqueue_scripts() {

			$has_mouse_type = false;
			$is_element_cache_active = \Elementor\Plugin::$instance->experiments->is_feature_active( 'e_element_cache' );

			if ( ! empty( $this->parallax_sections ) ) {

				foreach ( $this->parallax_sections as $parallax_items ) {
					foreach ( $parallax_items as $parallax_item ) {
						if ( 'mouse' === $parallax_item['jet_parallax_layout_type'] ) {
							$has_mouse_type = true;
						}
					}
				}

				//jet_elements_assets()->localize_data['jetParallaxSections'] = $this->parallax_sections;
			}

			//Register and enqueue parallax stylesheets
			wp_enqueue_style( 'jet-elements' );

			if ( $has_mouse_type || jet_elements()->elementor()->preview->is_preview_mode() || $is_element_cache_active ) {
				wp_enqueue_script( 'jet-tween-js' );
			}
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return object
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}
}

/**
 * Returns instance of Jet_Elements_Ext_Section
 *
 * @return object
 */
function jet_elements_ext_section() {
	return Jet_Elements_Ext_Section::get_instance();
}
