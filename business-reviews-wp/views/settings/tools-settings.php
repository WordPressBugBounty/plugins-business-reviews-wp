<?php

if ( !defined('ABSPATH') ) exit; 

/**
 * Tools Settings
 */

$options = array(
    'site_section' => array(
        'title'    => esc_html__('Licensing', 'business-reviews-wp'),
        'type'     => 'title',
    ),
    'license_key' => array( 
        'title'    => esc_html__('Main plugin license key', "business-reviews-wp"), 
        'type'     => 'text',
        'class'    => 'regular-text', 
    ),   
);

return apply_filters('rtbr_tools_settings_options', $options);
