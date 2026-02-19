<?php

namespace Rtbr\Controllers\Ajax;
if ( ! defined( 'ABSPATH' ) ) exit;
class Facebook {
	public function __construct() {
		add_action( 'wp_ajax_rtbr_fb_page_access_token', array( $this, 'fb_page_access_token' ) );
	}

	function fb_page_access_token() {
		check_ajax_referer( 'rtbr-fb-nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( -1 );
		}

		$user_id      = ( ! empty( $_REQUEST['user_id'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['user_id'] ) ) : '';
		$access_token = ( ! empty( $_REQUEST['access_token'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['access_token'] ) ) : '';

		if ( $user_id && $access_token ) {
			$page_secrect_info = wp_remote_get( 'https://graph.facebook.com/' . $user_id . '/accounts?fields=name,access_token&access_token=' . $access_token );
			$response_body     = wp_remote_retrieve_body( $page_secrect_info );
			if ( ! is_wp_error( $page_secrect_info ) && ! empty( $response_body ) ) {
				$decoded = json_decode( $response_body, true );
				if ( json_last_error() === JSON_ERROR_NONE ) {
					wp_send_json_success( $decoded );
				} else {
					wp_send_json_error( __( 'Invalid response from Facebook.', 'business-reviews-wp' ) );
				}
			} else {
				wp_send_json_error( __( 'An error occurred while retrieving the information.', 'business-reviews-wp' ) );
			}
		}
		wp_die();
	}
}
