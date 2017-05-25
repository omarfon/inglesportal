<?php

/**
 * Class Thim_Product_Registration.
 *
 * @package   Thim_Core
 * @since     0.2.1
 */
class Thim_Product_Registration extends Thim_Singleton {
	/**
	 * @since 0.2.1
	 *
	 * @var string
	 */
	public static $key_callback_request = 'tc_callback_registration';

	/**
	 * Premium themes.
	 *
	 * @since 0.9.0
	 *
	 * @var null
	 */
	private static $themes = null;

	/**
	 * Get product registration data.
	 *
	 * @since 0.9.0
	 *
	 * @return array();
	 */
	public static function get_themes() {
		if ( self::$themes === null ) {
			self::$themes = get_site_option( 'thim_core_product_registration_themes' );
		}

		self::$themes = (array) self::$themes;

		foreach ( self::$themes as $key => $theme ) {
			if ( is_numeric( $key ) ) {
				unset( self::$themes[ $key ] );
			}
		}

		return self::$themes;
	}

	/**
	 * Set product registration data.
	 *
	 * @since 0.9.0
	 *
	 * @param array $data
	 */
	public static function set_themes( $data = array() ) {
		self::$themes = $data;

		update_site_option( 'thim_core_product_registration_themes', $data );
	}

	/**
	 * Get registration data by theme.
	 *
	 * @since 0.9.0
	 *
	 * @param $field
	 * @param null $theme
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public static function get_data_by_theme( $field, $default = false, $theme = null ) {
		if ( ! $theme ) {
			$theme = Thim_Theme_Manager::get_current_theme();
		}

		$registration_data = self::get_themes();

		if ( ! $registration_data ) {
			return $default;
		}

		$theme_data = isset( $registration_data[ $theme ] ) ? $registration_data[ $theme ] : false;

		if ( ! $theme_data ) {
			return $default;
		}

		return isset( $theme_data[ $field ] ) ? $theme_data[ $field ] : $default;
	}

	/**
	 * Get filed data by theme.
	 *
	 * @since 0.9.0
	 *
	 * @param $theme
	 * @param $field
	 * @param $value
	 */
	public static function set_data_by_theme( $field, $value, $theme = null ) {
		if ( ! $theme ) {
			$theme = Thim_Theme_Manager::get_current_theme();
		}

		$registration_data = self::get_themes();

		$theme_data           = isset( $registration_data[ $theme ] ) ? $registration_data[ $theme ] : array();
		$theme_data           = (array) $theme_data;
		$theme_data[ $field ] = $value;

		$registration_data[ $theme ] = $theme_data;

		self::set_themes( $registration_data );
	}

	/**
	 * Check update theme from envato.
	 *
	 * @since 0.8.0
	 *
	 * @return bool|WP_Error
	 */
	public static function check_update() {
		if ( wp_installing() ) {
			return false;
		}

		$token         = self::get_token();
		$refresh_token = self::get_refresh_token();
		$item_id       = self::get_item_id();

		try {
			$theme_metadata = Thim_Envato_Service::get_theme_metadata( $item_id, $token, $refresh_token );
			self::set_remote_version( $theme_metadata['version'] );
		} catch ( Exception $exception ) {
			self::destroy_active();

			return new WP_Error( $exception->getCode(), $exception->getMessage() );
		}

		return true;
	}

	/**
	 * Can update?
	 *
	 * @since 0.7.0
	 *
	 * @param $stylesheet
	 *
	 * @return bool
	 */
	public static function can_update( $stylesheet = null ) {
		if ( ! $stylesheet ) {
			$stylesheet = Thim_Theme_Manager::get_current_theme();
		}

		$themes = wp_get_themes();
		foreach ( $themes as $slug => $theme ) {
			if ( $theme->parent() ) {
				continue;
			}

			if ( $slug == $stylesheet ) {
				$local_version  = $theme->get( 'Version' );
				$latest_version = self::get_remote_version( $stylesheet );
				if ( ! $latest_version ) {
					return false;
				}

				return $can_update = version_compare( $latest_version, $local_version ) > 0;
			}
		}

		return false;

	}

	/**
	 * Set can update.
	 *
	 * @since 0.9.0
	 *
	 * @param bool $able
	 */
	private static function set_can_update( $able = true ) {
		self::set_data_by_theme( 'can_update', $able );
	}

