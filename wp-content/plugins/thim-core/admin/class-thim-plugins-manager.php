<?php

class Thim_Plugins_Manager extends Thim_Admin_Sub_Page {
	public $key_page = 'plugins';

	/**
	 * @var string
	 *
	 * @since 0.4.0
	 */
	public static $page_key = 'plugins';

	/**
	 * @var null
	 *
	 * @since 0.5.0
	 */
	public static $all_plugins_require = null;

	/**
	 * Is writable.
	 *
	 * @since 0.5.0
	 *
	 * @var bool
	 */
	private static $is_writable = null;

	/**
	 * Add notice.
	 *
	 * @since 0.5.0
	 *
	 * @param string $content
	 * @param string $type
	 */
	public static function add_notification( $content = '', $type = 'success' ) {
		Thim_Dashboard::add_notification( array(
			'content' => $content,
			'type'    => $type,
			'page'    => self::$page_key,
		) );
	}

	/**
	 * Get url plugin actions.
	 *
	 * @since 0.8.8
	 *
	 * @param $args
	 *
	 * @return string
	 */
	public static function get_url_plugin_actions( $args ) {
		$args = wp_parse_args( $args, array(
			'slug'          => '',
			'plugin-action' => '',
			'network'       => '',
		) );

		$args['action'] = 'thim_plugins';

		$url = admin_url( 'admin.php' );
		$url = add_query_arg( $args, $url );

		return $url;
	}

	/**
	 * Thim_Plugins_Manager constructor.
	 *
	 * @since 0.4.0
	 */
	protected function __construct() {
		parent::__construct();

		$this->init();
		$this->init_hooks();
	}

	/**
	 * Init.
	 *
	 * @since 0.5.0
	 */
	private function init() {
		$this->notification();
	}

