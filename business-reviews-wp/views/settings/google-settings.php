<?php 

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Google Settings
 */
$options = array(
	'ls_section' => array(
		'title'       => esc_html__( 'Google API Settings', 'business-reviews-wp' ),
		'type'        => 'title',
		'description' => '',
	),
 
	'api_key' => array(
		'title'       => esc_html__( 'Google Place API Key', 'business-reviews-wp' ),  
		'description' => wp_kses( __( 'You can get Google Place API key from <a target="_blank" href="https://www.radiustheme.com/docs/business-reviews/configurations/google-settings/how-to-get-google-places-api-key">here</a>', 'business-reviews-wp' ), array( 'a' => array( 'href' => true, 'target' => true ) ) ),
		'type'        => 'text',  
		'class'       => 'regular-text',  
		'default'     => ''
	),  
	'place_id' => array(
		'title'       => esc_html__( 'Google Place ID', 'business-reviews-wp' ),  
		'description' => wp_kses( __( 'You can get Google place id from <a target="_blank" href="https://www.radiustheme.com/docs/business-reviews/configurations/google-settings/how-to-get-google-places-id">here</a>', 'business-reviews-wp' ), array( 'a' => array( 'href' => true, 'target' => true ) ) ),
		'type'        => 'text',  
		'class'       => 'regular-text',
		'default'     => ''
	), 
	'place_img'       => array(
		'title' => esc_html__( 'Custom Place Image', 'business-reviews-wp' ),
		'type'  => 'image'
	), 
);

return apply_filters( 'rtbr_google_settings_options', $options );