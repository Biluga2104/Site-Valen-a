<?php
namespace Jet_Engine\Modules\Rest_API_Listings\Query_Builder;

use Jet_Engine\Modules\Rest_API_Listings\Module;

class REST_API_Query extends \Jet_Engine\Query_Builder\Queries\Base_Query {

	public $current_query = null;

	/**
	 * Returns queries items
	 *
	 * @return [type] [description]
	 */
	public function _get_items() {

		$result = array();
		$eid = ! empty( $this->final_query['endpoint'] ) ? absint( $this->final_query['endpoint'] ) : false;

		if ( ! $eid ) {
			return $result;
		}

		$endpoint = Module::instance()->settings->get( $eid );

		if ( ! $endpoint ) {
			return $result;
		}

		$query_args = $this->get_query_args_from_query();

		$result = Module::instance()->request->set_endpoint( $endpoint )->get_items( $query_args );

		if ( empty( $result ) ) {
			$result = array();
		}

		array_walk( $result, function( &$item, $key ) {
			$item->is_rest_api_endpoint = true;
			$item->_rest_api_item_id = $this->id . '-' . $key;
		} );

		return $result;

	}

	/**
	 * Returns parsed query arguments
	 *
	 * @return array
	 */
	public function get_query_args_from_query() {

		$query_args = array();

		if ( ! empty( $this->final_query['args'] ) ) {
			foreach ( $this->final_query['args'] as $arg ) {

				$value = jet_engine()->listings->macros->do_macros( $arg['value'] );

				$exclude_empty = ! empty( $arg['exclude_empty'] ) ? $arg['exclude_empty'] : false;
				$exclude_empty = filter_var( $exclude_empty, FILTER_VALIDATE_BOOLEAN );

				if ( $exclude_empty && \Jet_Engine_Tools::is_empty( $value ) ) {
					continue;
				}

				if ( ! empty( $arg['field'] ) ) {
					$query_args[ $arg['field'] ] = $value;
				}
			}
		}

		return $query_args;
	}

	public function get_current_items_page() {
		return 1;
	}

	/**
	 * Returns total found items count
	 *
	 * @return [type] [description]
	 */
	public function get_items_total_count() {

		$cached = $this->get_cached_data( 'count' );

		if ( false !== $cached ) {
			return $cached;
		}

		$items = $this->get_items();
		$result = count( $items );

		$this->update_query_cache( $result, 'count' );

		return $result;

	}

	/**
	 * Returns count of the items visible per single listing grid loop/page
	 * @return [type] [description]
	 */
	public function get_items_per_page() {
		return 0;

	}

	/**
	 * Returns queried items count per page
	 *
	 * @return [type] [description]
	 */
	public function get_items_page_count() {
		return $this->get_items_total_count();
	}

	/**
	 * Returns queried items pages count
	 *
	 * @return [type] [description]
	 */
	public function get_items_pages_count() {
		return 1;
	}

	public function set_filtered_prop( $prop = '', $value = null ) {

		switch ( $prop ) {

			case 'meta_query':

				foreach ( $value as $row ) {

					$prepared_row = array(
						'field' => ! empty( $row['key'] ) ? $row['key'] : false,
						'value' => ! empty( $row['value'] ) ? $row['value'] : '',
					);

					$this->update_args_row( $prepared_row );

				}

				break;

		}

	}

	/**
	 * Update argumnts row in the arguments list of the final query
	 *
	 * @param  [type] $row [description]
	 * @return [type]      [description]
	 */
	public function update_args_row( $row ) {

		if ( empty( $this->final_query['args'] ) ) {
			$this->final_query['args'] = array();
		}

		foreach ( $this->final_query['args'] as $index => $existing_row ) {
			if ( $existing_row['field'] === $row['field'] ) {
				$this->final_query['args'][ $index ] = $row;
				return;
			}
		}

		$this->final_query['args'][] = $row;
	}

	public function before_preview_body() {

		$eid = ! empty( $this->final_query['endpoint'] ) ? absint( $this->final_query['endpoint'] ) : false;

		if ( ! $eid ) {
			return;
		}

		$endpoint = Module::instance()->settings->get( $eid );

		if ( ! $endpoint ) {
			return;
		}

		$query_args = $this->get_query_args_from_query();
		$url = Module::instance()->request->set_endpoint( $endpoint )->get_url_with_query_args( $query_args );

		print_r( esc_html__( 'REQUEST URL:' ) . "\n" );
		print_r( $url . "\n\n" );
	}

}
