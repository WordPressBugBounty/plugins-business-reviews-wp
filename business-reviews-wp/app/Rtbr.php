<?php

require_once RTBR_PATH . 'vendor/autoload.php';

use Rtbr\Traits\SingletonTrait;
use Rtbr\Widgets\Widget; 
use Rtbr\Helpers\Functions;
use Rtbr\Controllers\Shortcodes;   
use Rtbr\Controllers\Admin\Activation;
use Rtbr\Controllers\Ajax\AjaxController;
use Rtbr\Controllers\Admin\AdminController;
use Rtbr\Controllers\Marketing\Offer;
use Rtbr\Controllers\Marketing\Review;
use Rtbr\Hooks\Backend;

/**
 * Class Rtbr
 */
final class Rtbr {

    use SingletonTrait; 

    private $post_type = "rtbr";
    private $nonceId = "__rtbr_wpnonce";
    private $nonceText = "rtbr_nonce_kx2T6dRxD";

    /**
     * Business Reviews Constructor.
     */
    public function __construct() { 
        $this->define_constants();  
        new Activation();
        new Widget();

        $this->init_hooks(); 
    } 

    private function init_hooks() {
 
        add_action('plugins_loaded', [$this, 'on_plugins_loaded'], -1);
 
        add_action('init', [$this, 'init'], 1);
        add_action('init', [Shortcodes::class, 'init_short_code']);// Init ShortCode
    }

    public function init() {
        do_action('rtbr_before_init');

        $this->load_plugin_textdomain();
        // Load your all dependency hooks
        new AdminController();
        new AjaxController();  
        new Offer();
        Review::init();
        new Backend();

        do_action('rtbr_init');
    }

    public function on_plugins_loaded() {
        do_action('rtbr_loaded');
    }

    /**
     * Load Localisation files. 
     */
    public function load_plugin_textdomain() {
         
        $locale = determine_locale();
        $locale = apply_filters('rtbr_plugin_locale', $locale, 'business-reviews-wp');
        unload_textdomain('business-reviews-wp');
        load_textdomain('business-reviews-wp', WP_LANG_DIR . '/business-reviews-wp/business-reviews-wp-' . $locale . '.mo');
        load_plugin_textdomain('business-reviews-wp', false, plugin_basename(dirname(RTBR_PLUGIN_FILE)) . '/languages');
    }
 
    /**
     * What type of request is this?
     *
     * @param string $type admin, ajax, cron or frontend.
     *
     * @return bool
     */
    public function is_request($type) {
        switch ($type) {
            case 'admin':
                return is_admin();
            case 'ajax':
                return defined('DOING_AJAX');
            case 'cron':
                return defined('DOING_CRON');
            case 'frontend':
                return ( !is_admin() || defined('DOING_AJAX') ) && !defined('DOING_CRON');
        }
    } 

    private function define_constants() {
        $this->define('RTBR_URL', plugins_url('', RTBR_PLUGIN_FILE));
        $this->define('RTBR_SLUG', basename(dirname(RTBR_PLUGIN_FILE)));
        $this->define('RTBR_TEMPLATE_DEBUG_MODE', false); 
    }

    /**
     * Define constant if not already set.
     *
     * @param string      $name  Constant name.
     * @param string|bool $value Constant value.
     */
    public function define($name, $value) {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    /**
     * Get the plugin path.
     *
     * @return string
     */
    public function plugin_path() {
        return untrailingslashit(plugin_dir_path(RTBR_PLUGIN_FILE));
    } 

    /**
     * @return mixed
     */
    public function version() {
        return RTBR_VERSION;
    }

    /**
     * @return string
     */
    public function getPostType() {
        return $this->post_type;
    }

    /**
     * @return string
     */
    public function getNonceId() {
        return $this->nonceId;
    }

    /**
     * @return string
     */
    public function getNonceText() {
        return $this->nonceText;
    }

    /**
     * Get the template path.
     *
     * @return string
     */
    public function get_template_path() {
        return apply_filters('rtbr_template_path', 'business-reviews-wp/');
    } 

    /**
     * Get the template partial path.
     *
     * @return string
     */
    public function get_partial_path( $path = null, $args = []) {
        Functions::get_template_part( 'partials/' . $path, $args ); 
    } 

    /**
     * @param $file
     *
     * @return string
     */
    public function get_assets_uri($file) {
        $file = ltrim($file, '/');

        return trailingslashit(RTBR_URL . '/assets') . $file;
    }

    /**
     * @param $file
     *
     * @return string
     */
    public function render($viewName, $args = array(), $return = false) { 
        $path = str_replace(".", "/", $viewName);
        $viewPath = RTBR_PATH . '/views/' . $path . '.php';
        if (!file_exists($viewPath)) { 
            return;
        }
        if ($args) {
            extract($args);
        }
        if ($return) {
            ob_start();
            include $viewPath;

            return ob_get_clean();
        }
        include $viewPath;
    }

    /**
     * @param $file
     * Get all optoins field value
     * @return mixed
     */
    public function get_options() {

        $option_field = func_get_args()[0];
        $result = get_option( $option_field ); 
        $func_args = func_get_args();
        array_shift( $func_args );

        foreach ( $func_args as $arg ) {
            if ( is_array($arg) ) {
                if ( !empty( $result[$arg[0]] ) ) {
                    $result = $result[$arg[0]];
                } else {  
                  $result = $arg[1];
                }
            } else {
                if ( !empty($result[$arg] ) ) {
                    $result = $result[$arg];
                } else { 
                    $result = null;
                }
            }
        }
        return $result;
    } 
}

/**
 * @return bool|SingletonTrait|Rtbr
 */
function rtbr() {
    return Rtbr::getInstance();
} 
rtbr(); // Run Rtbr Plugin     