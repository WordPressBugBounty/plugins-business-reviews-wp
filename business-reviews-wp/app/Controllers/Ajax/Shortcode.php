<?php

namespace Rtbr\Controllers\Ajax;

use Rtbr\Shortcodes\BusinessReview;

class Shortcode {
    public function __construct() {
        add_action('wp_ajax_rtbr_shortcode_layout_preview', array($this, 'shortcode_layout_preview')); 
    } 

    function shortcode_layout_preview() {  
        $shortcode_id = ( !empty( $_REQUEST['sc_id'] ) ) ? absint( $_REQUEST['sc_id'] ) : ''; 
        if ( $shortcode_id ) {  
            $params = array(
                'id' => $shortcode_id, 
            );
            BusinessReview::output( $params );
        } 
        die();
    } 
}