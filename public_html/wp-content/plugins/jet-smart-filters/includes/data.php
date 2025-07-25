<?php
/**
 * Data class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Smart_Filters_Data' ) ) {

	/**
	 * Define Jet_Smart_Filters_Data class
	 */
	class Jet_Smart_Filters_Data {

		public $url_symbol = array(
			'provider_id'     => ':',
			'items_separator' => ';',
			'key_value'       => ':',
			'value_separator' => ',',
			'var_suffix'      => '!',
		);

		public function __construct() {

			// set custom url symbols
			if ( jet_smart_filters()->settings->use_url_custom_symbols ) {
				if ( jet_smart_filters()->settings->url_provider_id_delimiter ) {
					$this->url_symbol['provider_id'] = jet_smart_filters()->settings->url_provider_id_delimiter;
				}
				if ( jet_smart_filters()->settings->url_items_separator ) {
					$this->url_symbol['items_separator'] = jet_smart_filters()->settings->url_items_separator;
				}
				if ( jet_smart_filters()->settings->url_key_value_delimiter ) {
					$this->url_symbol['key_value'] = jet_smart_filters()->settings->url_key_value_delimiter;
				}
				if ( jet_smart_filters()->settings->url_value_separator ) {
					$this->url_symbol['value_separator'] = jet_smart_filters()->settings->url_value_separator;
				}
				if ( jet_smart_filters()->settings->url_var_suffix_separator ) {
					$this->url_symbol['var_suffix'] = jet_smart_filters()->settings->url_var_suffix_separator;
				}
			}
		}

		/**
		 * Get URL symbol
		 */
		public function get_url_symbol( $name ) {

			$use_tabindex = filter_var( jet_smart_filters()->settings->get( 'use_tabindex', false ), FILTER_VALIDATE_BOOLEAN );

			return $use_tabindex
				? 'tabindex="0"'
				: '';
		}

		/**
		 * Allowed filter types.
		 */
		public function filter_types() {

			$filter_types = jet_smart_filters()->filter_types->get_filter_types();
			$result       = array();

			foreach ( $filter_types as $filter_id => $filter ) {
				if ( ! method_exists( $filter, 'get_name' ) ) {
					continue;
				}

				$result[ $filter_id ] = $filter->get_name();
			}

			return $result;
		}

		/**
		 * Returns post types list for options
		 */
		public function get_post_types_for_options() {

			$args = array(
				'public' => true,
			);

			$post_types = get_post_types( $args, 'objects', 'and' );
			$post_types = wp_list_pluck( $post_types, 'label', 'name' );

			if ( isset( $post_types[ jet_smart_filters()->post_type->slug() ] ) ) {
				unset( $post_types[jet_smart_filters()->post_type->slug()] );
			}

			return $post_types;
		}

		/**
		 * Get taxonomies list for options.
		 */
		public function get_taxonomies_for_options() {

			$taxonomies         = get_taxonomies( array(), 'objects', 'and' );
			$options_taxonomies = wp_list_pluck( $taxonomies, 'label', 'name' );

			return $options_taxonomies;
		}

		/**
		 * Get sitepath.
		 */
		public function get_sitepath() {

			$parsed_home_url = wp_parse_url( home_url() );

			return array_key_exists( 'path', $parsed_home_url ) ? $parsed_home_url['path'] : '';
		}

		/**
		 * Get current url.
		 */
		public function get_current_url() {

			return (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}

		/**
		 * Get baseurl.
		 */
		public function get_baseurl() {

			$baseurl        = preg_replace( '/\bjsf[\/|=].*/', '', $_SERVER['REQUEST_URI'], 1 );
			$parsed_baseurl = wp_parse_url( $baseurl );

			return rtrim( array_key_exists( 'path', $parsed_baseurl ) ? $parsed_baseurl['path'] : $baseurl, '/' ) . '/';
		}

		/**
		 * Return information about compare data by label
		 */
		public function parse_comapre_label( $label ) {

			$result = array(
				'compare' => '=',
			);

			switch ( $label ) {

				case 'less' :
					$result['compare'] = '<=';
					$result['type']    = 'NUMERIC';
					break;

				case 'greater' :
					$result['compare'] = '>=';
					$result['type']    = 'NUMERIC';
					break;

				case 'like' :
					$result['compare'] = 'LIKE';
					break;

				case 'in' :
					$result['compare'] = 'IN';
					break;

				case 'between' :
					$result['compare'] = 'BETWEEN';
					break;

				case 'exists' :
					$result['compare'] = 'EXISTS';
					break;

				case 'regexp' :
					$result['compare'] = 'REGEXP';
					break;

				default:
					$result['compare'] = '=';
					break;

			}

			return $result;
		}

		/**
		 * Returns provider selectors list
		 */
		public function get_provider_selectors() {

			$providers = jet_smart_filters()->providers->get_providers();
			$result    = array();

			foreach ( $providers as $provider_id => $provider ) {
				if ( $provider->get_wrapper_selector() ) {
					$result[ $provider_id ] = array(
						'selector' => $provider->get_wrapper_selector(),
						'action'   => $provider->get_wrapper_action(),
						'inDepth'  => $provider->in_depth(),
						'idPrefix' => $provider->id_prefix(),
					);

					$list = $provider->get_list_selector();
					if ( $list ) {
						$result[ $provider_id ]['list'] = $list;
					}

					$item = $provider->get_item_selector();
					if ( $item ) {
						$result[ $provider_id ]['item'] = $item;
					}
				}
			}

			return $result;
		}

		/**
		 * Find choices for filter from field data
		 */
		public function get_choices_from_field_data( $args = array() ) {

			$result = array();

			$args = wp_parse_args( $args, array(
				'field_key' => false,
				'source'    => 'jet_engine',
			) );

			if ( empty( $args['field_key'] ) ) {
				return $result;
			}

			// trimming accidentally entered spaces in the key field
			$args['field_key'] = trim( $args['field_key'] );

			switch ( $args['source'] ) {
				case 'acf':

					if ( ! function_exists( 'acf_get_field' ) ) {
						return $result;
					}

					$field = acf_get_field( $args['field_key'] );

					if ( $field && is_array( $field ) && ! empty( $field['choices'] ) ) {
						return $field['choices'];
					} else {
						return $result;
					}

				default:

					if ( ! function_exists( 'jet_engine' ) || ! isset( jet_engine()->meta_boxes ) ) {
						return $result;
					}

					$all_fields  = jet_engine()->meta_boxes->get_registered_fields();
					$found_field = null;

					foreach ( $all_fields as $object => $fields ) {
						foreach ( $fields as $field_data ) {
							if ( ! empty( $field_data['name'] ) && $args['field_key'] === $field_data['name'] ) {
								$found_field = $field_data;
							}
						}
					}

					if ( ! empty( $found_field['options'] ) ) {
						foreach ( $found_field['options'] as $option ) {
							$label                  = apply_filters( 'jet-engine/compatibility/translate-string', $option['value'] );
							$result[$option['key']] = $label;
						}
					}

					if ( isset( $found_field['options_source'] ) && $found_field['options_source'] === 'manual_bulk' && ! empty( $found_field['bulk_options'] ) ) {
						$bulk_options = explode( PHP_EOL, $found_field['bulk_options'] );

						$result = array();

						foreach ( $bulk_options as $option ) {
							$parsed_option             = explode( '::', trim( $option ) );
							$result[$parsed_option[0]] = isset( $parsed_option[1] ) ? $parsed_option[1] : $parsed_option[0];
						}
					}

					return $result;
			}
		}

		/**
		 * get raw options list from the DB by field key
		 *
		 * @param  string $field_key Field key to get options by.
		 * @return array
		 */
		public function get_options_by_field_key( $field_key = '' ) {

			global $wpdb;

			$options = array();

			$raw_results = $wpdb->get_results( $wpdb->prepare(
				"SELECT DISTINCT `meta_value` FROM {$wpdb->postmeta} WHERE `meta_key` = '%s';",
				sanitize_text_field( $field_key )
			) );

			if ( ! empty( $raw_results ) ) {
				foreach ( $raw_results as $row ) {
					$value = maybe_unserialize( $row->meta_value );
					if ( $value && ! is_array( $value ) && ! is_object( $value ) ) {
						$value = wp_kses( $value, array() );
						$options[ $value ] = $value;
					}
				}
			}

			return $options;
		}

		/**
		 * Find choices for filter from custom content types
		 */
		public function get_choices_from_cct_data( $field_key ) {

			$result = array();

			if ( ! function_exists( 'jet_engine' ) || ! jet_engine()->modules->is_module_active( 'custom-content-types' ) ) {
				return $result;
			}

			$found_field        = null;
			$all_content_types  = jet_engine()->modules->get_module( 'custom-content-types' )->instance->manager->get_content_types();

			foreach ( $all_content_types as $content_type ) {
				$content_type_fields = property_exists( $content_type, 'fields' ) ? $content_type->fields : array();

				foreach ( $content_type_fields as $field_data ) {
					if ( ! empty( $field_data['name'] ) && $field_key === $field_data['name'] ) {
						$found_field = $field_data;
					}
				}
			}

			if ( ! empty( $found_field['options'] ) ) {
				foreach ( $found_field['options'] as $option ) {
					$result[ $option['key'] ] = $option['value'];
				}
			}

			if ( isset( $found_field['options_source'] ) && $found_field['options_source'] === 'manual_bulk' && ! empty( $found_field['bulk_options'] ) ) {
				$bulk_options = explode( PHP_EOL, $found_field['bulk_options'] );

				$result = array();

				foreach ( $bulk_options as $option ) {
					$parsed_option             = explode( '::', trim( $option ) );
					$result[$parsed_option[0]] = isset( $parsed_option[1] ) ? $parsed_option[1] : $parsed_option[0];
				}
			}

			return $result;
		}

		/**
		 * Retrun regitered content providers
		 */
		public function content_providers() {

			$providers = jet_smart_filters()->providers->get_providers();
			$result    = array(
				'' => esc_html__( 'Select...', 'jet-smart-filters' ),
			);

			foreach ( $providers as $provider_id => $provider ) {
				$result[ $provider_id ] = $provider->get_name();
			}

			return $result;
		}

		/**
		 * Retrun filters by passed type
		 */
		public function get_filters_by_type( $type = null ) {

			$args = array(
				'post_type'      => jet_smart_filters()->post_type->slug(),
				'posts_per_page' => -1,
			);

			if ( $type ) {
				$args['meta_query'] = array(
					array(
						'key'     => '_filter_type',
						'value'   => $type,
						'compare' => '=',
					),
				);
			}

			$filters = get_posts( $args );

			if ( empty( $filters ) ) {
				return array();
			}

			return wp_list_pluck( $filters, 'post_title', 'ID' );
		}

		/**
		 * Returns terms objects list
		 */
		public function get_terms_objects( $tax = null, $child_of_current = false, $custom_args = array() ) {

			if ( ! $tax ) {
				return array();
			}

			if ( ! is_array( $custom_args ) ) {
				$custom_args = array();
			}

			$args = array_merge( array( 'taxonomy' => $tax ), $custom_args );

			if ( $child_of_current && ( is_category() || is_tag() || is_tax( $tax ) ) ) {
				$args['child_of'] = get_queried_object_id();
			}

			$terms = get_terms( $args );

			if ( is_wp_error( $terms ) ) {
				$terms = array();
			}

			return apply_filters(
				'jet-smart-filters/data/terms-objects',
				$terms,
				$tax,
				$child_of_current
			);
		}

		/**
		 * Get terms of passed taxonomy for checkbox/select/radio options
		 */
		public function get_terms_for_options( $tax = null, $child_of_current = false, $is_slugs = false, $custom_args = array() ) {

			$terms   = $this->get_terms_objects( $tax, $child_of_current, $custom_args );
			$options = array();

			foreach ( $terms as $term ) {
				array_push( $options, array(
					'value' => $term->term_id,
					'label' => $term->name
				) );

				if ( $is_slugs ) {
					$options[count($options) - 1]['data_attrs'] = array(
						'url-value' => $term->slug
					);
				}
			}

			return apply_filters(
				'jet-smart-filters/data/terms-for-options',
				$options,
				$tax,
				$child_of_current
			);
		}

		/**
		 * Prepare repeater options fields
		 */
		public function maybe_parse_repeater_options( $options ) {

			if ( ! is_array( $options ) || empty( $options ) ) {
				return array();
			}

			$option_values = array_values( $options );

			if ( ! is_array( $option_values[0] ) ) {
				return $options;
			}

			$result = array();

			foreach ( $options as $option ) {

				$values = array_values( $option );

				if ( ! isset( $values[0] ) ) {
					continue;
				}

				$result[ $values[0] ] = isset( $values[1] ) ? $values[1] : $values[0];

			}

			return $result;
		}

		/**
		 * Exclude or include items in options list
		 */
		public function maybe_include_exclude_options( $use_exclude_include, $exclude_include_options, $options ) {

			if ( empty( $exclude_include_options ) ){
				return $options;
			}

			switch ( $use_exclude_include ) {
				case 'include' :
					$filtered_options = array();

					foreach ( $options as $key => $value ) {
						$search_key = $key;

						if ( is_object( $value ) ){
							$search_key = $value->term_id;
						}

						if ( is_array( $value ) && isset( $value['value'] ) ) {
							$search_key = $value['value'];
						}

						if ( in_array( $search_key, $exclude_include_options ) ){
							$filtered_options[ $key ] = $options[ $key ];
						}
					}

					$options = $filtered_options;
					break;
				case 'exclude' :
					foreach ( $options as $key => $value ) {
						$search_key = $key;

						if ( is_object( $value ) ){
							$search_key = $value->term_id;
						}

						if ( is_array( $value ) && isset( $value['value'] ) ) {
							$search_key = $value['value'];
						}

						if ( in_array( $search_key, $exclude_include_options ) ){
							unset( $options[ $key ] );
						}
					}
					break;
			}

			return $options;
		}

		/**
		 * Get tabindex attribute
		 */
		public function get_tabindex_attr() {

			$use_tabindex = filter_var( jet_smart_filters()->settings->get( 'use_tabindex', false ), FILTER_VALIDATE_BOOLEAN );

			return $use_tabindex
				? 'tabindex="0"'
				: '';
		}
	}
}
