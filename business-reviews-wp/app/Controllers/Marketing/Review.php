<?php

namespace Rtbr\Controllers\Marketing; 

class Review {

    public static function init() {
        register_activation_hook( RTBR_PLUGIN_FILE, [__CLASS__, 'rtbr_activation_time'] );
        add_action( 'admin_init', [__CLASS__, 'rtbr_check_installation_time'] );
        add_action( 'admin_init', [__CLASS__, 'rtbr_spare_me'], 5 );
    }

    // add plugin activation time
    public static function rtbr_activation_time() {
        $get_activation_time = strtotime( "now" );
        add_option( 'rtbr_plugin_activation_time', $get_activation_time ); // replace your_plugin with Your plugin name
    }

    //check if review notice should be shown or not
    public static function rtbr_check_installation_time() {
	    if ( isset( $GLOBALS['rtbr_notice'] ) ) {
			return;
	    }
        // Added Lines Start 
        $nobug = get_option( 'rtbr_spare_me', "0"); 

        if ($nobug == "1" || $nobug == "3") {
            return;
        }

        $install_date = get_option( 'rtbr_plugin_activation_time' );
        $past_date    = strtotime( '-10 days' );

        $remind_time = get_option( 'rtbr_remind_me' );
        $remind_due  = strtotime( '+15 days', $remind_time );
        $now         = strtotime( "now" );

        if ( $now >= $remind_due ) {
            add_action( 'admin_notices', [__CLASS__, 'rtbr_display_admin_notice']);
        } else if (($past_date >= $install_date) &&  $nobug !== "2") {
            add_action( 'admin_notices', [__CLASS__, 'rtbr_display_admin_notice']);
        }
    }

