<?php

/**
 * Class Thim_Envato_Service.
 *
 * @since 0.7.0
 */
class Thim_Envato_Service {
	private static $base_api_url = 'http://core.thimpress.com/core-envato-api';

	/**
	 * Get API url.
	 *
	 * @since 0.8.9
	 *
	 * @param $path
	 *
	 * @return string
	 */
	private static function get_api_url( $path ) {
		return sprintf( '%s/%s', self::$base_api_url, $path );
	}

	/**
	 * Get access token from refresh token.
	 *
	 * @since 0.8.9
	 *
	 * @param $refresh_token
	 *
	 * @return string|bool
	 */
	public static function get_access_token( $refresh_token ) {
		if ( empty( $refresh_token ) ) {
			return false;
		}

		$url      = self::get_api_url( "token?refresh_token=$refresh_token" );
		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body   = wp_remote_retrieve_body( $response );
		$object = json_decode( $body );
		$arr    = (array) $object;

		return isset( $arr['data'] ) ? $arr['data'] : false;
	}

	/**
	 * Verify by token.
	 *
	 * @since 0.7.0
	 *
	 * @param $token
	 * @param string $theme_name
	 *
	 * @return bool|WP_Error|array return array if success.
	 */
	public static function verify_by_token( $token, $theme_name = '' ) {
		if ( empty( $token ) ) {
			return false;
		}

		$url = 'https://api.envato.com/v3/market/buyer/list-purchases?filter_by=wordpress-themes';

		$response = self::request( $url, $token );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$themes = isset( $response['results'] ) ? $response['results'] : false;
		if ( empty( $themes ) ) {
			return new WP_Error( 'empty', __( 'No item. Please check again at <a href="https://themeforest.net/downloads" target="_blank">themeforest.net</a>', 'thim-core' ) );
		}

		foreach ( $themes as $theme ) {
			$theme_data = $theme['item']['wordpress_theme_metadata'];

			$theme_name_ = $theme_data['theme_name'];

			if ( strtolower( $theme_name ) == strtolower( $theme_name_ ) ) {
				$theme_id = $theme['item']['id'];

				return array(
					'id'       => $theme_id,
					'metadata' => $theme_data
				);
			}
		}

		return new WP_Error( 'empty', __( 'No matching item. Please check again at <a href="https://themeforest.net/downloads" target="_blank">themeforest.net</a>', 'thim-core' ) );
	}

	/**
	 * Get url file theme (zip file).
	 *
	 * @since 0.7.0
	 *
	 * @param $item_id
	 * @param $token
	 * @param $refresh_token
	 *
	 * @return bool|string
	 */
	public static function get_url_download_item( $item_id, $token, $refresh_token = '' ) {
		$url_api = 'https://api.envato.com/v3/market/buyer/download?item_id=' . $item_id;

		$response = self::request( $url_api, $token, $refresh_token );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		return $url_theme = ! empty( $response['wordpress_theme'] ) ? $response['wordpress_theme'] : false;
	}

	/**
	 * Get theme metadata.
	 *
	 * @since 0.7.0
	 *
	 * @param $id
	 * @param $token
	 * @param $refresh_token
	 *
	 * @return bool
	 * @throws Exception
	 */
	public static function get_theme_metadata( $id, $token, $refresh_token = '' ) {
		$url_api = 'https://api.envato.com/v3/market/catalog/item?id=' . $id;

		$response = self::request( $url_api, $token, $refresh_token );

		if ( is_wp_error( $response ) ) {
			throw new Exception( $response->get_error_message(), 401 );
		}

		$detect_error = ! empty( $response['error'] ) ? true : false;
		if ( $detect_error ) {
			throw new Exception( __( 'Some thing went wrong!', 'thim-core' ), 404 );
		}

		$metadata = isset( $response['wordpress_theme_metadata'] ) ? $response['wordpress_theme_metadata'] : false;

		return $metadata;
	}

	/**
	 * Request to Envato API.
	 *
	 * @since 0.7.0
	 *
	 * @param $url
	 * @param $token
	 * @param $refresh_token
	 *
	 * @return array|WP_Error
	 */
	private static function request( $url, $token, $refresh_token = '' ) {
		if ( empty( $token ) ) {
			return new WP_Error( 'api_token_error', __( 'An API token is required.', 'thim-core' ) );
		}

		$args = array(
			'headers' => array(
				'Authorization' => "Bearer $token",
			),
			'timeout' => 20,
		);

		$response = wp_remote_get( esc_url_raw( $url ), $args );

		$response_code = wp_remote_retrieve_response_code( $response );
		$return        = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $response_code ) {
			if ( 403 == $response_code ) {//Retake access token by refresh token if it expired
				$access_token = self::get_access_token( $refresh_token );

				if ( ! empty( $access_token ) ) {
					return self::request( $url, $access_token );
				}
			}

			if ( null === $return || empty( $return['error_description'] ) ) {
				return new WP_Error( 'api_error', __( 'An unknown API error occurred.', 'thim-core' ) );
			}

			return new WP_Error( $response_code, $return['error_description'] );
		} else {
			$return = json_decode( wp_remote_retrieve_body( $response ), true );

			return $return;
		}
	}
}