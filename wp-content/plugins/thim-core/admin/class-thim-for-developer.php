<?php

/**
 * Class Thim_For_Developer.
 *
 * @since 0.4.0
 */
class Thim_For_Developer extends Thim_Admin_Sub_Page {
	public $key_page = 'developer';

	/**
	 * @var string
	 *
	 * @since 0.5.0
	 */
	public static $page_key = 'developer';

	/**
	 * @var string
	 *
	 * @since 0.5.0
	 */
	private static $action_ajax = 'thim-developer';

	/**
	 * Get url ajax.
	 *
	 * @since 0.5.0
	 */
	public static function get_url_ajax() {
		return admin_url( 'admin-ajax.php?action=' . self::$action_ajax );
	}

	/**
	 * Get url download.
	 *
	 * @since 0.5.0
	 *
	 * @param $package
	 *
	 * @return string
	 */
	public static function get_url_download( $package ) {
		$url_ajax         = self::get_url_ajax();
		$url_ajax_package = add_query_arg( 'package', $package, $url_ajax );

		return $url_ajax_package;
	}

	/**
	 * Thim_For_Developer constructor.
	 *
	 * @since 0.5.0
	 */
	protected function __construct() {
		if ( ! TP::is_debug() ) {
			return;
		}

		parent::__construct();

		$this->init_hooks();
	}

	/**
	 * Init hooks.
	 *
	 * @since 0.5.0
	 */
	private function init_hooks() {
		add_action( 'wp_ajax_' . self::$action_ajax, array( $this, 'handle_ajax' ) );
		add_filter( 'thim_dashboard_sub_pages', array( $this, 'add_sub_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_filter( 'export_wp_filename', function () {
			return 'content.xml';
		} );
	}

	/**
	 * Rename file name content export.
	 *
	 * @since 1.0.0
	 *
	 * @param $file_name
	 *
	 * @return string
	 */
	public function rename_content_xml( $file_name ) {
		if ( isset( $_GET['thim_export'] ) ) {
			return 'content.xml';
		}

		return $file_name;
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 0.8.9
	 */
	public function enqueue_scripts() {
		if ( ! $this->is_myself() ) {
			return;
		}

		add_thickbox();
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
		$sub_pages['developer'] = array(
			'title' => __( 'For Developers', 'thim-core' )
		);

		return $sub_pages;
	}

	/**
	 * Handle ajax.
	 *
	 * @since 0.5.0
	 */
	public function handle_ajax() {
		$package = isset( $_REQUEST['package'] ) ? $_REQUEST['package'] : false;

		if ( ! $package ) {
			return;
		}

		switch ( $package ) {
			case 'content':
				Thim_Export_Service::content();
				break;

			case 'settings':
				Thim_Export_Service::settings();
				break;

			case 'theme_options':
				Thim_Export_Service::theme_options();
				break;

			case 'php_info':
				$this->show_php_info();
				break;

			default:
				wp_die( __( 'Package not found!', 'thim-core' ) );
		}
	}

	/**
	 * Show php information.
	 *
	 * @since 0.8.9
	 */
	private function show_php_info() {
		phpinfo();
		wp_die();
	}
}