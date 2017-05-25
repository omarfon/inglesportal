<?php

class Thim_Remote_Helper {

	/**
	 * Download file.
	 *
	 * @since 1.0.0
	 *
	 * @param $url
	 * @param $path
	 *
	 * @return bool|WP_Error
	 */
	public static function download( $url, $path ) {
		$code = 'tc_remote_download';

		$response      = wp_remote_get( $url );
		$response_code = wp_remote_retrieve_response_code( $response );

		if ( $response_code != 200 ) {
			$message = wp_remote_retrieve_response_message( $response );

			return new WP_Error( $code, "Download failed: <pre>$message</pre>" );
		}

		$body = wp_remote_retrieve_body( $response );

		return Thim_File_Helper::write( $path, $body );
	}
}