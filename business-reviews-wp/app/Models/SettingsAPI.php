<?php

namespace Rtbr\Models; 

use Rtbr\Helpers\Functions;

abstract class SettingsAPI {

	/**
	 * The plugin ID. Used for option names.
	 *
	 * @var string
	 */
	public $plugin_id = 'rtbr_';

	/**
	 * ID of the class extending the settings API. Used in option names.
	 *
	 * @var string
	 */
	public $option = '';

	/**
	 * Setting values.
	 *
	 * @var array
	 */
	public $settings = array();

	/**
	 * Form option fields.
	 *
	 * @var array
	 */
	public $form_fields = array();

	/**
	 * The posted settings data. When empty, $_POST data will be used.
	 *
	 * @var array
	 */
	protected $data = array();

	public static $messages = array();
	public static $errors = array();


	/**
	 * Add a message.
	 *
	 * @param string $text
	 */
	public static function add_message( $text ) {
		self::$messages[] = $text;
	}


	/**
	 * Output messages + errors.
	 */
	public static function show_messages() {
		if ( sizeof( self::$errors ) > 0 ) {
			foreach ( self::$errors as $error ) {
				echo '<div id="message" class="error inline"><p><strong>' . esc_html( $error ) . '</strong></p></div>';
			}
		} elseif ( sizeof( self::$messages ) > 0 ) {
			foreach ( self::$messages as $message ) {
				echo '<div id="message" class="updated inline"><p><strong>' . esc_html( $message ) . '</strong></p></div>';
			}
		}
	}

