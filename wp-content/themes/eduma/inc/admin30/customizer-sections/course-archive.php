<?php
/**
 * Section Course Archive
 *
 * @package Eduma
 */

thim_customizer()->add_section(
	array(
		'id'       => 'course_archive',
		'panel'    => 'course',
		'title'    => esc_html__( 'Archive Settings', 'eduma' ),
		'priority' => 10,
	)
);

thim_customizer()->add_field(
	array(
		'id'       => 'thim_learnpress_cate_layout',
		'type'     => 'radio-image',
		'label'    => esc_html__( 'Blog Archive Layouts', 'eduma' ),
		'tooltip'  => esc_html__( 'Allows you to choose a layout to display for all course archive, course category page on your site.', 'eduma' ),
		'section'  => 'course_archive',
		'priority' => 12,
		'default'  => 'sidebar-right',
		'choices'  => array(
			'sidebar-left'  => THIM_URI . 'images/layout/sidebar-left.jpg',
			'full-content'    => THIM_URI . 'images/layout/body-full.jpg',
			'sidebar-right' => THIM_URI . 'images/layout/sidebar-right.jpg',
		),
	)
);

thim_customizer()->add_field(
	array(
		'type'     => 'text',
		'id'       => 'thim_learnpress_cate_sub_title',
		'label'    => esc_html__( 'Sub Heading', 'eduma' ),
		'tooltip'  => esc_html__( 'Allows you can setup sub heading for course archive.', 'eduma' ),
		'section'  => 'course_archive',
		'priority' => 15,
	)
);

thim_customizer()->add_field(
	array(
		'type'     => 'select',
		'id'       => 'thim_learnpress_cate_grid_column',
		'label'    => esc_html__( 'Grid Columns', 'eduma' ),
		'tooltip'  => esc_html__( 'Choose the number of columns for archive course.', 'eduma' ),
		'default'  => '3',
		'priority' => 15,
		'multiple' => 0,
		'section'  => 'course_archive',
		'choices'  => array(
			'2' => esc_html__( '2', 'eduma' ),
			'3' => esc_html__( '3', 'eduma' ),
			'4' => esc_html__( '4', 'eduma' ),
		),
	)
);

thim_customizer()->add_field(
	array(
		'type'      => 'image',
		'id'        => 'thim_learnpress_cate_top_image',
		'label'     => esc_html__( 'Top Image', 'eduma' ),
		'priority'  => 30,
		'transport' => 'postMessage',
		'section'  => 'course_archive',
		'default'     => THIM_URI . "images/bg-page.jpg",
	)
);

// Page Title Background Color
thim_customizer()->add_field(
	array(
		'id'          => 'thim_learnpress_cate_bg_color',
		'type'        => 'color',
		'label'       => esc_html__( 'Background Color', 'eduma' ),
		'tooltip'     => esc_html__( 'If you do not use background image, then can use background color for page title on heading top. ', 'eduma' ),
		'section'     => 'course_archive',
		'default'     => 'rgba(0,0,0,0.5)',
		'priority'    => 35,
		'alpha'       => true,
		'transport' => 'postMessage',
		'js_vars'   => array(
			array(
				'choice'   => 'color',
				'element'  => '.top_site_main>.overlay-top-header',
				'property' => 'background',
			)
		)
	)
);

thim_customizer()->add_field(
	array(
		'id'          => 'thim_learnpress_cate_title_color',
		'type'        => 'color',
		'label'       => esc_html__( 'Title Color', 'eduma' ),
		'tooltip'     => esc_html__( 'Allows you can select a color make text color for title.', 'eduma' ),
		'section'     => 'course_archive',
		'default'     => '#ffffff',
		'priority'    => 40,
		'alpha'       => true,
		'transport' => 'postMessage',
		'js_vars'   => array(
			array(
				'choice'   => 'color',
				'element'  => '.top_site_main h1, .top_site_main h2',
				'property' => 'color',
			)
		)
	)
);

thim_customizer()->add_field(
	array(
		'id'          => 'thim_learnpress_cate_sub_title_color',
		'type'        => 'color',
		'label'       => esc_html__( 'Sub Title Color', 'eduma' ),
		'tooltip'     => esc_html__( 'Allows you can select a color make sub title color page title.', 'eduma' ),
		'section'     => 'course_archive',
		'default'     => '#999',
		'priority'    => 45,
		'alpha'       => true,
		'transport' => 'postMessage',
		'js_vars'   => array(
			array(
				'choice'   => 'color',
				'element'  => '.top_site_main .banner-description',
				'property' => 'color',
			)
		)
	)
);