    /**
     * Display Admin Notice, asking for a review
     **/
    public static function rtbr_display_admin_notice() {
        // wordpress global variable
        global $pagenow;

        $exclude = [ 'themes.php', 'users.php', 'tools.php', 'options-general.php', 'options-writing.php', 'options-reading.php', 'options-discussion.php', 'options-media.php', 'options-permalink.php', 'options-privacy.php', 'edit-comments.php', 'upload.php', 'media-new.php', 'admin.php', 'import.php', 'export.php', 'site-health.php', 'export-personal-data.php', 'erase-personal-data.php' ];

        if ( ! in_array( $pagenow, $exclude ) ) {
            $args         = [ '_wpnonce' => wp_create_nonce( 'rtbr_notice_nonce' ) ];
            $dont_disturb = esc_url( add_query_arg( $args + ['rtbr_spare_me' => '1'], self::rtbr_current_admin_url() ) );
            $remind_me    = esc_url( add_query_arg( $args + ['rtbr_remind_me' => '1'], self::rtbr_current_admin_url() ) );
            $rated        = esc_url( add_query_arg( $args + ['rtbr_rated' => '1'], self::rtbr_current_admin_url() ) );
            $reviewurl    = esc_url( 'https://wordpress.org/support/plugin/business-reviews-wp/reviews/?filter=5#new-post' );

            printf( __( '<div class="notice rtbr-review-notice rtbr-review-notice--extended"> 
                <div class="rtbr-review-notice_content">
                    <h3>Enjoying Widget for Google Reviews?</h3>
                    <p>Thank you for choosing Widget for Google Reviews. If you have found our plugin useful and makes you smile, please consider giving us a 5-star rating on WordPress.org. It will help us to grow.</p>
                    <div class="rtbr-review-notice_actions">
                        <a href="%s" class="rtbr-review-button rtbr-review-button--cta" target="_blank"><span>⭐ Yes, You Deserve It!</span></a>
                        <a href="%s" class="rtbr-review-button rtbr-review-button--cta rtbr-review-button--outline"><span>😀 Already Rated!</span></a>
                        <a href="%s" class="rtbr-review-button rtbr-review-button--cta rtbr-review-button--outline"><span>🔔 Remind Me Later</span></a>
                        <a href="%s" class="rtbr-review-button rtbr-review-button--cta rtbr-review-button--error rtbr-review-button--outline"><span>😐 No Thanks</span></a>
                    </div>
                </div> 
            </div>' ), $reviewurl, $rated, $remind_me, $dont_disturb );

            echo '<style> 
            .rtbr-review-button--cta {
                --e-button-context-color: #5d3dfd;
                --e-button-context-color-dark: #5d3dfd;
                --e-button-context-tint: rgb(75 47 157/4%);
                --e-focus-color: rgb(75 47 157/40%);
            } 
            .rtbr-review-notice {
                position: relative;
                margin: 5px 20px 5px 2px;
                border: 1px solid #ccd0d4;
                background: #fff;
                box-shadow: 0 1px 4px rgba(0,0,0,0.15);
                font-family: Roboto, Arial, Helvetica, Verdana, sans-serif;
                border-inline-start-width: 4px;
            }
            .rtbr-review-notice.notice {
                padding: 0;
            }
            .rtbr-review-notice:before {
                position: absolute;
                top: -1px;
                bottom: -1px;
                left: -4px;
                display: block;
                width: 4px;
                background: -webkit-linear-gradient(bottom, #5d3dfd 0%, #6939c6 100%);
                background: linear-gradient(0deg, #5d3dfd 0%, #6939c6 100%);
                content: "";
            } 
            .rtbr-review-notice_content {
                padding: 20px;
            } 
            .rtbr-review-notice_actions > * + * {
                margin-inline-start: 8px;
                -webkit-margin-start: 8px;
                -moz-margin-start: 8px;
            } 
            .rtbr-review-notice p {
                margin: 0;
                padding: 0;
                line-height: 1.5;
            }
            p + .rtbr-review-notice_actions {
                margin-top: 1rem;
            }
            .rtbr-review-notice h3 {
                margin: 0;
                font-size: 1.0625rem;
                line-height: 1.2;
            }
            .rtbr-review-notice h3 + p {
                margin-top: 8px;
            } 
            .rtbr-review-button {
                display: inline-block;
                padding: 0.4375rem 0.75rem;
                border: 0;
                border-radius: 3px;;
                background: var(--e-button-context-color);
                color: #fff;
                vertical-align: middle;
                text-align: center;
                text-decoration: none;
                white-space: nowrap; 
            }
            .rtbr-review-button:active {
                background: var(--e-button-context-color-dark);
                color: #fff;
                text-decoration: none;
            }
            .rtbr-review-button:focus {
                outline: 0;
                background: var(--e-button-context-color-dark);
                box-shadow: 0 0 0 2px var(--e-focus-color);
                color: #fff;
                text-decoration: none;
            }
            .rtbr-review-button:hover {
                background: var(--e-button-context-color-dark);
                color: #fff;
                text-decoration: none;
            } 
            .rtbr-review-button.focus {
                outline: 0;
                box-shadow: 0 0 0 2px var(--e-focus-color);
            } 
            .rtbr-review-button--error {
                --e-button-context-color: #d72b3f;
                --e-button-context-color-dark: #ae2131;
                --e-button-context-tint: rgba(215,43,63,0.04);
                --e-focus-color: rgba(215,43,63,0.4);
            }
            .rtbr-review-button.rtbr-review-button--outline {
                border: 1px solid;
                background: 0 0;
                color: var(--e-button-context-color);
            }
            .rtbr-review-button.rtbr-review-button--outline:focus {
                background: var(--e-button-context-tint);
                color: var(--e-button-context-color-dark);
            }
            .rtbr-review-button.rtbr-review-button--outline:hover {
                background: var(--e-button-context-tint);
                color: var(--e-button-context-color-dark);
            } 
            </style>';
        }
    }

    // remove the notice for the user if review already done or if the user does not want to
    public static function rtbr_spare_me() {
        
        if ( ! isset( $_REQUEST['_wpnonce'] ) || !wp_verify_nonce( $_REQUEST['_wpnonce'], 'rtbr_notice_nonce' ) ) {
			return;
		}

        if ( isset( $_GET['rtbr_spare_me'] ) && ! empty( $_GET['rtbr_spare_me'] ) ) {
            $spare_me = $_GET['rtbr_spare_me'];
            if ( 1 == $spare_me ) {
                update_option( 'rtbr_spare_me', "1" );
            }
        }

        if ( isset( $_GET['rtbr_remind_me'] ) && ! empty( $_GET['rtbr_remind_me'] ) ) {
            $remind_me = $_GET['rtbr_remind_me'];
            if ( 1 == $remind_me ) {
                $get_activation_time = strtotime( "now" );
                update_option( 'rtbr_remind_me', $get_activation_time );
                update_option( 'rtbr_spare_me', "2" );
            }
        }

        if ( isset( $_GET['rtbr_rated'] ) && ! empty( $_GET['rtbr_rated'] ) ) {
            $rtbr_rated = $_GET['rtbr_rated'];
            if ( 1 == $rtbr_rated ) {
                update_option( 'rtbr_rated', 'yes' );
                update_option( 'rtbr_spare_me', "3" );
            }
        }
    }

    protected static function rtbr_current_admin_url() {
        $uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
		$uri = preg_replace( '|^.*/wp-admin/|i', '', $uri );
		if (! $uri) {
			return '';
		}
        return remove_query_arg( [ '_wpnonce', '_wc_notice_nonce', 'wc_db_update', 'wc_db_update_nonce', 'wc-hide-notice' , 'rtbr_rated', 'rtbr_remind_me', 'rtbr_spare_me' ], admin_url( $uri ) );
    }
}  