	/**
	 * Save the settings.
	 */
	public function save() {
		if ( 'POST' !== $_SERVER['REQUEST_METHOD']
		    || ! isset( $_REQUEST['post_type'] )
		    || ! isset( $_REQUEST['page'] )
		    || ( isset( $_REQUEST['post_type'] ) && rtbr()->getPostType() !== $_REQUEST['post_type'] )
		    || ( isset( $_REQUEST['rtbr_settings'] ) && 'rtbr_settings' !== $_REQUEST['rtbr_settings'] )
		) {
			return;
		}
		if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'rtbr-settings' ) ) {
			die( esc_html__( 'Action failed. Please refresh the page and retry.', 'business-reviews-wp' ) );
		}

		// Find the active tab
		$this->option = $this->active_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'],
			$this->tabs ) ? $_GET['tab'] : 'general';
		 
		if ( ! empty( $this->subtabs ) ) {
			$this->current_section = isset( $_GET['section'] ) && in_array( $_GET['section'],
				array_filter( array_keys( $this->subtabs ) ) ) ? $_GET['section'] : '';
			$this->option          = ! empty( $this->current_section ) ? $this->option . '_' . $this->current_section : $this->active_tab . "_settings";
		} else {
			$this->option = $this->option . "_settings";
		}

		$this->process_admin_options();  
		self::add_message( esc_html__( 'Your settings have been saved.', 'business-reviews-wp' ) );
        update_option( 'rtbr_queue_flush_rewrite_rules', 'yes' );
        rtbr()->query->init_query_vars();
        rtbr()->query->add_endpoints();

		do_action( 'rtbr_admin_settings_saved', $this->option, $this );
	}

	/**
	 * Get the form fields after they are initialized.
	 *
	 * @return array of options
	 */
	public function get_form_fields() { 
		return apply_filters( 'rtbr_settings_api_form_fields_' . $this->option,
			array_map( array( $this, 'set_defaults' ), $this->form_fields ) );
	}

	/**
	 * Set default required properties for each field.
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	protected function set_defaults( $field ) {
		if ( ! isset( $field['default'] ) ) {
			$field['default'] = '';
		}

		return $field;
	}

	/**
	 * Output the admin options table.
	 */
	public function admin_options() {
		echo '<table class="form-table">' . $this->generate_settings_html( $this->get_form_fields() ) . '</table>';
	}

	/**
	 * Initialise settings form fields.
	 *
	 * Add an array of fields to be displayed
	 * on the gateway's settings screen.
	 *
	 * @since  1.0.0
	 */
	public function init_form_fields() {
	}

	/**
	 * Return the name of the option in the WP DB.
	 *
	 * @return string
	 * @since 2.6.0
	 */
	public function get_option_key() {
		return $this->plugin_id . $this->option;
	}

	/**
	 * Get a fields type. Defaults to "text" if not set.
	 *
	 * @param array $field
	 *
	 * @return string
	 */
	public function get_field_type( $field ) {
		return empty( $field['type'] ) ? 'text' : $field['type'];
	}

	/**
	 * Get a fields default value. Defaults to "" if not set.
	 *
	 * @param array $field
	 *
	 * @return string
	 */
	public function get_field_default( $field ) {
		return empty( $field['default'] ) ? '' : $field['default'];
	}

	/**
	 * Get a field's posted and validated value.
	 *
	 * @param string $key
	 * @param array $field
	 * @param array $post_data
	 *
	 * @return string
	 */
	public function get_field_value( $key, $field, $post_data = array() ) {
		$type = $this->get_field_type( $field );
		$field_key = $key; //$this->get_field_key( $key );
		$post_data = empty( $post_data ) ? $_POST : $post_data;
		$post_data = empty( $post_data ) ? ! empty( $_POST[ $this->get_option_key() ] ) ? $_POST[ $this->get_option_key() ] : array() : $post_data;
		$value     = isset( $post_data[ $field_key ] ) ? $post_data[ $field_key ] : null;

		// Look for a validate_FIELDID_field method for special handling
		if ( is_callable( array( $this, 'validate_' . $key . '_field' ) ) ) {
			return $this->{'validate_' . $key . '_field'}( $key, $value );
		}

		// Look for a validate_FIELDTYPE_field method
		if ( is_callable( array( $this, 'validate_' . $type . '_field' ) ) ) {
			return $this->{'validate_' . $type . '_field'}( $key, $value );
		}

		// Fallback to text
		return $this->validate_text_field( $key, $value );
	}

	/**
	 * Sets the POSTed data. This method can be used to set specific data, instead
	 * of taking it from the $_POST array.
	 *
	 * @param array data
	 */
	public function set_post_data( $data = array() ) {
		$this->data = $data;
	}

	/**
	 * Returns the POSTed data, to be used to save the settings.
	 *
	 * @return array
	 */
	public function get_post_data() {
		if ( ! empty( $this->data ) && is_array( $this->data ) ) {
			return $this->data;
		}

		return isset( $_POST[ $this->get_option_key() ] ) ? $_POST[ $this->get_option_key() ] : array();
	}

	/**
	 * Processes and saves options.
	 * If there is an error thrown, will continue to save and validate fields, but will leave the erroring field out.
	 *
	 * @return bool was anything saved?
	 */
	public function process_admin_options() {
		$this->init_settings();

		$post_data = $this->get_post_data();

		foreach ( $this->get_form_fields() as $key => $field ) {
			if ( 'title' !== $this->get_field_type( $field ) ) {
				try {
					$this->settings[ $key ] = $this->get_field_value( $key, $field, $post_data );
				} catch ( \Exception $e ) {
					$this->add_error( $e->getMessage() );
				}
			} else {
				unset( $this->settings[ $key ] );
			}
		}

		$sanitized_new_settings = apply_filters( 'rtbr_settings_api_sanitized_fields_' . $this->option, $this->settings, $this );
		do_action( 'rtbr_admin_settings_before_saved_' . $this->option, $sanitized_new_settings, Functions::get_option( $this->get_option_key() ), $this );

		return update_option( $this->get_option_key(), $sanitized_new_settings );
	}

	/**
	 * Add an error message for display in admin on save.
	 *
	 * @param string $error
	 */
	public static function add_error( $error ) {
		self::$errors[] = $error;
	}

	/**
	 * Get admin error messages.
	 */
	public function get_errors() {
		return self::$errors;
	}

	/**
	 * Display admin error messages.
	 */
	public function display_errors() {
		if ( $this->get_errors() ) {
			echo '<div class="error notice is-dismissible">';
			foreach ( $this->get_errors() as $error ) {
				echo '<p>' . wp_kses_post( $error ) . '</p>';
			}
			echo '</div>';
		}
	}

	/**
	 * Initialise Settings.
	 *
	 * Store all settings in a single database entry
	 * and make sure the $settings array is either the default
	 * or the settings stored in the database.
	 *
	 * @since 1.0.0
	 * @uses  get_option(), add_option()
	 */
	public function init_settings() {
		$this->settings = get_option( $this->get_option_key(), null ); 

		// If there are no settings defined, use defaults.
		if ( ! is_array( $this->settings ) ) {
			$form_fields    = $this->get_form_fields();
			$this->settings = array_merge( array_fill_keys( array_keys( $form_fields ), '' ),
				wp_list_pluck( $form_fields, 'default' ) );
		}
	}

	/**
	 * get_option function.
	 *
	 * Gets an option from the settings API, using defaults if necessary to prevent undefined notices.
	 *
	 * @param string $key
	 * @param mixed $empty_value
	 *
	 * @return string The value specified for the option or a default value for the option.
	 */
	public function get_option( $key, $empty_value = null ) {

		if ( empty( $this->settings ) ) {
			$this->init_settings();
		}

		// Get option default if unset.
		if ( ! isset( $this->settings[ $key ] ) ) {
			$form_fields            = $this->get_form_fields();
			$this->settings[ $key ] = isset( $form_fields[ $key ] ) ? $this->get_field_default( $form_fields[ $key ] ) : '';
		}

		if ( ! is_null( $empty_value ) && '' === $this->settings[ $key ] ) {
			$this->settings[ $key ] = $empty_value;
		}

		return $this->settings[ $key ];
	}

	/**
	 * Prefix key for settings.
	 *
	 * @param mixed $key
	 *
	 * @return string
	 */
	public function get_field_key( $key ) {
		return $this->plugin_id . $this->option . '[' . $key . ']';
	}

	public function get_field_id( $key ) {
		return $this->plugin_id . $this->option . '-' . $key;
	}

	/**
	 * Generate Settings HTML.
	 *
	 * Generate the HTML for the fields on the "settings" screen.
	 *
	 * @param array $form_fields (default: array())
	 * @param bool $echo
	 *
	 * @return string the html for the settings
	 * @since  1.0.0
	 * @uses   method_exists()
	 */
	public function generate_settings_html( $form_fields = array() ) {
		if ( empty( $form_fields ) ) {
			$form_fields = $this->get_form_fields();
		}
		$html = '';
		foreach ( $form_fields as $k => $v ) {
			$type = $this->get_field_type( $v );

			if ( method_exists( $this, 'generate_' . $type . '_html' ) ) {
				$html .= $this->{'generate_' . $type . '_html'}( $k, $v );
			} else {
				$html .= $this->generate_text_html( $k, $v );
			}
		}

		return $html;
	}

	/**
	 * Get HTML for tooltips.
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	public function get_tooltip_html( $data ) {
		if ( true === $data['desc_tip'] ) {
			$tip = esc_html( $data['description'] );
		} elseif ( ! empty( $data['desc_tip'] ) ) {
			$tip = esc_html( $data['desc_tip'] );
		} else {
			$tip = '';
		}

		return $tip;
	}

	/**
	 * Get HTML for descriptions.
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	public function get_description_html( $data ) {
		if ( true === $data['desc_tip'] ) {
			$description = '';
		} elseif ( ! empty( $data['desc_tip'] ) ) {
			$description = $data['description'];
		} elseif ( ! empty( $data['description'] ) ) {
			$description = $data['description'];
		} else {
			$description = '';
		}

		return $description ? '<p class="description">' . wp_kses_post( $description ) . '</p>' . "\n" : '';
	}

	/**
	 * Get custom attributes.
	 *
	 * @param array $data
	 *
	 * @return string
	 */
	public function get_custom_attribute_html( $data ) {
		$custom_attributes = array();

		if ( ! empty( $data['custom_attributes'] ) && is_array( $data['custom_attributes'] ) ) {
			foreach ( $data['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}

		return implode( ' ', $custom_attributes );
	}

	/**
	 * Generate Text Input HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function generate_text_html( $key, $data ) {
		$field_key     = $this->get_field_key( $key );
		$id            = $this->get_field_id( $key );
		$defaults      = $this->get_placeholder_data();
		$data          = wp_parse_args( $data, $defaults );
		$wrapper_class = implode( " ", array( $id, $data['wrapper_class'] ) );
		$depends       = empty( $data['dependency'] ) ? '' : "data-rt-depends='" . wp_json_encode( $data['dependency'] ) . "'";
		ob_start();
		?>
        <tr valign="top" class="<?php echo esc_attr( $wrapper_class ); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html( $this->get_tooltip_html( $data ) ); ?>
                <label for="<?php echo esc_attr( $id ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span>
                    </legend>
                    <input class="input-text regular-input <?php echo esc_attr( $data['class'] ); ?>"
                           type="<?php echo esc_attr( $data['type'] ); ?>" name="<?php echo esc_attr( $field_key ); ?>"
                           id="<?php echo esc_attr( $id ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>"
                           value="<?php echo esc_attr( $this->get_option( $key ) ); ?>"
                           placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'],
						true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?> />
					<?php echo $this->get_description_html( $data ); ?>
                </fieldset>
            </td>
        </tr>
		<?php

		return ob_get_clean();
	}

	public function generate_image_size_html( $key, $data ) {
		$field_key     = $this->get_field_key( $key );
		$id            = $this->get_field_id( $key );
		$defaults      = $this->get_placeholder_data();
		$data          = wp_parse_args( $data, $defaults );
		$wrapper_class = implode( " ", array( $id, $data['wrapper_class'] ) );
		$depends       = empty( $data['dependency'] ) ? '' : "data-rt-depends='" . wp_json_encode( $data['dependency'] ) . "'";
		$size          = $this->get_option( $key );
		ob_start();
		?>
        <tr valign="top" class="<?php echo esc_attr( $wrapper_class ); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html( $this->get_tooltip_html( $data ) ); ?>
                <label for="<?php echo esc_attr( $id ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
            </th>
            <td class="form-input rtbr-image-size-wrap">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span>
                    </legend>
					<?php foreach ( (array) $data['options'] as $option_key => $option_value ) : ?>
                        <div class='rtbr-image-size-item'>
							<?php
							if ( $option_key == 'crop' ): ?>
                                <label for="<?php echo esc_attr( $id ) . "-" . $option_key; ?>">
                                    <input type="checkbox"
                                           name="<?php echo esc_attr( $field_key ); ?>[<?php echo esc_attr( $option_key ); ?>]"
                                           id="<?php echo esc_attr( $id ) . "-" . $option_key; ?>"
                                           value="yes" <?php checked( isset( $size[ $option_key ] ) ? $size[ $option_key ] : null, 'yes' ); ?> />
									<?php echo wp_kses_post( $option_value ); ?>
                                </label><br/>
							<?php else:
								$value = ! empty( $size[ $option_key ] ) ? absint( esc_attr( $size[ $option_key ] ) ) : null;
								?>
                                <label for='<?php echo esc_attr( $id ) . "-" . $option_key; ?>'><?php echo wp_kses_post( $option_value ); ?></label>
                                <input type='number'
                                       name='<?php echo esc_attr( $field_key ); ?>[<?php echo esc_attr( $option_key ); ?>]'
                                       id="<?php echo esc_attr( $id ) . "-" . $option_key; ?>"
                                       value="<?php echo esc_attr( $value ); ?>"
                                />
							<?php endif; ?>
                        </div>
					<?php endforeach; ?>
					<?php echo $this->get_description_html( $data ); ?>
                </fieldset>
            </td>
        </tr>
		<?php

		return ob_get_clean();
	}

	public function generate_image_html( $key, $data ) {
		$field_key       = $this->get_field_key( $key );
		$id              = $this->get_field_id( $key );
		$defaults        = $this->get_placeholder_data();
		$data            = wp_parse_args( $data, $defaults );
		$wrapper_class   = implode( " ", array( $id, $data['wrapper_class'] ) );
		$depends         = empty( $data['dependency'] ) ? '' : "data-rt-depends='" . wp_json_encode( $data['dependency'] ) . "'";
		$value           = absint( $this->get_option( $key ) );
		$placeholder_url = Functions::get_default_placeholder_url();
		$image_src       = $value ? wp_get_attachment_thumb_url( $value ) : $placeholder_url;
		ob_start();
		?>
        <tr valign="top" class="<?php echo esc_attr( $wrapper_class ); ?>" <?php echo wp_kses_post( $depends ); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html( $this->get_tooltip_html( $data ) ); ?>
                <label for="<?php echo esc_attr( $id ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span>
                    </legend>
                    <div class="rtbr-setting-image-wrap">
                        <input type="hidden" id="<?php echo esc_attr( $id ); ?>" class="rtbr-setting-image-id"
                               value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $field_key ); ?>"/>
                        <div class="image-preview-wrapper"
                             data-placeholder="<?php echo esc_url( $placeholder_url ); ?>">
                            <img src="<?php echo esc_url( $image_src ); ?>"/>
                        </div>
                        <input type="button" class="button button-secondary rtbr-add-image"
                               value="<?php esc_attr_e( 'Add Image', 'business-reviews-wp' ); ?>"/>
                        <input type="button" class="button button-secondary rtbr-remove-image"
                               value="<?php esc_attr_e( 'Remove Image', 'business-reviews-wp' ); ?>"/>
                    </div>
					<?php echo $this->get_description_html( $data ); ?>
                </fieldset>
            </td>
        </tr>
		<?php

		return ob_get_clean();
	}

	public function generate_fb_login_btn_html( $key, $data ) {
		$field_key       = $this->get_field_key( $key );
		$id              = $this->get_field_id( $key );
		$defaults        = $this->get_placeholder_data();
		$data            = wp_parse_args( $data, $defaults );
		$wrapper_class   = implode( " ", array( $id, $data['wrapper_class'] ) );
		$depends         = empty( $data['dependency'] ) ? '' : "data-rt-depends='" . wp_json_encode( $data['dependency'] ) . "'";
		$value           = absint( $this->get_option( $key ) ); 
		ob_start();
		?>
        <tr valign="top" class="<?php echo esc_attr( $wrapper_class ); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html( $this->get_tooltip_html( $data ) ); ?>
                <label for="<?php echo esc_attr( $id ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
            </th>
            <td class="form-input"> 

			<fb:login-button 
			scope="pages_show_list,pages_read_user_content,pages_read_engagement"
			onlogin="checkLoginState();">
			</fb:login-button>   

			<script>
				window.fbAsyncInit = function() {
					FB.init({ 
					appId      : '<?php echo rtbr()->get_options('rtbr_facebook_settings', array('fb_app_id', '713789302671576') ); ?>',  
					cookie     : true,
					xfbml      : true,
					version    : 'v2.0'
					}); 
					FB.AppEvents.logPageView();    
				};
				
				(function(d, s, id){
					var js, fjs = d.getElementsByTagName(s)[0];
					if (d.getElementById(id)) {return;}
					js = d.createElement(s); js.id = id;
					js.src = "https://connect.facebook.net/en_US/sdk.js";
					fjs.parentNode.insertBefore(js, fjs);
				}(document, 'script', 'facebook-jssdk'));

				function checkLoginState() { 
					FB.getLoginStatus(function(response) {
						if (response.status === 'connected') {
							var accessToken = response.authResponse.accessToken;
							var userID = response.authResponse.userID; 
							// get page access token
							jQuery.ajax({
								type: "post",
								dataType: "json",
								url: "<?php echo esc_url( admin_url( "admin-ajax.php" ) ); ?>",
								data: {
									action: "rtbr_fb_page_access_token", 
									user_id: userID, 
									access_token: accessToken, 
									nonce: "secrect_nonce"
								},
								beforeSend: function() { 
								},
								success: function(resp) { 
									let page_name = resp.data[0].name;
									let page_access_token = resp.data[0].access_token;
									let page_id = resp.data[0].id;
									jQuery("#rtbr_facebook_settings-page_access_token").val(page_access_token);
									jQuery("#rtbr_facebook_settings-page_name").val(page_name);
									jQuery("#rtbr_facebook_settings-page_id").val(page_id); 
								},
								error: function(resp) {  
								},
							});    
						} 
					} );
				} 
			</script>
				<?php echo $this->get_description_html( $data ); ?>
            </td>
        </tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate Price Input HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function generate_price_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$id        = $this->get_field_id( $key );
		$defaults  = $this->get_placeholder_data();

		$data          = wp_parse_args( $data, $defaults );
		$wrapper_class = implode( " ", array( $id, $data['wrapper_class'] ) );
		$depends       = empty( $data['dependency'] ) ? '' : "data-rt-depends='" . wp_json_encode( $data['dependency'] ) . "'";

		ob_start();
		?>
        <tr valign="top" class="<?php echo esc_attr( $wrapper_class ); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html( $this->get_tooltip_html( $data ) ); ?>
                <label for="<?php echo esc_attr( $id ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span>
                    </legend>
                    <input class="rtbr_input_price input-text regular-input <?php echo esc_attr( $data['class'] ); ?>"
                           type="text" name="<?php echo esc_attr( $field_key ); ?>"
                           id="<?php echo esc_attr( $id ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>"
                           value="<?php echo esc_attr( Functions::format_decimal( $this->get_option( $key ) ) ); ?>"
                           placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'],
						true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?> />
					<?php echo $this->get_description_html( $data ); ?>
                </fieldset>
            </td>
        </tr>
		<?php 
		return ob_get_clean();
	}

	/**
	 * Generate Decimal Input HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function generate_decimal_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$id        = $this->get_field_id( $key );
		$defaults  = $this->get_placeholder_data();

		$data          = wp_parse_args( $data, $defaults );
		$wrapper_class = implode( " ", array( $id, $data['wrapper_class'] ) );
		$depends       = empty( $data['dependency'] ) ? '' : "data-rt-depends='" . wp_json_encode( $data['dependency'] ) . "'";

		ob_start();
		?>
        <tr valign="top" class="<?php echo esc_attr( $wrapper_class ); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html( $this->get_tooltip_html( $data ) ); ?>
                <label for="<?php echo esc_attr( $id ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span>
                    </legend>
                    <input class="rtbr_input_decimal input-text regular-input <?php echo esc_attr( $data['class'] ); ?>"
                           type="text" name="<?php echo esc_attr( $field_key ); ?>"
                           id="<?php echo esc_attr( $id ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>"
                           value="<?php echo esc_attr( Functions::format_decimal( $this->get_option( $key ) ) ); ?>"
                           placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'],
						true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?> />
					<?php echo $this->get_description_html( $data ); ?>
                </fieldset>
            </td>
        </tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate Password Input HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function generate_password_html( $key, $data ) {
		$data['type'] = 'password'; 
		return $this->generate_text_html( $key, $data );
	}

	/**
	 * Generate Color Picker Input HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function generate_color_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$id        = $this->get_field_id( $key );
		$defaults  = $this->get_placeholder_data();

		$data          = wp_parse_args( $data, $defaults );
		$wrapper_class = implode( " ", array( $id, $data['wrapper_class'] ) );
		$depends       = empty( $data['dependency'] ) ? '' : "data-rt-depends='" . wp_json_encode( $data['dependency'] ) . "'";

		ob_start();
		?>
        <tr valign="top" class="<?php echo esc_attr( $wrapper_class ); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html( $this->get_tooltip_html( $data ) ); ?>
                <label for="<?php echo esc_attr( $id ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span>
                    </legend>
                    <input class="rtbr-color <?php echo esc_attr( $data['class'] ); ?>" type="text"
                           name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $id ); ?>"
                           style="<?php echo esc_attr( $data['css'] ); ?>"
                           value="<?php echo esc_attr( $this->get_option( $key ) ); ?>"
						<?php disabled( $data['disabled'],
							true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?> />
					<?php echo $this->get_description_html( $data ); ?>
                </fieldset>
            </td>
        </tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate Textarea HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function generate_textarea_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$id        = $this->get_field_id( $key );
		$defaults  = $this->get_placeholder_data(); 
		$data          = wp_parse_args( $data, $defaults );
		$wrapper_class = implode( " ", array( $id, $data['wrapper_class'] ) );
		$depends       = empty( $data['dependency'] ) ? '' : "data-rt-depends='" . wp_json_encode( $data['dependency'] ) . "'";

		ob_start();
		?>
        <tr valign="top" class="<?php echo esc_attr( $wrapper_class ); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html( $this->get_tooltip_html( $data ) ); ?>
                <label for="<?php echo esc_attr( $id ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span>
                    </legend>
                    <textarea rows="3" cols="20" class="input-text wide-input <?php echo esc_attr( $data['class'] ); ?>"
                              type="<?php echo esc_attr( $data['type'] ); ?>"
                              name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $id ); ?>"
                              style="<?php echo esc_attr( $data['css'] ); ?>"
                              placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'],
						true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?>><?php echo esc_textarea( $this->get_option( $key ) ); ?></textarea>
					<?php echo $this->get_description_html( $data ); ?>
                </fieldset>
            </td>
        </tr>
		<?php 
		return ob_get_clean();
	}

	public function generate_wysiwyg_html( $key, $data ) {

		$field_key = $this->get_field_key( $key );
		$id        = $this->get_field_id( $key );
		$defaults  = $this->get_placeholder_data(); 
		$data          = wp_parse_args( $data, $defaults );
		$wrapper_class = implode( " ", array( $id, $data['wrapper_class'] ) );
		$depends       = empty( $data['dependency'] ) ? '' : "data-rt-depends='" . wp_json_encode( $data['dependency'] ) . "'";

		ob_start();
		?>
        <tr valign="top" class="<?php echo esc_attr( $wrapper_class ); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html( $this->get_tooltip_html( $data ) ); ?>
                <label for="<?php echo esc_attr( $id ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span>
                    </legend>
					<?php
					wp_editor(
						htmlspecialchars_decode( $this->get_option( $key ) ),
						$id,
						array(
							'textarea_name' => esc_attr( $field_key ),
							'media_buttons' => false,
							'quicktags'     => true,
							'editor_height' => 250
						)
					);

					echo '<pre>' . $this->get_description_html( $data ) . '</pre>';

					?>
                </fieldset>
            </td>
        </tr>
		<?php 
		return ob_get_clean();
	}

	/**
	 * Generate Checkbox HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function generate_checkbox_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$id        = $this->get_field_id( $key );
		$defaults  = $this->get_placeholder_data();

		$data          = wp_parse_args( $data, $defaults );
		$wrapper_class = implode( " ", array( $id, $data['wrapper_class'] ) );
		$depends       = empty( $data['dependency'] ) ? '' : "data-rt-depends='" . wp_json_encode( $data['dependency'] ) . "'";

		if ( ! $data['label'] ) {
			$data['label'] = $data['title'];
		}

		ob_start();
		?>
        <tr valign="top" class="<?php echo esc_attr( $wrapper_class ); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html( $this->get_tooltip_html( $data ) ); ?>
                <label for="<?php echo esc_attr( $id ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span>
                    </legend>
                    <label for="<?php echo esc_attr( $id ); ?>">
                        <input <?php disabled( $data['disabled'], true ); ?>
                                class="<?php echo esc_attr( $data['class'] ); ?>" type="checkbox"
                                name="<?php echo esc_attr( $field_key ); ?>"
                                id="<?php echo esc_attr( $id ); ?>"
                                style="<?php echo esc_attr( $data['css'] ); ?>"
                                value="yes" <?php checked( $this->get_option( $key ),
							'yes' ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?> /> <?php echo wp_kses_post( $data['label'] ); ?>
                    </label><br/>
					<?php echo $this->get_description_html( $data ); ?>
                </fieldset>
            </td>
        </tr>
		<?php 
		return ob_get_clean();
	}

	/**
	 * Generate Checkbox HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 * @since  1.0.0
	 */

	public function generate_multi_checkbox_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$id        = $this->get_field_id( $key );
		$defaults  = $this->get_placeholder_data(); 
		$data          = wp_parse_args( $data, $defaults );
		$wrapper_class = implode( " ", array( $id, $data['wrapper_class'] ) );
		$depends       = empty( $data['dependency'] ) ? '' : "data-rt-depends='" . wp_json_encode( $data['dependency'] ) . "'";

		if ( ! $data['label'] ) {
			$data['label'] = $data['title'];
		}
		$values = $this->get_option( $key );
		$values = is_array( $values ) ? $values : array();
		ob_start();
		?>
        <tr valign="top" class="<?php echo esc_attr( $wrapper_class ); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html( $this->get_tooltip_html( $data ) ); ?>
                <label for="<?php echo esc_attr( $id ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span>
                    </legend>
					<?php foreach ( (array) $data['options'] as $option_key => $option_value ) : ?>
                        <label for="<?php echo esc_attr( $id . "-" . $option_key ); ?>">
                            <input class="<?php echo esc_attr( $data['class'] ); ?>" type="checkbox"
                                   name="<?php echo esc_attr( $field_key ); ?>[]"
                                   id="<?php echo esc_attr( $id . "-" . $option_key ); ?>"
                                   value="<?php echo esc_attr( $option_key ); ?>"
								<?php checked( in_array( $option_key, $values ) ); ?> />
							<?php echo esc_attr( $option_value ); ?>
                        </label><br/>
					<?php endforeach; ?>
					<?php echo $this->get_description_html( $data ); ?>
                </fieldset>
            </td>
        </tr>
		<?php 
		return ob_get_clean();
	}

	/**
	 * Generate Select HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function generate_select_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$id        = $this->get_field_id( $key );
		$defaults  = $this->get_placeholder_data();

		$data          = wp_parse_args( $data, $defaults );
		$wrapper_class = implode( " ", array( $id, $data['wrapper_class'] ) );
		$depends       = empty( $data['dependency'] ) ? '' : "data-rt-depends='" . wp_json_encode( $data['dependency'] ) . "'";

		ob_start();
		?>
        <tr valign="top" class="<?php echo esc_attr( $wrapper_class ); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html( $this->get_tooltip_html( $data ) ); ?>
                <label for="<?php echo esc_attr( $id ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span>
                    </legend>
                    <select class="select <?php echo esc_attr( $data['class'] ); ?>"
                            name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $id ); ?>"
                            style="<?php echo esc_attr( $data['css'] ); ?>" <?php disabled( $data['disabled'],
						true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?>>
						<?php if ( ! empty( $data['blank'] ) ): ?>
                            <option value="<?php echo esc_attr( $data['blank_value'] ); ?>"><?php echo esc_html( $data['blank_text'] ); ?></option>
						<?php endif; ?>
						<?php foreach ( (array) $data['options'] as $option_key => $option_value ) : ?>
                            <option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key,
								esc_attr( $this->get_option( $key ) ) ); ?>><?php echo esc_html( $option_value ); ?></option>
						<?php endforeach; ?>
                    </select>
					<?php echo $this->get_description_html( $data ); ?>
                </fieldset>
            </td>
        </tr>
		<?php 
		return ob_get_clean();
	}

	/**
	 * Generate Radio HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function generate_radio_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$id        = $this->get_field_id( $key );
		$defaults  = $this->get_placeholder_data(); 
		$data          = wp_parse_args( $data, $defaults );
		$wrapper_class = implode( " ", array( $id, $data['wrapper_class'] ) );
		$depends       = empty( $data['dependency'] ) ? '' : "data-rt-depends='" . wp_json_encode( $data['dependency'] ) . "'";

		ob_start();
		?>
        <tr valign="top" class="<?php echo esc_attr( $wrapper_class ); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html( $this->get_tooltip_html( $data ) ); ?>
                <label for="<?php echo esc_attr( $id ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span>
                    </legend>
					<?php foreach ( (array) $data['options'] as $option_key => $option_value ) : ?>
                        <label><input type="radio" name="<?php echo esc_attr( $field_key ); ?>"
                                      value="<?php echo esc_attr( $option_key ) ?>"
								<?php checked( $option_key,
									$this->get_option( $key ) ) ?> > <?php echo wp_kses_post( $option_value ) ?></label>
                        <br>
					<?php endforeach; ?>
					<?php echo $this->get_description_html( $data ); ?>
                </fieldset>
            </td>
        </tr>
		<?php 
		return ob_get_clean();
	}

	/**
	 * Generate Multiselect HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function generate_multiselect_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$id        = $this->get_field_id( $key );
		$defaults  = $this->get_placeholder_data(); 
		$data          = wp_parse_args( $data, $defaults );
		$wrapper_class = implode( " ", array( $id, $data['wrapper_class'] ) );
		$depends       = empty( $data['dependency'] ) ? '' : "data-rt-depends='" . wp_json_encode( $data['dependency'] ) . "'";
		$value         = (array) $this->get_option( $key, array() );

		ob_start();
		?>
        <tr valign="top" class="<?php echo esc_attr( $wrapper_class ); ?>" <?php echo wp_kses_post($depends); ?>>
            <th scope="row" class="title-desc">
				<?php echo esc_html( $this->get_tooltip_html( $data ) ); ?>
                <label for="<?php echo esc_attr( $id ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
            </th>
            <td class="form-input">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span>
                    </legend>
                    <select multiple="multiple" class="multiselect <?php echo esc_attr( $data['class'] ); ?>"
                            name="<?php echo esc_attr( $field_key ); ?>[]" id="<?php echo esc_attr( $id ); ?>"
                            style="<?php echo esc_attr( $data['css'] ); ?>" <?php disabled( $data['disabled'],
						true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?>>
						<?php foreach ( (array) $data['options'] as $option_key => $option_value ) : ?>
                            <option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( in_array( $option_key,
								$value ), true ); ?>><?php echo esc_html( $option_value ); ?></option>
						<?php endforeach; ?>
                    </select>
					<?php echo $this->get_description_html( $data ); ?>
					<?php if ( $data['select_buttons'] ) : ?>
                        <br/><a class="select_all button"
                                href="#"><?php _e( 'Select all', 'business-reviews-wp' ); ?></a> <a
                                class="select_none button"
                                href="#"><?php _e( 'Select none', 'business-reviews-wp' ); ?></a>
					<?php endif; ?>
                </fieldset>
            </td>
        </tr>
		<?php 
		return ob_get_clean();
	}

	/**
	 * Generate Title HTML.
	 *
	 * @param mixed $key
	 * @param mixed $data
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public function generate_title_html( $key, $data ) {
		$field_key = $this->get_field_key( $key );
		$id        = $this->get_field_id( $key );
		$defaults  = array(
			'title' => '',
			'class' => '',
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();
		?>
        </table>
        <h3 class="rtbr-settings-sub-title <?php echo esc_attr( $data['class'] ); ?>"
            id="<?php echo esc_attr( $id ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></h3>
		<?php if ( ! empty( $data['description'] ) ) : ?>
            <p><?php echo wp_kses_post( $data['description'] ); ?></p>
		<?php endif; ?>
        <table class="form-table">
		<?php 
		return ob_get_clean();
	}

	/**
	 * Validate Text Field.
	 *
	 * Make sure the data is escaped correctly, etc.
	 *
	 * @param string $key Field key
	 * @param string|null $value Posted Value
	 *
	 * @return string
	 */
	public function validate_text_field( $key, $value ) {
		$value = is_null( $value ) ? '' : $value; 
		return wp_kses_post( trim( stripslashes( $value ) ) );
	}

	/**
	 * Validate Price Field.
	 *
	 * Make sure the data is escaped correctly, etc.
	 *
	 * @param string $key
	 * @param string|null $value Posted Value
	 *
	 * @return string
	 */
	public function validate_price_field( $key, $value ) {
		$value = is_null( $value ) ? '' : $value; 
		return ( '' === $value ) ? '' : Functions::get_formatted_amount( trim( stripslashes( $value ) ), true );
	}

	/**
	 * Validate Decimal Field.
	 *
	 * Make sure the data is escaped correctly, etc.
	 *
	 * @param string $key
	 * @param string|null $value Posted Value
	 *
	 * @return string
	 */
	public function validate_decimal_field( $key, $value ) {
		$value = is_null( $value ) ? '' : $value; 
		return ( '' === $value ) ? '' : Functions::get_formatted_amount( trim( stripslashes( $value ) ), true );
	}

	/**
	 * Validate Password Field. No input sanitization is used to avoid corrupting passwords.
	 *
	 * @param string $key
	 * @param string|null $value Posted Value
	 *
	 * @return string
	 */
	public function validate_password_field( $key, $value ) {
		$value = is_null( $value ) ? '' : $value; 
		return trim( stripslashes( $value ) );
	}

	/**
	 * Validate Textarea Field.
	 *
	 * @param string $key
	 * @param string|null $value Posted Value
	 *
	 * @return string
	 */
	public function validate_textarea_field( $key, $value ) {
		$value = is_null( $value ) ? '' : $value;

		return wp_kses( trim( stripslashes( $value ) ),
			array_merge(
				array(
					'iframe' => array( 'src' => true, 'style' => true, 'id' => true, 'class' => true ),
				),
				wp_kses_allowed_html( 'post' )
			)
		);
	}

	public function validate_wysiwyg_field( $key, $value ) {
		$value = is_null( $value ) ? '' : $value;

		return wp_kses( trim( stripslashes( $value ) ),
			array_merge(
				array(
					'iframe' => array( 'src' => true, 'style' => true, 'id' => true, 'class' => true ),
				),
				wp_kses_allowed_html( 'post' )
			)
		);
	}

	/**
	 * Validate Checkbox Field.
	 *
	 * If not set, return "no", otherwise return "yes".
	 *
	 * @param string $key
	 * @param string|null $value Posted Value
	 *
	 * @return string
	 */
	public function validate_checkbox_field( $key, $value ) {
		return ! is_null( $value ) ? 'yes' : 'no';
	}

	/**
	 * Validate Select Field.
	 *
	 * @param string $key
	 * @param string $value Posted Value
	 *
	 * @return string
	 */
	public function validate_select_field( $key, $value ) {
		$value = is_null( $value ) ? '' : $value; 
		return Functions::clean( stripslashes( $value ) );
	}

	/**
	 * Validate Multiselect Field.
	 *
	 * @param string $key
	 * @param string $value Posted Value
	 *
	 * @return string|array
	 */
	public function validate_multiselect_field( $key, $value ) {
		return is_array( $value ) ? array_map( array( Functions::class, 'clean' ),
			array_map( 'stripslashes', $value ) ) : '';
	}

	public function validate_image_size_field( $key, $value ) {
		return is_array( $value ) ? array_map( array( Functions::class, 'clean' ),
			array_map( 'stripslashes', $value ) ) : '';
	}

	public function validate_image_field( $key, $value ) {
		return $value = is_null( $value ) ? '' : absint( $value );
	}

	/**
	 * Validate Multiselect Field.
	 *
	 * @param string $key
	 * @param string $value Posted Value
	 *
	 * @return string|array
	 */
	public function validate_multi_checkbox_field( $key, $value ) {
		return is_array( $value ) ? array_map( array( Functions::class, 'clean' ),
			array_map( 'stripslashes', $value ) ) : '';
	} 

	private function get_placeholder_data() {
		return array(
			'title'             => '',
			'label'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'placeholder'       => '',
			'blank'             => true,
			'blank_text'        => esc_html__( 'Select one', 'business-reviews-wp' ),
			'blank_value'       => '',
			'type'              => 'text',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array(),
			'wrapper_class'     => '',
			'options'           => array(),
			'select_buttons'    => false,
			'dependency'        => '',
		);
	} 
}