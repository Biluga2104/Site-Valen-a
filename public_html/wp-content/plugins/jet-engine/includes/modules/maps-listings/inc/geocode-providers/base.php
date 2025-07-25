<?php
namespace Jet_Engine\Modules\Maps_Listings\Geocode_Providers;

use Jet_Engine\Modules\Maps_Listings\Base_Provider;

abstract class Base extends Base_Provider {

	private $errors = array();

	/**
	 * Hook name to register provider-specific settings
	 *
	 * @return [type] [description]
	 */
	public function settings_hook() {
		return 'jet-engine/maps-listing/settings/geocode-provider-controls';
	}

	public function get_api_key( $for_geocoding = false ) {
		return '';
	}

	/**
	 * Build API URL for given location string
	 * @return [type] [description]
	 */
	public function build_api_url( $location ) {
		return false;
	}

	/**
	 * Build Reverse geocoding API URL for given coordinates point
	 * @return [type] [description]
	 */
	public function build_reverse_api_url( $point = array() ) {
		return false;
	}

	/**
	 * Build Autocomplete API URL for given place predictions
	 * @return mixed
	 */
	public function build_autocomplete_api_url( $query = '' ) {
		return false;
	}

	public function get_error( $type = 'autocomplete' ) {
		return $this->errors[ $type ] ?? false;
	}

	public function save_error( $error, $type = 'autocomplete' ) {
		$this->errors[ $type ] = $error;
	}

	public function clear_error( $type = 'autocomplete' ) {
		unset( $this->errors[ $type ] );
	}

	/**
	 * Make geocoding request to the given URL
	 *
	 * @param  [type] $url [description]
	 * @return [type]      [description]
	 */
	public function make_request( $request_url ) {

		$response = wp_remote_get( $request_url, array(
			'headers' => array(
				'accept-language' => get_bloginfo( 'language' ),
			)
		) );

		$json = wp_remote_retrieve_body( $response );

		$data = json_decode( $json, true );

		return $data;
	}

	/**
	 * Find coordinates in the response data and return it
	 *
	 * @param  array       $data Response data from API
	 * @return array|false       Array of [ 'lat' => float, 'lng' => float ] if location found, false otherwise
	 */
	public function extract_coordinates_from_response_data( $data = array() ) {
		return false;
	}

	/**
	 * Find location name in the reverse geocoding response data and return it
	 *
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function extract_location_from_response_data( $data = array() ) {
		return false;
	}

	/**
	 * Find place predictions in the response data and return it
	 *
	 * @param  array $data
	 * @return array|false
	 */
	public function extract_autocomplete_data_from_response_data( $data = array() ) {
		return false;
	}

	/**
	 * Returns location name for given coordinates
	 *
	 * @param  string $location [description]
	 * @return [type]           [description]
	 */
	public function get_reverse_location_data( $point = array() ) {

		if ( empty( $point ) || empty( $point['lat'] ) || empty( $point['lng'] ) ) {
			return false;
		}

		$url = $this->build_reverse_api_url( $point );

		if ( ! $url ) {
			return false;
		}

		$data = $this->make_request( $url );

		if ( ! $data ) {
			return false;
		}

		return $this->extract_location_from_response_data( $data );

	}

	/**
	 * WP esc_attr function analog, that does not escape quotes
	 * https://github.com/Crocoblock/issues-tracker/issues/12562
	 *
	 * @param  string $location String to escape
	 * @return string           Escaped string
	 */
	public function esc_attr( $location ) {
		if ( ! preg_match( '/[&<>"\']/', $location ) ) {
			return $location;
		}

		return htmlspecialchars( $location, ENT_NOQUOTES );
	}

	/**
	 * Returns data for the given location
	 *
	 * @param  string      $location Location string, address
	 * @return array|false [ 'lat' => float, 'lng' => float ] if location found, false if not found or empty string given
	 */
	public function get_location_data( $location = '' ) {

		if ( ! $location ) {
			return false;
		}

		$url = $this->build_api_url( $this->esc_attr( $location ) );

		if ( ! $url ) {
			return false;
		}

		$data = $this->make_request( $url );

		return $this->extract_coordinates_from_response_data( $data );

	}

	/**
	 * Returns data for the given place predictions
	 *
	 * @param  string $query
	 * @return array|false
	 */
	public function get_autocomplete_data( $query = '' ) {

		if ( ! $query ) {
			return false;
		}

		$url = $this->build_autocomplete_api_url( esc_attr( $query ) );

		if ( ! $url ) {
			return false;
		}

		$data = $this->make_request( $url );

		if ( ! empty( $data['error_message'] ) || ! empty( $data['error'] ) ) {
			$this->save_error( $data, 'autocomplete' );
		}

		if ( ! $data ) {
			return false;
		}

		return $this->extract_autocomplete_data_from_response_data( $data );
	}

}
