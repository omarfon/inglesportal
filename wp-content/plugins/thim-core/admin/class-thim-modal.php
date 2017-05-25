<?php

/**
 * Class Thim_Modal.
 *
 * @since 0.9.0
 */
class Thim_Modal extends Thim_Singleton {
	/**
	 * Thim_Modal constructor.
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
		add_action( 'admin_footer', array( $this, 'add_iframe_template' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 0.9.1
	 */
	public function enqueue_scripts() {
		wp_register_script( 'thim-modal', THIM_CORE_ADMIN_URI . '/assets/js/modals/modal.js', array( 'jquery', 'backbone' ), THIM_CORE_VERSION );
	}

	/**
	 * Add iframe template.
	 *
	 * @since 0.9.0
	 */
	public function add_iframe_template() {
		$file = THIM_CORE_ADMIN_PATH . '/views/modals/iframe.php';
		Thim_Template_Helper::render_template( $file );
	}

	/**
	 * Add modal.
	 *
	 * @since 0.9.0
	 *
	 * @param array $args
	 *
	 * @return bool
	 */
	public static function render_modal( $args ) {
		$args = wp_parse_args( $args, array(
			'template' => '',
			'id'       => '',
		) );

		$html = Thim_Template_Helper::get_template( $args['template'], $args );
		if ( ! $html ) {
			return false;
		}
		$args['html'] = $html;
		$dir_path     = THIM_CORE_ADMIN_PATH . '/views/modals/';
		$file         = $dir_path . 'master.php';

		return Thim_Template_Helper::render_template( $file, $args );
	}

	/**
	 * Enqueue script thim modal. You need call this function if you want to add modal.
	 *
	 * @since 0.9.0
	 */
	public static function enqueue_modal() {
		wp_enqueue_script( 'thim-modal' );
	}
}