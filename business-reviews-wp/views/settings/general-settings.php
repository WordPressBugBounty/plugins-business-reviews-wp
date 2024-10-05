<?php 

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * General Settings
 */
$options = array(
	'ls_section' => array(
		'title'       => esc_html__( 'General Settings', 'business-reviews-wp' ),
		'type'        => 'title',
		'description' => '',
	), 
	'save_into_database'  => array(
        'title'   => esc_html__('Save Data To Database', 'business-reviews-wp'),
        'type'    => 'checkbox',
        'default' => 'yes',
        'label'   => esc_html__('Uncheck to get always live data', 'business-reviews-wp')
    ),
	'refresh_review' => array(
		'title'   => esc_html__( 'Auto Refresh Reviews', 'business-reviews-wp' ),
		'description'   => esc_html__( 'Auto refresh reviews every', 'business-reviews-wp' ),
		'type'    => 'select',
		'class'   => 'rt-select2',
		'default' => 24,
		'options' => array( 
			6 => esc_html__( '6 Hours', 'business-reviews-wp' ),
			12 => esc_html__( '12 Hours', 'business-reviews-wp' ),
			24 => esc_html__( '1 Day', 'business-reviews-wp' ),  
			168 => esc_html__( '1 Week', 'business-reviews-wp' ),
			360 => esc_html__( '15 Days', 'business-reviews-wp' ),
			720 => esc_html__( '1 Month', 'business-reviews-wp' ),
			4392 => esc_html__( '6 Months', 'business-reviews-wp' ),
			8760 => esc_html__( '1 Year', 'business-reviews-wp' ),
		)
	), 
); 
return apply_filters( 'rtbr_general_settings_options', $options );