	/**
	 * Set remote version.
	 *
	 * @param $version
	 * @param $stylesheet
	 *
	 * @since 0.9.0
	 */
	private static function set_remote_version( $version, $stylesheet = null ) {
		if ( ! $stylesheet ) {
			$stylesheet = Thim_Theme_Manager::get_current_theme();
			Thim_Theme_Manager::update_latest_version( $stylesheet, $version );
		}

		self::set_data_by_theme( 'remote_version', $version, $stylesheet );
	}

	/**
	 * Get remote version.
	 *
	 * @since 0.9.0
	 *
	 * @param $stylesheet
	 *
	 * @return mixed
	 */
	public static function get_remote_version( $stylesheet = null ) {
		return self::get_data_by_theme( 'remote_version', false, $stylesheet );
	}

	/**
	 * Save item id.
	 *
	 * @since 0.7.0
	 *
	 * @param $item_id
	 */
	private static function save_item_id( $item_id ) {
		self::set_data_by_theme( 'envato_item_id', $item_id );
		self::set_time_activation_successful();
	}

	/**
	 * Set time activation successful.
	 *
	 * @since 0.8.0
	 *
	 * @param $time
	 */
	private static function set_time_activation_successful( $time = null ) {
		if ( ! $time ) {
			$time = time();
		}

		self::set_data_by_theme( 'time_activate_successful', $time );
	}

	/**
	 * Set time activation successful.
	 *
	 * @since 0.8.0
	 *
	 * @return int
	 */
	public static function get_time_activation_successful() {
		self::get_data_by_theme( 'time_activate_successful' );

		if ( empty( $time ) ) {
			$time = time();
			self::set_time_activation_successful( $time );
		}

		return (int) $time;
	}

	/**
	 * Get item id.
	 *
	 * @since 0.7.0
	 *
	 * @param $stylesheet
	 *
	 * @return bool|string
	 */
	public static function get_item_id( $stylesheet = null ) {
		$option = self::get_data_by_theme( 'envato_item_id', false, $stylesheet );

		return $option;
	}

	/**
	 * Save personal token.
	 *
	 * @since 0.7.0
	 *
	 * @param $token
	 */
	private static function save_token( $token ) {
		self::set_data_by_theme( 'envato_personal_token', $token );
	}

	/**
	 * Get personal token.
	 *
	 * @since 0.7.0
	 *
	 * @param $stylesheet
	 *
	 * @return bool|string
	 */
	public static function get_token( $stylesheet = null ) {
		$type = self::get_type_activation( $stylesheet );
		if ( $type != 'personal' ) {
			return self::get_access_token( $stylesheet );
		}

		return $option = self::get_data_by_theme( 'envato_personal_token', false, $stylesheet );
	}

	/**
	 * Save refresh token.
	 *
	 * @since 0.7.0
	 *
	 * @param $token
	 */
	private static function save_refresh_token( $token ) {
		self::set_data_by_theme( 'envato_refresh_token', $token );
	}

	/**
	 * Get refresh token.
	 *
	 * @since 0.7.0
	 *
	 * @param $stylesheet
	 *
	 * @return bool|string
	 */
	public static function get_refresh_token( $stylesheet = null ) {
		$option = self::get_data_by_theme( 'envato_refresh_token', false, $stylesheet );

		return $option;
	}

	/**
	 * Save refresh token.
	 *
	 * @since 0.7.0
	 *
	 * @param $token
	 * @param $stylesheet
	 */
	private static function save_access_token( $token, $stylesheet = null ) {
		self::set_data_by_theme( 'envato_access_token', $token, $stylesheet );
	}

	/**
	 * Get refresh token.
	 *
	 * @since 0.7.0
	 *
	 * @param $stylesheet
	 *
	 * @return bool|string
	 */
	public static function get_access_token( $stylesheet = null ) {
		$option = self::get_data_by_theme( 'envato_access_token', false, $stylesheet );

		return $option;
	}

	/**
	 * Set type activation.
	 *
	 * @since 0.8.9
	 *
	 * @param $type
	 */
	private static function set_type_activation( $type ) {
		self::set_data_by_theme( 'envato_type_activation', $type );
	}

	/**
	 * Get type activation.
	 *
	 * @since 0.8.9
	 *
	 * @param $stylesheet
	 *
	 * @return mixed
	 */
	public static function get_type_activation( $stylesheet = null ) {
		$option = self::get_data_by_theme( 'envato_type_activation', 'personal', $stylesheet );

		return $option;
	}

	/**
	 * Get theme stylesheet can update.
	 *
	 * @since 0.8.0
	 *
	 * @return bool|string
	 */
	public static function get_stylesheet_can_update() {
		$option = get_site_option( 'thim_stylesheet_update' );

		if ( empty( $option ) ) {
			return false;
		}

		return $option;
	}

