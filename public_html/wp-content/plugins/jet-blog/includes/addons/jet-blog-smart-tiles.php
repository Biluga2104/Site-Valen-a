<?php
/**
 * Class: Jet_Blog_Smart_Tiles
 * Name: Smart Posts Tiles
 * Slug: jet-blog-smart-tiles
 */

namespace Elementor;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Jet_Blog_Smart_Tiles extends Jet_Blog_Base {

	public $_current_post_index = 0;
	public $_current_posts_num  = 0;

	public function get_name() {
		return 'jet-blog-smart-tiles';
	}

	public function get_title() {
		return esc_html__( 'Smart Posts Tiles', 'jet-blog' );
	}

	public function get_icon() {
		return 'jet-blog-icon-smart-tiles';
	}

	public function get_jet_help_url() {
		return 'https://crocoblock.com/knowledge-base/articles/how-to-arrange-the-blog-posts-in-the-form-of-attractive-tile-layout-jetblog-smart-posts-tiles-widget-overview/';
	}

	public function get_categories() {
		return array( 'cherry' );
	}

	public function get_script_depends() {
		return array( 'jet-slick' );
	}

	protected function register_controls() {

		$layout_data       = $this->_layout_data();
		$available_layouts = array();
		$has_rows          = array();

		foreach ( $layout_data as $key => $data ) {
			$available_layouts[ $key ] = array(
				'title' => $data['label'],
				'icon'  => $data['icon'],
			);

			if ( true === $data['has_rows'] ) {
				$has_rows[] = $key;
			}

		}

		$css_scheme = apply_filters(
			'jet-blog/smart-tiles/css-scheme',
			array(
				'slide'      => '.jet-smart-tiles-slide__wrap',
				'box'        => '.jet-smart-tiles__box',
				'title'      => '.jet-smart-tiles__box-title',
				'excerpt'    => '.jet-smart-tiles__box-excerpt',
				'meta'       => '.jet-smart-tiles__meta',
				'meta_avatar'=> '.jet-smart-tiles__meta .has-author-avatar',
				'meta_item'  => '.jet-smart-tiles__meta-item',
				'terms'      => '.jet-smart-tiles__terms',
				'terms_link' => '.jet-smart-tiles__terms-link',
			)
		);

		if (isset($settings['use_scroll_slider_mobile']) && $settings['use_scroll_slider_mobile'] === 'yes' && isset($settings['carousel_enabled']) && $settings['carousel_enabled'] === 'yes') {
			$settings['use_scroll_slider_mobile'] = '';
			$this->set_settings('use_scroll_slider_mobile', '');
		}

		$this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__( 'General', 'jet-blog' ),
			)
		);

		$this->add_control(
			'layout',
			array(
				'label'       => esc_html__( 'Layout', 'jet-blog' ),
				'label_block' => true,
				'type'        => Controls_Manager::CHOOSE,
				'default'     => '2-1-2',
				'options'     => $available_layouts,
				'render_type' => 'template',
				'classes'     => 'jet-blog-layout-control',
			)
		);

		$this->add_control(
			'rows_num',
			array(
				'label'     => esc_html__( 'Rows Number', 'jet-blog' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 1,
				'options'   => jet_blog_tools()->get_select_range( 3 ),
				'condition' => array(
					'layout' => array( '2-x', '3-x', '4-x' ),
				),
			)
		);

		$this->add_control(
			'use_scroll_slider_mobile',
			array(
				'label'        => esc_html__( 'Use Scroll Slider For Mobile', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition' => array(
					'carousel_enabled' => '',
				),
			)
		);

		$this->add_control(
			'scroll_slider_note',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'The Scroll Slider For Mobile option is not allowed when the Carousel is enabled.', 'jet-blog' ),
				'condition' => [
					'carousel_enabled' => 'yes',
				],
			]
		);

		$this->add_control(
			'mobile_rows_num',
			array(
				'label'     => esc_html__( 'Mobile Rows Number', 'jet-blog' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 2,
				'options'   => jet_blog_tools()->get_select_range( 5 ),
				'condition' => array(
					'use_scroll_slider_mobile' => 'yes',
				),
			)
		);

		$this->add_control(
			'mobile_col_width',
			array(
				'label'       => esc_html__( 'Mobile Column Width ( px )', 'jet-blog' ),
				'label_block' => true,
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( 'px' ),
				'default'     => array(
					'unit' => 'px',
					'size' => 320,
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 400,
					),
				),
				'condition' => array(
					'use_scroll_slider_mobile' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'min_height',
			array(
				'label'      => esc_html__( 'Min Height', 'jet-blog' ),
				'label_block' => true,
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 300,
				),
				'render_type' => 'template',
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 1200,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['slide'] => 'min-height: {{SIZE}}{{UNIT}};',
					'.elementor-msie {{WRAPPER}} ' . $css_scheme['slide'] => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$main_img_selectors = apply_filters( 'jet-blog/smart-tiles/main-image-selectors',array(
			'{{WRAPPER}} .jet-smart-tiles-slide__wrap.layout-2-1-2'   => 'grid-template-columns: 1fr {{SIZE}}% 1fr; -ms-grid-columns: 1fr {{SIZE}}{{UNIT}} 1fr;',
			'{{WRAPPER}} .jet-smart-tiles-slide__wrap.layout-1-1-2-h' => 'grid-template-columns: {{SIZE}}% 1fr 1fr; -ms-grid-columns: {{SIZE}}{{UNIT}} 1fr 1fr;',
			'{{WRAPPER}} .jet-smart-tiles-slide__wrap.layout-1-1-2-v' => 'grid-template-columns: {{SIZE}}% 1fr 1fr; -ms-grid-columns: {{SIZE}}{{UNIT}} 1fr 1fr;',
			'{{WRAPPER}} .jet-smart-tiles-slide__wrap.layout-1-2'     => 'grid-template-columns: {{SIZE}}% 1fr; -ms-grid-columns: {{SIZE}}{{UNIT}} 1fr',
			'{{WRAPPER}} .jet-smart-tiles-slide__wrap.layout-1-2-2'   => 'grid-template-columns: {{SIZE}}% 1fr 1fr; -ms-grid-columns: {{SIZE}}{{UNIT}} 1fr 1fr;',
		) );

		$main_img_conditions = apply_filters( 'jet-blog/smart-tiles/main-image-conditions',array(
			'2-1-2',
			'1-1-2-h',
			'1-1-2-v',
			'1-2',
			'1-2-2',
		) );

		$this->add_responsive_control(
			'main_img_width',
			array(
				'label'       => esc_html__( 'Main Box Width ( % )', 'jet-blog' ),
				'label_block' => true,
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( '%' ),
				'default'     => array(
					'unit' => '%',
					'size' => 50,
				),
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'devices'   => array( 'widescreen', 'desktop', 'laptop', 'tablet', 'tablet_extra', 'mobile_extra' ),
				'selectors' => $main_img_selectors,
				'condition' => array(
					'layout' => $main_img_conditions,
				),
			)
		);

		$this->add_control(
			'image_size',
			array(
				'type'      => 'select',
				'label'     => esc_html__( 'Image Size', 'jet-blog' ),
				'default'   => 'full',
				'options'   => jet_blog_tools()->get_image_sizes(),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'show_terms',
			array(
				'label'        => esc_html__( 'Show Post Terms', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'show_terms_tax',
			array(
				'label'     => esc_html__( 'Show Terms From', 'jet-blog' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'category',
				'options'   => jet_blog_tools()->get_post_taxonomies(),
				'condition' => array(
					'show_terms' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_terms_num',
			array(
				'label'   => esc_html__( 'Max Terms to Show', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '1',
				'options' => array(
					'all' => esc_html__( 'All', 'jet-blog' ),
					'1'   => 1,
					'2'   => 2,
					'3'   => 3,
					'4'   => 4,
				),
				'condition' => array(
					'show_terms' => 'yes',
				),
			)
		);

		$this->add_control(
			'title_length',
			array(
				'label'       => esc_html__( 'Title Max Length (Words)', 'jet-blog' ),
				'description' => esc_html__( 'Set 0 to show full title', 'jet-blog' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'max'         => 15,
				'step'        => 1,
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'excerpt_length',
			array(
				'label'       => esc_html__( 'Excerpt Length', 'jet-blog' ),
				'description' => esc_html__( 'Set 0 to hide excerpt or -1 to show full excerpt', 'jet-blog' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 10,
				'min'         => -1,
				'max'         => 200,
				'step'        => 1,
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'excerpt_trimmed_ending',
			array(
				'label'   => esc_html__( 'Excerpt Trimmed Ending', 'jet-blog' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '...',
			)
		);

		$this->add_control(
			'excerpt_on_hover',
			array(
				'label'        => esc_html__( 'Show Excerpt on Small Boxes Only on Hover', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => '',
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'show_meta',
			array(
				'label'        => esc_html__( 'Post Meta', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'jet-blog' ),
				'label_off'    => esc_html__( 'Hide', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'show_author',
			array(
				'label'        => esc_html__( 'Show Post Author', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'jet-blog' ),
				'label_off'    => esc_html__( 'Hide', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'   => array(
					'show_meta' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_author_avatar',
			array(
				'label'        => esc_html__( 'Show Author Avatar', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'jet-blog' ),
				'label_off'    => esc_html__( 'Hide', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'   => array(
					'show_meta' => 'yes',
					'show_author' => 'yes',
				),
			)
		);

		$this->add_control(
			'get_avatar_from',
			array(
				'label'        => esc_html__( 'Get Avatar From', 'jet-blog' ),
				'type'         => Controls_Manager::SELECT,
				'options'      => array(
					'default' => esc_html__( 'Default Avatar', 'jet-blog' ),
					'custom'  => esc_html__( 'Get from Custom Field', 'jet-blog' ),
				),
				'default'      => 'default',
				'condition'    => array(
					'show_meta' => 'yes',
					'show_author_avatar' => 'yes',
					'show_author' => 'yes',
				),
			)
		);

		$this->add_control(
			'avatar_custom_field',
			array(
				'label'       => esc_html__( 'Field Name', 'jet-blog' ),
				'type'        => Controls_Manager::TEXT,
				'condition'   => array(
					'show_meta' => 'yes',
					'show_author_avatar' => 'yes',
					'get_avatar_from' => 'custom',
					'show_author' => 'yes',
				),
			)
		);

		$this->add_control(
			'avatar_size',
			array(
				'label'       => esc_html__( 'Avatar Size', 'jet-blog' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => 50,
				),
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 200,
					),
				),
				'condition'   => array(
					'show_meta' => 'yes',
					'show_author_avatar' => 'yes',
					'show_author' => 'yes',
				),
			)
		);

		$this->add_control(
			$this->_new_icon_prefix . 'show_author_icon',
			array(
				'type'             => Controls_Manager::ICONS,
				'label'            => esc_html__( 'Author Icon', 'jet-blog' ),
				'label_block'      => false,
				'skin'             => 'inline',
				'fa4compatibility' => 'show_author_icon',
				'default'          => array(
					'value'   => 'fas fa-user',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'show_meta'   => 'yes',
					'show_author' => 'yes',
					'show_author_avatar!' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_date',
			array(
				'label'        => esc_html__( 'Show Post Date', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'jet-blog' ),
				'label_off'    => esc_html__( 'Hide', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'   => array(
					'show_meta' => 'yes',
				),
			)
		);

		$this->add_control(
			$this->_new_icon_prefix . 'show_date_icon',
			array(
				'type'             => Controls_Manager::ICONS,
				'label'            => esc_html__( 'Date Icon', 'jet-blog' ),
				'label_block'      => false,
				'skin'             => 'inline',
				'fa4compatibility' => 'show_date_icon',
				'default'          => array(
					'value'   => 'fas fa-calendar-alt',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'show_meta' => 'yes',
					'show_date' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_comments',
			array(
				'label'        => esc_html__( 'Show Post Comments', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'jet-blog' ),
				'label_off'    => esc_html__( 'Hide', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'   => array(
					'show_meta' => 'yes',
				),
			)
		);

		$this->add_control(
			$this->_new_icon_prefix . 'show_comments_icon',
			array(
				'type'             => Controls_Manager::ICONS,
				'label'            => esc_html__( 'Comments Icon', 'jet-blog' ),
				'label_block'      => false,
				'skin'             => 'inline',
				'fa4compatibility' => 'show_comments_icon',
				'default'          => array(
					'value'   => 'fas fa-comments',
					'library' => 'fa-solid',
				),
				'condition'        => array(
					'show_meta'     => 'yes',
					'show_comments' => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_query',
			array(
				'label' => esc_html__( 'Query & Controls', 'jet-blog' ),
			)
		);

		$this->add_control(
			'is_archive_template',
			array(
				'label'        => esc_html__( 'Use as Archive Template', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => jet_blog_tools()->get_archive_control_desc(),
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'use_custom_query',
			array(
				'label'        => esc_html__( 'Use Custom Query', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'true',
				'default'      => '',
				'condition'   => array(
					'is_archive_template!' => 'yes',
				),
			)
		);

		$custom_query_link = sprintf(
			'<a href="https://crocoblock.com/wp-query-generator/" target="_blank">%s</a>',
			__( 'Generate custom query', 'jet-blog' )
		);

		$this->add_control(
			'custom_query',
			array(
				'type'        => Controls_Manager::TEXTAREA,
				'label'       => esc_html__( 'Set custom query', 'jet-blog' ),
				'default'     => '',
				'description' => $custom_query_link,
				'condition'   => array(
					'is_archive_template!' => 'yes',
					'use_custom_query'     => 'true',
				),
			)
		);

		do_action( 'jet-blog/query-controls', $this, true );

		$this->add_control(
			'post_type',
			array(
				'label'   => esc_html__( 'Post Type', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT2,
				'multiple' => true,
				'default' => array( 'post' ),
				'options' => jet_blog_tools()->get_post_types(),
				'condition' => array(
					'use_custom_query!'    => 'true',
					'is_archive_template!' => 'yes',
				),
			)
		);

		$this->add_control(
			'custom_query_by',
			array(
				'label'   => esc_html__( 'Query Custom Posts By', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'all',
				'options' => array(
					'all'   => esc_html__( 'All', 'jet-blog' ),
					'ids'   => esc_html__( 'IDs', 'jet-blog' ),
					'terms' => esc_html__( 'Terms', 'jet-blog' ),
				),
				'condition' => array(
					'use_custom_query!'    => 'true',
					'is_archive_template!' => 'yes',
					'post_type!'           => '',
				),
			)
		);

		$this->add_control(
			'custom_terms_ids',
			array(
				'label'       => esc_html__( 'Get custom posts from terms:', 'jet-blog' ),
				'description' => esc_html__( 'Set comma separated terms IDs list (10, 22, 19 etc.)', 'jet-blog' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '',
				'dynamic'     => array(
					'active'     => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
					),
				),
				'condition'   => array(
					'use_custom_query!'    => 'true',
					'is_archive_template!' => 'yes',
					'post_type!'           => '',
					'custom_query_by'      => 'terms',
				),
			)
		);

		$this->add_control(
			'post_ids',
			array(
				'type'        => 'text',
				'label'       => esc_html__( 'Set comma separated IDs list (10, 22, 19 etc.)', 'jet-blog' ),
				'default'     => '',
				'label_block' => true,
				'dynamic'     => array(
					'active'     => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
					),
				),
				'condition' => array(
					'use_custom_query!'    => 'true',
					'is_archive_template!' => 'yes',
					'post_type!'           => '',
					'custom_query_by'      => 'ids',
				),
			)
		);

		$this->add_control(
			'query_by',
			array(
				'label'   => esc_html__( 'Query Posts By', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'category',
				'options' => array(
					'all'      => esc_html__( 'All', 'jet-blog' ),
					'category' => esc_html__( 'Categories', 'jet-blog' ),
					'post_tag' => esc_html__( 'Tags', 'jet-blog' ),
					'ids'      => esc_html__( 'IDs', 'jet-blog' ),
				),
				'condition' => array(
					'use_custom_query!'    => 'true',
					'is_archive_template!' => 'yes',
					'post_type'            => 'post',
				),
			)
		);

		$this->add_control(
			'category_ids',
			array(
				'label'       => esc_html__( 'Get posts from categories:', 'jet-blog' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => jet_blog_tools()->get_terms( 'category' ),
				'label_block' => true,
				'multiple'    => true,
				'condition'   => array(
					'use_custom_query!'    => 'true',
					'is_archive_template!' => 'yes',
					'post_type'            => 'post',
					'query_by'             => 'category',
				),
			)
		);

		$this->add_control(
			'post_tag_ids',
			array(
				'label'       => esc_html__( 'Get posts from tags:', 'jet-blog' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => jet_blog_tools()->get_terms( 'post_tag' ),
				'label_block' => true,
				'multiple'    => true,
				'condition'   => array(
					'use_custom_query!'    => 'true',
					'is_archive_template!' => 'yes',
					'post_type'            => 'post',
					'query_by'             => 'post_tag',
				),
			)
		);

		$this->add_control(
			'include_ids',
			array(
				'type'        => 'text',
				'label'       => esc_html__( 'Set comma separated IDs list (10, 22, 19 etc.)', 'jet-blog' ),
				'default'     => '',
				'label_block' => true,
				'dynamic'     => array(
					'active'     => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
					),
				),
				'condition'   => array(
					'use_custom_query!'    => 'true',
					'is_archive_template!' => 'yes',
					'post_type'            => 'post',
					'query_by'             => 'ids',
				),
			)
		);

		$this->add_control(
			'exclude_ids',
			array(
				'type'        => 'text',
				'label_block' => true,
				'description' => esc_html__( 'If this is used with query posts by ID, it will be ignored. Note: use the %current_id% macros to exclude the current post.', 'jet-blog' ),
				'label'       => esc_html__( 'Exclude posts by IDs (eg. 10, 22, 19 etc.)', 'jet-blog' ),
				'default'     => '',
				'dynamic'     => array(
					'active' => true,
					'categories' => array(
						TagsModule::POST_META_CATEGORY,
					),
				),
				'condition' => array(
					'use_custom_query!'    => 'true',
					'is_archive_template!' => 'yes',
				),
			)
		);

		$this->add_control(
			'posts_offset',
			array(
				'label'     => esc_html__( 'Posts Offset', 'jet-blog' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 0,
				'min'       => 0,
				'max'       => 300,
				'step'      => 1,
				'condition' => array(
					'use_custom_query!'    => 'true',
					'is_archive_template!' => 'yes',
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => esc_html__( 'Order', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => array(
					'ASC'  => esc_html__( 'ASC', 'jet-blog' ),
					'DESC' => esc_html__( 'DESC', 'jet-blog' ),
				),
				'condition' => array(
					'use_custom_query!'    => 'true',
					'is_archive_template!' => 'yes',
				),
			)
		);

		$this->add_control(
			'order_by',
			array(
				'label'   => esc_html__( 'Order by', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'none'          => esc_html__( 'None', 'jet-blog' ),
					'ID'            => esc_html__( 'ID', 'jet-blog' ),
					'author'        => esc_html__( 'Author', 'jet-blog' ),
					'title'         => esc_html__( 'Title', 'jet-blog' ),
					'name'          => esc_html__( 'Name', 'jet-blog' ),
					'type'          => esc_html__( 'Type', 'jet-blog' ),
					'date'          => esc_html__( 'Date', 'jet-blog' ),
					'modified'      => esc_html__( 'Modified', 'jet-blog' ),
					'parent'        => esc_html__( 'Parent', 'jet-blog' ),
					'rand'          => esc_html__( 'Rand', 'jet-blog' ),
					'comment_count' => esc_html__( 'Comment count', 'jet-blog' ),
					'relevance'     => esc_html__( 'Relevance', 'jet-blog' ),
					'menu_order'    => esc_html__( 'Menu order', 'jet-blog' ),
					'post__in'      => esc_html__( 'Preserve post ID order given in the "Include posts by IDs" option', 'jet-blog' ),
				),
				'condition' => array(
					'use_custom_query!'    => 'true',
					'is_archive_template!' => 'yes',
				),
			)
		);

		$this->add_control(
			'meta_query',
			array(
				'label'        => esc_html__( 'Filter by Custom Field', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition' => array(
					'use_custom_query!'    => 'true',
					'is_archive_template!' => 'yes',
				),
			)
		);

		$this->add_control(
			'meta_key',
			array(
				'type'        => 'text',
				'label_block' => true,
				'label'       => esc_html__( 'Custom Field Key', 'jet-blog' ),
				'default'     => '',
				'condition'   => array(
					'use_custom_query!'    => 'true',
					'is_archive_template!' => 'yes',
					'meta_query'           => 'yes',
				),
			)
		);

		$this->add_control(
			'meta_value',
			array(
				'type'        => 'text',
				'label_block' => true,
				'label'       => esc_html__( 'Custom Field Value', 'jet-blog' ),
				'default'     => '',
				'condition'   => array(
					'use_custom_query!'    => 'true',
					'is_archive_template!' => 'yes',
					'meta_query'           => 'yes',
				),
			)
		);

		$this->add_control(
			'carousel_enabled',
			array(
				'label'        => esc_html__( 'Enable Carousel', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'separator'    => 'before',
				'default'      => '',
				'condition' => array(
					'is_archive_template!' => 'yes',
					'use_scroll_slider_mobile' => '',
				),
			)
		);

		$this->add_control(
			'carousel_note',
			[
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => esc_html__( 'The Carousel option is not allowed when the Scroll Slider For Mobile is enabled.', 'jet-blog' ),
				'condition' => [
					'use_scroll_slider_mobile' => 'yes',
				],
			]
		);

		$this->add_control(
			'hide_unfilled_slide',
			array(
				'label'        => esc_html__( 'Hide unfilled slider', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => array(
					'carousel_enabled'     => 'yes',
					'is_archive_template!' => 'yes',
				),
			)
		);

		$this->add_control(
			'slides_num',
			array(
				'label'       => esc_html__( 'Number of Slides', 'jet-blog' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 3,
				'min'         => 1,
				'max'         => 20,
				'step'        => 1,
				'condition'   => array(
					'use_custom_query!'    => 'true',
					'is_archive_template!' => 'yes',
					'carousel_enabled'     => 'yes',
				),
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => esc_html__( 'Autoplay', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'condition'    => array(
					'carousel_enabled'     => 'yes',
					'is_archive_template!' => 'yes',
				),
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'     => esc_html__( 'Autoplay Speed', 'jet-blog' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'condition' => array(
					'carousel_enabled'     => 'yes',
					'autoplay'             => 'yes',
					'is_archive_template!' => 'yes',
				),
			)
		);

		$this->add_control(
			'show_arrows',
			array(
				'label'        => esc_html__( 'Show Controls Arrows', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'jet-blog' ),
				'label_off'    => esc_html__( 'Hide', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition'   => array(
					'is_archive_template!' => 'yes',
					'carousel_enabled'     => 'yes',
				),
			)
		);

		$this->add_control(
			'arrow_type',
			array(
				'label'       => esc_html__( 'Select Control Arrows Type', 'jet-blog' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'default'     => 'fa fa-angle-left',
				'options'     => jet_blog_tools()->get_available_prev_arrows_list(),
				'condition'   => array(
					'is_archive_template!' => 'yes',
					'carousel_enabled'     => 'yes',
					'show_arrows'          => 'yes',
				),
			)
		);

		$this->add_control(
			'show_arrows_on_hover',
			array(
				'label'        => esc_html__( 'Show Arrows Only on Hover', 'jet-blog' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-blog' ),
				'label_off'    => esc_html__( 'No', 'jet-blog' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'   => array(
					'is_archive_template!' => 'yes',
					'carousel_enabled'     => 'yes',
					'show_arrows'          => 'yes',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_custom_fields',
			array(
				'label' => esc_html__( 'Custom Fields', 'jet-blog' ),
			)
		);

		$this->_add_meta_controls( 'title_related', esc_html__( 'Before/After Title', 'jet-blog' ) );

		$this->_add_meta_controls( 'content_related', esc_html__( 'Before/After Content', 'jet-blog' ) );

		$this->end_controls_section();

		$this->_start_controls_section(
			'section_box_style',
			array(
				'label'      => esc_html__( 'Box', 'jet-blog' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_control(
			'boxes_gap',
			array(
				'label'      => esc_html__( 'Gap Between Boxes', 'jet-blog' ),
				'label_block' => true,
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'default'    => array(
					'unit' => 'px',
					'size' => 1,
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 60,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['slide'] => 'grid-column-gap: {{SIZE}}{{UNIT}}; grid-row-gap: {{SIZE}}{{UNIT}};',
					'(mobile){{WRAPPER}} ' . $css_scheme['box'] => 'margin-bottom: {{SIZE}}{{UNIT}};',

				),
			),
			25
		);

		$this->_add_responsive_control(
			'boxes_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['box'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'boxes_border',
				'label'          => esc_html__( 'Border', 'jet-blog' ),
				'placeholder'    => '1px',
				'selector'       => '{{WRAPPER}} ' . $css_scheme['box'],
			),
			75
		);

		$this->_add_responsive_control(
			'boxes_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['box'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'boxes_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['box'],
			),
			100
		);

		$this->_add_control(
			'boxes_overlay_styles',
			array(
				'label'     => esc_html__( 'Box Overlay', 'jet-blog' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			50
		);

		$this->_start_controls_tabs( 'tabs_overlay_style', 50 );

		$this->_start_controls_tab(
			'tab_overlay_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-blog' ),
			),
			50
		);

		$this->_add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'boxes_overlay_background_normal',
				'selector' => '{{WRAPPER}} ' . $css_scheme['box'] . ':before',
			),
			50
		);

		$this->_end_controls_tab( 50 );

		$this->_start_controls_tab(
			'tab_overlay_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-blog' ),
			),
			50
		);

		$this->_add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'boxes_overlay_background_hover',
				'selector' => '{{WRAPPER}} ' . $css_scheme['box'] . ':hover:before',
			),
			50
		);

		$this->_end_controls_tab( 50 );

		$this->_end_controls_tabs( 50 );

		$this->_end_controls_section();

		$this->_start_controls_section(
			'section_content_style',
			array(
				'label'      => esc_html__( 'Content', 'jet-blog' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_control(
			'boxes_title_styles',
			array(
				'label'     => esc_html__( 'Title', 'jet-blog' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			25
		);

		$this->_add_control(
			'boxes_title_bg',
			array(
				'label' => esc_html__( 'Background Color', 'jet-blog' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'background-color: {{VALUE}}',
				),
			),
			75
		);

		$this->_add_control(
			'boxes_title_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_control(
			'boxes_title_color_hover',
			array(
				'label'     => esc_html__( 'Color Hover', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .jet-smart-tiles:hover ' . $css_scheme['title']=> 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'boxes_title_typography',
				'selector' => '{{WRAPPER}}  ' . $css_scheme['title'],
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
			),
			50
		);

		$this->_add_responsive_control(
			'boxes_title_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_responsive_control(
			'boxes_title_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_responsive_control(
			'boxes_title_alignment',
			array(
				'label'   => esc_html__( 'Alignment', 'jet-blog' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'jet-blog' ),
						'icon'  => 'fa fa-arrow-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-blog' ),
						'icon'  => 'fa fa-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'jet-blog' ),
						'icon'  => 'fa fa-arrow-right',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'text-align: {{VALUE}};',
				),
				'classes' => 'jet-elements-text-align-control',
			),
			50
		);

		$this->_add_control(
			'boxes_main_title_styles',
			array(
				'label'     => esc_html__( 'Main Box Title', 'jet-blog' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			25
		);

		$this->_add_control(
			'boxes_main_title_bg',
			array(
				'label' => esc_html__( 'Background Color', 'jet-blog' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .layout-2-1-2 > div:nth-child( 3 ) ' . $css_scheme['title']   => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .layout-1-1-2-h > div:nth-child( 1 ) ' . $css_scheme['title'] => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .layout-1-1-2-v > div:nth-child( 1 ) ' . $css_scheme['title'] => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .layout-1-2 > div:nth-child( 1 ) ' . $css_scheme['title']     => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .layout-1-2-2 > div:nth-child( 1 ) ' . $css_scheme['title']   => 'background-color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_control(
			'boxes_main_title_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .layout-2-1-2 > div:nth-child( 3 ) ' . $css_scheme['title']   => 'color: {{VALUE}}',
					'{{WRAPPER}} .layout-1-1-2-h > div:nth-child( 1 ) ' . $css_scheme['title'] => 'color: {{VALUE}}',
					'{{WRAPPER}} .layout-1-1-2-v > div:nth-child( 1 ) ' . $css_scheme['title'] => 'color: {{VALUE}}',
					'{{WRAPPER}} .layout-1-2 > div:nth-child( 1 ) ' . $css_scheme['title']     => 'color: {{VALUE}}',
					'{{WRAPPER}} .layout-1-2-2 > div:nth-child( 1 ) ' . $css_scheme['title']   => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_control(
			'boxes_main_title_color_hover',
			array(
				'label'     => esc_html__( 'Color Hover', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .layout-2-1-2 > div:nth-child( 3 ):hover ' . $css_scheme['title']   => 'color: {{VALUE}}',
					'{{WRAPPER}} .layout-1-1-2-h > div:nth-child( 1 ):hover ' . $css_scheme['title'] => 'color: {{VALUE}}',
					'{{WRAPPER}} .layout-1-1-2-v > div:nth-child( 1 ):hover ' . $css_scheme['title'] => 'color: {{VALUE}}',
					'{{WRAPPER}} .layout-1-2 > div:nth-child( 1 ):hover ' . $css_scheme['title']     => 'color: {{VALUE}}',
					'{{WRAPPER}} .layout-1-2-2 > div:nth-child( 1 ):hover ' . $css_scheme['title']   => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'boxes_main_title_typography',
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				),
				'selector' => '{{WRAPPER}} .layout-2-1-2 > div:nth-child( 3 ) ' . $css_scheme['title'] . ', {{WRAPPER}} .layout-1-1-2-h > div:nth-child( 1 ) ' . $css_scheme['title'] . ', {{WRAPPER}} .layout-1-1-2-v > div:nth-child( 1 ) ' . $css_scheme['title'] . ', {{WRAPPER}} .layout-1-2 > div:nth-child( 1 ) ' . $css_scheme['title'] . ', {{WRAPPER}} .layout-1-2-2 > div:nth-child( 1 ) ' . $css_scheme['title'],
			),
			50
		);

		$this->_add_responsive_control(
			'boxes_main_title_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors' => array(
					'{{WRAPPER}} .layout-2-1-2 > div:nth-child( 3 ) ' . $css_scheme['title']   => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .layout-1-1-2-h > div:nth-child( 1 ) ' . $css_scheme['title'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .layout-1-1-2-v > div:nth-child( 1 ) ' . $css_scheme['title'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .layout-1-2 > div:nth-child( 1 ) ' . $css_scheme['title']     => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .layout-1-2-2 > div:nth-child( 1 ) ' . $css_scheme['title']   => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_control(
			'boxes_text_style',
			array(
				'label'     => esc_html__( 'Post Text', 'jet-blog' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			25
		);

		$this->_add_control(
			'boxes_text_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['excerpt'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'boxes_text_typography',
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}}  ' . $css_scheme['excerpt'],
			),
			50
		);

		$this->_add_responsive_control(
			'boxes_text_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['excerpt'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_responsive_control(
			'boxes_text_alignment',
			array(
				'label'   => esc_html__( 'Alignment', 'jet-blog' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'jet-blog' ),
						'icon'  => 'fa fa-arrow-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-blog' ),
						'icon'  => 'fa fa-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'jet-blog' ),
						'icon'  => 'fa fa-arrow-right',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['excerpt'] => 'text-align: {{VALUE}};',
				),
				'classes' => 'jet-elements-text-align-control',
			),
			50
		);

		$this->_end_controls_section();

		$this->_start_controls_section(
			'section_meta_style',
			array(
				'label'      => esc_html__( 'Meta', 'jet-blog' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_control(
			'meta_icon_size',
			array(
				'label'      => esc_html__( 'Meta Icon Size', 'jet-blog' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 12,
						'max' => 90,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['meta_item'] . ' .jet-smart-tiles__meta-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_control(
			'meta_icon_gap',
			array(
				'label'      => esc_html__( 'Meta Icon Gap', 'jet-blog' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 90,
					),
				),
				'selectors' => array(
					'body:not(.rtl) {{WRAPPER}} ' . $css_scheme['meta_item'] . ' .jet-smart-tiles__meta-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
					'body.rtl {{WRAPPER}} ' . $css_scheme['meta_item'] . ' .jet-smart-tiles__meta-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['meta'] . ' .has-author-avatar' => 'margin-top: {{SIZE}}{{UNIT}} !important;margin-bottom: {{SIZE}}{{UNIT}} !important;',
				),
			),
			50
		);

		$this->_add_control(
			'meta_bg',
			array(
				'label' => esc_html__( 'Background Color', 'jet-blog' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['meta'] => 'background-color: {{VALUE}}',
				),
			),
			75
		);

		$this->_add_control(
			'meta_color',
			array(
				'label'  => esc_html__( 'Text Color', 'jet-blog' ),
				'type'   => Controls_Manager::COLOR,
				'global' => array(
					'default' => Global_Colors::COLOR_TEXT,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['meta'] . ' .jet-smart-tiles__meta-item' => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'meta_typography',
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}} ' . $css_scheme['meta'],
			),
			50
		);

		$this->_add_responsive_control(
			'meta_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['meta'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_responsive_control(
			'meta_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['meta'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_responsive_control(
			'meta_alignment',
			array(
				'label'   => esc_html__( 'Alignment', 'jet-blog' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'jet-blog' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-blog' ),
						'icon'  => 'fa fa-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'jet-blog' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['meta'] => 'text-align: {{VALUE}};',
					'{{WRAPPER}} ' . $css_scheme['meta_avatar'] => 'justify-content: {{VALUE}};',
				),
				'classes' => 'jet-elements-text-align-control',
			),
			50
		);

		$this->_add_control(
			'meta_divider',
			array(
				'label'     => esc_html__( 'Meta Divider', 'jet-blog' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['meta_item'] . ':not(:first-child):before' => 'content: "{{VALUE}}";',
				),
			),
			50
		);

		$this->_add_control(
			'meta_divider_gap',
			array(
				'label'      => esc_html__( 'Divider Gap', 'jet-blog' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 90,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['meta_item'] . ':not(:first-child):before' => 'margin-left: {{SIZE}}{{UNIT}};margin-right: {{SIZE}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_control(
			'meta_avatar_styles',
			array(
				'label'     => esc_html__( 'Author Avatar', 'jet-blog' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition'   => array(
					'show_meta' => 'yes',
					'show_author_avatar' => 'yes',
					'show_author' => 'yes',
				),
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'meta_avatar_border',
				'label'          => esc_html__( 'Border', 'jet-blog' ),
				'placeholder'    => '1px',
				'condition'   => array(
					'show_meta' => 'yes',
					'show_author_avatar' => 'yes',
					'show_author' => 'yes',
				),
				'selector'       => '{{WRAPPER}} ' . $css_scheme['meta_avatar'] . ' img',
			),
			75
		);


		$this->_add_responsive_control(
			'meta_avatar_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'condition'   => array(
					'show_meta' => 'yes',
					'show_author_avatar' => 'yes',
					'show_author' => 'yes',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['meta_avatar'] . ' img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_end_controls_section();

		$this->_start_controls_section(
			'section_terms_link_style',
			array(
				'label'      => esc_html__( 'Terms Links', 'jet-blog' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition'  => array(
					'show_terms' => 'yes',
				),
			)
		);

		$this->_start_controls_tabs( 'tabs_terms_link_style' );

		$this->_start_controls_tab(
			'tab_terms_link_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-blog' ),
			)
		);

		$this->_add_control(
			'terms_link_bg_color',
			array(
				'label'     => _x( 'Color', 'Background Control', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'global' => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'title'     => _x( 'Background Color', 'Background Control', 'jet-blog' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['terms_link'] => 'background-color: {{VALUE}};',
				),
			),
			25
		);

		$this->_add_control(
			'terms_link_color',
			array(
				'label' => esc_html__( 'Text Color', 'jet-blog' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['terms_link'] => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'terms_link_typography',
				'global' => array(
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				),
				'selector' => '{{WRAPPER}}  ' . $css_scheme['terms_link'],
			),
			50
		);

		$this->_add_control(
			'terms_link_text_decor',
			array(
				'label'   => esc_html__( 'Text Decoration', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'none'      => esc_html__( 'None', 'jet-blog' ),
					'underline' => esc_html__( 'Underline', 'jet-blog' ),
				),
				'default' => 'none',
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['terms_link'] . '' => 'text-decoration: {{VALUE}}',
				),
			),
			100
		);

		$this->_add_responsive_control(
			'terms_link_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['terms_link'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'terms_link_border',
				'label'       => esc_html__( 'Border', 'jet-blog' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['terms_link'],
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'terms_link_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['terms_link'],
			),
			100
		);

		$this->_end_controls_tab();

		$this->_start_controls_tab(
			'tab_terms_link_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-blog' ),
			)
		);

		$this->_add_control(
			'terms_link_hover_bg_color',
			array(
				'label'     => _x( 'Color', 'Background Control', 'jet-blog' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'global' => array(
					'default' => Global_Colors::COLOR_PRIMARY,
				),
				'title'     => _x( 'Background Color', 'Background Control', 'jet-blog' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['terms_link'] . ':hover' => 'background-color: {{VALUE}};',
				),
			),
			25
		);

		$this->_add_control(
			'terms_link_hover_color',
			array(
				'label' => esc_html__( 'Text Color', 'jet-blog' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['terms_link'] . ':hover' => 'color: {{VALUE}}',
				),
			),
			25
		);

		$this->_add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name' => 'terms_link_hover_typography',
				'label' => esc_html__( 'Typography', 'jet-blog' ),
				'selector' => '{{WRAPPER}}  ' . $css_scheme['terms_link'] . ':hover',
			),
			50
		);

		$this->_add_control(
			'terms_link_hover_text_decor',
			array(
				'label'   => esc_html__( 'Text Decoration', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'none'      => esc_html__( 'None', 'jet-blog' ),
					'underline' => esc_html__( 'Underline', 'jet-blog' ),
				),
				'default' => 'none',
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['terms_link'] . ':hover' => 'text-decoration: {{VALUE}}',
				),
			),
			100
		);

		$this->_add_responsive_control(
			'terms_link_hover_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['terms_link'] . ':hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'terms_link_hover_border',
				'label'       => esc_html__( 'Border', 'jet-blog' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['terms_link'] . ':hover',
			),
			75
		);

		$this->_add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'terms_link_hover_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['terms_link'] . ':hover',
			),
			100
		);

		$this->_end_controls_tab();

		$this->_end_controls_tabs();

		$this->_add_responsive_control(
			'terms_link_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['terms_link'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator' => 'before',
			),
			25
		);

		$this->_add_responsive_control(
			'terms_link_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-blog' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['terms_link'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			),
			50
		);

		$this->_add_control(
			'terms_link_alignment_h',
			array(
				'label'   => esc_html__( 'Horizontal Alignment', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => array(
					'left'   => esc_html__( 'Left', 'jet-blog' ),
					'center' => esc_html__( 'Center', 'jet-blog' ),
					'right'  => esc_html__( 'Right', 'jet-blog' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['terms'] => 'text-align: {{VALUE}};',
				),
				'separator' => 'before',
				'classes'   => 'jet-elements-text-align-control',
			),
			25
		);

		$this->_add_control(
			'terms_link_alignment_v',
			array(
				'label'   => esc_html__( 'Vertical Alignment', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'space-between',
				'options' => array(
					'space-between' => esc_html__( 'Top', 'jet-blog' ),
					'flex-end'   => esc_html__( 'Bottom', 'jet-blog' ),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['box'] => 'align-content: {{VALUE}};',
				),
			),
			25
		);

		$this->_end_controls_section();

		$this->_start_controls_section(
			'section_pagination_arrows',
			array(
				'label'      => esc_html__( 'Paging Arrows', 'jet-blog' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_start_controls_tabs( 'tabs_arrows_style', 50 );

		$this->_start_controls_tab(
			'tab_prev',
			array(
				'label' => esc_html__( 'Normal', 'jet-blog' ),
			),
			50
		);

		$this->_add_group_control(
			\Jet_Blog_Group_Control_Box_Style::get_type(),
			array(
				'name'           => 'arrows_style',
				'label'          => esc_html__( 'Arrows Style', 'jet-blog' ),
				'selector'       => '{{WRAPPER}} .jet-blog-arrow',
				'fields_options' => array(
					'color' => array(
						'global' => array(
							'default' => Global_Colors::COLOR_PRIMARY,
						),
					),
				),
			),
			50
		);

		$this->_end_controls_tab( 50 );

		$this->_start_controls_tab(
			'tab_next_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-blog' ),
			),
			50
		);

		$this->_add_group_control(
			\Jet_Blog_Group_Control_Box_Style::get_type(),
			array(
				'name'           => 'arrows_style_hover',
				'label'          => esc_html__( 'Arrows Style', 'jet-blog' ),
				'selector'       => '{{WRAPPER}} .jet-blog-arrow:hover',
				'fields_options' => array(
					'color' => array(
						'global' => array(
							'default' => Global_Colors::COLOR_PRIMARY,
						),
					),
				),
			),
			50
		);

		$this->_end_controls_tab( 50 );

		$this->_end_controls_tabs( 50 );

		$this->_add_control(
			'prev_arrow_position',
			array(
				'label'     => esc_html__( 'Prev Arrow Position', 'jet-blog' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			25
		);

		$this->_add_control(
			'prev_vert_position',
			array(
				'label'   => esc_html__( 'Vertical Postition by', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top',
				'options' => array(
					'top'    => esc_html__( 'Top', 'jet-blog' ),
					'bottom' => esc_html__( 'Bottom', 'jet-blog' ),
				),
			),
			25
		);

		$this->_add_responsive_control(
			'prev_top_position',
			array(
				'label'      => esc_html__( 'Top Indent', 'jet-blog' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'prev_vert_position' => 'top',
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-blog-arrow.jet-arrow-prev' => 'top: {{SIZE}}{{UNIT}}; bottom: auto;',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'prev_bottom_position',
			array(
				'label'      => esc_html__( 'Bottom Indent', 'jet-blog' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'prev_vert_position' => 'bottom',
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-blog-arrow.jet-arrow-prev' => 'bottom: {{SIZE}}{{UNIT}}; top: auto;',
				),
			),
			25
		);

		$this->_add_control(
			'prev_hor_position',
			array(
				'label'   => esc_html__( 'Horizontal Postition by', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => array(
					'left'  => esc_html__( 'Left', 'jet-blog' ),
					'right' => esc_html__( 'Right', 'jet-blog' ),
				),
			),
			25
		);

		$this->_add_responsive_control(
			'prev_left_position',
			array(
				'label'      => esc_html__( 'Left Indent', 'jet-blog' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'prev_hor_position' => 'left',
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-blog-arrow.jet-arrow-prev' => 'left: {{SIZE}}{{UNIT}}; right: auto;',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'prev_right_position',
			array(
				'label'      => esc_html__( 'Right Indent', 'jet-blog' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'prev_hor_position' => 'right',
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-blog-arrow.jet-arrow-prev' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
				),
			),
			25
		);

		$this->_add_control(
			'next_arrow_position',
			array(
				'label'     => esc_html__( 'Next Arrow Position', 'jet-blog' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			),
			25
		);

		$this->_add_control(
			'next_vert_position',
			array(
				'label'   => esc_html__( 'Vertical Postition by', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top',
				'options' => array(
					'top'    => esc_html__( 'Top', 'jet-blog' ),
					'bottom' => esc_html__( 'Bottom', 'jet-blog' ),
				),
			),
			25
		);

		$this->_add_responsive_control(
			'next_top_position',
			array(
				'label'      => esc_html__( 'Top Indent', 'jet-blog' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'next_vert_position' => 'top',
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-blog-arrow.jet-arrow-next' => 'top: {{SIZE}}{{UNIT}}; bottom: auto;',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'next_bottom_position',
			array(
				'label'      => esc_html__( 'Bottom Indent', 'jet-blog' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'next_vert_position' => 'bottom',
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-blog-arrow.jet-arrow-next' => 'bottom: {{SIZE}}{{UNIT}}; top: auto;',
				),
			),
			25
		);

		$this->_add_control(
			'next_hor_position',
			array(
				'label'   => esc_html__( 'Horizontal Postition by', 'jet-blog' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => array(
					'left'  => esc_html__( 'Left', 'jet-blog' ),
					'right' => esc_html__( 'Right', 'jet-blog' ),
				),
			),
			25
		);

		$this->_add_responsive_control(
			'next_left_position',
			array(
				'label'      => esc_html__( 'Left Indent', 'jet-blog' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'next_hor_position' => 'left',
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-blog-arrow.jet-arrow-next' => 'left: {{SIZE}}{{UNIT}}; right: auto;',
				),
			),
			25
		);

		$this->_add_responsive_control(
			'next_right_position',
			array(
				'label'      => esc_html__( 'Right Indent', 'jet-blog' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => -400,
						'max' => 400,
					),
					'%' => array(
						'min' => -100,
						'max' => 100,
					),
					'em' => array(
						'min' => -50,
						'max' => 50,
					),
				),
				'condition' => array(
					'next_hor_position' => 'right',
				),
				'selectors'  => array(
					'{{WRAPPER}} .jet-blog-arrow.jet-arrow-next' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
				),
			),
			25
		);

		$this->_end_controls_section();

		$this->_start_controls_section(
			'section_custom_fields_styles',
			array(
				'label'      => esc_html__( 'Custom Fields', 'jet-blog' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->_add_meta_style_controls(
			'title_related',
			esc_html__( 'Before/After Title', 'jet-blog' ),
			'jet-title-fields'
		);

		$this->_add_meta_style_controls(
			'content_related',
			esc_html__( 'Before/After Content', 'jet-blog' ),
			'jet-content-fields'
		);

		$this->_end_controls_section();

	}

	/**
	 * Returns information about available layouts
	 *
	 * @return array
	 */
	public function _layout_data() {

		return apply_filters( 'jet-blog/samrt-tiles/available_layouts', array(
			'2-1-2'   => array(
				'label'    => esc_html__( 'Layout 1 (5 posts)', 'jet-blog' ),
				'icon'     => 'jet-blog-layout-1',
				'num'      => 5,
				'has_rows' => false,
			),
			'1-1-2-h' => array(
				'label'    => esc_html__( 'Layout 2 (4 posts)', 'jet-blog' ),
				'icon'     => 'jet-blog-layout-2',
				'num'      => 4,
				'has_rows' => false,
			),
			'1-1-2-v' => array(
				'label'    => esc_html__( 'Layout 3 (4 posts)', 'jet-blog' ),
				'icon'     => 'jet-blog-layout-3',
				'num'      => 4,
				'has_rows' => false,
			),
			'1-2'     => array(
				'label'    => esc_html__( 'Layout 4 (3 posts)', 'jet-blog' ),
				'icon'     => 'jet-blog-layout-4',
				'num'      => 3,
				'has_rows' => false,
			),
			'2-3-v'   => array(
				'label'    => esc_html__( 'Layout 5 (5 posts)', 'jet-blog' ),
				'icon'     => 'jet-blog-layout-5',
				'num'      => 5,
				'has_rows' => false,
			),
			'1-2-2'   => array(
				'label'    => esc_html__( 'Layout 6 (5 posts)', 'jet-blog' ),
				'icon'     => 'jet-blog-layout-6',
				'num'      => 5,
				'has_rows' => false,
			),
			'2-x'   => array(
				'label'    => esc_html__( 'Layout 7 (2, 4, 6 posts)', 'jet-blog' ),
				'icon'     => 'jet-blog-layout-7',
				'num'      => 2,
				'has_rows' => false,
			),
			'3-x'   => array(
				'label'    => esc_html__( 'Layout 8 (3, 6, 9 posts)', 'jet-blog' ),
				'icon'     => 'jet-blog-layout-8',
				'num'      => 3,
				'has_rows' => false,
			),
			'4-x'   => array(
				'label'    => esc_html__( 'Layout 9 (4, 8, 12 posts)', 'jet-blog' ),
				'icon'     => 'jet-blog-layout-9',
				'num'      => 4,
				'has_rows' => false,
			),

		) );

	}

	/**
	 * Get style attribute with post background.
	 *
	 * @return void|null
	 */
	public function _get_post_bg_attr() {

		$settings = $this->get_settings();

		if ( has_post_thumbnail() ) {
			$thumb_size = isset( $settings['image_size'] ) ? $settings['image_size'] : 'full';
			$thumb      = get_the_post_thumbnail_url( null, $thumb_size );
		} else {
			$thumb = sprintf( '//via.placeholder.com/900x600?text=%s', str_replace( ' ', '+', get_the_title() ) );
		}

		printf( 'style="background-image:url(\'%s\')"', $thumb );

	}

	/**
	 * Slider attributes.
	 *
	 * @return void
	 */
	public function _slider_atts() {

		$slider_attributes                  = array( 'adaptiveHeight' => true );
		$settings                           = $this->get_settings();
		$slider_attributes['arrows']        = filter_var( $settings['show_arrows'], FILTER_VALIDATE_BOOLEAN );
		$slider_attributes['prevArrow']     = jet_blog_tools()->get_carousel_arrow( $settings['arrow_type'], 'prev' );
		$slider_attributes['nextArrow']     = jet_blog_tools()->get_carousel_arrow( $settings['arrow_type'], 'next' );
		$slider_attributes['autoplay']      = isset( $settings['autoplay'] ) ? filter_var( $settings['autoplay'], FILTER_VALIDATE_BOOLEAN ) : false;
		$slider_attributes['autoplaySpeed'] = ! empty( $settings['autoplay_speed'] ) ? absint( $settings['autoplay_speed'] ) : 5000;
		$slider_attributes['rtl']           = is_rtl();

		$slider_attributes = apply_filters( 'jet-blog/smart-tiles/slider-settings', $slider_attributes );

		printf( "data-slider-atts='%s'", json_encode( $slider_attributes ) );

	}

	public function _get_posts_num( $settings ) {

		if ( 0 === $this->_current_posts_num ) {

			$layout         = $settings['layout'];
			$layouts_data   = $this->_layout_data();
			$current_layout = isset( $layouts_data[ $layout ] ) ? $layouts_data[ $layout ] : false;

			if ( ! $current_layout ) {
				return $this->_current_posts_num;
			}

			$this->_current_posts_num = $current_layout['num'];

			if ( $this->is_multirow_layout( $layout ) ) {
				$rows = isset( $settings['rows_num'] ) ? absint( $settings['rows_num'] ) : 1;
				$this->_current_posts_num = $this->_current_posts_num * $rows;
			}

		}

		return $this->_current_posts_num;

	}

	/**
	 * Check if current layout is multirow layout
	 *
	 * @param  string  $layout Layout name.
	 * @return boolean
	 */
	public function is_multirow_layout( $layout ) {
		$multirow_layouts = apply_filters( 'jet-blog/smart-tiles/multirow-layouts', array( '2-x', '3-x', '4-x' ) );
		return in_array( $layout, $multirow_layouts );
	}

	public function _maybe_open_slide_wrapper( $settings ) {

		$num = $this->_get_posts_num( $settings );

		if ( ! $num ) {
			return;
		}

		$classes   = array( 'jet-smart-tiles-slide__wrap' );
		$classes[] = 'layout-' . esc_attr( $settings['layout'] );

		if ( $this->is_multirow_layout( $settings['layout'] ) ) {
			$rows      = isset( $settings['rows_num'] ) ? absint( $settings['rows_num'] ) : 1;
			$classes[] = 'rows-' . $rows;
		}

		if ( 'yes' === $settings['use_scroll_slider_mobile'] ) {
			$rows      = isset( $settings['mobile_rows_num'] ) ? absint( $settings['mobile_rows_num'] ) : 1;
			$classes[] = 'scroll-slider-mobile';
			$classes[] = 'mobile-rows-' . $rows;
		}

		$style = '';
		if ( isset ( $settings['mobile_col_width']['size'] ) && $settings['use_scroll_slider_mobile'] === 'yes' ) {
			$col_width = $settings['mobile_col_width']['size'];
			$style = 'style="--jet-blog-tiles-col-width:' . esc_attr( $col_width ) . 'px;"';
		}

		if ( 0 === ( $this->_current_post_index % $num ) ) {
			printf( '<div class="jet-smart-tiles-slide"><div class="%1$s" %2$s>', implode( ' ', $classes ), $style );
		}

	}

	public function _maybe_close_slide_wrapper( $settings ) {

		$num = $this->_get_posts_num( $settings );

		if ( ! $num ) {
			return;
		}

		if ( 0 === ( ( $this->_current_post_index + 1 ) % $num ) ) {
			echo '</div></div>';
		} elseif ( $this->_current_post_index + 1 === count( $this->_query ) ) {
			echo '</div></div>';
		}

		$this->_current_post_index++;

	}

	public function _reset_data() {
		wp_reset_postdata();
		$this->_current_post_index = 0;
	}

	public function get_default_query_args( $settings = array() ) {

		$num       = $this->_get_posts_num( $settings );
		$post_types = ! empty( $settings['post_type'] ) ? ( array ) $settings['post_type'] : array( 'post' );
		$exclude   = ! empty( $settings['exclude_ids'] ) ? $settings['exclude_ids'] : '';
		$include   = ! empty( $settings['include_ids'] ) ? $settings['include_ids'] : '';
		$offset    = ! empty( $settings['posts_offset'] ) ? absint( $settings['posts_offset'] ) : 0;

		if ( 'yes' === $settings['carousel_enabled'] ) {
			$slides = ( 0 !== absint( $settings['slides_num'] ) ) ? absint( $settings['slides_num'] ) : 1;
			$num = $slides * $num;
		}

		if ( ! $num ) {
			return;
		}

		$query_args = array(
			'posts_per_page'      => $num,
			'ignore_sticky_posts' => true,
			'post_status'         => 'publish',
			'paged'               => 1,
			'post_type'           => $post_types,
		);

		$tax = $settings['query_by'];
		$include_ids = array();
		$post_ids = array();
		$tax_query = array( 'relation' => 'OR' );

		if ( ! empty( $settings['custom_query_by'] ) ) {

			switch ( $settings['custom_query_by'] ) {
				case 'ids':

					if ( ! empty( $settings['post_ids'] ) ) {
						$post_ids = array_merge( $post_ids, explode( ',', str_replace( ' ', '', $settings['post_ids'] ) ) );
					}

					break;

				case 'terms':

					$custom_terms_ids = ! empty( $settings['custom_terms_ids'] ) ? explode( ',', str_replace( ' ', '', $settings['custom_terms_ids'] ) ) : array();
					$custom_terms     = array();

					foreach ( $custom_terms_ids as $term_id ) {
						$term_data = get_term_by( 'term_taxonomy_id', $term_id );
						if ( false !== $term_data ) {
							$custom_terms[ $term_data->taxonomy ][] = $term_id;
						}
					}
					foreach ( $custom_terms as $taxonomy => $term_ids ) {
						$tax_query[] = array(
							'taxonomy' => $taxonomy,
							'field'    => 'term_id',
							'terms'    => $term_ids,
						);
					}

					break;

			}
		}

		if ( in_array( 'post', $post_types, true ) ) {

			switch ( $tax ) {
				case 'ids':

					if ( ! empty( $include ) ) {
						$include_ids = array_merge( $include_ids, explode( ',', str_replace( ' ', '', $include ) ) );
					}

					break;

				case 'category':
				case 'post_tag':

					$term_ids = ! empty( $settings[ $tax . '_ids' ] ) ? ( is_array( $settings[ $tax . '_ids' ] ) ? array_filter( $settings[ $tax . '_ids' ] ) : array( $settings[ $tax . '_ids' ] ) ) : array();

				if ( ! empty( $term_ids ) ) {
						$tax_query[] = array(
							'taxonomy' => $tax,
							'field'    => 'term_id',
							'terms'    => $term_ids,
						);
					}

					break;
			}
		}

		$post_in = array_merge( $include_ids, $post_ids );

		if ( ! empty( $post_in ) ) {
			$query_args['post__in'] = $post_in;
		}

		if ( ! empty( $exclude ) && empty( $query_args['post__in'] ) ) {
			$exclude                    = $this->render_macros( $exclude );
			$exclude_ids                = explode( ',', str_replace( ' ', '', $exclude ) );
			$query_args['post__not_in'] = $exclude_ids;
		}

		if ( $offset ) {
			$query_args['offset'] = $offset;
		}

		if ( ! empty( $settings['order'] ) ) {
			$query_args['order'] = $settings['order'];
		}

		if ( ! empty( $settings['order_by'] ) ) {
			$query_args['orderby'] = $settings['order_by'];
		}

		if ( isset( $settings['meta_query'] ) && 'yes' === $settings['meta_query'] ) {

			$meta_key   = ! empty( $settings['meta_key'] ) ? esc_attr( $settings['meta_key'] ) : false;
			$meta_value = ! empty( $settings['meta_value'] ) ? esc_attr( $settings['meta_value'] ) : '';

			if ( ! empty( $meta_key ) ) {
				$query_args['meta_key']   = $meta_key;
				$query_args['meta_value'] = $meta_value;
			}

		}

		if ( ! empty( $tax_query ) && count( $tax_query ) > 1 ) {
			$query_args['tax_query'] = $tax_query;
		}

		return $query_args;

	}

	/**
	 * Get custom query args
	 *
	 * @return array
	 */
	public function get_custom_query_args( $settings = array() ) {

		$query_args = $settings['custom_query'];
		$query_args = json_decode( $query_args, true );

		if ( ! $query_args ) {
			$query_args = array();
		}

		return $query_args;

	}

	/**
	 * Get posts.
	 *
	 * @return void
	 */
	public function _get_posts() {

		$settings = $this->get_settings_for_display();
		$posts = array();

		if ( isset( $settings['is_archive_template'] ) && 'yes' === $settings['is_archive_template'] ) {

			if ( $this->_is_template_preview() ){
				$this->_set_query( get_posts( array(
					'post_type'   => 'post',
					'numberposts' => get_option( 'posts_per_page', 10 ),
				) ) );
			} else {
				global $wp_query;
				$this->_set_query( $wp_query->posts );
			}

			return;

		}

		if ( isset( $settings['use_custom_query'] ) && 'true' === $settings['use_custom_query'] ) {
			$query_args = $this->get_custom_query_args( $settings );
		} else {
			$query_args = $this->get_default_query_args( $settings );
		}

		/**
		 * Filter query arguments before posts requested
		 *
		 * @var array
		 */
		$query_args = apply_filters( 'jet-blog/smart-tiles/query-args', $query_args, $this );

		$posts = apply_filters( 'jet-blog/pre-query', false, $settings, $query_args, $this );

		if ( false === $posts ) {
			$query = new \WP_Query( $query_args );
			$posts = $query->posts;
		}

		if ( ! empty( $posts ) ) {

			$hide_unfilled_slide_enabled = ! empty( $settings['hide_unfilled_slide'] ) ? $settings['hide_unfilled_slide'] : false;
			$hide_unfilled_slide_enabled = filter_var( $hide_unfilled_slide_enabled, FILTER_VALIDATE_BOOLEAN );

			if ( 'yes' === $settings['carousel_enabled'] && $hide_unfilled_slide_enabled ) {
				$posts = $this->_remove_unfilled_slide( $posts, $this->_get_posts_num( $settings ) );
			}

		}

		$this->_set_query( $posts );

	}

	/**
	 * Removes unfilled slide posts.
	 *
	 * @param array $posts
	 * @param number $posts_per_slide number of posts per slide
	 * @return array posts
	 */
	public function _remove_unfilled_slide( $posts, $posts_per_slide ) {

		while ( count( $posts ) % $posts_per_slide !== 0 ) {
			array_pop( $posts );
		}

		return $posts;

	}

	/**
	 * Show post categories depends on settings
	 *
	 * @return void|null
	 */
	public function _post_terms() {

		$settings = $this->get_settings();
		$show     = isset( $settings['show_terms'] ) ? $settings['show_terms'] : '';
		$tax      = isset( $settings['show_terms_tax'] ) ? $settings['show_terms_tax'] : '';
		$num      = isset( $settings['show_terms_num'] ) ? $settings['show_terms_num'] : '';

		if ( 'yes' !== $show ) {
			return;
		}

		$terms = wp_get_post_terms( get_the_ID(), esc_attr( $tax ) );

		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return;
		}

		if ( 'all' !== $num ) {
			$num   = absint( $num );
			$terms = array_slice( $terms, 0, $num );
		}

		$format = apply_filters(
			'jet-blog/smart-tiles/post-term-format',
			'<a href="%2$s" class="jet-smart-tiles__terms-link jet-smart-tiles__terms-link--%3$s">%1$s</a>'
		);

		$result = '';

		foreach ( $terms as $term ) {
			$result .= sprintf( $format, $term->name, get_term_link( (int) $term->term_id, $tax ), $term->term_id );
		}

		printf( '<div class="jet-smart-tiles__terms">%s</div>', $result );

	}

	/**
	 * Retrieves meta settings ad required data.
	 *
	 * @return array
	 */
	public function _get_meta() {

		$settings = $this->get_settings();

		$show = array(
			'author'   => 'show_author',
			'date'     => 'show_date',
			'comments' => 'show_comments',
		);

		$html = array(
			'author' => '<span class="posted-by post-meta__item jet-smart-tiles__meta-item">%1$s<span %3$s %4$s>%5$s%6$s</span></span>',
			'date' => '<span class="post__date post-meta__item jet-smart-tiles__meta-item">%1$s<span %3$s %4$s ><time datetime="%5$s" title="%5$s">%6$s%7$s</time></span></span>',
			'comments' => '<span class="post__comments post-meta__item jet-smart-tiles__meta-item">%1$s<span %3$s %4$s>%5$s%6$s</span></span>',
		);

		$icon_format = '<span class="jet-smart-tiles__meta-icon jet-blog-icon">%s</span>';
		$result      = array();

		foreach ( $show as $key => $setting ) {

			$prefix = $this->_get_icon( $setting . '_icon', $settings, $icon_format );
			$current_html = $html[ $key ];

			if ( 'author' === $key && isset( $settings['show_author_avatar'] ) && 'yes' === $settings['show_author_avatar'] ) {
				$avatar_size_value = $settings['avatar_size']['size'] ?? 50;
				$author_id = get_the_author_meta('ID');
				$avatar_source = $settings['get_avatar_from'];
				$source_meta_field = $settings['avatar_custom_field'] ?? false;
				$avatar = jet_blog_tools()->render_avatar( $author_id, $avatar_size_value, $avatar_source, $source_meta_field );

				if ( $avatar ) {
					$current_html = '<div class="has-author-avatar">' . $avatar . str_replace( '%1$s', '', $current_html ) . '</div>';
				}
			}

			$current = array(
				'visible' => $settings[ $setting ],
				'prefix'  => $prefix,
				'html'    => $current_html,
			);

			$result[ $key ] = $current;

		}

		return $result;

	}

	/**
	 * Show post excerpt.
	 * @return [type] [description]
	 */
	public function _post_excerpt( $before = '', $after = '' ) {

		$excerpt  = has_excerpt( get_the_ID() ) ? apply_filters( 'the_excerpt', get_the_excerpt() ) : '';
		$settings = $this->get_settings();
		$length   = $settings['excerpt_length'];
		$trimmed  = $settings['excerpt_trimmed_ending'];

		if ( ! $length ) {
			return;
		}

		if ( ! $excerpt ) {

			$content = get_the_content();
			$excerpt = strip_shortcodes( $content );
			$excerpt = str_replace( ']]>', ']]&gt;', $excerpt );

			if ( -1 === $length ) {
				$excerpt = wp_trim_words( $excerpt, 55, '' );
			}

		}

		if ( -1 !== $length ) {
			$excerpt = wp_trim_words( $excerpt, $length, $trimmed );
		}

		printf( '%2$s%1$s%3$s', $excerpt, wp_kses_post( $before ), wp_kses_post( $after ) );
	}

	/**
	 * Print tiles wrapper CSS classes string
	 *
	 * @return void
	 */
	public function _tiles_wrap_classes() {

		$settings = $this->get_settings();
		$classes  = array( 'jet-smart-tiles-wrap' );

		if ( 'yes' === $settings['excerpt_on_hover'] ) {
			$classes[] = 'jet-hide-excerpt';
		}

		if ( 'yes' === $settings['carousel_enabled'] ) {
			$classes[] = 'jet-smart-tiles-carousel';
		}

		if ( 'yes' === $settings['carousel_enabled'] && 'yes' === $settings['show_arrows_on_hover'] ) {
			$classes[] = 'jet-arrows-on-hover';
		}

		echo implode( ' ', $classes );
	}

	public function _trim_title( $title ) {

		$settings = $this->get_settings();

		if ( ! isset( $settings['title_length'] ) ) {
			return $title;
		}

		$length = absint( $settings['title_length'] );

		if ( 0 === $length ) {
			return $title;
		}

		$title_arr = explode( ' ', $title );

		if ( count( $title_arr ) <= $length ) {
			return $title;
		}

		$new_title = array_slice( $title_arr, 0, $length );

		return implode( ' ', $new_title ) . '...';
	}

	protected function render() {

		$this->_context = 'render';

		$this->_get_posts();

		$this->_open_wrap();
		add_filter( 'the_title', array( $this, '_trim_title' ), 999 );
		include $this->_get_global_template( 'index' );
		remove_filter( 'the_title', array( $this, '_trim_title' ), 999 );
		$this->_close_wrap();
	}

}
