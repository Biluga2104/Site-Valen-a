<?php
/**
 * Plugin Name: JetTricks
 * Plugin URI:  https://crocoblock.com/plugins/jettricks/
 * Description: Use different eye-catching stylish animation effects and let your content become truly alive with outstanding visual tricks!
 * Version:     1.5.5.1
 * Author:      Crocoblock
 * Author URI:  https://crocoblock.com/
 * Text Domain: jet-tricks
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

// If class `Jet_Tricks` doesn't exists yet.
if ( ! class_exists( 'Jet_Tricks' ) ) {

	/**
	 * Sets up and initializes the plugin.
	 */
	class Jet_Tricks {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    object
		 */
		private static $instance = null;

		/**
		 * Plugin version
		 *
		 * @var string
		 */

		private $version = '1.5.5.1';

		/**
		 * Holder for base plugin URL
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string
		 */
		private $plugin_url = null;

		/**
		 * Holder for base plugin path
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string
		 */
		private $plugin_path = null;

		/**
		 * Framework component
		 *
		 * @since  1.0.0
		 * @access public
		 * @var    object
		 */
		public $module_loader;

		/**
		 * Sets up needed actions/filters for the plugin to initialize.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct() {

			// Load framework
			add_action( 'after_setup_theme', array( $this, 'module_loader' ), -20 );

			// Internationalize the text strings used.
			add_action( 'init', array( $this, 'lang' ), -999 );
			// Load files.
			add_action( 'init', array( $this, 'init' ), -999 );
			// Jet Dashboard Init
			add_action( 'init', array( $this, 'jet_dashboard_init' ), -999 );

			// Register activation and deactivation hook.
			register_activation_hook( __FILE__, array( $this, 'activation' ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
		}

		/**
		 * Load framework modules
		 *
		 * @since  1.0.0
		 * @access public
		 * @return object
		 */
		public function module_loader() {
			require $this->plugin_path( 'includes/modules/loader.php' );

			$this->module_loader = new Jet_Tricks_CX_Loader(
				array(
					$this->plugin_path( 'includes/modules/vue-ui/cherry-x-vue-ui.php' ),
					$this->plugin_path( 'includes/modules/jet-dashboard/jet-dashboard.php' ),
					$this->plugin_path( 'includes/modules/jet-elementor-extension/jet-elementor-extension.php' ),
				)
			);
		}

		/**
		 * Returns plugin version
		 *
		 * @return string
		 */
		public function get_version() {
			return $this->version;
		}

		/**
		 * Manually init required modules.
		 *
		 * @return void
		 */
		public function init() {

			if ( ! $this->has_elementor() ) {
				add_action( 'admin_notices', array( $this, 'required_plugins_notice' ) );
				return;
			}

			$this->load_files();

			jet_tricks_settings()->init();
			jet_tricks_elementor_section_extension()->init();
			jet_tricks_elementor_column_extension()->init();
			jet_tricks_elementor_widget_extension()->init();
			jet_tricks_integration()->init();
			jet_tricks_assets()->init();
			jet_tricks_compatibility()->init();

			//Init Rest Api
			new \Jet_Tricks\Rest_Api();

			if ( is_admin() ) {
				//Init Rest Api
				new \Jet_Tricks\Settings();
			}

			do_action( 'jet-tricks/init', $this );

		}

		/**
		 * [jet_dashboard_init description]
		 * @return [type] [description]
		 */
		public function jet_dashboard_init() {

			if ( is_admin() ) {

				$cx_ui_module_data         = $this->module_loader->get_included_module_data( 'cherry-x-vue-ui.php' );
				$jet_dashboard_module_data = $this->module_loader->get_included_module_data( 'jet-dashboard.php' );

				$jet_dashboard = \Jet_Dashboard\Dashboard::get_instance();

				$jet_dashboard->init( array(
					'path'           => $jet_dashboard_module_data['path'],
					'url'            => $jet_dashboard_module_data['url'],
					'cx_ui_instance' => array( $this, 'jet_dashboard_ui_instance_init' ),
					'plugin_data'    => array(
						'slug'    => 'jet-tricks',
						'file'    => 'jet-tricks/jet-tricks.php',
						'version' => $this->get_version(),
						'plugin_links' => array(
							array(
								'label'  => esc_html__( 'Go to settings', 'jet-tricks' ),
								'url'    => add_query_arg( array( 'page' => 'jet-dashboard-settings-page', 'subpage' => 'jet-tricks-general-settings' ), admin_url( 'admin.php' ) ),
								'target' => '_self',
							),
						),
					),
				) );
			}
		}

		/**
		 * [jet_dashboard_ui_instance_init description]
		 * @return [type] [description]
		 */
		public function jet_dashboard_ui_instance_init() {
			$cx_ui_module_data = $this->module_loader->get_included_module_data( 'cherry-x-vue-ui.php' );

			return new CX_Vue_UI( $cx_ui_module_data );
		}

		/**
		 * Show recommended plugins notice.
		 *
		 * @return void
		 */
		public function required_plugins_notice() {
			$screen = get_current_screen();

			if ( isset( $screen->parent_file ) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id ) {
				return;
			}

			$plugin = 'elementor/elementor.php';

			$installed_plugins      = get_plugins();
			$is_elementor_installed = isset( $installed_plugins[ $plugin ] );

			if ( $is_elementor_installed ) {
				if ( ! current_user_can( 'activate_plugins' ) ) {
					return;
				}

				$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );

				$message = sprintf( '<p>%s</p>', esc_html__( 'JetTricks requires Elementor to be activated.', 'jet-tricks' ) );
				$message .= sprintf( '<p><a href="%s" class="button-primary">%s</a></p>', $activation_url, esc_html__( 'Activate Elementor Now', 'jet-tricks' ) );
			} else {
				if ( ! current_user_can( 'install_plugins' ) ) {
					return;
				}

				$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );

				$message = sprintf( '<p>%s</p>', esc_html__( 'JetTricks requires Elementor to be installed.', 'jet-tricks' ) );
				$message .= sprintf( '<p><a href="%s" class="button-primary">%s</a></p>', $install_url, esc_html__( 'Install Elementor Now', 'jet-tricks' ) );
			}

			printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', wp_kses_post( $message ) );
		}

		/**
		 * Load required files
		 *
		 * @return void
		 */
		public function load_files() {
			require $this->plugin_path( 'includes/settings.php' );
			require $this->plugin_path( 'includes/settings/manager.php' );
			require $this->plugin_path( 'includes/integration.php' );
			require $this->plugin_path( 'includes/tools.php' );
			require $this->plugin_path( 'includes/ext/element-section-extension.php' );
			require $this->plugin_path( 'includes/ext/element-column-extension.php' );
			require $this->plugin_path( 'includes/ext/element-widget-extension.php' );
			require $this->plugin_path( 'includes/assets.php' );
			require $this->plugin_path( 'includes/compatibility/compatibility.php' );
			require $this->plugin_path( 'includes/rest-api/rest-api.php' );
			require $this->plugin_path( 'includes/rest-api/endpoints/base.php' );
			require $this->plugin_path( 'includes/rest-api/endpoints/plugin-settings.php' );
		}

		/**
		 * Check if theme has elementor
		 *
		 * @return boolean
		 */
		public function has_elementor() {
			return defined( 'ELEMENTOR_VERSION' );
		}

		/**
		 * [elementor description]
		 * @return [type] [description]
		 */
		public function elementor() {
			return \Elementor\Plugin::$instance;
		}

		/**
		 * Returns path to file or dir inside plugin folder
		 *
		 * @param  string $path Path inside plugin dir.
		 * @return string
		 */
		public function plugin_path( $path = null ) {

			if ( ! $this->plugin_path ) {
				$this->plugin_path = trailingslashit( plugin_dir_path( __FILE__ ) );
			}

			return $this->plugin_path . $path;
		}
		/**
		 * Returns url to file or dir inside plugin folder
		 *
		 * @param  string $path Path inside plugin dir.
		 * @return string
		 */
		public function plugin_url( $path = null ) {

			if ( ! $this->plugin_url ) {
				$this->plugin_url = trailingslashit( plugin_dir_url( __FILE__ ) );
			}

			return $this->plugin_url . $path;
		}

		/**
		 * Loads the translation files.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function lang() {
			load_plugin_textdomain( 'jet-tricks', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Get the template path.
		 *
		 * @return string
		 */
		public function template_path() {
			return apply_filters( 'jet-tricks/template-path', 'jet-tricks/' );
		}

		/**
		 * Returns path to template file.
		 *
		 * @return string|bool
		 */
		public function get_template( $name = null ) {

			$template = locate_template( $this->template_path() . $name );

			if ( ! $template ) {
				$template = $this->plugin_path( 'templates/' . $name );
			}

			if ( file_exists( $template ) ) {
				return $template;
			} else {
				return false;
			}
		}

		/**
		 * Do some stuff on plugin activation
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function activation() {
		}

		/**
		 * Do some stuff on plugin activation
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function deactivation() {
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @access public
		 * @return object
		 */
		public static function get_instance() {
			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}
			return self::$instance;
		}
	}
}

if ( ! function_exists( 'jet_tricks' ) ) {

	/**
	 * Returns instanse of the plugin class.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	function jet_tricks() {
		return Jet_Tricks::get_instance();
	}
}

jet_tricks();
