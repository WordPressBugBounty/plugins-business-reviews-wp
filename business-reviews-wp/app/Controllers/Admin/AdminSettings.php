<?php

namespace Rtbr\Controllers\Admin; 

use Rtbr\Models\SettingsAPI;

class AdminSettings extends SettingsAPI {

    protected $tabs = array();
    protected $active_tab;
    protected $current_section; 

    public function __construct() {
        add_action('admin_init', array($this, 'setTabs'));
        add_action('admin_init', array($this, 'save')); 
        add_action('admin_menu', array($this, 'add_settings_menu'), 50); 
        add_action('rtbr_admin_settings_groups', array($this, 'setup_settings')); 
    }
    
    public function add_settings_menu() {
        // add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function = '', $position = null
        add_submenu_page(
            'edit.php?post_type=' . rtbr()->getPostType(),
            esc_html__('Settings', 'business-reviews-wp'),
            esc_html__('Settings', 'business-reviews-wp'),
            'manage_options',
            'rtbr-settings',
            array($this, 'display_settings_form')
        ); 
    } 

    function display_settings_form() { 
        require_once RTBR_PATH . 'views/settings/admin-settings-display.php';
    }  

    function setup_settings() {
        $this->set_fields();
        $this->admin_options();
    }

    function set_fields() {
        $field = array();
        $file_name = RTBR_PATH . "views/settings/{$this->active_tab}-settings.php";
        if (file_exists($file_name)) {
            $field = include($file_name);
        }

        $this->form_fields = apply_filters('rtbr_settings_option_fields', $field, $this->active_tab);
    }

    public function save() {
        if ('POST' !== $_SERVER['REQUEST_METHOD']
            || !isset($_REQUEST['post_type'])
            || !isset($_REQUEST['page'])
            || (isset($_REQUEST['post_type']) && rtbr()->getPostType() !== $_REQUEST['post_type'])
            || (isset($_REQUEST['rtbr_settings']) && 'rtbr_settings' !== $_REQUEST['rtbr_settings'])
        ) {
            return;
        }
        if (empty($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'rtbr-settings')) {
            die( __('Action failed. Please refresh the page and retry.', 'business-reviews-wp'));
        }
        $this->set_fields();
        $this->process_admin_options();

        self::add_message( esc_html__('Your settings have been saved.', 'business-reviews-wp'));
        update_option('rtbr_queue_flush_rewrite_rules', 'yes'); 
        // delete old if value changed
        $this->delete_old_transient();

        do_action('rtbr_admin_settings_saved', $this->option, $this);
    }

    function setTabs() {
        $this->tabs = array(
            'general'    => esc_html__('General', 'business-reviews-wp'),
            'google'     => esc_html__('Google', 'business-reviews-wp'),
            'facebook'   => esc_html__('Facebook', 'business-reviews-wp'),
            'yelp'       => esc_html__('Yelp', 'business-reviews-wp'),   
            'support'    => esc_html__('Support', 'business-reviews-wp'), 
        );

        // Hook to register custom tabs
        $this->tabs = apply_filters('rtbr_register_settings_tabs', $this->tabs);
        // Find the active tab
        $this->option = $this->active_tab = isset($_GET['tab']) && array_key_exists($_GET['tab'],
            $this->tabs) ? $_GET['tab'] : 'general'; 
        if (!empty($this->subtabs)) {
            $this->current_section = isset($_GET['section']) && in_array($_GET['section'],
                array_filter(array_keys($this->subtabs))) ? $_GET['section'] : '';
            $this->option = !empty($this->current_section) ? $this->option . '_' . $this->current_section : $this->active_tab . "_settings";
        } else {
            $this->option = $this->option . "_settings";
        } 
    }

    function delete_old_transient() { 
        if ( !isset( $_REQUEST['tab'] ) ) {
            // when general settings save delete old data
            delete_transient('rtbr_google_reviews');
            delete_transient('rtbr_fb_reviews');
            delete_transient('rtbr_yelp_business_info');
            delete_transient('rtbr_yelp_reviews');
        } else {
            switch ( $_REQUEST['tab'] ) {
                // when general settings save by review type delete old data
                case "google": 
                    delete_transient('rtbr_google_reviews');
                    break;
    
                case "facebook":
                    delete_transient('rtbr_fb_reviews');
                    break;
    
                case "yelp":
                    delete_transient('rtbr_yelp_business_info');
                    delete_transient('rtbr_yelp_reviews');
                    break;
            }
        } 
    }
 

}