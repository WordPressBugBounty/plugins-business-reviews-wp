<?php

// use Rtbr\Helpers\Functions; 

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Yelp Settings
 */
$options = array(
	'ls_section' => array(
		'title'       => esc_html__( 'Yelp API Settings', 'business-reviews-wp' ),
		'type'        => 'title',
		'description' => '',
	), 
	'api_key' => array(
		'title'       => esc_html__( 'Yelp API Key', 'business-reviews-wp' ),  
		'description' => wp_kses( __( 'You can get Yelp API key from <a target="_blank" href="https://www.radiustheme.com/docs/business-reviews/configurations/yelp-settings/how-to-create-yelp-api-key">here</a>', 'business-reviews-wp' ), array( 'a' => array( 'href' => true, 'target' => true ) ) ),
		'type'        => 'text',  
		'class'       => 'regular-text',
		'default'     => ''
	),  
	'business_url' => array(
		'title'       => esc_html__( 'Yelp Business URL', 'business-reviews-wp' ), 
		'description' => wp_kses( __( 'You can get Yelp Business URL from <a target="_blank" href="https://www.radiustheme.com/docs/business-reviews/configurations/yelp-settings/how-to-get-yelp-business-url">here</a>', 'business-reviews-wp' ), array( 'a' => array( 'href' => true, 'target' => true ) ) ), 
		'type'        => 'text',  
		'class'       => 'regular-text',
		'default'     => ''
	),  
	'business_img'       => array(
		'title' => esc_html__( 'Custom Business Image', 'business-reviews-wp' ),
		'type'  => 'image'
	), 
); 
return apply_filters( 'rtbr_yelp_settings_options', $options );