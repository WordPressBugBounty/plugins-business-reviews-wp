<?php

namespace Rtbr\Hooks;

class Backend {
	public function __construct() { 
		add_filter( 'plugin_action_links_' . plugin_basename(RTBR_PLUGIN_FILE), array(
			$this,
			'plugin_action_links'
		) );
	}

	public function plugin_action_links( $links ) {
		$new_links = array(
			'<a href="' . admin_url( '/edit.php?post_type=rtbr&page=rtbr-settings' ) . '">' . __( "Settings", 'business-reviews-wp' ) . '</a>',
			'<a target="_blank" href="' . esc_url( 'https://www.radiustheme.com/demo/plugins/business-reviews/' ) . '">' . esc_html__( "Demo", 'business-reviews-wp' ) . '</a>',
			'<a target="_blank" href="' . esc_url( 'https://www.radiustheme.com/docs/business-reviews/business-reviews/' ) . '">' . esc_html__( "Documentation", 'business-reviews-wp' ) . '</a>'
		);

		if ( !function_exists('rtbrp') ) {
            $new_links[] = '<a style="color: #39b54a;font-weight: 700;" target="_blank" href="' . esc_url('https://www.radiustheme.com/downloads/business-review/?utm_source=WordPress&utm_medium=business-review&utm_campaign=pro_click') . '">' . esc_html__("Get Pro", 'business-reviews-wp') . '</a>';
        }

		return array_merge( $links, $new_links );
	}

}
