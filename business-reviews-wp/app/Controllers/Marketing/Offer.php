<?php

namespace Rtbr\Controllers\Marketing; 

class Offer {
	public function __construct() {
		add_action(
			'admin_init',
			function () {
				$current = time();

                /*
				if ( mktime( 0, 0, 0, 11, 17, 2022 ) <= $current && $current <= mktime( 0, 0, 0, 1, 15, 2023 ) ) {
					if ( get_option( 'rtbr_ny_2023' ) != '1' ) {
						if ( ! isset( $GLOBALS['rtbr_ny_2023_notice'] ) ) {
							// $GLOBALS['rtbr_ny_2023_notice'] = 'rtbr_ny_2023';
							// self::notice();
						}
					}
				}
                */

				$start         = strtotime( '19 November 2023' );
				$end           = strtotime( '05 January 2024' );
				// Black Friday Notice
				if ( $start <= $current && $current <= $end ) {
					if ( get_option( 'rtbr_black_friday_offer_2023' ) != '1' ) {
						if ( ! isset( $GLOBALS['rtbr_notice'] ) ) {
							$GLOBALS['rtbr_notice'] = 'rtbr_notice';
							self::black_friday_notice();

						}
					}
				}


			}
		);
	}

	/**
	 * Undocumented function.
	 *
	 * @return void
	 */
	public static function notice() {
		add_action(
			'admin_enqueue_scripts',
			function () {
				wp_enqueue_script( 'jquery' );
			}
		);

		add_action(
			'admin_notices',
			function () {
				$plugin_name   = 'Widget for Google Reviews Pro';
				$download_link = 'https://www.radiustheme.com/downloads/business-review/'; ?>
				<div class="notice notice-info is-dismissible" data-rtbrdismissable="rtbr_ny_2023"
					style="display:grid;grid-template-columns: 100px auto;padding-top: 25px; padding-bottom: 22px;">
					<img alt="<?php echo esc_attr( $plugin_name ); ?>"
						src="<?php echo rtbr()->get_assets_uri( 'imgs/icon-128x128.png' ); ?>" width="74px"
						height="74px" style="grid-row: 1 / 4; align-self: center;justify-self: center"/>
					<h3 style="margin:0;"><?php echo sprintf( '%s New Year Deal!!', $plugin_name ); ?></h3>

					<p style="margin:0 0 2px;">
						<?php echo esc_html__( "Don't miss out on our biggest sale of the year! Get your.", 'review-schema' ); ?>
						<b><?php echo esc_attr( $plugin_name ); ?> plan</b> with <b>UP TO 50% OFF</b>! Limited time offer!!
					</p>

					<p style="margin:0;">
						<a class="button button-primary" href="<?php echo esc_url( $download_link ); ?>" target="_blank">Buy Now</a>
						<a class="button button-dismiss" href="#">Dismiss</a>
					</p>
				</div>
					<?php
			}
		);

		add_action(
			'admin_footer',
			function () {
				?>
				<script type="text/javascript">
					(function ($) {
						$(function () {
							setTimeout(function () {
								$('div[data-rtbrdismissable] .notice-dismiss, div[data-rtbrdismissable] .button-dismiss')
									.on('click', function (e) {
										e.preventDefault();
										$.post(ajaxurl, {
											'action': 'rtbr_dismiss_admin_notice',
											'nonce': <?php echo json_encode( wp_create_nonce( 'rtbr-dismissible-notice' ) ); ?>
										});
										$(e.target).closest('.is-dismissible').remove();
									});
							}, 1000);
						});
					})(jQuery);
				</script>
					<?php
			}
		);

		add_action(
			'wp_ajax_rtbr_dismiss_admin_notice',
			function () {
				check_ajax_referer( 'rtbr-dismissible-notice', 'nonce' );

				update_option( 'rtbr_ny_2023', '1' );
				wp_die();
			}
		);
	}

    
	/**
	 * Undocumented function.
	 *
	 * @return void
	 */
	public static function black_friday_notice() {
		add_action(
			'admin_enqueue_scripts',
			function () {
				wp_enqueue_script( 'jquery' );
			}
		);

		add_action(
			'admin_notices',
			function () {
				$plugin_name   = 'Widget for Google Reviews Pro';
				$download_link = 'https://www.radiustheme.com/downloads/business-review/'; ?>
                <div class="notice notice-info is-dismissible" data-rtbrbfdismissable="rtbr_black_friday_offer_2023"
                     style="display:grid;grid-template-columns: 100px auto;padding-top: 25px; padding-bottom: 22px;">
                    <img alt="<?php echo esc_attr( $plugin_name ); ?>"
                         src="<?php echo rtbr()->get_assets_uri( 'imgs/icon-128x128.png' ); ?>" width="74px"
                         height="74px" style="grid-row: 1 / 4; align-self: center;justify-self: center"/>
                    <h3 style="margin:0;"><?php echo sprintf( '%s Black Friday Sale 2023!!', $plugin_name ); ?></h3>

                    <p style="margin:0 0 2px;padding: 5px 0;">
                        Exciting News: <b><?php echo $plugin_name; ?></b> Black Friday sale is now live! Get the plugin today and enjoy discounts up to 50%.
                    </p>

                    <p style="margin:0;">
                        <a class="button button-primary" href="<?php echo esc_url( $download_link ); ?>" target="_blank">Buy Now</a>
                        <a class="button button-dismiss" href="#">Dismiss</a>
                    </p>
                </div>
				<?php
			}
		);

		add_action(
			'admin_footer',
			function () {
				?>
                <script type="text/javascript">
                    (function ($) {
                        $(function () {
                            setTimeout(function () {
                                $('div[data-rtbrbfdismissable] .notice-dismiss, div[data-rtbrbfdismissable] .button-dismiss')
                                    .on('click', function (e) {
                                        e.preventDefault();
                                        $.post(ajaxurl, {
                                            'action': 'rtbr_dismiss_admin_black_friday_notice',
                                            'nonce': <?php echo json_encode( wp_create_nonce( 'rtbr-black-friday-offer-2023' ) ); ?>
                                        });
                                        $(e.target).closest('.is-dismissible').remove();
                                    });
                            }, 1000);
                        });
                    })(jQuery);
                </script>
				<?php
			}
		);

		add_action(
			'wp_ajax_rtbr_dismiss_admin_black_friday_notice',
			function () {
				check_ajax_referer( 'rtbr-black-friday-offer-2023', 'nonce' );

				update_option( 'rtbr_black_friday_offer_2023', '1' );
				wp_die();
			}
		);
	}





}
