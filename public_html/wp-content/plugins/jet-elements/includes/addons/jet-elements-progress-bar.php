<?php
/**
 * Class: Jet_Elements_Progress_Bar
 * Name: Progress Bar
 * Slug: jet-progress-bar
 */

namespace Elementor;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Utils;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Jet_Elements_Progress_Bar extends Jet_Elements_Base {

	public function get_name() {
		return 'jet-progress-bar';
	}

	public function get_title() {
		return esc_html__( 'Progress Bar', 'jet-elements' );
	}

	public function get_icon() {
		return 'jet-elements-icon-progress-bar';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/how-to-display-the-progress-on-elementor-built-pages-with-jetelements-progress-bar-and-circle-progress-widgets/';
	}

	public function get_categories() {
		return array( 'cherry' );
	}

	protected function is_dynamic_content(): bool {
		return false;
	}

	public function get_style_depends() { 
		return array( 'jet-progress-bar', 'jet-progress-bar-skin' ); 
	}

	public function get_script_depends() {
		return array( 'jet-anime-js' );
	}

	protected function register_controls() {
		$css_scheme = apply_filters(
			'jet-elements/progress-bar/css-scheme',
			array(
				'instance'         => '.jet-progress-bar',
				'title'            => '.jet-progress-bar__title',
				'title_icon'       => '.jet-progress-bar__title-icon',
				'title_text'       => '.jet-progress-bar__title-text',
				'progress_wrapper' => '.jet-progress-bar__wrapper',
				'status_bar'       => '.jet-progress-bar__status-bar',
				'percent'          => '.jet-progress-bar__percent',
				'completed_bar'    => '.jet-progress-bar--completed .jet-progress-bar__status-bar',
			)
		);

		$this->start_controls_section(
			'section_progress',
			array(
				'label' => esc_html__( 'Progress Bar', 'jet-elements' ),
			)
		);

		$this->add_control(
			'progress_type',
			array(
				'label' => esc_html__( 'Type', 'jet-elements' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'type-1',
				'options' => array(
					'type-1' => esc_html__( 'Inside the bar', 'jet-elements' ),
					'type-2' => esc_html__( 'Placed above', 'jet-elements' ),
					'type-3' => esc_html__( 'Shown as tip', 'jet-elements' ),
					'type-4' => esc_html__( 'On the right', 'jet-elements' ),
					'type-5' => esc_html__( 'Inside the empty bar', 'jet-elements' ),
					'type-6' => esc_html__( 'Inside the bar with title', 'jet-elements' ),
					'type-7' => esc_html__( 'Inside the vertical bar', 'jet-elements' ),
				),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'jet-elements' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter your title', 'jet-elements' ),
				'default'     => esc_html__( 'Title', 'jet-elements' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->_add_advanced_icon_control(
			'icon',
			array(
				'label'       => esc_html__( 'Icon', 'jet-elements' ),
				'type'        => Controls_Manager::ICON,
				'label_block' => true,
				'file'        => '',
			)
		);

		$this->add_control(
			'values_type',
			array(
				'label' => esc_html__( 'Progress Values Type', 'jet-elements' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'percent',
				'options' => array(
					'percent' => esc_html__( 'Percent', 'jet-elements' ),
					'absolute' => esc_html__( 'Absolute', 'jet-elements' ),
				),
			)
		);

		$this->add_control(
			'percent',
			array(
				'label'   => esc_html__( 'Percentage', 'jet-elements' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 50,
				'min'     => 0,
				'max'     => 100,
				'dynamic' => version_compare( ELEMENTOR_VERSION, '2.7.0', '>=' ) ?
					array(
						'active'     => true,
						'categories' => array(
							TagsModule::POST_META_CATEGORY,
							TagsModule::NUMBER_CATEGORY,
						),
					) : array(),
				'condition'	=> array(
					'values_type' => 'percent'
				),
			)
		);

		$this->add_control(
			'absolute_value_curr',
			array(
				'label'     => esc_html__( 'Current Value', 'jet-elements' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 50,
				'dynamic'   => version_compare( ELEMENTOR_VERSION, '2.7.0', '>=' ) ?
					array(
						'active'     => true,
						'categories' => array(
							TagsModule::POST_META_CATEGORY,
							TagsModule::NUMBER_CATEGORY,
						),
					) : array(),
				'condition' => array(
					'values_type' => 'absolute',
				),
			)
		);

		$this->add_control(
			'absolute_value_max',
			array(
				'label'     => esc_html__( 'Max Value', 'jet-elements' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 100,
				'dynamic'   => version_compare( ELEMENTOR_VERSION, '2.7.0', '>=' ) ?
					array(
						'active'     => true,
						'categories' => array(
							TagsModule::POST_META_CATEGORY,
							TagsModule::NUMBER_CATEGORY,
						),
					) : array(),
				'condition' => array(
					'values_type' => 'absolute',
				),
			)
		);

		$this->add_control(
			'absolute_value_prefix',
			array(
				'label'       => esc_html__( 'Value Prefix', 'jet-elements' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( '', 'jet-elements' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'condition'   => array(
					'values_type' => 'absolute',
				),
			)
		);

		$this->add_control(
			'absolute_value_suffix',
			array(
				'label'       => esc_html__( 'Value Suffix', 'jet-elements' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( '%', 'jet-elements' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'condition'   => array(
					'values_type' => 'absolute',
				),
			)
		);

		$this->end_controls_section();

		/**
		 * Progress Bar Style Section
		 */
		$this->_start_controls_section(
			'section_progress_style',
			array(
				'label'      => esc_html__( 'Progress Bar', 'jet-elements' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_responsive_control(
			'progress_wrapper_height',
			array(
				'label'      => esc_html__( 'Progress Height', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', 'custom'
				),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 500,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['progress_wrapper'] => 'height: {{SIZE}}{{UNIT}}',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'progress_wrapper_width',
			array(
				'label'      => esc_html__( 'Progress Width', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', 'custom'
				),
				'range'      => array(
					'px' => array(
						'min' => 1,
						'max' => 200,
					),
				),
				'condition' => array(
					'progress_type' => array( 'type-7' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['progress_wrapper'] => 'width: {{SIZE}}{{UNIT}}',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'progress_wrapper_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['progress_wrapper'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'progress_wrapper_background',
				'selector' => '{{WRAPPER}} ' . $css_scheme['progress_wrapper'],
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'progress_wrapper_border',
				'label'       => esc_html__( 'Border', 'jet-elements' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['progress_wrapper'],
			),
			75
		);

		$this->_add_responsive_control(
			'progress_wrapper_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['progress_wrapper'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'progress_wrapper_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['progress_wrapper'],
			),
			100
		);

		$this->_add_control(
			'status_bar_heading',
			array(
				'label' => esc_html__( 'Status Bar', 'jet-elements' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'status_bar_background',
				'selector' => '{{WRAPPER}} ' . $css_scheme['status_bar'],
			),
			25
		);

		$this->_add_responsive_control(
			'status_bar_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['status_bar'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_control(
			'completed_bar_heading',
			array(
				'label' => esc_html__( 'Сompleted Bar', 'jet-elements' ),
				'type'  => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'completed_bar_background',
				'selector' => '{{WRAPPER}} ' . $css_scheme['completed_bar'],
			),
			25
		);

		$this->_end_controls_section();

		/**
		 * Title Style Section
		 */
		$this->_start_controls_section(
			'section_title_style',
			array(
				'label'      => esc_html__( 'Title', 'jet-elements' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_responsive_control(
			'title_alignment',
			array(
				'label'       => esc_html__( 'Title Alignment', 'jet-elements' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default'     => '',
				'options'     => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'jet-elements' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-elements' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'End', 'jet-elements' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
					),
				),
				'condition' => array(
					'progress_type' => array( 'type-1', 'type-2', 'type-3', 'type-5' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} '. $css_scheme['title'] => 'align-self: {{VALUE}};',
				),
			),
			50
		);

		$this->_add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'title_background',
				'selector' => '{{WRAPPER}} ' . $css_scheme['title'],
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'title_border',
				'label'       => esc_html__( 'Border', 'jet-elements' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['title'],
			),
			75
		);

		$this->_add_responsive_control(
			'title_border_radius',
			array(
				'label'      => __( 'Border Radius', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'title_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['title'],
			),
			100
		);

		$this->_add_responsive_control(
			'title_padding',
			array(
				'label'      => __( 'Padding', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_responsive_control(
			'title_margin',
			array(
				'label'      => __( 'Margin', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			25
		);

		$this->_add_control(
			'title_icon_heading',
			array(
				'label'     => esc_html__( 'Icon', 'jet-elements' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			25
		);

		$this->_add_control(
			'icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'jet-elements' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['title_icon'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', 'em', 'custom'
				),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['title_icon'] => 'font-size: {{SIZE}}{{UNIT}}',
				),
			),
			50
		);

		$this->_add_responsive_control(
			'icon_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['title_icon'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			25
		);

		$this->_add_control(
			'title_text_heading',
			array(
				'label'     => esc_html__( 'Text', 'jet-elements' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			25
		);

		$this->_add_control(
			'text_color',
			array(
				'label'  => esc_html__( 'Text Color', 'jet-elements' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['title_text'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['title_text'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
			),
			50
		);

		$this->_add_responsive_control(
			'text_alignment',
			array(
				'label'       => esc_html__( 'Text Alignment', 'jet-elements' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default'     => '',
				'options'     => array(
					'flex-start' => array(
						'title' => esc_html__( 'Top', 'jet-elements' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-elements' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end' => array(
						'title' => esc_html__( 'Bottom', 'jet-elements' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} '. $css_scheme['title_text'] => 'align-self: {{VALUE}};',
				),
			),
			50
		);

		$this->_end_controls_section();

		/**
		 * Percent Style Section
		 */
		$this->_start_controls_section(
			'section_percent_style',
			array(
				'label'      => esc_html__( 'Percent', 'jet-elements' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_responsive_control(
			'percent_width',
			array(
				'label'      => esc_html__( 'Percent Width', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', 'custom'
				),
				'range'      => array(
					'px' => array(
						'min' => 20,
						'max' => 200,
					),
				),
				'condition' => array(
					'progress_type' => array( 'type-3' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} '. $css_scheme['percent'] => 'width: {{SIZE}}{{UNIT}}; margin-right: calc( {{SIZE}}{{UNIT}}/-2 );',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'percent_alignment',
			array(
				'label'       => esc_html__( 'Percent Alignment', 'jet-elements' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default'     => '',
				'options'     => array(
					'flex-start' => array(
						'title' => esc_html__( 'Start', 'jet-elements' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-elements' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'End', 'jet-elements' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
					),
				),
				'condition' => array(
					'progress_type' => array( 'type-1' ,'type-2' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} '. $css_scheme['percent'] => 'align-self: {{VALUE}};',
				),
			),
			50
		);

		$this->_add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'percent_background',
				'selector' => '{{WRAPPER}} ' . $css_scheme['percent'],
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'percent_border',
				'label'       => esc_html__( 'Border', 'jet-elements' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['percent'],
			),
			75
		);

		$this->_add_responsive_control(
			'percent_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['percent'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'percent_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['percent'],
			),
			100
		);

		$this->_add_responsive_control(
			'percent_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['percent'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'percent_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'custom' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['percent'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			25
		);

		$this->_add_control(
			'percent_color',
			array(
				'label'  => esc_html__( 'Text Color', 'jet-elements' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['percent'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'percent_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['percent'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
			),
			50
		);

		$this->_add_responsive_control(
			'number_suffix_font_size',
			array(
				'label'      => esc_html__( 'Suffix Font Size', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', 'em', 'rem', 'custom'
				),
				'selectors'  => array(
					'{{WRAPPER}} '. $css_scheme['percent'] . ' .jet-progress-bar__percent-suffix' => 'font-size: {{SIZE}}{{UNIT}}',
				),
			),
			50
		);

		$this->_add_responsive_control(
			'percent_suffix_alignment',
			array(
				'label'       => esc_html__( 'Percent Suffix Alignment', 'jet-elements' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'default'     => 'center',
				'options'     => array(
					'flex-start' => array(
						'title' => esc_html__( 'Top', 'jet-elements' ),
						'icon'  => 'eicon-v-align-top',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-elements' ),
						'icon'  => 'eicon-v-align-middle',
					),
					'flex-end' => array(
						'title' => esc_html__( 'Bottom', 'jet-elements' ),
						'icon'  => 'eicon-v-align-bottom',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} '. $css_scheme['percent'] . ' .jet-progress-bar__percent-suffix' => 'align-self: {{VALUE}};',
				),
			),
			50
		);

		$this->_end_controls_section();

	}

	protected function render() {
		$this->_context = 'render';

		$this->_open_wrap();
		include $this->_get_global_template( 'index' );
		$this->_close_wrap();
	}

	/**
	 * Get type template
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public function _get_type_template( $type = null ) {
		return jet_elements()->get_template( $this->get_name() . '/global/types/' . $type . '.php' );
	}

}
