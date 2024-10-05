<?php

namespace Rtbr\Controllers\Admin; 

class RegisterPostType {

    public function __construct() {
        add_action('init', [$this, 'register_post_types'], 5);
    } 

    public static function register_post_types() {
          
        if (!is_blog_installed() || post_type_exists(rtbr()->getPostType())) {
            return;
        }

        do_action('rtbr_register_post_type');
 
        $label = array(
            'name' => esc_html_x('Shortcode', 'Post Type General Name', 'business-reviews-wp'),
            'singular_name' => esc_html_x('Shortcode', 'Post Type Singular Name', 'business-reviews-wp'),
            'menu_name' => esc_html__('Business Reviews', 'business-reviews-wp'),
            'parent_item_colon' => esc_html__('Parent Shortcode:', 'business-reviews-wp'),
            'all_items' => esc_html__('All Shortcodes', 'business-reviews-wp'),
            'view_item' => esc_html__('View Shortcode', 'business-reviews-wp'),
            'add_new_item' => esc_html__('Add New Shortcode', 'business-reviews-wp'),
            'add_new' => esc_html__('New Shortcode', 'business-reviews-wp'),
            'edit_item' => esc_html__('Edit Shortcode', 'business-reviews-wp'),
            'update_item' => esc_html__('Update Shortcode', 'business-reviews-wp'),
            'search_items' => esc_html__('Search Shortcode', 'business-reviews-wp'),
            'not_found' => esc_html__('No google review found', 'business-reviews-wp'),
            'not_found_in_trash' => esc_html__('No google review found in Trash', 'business-reviews-wp'),
        );

        $rtbr_support = array('title'); 
        $args = array(
            'label' => esc_html__('Shortcode', 'business-reviews-wp'),
            'description' => esc_html__('Shortcode', 'business-reviews-wp'),
            'labels' => $label,
            'supports' => $rtbr_support, 
            'hierarchical' => false,
            'public' => false, 
            'show_ui' => current_user_can('administrator') ? true : false,
            'show_in_menu' => true, 
            'show_in_nav_menus' => false,
            'show_in_admin_bar' => true,
            'menu_position' => 20,
            'menu_icon'  => RTBR_URL . '/assets/imgs/icon-20x20.png',
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'capability_type' => 'page',
        );
        register_post_type(rtbr()->getPostType(), apply_filters('rtbr_register_post_type_args', $args));

        do_action('rtbr_after_register_post_type');
    }  
}