	/**
	 * Get active theme from envato.s
	 *
	 * @since 0.2.1
	 *
	 * @return bool
	 */
	public static function is_active() {
		$activated = self::get_item_id();

		if ( empty( $activated ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Destroy active theme from envato.
	 *
	 * @since 0.8.0
	 */
	public static function destroy_active() {
		self::save_item_id( false );
	}

	/**
	 * Get url auth.
	 *
	 * @since 0.2.1
	 *
	 * @return string
	 */
	public static function get_url_auth() {
		return 'http://core.thimpress.com/?thim_envato_activate=1';
	}

	/**
	 * Get verify callback url.
	 *
	 * @since 0.2.1
	 *
	 * @param $return
	 *
	 * @return string
	 */
	public static function get_url_verify_callback( $return = false ) {
		$url = Thim_Dashboard::get_link_main_dashboard( array(
			self::$key_callback_request => 1
		) );

		if ( $return ) {
			$url = add_query_arg( array( 'return' => urlencode( $return ) ), $url );
		}

		return $url;
	}

	/**
	 * Get link fake download theme from envato.
	 *
	 * @since 0.7.0
	 *
	 * @return string
	 */
	public static function _get_fake_url_download_theme() {
		return 'https://thimpress.com';
	}

	/**
	 * Get url link download theme from envato.
	 *
	 * @since 0.7.0
	 *
	 * @param $stylesheet
	 *
	 * @return bool|string
	 */
	public static function _get_url_download_theme( $stylesheet = null ) {
		$token         = self::get_token( $stylesheet );
		$refresh_token = self::get_refresh_token( $stylesheet );
		$item_id       = self::get_item_id( $stylesheet );

		return Thim_Envato_Service::get_url_download_item( $item_id, $token, $refresh_token );
	}

	/**
	 * Get link review of theme on themeforest.
	 *
	 * @sicne
	 *
	 * @return string
	 */
	public static function get_link_reviews() {
		$link     = 'https://themeforest.net/downloads';
		$theme_id = self::get_item_id();

		if ( ! empty( $theme_id ) ) {
			$link .= sprintf( '#item-%s', $theme_id );
		}

		return $link;
	}

	/**
	 * Get link go to update theme.
	 *
	 * @since 0.8.1
	 *
	 * @return string
	 */
	public static function get_link_go_to_update() {
		if ( TP::is_active_network() ) {
			return network_admin_url( 'update-core.php#update-themes-table' );
		}

		return admin_url( 'themes.php' );
	}

	/**
	 * Thim_Product_Registration constructor.
	 *
	 * @since 0.2.1
	 */
	protected function __construct() {
		$this->init();
		$this->init_hooks();
		$this->upgrader();
	}

	/**
	 * Upgrader.
	 *
	 * @since 0.9.0
	 */
	private function upgrader() {
		Thim_Auto_Upgrader::instance();
	}

	/**
	 * Init.
	 *
	 * @since 0.8.7
	 */
	private function init() {
	}

	/**
	 * Init hooks.
	 *
	 * @since 0.2.1
	 */
	private function init_hooks() {
		add_action( 'admin_init', array( $this, 'handle_callback_verify' ) );
		add_action( 'init', array( $this, 'activate_theme_by_personal_token' ) );
		add_action( 'wp_ajax_thim_check_update', array( $this, 'handle_check_update_theme' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'notice_review_theme' ) );
	}

	/**
	 * Notice review for theme on themeforest.
	 *
	 * @since 0.8.9
	 */
	public function notice_review_theme() {
		if ( ! self::is_active() ) {
			return;
		}

		$start  = self::get_time_activation_successful();
		$now    = time();
		$period = $now - $start;
		if ( $period / 86400 < 7 ) {// If activated great than 7 days then notice
			return;
		}

		$link_review = Thim_Product_Registration::get_link_reviews();

		Thim_Notification::add_notification(
			array(
				'id'          => 'review_theme',
				'type'        => 'success',
				'content'     => sprintf( __( 'If you are happy with this theme, please <a href="%s" target="_blank">leave us a 5-star rating</a> on ThemeForest to support and encourage us.', 'thim-core' ), $link_review ),
				'dismissible' => true,
				'global'      => false,
			)
		);
	}

	/**
	 * Handle ajax check update theme.
	 *
	 * @since 0.7.0
	 */
	public function handle_check_update_theme() {
		if ( ! self::is_active() ) {
			wp_send_json_error();
		}

		$check = self::check_update();

		if ( is_wp_error( $check ) ) {
			wp_send_json_error( $check->get_error_message() );
		}

		if ( ! $check ) {
			wp_send_json_error();
		}

		wp_send_json_success( array(
			'can_update' => self::can_update(),
			'current'    => Thim_Theme_Manager::get_current_version(),
			'latest'     => self::get_remote_version(),
		) );
	}

	/**
	 * Activate theme by personal token.
	 *
	 * @since 0.7.0
	 */
	public function activate_theme_by_personal_token() {
		$detect = isset( $_REQUEST['thim-activate-theme'] ) ? true : false;
		if ( ! $detect ) {
			return;
		}

		$token = ! empty( $_REQUEST['token'] ) ? $_REQUEST['token'] : false;
		if ( ! $token ) {
			return;
		}

		$theme_name = ! empty( $_REQUEST['theme'] ) ? $_REQUEST['theme'] : false;
		if ( ! $theme_name ) {
			return;
		}

		$verify = Thim_Envato_Service::verify_by_token( $token, $theme_name );

		if ( is_wp_error( $verify ) ) {
			Thim_Dashboard::add_notification( array(
				'content' => $verify->get_error_message(),
				'type'    => 'error',
			) );

			return;
		}

		if ( ! $verify ) {
			Thim_Dashboard::add_notification( array(
				'content' => __( 'Verify failed. Please try again or enter another personal token!', 'thim-core' ),
				'type'    => 'error',
			) );

			return;
		}

		$this->save_token( $token );
		$this->save_item_id( $verify['id'] );
		Thim_Dashboard::add_notification( array(
			'content' => __( 'Activate theme success!', 'thim-core' ),
			'type'    => 'success',
		) );
	}

	/**
	 * Handle callback from server verify.
	 *
	 * @since 0.2.1
	 */
	public function handle_callback_verify() {
		$detect_request = ! empty( $_GET[ self::$key_callback_request ] ) ? $_GET[ self::$key_callback_request ] : false;

		if ( ! $detect_request ) {
			return;
		}

		if ( self::is_active() ) {
			return;
		}

		$error = isset( $_GET['error'] ) ? $_GET['error'] : false;
		if ( $error ) {
			$error_description = isset( $_GET['error_description'] ) ? $_GET['error_description'] : false;
			Thim_Notification::add_notification( array(
				'id'      => 'activate_theme',
				'type'    => 'error',
				'content' => $error_description,
			) );

			return;
		}

		$queries = wp_parse_args( $_GET, array(
			'refresh_token' => '',
			'access_token'  => '',
			'item_id'       => '',
			'redirect'      => '',
		) );

		$refresh_token = $queries['refresh_token'];
		$access_token  = $queries['access_token'];
		$item_id       = $queries['item_id'];
		self::save_refresh_token( $refresh_token );
		self::save_access_token( $access_token );
		self::save_item_id( $item_id );
		self::set_type_activation( 'oath' );

		Thim_Notification::add_notification( array(
			'id'      => 'activate_theme',
			'type'    => 'success',
			'content' => __( 'Activate theme successful!', 'thim-core' ),
		) );

		$redirect = $queries['redirect'];
		if ( ! empty( $redirect ) ) {
			thim_core_redirect( $redirect );
		}

		thim_core_redirect( Thim_Dashboard::get_link_main_dashboard() );
	}

	/**
	 * Enqueue scripts.
	 *
	 * @param $page_now
	 *
	 * @since 0.7.0
	 */
	public function enqueue_scripts( $page_now ) {
		if ( strpos( $page_now, Thim_Dashboard::$prefix_slug . 'dashboard' ) === false ) {
			return;
		}

		wp_enqueue_script( 'thim-theme-update', THIM_CORE_ADMIN_URI . '/assets/js/theme-update.js', array( 'jquery' ), THIM_CORE_VERSION );

		$this->_localize_script();
	}

	/**
	 * Localize script.
	 *
	 * @since 0.7.0
	 */
	private function _localize_script() {
		wp_localize_script( 'thim-theme-update', 'thim_theme_update', array(
			'admin_ajax' => admin_url( 'admin-ajax.php?action=thim_check_update' ),
			'i18l'       => array(
				'check_failed'   => __( 'Check update failed!', 'thim-core' ),
				'can_update'     => __( 'Your theme can update, click "Go To Update" to start.', 'thim-core' ),
				'can_not_update' => __( 'Your theme is the latest version.', 'thim-core' ),
				'wrong'          => __( 'Some thing went wrong. Please try again later!', 'thim-core' ),
			)
		) );
	}
}