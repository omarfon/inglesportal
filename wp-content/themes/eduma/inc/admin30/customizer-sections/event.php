<?php
/**
 * Panel Event
 * 
 * @package Eduma
 */

thim_customizer()->add_panel(
    array(
        'id'       => 'event',
        'priority' => 45,
        'title'    => esc_html__( 'Event', 'eduma' ),
    )
);