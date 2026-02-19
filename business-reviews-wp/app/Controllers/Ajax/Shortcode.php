<?php

namespace Rtbr\Controllers\Ajax;

use Rtbr\Shortcodes\BusinessReview;
if ( ! defined( 'ABSPATH' ) ) exit;
class Shortcode {
	public function __construct() {
		add_action( 'wp_ajax_rtbr_shortcode_layout_preview', array( $this, 'shortcode_layout_preview' ) );
	}

	function shortcode_layout_preview() {
		if ( ! \Rtbr\Helpers\Functions::verify_nonce() ) {
			wp_die( -1 );
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( -1 );
		}

		$shortcode_id = ( ! empty( $_REQUEST['sc_id'] ) ) ? absint( $_REQUEST['sc_id'] ) : ''; //phpcs:disable
		if ( $shortcode_id ) {
			$params = array(
				'id' => $shortcode_id,
			);
			BusinessReview::output( $params );
		}
		die();
	}
}
