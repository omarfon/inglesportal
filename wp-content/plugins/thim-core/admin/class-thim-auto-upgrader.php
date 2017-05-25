<?php

/**
 * Class Thim_Theme_Upgrader.
 *
 * @since 0.9.0
 */
class Thim_Auto_Upgrader extends Thim_Singleton {
	/**
	 * Thim_Theme_Upgrader constructor.
	 *
	 * @since 0.9.0
	 */
	protected function __construct() {
		$this->init_hooks();
	}

	/**
	 * Init hooks.
	 *
	 * @since 0.9.0
	 */
	private function init_hooks() {
		add_filter( 'http_request_args', array( $this, 'exclude_check_update_from_wp_org' ), 5, 2 );

		add_filter( 'pre_set_site_transient_update_themes', array( $this, 'inject_update_themes' ) );
		add_filter( 'pre_set_transient_update_themes', array( $this, 'inject_update_themes' ) );

		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'inject_update_plugins' ) );
		add_filter( 'pre_set_transient_update_plugins', array( $this, 'inject_update_plugins' ) );

		add_filter( 'upgrader_package_options', array( $this, 'pre_update_theme' ) );
	}

	/**
	 * Pre update theme, get again link download theme.
	 *
	 * @since 0.8.0
	 *
	 * @param $options
	 *
	 * @return mixed
	 */
	public function pre_update_theme( $options ) {
		$hook_extra = isset( $options['hook_extra'] ) ? $options['hook_extra'] : false;

		if ( ! $hook_extra ) {
			return $options;
		}

		$theme = isset( $hook_extra['theme'] ) ? $hook_extra['theme'] : false;

		if ( ! $theme ) {
			return $options;
		}

		$themes = Thim_Product_Registration::get_themes();
		foreach ( $themes as $stylesheet => $data ) {
			if ( $theme == $stylesheet ) {
				$options['package'] = $x = Thim_Product_Registration::_get_url_download_theme( $stylesheet );

				return $options;
			}
		}

		return $options;
	}

	/**
	 * Add filter update plugins.
	 *
	 * @since 1.0.0
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	public function inject_update_plugins( $value ) {
		if ( ! TP::is_active_network() ) {
			return $value;
		}

		$response = ! empty( $value->response ) ? $value->response : array();

		$plugins = Thim_Plugins_Manager::get_all_plugins();

		foreach ( $plugins as $index => $plugin ) {
			$thim_plugin = new Thim_Plugin();
			$thim_plugin->set_args( $plugin );

			if ( $thim_plugin->is_wporg() ) {
				continue;
			}

			if ( ! $thim_plugin->can_update() ) {
				continue;
			}

			$plugin_file = $thim_plugin->get_plugin_file();

			$object              = new stdClass();
			$object->slug        = $thim_plugin->get_slug();
			$object->plugin      = $plugin_file;
			$object->new_version = $thim_plugin->get_require_version();
			$object->url         = $thim_plugin->get_url();
			$object->package     = $thim_plugin->get_source();

			$response[ $plugin_file ] = $object;
		}

		$value->response = $response;

		return $value;
	}

	/**
	 * Add filter update theme.
	 *
	 * @since 0.7.0
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	public function inject_update_themes( $value ) {
		if ( ! TP::is_active_network() ) {
			return $value;
		}

		$themes = Thim_Product_Registration::get_themes();
		foreach ( $themes as $stylesheet => $theme ) {
			if ( Thim_Product_Registration::can_update( $stylesheet ) ) {
				$value->response[ $stylesheet ] = array(
					'theme'       => $stylesheet,
					'new_version' => Thim_Theme_Manager::get_latest_version( $stylesheet ),
					'url'         => 'https://thimpress.com/forums/',
					'package'     => Thim_Product_Registration::_get_fake_url_download_theme(),
				);
			} else {
				if ( isset( $value->response[ $stylesheet ] ) ) {
					unset( $value->response[ $stylesheet ] );
				}
			}
		}

		return $value;
	}

	/**
	 * Schedule check update theme.
	 *
	 * @since 0.8.1
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	public function schedule_check_update( $value ) {
		if ( ! Thim_Product_Registration::is_active() ) {
			return $value;
		}

		$stylesheet = Thim_Product_Registration::get_stylesheet_can_update();
		if ( $stylesheet && ! empty( $value->response[ $stylesheet ] ) ) {
			return $value;
		}

		Thim_Product_Registration::check_update();

		$stylesheet                     = Thim_Product_Registration::get_stylesheet_can_update();
		$value->response[ $stylesheet ] = array(
			'theme'       => $stylesheet,
			'new_version' => Thim_Theme_Manager::get_latest_version( $stylesheet ),
			'url'         => 'https://thimpress.com/forums/',
			'package'     => Thim_Product_Registration::_get_fake_url_download_theme(),
		);

		return $value;
	}

	/**
	 * Exclude check theme update from wp.org.
	 *
	 * @since 0.9.0
	 *
	 * @param $request
	 * @param $url
	 *
	 * @return mixed
	 */
	public function exclude_check_update_from_wp_org( $request, $url ) {
		if ( false === strpos( $url, '//api.wordpress.org/themes/update-check/1.1/' ) ) {
			return $request;
		}

		$data   = json_decode( $request['body']['themes'] );
		$themes = Thim_Product_Registration::get_themes();
		foreach ( $themes as $slug => $data ) {
			if ( isset( $data->themes->$slug ) ) {
				unset( $data->themes->$slug );
			}
		}

		$request['body']['themes'] = wp_json_encode( $data );

		return $request;
	}
}