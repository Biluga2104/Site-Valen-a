<?php
namespace Jet_Theme_Core\Locations;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	/**
	 * @var array
	 */
	private $_locations = array();

	/**
	 * Load files
	 */
	public function load_files() {
		require jet_theme_core()->plugin_path( 'includes/locations/render/base.php' );
		require jet_theme_core()->plugin_path( 'includes/locations/render/block-editor-render.php' );
		require jet_theme_core()->plugin_path( 'includes/locations/render/elementor-render.php' );
	}

	/**
	 * Return all locations data
	 *
	 * @return array
	 */
	public function get_locations() {
		return $this->_locations;
	}

	/**
	 * Register new location
	 *
	 * @param  [type] $id                 [description]
	 * @param  [type] $structure_instance [description]
	 * @return [type]                     [description]
	 */
	public function register_location( $id, $structure_instance ) {
		$this->_locations[ $id ] = $structure_instance;
	}

	/**
	 * Get structure object for passed location name
	 *
	 * @param  [type] $location [description]
	 * @return [type]           [description]
	 */
	public function get_structure_for_location( $location ) {
		return isset( $this->_locations[ $location ] ) ? $this->_locations[ $location ] : false;
	}

	/**
	 * Try to print location
	 *
	 * @param  string $location [description]
	 * @return [type]           [description]
	 */
	public function do_location( $location = 'header' ) {
		$is_theme_builder_render = jet_theme_core()->theme_builder->frontend_manager->is_theme_builder_render;

		if ( $is_theme_builder_render ) {
			return false;
		}

		$location = apply_filters( 'jet-theme-core/location/do-location/active-location', $location );

		$structure = $this->get_structure_for_location( $location );

		if ( ! $structure ) {
			return false;
		}

		$template_ids = jet_theme_core()->template_conditions_manager->find_matched_conditions( $structure->get_id() );

		if ( is_array( $template_ids ) && ! empty( $template_ids ) ) {
			$template_id = $template_ids[0];
		} else {
			$template_id = $template_ids;
		}

		$content_type = jet_theme_core()->templates->get_template_content_type( $template_id );

		if ( ! $template_id ) {
			$content_type = 'elementor';
		}

		/**
		 * Fires before Jet template output started
		 */
		do_action( "jet-theme-core/location/before-render/{$content_type}-location-content", $template_id, $location );

		/**
		 * Fires when template content rendered
		 */
		$render_status = apply_filters( "jet-theme-core/location/render/{$content_type}-location-content", false, $template_id, $location );

		/**
		 * Fires after Jet template output ended
		 */
		do_action( "jet-theme-core/location/after-render/{$content_type}-location-content", $template_id, $location );

		return $render_status;
	}

	/**
	 * @param $template_id
	 *
	 * @return false|void
	 */
	public function render_template( $template_id = false ) {

		if ( ! $template_id ) {
			return false;
		}

		$content_type = jet_theme_core()->templates->get_template_content_type( $template_id );
		$render_instance = false;

		switch ( $content_type ) {
			case 'default':
				$render_instance = new \Jet_Menu\Render\Block_Editor_Template_Render( [
					'template_id' => $template_id,
				] );

				break;
			case 'elementor':
				$render_instance = new \Jet_Menu\Render\Elementor_Template_Render( [
					'template_id' => $template_id,
				] );

				break;
		}

		$render_status = false;

		if ( $render_instance ) {
			do_action( 'jet-theme-core/templates/render-template/before-render', $template_id, $content_type );

			$render_status = $render_instance->render();

			do_action( 'jet-theme-core/templates/render-template/after-render', $template_id, $content_type );
		}

		return $render_status;

	}

	/**
	 * Locations constructor.
	 */
	function __construct() {
		$this->load_files();
	}

}