	/**
	 * Init hooks.
	 *
	 * @since 0.4.0
	 */
	private function init_hooks() {
		add_action( 'wp_ajax_thim_plugins_manager', array( $this, 'handle_ajax' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'thim_dashboard_sub_pages', array( $this, 'add_sub_page' ) );
		add_action( 'admin_action_thim_plugins', array( $this, 'handle_plugin_actions' ) );
	}

	/**
	 * Handle plugin actions like install, activate, deactivate.
	 *
	 * @since 0.8.8
	 */
	public function handle_plugin_actions() {
		$action   = isset( $_GET['plugin-action'] ) ? $_GET['plugin-action'] : false;
		$slug     = isset( $_GET['slug'] ) ? $_GET['slug'] : false;
		$network  = ! empty( $_GET['network'] ) ? true : false;
		$is_wporg = ! empty( $_GET['wporg'] ) ? true : false;

		if ( ! $action || ! $slug ) {
			return;
		}

		$plugin = new Thim_Plugin( $slug, $is_wporg );

		if ( $action == 'install' ) {
			$plugin->install();

			// Activate after install.
			$link_activate = self::get_url_plugin_actions( array(
				'slug'          => $slug,
				'plugin-action' => 'activate',
				'network'       => $network,
			) );

			thim_core_redirect( $link_activate );
		}

		if ( $action == 'activate' ) {
			$plugin->activate( false, $network );
		}

		if ( $action == 'deactivate' ) {
			$plugin->deactivate();
		}

		thim_core_redirect( admin_url( 'plugins.php' ) );
	}

	/**
	 * Add sub page.
	 *
	 * @since 0.8.5
	 *
	 * @param $sub_pages
	 *
	 * @return mixed
	 */
	public function add_sub_page( $sub_pages ) {
		$sub_pages['plugins'] = array(
			'title' => __( 'Plugins', 'thim-core' ),
		);

		return $sub_pages;
	}

	/**
	 * Handle ajax.
	 *
	 * @since 0.4.0
	 */
	public function handle_ajax() {
		$slug   = isset( $_POST['slug'] ) ? $_POST['slug'] : false;
		$action = isset( $_POST['plugin_action'] ) ? $_POST['plugin_action'] : false;

		$plugins = self::get_all_plugins();
		foreach ( $plugins as $plugin ) {
			if ( $plugin['slug'] == $slug ) {
				$result = false;

				$thim_plugin = new Thim_Plugin();
				$thim_plugin->set_args( $plugin );

				$next_action = 'activate';

				switch ( $action ) {
					case 'install':
						$result = $thim_plugin->install();
						break;

					case 'activate':
						$result      = $thim_plugin->activate( null );
						$next_action = 'deactivate';
						break;

					case 'deactivate':
						$result = $thim_plugin->deactivate();
						break;

					case 'update':
						$result      = $thim_plugin->update();
						$next_action = 'reload';
						break;
				}

				if ( $result ) {
					wp_send_json_success( array(
						'messages' => $thim_plugin->get_messages(),
						'action'   => $next_action,
						'text'     => ucfirst( $next_action ),
						'plugin'   => $thim_plugin->get_slug(),
						'version'  => $thim_plugin->get_current_version(),
						'info'     => $thim_plugin->get_info()
					) );
				}

				wp_send_json_error( array(
					'messages' => $thim_plugin->get_messages()
				) );
			}
		}

		wp_send_json_error();
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 0.4.0
	 *
	 * @param $page_now
	 */
	public function enqueue_scripts( $page_now ) {
		if ( ! $this->is_myself() ) {
			return;
		}

		wp_enqueue_script( 'thim-isotope', THIM_CORE_ADMIN_URI . '/assets/js/plugins/isotope.pkgd.min.js', array( 'jquery' ) );
		wp_enqueue_script( 'thim-plugins', THIM_CORE_ADMIN_URI . '/assets/js/plugins/thim-plugins.js', array( 'jquery' ) );
		wp_enqueue_script( 'thim-plugins-manager', THIM_CORE_ADMIN_URI . '/assets/js/plugins/plugins-manager.js', array( 'thim-plugins', 'thim-isotope' ) );

		$this->localize_script();
	}

	/**
	 * Localize script.
	 *
	 * @since 0.4.0
	 */
	public function localize_script() {
		wp_localize_script( 'thim-plugins', 'thim_plugins_manager', array(
			'admin_ajax_action' => admin_url( 'admin-ajax.php?action=thim_plugins_manager' ),
		) );
	}

	/**
	 * Get all plugins.
	 *
	 * @since 0.4.0
	 *
	 * @return array
	 */
	public static function get_all_plugins() {
		if ( self::$all_plugins_require ) {
			return self::$all_plugins_require;
		}

		$plugins = array();

		$plugins = apply_filters( 'thim_core_get_all_plugins_require', $plugins );

		foreach ( $plugins as $index => $plugin ) {
			$plugin = wp_parse_args( $plugin, array(
				'required' => false,
				'add-on'   => false,
				'silent'   => true,
			) );

			$plugins[ $index ] = $plugin;
		}

		uasort( $plugins, function ( $first, $second ) {
			if ( $first['required'] ) {
				return false;
			}

			return true;
		} );

		self::$all_plugins_require = $plugins;

		return self::get_all_plugins();
	}

	/**
	 * Get required plugins inactive or not installed.
	 *
	 * @since 0.8.7
	 *
	 * @return array<Thim_Plugin>  list plugins (array Thim_Plugin)
	 */
	public static function get_required_plugins_inactive() {
		$required_plugins = self::get_all_plugins();

		$plugins = array();
		foreach ( $required_plugins as $plugin ) {
			$thim_plugin = new Thim_Plugin();
			$thim_plugin->set_args( $plugin );

			if ( $thim_plugin->is_active() || $thim_plugin->is_add_on() ) {
				continue;
			}

			$plugins[] = $thim_plugin;
		}

		return $plugins;
	}

	/**
	 * Get all add ons.
	 *
	 * @since 0.8.6
	 *
	 * @return array
	 */
	public static function get_all_add_ons() {
		$all_plugins = self::get_all_plugins();

		$add_ons = array_filter( $all_plugins, function ( $plugin ) {
			if ( isset( $plugin['add-on'] ) && $plugin['add-on'] ) {
				return true;
			}

			return false;
		} );

		return $add_ons;
	}

	/**
	 * Get list slug plugins require all demo.
	 *
	 * @since 0.5.0
	 *
	 * @return array
	 */
	public static function get_slug_plugins_require_all() {
		$all_plugins = self::get_all_plugins();

		$plugins_require_all = array();
		foreach ( $all_plugins as $index => $plugin ) {
			if ( isset( $plugin['required'] ) && $plugin['required'] ) {
				array_push( $plugins_require_all, $plugin['slug'] );
			}
		}

		return $plugins_require_all;
	}

	/**
	 * Get plugin by slug.
	 *
	 * @since 0.5.0
	 *
	 * @param $slug
	 *
	 * @return bool|array
	 */
	public static function get_plugin_by_slug( $slug ) {
		$all_plugins = self::get_all_plugins();

		if ( count( $all_plugins ) === 0 ) {
			return false;
		}

		foreach ( $all_plugins as $plugin ) {
			if ( $plugin['slug'] == $slug ) {
				return $plugin;
			}
		}

		return false;
	}

	/**
	 * Check permission plugins directory.
	 *
	 * @since 0.5.0
	 */
	private static function check_permission() {
		self::$is_writable = wp_is_writable( WP_PLUGIN_DIR );
	}

	/**
	 * Get permission writable plugins directory.
	 *
	 * @since 0.8.2
	 */
	public static function get_permission() {
		if ( is_null( self::$is_writable ) ) {
			self::check_permission();
		}

		return self::$is_writable;
	}

	/**
	 * Notice waring.
	 *
	 * @since 0.5.0
	 */
	private function notification() {
		if ( ! self::get_permission() ) {
			Thim_Dashboard::add_notification( array(
				'content' => '<strong>Important!</strong> Please check permission directory <code>' . WP_PLUGIN_DIR . '</code>. Please follow <a href="http://goo.gl/sKRoXT" target="_blank">the guide</a>.',
				'type'    => 'error'
			) );
		}
	}
}