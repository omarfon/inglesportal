<?php

/**
 * Class Thim_Importer_Mapping
 *
 * @since 1.0.1
 */
class Thim_Importer_Mapping extends Thim_Singleton {
	/**
	 * Thim_Importer_Mapping constructor.
	 *
	 * @since 1.0.1
	 */
	protected function __construct() {
		$this->init_hooks();
	}

	/**
	 * Init hooks.
	 *
	 * @since 1.0.1
	 */
	private function init_hooks() {
		add_filter( 'tc_importer_meta_menu_item', array( $this, 'filter_post_meta_menu_item' ), 10, 2 );
	}

	/**
	 * Filter post meta menu item.
	 *
	 * @since 1.0.1
	 *
	 * @param $post_meta
	 * @param $post
	 *
	 * @return mixed
	 */
	public function filter_post_meta_menu_item( $post_meta, $post ) {
		foreach ( $post_meta as $index => $meta ) {
			$key = isset( $meta['key'] ) ? $meta['key'] : false;

			if ( $key != 'tc_mega_menu_content' ) {
				continue;
			}

			$value = ! empty( $meta['value'] ) ? $meta['value'] : false;
			if ( ! $value ) {
				continue;
			}

			$data          = maybe_unserialize( $value );
			$child_widgets = ! empty( $data['widgets'] ) ? $data['widgets'] : false;
			if ( ! is_array( $child_widgets ) ) {
				continue;
			}

			foreach ( $child_widgets as $i => $child_widget ) {
				$new_widget = Thim_Widget_Importer_Service::map_so_settings_widget( $child_widget );

				if ( $new_widget ) {
					$child_widgets[ $i ] = $new_widget;
				}
			}

			$data['widgets']     = $child_widgets;
			$value               = maybe_serialize( $data );
			$meta['value']       = $value;
			$post_meta[ $index ] = $meta;
		}

		return $post_meta;
	}
}