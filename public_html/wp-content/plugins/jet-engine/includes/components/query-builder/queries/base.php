<?php
namespace Jet_Engine\Query_Builder\Queries;

use Jet_Engine\Query_Builder\Manager;

abstract class Base_Query {

	public $id            = false;
	public $name          = false;
	public $query         = array();
	public $dynamic_query = array();
	public $final_query   = null;
	public $query_type    = null;
	public $query_id      = null;
	public $preview       = array();
	public $cache_query   = true;
	public $cache_expires = 0;
	public $cache_group   = 'jet-engine';

	public $api_settings = [];

	public $parsed_macros = array();

	protected $nested_args_prefix = '_id::';

	public function __construct( $args = array() ) {

		$this->id            = ! empty( $args['id'] ) ? $args['id'] : false;
		$this->name          = ! empty( $args['name'] ) ? $args['name'] : false;
		$this->query_id      = ! empty( $args['query_id'] ) ? $args['query_id'] : null;
		$this->query_type    = ! empty( $args['type'] ) ? $args['type'] : false;
		$this->query         = ! empty( $args['query'] ) ? $args['query'] : false;
		$this->dynamic_query = ! empty( $args['dynamic_query'] ) ? $args['dynamic_query'] : false;
		$this->preview       = ! empty( $args['preview'] ) ? $args['preview'] : $this->preview;
		$this->cache_query   = isset( $args['cache_query'] ) ? filter_var( $args['cache_query'], FILTER_VALIDATE_BOOLEAN ) : true;
		$this->cache_expires = isset( $args['cache_expires'] ) ? absint( $args['cache_expires'] ) : 0;

		$this->api_settings = [
			'api_endpoint' => ! empty( $args['api_endpoint'] ) ? $args['api_endpoint'] : false,
			'api_namespace' => ! empty( $args['api_namespace'] ) ? $args['api_namespace'] : false,
			'api_path' => ! empty( $args['api_path'] ) ? $args['api_path'] : false,
			'api_access' => ! empty( $args['api_access'] ) ? $args['api_access'] : false,
			'api_access_cap' => ! empty( $args['api_access_cap'] ) ? $args['api_access_cap'] : false,
			'api_access_role' => ! empty( $args['api_access_role'] ) ? $args['api_access_role'] : false,
			'api_schema' => ! empty( $args['api_schema'] ) ? $args['api_schema'] : false,
		];

		$this->maybe_add_instance_fields_to_ui();

	}

	/**
	 * Returns query type for 3rd party integrations. For any internal usage take property directly
	 *
	 * @return string
	 */
	public function get_query_type() {
		return $this->query_type;
	}

	/**
	 * Register Rest API endpoint for this query if enbaled.
	 *
	 * @return void
	 */
	public function maybe_register_rest_api_endpoint() {

		if ( empty( $this->api_settings['api_endpoint'] ) ) {
			return;
		}

		if ( empty( $this->api_settings['api_namespace'] ) || empty( $this->api_settings['api_path'] ) ) {
			return;
		}

		if ( ! class_exists( '\Jet_Engine\Query_Builder\Rest\Query_Endpoint' ) ) {
			require_once Manager::instance()->component_path( 'rest-api/query-endpoint.php' );
		}

		new \Jet_Engine\Query_Builder\Rest\Query_Endpoint( array_merge( $this->api_settings, [
			'id' => $this->id
		] ) );

	}

	/**
	 * Returns items per page.
	 * Each query which allows pagintaion should implement own method to gettings items per page.
	 *
	 * @param  integer $default [description]
	 * @return integer
	 */
	public function get_items_per_page() {
		return 0;
	}

	/**
	 * Returns query cache
	 *
	 * @param  string $key [description]
	 * @return string
	 */
	public function get_query_hash( $key = null ) {

		$this->setup_query();

		$prefix = 'jet_query_';

		if ( $key ) {
			$prefix .= $key . '_';
		}

		return $prefix . md5( json_encode( $this->final_query ) );

	}

