<?php

require THIM_DIR . 'inc/admin30/require-thim-core.php';

if ( !thim_plugin_active( 'thim-framework/tp-framework.php' ) ) {
	if ( thim_plugin_active( 'thim-core/thim-core.php' ) ) {
		require THIM_DIR . 'inc/admin30/plugins-require.php';
		require THIM_DIR . 'inc/admin30/customizer-options.php';
		require THIM_DIR . 'inc/widgets/widgets.php';
	}
} else {
	return;
}

require THIM_DIR . 'inc/libs/Tax-meta-class/Tax-meta-class.php';
require THIM_DIR . 'inc/tax-meta.php';


/**
 * Compile Sass from theme customize.
 */
add_filter( 'thim_core_config_sass', 'thim_theme_options_sass' );

function thim_theme_options_sass() {
	$dir = THIM_DIR . 'assets/sass/';

	return array(
		'dir'  => $dir,
		'name' => '_style-options.scss',
	);
}

//Filter meta-box
add_filter( 'thim_metabox_display_settings', 'thim_add_metabox_settings', 100, 2 );
if ( !function_exists( 'thim_add_metabox_settings' ) ) {
	function thim_add_metabox_settings( $meta_box, $prefix ) {
		$prefix = 'thim_mtb_';
		$meta_box['tabs']['related'] = array(
			'label' => __( 'Related posts', 'eduma' ),
		);
		$meta_box['tabs']            = array(
			'title'   => array(
				'label' => __( 'Featured Title Area', 'eduma' ),
				'icon'  => 'dashicons-admin-appearance',
			),
			'layout'  => array(
				'label' => __( 'Layout', 'eduma' ),
				'icon'  => 'dashicons-align-left',
			),
		);

		$meta_box['fields'] = array(
			/**
			 * Custom Title and Subtitle.
			 */
			array(
				'name' => __( 'Custom Title and Subtitle', 'thim-core' ),
				'id'   => $prefix . 'using_custom_heading',
				'type' => 'checkbox',
				'std'  => false,
				'tab'  => 'title',
			),
			array(
				'name' => __( 'Hide Title and Subtitle', 'thim-core' ),
				'id'   => $prefix . 'hide_title_and_subtitle',
				'type' => 'checkbox',
				'std'  => false,
				'hidden' => array( $prefix . 'using_custom_heading', '!=', true ),
				'tab'    => 'title',
			),
			array(
				'name'   => __( 'Custom Title', 'thim-core' ),
				'id'     => $prefix . 'custom_title',
				'type'   => 'text',
				'desc'   => __( 'Leave empty to use post title', 'thim-core' ),
				'hidden' => array( $prefix . 'using_custom_heading', '!=', true ),
				'tab'    => 'title',
			),
			array(
				'name'   => __( 'Color Title', 'thim-core' ),
				'id'     => $prefix . 'text_color',
				'type'   => 'color',
				'hidden' => array( $prefix . 'using_custom_heading', '!=', true ),
				'tab'    => 'title',
			),
			array(
				'name'   => __( 'Subtitle', 'thim-core' ),
				'id'     => 'thim_subtitle',
				'type'   => 'text',
				'hidden' => array( $prefix . 'using_custom_heading', '!=', true ),
				'tab'    => 'title',
			),
			array(
				'name'   => __( 'Color Subtitle', 'thim-core' ),
				'id'     => $prefix . 'color_sub_title',
				'type'   => 'color',
				'hidden' => array( $prefix . 'using_custom_heading', '!=', true ),
				'tab'    => 'title',
			),
			array(
				'name' => __( 'Hide Breadcrumbs', 'thim-core' ),
				'id'   => $prefix . 'hide_breadcrumbs',
				'type' => 'checkbox',
				'std'  => false,
				'hidden' => array( $prefix . 'using_custom_heading', '!=', true ),
				'tab'    => 'title',
			),

			array(
				'name'             => __( 'Background Image', 'thim-core' ),
				'id'               => $prefix . 'top_image',
				'type'             => 'image_advanced',
				'max_file_uploads' => 1,
				'tab'    => 'title',
				'hidden' => array( $prefix . 'using_custom_heading', '!=', true ),
			),
			array(
				'name' => __( 'Background color', 'thim-core' ),
				'id'   => $prefix . 'bg_color',
				'type' => 'color',
				'hidden' => array( $prefix . 'using_custom_heading', '!=', true ),
				'tab'    => 'title',
			),
			array(
				'name'    => __( 'Background color opacity', 'thim-core' ),
				'id'      => $prefix . 'bg_opacity',
				'type'    => 'text',
				'default' => 1,
				'hidden' => array( $prefix . 'using_custom_heading', '!=', true ),
				'tab'    => 'title',
			),

			/**
			 * Custom layout
			 */
			array(
				'name' => __( 'Use Custom Layout', 'thim-core' ),
				'id'   => $prefix . 'custom_layout',
				'type' => 'checkbox',
				'tab'  => 'layout',
				'std'  => false,
			),
			array(
				'name'    => __( 'Select Layout', 'thim-core' ),
				'id'      => $prefix . 'layout',
				'type'    => 'image_select',
				'options' => array(
					'sidebar-left'  => THIM_URI . 'images/layout/sidebar-left.jpg',
					'full-content'    => THIM_URI . 'images/layout/body-full.jpg',
					'sidebar-right' => THIM_URI . 'images/layout/sidebar-right.jpg',
				),
				'default' => 'sidebar-right',
				'tab'     => 'layout',
				'hidden'  => array( $prefix . 'custom_layout', '=', false ),
			),
			array(
				'name' => __( 'No Padding Content', 'thim-core' ),
				'id'   => $prefix . 'no_padding',
				'type' => 'checkbox',
				'std'  => false,
				'tab'  => 'layout',
			),
		);

		return $meta_box;
	}
}