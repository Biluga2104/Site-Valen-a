<?php
/**
 * Select filter class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Select_Filter' ) ) {
	/**
	 * Define Jet_Smart_Filters_Select_Filter class
	 */
	class Jet_Smart_Filters_Select_Filter extends Jet_Smart_Filters_Filter_Base {
		/**
		 * Constructor for the class
		 */
		function __construct() {

			add_filter( 'jet-smart-filters/render_filter_template/base_class', array( $this, 'modify_base_class' ), 10, 2 );
		}

		/**
		 * Get provider name
		 */
		public function get_name() {

			return __( 'Select', 'jet-smart-filters' );
		}

		/**
		 * Get provider ID
		 */
		public function get_id() {

			return 'select';
		}

		/**
		 * Get icon URL
		 */
		public function get_icon_url() {

			return jet_smart_filters()->plugin_url( 'admin/assets/img/filter-types/select.png' );
		}

		/**
		 * Get provider wrapper selector
		 */
		public function get_scripts() {

			return false;
		}

		/**
		 * Return arguments
		 */
		public function get_args() {

			return $this->args;
		}

		/**
		 * Prepare filter template argumnets
		 */
		public function prepare_args( $args ) {

			$filter_id            = $args['filter_id'];
			$content_provider     = isset( $args['content_provider'] ) ? $args['content_provider'] : false;
			$additional_providers = isset( $args['additional_providers'] ) ? $args['additional_providers'] : false;
			$apply_type           = isset( $args['apply_type'] ) ? $args['apply_type'] : false;
			$apply_on             = isset( $args['apply_on'] ) ? $args['apply_on'] : false;

			if ( ! $filter_id ) {
				return false;
			}

			$source                  = get_post_meta( $filter_id, '_data_source', true );
			$use_exclude_include     = get_post_meta( $filter_id, '_use_exclude_include', true );
			$exclude_include_options = get_post_meta( $filter_id, '_data_exclude_include', true );
			$is_hierarchical         = filter_var( get_post_meta( $filter_id, '_is_hierarchical', true ), FILTER_VALIDATE_BOOLEAN );
			$options                 = array();
			$query_type              = false;
			$query_var               = '';
			$current_value           = false;
			$predefined_value        = $this->get_predefined_value( $filter_id );

			if ( $is_hierarchical ) {
				$source = false;
			}

			switch ( $source ) {
				case 'taxonomies':
					$tax              = get_post_meta( $filter_id, '_source_taxonomy', true );
					$query_type       = 'tax_query';
					$query_var        = $tax;
					$only_child       = filter_var( get_post_meta( $filter_id, '_only_child', true ), FILTER_VALIDATE_BOOLEAN );
					$show_empty_terms = filter_var( get_post_meta( $filter_id, '_show_empty_terms', true ), FILTER_VALIDATE_BOOLEAN );
					$terms_orderby    = get_post_meta( $filter_id, '_terms_orderby', true );
					$terms_order      = get_post_meta( $filter_id, '_terms_order', true );
					$terms_meta_key   = '';
					$custom_query_var = $this->get_custom_query_var( $filter_id );
					$is_terms_slugs   = jet_smart_filters()->settings->url_taxonomy_term_name === 'slug' && ! $custom_query_var;

					if ( in_array( $terms_orderby, array( 'meta_value', 'meta_value_num' ) ) ) {
						$terms_meta_key = get_post_meta( $filter_id, '_terms_orderby_meta_value', '' );
					}

					$options = jet_smart_filters()->data->get_terms_for_options( $tax, $only_child, $is_terms_slugs, array(
						'hide_empty' => ! $show_empty_terms,
						'orderby'    => ! empty( $terms_orderby ) ? $terms_orderby : 'name',
						'order'      => ! empty( $terms_order ) ? $terms_order : 'ASC',
						'meta_key'   => $terms_meta_key
					) );

					if ( is_category() || is_tag() || is_tax( $tax ) ) {
						$current_value = get_queried_object_id();
					}

					if ( $custom_query_var ) {
						$query_type = 'meta_query';
						$query_var  = $custom_query_var;
					}

					break;

				case 'posts':
					$post_type  = get_post_meta( $filter_id, '_source_post_type', true );
					$posts_args = apply_filters( 'jet-smart-filters/filters/posts-source/args', array(
						'post_type' => $post_type,
						'post_status' => 'publish',
						'posts_per_page' => -1
					) );

					$posts      = get_posts( $posts_args );
					$query_type = 'meta_query';
					$query_var  = get_post_meta( $filter_id, '_query_var', true );

					if ( ! empty( $posts ) ) {
						$options = wp_list_pluck( $posts, 'post_title', 'ID' );
					}

					break;

				case 'custom_fields':
					$custom_field   = get_post_meta( $filter_id, '_source_custom_field', true );
					$get_from_field = get_post_meta( $filter_id, '_source_get_from_field_data', true );
					$get_from_field = filter_var( $get_from_field, FILTER_VALIDATE_BOOLEAN );

					if ( $get_from_field ) {
						$options = jet_smart_filters()->data->get_choices_from_field_data( array(
							'field_key' => $custom_field,
							'source'    => get_post_meta( $filter_id, '_custom_field_source_plugin', true ),
						) );
					} else {
						$options = get_post_meta( get_the_ID(), $custom_field, true );

						if ( ! is_array( $options ) ) {
							$options = jet_smart_filters()->data->get_options_by_field_key( $custom_field );
						} else {
							$options = jet_smart_filters()->data->maybe_parse_repeater_options( $options );
						}
					}

					$query_type = 'meta_query';
					$query_var  = get_post_meta( $filter_id, '_query_var', true );

					break;
				
				case 'cct':
					$query_type = 'meta_query';
					$query_var  = get_post_meta( $filter_id, '_query_var', true );
					$options    = jet_smart_filters()->data->get_choices_from_cct_data( $query_var );
	
						break;

				default:
					$options    = get_post_meta( $filter_id, '_source_manual_input', true );
					$options    = ! empty( $options ) ? $options : array();
					$query_type = 'meta_query';
					$query_var  = get_post_meta( $filter_id, '_query_var', true );

					if ( ! empty( $options ) ) {
						$options = wp_list_pluck( $options, 'label', 'value' );
					}

					break;
			}

			if ( $is_hierarchical ) {
				$query_type = 'tax_query';
			}

			$options = jet_smart_filters()->data->maybe_include_exclude_options( $use_exclude_include, $exclude_include_options, $options );

			$placeholder = get_post_meta( $filter_id, '_placeholder', true );
			if ( ! $placeholder ) {
				$placeholder = __( 'Select...', 'jet-smart-filters' );
			}

			if ( !empty( $options ) ) {
				$options = array( '' => $placeholder ) + $options;
			}

			$options = apply_filters( 'jet-smart-filters/filters/filter-options', $options, $filter_id, $this );

			$result = array(
				'options'              => $options,
				'query_type'           => $query_type,
				'query_var'            => $query_var,
				'query_var_suffix'     => jet_smart_filters()->filter_types->get_filter_query_var_suffix( $filter_id ),
				'content_provider'     => $content_provider,
				'additional_providers' => $additional_providers,
				'apply_type'           => $apply_type,
				'apply_on'             => $apply_on,
				'filter_id'            => $filter_id,
				'is_hierarchical'      => $is_hierarchical,
				'query_id'             => ! empty( $args['query_id'] ) ? $args['query_id'] : false,
				'accessibility_label'  => $this->get_accessibility_label( $filter_id )
			);

			if ( $current_value ) {
				$result['current_value'] = $current_value;
			}

			if ( $predefined_value !== false ) {
				$result['predefined_value'] = $predefined_value;
			}

			return $result;
		}

		public function modify_base_class( $base_class, $filter_id ) {

			if ( $base_class === 'jet-smart-filters-select' && filter_var( get_post_meta( $filter_id, '_is_hierarchical', true ), FILTER_VALIDATE_BOOLEAN ) ) {
				return 'jet-smart-filters-hierarchy';
			}

			return $base_class;
		}
	}
}
