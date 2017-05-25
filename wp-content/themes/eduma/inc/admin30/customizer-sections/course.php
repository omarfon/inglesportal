<?php
/**
 * Panel Course
 * 
 * @package Eduma
 */

thim_customizer()->add_panel(
    array(
        'id'       => 'course',
        'priority' => 43,
        'title'    => esc_html__( 'Course', 'eduma' ),
    )
);