	/**
	 * Allows to return any query specific data that may be used by abstract 3rd parties
	 *
	 * @return array
	 */
	public function get_query_meta() {
		return array();
	}

	/**
	 * Get cached data
	 *
	 * @param  [type] $key [description]
	 * @return mixed
	 */
	public function get_cached_data( $key = null ) {
		return $this->cache_query ? wp_cache_get( $this->get_query_hash( $key ), $this->cache_group ) : false;
	}

	/**
	 * Update cache for the current query instance
	 *
	 * @param  [type] $data [description]
	 * @param  [type] $key  [description]
	 * @return bool
	 */
	public function update_query_cache( $data = null, $key = null ) {
		return $this->cache_query ? wp_cache_set( $this->get_query_hash( $key ), $data, $this->cache_group, $this->get_cache_expiration() ) : false;
	}

	/**
	 * Get cache expiration period for query results
	 *
	 * @return integer
	 */
	public function get_cache_expiration() {
		return $this->cache_expires;
	}

	/**
	 * Check if current query has items
	 *
	 * @return boolean [description]
	 */
	public function has_items() {
		$items = $this->get_items();
		return ! empty( $items );
	}

	/**
	 * Add current instance fields into the UI elements
	 *
	 * @param [type] $groups [description]
	 */
	public function maybe_add_instance_fields_to_ui() {

		$fields = $this->get_instance_fields();

		if ( empty( $fields ) ) {
			return;
		}

		add_filter(
			'jet-engine/listing/data/object-fields-groups',
			array( $this, 'add_source_fields' )
		);

	}

	/**
	 * Add source fields into the UI elements
	 *
	 * @param [type] $groups [description]
	 */
	public function add_source_fields( $groups ) {

		$groups[] = array(
			'label'   => __( 'Query:', 'jet-engine' ) . ' ' . $this->get_instance_name(),
			'options' => $this->get_instance_fields(),
		);

		return $groups;

	}

	/**
	 * Get fields list are available for the current instance of this query
	 *
	 * @return [type] [description]
	 */
	public function get_instance_fields() {
		return array();
	}

	/**
	 * Returns query instance name to use in the UI
	 * @return [type] [description]
	 */
	public function get_instance_name() {
		return $this->name;
	}

	/**
	 * Preapre query
	 *
	 * @return [type] [description]
	 */
	public function setup_query() {

		$this->maybe_reset_query();

		if ( null !== $this->final_query ) {

			/**
			 * Before get query items
			 */
			//do_action( 'jet-engine/query-builder/query/after-query-setup', $this );
			return;
		}

		$processed_dynamics = array();

		$this->final_query = $this->query;

		if ( ! $this->final_query ) {
			$this->final_query = array();
		}

		foreach ( $this->final_query as $key => $value ) {
			if ( is_array( $value ) ) {
				$reset = false;

				foreach ( $value as $inner_key => $inner_value ) {

					if ( ! $reset && is_array( $inner_value ) && ! empty( $inner_value['_id'] ) ) {
						$reset = true;
						$this->final_query[ $key ] = array();
					}

					if ( $reset ) {

						if ( isset( $this->dynamic_query[ $key ][ $inner_value['_id'] ] ) ) {

							$inner_value = array_merge(
								$inner_value,
								$this->apply_macros(
									$this->dynamic_query[ $key ][ $inner_value['_id'] ]
								)
							);

							if ( ! in_array( $key, $processed_dynamics ) ) {
								$processed_dynamics[] = $key;
							}
						}

						/**
						 * Check if inner value represents a group of nested arguments
						 * (like nested meta queries, CCT args etc.)
						 * if yes - merge parsed dynamic argumnets into the 'args' key
						 */
						if (
							is_array( $inner_value )
							&& ! empty( $inner_value['is_group'] )
							&& ! empty( $inner_value['args'] )
						) {
							$inner_value= $this->merge_dynamic_nested_args( $inner_value );
						}

						$this->final_query[ $key ][] = $inner_value;
					}
				}
			}
		}

		if ( ! empty( $this->dynamic_query ) ) {
			foreach ( $this->dynamic_query as $key => $value ) {
				if ( ! in_array( $key, $processed_dynamics ) ) {
					if ( ! empty( $value ) ) {
						$this->final_query[ $key ] = $this->apply_macros( $value );
					}
				}
			}
		}

		$raw_dynamic = $this->get_args_to_dynamic();

		if ( ! empty( $raw_dynamic ) ) {
			foreach ( $raw_dynamic as $key ) {
				if ( ! empty( $this->final_query[ $key ] ) ) {
					$this->final_query[ $key ] = $this->apply_macros( $this->final_query[ $key ] );
				}
			}
		}

		$explode = $this->get_args_to_explode();

		if ( ! empty( $explode ) ) {
			foreach ( $this->final_query as $key => $value ) {
				if ( in_array( $key, $explode ) ) {
					$this->final_query[ $key ] = $this->explode_string( $value );
				}
			}
		}

		$this->final_query['_query_type']       = $this->query_type;
		$this->final_query['queried_object_id'] = jet_engine()->listings->data->get_current_object_id();

		jet_engine()->admin_bar->register_item( $this->get_instance_id(), array(
			'title'        => $this->get_instance_name(),
			'sub_title'    => __( 'Query', 'jet-engine' ),
			'href'         => admin_url( 'admin.php?page=jet-engine-query&query_action=edit&id=' . $this->id ),
		) );

		/**
		 * Before get query items
		 */
		do_action( 'jet-engine/query-builder/query/after-query-setup', $this );

	}

