<?php

/**
 * Class Thim_Core_Admin.
 *
 * @package   Thim_Core
 * @since     0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Thim_Core_Admin extends Thim_Singleton {
	/**
	 * Go to theme dashboard.
	 *
	 * @since 0.8.1
	 */
	public static function go_to_theme_dashboard() {
		$link_page = admin_url( '?thim-core-redirect-to-dashboard' );

		thim_core_redirect( $link_page );
	}

	/**
	 * Detect my theme.
	 *
	 * @since 0.8.0
	 *
	 * @return bool
	 */
	public static function is_my_theme() {
		return (bool) get_theme_support( 'thim-core' );
	}

	/**
	 * Thim_Core_Admin constructor.
	 *
	 * @since 0.1.0
	 */
	protected function __construct() {
		spl_autoload_register( array( $this, 'autoload' ) );

		if ( ! self::is_my_theme() ) {
			return;
		}

		$this->init();
		$this->init_hooks();
	}

	/**
	 * Fake page to redirect to dashboard.
	 *
	 * @since 0.8.1
	 */
	public function redirect_to_dashboard() {
		$request = isset( $_REQUEST['thim-core-redirect-to-dashboard'] ) ? true : false;

		if ( ! $request ) {
			return;
		}

		$this->redirect_user();
	}

	/**
	 * Handle redirect the user.
	 *
	 * @since 0.8.5
	 */
	private function redirect_user() {
		if ( Thim_Dashboard::check_first_install() ) {
			$url = Thim_Dashboard::get_link_page_by_slug( 'getting-started' );

			thim_core_redirect( $url );
		}

		thim_core_redirect( Thim_Dashboard::get_link_main_dashboard() );
	}

	/**
	 * Init.
	 *
	 * @since 0.1.0
	 */
	private function init() {
		$this->notice_permission_uploads();
		$this->run();
	}

	/**
	 * Autoload classes.
	 *
	 * @since 0.3.0
	 *
	 * @param $class
	 */
	public function autoload( $class ) {
		$class = strtolower( $class );

		$file_name = 'class-' . str_replace( '_', '-', $class ) . '.php';

		if ( strpos( $class, 'service' ) !== false ) {
			$file_name = 'services/' . $file_name;
		}

		$file = THIM_CORE_ADMIN_PATH . DIRECTORY_SEPARATOR . $file_name;
		if ( is_readable( $file ) ) {
			require_once $file;
		}
	}

	/**
	 * Notice permission uploads.
	 *
	 * @since 0.8.9
	 */
	private function notice_permission_uploads() {
		$dir = WP_CONTENT_DIR;

		$writable = wp_is_writable( $dir );
		if ( $writable ) {
			return;
		}

		Thim_Notification::add_notification(
			array(
				'id'          => 'permission_uploads',
				'type'        => 'error',
				'content'     => __( "<h3>Important!</h3>Your server doesn't not have a permission to write in <strong>WP Uploads</strong> folder ($dir).
									The theme may not work properly with the issue. Please check this <a href='https://goo.gl/guirO5' target='_blank'>guide</a> to fix it.", 'thim-core' ),
				'dismissible' => false,
				'global'      => true,
			)
		);
	}

	/**
	 * Run admin core.
	 *
	 * @since 0.3.0
	 */
	private function run() {
		Thim_Subscribe::instance();
		Thim_Modal::instance();
		Thim_Metabox::instance();
		Thim_Post_Formats::instance();
		Thim_Singular_Settings::instance();
		Thim_Sidebar_Manager::instance();
		Thim_Dashboard::instance();
		Thim_Layout_Builder::instance();
		Thim_Menu_Manager::instance();
		Thim_Importer_Mapping::instance();
	}

	/**
	 * Init hooks.
	 *
	 * @since 0.1.0
	 */
	private function init_hooks() {
		add_action( 'admin_init', array( $this, 'redirect_to_dashboard' ) );
		add_action( 'admin_menu', array( $this, 'remove_unnecessary_menus' ), 999 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( "plugin_action_links_thim-core/thim-core.php", array( $this, 'add_action_links' ) );
		add_action( 'admin_head', array( $this, 'admin_styles' ) );
	}

	/**
	 * Add custom style inline in admin.
	 *
	 * @since 0.9.1
	 */
	public function admin_styles() {
		global $_wp_admin_css_colors;

		$colors = array(
			'#222',
			'#333',
			'#0073aa',
			'#00a0d2',
		);

		$pack = get_user_meta( get_current_user_id(), 'admin_color', true );
		if ( is_array( $_wp_admin_css_colors ) ) {
			foreach ( $_wp_admin_css_colors as $key => $package ) {
				if ( $pack == $key ) {
					$package = (array) $package;
					$colors  = $package['colors'];
				}
			}

		}

		Thim_Template_Helper::render_template( THIM_CORE_ADMIN_PATH . '/views/admin-styles.php', array( 'colors' => $colors, 'key' => $pack ) );
	}

	/**
	 * Remove unnecessary menus.
	 *
	 * @since 0.8.8
	 */
	public function remove_unnecessary_menus() {
		global $submenu;
		unset( $submenu['themes.php'][15] );
		unset( $submenu['themes.php'][20] );
	}

	/**
	 * Add action links.
	 *
	 * @since 0.8.0
	 *
	 * @param $links
	 *
	 * @return array
	 */
	public function add_action_links( $links ) {
		$links[] = '<a href="https://thimpress.com/forums/" target="_blank">' . __( 'Support', 'thim-core' ) . '</a>';
		$links[] = '<a href="' . esc_url( THIM_CORE_URI . '/changelog.html' ) . '" target="_blank">' . __( 'Changelog', 'thim-core' ) . '</a>';

		return $links;
	}

	/**
	 * Enqueue scripts.
	 *
	 * @param string $page_now
	 *
	 * @since 0.2.1
	 */
	public function enqueue_scripts( $page_now ) {
		wp_register_script( 'sentry.io', 'https://cdn.ravenjs.com/3.12.1/raven.min.js', array(), '3.12.1' );
		wp_register_script( 'youtube-api', 'https://www.youtube.com/iframe_api', array() );
		wp_register_script( 'thim-video-youtube', THIM_CORE_ADMIN_URI . '/assets/js/youtube.js', array( 'jquery', 'youtube-api' ), THIM_CORE_VERSION );

		wp_enqueue_style( 'thim-admin', THIM_CORE_ADMIN_URI . '/assets/css/admin.css', array(), THIM_CORE_VERSION );
		wp_enqueue_style( 'thim-icomoon', THIM_CORE_ADMIN_URI . '/assets/css/icomoon.css', array(), THIM_CORE_VERSION );

		if ( is_rtl() ) {
			wp_enqueue_style( 'thim-rtl-admin', THIM_CORE_ADMIN_URI . '/assets/css/rtl.css', array(), THIM_CORE_VERSION );
		}
	}
}

/**
 * Thim Core Admin init.
 *
 * @since 0.8.1
 */
function thim_core_admin_init() {
	Thim_Core_Admin::instance();
}

add_action( 'after_setup_theme', 'thim_core_admin_init', 99999 );

/**
 * Include functions.
 *
 * @since 0.1.0
 */
include_once THIM_CORE_ADMIN_PATH . '/functions.php';