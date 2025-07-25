<?php
/**
 * Class: Jet_Blog_Video_Playlist
 * Name: Video Playlist
 * Slug: jet-blog-video-playlist
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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Jet_Blog_Video_Playlist extends Jet_Blog_Base {

	public function get_name() {
		return 'jet-blog-video-playlist';
	}

	public function get_title() {
		return esc_html__( 'Video Playlist', 'jet-blog' );
	}

	public function get_icon() {
		return 'jet-blog-icon-video-playlist';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/how-to-create-a-video-playlist-with-jetblog-video-playlist-widget/';
	}

	public function get_categories() {
		return array( 'cherry' );
	}

	public function get_script_depends() {
		return array( 'youtube-iframe-api', 'vimeo-iframe-api' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-blog/video-playlist/css-scheme',
			array(
				'canvas'          => '.jet-blog-playlist__canvas',
				'thumb_list'      => '.jet-blog-playlist__items',
				'thumb_item'      => '.jet-blog-playlist__item',
				'thumb_title'     => '.jet-blog-playlist__item-title',
				'thumb_duration'  => '.jet-blog-playlist__item-duration',
				'thumb_date'      => '.jet-blog-playlist__item-date',
				'thumb_index'     => '.jet-blog-playlist__item-index',
				'heading'         => '.jet-blog-playlist__heading',
				'heading_icon'    => '.jet-blog-playlist__heading-icon',
				'heading_text'    => '.jet-blog-playlist__heading-title',
				'heading_counter' => '.jet-blog-playlist__counter',
			)
		);

		if ( \Elementor\Plugin::$instance->breakpoints && method_exists( \Elementor\Plugin::$instance->breakpoints, 'get_active_breakpoints') ) {
			$active_breakpoints      = \Elementor\Plugin::$instance->breakpoints->get_active_breakpoints();
			$breakpoints_list        = array();
			$hide_breakpoints_list   = array();

			foreach ($active_breakpoints as $key => $value) {

				if ( 'mobile' != $key ) {
					$breakpoints_list[$key] = $key;
				}

				$hide_breakpoints_list[$key] = $value->get_label();
			}

			$hide_breakpoints_list['desktop'] = 'Desktop';
			$hide_breakpoints_list            = array_reverse($hide_breakpoints_list);
			$breakpoints_list['desktop']      = 'desktop';
			$breakpoints_list                 = array_reverse($breakpoints_list);
		} else {
			$breakpoints_list = array(
				'desktop' => 'desktop',
				'tablet'  => 'tablet'
			);

			$hide_breakpoints_list = array(
				'desktop' => 'Desktop',
				'tablet'  => 'Tablet',
				'mobile'  => 'Mobile'
			);
		}

		$this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__( 'Items', 'jet-blog' ),
			)
		);

		$this->add_control(
			'set_key',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => sprintf(
					esc_html__( 'Please set Google API key to correctly get data for YouTube videos. You can create own API key  %1$s. Paste created key on %2$s', 'jet-blog' ),
					'<a target="_blank" href="https://console.developers.google.com/apis/dashboard">' . esc_html__( 'here', 'jet-blog' ) . '</a>',
					'<a target="_blank" href="' . jet_blog_settings()->get_settings_page_link() . '">' . esc_html__( 'settings page', 'jet-blog' ) . '</a>'
				)
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => esc_html__( 'Source', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'custom',
				'options' => array(
					'custom'    => esc_html__( 'Custom Video List', 'jet-blog' ),
					'yt_source' => esc_html__( 'YouTube Channel or Playlist', 'jet-blog' ),
                    'repeater'  => esc_html__( 'Repeater Field', 'jet-blog' ),
				),
			)
		);

        $this->add_control(
            'repeater_notice',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw'  => esc_html__(
                    'The post meta values will be taken from the current page where this widget is placed.',
                    'jet-blog'
                ),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
                'condition' => [
                    'source' => 'repeater',
                ],
            ]
        );

        $this->add_control(
            'repeater_meta_name',
            [
                'label' => esc_html__('Repeater Meta Name', 'jet-blog'),
                'type' => Controls_Manager::TEXT,
                'condition' => ['source' => 'repeater'],
                'description' => esc_html__('Enter the repeater meta field name, e.g. videos.', 'jet-blog'),
            ]
        );

        $this->add_control(
            'repeater_url_name',
            [
                'label' => esc_html__('URL Sub-Field Name', 'jet-blog'),
                'type' => Controls_Manager::TEXT,
                'condition' => ['source' => 'repeater'],
                'description' => esc_html__('Enter the sub-field name containing video URL, e.g. url.', 'jet-blog'),
            ]
        );

        $this->add_control(
            'repeater_title_name',
            [
                'label' => esc_html__('Title Sub-Field Name', 'jet-blog'),
                'type' => Controls_Manager::TEXT,
                'condition' => ['source' => 'repeater'],
                'description' => esc_html__('Enter the sub-field name containing video title, or leave empty.', 'jet-blog'),
            ]
        );

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'jet-blog' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'Leave empty to automatically get title from video', 'jet-blog' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'url',
			array(
				'label'       => esc_html__( 'URL', 'jet-blog' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'YouTube or Vimeo video URL', 'jet-blog' ),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'start_time',
			array(
				'label'     => esc_html__( 'Start Time (in seconds)', 'jet-blog' ),
				'type'      => Controls_Manager::NUMBER,
			)
		);

		$repeater->add_control(
			'custom_thumb',
			array(
				'label'   => esc_html__( 'Custom Thumbnail', 'jet-blog' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'custom_thumb_size',
			array(
				'label'     => esc_html__( 'Thumbnail Size', 'jet-blog' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'thumbnail',
				'options'   => jet_blog_tools()->get_image_sizes(),
				'condition' => array(
					'custom_thumb[url]!' => '',
				),
			)
		);

		$this->add_control(
			'videos_list',
			array(
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => array(
					array(
						'title'        => '',
						'url'          => 'https://www.youtube.com/watch?v=CJO0u_HrWE8',
						'custom_thumb' => '',
					),
				),
				'title_field' => '{{{ title }}}',
				'condition'   => array(
					'source' => 'custom',
				),
			)
		);

		$this->add_control(
			'source_url',
			array(
				'label'       => esc_html__( 'Source URL', 'jet-blog' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'YouTube Channel or Playlist URL', 'jet-blog' ),
				'dynamic'     => array( 'active' => true ),
                'condition'   => array(
                    'source' => 'yt_source',
                ),
			)
		);

		$this->add_control(
			'max_results',
			array(
				'label'     => esc_html__( 'Number of videos', 'jet-blog' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 10,
				'min'       => 1,
				'max'       => 50,
                'condition' => array(
                    'source' => 'yt_source',
                ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_settings',
			array(
				'label' => esc_html__( 'Settings', 'jet-blog' ),
			)
		);

		$this->add_responsive_control(
			'playlist_height',
			array(
				'label'       => esc_html__( 'Playlist Height', 'jet-blog' ),
				'description' => esc_html__( 'Option for non-mobile devices', 'jet-blog' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 450,
				'min'         => 150,
				'max'         => 1000,
				'step'        => 1,
				'devices'     => $breakpoints_list,
				'render_type' => 'template',
				'selectors'   => array(
					'{{WRAPPER}} ' . $css_scheme['canvas']              => 'height: {{VALUE}}px;',
					'{{WRAPPER}} .jet-blog-playlist.jet-tumbs-vertical' => 'height: {{VALUE}}px;',
					'{{WRAPPER}} .jet-blog-playlist__embed-wrap'        => 'padding-bottom: {{VALUE}}px;',
				),
			)
		);

		$this->add_control(
			'thumbnails_orientation',
			array(
				'label'   => esc_html__( 'Thumbnails Orientation', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'vertical',
				'options' => array(
					'vertical'   => esc_html__( 'Vertical', 'jet-blog' ),
					'horizontal' => esc_html__( 'Horizontal', 'jet-blog' ),
				),
			)
		);

		$this->add_control(
			'thumbnails_v_position',
			array(
				'label'   => esc_html__( 'Thumbnails Position', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => array(
					'left'  => esc_html__( 'Left', 'jet-blog' ),
					'right' => esc_html__( 'Right', 'jet-blog' ),
				),
				'condition' => array(
					'thumbnails_orientation' => 'vertical',
				),
			)
		);

		$this->add_control(
			'thumbnails_h_position',
			array(
				'label'   => esc_html__( 'Thumbnails Position', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'bottom',
				'options' => array(
					'top'    => esc_html__( 'Top', 'jet-blog' ),
					'bottom' => esc_html__( 'Bottom', 'jet-blog' ),
				),
				'condition' => array(
					'thumbnails_orientation' => 'horizontal',
				),
			)
		);

		$this->add_responsive_control(
			'thumbnails_width',
			array(
				'label'       => esc_html__( 'Thumbnails List Width (%)', 'jet-blog' ),
				'description' => esc_html__( 'Option for non-mobile devices', 'jet-blog' ),
				'label_block' => true,
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( '%' ),
				'devices'     => $breakpoints_list,
				'default'     => array(
					'unit' => '%',
					'size' => 33,
				),
				'range'     => array(
					'%' => array(
						'min'  => 1,
						'max'  => 100,
						'step' => 0.1,
					),
				),
				'condition' => array(
					'thumbnails_orientation' => 'vertical',
				),
				'selectors' => array(
					'{{WRAPPER}} .jet-tumbs-vertical ' . $css_scheme['thumb_list'] => 'width: {{SIZE}}%;',
				),
			)
		);

		$this->add_responsive_control(
			'thumbnail_item_width',
			array(
				'label'      => esc_html__( 'Thumbnail Column Width (in pixels)', 'jet-blog' ),
				'label_block' => true,
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 200,
				),
				'range'      => array(
					'px' => array(
						'min' => 100,
						'max' => 500,
					),
				),
				'condition' => array(
					'thumbnails_orientation' => 'horizontal',
				),
				'selectors' => array(
					'{{WRAPPER}} .jet-tumbs-horizontal ' . $css_scheme['thumb_item'] => 'width: {{SIZE}}px; flex: 0 0 {{SIZE}}px;',
				),
			)
		);

		$this->add_control(
			'show_heading',
			array(
				'label'        => esc_html__( 'Show Thumbnails Heading', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_responsive_control(
			'heading_width',
			array(
				'label'      => esc_html__( 'Heading Column Width (in pixels)', 'jet-blog' ),
				'label_block' => true,
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 195,
				),
				'range'      => array(
					'px' => array(
						'min' => 100,
						'max' => 500,
					),
				),
				'condition' => array(
					'thumbnails_orientation' => 'horizontal',
					'show_heading'           => 'yes',
				),
				'selectors' => array(
					'{{WRAPPER}} .jet-tumbs-horizontal ' . $css_scheme['heading'] => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'heading_text',
			array(
				'label'     => esc_html__( 'Heading Text', 'jet-blog' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Video Playlist', 'jet-blog' ),
				'condition' => array(
					'show_heading' => 'yes',
				),
			)
		);

		$this->add_control(
			$this->_new_icon_prefix . 'heading_icon',
			array(
				'type'             => Controls_Manager::ICONS,
				'label'            => esc_html__( 'Heading Icon', 'jet-blog' ),
				'label_block'      => false,
				'skin'             => 'inline',
				'fa4compatibility' => 'heading_icon',
				'default'          => array(
					'value'   => 'fas fa-play',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'show_heading' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_counter',
			array(
				'label'        => esc_html__( 'Show Videos Counter', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition' => array(
					'show_heading' => 'yes',
				),
			)
		);

		$this->add_control(
			'counter_suffix',
			array(
				'label'     => esc_html__( 'Counter Suffix', 'jet-blog' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'videos', 'jet-blog' ),
				'condition' => array(
					'show_heading' => 'yes',
					'show_counter' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_publication_date',
			array(
				'label'        => esc_html__( 'Show Video Publication Date', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => '',
				'separator'    => 'before',
				'description'  => sprintf( esc_html__( 'You can create own API key %1$s. Then paste created key on the %2$s', 'jet-blog' ),
				'<a target="_blank" href="https://console.developers.google.com/apis/dashboard">' . esc_html__( 'here', 'jet-blog' ) . '</a>',
				'<a target="_blank" href="' . jet_blog_settings()->get_settings_page_link() . '">' . esc_html__( 'settings page', 'jet-blog' ) . '</a>'),
			)
		);

		$this->add_control(
			'human_readable_date_diff',
			array(
				'label'        => esc_html__( 'Human Readable Date Diff', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition' => array(
					'show_publication_date' => 'yes',
				),
			)
		);

		$this->add_control(
			'date_format',
			array(
				'label'       => esc_html__( 'Date Format', 'jet-blog' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'F j, Y',
				'description' => sprintf( '<a href="https://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">%s</a>', esc_html__( 'Documentation on date and time formatting', 'jet-blog' ) ),
				'placeholder' => esc_html__( 'Enter date format', 'jet-blog' ),
				'condition' => array(
					'show_publication_date'     => 'yes',
					'human_readable_date_diff!' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_item_index',
			array(
				'label'        => esc_html__( 'Show Item Number and Status', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'hide_index',
			array(
				'label'       => esc_html__( 'Hide Index On Device', 'jet-blog' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => 'true',
				'options'     => $hide_breakpoints_list,
			)
		);

		// $this->add_control(
		// 	'hide_index_mobile',
		// 	array(
		// 		'label'        => esc_html__( 'Hide Index On Mobile', 'jet-blog' ),
		// 		'type'         => Controls_Manager::SWITCHER,
		// 		'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
		// 		'label_off'    => esc_html__( 'No', 'jet-blog' ),
		// 		'return_value' => 'yes',
		// 		'default'      => '',
		// 	)
		// );

		$this->add_control(
			'show_item_duration',
			array(
				'label'        => esc_html__( 'Show Item Duration', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'hide_duration',
			array(
				'label'       => esc_html__( 'Hide Duration On Device', 'jet-blog' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => 'true',
				'options'     => $hide_breakpoints_list,
			)
		);

		// $this->add_control(
		// 	'hide_duration_tablet',
		// 	array(
		// 		'label'        => esc_html__( 'Hide Duration On Tablets', 'jet-blog' ),
		// 		'type'         => Controls_Manager::SWITCHER,
		// 		'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
		// 		'label_off'    => esc_html__( 'No', 'jet-blog' ),
		// 		'return_value' => 'yes',
		// 		'default'      => '',
		// 	)
		// );

		// $this->add_control(
		// 	'hide_duration_mobile',
		// 	array(
		// 		'label'        => esc_html__( 'Hide Duration On Mobile', 'jet-blog' ),
		// 		'type'         => Controls_Manager::SWITCHER,
		// 		'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
		// 		'label_off'    => esc_html__( 'No', 'jet-blog' ),
		// 		'return_value' => 'yes',
		// 		'default'      => '',
		// 	)
		// );

		$this->add_control(
			'hide_image',
			array(
				'label'       => esc_html__( 'Hide Thumbnail Image On Device', 'jet-blog' ),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => 'true',
				'options'     => $hide_breakpoints_list,
			)
		);

		// $this->add_control(
		// 	'hide_image_tablet',
		// 	array(
		// 		'label'        => esc_html__( 'Hide Thumbnail Image On Tablets', 'jet-blog' ),
		// 		'type'         => Controls_Manager::SWITCHER,
		// 		'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
		// 		'label_off'    => esc_html__( 'No', 'jet-blog' ),
		// 		'return_value' => 'yes',
		// 		'default'      => '',
		// 		'separator'    => 'before',
		// 	)
		// );

		// $this->add_control(
		// 	'hide_image_mobile',
		// 	array(
		// 		'label'        => esc_html__( 'Hide Thumbnail Image On Mobile', 'jet-blog' ),
		// 		'type'         => Controls_Manager::SWITCHER,
		// 		'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
		// 		'label_off'    => esc_html__( 'No', 'jet-blog' ),
		// 		'return_value' => 'yes',
		// 		'default'      => '',
		// 	)
		// );

		$this->add_control(
			'show_scroll_on_hover',
			array(
				'label'        => esc_html__( 'Show Scroll Only on Hover', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'thumbnails_orientation' => 'vertical',
				),
			)
		);

		$this->add_control(
			'disable_caching',
			array(
				'label'        => esc_html__( 'Disable Caching', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => esc_html__( 'Force getting videos data from API on each widget refresh. Not recommended, only for debugging.', 'jet-blog' ),
				'separator'    => 'before',
			)
		);

		$this->end_controls_section();

		$this->_start_controls_section(
			'section_general_style',
			array(
				'label'      => esc_html__( 'General Styles', 'jet-blog' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_control(
			'canvas_bg',
			array(
				'label'  => esc_html__( 'Canvas Background', 'jet-blog' ),
				'type'   => Controls_Manager::COLOR,
				'global' => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['canvas'] => 'background-color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_control(
			'thumbs_bg',
			array(
				'label'  => esc_html__( 'Thumbnails Background', 'jet-blog' ),
				'type'   => Controls_Manager::COLOR,
				'global' => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['thumb_list'] => 'background-color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_control(
			'heading_bg',
			array(
				'label'  => esc_html__( 'Heading Background', 'jet-blog' ),
				'type'   => Controls_Manager::COLOR,
				'global' => array(
					'default' => Global_Colors::COLOR_SECONDARY,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['heading'] => 'background-color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'playlist_border_radius',
			array(
				'label'      => esc_html__( 'Wrapper Border Radius', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .jet-blog-playlist' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_responsive_control(
			'heading_padding',
			array(
				'label'      => esc_html__( 'Heading Padding', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['heading'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_control(
			'heading_icon_styles',
			array(
				'label'     => esc_html__( 'Heading Icon', 'jet-blog' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			25
		);

		$this->_add_control(
			'heading_icon_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-blog' ),
				'type'   => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['heading_icon'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_control(
			'heading_icon_size',
			array(
				'label'      => esc_html__( 'Icon Size', 'jet-blog' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 12,
						'max' => 90,
					),
				),
				'default' => array(
					'units' => 'px',
					'size'  => 30,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['heading_icon'] => 'font-size: {{SIZE}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_responsive_control(
			'heading_icon_padding',
			array(
				'label'      => esc_html__( 'Icon Padding', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['heading_icon'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_control(
			'heading_title_styles',
			array(
				'label'     => esc_html__( 'Heading Title', 'jet-blog' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			25
		);

		$this->_add_control(
			'heading_title_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-blog' ),
				'type'   => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['heading_text'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading_title_typography',
				'selector' => '{{WRAPPER}}  ' . $css_scheme['heading_text'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				),
			),
			50
		);

		$this->_add_control(
			'heading_counter_styles',
			array(
				'label'     => esc_html__( 'Heading Counter', 'jet-blog' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			25
		);

		$this->_add_control(
			'heading_counter_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-blog' ),
				'type'   => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['heading_counter'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'heading_counter_typography',
				'selector' => '{{WRAPPER}}  ' . $css_scheme['heading_counter'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
			),
			50
		);

		$this->_end_controls_section();

		$this->_start_controls_section(
			'section_thumb_item_style',
			array(
				'label'      => esc_html__( 'Thumbnail Styles', 'jet-blog' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_responsive_control(
			'thumb_item_padding',
			array(
				'label'      => esc_html__( 'Thumbnail Row Padding', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['thumb_item'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'thumb_title_border',
				'label'       => esc_html__( 'Border', 'jet-blog' ),
				'placeholder' => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['thumb_item'],
			),
			75
		);

		$this->_add_control(
			'thumb_img_gap',
			array(
				'label'      => esc_html__( 'Thumbnail Image Gap', 'jet-blog' ),
				'label_block' => true,
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors' => array(
					'body:not(.rtl) {{WRAPPER}} .jet-tumbs-vertical ' . $css_scheme['thumb_list'] . ' .jet-blog-playlist__item-thumb' => 'margin-right: {{SIZE}}{{UNIT}};',
					'body.rtl {{WRAPPER}} .jet-tumbs-vertical ' . $css_scheme['thumb_list'] . ' .jet-blog-playlist__item-thumb' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .jet-tumbs-horizontal ' . $css_scheme['thumb_list'] . ' .jet-blog-playlist__item-thumb' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_control(
			'thumb_title_styles',
			array(
				'label'     => esc_html__( 'Title', 'jet-blog' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			50
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'thumb_title_typography',
				'selector' => '{{WRAPPER}}  ' . $css_scheme['thumb_title'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
			),
			50
		);

		$this->_add_responsive_control(
			'thumb_item_title_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['thumb_title'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_control(
			'thumb_date_styles',
			array(
				'label'     => esc_html__( 'Publication Date', 'jet-blog' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			50
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'thumb_date_typography',
				'selector' => '{{WRAPPER}}  ' . $css_scheme['thumb_date'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
			),
			50
		);

		$this->_add_responsive_control(
			'thumb_item_date_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['thumb_date'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_control(
			'thumb_duration_styles',
			array(
				'label'     => esc_html__( 'Duration', 'jet-blog' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			50
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'thumb_duration_typography',
				'selector' => '{{WRAPPER}}  ' . $css_scheme['thumb_duration'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
			),
			50
		);

		$this->_add_responsive_control(
			'thumb_item_duration_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['thumb_duration'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_start_controls_tabs( 'tabs_thumb_item_style' );

		$this->_start_controls_tab(
			'tab_thumb_item_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-blog' ),
			)
		);

		$this->_add_control(
			'thumb_item_title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['thumb_title'] => 'color: {{VALUE}};',
				),
			),
			25
		);

		$this->_add_control(
			'thumb_item_date_color',
			array(
				'label'     => esc_html__( 'Publication Date Color', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['thumb_date'] => 'color: {{VALUE}};',
				),
			),
			25
		);

		$this->_add_control(
			'thumb_item_duration_color',
			array(
				'label'     => esc_html__( 'Duration Color', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['thumb_duration'] => 'color: {{VALUE}};',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'thumb_item_background',
				'selector' => '{{WRAPPER}} ' . $css_scheme['thumb_item'],
			),
			25
		);

		$this->_end_controls_tab();

		$this->_start_controls_tab(
			'tab_thumb_item_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-blog' ),
			),
			25
		);

		$this->_add_control(
			'thumb_item_title_color_hover',
			array(
				'label'     => esc_html__( 'Title Color', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['thumb_item'] . ':hover ' . $css_scheme['thumb_title'] => 'color: {{VALUE}};',
				),
			),
			25
		);

		$this->_add_control(
			'thumb_item_date_color_hover',
			array(
				'label'     => esc_html__( 'Publication Date Color', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['thumb_item'] . ':hover ' . $css_scheme['thumb_date'] => 'color: {{VALUE}};',
				),
			),
			25
		);

		$this->_add_control(
			'thumb_item_duration_color_hover',
			array(
				'label'     => esc_html__( 'Duration Color', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['thumb_item'] . ':hover ' . $css_scheme['thumb_duration'] => 'color: {{VALUE}};',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'thumb_item_background_hover',
				'selector' => '{{WRAPPER}} ' . $css_scheme['thumb_item'] . ':hover',
			),
			25
		);

		$this->_end_controls_tab();

		$this->_start_controls_tab(
			'tab_thumb_item_active',
			array(
				'label' => esc_html__( 'Active', 'jet-blog' ),
			),
			25
		);

		$this->_add_control(
			'thumb_item_title_color_active',
			array(
				'label'     => esc_html__( 'Title Color', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['thumb_item'] . '.jet-blog-active ' . $css_scheme['thumb_title'] => 'color: {{VALUE}};',
				),
			),
			25
		);

		$this->_add_control(
			'thumb_item_date_color_active',
			array(
				'label'     => esc_html__( 'Publication Date Color', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['thumb_item'] . '.jet-blog-active ' . $css_scheme['thumb_date'] => 'color: {{VALUE}};',
				),
			),
			25
		);

		$this->_add_control(
			'thumb_item_duration_color_active',
			array(
				'label'     => esc_html__( 'Duration Color', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['thumb_item'] . '.jet-blog-active ' . $css_scheme['thumb_duration'] => 'color: {{VALUE}};',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'thumb_item_background_active',
				'selector' => '{{WRAPPER}} ' . $css_scheme['thumb_item'] . '.jet-blog-active',
			),
			25
		);

		$this->_end_controls_tab();

		$this->_end_controls_tabs();

		$this->_end_controls_section();

		$this->_start_controls_section(
			'section_thumb_index_style',
			array(
				'label'      => esc_html__( 'Thumbnails Numbers and Status Icons', 'jet-blog' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'thumb_index_typography',
				'selector' => '{{WRAPPER}}  ' . $css_scheme['thumb_index'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
			),
			50
		);

		$this->_add_responsive_control(
			'thumb_index_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['thumb_index'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_start_controls_tabs( 'tabs_thumb_index_style' );

		$this->_start_controls_tab(
			'tab_thumb_index_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-blog' ),
			)
		);

		$this->_add_group_control(
			\Jet_Blog_Group_Control_Box_Style::get_type(),
			array(
				'name'           => 'thumb_index',
				'label'          => esc_html__( 'Thumb Style', 'jet-blog' ),
				'fields_options' => array(
					'box_font_color' => array(
						'default' => '#fff',
					),
					'box_font_size' => array(
						'default'    => array(
							'unit' => 'px',
							'size' => 10,
						),
						'selectors' => array(
							'{{WRAPPER}} '. $css_scheme['thumb_index'] .' .jet-blog-playlist__item-status' => 'font-size: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}} ' . $css_scheme['thumb_index'],
			),
			25
		);

		$this->_end_controls_tab();

		$this->_start_controls_tab(
			'tab_thumb_index_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-blog' ),
			)
		);

		$this->_add_group_control(
			\Jet_Blog_Group_Control_Box_Style::get_type(),
			array(
				'name'           => 'thumb_index_hover',
				'label'          => esc_html__( 'Thumb Style', 'jet-blog' ),
				'fields_options' => array(
					'box_font_size' => array(
						'selectors' => array(
							'{{WRAPPER}} ' . $css_scheme['thumb_item'] . ':hover ' . $css_scheme['thumb_index'] . ' .jet-blog-playlist__item-status' => 'font-size: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}} ' . $css_scheme['thumb_item'] . ':hover ' . $css_scheme['thumb_index'],
			),
			25
		);

		$this->_end_controls_tab();

		$this->_start_controls_tab(
			'tab_thumb_index_active',
			array(
				'label' => esc_html__( 'Active', 'jet-blog' ),
			)
		);

		$this->_add_group_control(
			\Jet_Blog_Group_Control_Box_Style::get_type(),
			array(
				'name'           => 'thumb_index_active',
				'label'          => esc_html__( 'Thumb Style', 'jet-blog' ),
				'fields_options' => array(
					'box_font_size' => array(
						'selectors' => array(
							'{{WRAPPER}} ' . $css_scheme['thumb_item'] . '.jet-blog-active ' . $css_scheme['thumb_index'] . ' .jet-blog-playlist__item-status' => 'font-size: {{SIZE}}{{UNIT}}',
						),
					),
				),
				'selector' => '{{WRAPPER}} ' . $css_scheme['thumb_item'] . '.jet-blog-active ' . $css_scheme['thumb_index'],
			),
			25
		);

		$this->_end_controls_tab();

		$this->_end_controls_tabs();

		$this->_end_controls_section();

		$this->_start_controls_section(
			'section_scroll_style',
			array(
				'label'      => esc_html__( 'Scrollbar Styles', 'jet-blog' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_control(
			'non_webkit_notice',
			array(
				'type' => Controls_Manager::RAW_HTML,
				'raw'  => esc_html__( 'Currently works only in -webkit- browsers', 'jet-blog' )
			),
			25
		);

		$this->_add_control(
			'scroll_thumb',
			array(
				'label'     => esc_html__( 'Scrollbar Thumb', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ::-webkit-scrollbar-thumb' => 'background-color: {{VALUE}};',
				),
			),
			25
		);

		$this->_add_control(
			'scroll_track',
			array(
				'label'     => esc_html__( 'Scrollbar Track', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ::-webkit-scrollbar-track' => 'background-color: {{VALUE}};',
				),
			),
			25
		);

		$this->_end_controls_section();

	}

	protected function render() {

		$this->_context = 'render';

		add_filter( 'no_texturize_tags', array( $this, 'prevent_playlist_from_texturizing' ) );
		add_filter( 'elementor/widget/render_content', array( $this, 'clear_notexturize_filter' ), 10, 2 );

		jet_blog_integration()->set_playlist_trigger();

		$this->_open_wrap();
		include $this->_get_global_template( 'index' );
		$this->_close_wrap();

		$video_data = jet_blog_video_data();

		if ( ! empty( $video_data->api_error ) && is_user_logged_in() && current_user_can( 'manage_options' ) ) {
			echo '<div style="color: red; border: 1px solid red; padding: 10px; margin-top: 10px;">';
			echo esc_html( $video_data->api_error );
			echo '</div>';
		}
	}

	/**
	 * Remove div tags from texturizing for video playlist.
	 *
	 * @param  array $tags No texturized tags array
	 * @return array
	 */
	public function prevent_playlist_from_texturizing( $tags ) {
		$tags[] = 'div';
		return $tags;
	}

	/**
	 * Remove $this->prevent_playlist_from_texturizing() from no_texturize_tags hook after tabs content processing to avoid unnecessary firing.
	 * @param  string $widget_content  Rendered widget content.
	 * @param  object $widget_instance Processed widget instance.
	 * @return string
	 */
	public function clear_notexturize_filter( $widget_content, $widget_instance ) {

		if ( 'tabs' === $widget_instance->get_name() ) {
			remove_filter( 'no_texturize_tags', array( $this, 'prevent_playlist_from_texturizing' ) );
		}

		return $widget_content;
	}

	/**
	 * Change height in iframe to new value from settings
	 *
	 * @param  string $html
	 * @return string
	 */
	public function adjust_height( $html ) {

		$settings = $this->get_settings();
		$height   = $settings['playlist_height'];
		$html     = preg_replace( '/width=[\'\"]\d+[\'\"]/', 'width="100%"', $html );

		return preg_replace( '/height=[\'\"]\d+[\'\"]/', 'height="' . $height . '"', $html );
	}

	/**
	 * Print playlist container classes list
	 *
	 * @return string
	 */
	public function _container_classes( $settings ) {

		$classes     = array( 'jet-blog-playlist' );
		$orientation = $settings['thumbnails_orientation'];
		$v_position  = $settings['thumbnails_v_position'];
		$h_position  = $settings['thumbnails_h_position'];

		$classes[] = 'jet-tumbs-' . $orientation;
		$classes[] = 'jet-tumbs-v-pos-' . $v_position;
		$classes[] = 'jet-tumbs-h-pos-' . $h_position;

		if ( 'yes' === $settings['show_scroll_on_hover'] ) {
			$classes[] = 'jet-scroll-on-hover';
		} else {
			$classes[] = 'jet-scroll-regular';
		}

		$classes = apply_filters( 'jet-blog/video-playlist/container-classes', $classes );

		echo implode( ' ', $classes );
	}

	public function _get_video_data_atts( $video_data, $item, $settings, $index ) {

        $allowed_tags = array(
            'iframe' => array(
                'src'             => true,
                'height'          => true,
                'width'           => true,
                'frameborder'     => true,
                'allow'           => true,
                'allowfullscreen' => true,
                'title'           => true,
                'referrerpolicy'  => true,
                'loading'         => true,
            )
        );

		$data = array(
			'data-id'          => sanitize_key($item['_id']),
			'data-video_id'    => sanitize_text_field($video_data['video_id']),
			'data-provider'    => sanitize_text_field(strtolower($video_data['provider_name'])),
            'data-html'        => wp_json_encode( $this->adjust_height( wp_kses( $video_data['html'], $allowed_tags ) ) ),
			'data-height'      => absint($settings['playlist_height']),
			'data-video_index' => absint($index) + 1,
			'data-video_start' => isset($item['start_time']) ? absint($item['start_time']) : '',
		);

		$result = '';

		foreach ( $data as $key => $value ) {
			$result .= sprintf(' %s=\'%s\'', esc_attr($key), esc_attr($value));
		}

		return $result;
	}

	public function _video_counter( $settings, $list ) {

		if ( 'yes' !== $settings['show_counter'] ) {
			return;
		}

		$suffix = '';

		if ( ! empty( $settings['counter_suffix'] ) ) {
			$suffix = sprintf(
				' <span class="jet-blog-playlist__counter-suffix">%s</span>',
				$settings['counter_suffix']
			);
		}

		printf(
			'<div class="jet-blog-playlist__counter"><span class="jet-blog-playlist__counter-val">1</span>/%1$s%2$s</div>',
			count( $list ),
			$suffix
		);

	}

	public function _get_hide_settings( $settings ) {
		$hide_index    = isset( $settings['hide_index'] ) ? json_encode( $settings['hide_index'] ) : [];
		$hide_duration = isset( $settings['hide_duration'] ) ? json_encode( $settings['hide_duration'] ) : [];
		$hide_image    = isset( $settings['hide_image'] ) ? json_encode( $settings['hide_image'] ) : [];
		$result        = '';

		$result .= ' data-hide-index=\'' . esc_attr( $hide_index ) . '\' data-hide-duration=\'' . esc_attr( $hide_duration ) . '\' data-hide-image=\'' . esc_attr( $hide_image ) . '\'';

		return $result;
	}

	public function _get_custom_thumb( $item ) {

		if ( empty( $item['custom_thumb']['id'] ) ) {
			return '';
		}

		$thumb_size = isset( $item['custom_thumb_size'] ) ? $item['custom_thumb_size'] : 'thumbnail';

		return wp_get_attachment_image_url( $item['custom_thumb']['id'], $thumb_size );
	}

	/**
	 * Get human-readable date difference.
	 *
	 * @param string $date The date to compare with the current date.
	 * @return string The human-readable difference between the provided date and the current date.
	 */
	public function _get_human_readable_date_diff( $date ) {

		$date_time    = strtotime( $date );
		$current_time = current_time( 'timestamp' );

		return esc_html__( human_time_diff( $date_time, $current_time ) . ' ago', 'jet-blog' );

	}

	/**
	 * Format the publication date.
	 *
	 * @param string $date The date to be formatted.
	 * @param string $format The format to apply to the date. Defaults to 'F j, Y'.
	 * @return string The formatted date.
	 */
	public function _format_date( $date, $format = 'F j, Y' ) {

		return date( $format, strtotime( $date ) );

	}

    /**
     * Retrieve the list of videos according to the selected source type.
     *
     * Supports the following source types:
     * - 'custom': Uses manually entered video list.
     * - 'yt_source': Fetches videos from YouTube playlist or channel URL.
     * - 'repeater': Retrieves videos dynamically from the specified repeater meta field.
     *
     * @param  array $settings Current widget settings.
     *
     * @return array Array of video data (title, URL, custom_thumb, _id).
     */
    protected function get_videos_list( $settings ) {
        $post_id = get_the_ID();
        $videos = get_post_meta($post_id, 'videos', true);

        // Custom source
        if ( $settings['source'] === 'custom' ) {
            return $settings['videos_list'];
        }

        // Youtube playlist
        if ( $settings['source'] === 'yt_source' ) {
            $source_url = $settings['source_url'];
            $max_results = $settings['max_results'];
            $caching = empty($settings['disable_caching']) || $settings['disable_caching'] !== 'yes';

            return jet_blog_video_data()->get_video_list_from_source($source_url, $max_results, $caching);
        }

        // Repeater
        if ( $settings['source'] === 'repeater' ) {
            $post_id = get_the_ID();
            $repeater = get_post_meta($post_id, $settings['repeater_meta_name'], true);
            $url_key = $settings['repeater_url_name'];
            $title_key = $settings['repeater_title_name'];
            $videos = [];
            if ( is_array($repeater) ) {
                foreach ( $repeater as $i => $item ) {
                    $videos[] = [
                        'title' => $item[$title_key],
                        'url'   => $item[$url_key],
                        'custom_thumb' => '',
                        '_id' => 'custom-' . $i,
                    ];
                }
            }
            return $videos;
        }

        return [];
    }

}