	/**
	 * Merge dynamic inner args.
	 * Required for nested arguments like meta queries, CCT args etc.
	 *
	 * @param  array $args_group Query argument, which describes an additional group of nested arguments
	 * @return array
	 */
	public function merge_dynamic_nested_args( $args_group = [] ) {

		$args = ! empty( $args_group['args'] ) ? $args_group['args'] : [];

		foreach ( $args as $index => $arg ) {
			if (
				! empty( $arg['_id'] )
				&& isset( $args_group[ $this->nested_args_prefix . $arg['_id'] ] )
			) {
				$args[ $index ] = array_merge(
					$arg,
					$args_group[ $this->nested_args_prefix . $arg['_id'] ]
				);

				unset( $args_group[ $this->nested_args_prefix . $arg['_id'] ] );
			}
		}

		$args_group['args'] = $args;

		return $args_group;
	}

	public function maybe_reset_query() {

		if ( null === $this->final_query ) {
			return;
		}

		if ( empty( $this->parsed_macros ) ) {
			return;
		}

		$dynamic_query_changed = false;

		foreach ( $this->parsed_macros as $macro => $value ) {

			if ( $value !== jet_engine()->listings->macros->do_macros( $macro ) ) {
				$dynamic_query_changed = true;
				break;
			}
		}

		if ( ! $dynamic_query_changed ) {
			return;
		}

		$this->final_query = null;

		$this->reset_query();
	}

	public function reset_query() {}

	/**
	 * Apply macros by passed string
	 *
	 * @param  [type] $val [description]
	 * @return [type]      [description]
	 */
	public function apply_macros( $val ) {

		if ( is_array( $val ) ) {

			$result = array();

			foreach ( $val as $key => $value ) {
				if ( ! empty( $value ) ) {
					if ( is_array( $value ) ) {
						$result[ $this->nested_args_prefix . $key ] = $this->apply_macros( $value );
					} else {
						$result[ $key ] = jet_engine()->listings->macros->do_macros( $value );
						$this->parsed_macros[ $value ] = $result[ $key ];
					}
				}
			}

			return $result;
		} else {
			$result = jet_engine()->listings->macros->do_macros( $val );
			$this->parsed_macros[ $val ] = $result;

			return $result;
		}

	}

	/**
	 * Returns query instance id
	 * @return [type] [description]
	 */
	public function get_instance_id() {
		return '_query_' . $this->id;
	}

	/**
	 * Returns current query arguments
	 *
	 * @return array
	 */
	public function get_query_args() {

		if ( null === $this->final_query ) {
			$this->setup_query();
		}

		return $this->final_query;
	}

