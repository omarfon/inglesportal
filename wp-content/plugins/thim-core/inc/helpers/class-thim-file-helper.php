<?php

/**
 * Class Thim_File_Helper.
 *
 * @since 1.0.1
 */
class Thim_File_Helper {
	/**
	 * Call $wp_filesystem
	 *
	 * @since 1.0.1
	 */
	private static function call_wp_file_system() {
		/**
		 * Call $wp_filesystem
		 */
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}
	}

	/**
	 * Remove file or directory.
	 *
	 * @since 1.0.1
	 *
	 * @param $path
	 *
	 * @return bool
	 */
	public static function remove_file( $path ) {
		self::call_wp_file_system();
		global $wp_filesystem;


		$wp_filesystem->delete( $path );

		return true;
	}

	/**
	 * Put file content.
	 *
	 * @since 1.0.1
	 *
	 * @param $dir
	 * @param $file_name
	 * @param $content
	 *
	 * @return bool|WP_Error
	 */
	public static function put_file( $dir, $file_name, $content ) {
		self::call_wp_file_system();
		global $wp_filesystem;

		/**
		 * Directory didn't exist, so let's create it.
		 */
		if ( ! $wp_filesystem->is_dir( $dir ) ) {
			$wp_filesystem->mkdir( $dir );
		}

		if ( ! wp_is_writable( $dir ) ) {
			return new WP_Error( self::$tag, 'Can not write in directory ' . $dir );
		}

		$put_file = $wp_filesystem->put_contents(
			trailingslashit( $dir ) . $file_name,
			$content,
			FS_CHMOD_FILE
		);

		if ( ! $put_file ) {
			return new WP_Error( self::$tag, 'Put file error!' );
		}

		return true;
	}

	/**
	 * Remove a directory with sub paths and files
	 *
	 * @since 1.0.0
	 *
	 * @param $dir
	 *
	 * @return bool
	 */
	public static function rmdir( $dir ) {
		if ( ! file_exists( $dir ) ) {
			return true;
		}

		if ( ! is_dir( $dir ) ) {
			return unlink( $dir );
		}

		foreach ( scandir( $dir ) as $item ) {
			if ( $item == '.' || $item == '..' ) {
				continue;
			}
			if ( ! Thim_File_Helper::rmdir( $dir . DIRECTORY_SEPARATOR . $item ) ) {
				return false;
			}
		}

		return rmdir( $dir );
	}

	/**
	 * Recursive directory creation based on full path.
	 *
	 * @since 1.0.0
	 *
	 * @param $dir
	 *
	 * @return bool
	 */
	public static function mkdir( $dir ) {
		return wp_mkdir_p( $dir );
	}

	/**
	 * Write file.
	 *
	 * @since 1.0.0
	 *
	 * @param $path
	 * @param $content
	 *
	 * @return bool|WP_Error
	 */
	public static function write( $path, $content ) {
		$out_fp = fopen( $path, 'w' );
		if ( ! $out_fp ) {
			return new WP_Error( self::$tag, __( 'Can not save file', 'thim-core' ) );
		}

		fwrite( $out_fp, $content );
		fclose( $out_fp );
		clearstatcache();

		return true;
	}
}