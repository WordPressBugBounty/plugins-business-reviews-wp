<?php

/**
 * @wordpress-plugin 
 * Plugin Name:       Widget for Google Reviews 
 * Plugin URI:        https://www.radiustheme.com/demo/plugins/business-reviews
 * Description:       This is a business reviews plugin for Google, Facebook and Yelp
 * Version:           1.0.14
 * Author:            RadiusTheme
 * Author URI:        https://radiustheme.com
 * Text Domain:       business-reviews-wp
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
  exit;
}

// Define PLUGIN_FILE.
if (!defined('RTBR_PLUGIN_FILE')) {
  define('RTBR_PLUGIN_FILE', __FILE__);
}

// Define VERSION.
if (!defined('RTBR_VERSION')) {
  define('RTBR_VERSION', '1.0.14');
}

// Define VERSION.
if (!defined('RTBR_PATH')) {
	define( 'RTBR_PATH', plugin_dir_path( __FILE__ ) );
}


if (!class_exists('Rtbr')) {
  require_once("app/Rtbr.php");
}

