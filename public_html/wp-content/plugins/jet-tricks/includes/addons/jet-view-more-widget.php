<?php
/**
 * Class: Jet_View_More_Widget
 * Name: Read More
 * Slug: jet-view-more
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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Jet_View_More_Widget extends Jet_Tricks_Base {

	public function get_name() {
		return 'jet-view-more';
	}

	public function get_title() {
		return esc_html__( 'Read More', 'jet-tricks' );
	}

	public function get_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/how-to-organize-content-in-a-compact-way-with-view-more-button?utm_source=jettricks&utm_medium=jet-view-more&utm_campaign=need-help';
	}

	public function get_icon() {
		return 'jet-tricks-icon-view-more';
	}

	public function get_categories() {
		return array( 'jet-tricks' );
	}

	protected function register_controls() {
		$css_scheme = apply_filters(
			'jet-tricks/view-more/css-scheme',
			array(
				'instance' => '.jet-view-more',
				'button'   => '.jet-view-more__button',
				'icon'     => '.jet-view-more__icon',
				'label'    => '.jet-view-more__label',
			)
		);

		$this->start_controls_section(
			'section_items_data',
			array(
				'label' => esc_html__( 'Sections', 'jet-tricks' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'section_id',
			array(
				'label'   => esc_html__( 'Section Id', 'jet-tricks' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'section_1',
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'sections',
			array(
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'section_id' => 'section_1'
					)
				),
				'title_field' => '{{{ section_id }}}',
				'render_type' => 'template',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_settings_data',
			array(
				'label' => esc_html__( 'Settings', 'jet-tricks' ),
			)
		);

		$this->add_control(
			$this->__new_icon_prefix . 'button_icon',
			array(
				'label'            => esc_html__( 'Icon', 'jet-tricks' ),
				'type'             => Controls_Manager::ICONS,
				'label_block'      => false,
				'skin'             => 'inline',
				'fa4compatibility' => 'button_icon',
				'default'          => array(
					'value'   => 'fas fa-arrow-circle-right',
					'library' => 'fa-solid',
				),
			)
		);

		$this->add_control(
			'button_label',
			array(
				'label'   => esc_html__( 'Label', 'jet-tricks' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Read More', 'jet-tricks' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'show_all',
			array(
				'label'        => esc_html__( 'Show All Sections', 'jet-tricks' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-tricks' ),
				'label_off'    => esc_html__( 'No', 'jet-tricks' ),
				'return_value' => 'true',
				'default'      => 'false',
			)
		);

		$this->add_control(
			'show_effect',
			array(
				'label'       => esc_html__( 'Show Effect', 'jet-tricks' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'move-up',
				'options' => array(
					'none'             => esc_html__( 'None', 'jet-tricks' ),
					'fade'             => esc_html__( 'Fade', 'jet-tricks' ),
					'zoom-in'          => esc_html__( 'Zoom In', 'jet-tricks' ),
					'zoom-out'         => esc_html__( 'Zoom Out', 'jet-tricks' ),
					'move-up'          => esc_html__( 'Move Up', 'jet-tricks' ),
					'fall-perspective' => esc_html__( 'Fall Perspective', 'jet-tricks' ),
				),
				'render_type' => 'template',
			)
		);

		$this->add_control(
			'read_less',
			array(
				'label'        => esc_html__( 'Read Less Button', 'jet-tricks' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-tricks' ),
				'label_off'    => esc_html__( 'No', 'jet-tricks' ),
				'return_value' => 'true',
				'default'      => 'false',
				'description'  => esc_html__( 'Enable to show "Read Less" button after content is expanded, allowing users to collapse sections back.', 'jet-tricks' ),
			)
		);

		$this->add_control(
			$this->__new_icon_prefix . 'read_less_icon',
			array(
				'label'            => esc_html__( 'Read Less Icon', 'jet-tricks' ),
				'type'             => Controls_Manager::ICONS,
				'label_block'      => false,
				'skin'             => 'inline',
				'fa4compatibility' => 'read_less_icon',
				'default'          => array(
					'value'   => 'fas fa-arrow-circle-up',
					'library' => 'fa-solid',
				),
				'condition' => array(
					'read_less' => 'true',
				),
			)
		);
		
		$this->add_control(
			'read_less_label',
			array(
				'label'   => esc_html__( 'Read Less Label', 'jet-tricks' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Read Less', 'jet-tricks' ),
				'condition' => array(
					'read_less' => 'true',
				),
			)
		);

		$this->add_control(
			'hide_all',
			array(
				'label'   => esc_html__( 'Hide All Sections', 'jet-tricks' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'false',
				'condition' => array(
					'read_less' => 'true',
				),
			)
		);
		
		$this->add_control(
			'hide_effect',
			array(
				'label'       => esc_html__( 'Hide Effect', 'jet-tricks' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'move-down',
				'options' => array(
					'none'             => esc_html__( 'None', 'jet-tricks' ),
					'fade'             => esc_html__( 'Fade', 'jet-tricks' ),
					'zoom-in'          => esc_html__( 'Zoom In', 'jet-tricks' ),
					'zoom-out'         => esc_html__( 'Zoom Out', 'jet-tricks' ),
					'move-down'        => esc_html__( 'Move Down', 'jet-tricks' ),
					'fall-perspective' => esc_html__( 'Fall Perspective', 'jet-tricks' ),
				),
				'render_type' => 'template',
				'condition' => array(
					'read_less' => 'true',
				),
			)
		);

		$this->end_controls_section();

		$this->__start_controls_section(
			'section_general_style',
			array(
				'label'      => esc_html__( 'Button', 'jet-tricks' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->__add_responsive_control(
			'button_alignment',
			array(
				'label'   => esc_html__( 'Alignment', 'jet-tricks' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => array(
					'flex-start'    => array(
						'title' => esc_html__( 'Start', 'jet-tricks' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-tricks' ),
						'icon'  => 'eicon-h-align-center',
					),
					'flex-end' => array(
						'title' => esc_html__( 'End', 'jet-tricks' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['instance'] => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->__add_responsive_control(
			'button_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-tricks' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['button'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->__add_responsive_control(
			'button_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-tricks' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['button'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->__add_responsive_control (
			'icon_position',
			array(
				'label'   => esc_html__( 'Icon Position', 'jet-tricks' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 1,
				'options' => array(
					1 => esc_html__( 'Before Label', 'jet-tricks' ),
					3 => esc_html__( 'After Label', 'jet-tricks' )
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['button'] . ' ' . $css_scheme['icon'] => 'order: {{VALUE}};',
				),
			),
			75
		);

		$this->__add_responsive_control (
			'icon_orientation',
			array(
				'label'   => esc_html__( 'Icon Orientation', 'jet-tricks' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'row',
				'options' => array(
					'row'    => esc_html__( 'Horizontal', 'jet-tricks' ),
					'column' => esc_html__( 'Vertical', 'jet-tricks' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['button'] => 'flex-direction: {{VALUE}};',
				),
			),
			75
		);

		$this->__add_responsive_control(
			'icon_gap',
			array(
				'label'      => esc_html__( 'Icon Gap', 'jet-tricks' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['button'] . ' ' . $css_scheme['icon'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->__start_controls_tabs( 'button_styles' );

		$this->__start_controls_tab(
			'button_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-tricks' ),
			)
		);

		$this->__add_control(
			'button_icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'jet-tricks' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['button'] . ' ' . $css_scheme['icon'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->__add_responsive_control(
			'button_icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'jet-tricks' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', 'em', 'rem',
				),
				'range'      => array(
					'px' => array(
						'min' => 18,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['button'] . ' ' . $css_scheme['icon'] => 'font-size: {{SIZE}}{{UNIT}}',
				),
			),
			50
		);

		$this->__add_control(
			'button_label_color',
			array(
				'label'  => esc_html__( 'Text Color', 'jet-tricks' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['button'] . ' ' . $css_scheme['label'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->__add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_label_typography',
				'selector' => '{{WRAPPER}} '. $css_scheme['button'] . ' ' . $css_scheme['label'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
			),
			50
		);

		$this->__add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'button_background',
				'selector' => '{{WRAPPER}} ' . $css_scheme['button'],
			),
			25
		);

		$this->__add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'button_border',
				'label'       => esc_html__( 'Border', 'jet-tricks' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'  => '{{WRAPPER}} ' . $css_scheme['button'],
			),
			50
		);

		$this->__add_responsive_control(
			'button_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-tricks' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['button'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->__add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['button'],
			),
			100
		);

		$this->__end_controls_tab();

		$this->__start_controls_tab(
			'button_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-tricks' ),
			)
		);

		$this->__add_control(
			'button_icon_color_hover',
			array(
				'label'     => esc_html__( 'Icon Color', 'jet-tricks' ),
				'type'      => Controls_Manager::COLOR,
				'global' => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['button'] . ':hover ' . $css_scheme['icon'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->__add_responsive_control(
			'button_icon_size_hover',
			array(
				'label'      => esc_html__( 'Icon Size', 'jet-tricks' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px', 'em', 'rem',
				),
				'range'      => array(
					'px' => array(
						'min' => 18,
						'max' => 200,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['button'] . ':hover ' . $css_scheme['icon'] => 'font-size: {{SIZE}}{{UNIT}}',
				),
			),
			50
		);

		$this->__add_control(
			'buttonlabel_color_hover',
			array(
				'label'  => esc_html__( 'Text Color', 'jet-tricks' ),
				'type'   => Controls_Manager::COLOR,
				'global' => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['button'] . ':hover ' . $css_scheme['label'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->__add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_label_typography_hover',
				'selector' => '{{WRAPPER}} ' . $css_scheme['button'] . ':hover ' . $css_scheme['label'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
			),
			50
		);

		$this->__add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'button_background_hover',
				'selector' => '{{WRAPPER}} ' . $css_scheme['button'] . ':hover',
			),
			25
		);

		$this->__add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'button_border_hover',
				'label'       => esc_html__( 'Border', 'jet-tricks' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'  => '{{WRAPPER}} ' . $css_scheme['button'] . ':hover',
			),
			50
		);

		$this->__add_responsive_control(
			'button_border_radius_hover',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-tricks' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['button'] . ':hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->__add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_box_shadow_hover',
				'selector' => '{{WRAPPER}} ' . $css_scheme['button'] . ':hover',
			),
			100
		);

		$this->__end_controls_tab();

		$this->__end_controls_tabs();

		// === Read Less Button Custom Styles ===
		$this->__add_control(
			'read_less_style_heading',
			array(
				'label' => esc_html__( 'Read Less Button', 'jet-tricks' ),
				'type'  => Controls_Manager::HEADING,
				'condition' => array(
					'read_less' => 'true',
				),
				'separator' => 'before',
			),
			25
		);

		$this->__start_controls_tabs( 'button_read_less_styles' );

		$this->__start_controls_tab(
			'button_read_less_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-tricks' ),
				'condition' => array(
					'read_less' => 'true',
				),
			)
		);

		$this->__add_control(
			'button_read_less_icon_color',
			array(
				'label'     => esc_html__( 'Icon Color', 'jet-tricks' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-view-more__button.jet-view-more__button--read-less ' . $css_scheme['icon'] => 'color: {{VALUE}}',
				),
				'condition' => array(
					'read_less' => 'true',
				),
			),
			25
		);

		$this->__add_responsive_control(
			'button_read_less_icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'jet-tricks' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array('px', 'em', 'rem'),
				'range'      => array(
					'px' => array('min' => 18, 'max' => 200),
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-view-more__button.jet-view-more__button--read-less ' . $css_scheme['icon'] => 'font-size: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'read_less' => 'true',
				),
			),
			50
		);

		$this->__add_control(
			'button_read_less_label_color',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-tricks' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-view-more__button.jet-view-more__button--read-less ' . $css_scheme['label'] => 'color: {{VALUE}}',
				),
				'condition' => array(
					'read_less' => 'true',
				),
			),
			25
		);

		$this->__add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_read_less_label_typography',
				'selector' => '{{WRAPPER}} .jet-view-more__button.jet-view-more__button--read-less ' . $css_scheme['label'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'condition' => array(
					'read_less' => 'true',
				),
			),
			50
		);

		$this->__add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			array(
				'name'     => 'button_read_less_background',
				'selector' => '{{WRAPPER}} .jet-view-more__button.jet-view-more__button--read-less',
				'condition' => array(
					'read_less' => 'true',
				),
			),
			25
		);

		$this->__add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'button_read_less_border',
				'label'       => esc_html__( 'Border', 'jet-tricks' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'  => '{{WRAPPER}} .jet-view-more__button.jet-view-more__button--read-less',
				'condition' => array(
					'read_less' => 'true',
				),
			),
			50
		);

		$this->__add_responsive_control(
			'button_read_less_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-tricks' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .jet-view-more__button.jet-view-more__button--read-less' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'read_less' => 'true',
				),
			),
			75
		);

		$this->__add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_read_less_box_shadow',
				'selector' => '{{WRAPPER}} .jet-view-more__button.jet-view-more__button--read-less',
				'condition' => array(
					'read_less' => 'true',
				),
			),
			100
		);

		$this->__end_controls_tab();

		$this->__start_controls_tab(
			'button_read_less_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-tricks' ),
				'condition' => array(
					'read_less' => 'true',
				),
			)
		);

		$this->__add_control(
			'button_read_less_icon_color_hover',
			array(
				'label'     => esc_html__( 'Icon Color', 'jet-tricks' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-view-more__button.jet-view-more__button--read-less:hover ' . $css_scheme['icon'] => 'color: {{VALUE}}',
				),
				'condition' => array(
					'read_less' => 'true',
				),
			),
			25
		);

		$this->__add_responsive_control(
			'button_read_less_icon_size_hover',
			array(
				'label'      => esc_html__( 'Icon Size', 'jet-tricks' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array('px', 'em', 'rem'),
				'range'      => array(
					'px' => array('min' => 18, 'max' => 200),
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-view-more__button.jet-view-more__button--read-less:hover ' . $css_scheme['icon'] => 'font-size: {{SIZE}}{{UNIT}}',
				),
				'condition' => array(
					'read_less' => 'true',
				),
			),
			50
		);

		$this->__add_control(
			'button_read_less_label_color_hover',
			array(
				'label'     => esc_html__( 'Text Color', 'jet-tricks' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-view-more__button.jet-view-more__button--read-less:hover ' . $css_scheme['label'] => 'color: {{VALUE}}',
				),
				'condition' => array(
					'read_less' => 'true',
				),
			),
			25
		);

		$this->__add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_read_less_label_typography_hover',
				'selector' => '{{WRAPPER}} .jet-view-more__button.jet-view-more__button--read-less:hover ' . $css_scheme['label'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'condition' => array(
					'read_less' => 'true',
				),
			),
			50
		);

		$this->__add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			array(
				'name'     => 'button_read_less_background_hover',
				'selector' => '{{WRAPPER}} .jet-view-more__button.jet-view-more__button--read-less:hover',
				'condition' => array(
					'read_less' => 'true',
				),
			),
			25
		);

		$this->__add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'button_read_less_border_hover',
				'label'       => esc_html__( 'Border', 'jet-tricks' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'  => '{{WRAPPER}} .jet-view-more__button.jet-view-more__button--read-less:hover',
			),
			50
		);

		$this->__add_responsive_control(
			'button_read_less_border_radius_hover',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-tricks' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .jet-view-more__button.jet-view-more__button--read-less:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition' => array(
					'read_less' => 'true',
				),
			),
			75
		);

		$this->__add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'button_read_less_box_shadow_hover',
				'selector' => '{{WRAPPER}} .jet-view-more__button.jet-view-more__button--read-less:hover',
				'condition' => array(
					'read_less' => 'true',
				),
			),
			100
		);

		$this->__end_controls_tab();

		$this->__end_controls_tabs();

		$this->__end_controls_section();
	}

	/**
	 * [render description]
	 * @return [type] [description]
	 */
	protected function render() {

		$this->__context = 'render';

		$button_settings = $this->get_settings_for_display();

		$sections = $button_settings[ 'sections' ];

		foreach ( $sections as $key => $section ) {
			$sections_list[ $section['_id'] ] = $section['section_id'];
		}

		$settings = array(
			'effect'   => $button_settings['show_effect'],
			'hide_effect' => $button_settings['hide_effect'],
			'sections' => $sections_list,
			'showall'  => filter_var( $button_settings['show_all'], FILTER_VALIDATE_BOOLEAN ),
			'read_less'   => filter_var( $button_settings['read_less'], FILTER_VALIDATE_BOOLEAN ),
			'read_more_label' => $button_settings['button_label'],
			'read_more_icon'  => $button_settings[ $this->__new_icon_prefix . 'button_icon' ],
			'read_less_label' => $button_settings['read_less_label'],
			'read_less_icon'  => $button_settings[ $this->__new_icon_prefix . 'read_less_icon' ],
			'hide_all'        => filter_var( $button_settings['hide_all'], FILTER_VALIDATE_BOOLEAN ),
		);

		$this->add_render_attribute( 'instance', array(
			'class' => array(
				'jet-view-more',
			),
			'data-settings' => json_encode( $settings ),
		) );

		$button_icon_html = $this->__get_icon( 'button_icon', $button_settings, '<div class="jet-view-more__icon jet-tricks-icon">%s</div>' );

		$button_label_html = '';

		if ( ! empty( $button_settings['button_label'] ) ) {
			$button_label_html = sprintf( '<div class="jet-view-more__label">%1$s</div>', $button_settings['button_label'] );
		}

		echo sprintf(
			'<div %1$s><div class="jet-view-more__button" role="button" tabindex="0">%2$s%3$s</div></div>',
			$this->get_render_attribute_string( 'instance' ),
			$button_icon_html,
			$button_label_html
		);
	}
}
