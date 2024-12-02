<?php

namespace Rtbr\Controllers\Ajax;

class Facebook {
    public function __construct() {
        add_action('wp_ajax_rtbr_fb_page_access_token', array($this, 'fb_page_access_token')); 
    } 

    function fb_page_access_token() {  
        $user_id = ( !empty( $_REQUEST['user_id'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['user_id'] ) ) : '';
        $access_token = ( !empty( $_REQUEST['access_token'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['access_token'] ) ) : '';
        if ( $user_id && $access_token ) { 
            $page_secrect_info = wp_remote_get( "https://graph.facebook.com/". $user_id ."/accounts?fields=name,access_token&access_token=" . $access_token );
            $response_body = wp_remote_retrieve_body( $page_secrect_info );
            if ( ! is_wp_error( $page_secrect_info ) && ! empty( $response_body ) ) {
                echo esc_html( $response_body );
            } else {
                echo esc_html__( 'An error occurred while retrieving the information.', 'business-reviews-wp' );
            }
        }
        die();
    } 
}