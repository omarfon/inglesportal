<?php
/**
 * Panel Product
 * 
 * @package Eduma
 */

thim_customizer()->add_panel(
    array(
        'id'       => 'product',
        'priority' => 44,
        'title'    => esc_html__( 'Product', 'eduma' ),
    )
);