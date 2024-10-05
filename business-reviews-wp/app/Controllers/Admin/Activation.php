<?php

namespace Rtbr\Controllers\Admin;  
use Rtbr\Helpers\Functions;

class Activation {

    public function __construct() { 
        register_activation_hook(RTBR_PLUGIN_FILE, array($this, 'plugin_activate'));
        add_action('admin_init', array($this, 'plugin_redirect') );
    }   

    function plugin_activate() { 
        $this->reGenerateCss(); 
        add_option('rtbr_activation_redirect', true);
    }

    function plugin_redirect() {
        if ( get_option('rtbr_activation_redirect', false) ) {
            delete_option('rtbr_activation_redirect'); 
            wp_redirect( admin_url('edit.php?post_type=rtbr&page=rtbr-settings&tab=support') );
        }
    }  
    
    function reGenerateCss() { 
        //review post type
        $scPostIds = get_posts( array(
            'post_type'      => rtbr()->getPostType(),
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'fields'         => 'ids'
        ) ); 
    
        if ( is_array($scPostIds) && !empty($scPostIds) ) { 
            foreach ($scPostIds as $scPostId) {  
                Functions::generatorShortCodeCss($scPostId);
            }
        } 
        wp_reset_query(); 
    } 
}