	/**
	 * Returns queried items array
	 *
	 * @return array
	 */
	public function get_items() {

		$cached = $this->get_cached_data();

		if ( false !== $cached ) {
			/**
			 * Before get query items
			 */
			do_action( 'jet-engine/query-builder/query/before-get-items', $this, true );

			return apply_filters( 'jet-engine/query-builder/query/items', $cached, $this );
		}

		$this->setup_query();

		/**
		 * Before get query items
		 */
		do_action( 'jet-engine/query-builder/query/before-get-items', $this, false );

		$items = $this->_get_items();

		$this->update_query_cache( $items );

		return apply_filters( 'jet-engine/query-builder/query/items', $items, $this );

	}

	/**
	 * Array of arguments where string should be exploded into array
	 * Format:
	 * array(
	 * 	'post__in',
	 * 	'post__not_in',
	 * )
	 * @return [type] [description]
	 */
	public function get_args_to_explode() {
		return array();
	}

	/**
	 * Array of arguments whose values can contain macros
	 *
	 * @return array
	 */
	public function get_args_to_dynamic() {
		return array();
	}

	public function explode_string( $value ) {

		if ( $value && ! is_array( $value ) && ( false !== strpos( $value, ',' ) ) ) {
			$value = str_replace( ', ', ',', $value );
			$value = explode( ',', $value );
			$value = array_map( 'trim', $value );
		}

		if ( $value && ! is_array( $value ) ) {
			$value = array( $value );
		}

		return $value;

	}

	/**
	 * Returns queries items
	 *
	 * @return [type] [description]
	 */
	abstract public function _get_items();

	/**
	 * Returns total found items count
	 *
	 * @return [type] [description]
	 */
	abstract public function get_items_total_count();

	/**
	 * Returns queried items count per page
	 *
	 * @return [type] [description]
	 */
	abstract public function get_items_page_count();

	/**
	 * Returns queried items pages count
	 *
	 * @return [type] [description]
	 */
	abstract public function get_items_pages_count();

	/**
	 * Returns currently queried items page
	 *
	 * @return [type] [description]
	 */
	abstract public function get_current_items_page();

	/**
	 * Set filtered prop in specific for current query type way
	 *
	 * @param string $prop  [description]
	 * @param [type] $value [description]
	 */
	abstract public function set_filtered_prop( $prop = '', $value = null );

	public function merge_default_props( $prop, $value ) {

		if ( is_array( $value ) ) {

			$replace_array_props = apply_filters( 'jet-engine/query-builder/query/replace-array-props', false, $prop, $value, $this );

			if ( empty( $this->final_query[ $prop ] ) || ! is_array( $this->final_query[ $prop ] ) || $replace_array_props ) {
				$this->final_query[ $prop ] = $value;
			} else {
				$this->final_query[ $prop ] = array_merge( $this->final_query[ $prop ], $value );
			}
		} else {
			$this->final_query[ $prop ] = $value;
		}

	}

	/**
	 * Adds date range query arguments to given query parameters.
	 * Required to allow ech query to ensure compatibility with Dynamic Calendar
	 *
	 * @param array $args [description]
	 */
	public function add_date_range_args( $args = array(), $dates_range = array(), $settings = array() ) {
		$group_by = $settings['group_by'];
		return $args;
	}

	public function before_preview_body() {}

	public function debug_info() {
		return apply_filters( 'jet-engine/query-builder/query/debug-info', $this->_debug_info(), $this );
	}

	public function _debug_info() {}

	public function get_start_item_index_on_page() {

		$page = $this->get_current_items_page();

		if ( 1 === $page ) {
			return 1;
		}

		$per_page = $this->get_items_per_page();

		return ( $page - 1 ) * $per_page + 1;
	}

	public function get_end_item_index_on_page() {

		$page             = $this->get_current_items_page();
		$items_page_count = $this->get_items_page_count();

		if ( 1 === $page ) {
			return $items_page_count;
		}

		$per_page = $this->get_items_per_page();

		return ( $page - 1 ) * $per_page + $items_page_count;
	}

}
