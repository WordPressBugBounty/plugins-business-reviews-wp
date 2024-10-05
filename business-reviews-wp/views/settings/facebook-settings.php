<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Facebook Settings
 */ 

$options = array(  
	'fb_app_id' => array(
		'title'       => esc_html__( 'Facebook App ID', 'business-reviews-wp' ),  
		'description' => wp_kses( __( 'If you want to use your own facebook app then you can mention the app id here. <a target="_blank" href="https://www.radiustheme.com/docs/business-reviews/configurations/facebook-settings/how-to-create-your-own-facebook-app-id">How to create app?</a>', 'business-reviews-wp' ), array( 'a' => array( 'href' => true, 'target' => true ) ) ),
		'type'        => 'text',  
		'class'       => 'regular-text',
		'default'     => ''
	), 
	'fb_login_btn'       => array(
		'title' => esc_html__( 'Get Page Secrect Info', 'business-reviews-wp' ),
		'type'  => 'fb_login_btn',
		'description' => wp_kses( __( '<strong>Note:</strong> Please keep in mind, Facebook restricted it on localhost so try it online domain.', 'business-reviews-wp' ), array( 'strong' => array() ) ), 
	), 
	'page_access_token' => array(
		'title'       => esc_html__( 'Facebook Page Access Token', 'business-reviews-wp' ),
		'description' => esc_html__( 'To get page secrect info click on login button and give your page permission', 'business-reviews-wp' ),
		'type'        => 'text',  
		'class'       => 'regular-text',
		'default'     => ''
	), 
	'page_name' => array(
		'title'       => esc_html__( 'Facebook Page Name', 'business-reviews-wp' ),
		'type'        => 'text',  
		'class'       => 'regular-text',
		'default'     => ''
	),  
	'page_id' => array(
		'title'       => esc_html__( 'Facebook Page ID', 'business-reviews-wp' ),
		'type'        => 'text',  
		'class'       => 'regular-text',
		'default'     => ''
	), 
	'page_img'       => array(
		'title' => esc_html__( 'Custom Page Image', 'business-reviews-wp' ),
		'type'  => 'image'
	), 
); 
return apply_filters( 'rtbr_facebook_settings_options', $options );