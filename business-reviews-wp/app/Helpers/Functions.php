<?php

namespace Rtbr\Helpers;
 
use Rtbr\Models\Field;

class Functions { 

    public static function verify_nonce() {
        $nonce = self::get_nonce();
        $nonceText = rtbr()->getNonceText();
        if ( wp_verify_nonce($nonce, $nonceText) ) {
            return true;
        }

        return false;
    }

    public static function get_nonce() {
        return isset($_REQUEST[rtbr()->getNonceId()]) ? sanitize_text_field( $_REQUEST[rtbr()->getNonceId()] ) : null;
    } 

    public static function locate_template($name) {
        // Look within passed path within the theme - this is priority.
        $template = []; 

        $template[] = rtbr()->get_template_path() . $name . ".php";

        if (!$template_file = locate_template(apply_filters('rtbr_locate_template_names', $template))) {
            $template_file = RTBR_PATH . "templates/$name.php";
        }

        return apply_filters('rtbr_locate_template', $template_file, $name);
    }

    /**
     * Get template part (for templates like the shop-loop).
     *
     * RTBR_TEMPLATE_DEBUG_MODE will prevent overrides in themes from taking priority.
     *
     * @param mixed  $slug Template slug.
     * @param string $name Template name (default: '').
     */
    static function get_template_part($slug, $args = null) { 
        // load template from theme if exist
        $template = RTBR_TEMPLATE_DEBUG_MODE ? '' : locate_template(
            array(
                "{$slug}.php",
                rtbr()->get_template_path() . "{$slug}.php"
            )
        ); 

        // load template from pro plugin if exist
        if ( !$template && function_exists('rtbrp') ) { 
            $fallback = rtbr()->plugin_path() . "-pro" . "/templates/{$slug}.php";  
            $template = file_exists($fallback) ? $fallback : '';
        }

        // load template from current plugin if exist
        if ( !$template ) { 
            $fallback = rtbr()->plugin_path() . "/templates/{$slug}.php";  
            $template = file_exists($fallback) ? $fallback : '';
        }

        // Allow 3rd party plugins to filter template file from their plugin.
        $template = apply_filters('rtbr_get_template_part', $template, $slug);

        if ( $template ) {
            if ( !empty($args) && is_array($args) ) {
                extract($args); // @codingStandardsIgnoreLine
            }

            // load_template($template, false, $args);
            include $template;
        }
    }

    static function get_template($fileName, $args = null) {

        if (!empty($args) && is_array($args)) {
            extract($args); // @codingStandardsIgnoreLine
        }

        $located = self::locate_template($fileName); 

        if (!file_exists($located)) {
            /* translators: %s template */
            self::doing_it_wrong(__FUNCTION__, sprintf(__('%s does not exist.', 'business-reviews-wp'), '<code>' . $located . '</code>'), '1.0');

            return;
        }

        // Allow 3rd party plugin filter template file from their plugin.
        $located = apply_filters('rtbr_get_template', $located, $fileName, $args);

        do_action('rtbr_before_template_part', $fileName, $located, $args);

        include $located;

        do_action('rtbr_after_template_part', $fileName, $located, $args);

    }

    /**
     * @param $id
     *
     * @return bool|mixed|void
     */

    public static function get_default_placeholder_url() {
        $placeholder_url = RTBR_URL . '/assets/imgs/placeholder.jpg';
        return apply_filters('rtbr_default_placeholder_url', $placeholder_url);
    }

    public static function array_insert(&$array, $position, $insert_array) {
		$first_array = array_splice($array, 0, $position + 1);
		$array       = array_merge($first_array, $insert_array, $array);
	}

    /**
     * @param $id
     *
     * @return bool|mixed|void
     */
    static function get_option($id) {
        if (!$id) {
            return false;
        }
        $settings = get_option($id, array());

        return apply_filters($id, $settings);
    }

    /**
     * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
     * Non-scalar values are ignored.
     *
     * @param string|array $var Data to sanitize.
     *
     * @return string|array
     */
    static function clean($var) {
        if ( is_array($var) ) {
            return array_map(array(self::class, 'clean'), $var);
        } else {
            return is_scalar($var) ? sanitize_text_field($var) : $var;
        }
    }

    /**
     * @param $id
     *
     * @return bool|mixed|void
     */
    function fieldGenerator($fields = array(), $multi = false) {
        $html = null;
        if (is_array($fields) && !empty($fields)) {
            $rtField = new Field();
            if ($multi) {
                foreach ($fields as $field) {
                    $html .= $rtField->Field($field);
                }
            } else {
                $html .= $rtField->Field($fields);
            }
        } 
        return $html;
    } 

    /**
     *  Business Reviews Star Icon
     *
     * @package Business Reviews
     * @since 1.0
     */
    public static function review_stars($rating) {
        ob_start(); 
        foreach ( array(1,2,3,4,5) as $val ) {
            $score = $rating - $val;
            if ($score >= 0) { ?>
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="16" height="16" viewBox="0 0 1792 1792"><path d="M1728 647q0 22-26 48l-363 354 86 500q1 7 1 20 0 21-10.5 35.5t-30.5 14.5q-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z" ></path></svg><?php
            } else if ($score > -1 && $score < 0) { ?>
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="16" height="16" viewBox="0 0 1792 1792"><path d="M1250 957l257-250-356-52-66-10-30-60-159-322v963l59 31 318 168-60-355-12-66zm452-262l-363 354 86 500q5 33-6 51.5t-34 18.5q-17 0-40-12l-449-236-449 236q-23 12-40 12-23 0-34-18.5t-6-51.5l86-500-364-354q-32-32-23-59.5t54-34.5l502-73 225-455q20-41 49-41 28 0 49 41l225 455 502 73q45 7 54 34.5t-24 59.5z" ></path></svg><?php
            } else { ?>
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="16" height="16" viewBox="0 0 1792 1792"><path d="M1201 1004l306-297-422-62-189-382-189 382-422 62 306 297-73 421 378-199 377 199zm527-357q0 22-26 48l-363 354 86 500q1 7 1 20 0 50-41 50-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z" fill="#ccc"></path></svg><?php
            }
        } 
        return ob_get_clean();
    }

    public static function generatorShortCodeCss($sc_id) {
		global $wp_filesystem;
		// Initialize the WP filesystem, no more using 'file-put-contents' function
		if ( empty($wp_filesystem) ) {
			require_once (ABSPATH . '/wp-admin/includes/file.php');
			WP_Filesystem();
		}
		
		$upload_dir = wp_upload_dir(); 
		$upload_basedir = $upload_dir['basedir'] ;
		$cssFile = $upload_basedir . '/business-reviews/sc.css'; 
        
		if ( $css = rtbr()->render('sc-css', compact('sc_id'), true) ) {  
			$css = sprintf('/*sc-%2$d-start*/%1$s/*sc-%2$d-end*/', $css, $sc_id);
			if ( file_exists($cssFile) && ($oldCss = $wp_filesystem->get_contents($cssFile)) ) {
				if ( strpos($oldCss, '/*sc-' . $sc_id . '-start') !== false ) {
					$oldCss = preg_replace('/\/\*sc-' . $sc_id . '-start[\s\S]+?sc-' . $sc_id . '-end\*\//', '', $oldCss);
					$oldCss = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "", $oldCss);
				}
				$css = $oldCss . $css;				
			} else if ( ! file_exists( $cssFile ) ) {
				$upload_basedir_trailingslashit = trailingslashit( $upload_basedir ); 
				$wp_filesystem->mkdir( $upload_basedir_trailingslashit. 'business-reviews' );
			}
			if( ! $wp_filesystem->put_contents( $cssFile, $css  ) ){
				error_log(print_r('Business Reviews: Error Generated css file ',true));
			}
		} 
	}

    /**
     *  Shortcode Style Generate
     *
     * @package Business Reviews
     * @since 1.0
     */
    public static function shortcode_style( $sc_id, $style_tag = false ) { 
        $sc_meta = []; 
        
        $sc_meta['layout'] = isset( $_REQUEST['layout'] ) ? sanitize_text_field( $_REQUEST['layout'] ) : get_post_meta( $sc_id, 'layout', true ); 
        $sc_meta['floating_badge_pos'] = isset( $_REQUEST['floating_badge_pos'] ) ? sanitize_text_field( $_REQUEST['floating_badge_pos'] ) : get_post_meta( $sc_id, 'floating_badge_pos', true );
        $sc_meta['width'] = isset( $_REQUEST['width'] ) ? sanitize_text_field( $_REQUEST['width'] ) : get_post_meta( $sc_id, 'width', true );
		
		$sc_meta['badge_bg'] = isset( $_REQUEST['badge_bg'] ) ? sanitize_hex_color( $_REQUEST['badge_bg'] ) : get_post_meta( $sc_id, 'badge_bg', true ); 
        $sc_meta['business_title'] = isset( $_REQUEST['business_title'] ) ? array_map( 'sanitize_text_field', $_REQUEST['business_title'] ) : get_post_meta( $sc_id, 'business_title', true ); 
		$sc_meta['business_title_hover'] = isset( $_REQUEST['business_title_hover'] ) ? array_map( 'sanitize_text_field', $_REQUEST['business_title_hover'] ) : get_post_meta( $sc_id, 'business_title_hover', true );
		$sc_meta['author_name'] = isset( $_REQUEST['author_name'] ) ? array_map( 'sanitize_text_field', $_REQUEST['author_name'] ) : get_post_meta( $sc_id, 'author_name', true ); 
		$sc_meta['author_name_hover'] = isset( $_REQUEST['author_name_hover'] ) ? array_map( 'sanitize_text_field', $_REQUEST['author_name_hover'] ) : get_post_meta( $sc_id, 'author_name_hover', true );
		$sc_meta['review_text'] = isset( $_REQUEST['review_text'] ) ? array_map( 'sanitize_text_field', $_REQUEST['review_text'] ) : get_post_meta( $sc_id, 'review_text', true );  
		$sc_meta['time_ago_text'] = isset( $_REQUEST['time_ago_text'] ) ? array_map( 'sanitize_text_field', $_REQUEST['time_ago_text'] ) : get_post_meta( $sc_id, 'time_ago_text', true );   
		$sc_meta['total_review_text'] = isset( $_REQUEST['total_review_text'] ) ? array_map( 'sanitize_text_field', $_REQUEST['total_review_text'] ) : get_post_meta( $sc_id, 'total_review_text', true );   
		$sc_meta['powered_by_text'] = isset( $_REQUEST['powered_by_text'] ) ? array_map( 'sanitize_text_field', $_REQUEST['powered_by_text'] ) : get_post_meta( $sc_id, 'powered_by_text', true );  
		
        $sc_meta['google_star_color'] = isset( $_REQUEST['google_star_color'] ) ? sanitize_hex_color( $_REQUEST['google_star_color'] ) : get_post_meta( $sc_id, 'google_star_color', true );
		$sc_meta['facebook_star_color'] = isset( $_REQUEST['facebook_star_color'] ) ? sanitize_hex_color( $_REQUEST['facebook_star_color'] ) : get_post_meta( $sc_id, 'facebook_star_color', true );
		$sc_meta['yelp_star_color'] = isset( $_REQUEST['yelp_star_color'] ) ? sanitize_hex_color( $_REQUEST['yelp_star_color'] ) : get_post_meta( $sc_id, 'yelp_star_color', true );
		$sc_meta['img_border_radius'] = isset( $_REQUEST['img_border_radius'] ) ? sanitize_text_field( $_REQUEST['img_border_radius'] ) : get_post_meta( $sc_id, 'img_border_radius', true );
		$sc_meta['review_border_color'] = isset( $_REQUEST['review_border_color'] ) ? sanitize_hex_color( $_REQUEST['review_border_color'] ) : get_post_meta( $sc_id, 'review_border_color', true );
		$sc_meta['review_bg_color'] = isset( $_REQUEST['review_bg_color'] ) ? sanitize_hex_color( $_REQUEST['review_bg_color'] ) : get_post_meta( $sc_id, 'review_bg_color', true );

        $sc_meta['btn_text_color'] = isset( $_REQUEST['btn_text_color'] ) ? sanitize_hex_color( $_REQUEST['btn_text_color'] ) : get_post_meta( $sc_id, 'btn_text_color', true );
        $sc_meta['btn_bg_color'] = isset( $_REQUEST['btn_bg_color'] ) ? sanitize_hex_color( $_REQUEST['btn_bg_color'] ) : get_post_meta( $sc_id, 'btn_bg_color', true );
        $sc_meta['btn_text_hover_color'] = isset( $_REQUEST['btn_text_hover_color'] ) ? sanitize_hex_color( $_REQUEST['btn_text_hover_color'] ) : get_post_meta( $sc_id, 'btn_text_hover_color', true );
        $sc_meta['btn_bg_hover_color'] = isset( $_REQUEST['btn_bg_hover_color'] ) ? sanitize_hex_color( $_REQUEST['btn_bg_hover_color'] ) : get_post_meta( $sc_id, 'btn_bg_hover_color', true );
        $sc_meta['btn_border_radius'] = isset( $_REQUEST['btn_border_radius'] ) ? sanitize_text_field( $_REQUEST['btn_border_radius'] ) : get_post_meta( $sc_id, 'btn_border_radius', true );
        
        $css  = null;
        if ( $style_tag ) {
            $css .= "<style>";
        }
        if ( $value = $sc_meta['width'] ) {
			$css  .= "#review-list-content-{$sc_id} { ";
			$css .= "width:" . $value . ";";
			$css .= "}";
		}

		$title = ( ! empty( $sc_meta['business_title'] ) ? $sc_meta['business_title'] : array() );
		if ( ! empty( $title ) ) {
			$title_color     = ( ! empty( $title['color'] ) ? $title['color'] : null );
			$title_size      = ( ! empty( $title['size'] ) ? absint( $title['size'] ) : null );
			$title_weight    = ( ! empty( $title['weight'] ) ? $title['weight'] : null );
			$title_alignment = ( ! empty( $title['align'] ) ? $title['align'] : null ); 
			$css             .= "#review-list-content-{$sc_id} .rt-review-top .rt-author-title a, #review-list-content-{$sc_id} .rt-badge-tab-style .rt-review-top .rt-author-title { ";
			if ( $title_color ) {
				$css .= "color:" . $title_color . ";";
			}
			if ( $title_size ) {
				$css .= "font-size:" . $title_size . "px;";
			}
			if ( $title_weight ) {
				$css .= "font-weight:" . $title_weight . ";";
			}
			if ( $title_alignment ) {
				$css .= "text-align:" . $title_alignment . ";";
			}
			$css .= "}";  
        }  

        $title = ( ! empty( $sc_meta['business_title_hover'] ) ? $sc_meta['business_title_hover'] : array() );
		if ( ! empty( $title ) ) {
			$title_color     = ( ! empty( $title['color'] ) ? $title['color'] : null );
			$title_size      = ( ! empty( $title['size'] ) ? absint( $title['size'] ) : null );
			$title_weight    = ( ! empty( $title['weight'] ) ? $title['weight'] : null );
			$title_alignment = ( ! empty( $title['align'] ) ? $title['align'] : null ); 
			$css             .= "#review-list-content-{$sc_id} .rt-review-top .rt-author-title a:hover { ";
			if ( $title_color ) {
				$css .= "color:" . $title_color . ";";
			}
			if ( $title_size ) {
				$css .= "font-size:" . $title_size . "px;";
			}
			if ( $title_weight ) {
				$css .= "font-weight:" . $title_weight . ";";
			}
			if ( $title_alignment ) {
				$css .= "text-align:" . $title_alignment . ";";
			}
			$css .= "}";  
        } 
        
        if ( $value = $sc_meta['badge_bg'] ) {
            $css  .= "#review-list-content-{$sc_id} .rt-badge-tab-style .rt-review-top { ";
            $css .= "background:" . $value . ";";
            $css .= "}";
        }  

		$title = ( ! empty( $sc_meta['author_name'] ) ? $sc_meta['author_name'] : array() );
		if ( ! empty( $title ) ) {
			$title_color     = ( ! empty( $title['color'] ) ? $title['color'] : null );
			$title_size      = ( ! empty( $title['size'] ) ? absint( $title['size'] ) : null );
			$title_weight    = ( ! empty( $title['weight'] ) ? $title['weight'] : null );
			$title_alignment = ( ! empty( $title['align'] ) ? $title['align'] : null ); 
			$css             .= "#review-list-content-{$sc_id} .rt-author-title a { ";
			if ( $title_color ) {
				$css .= "color:" . $title_color . ";";
			}
			if ( $title_size ) {
				$css .= "font-size:" . $title_size . "px;";
			}
			if ( $title_weight ) {
				$css .= "font-weight:" . $title_weight . ";";
			}
			if ( $title_alignment ) {
				$css .= "text-align:" . $title_alignment . ";";
			}
			$css .= "}";  
        } 
        
        $title = ( ! empty( $sc_meta['author_name_hover'] ) ? $sc_meta['author_name_hover'] : array() );
		if ( ! empty( $title ) ) {
			$title_color     = ( ! empty( $title['color'] ) ? $title['color'] : null );
			$title_size      = ( ! empty( $title['size'] ) ? absint( $title['size'] ) : null );
			$title_weight    = ( ! empty( $title['weight'] ) ? $title['weight'] : null );
			$title_alignment = ( ! empty( $title['align'] ) ? $title['align'] : null ); 
			$css             .= "#review-list-content-{$sc_id} .rt-author-title a:hover { ";
			if ( $title_color ) {
				$css .= "color:" . $title_color . ";";
			}
			if ( $title_size ) {
				$css .= "font-size:" . $title_size . "px;";
			}
			if ( $title_weight ) {
				$css .= "font-weight:" . $title_weight . ";";
			}
			if ( $title_alignment ) {
				$css .= "text-align:" . $title_alignment . ";";
			}
			$css .= "}";  
		} 
 

		$title = ( ! empty( $sc_meta['review_text'] ) ? $sc_meta['review_text'] : array() ); 
		if ( ! empty( $title ) ) {
			$title_color     = ( ! empty( $title['color'] ) ? $title['color'] : null );
			$title_size      = ( ! empty( $title['size'] ) ? absint( $title['size'] ) : null );
			$title_weight    = ( ! empty( $title['weight'] ) ? $title['weight'] : null );
			$title_alignment = ( ! empty( $title['align'] ) ? $title['align'] : null ); 
			$css             .= "#review-list-content-{$sc_id} p { ";
			if ( $title_color ) {
				$css .= "color:" . $title_color . ";";
			}
			if ( $title_size ) {
				$css .= "font-size:" . $title_size . "px;";
			}
			if ( $title_weight ) {
				$css .= "font-weight:" . $title_weight . ";";
			}
			if ( $title_alignment ) {
				$css .= "text-align:" . $title_alignment . ";";
			}
			$css .= "}";  
        }  
        
        $title = ( ! empty( $sc_meta['rating_text'] ) ? $sc_meta['rating_text'] : array() );  
		if ( ! empty( $title ) ) {
			$title_color     = ( ! empty( $title['color'] ) ? $title['color'] : null );
			$title_size      = ( ! empty( $title['size'] ) ? absint( $title['size'] ) : null );
			$title_weight    = ( ! empty( $title['weight'] ) ? $title['weight'] : null );
			$title_alignment = ( ! empty( $title['align'] ) ? $title['align'] : null ); 
			$css             .= "#review-list-content-{$sc_id} .rating-text { ";
			if ( $title_color ) {
				$css .= "color:" . $title_color . ";";
			}
			if ( $title_size ) {
				$css .= "font-size:" . $title_size . "px;";
			}
			if ( $title_weight ) {
				$css .= "font-weight:" . $title_weight . ";";
			}
			if ( $title_alignment ) {
				$css .= "text-align:" . $title_alignment . ";";
			}
			$css .= "}";  
        } 
        
        $title = ( ! empty( $sc_meta['time_ago_text'] ) ? $sc_meta['time_ago_text'] : array() );  
		if ( ! empty( $title ) ) {
			$title_color     = ( ! empty( $title['color'] ) ? $title['color'] : null );
			$title_size      = ( ! empty( $title['size'] ) ? absint( $title['size'] ) : null );
			$title_weight    = ( ! empty( $title['weight'] ) ? $title['weight'] : null );
			$title_alignment = ( ! empty( $title['align'] ) ? $title['align'] : null ); 
			$css             .= "#review-list-content-{$sc_id} .rt-time-ago { ";
			if ( $title_color ) {
				$css .= "color:" . $title_color . ";";
			}
			if ( $title_size ) {
				$css .= "font-size:" . $title_size . "px;";
			}
			if ( $title_weight ) {
				$css .= "font-weight:" . $title_weight . ";";
			}
			if ( $title_alignment ) {
				$css .= "text-align:" . $title_alignment . ";";
			}
			$css .= "}";  
		}   

        $title = ( ! empty( $sc_meta['total_review_text'] ) ? $sc_meta['total_review_text'] : array() );  
		if ( ! empty( $title ) ) {
			$title_color     = ( ! empty( $title['color'] ) ? $title['color'] : null );
			$title_size      = ( ! empty( $title['size'] ) ? absint( $title['size'] ) : null );
			$title_weight    = ( ! empty( $title['weight'] ) ? $title['weight'] : null );
			$title_alignment = ( ! empty( $title['align'] ) ? $title['align'] : null ); 
			$css             .= "#review-list-content-{$sc_id} .rating-text span { ";
			if ( $title_color ) {
				$css .= "color:" . $title_color . ";";
			}
			if ( $title_size ) {
				$css .= "font-size:" . $title_size . "px;";
			}
			if ( $title_weight ) {
				$css .= "font-weight:" . $title_weight . ";";
			}
			if ( $title_alignment ) {
				$css .= "text-align:" . $title_alignment . ";";
			}
			$css .= "}";  
		} 

        $title = ( ! empty( $sc_meta['powered_by_text'] ) ? $sc_meta['powered_by_text'] : array() );  
		if ( ! empty( $title ) ) {
			$title_color     = ( ! empty( $title['color'] ) ? $title['color'] : null );
			$title_size      = ( ! empty( $title['size'] ) ? absint( $title['size'] ) : null );
			$title_weight    = ( ! empty( $title['weight'] ) ? $title['weight'] : null );
			$title_alignment = ( ! empty( $title['align'] ) ? $title['align'] : null ); 
			$css             .= "#review-list-content-{$sc_id} .powerd-by { ";
			if ( $title_color ) {
				$css .= "color:" . $title_color . ";";
			}
			if ( $title_size ) {
				$css .= "font-size:" . $title_size . "px;";
			}
			if ( $title_weight ) {
				$css .= "font-weight:" . $title_weight . ";";
			}
			if ( $title_alignment ) {
				$css .= "text-align:" . $title_alignment . ";";
			}
			$css .= "}";  
		}
        
		if ( $value = $sc_meta['google_star_color'] ) {
			$css  .= "#review-list-content-{$sc_id} .google-rating svg { ";
			$css .= "fill:" . $value . ";";
			$css .= "}";
        }
        
        if ( $value = $sc_meta['facebook_star_color'] ) {
			$css  .= "#review-list-content-{$sc_id} .facebook-rating svg { ";
			$css .= "fill:" . $value . ";";
			$css .= "}";
        }
        
        if ( $value = $sc_meta['yelp_star_color'] ) {
			$css  .= "#review-list-content-{$sc_id} .yelp-rating svg { "; 
			$css .= "background-color:" . $value . ";";
			$css .= "}";
		}

        // review background and border color  
        if ( $value = $sc_meta['review_bg_color'] ) {
            switch ( $sc_meta['layout'] ) {
                case 'grid-five':
                        $css  .= "#review-list-content-{$sc_id} .rt-grid-view-style-5 .rt-media { ";
                        $css .= "background-color:" . $value . ";";
                        $css .= "}";

                        $css  .= "#review-list-content-{$sc_id} .rt-grid-view-style-5 .rt-media:before { ";
                        $css .= "border-bottom-color:" . $value . ";";
                        $css .= "}";
                    break;
                
                case 'grid-six':
                case 'grid-seven': 
                        $css  .= "#review-list-content-{$sc_id} .rt-media-body { ";
                        $css .= "background-color:" . $value . ";";
                        $css .= "}";

                        $css  .= "#review-list-content-{$sc_id} .rt-media-body:after{ ";
                        $css .= "border-right-color:" . $value . ";";
                        $css .= "}";
                    break; 
                
                case 'list-two':
                    break;

                case 'slider-two':
                    $css  .= "#review-list-content-{$sc_id} .rt-slide-style-2 .rt-media .rt-media-body { ";
                    $css .= "background-color:" . $value . ";";
                    $css .= "}";

                    $css  .= "#review-list-content-{$sc_id} .rt-slide-style-2 .rt-media .rt-media-body:before { ";
                    $css .= "border-left-color:" . $value . ";";
                    $css .= "}";
                break;

                case 'badge-two':
                    $css  .= "#review-list-content-{$sc_id} .rt-badge-style-2 { ";
                    $css .= "background-color:" . $value . ";"; 
                    $css .= "}"; 

                    $title = ( ! empty( $sc_meta['business_title'] ) ? $sc_meta['business_title'] : array() );
                    if ( ! empty( $title ) ) {
                        $title_color     = ( ! empty( $title['color'] ) ? $title['color'] : null ); 
                        $css             .= "#review-list-content-{$sc_id} .rt-badge-style-2:before { ";
                        if ( $title_color ) {
                            $css .= "background-color:" . $title_color . ";";
                        } 
                        $css .= "}";  
                    } 
                break;
                
                default:
                    $css  .= "#review-list-content-{$sc_id} .rt-grid-view-style, #review-list-content-{$sc_id} .rt-slide-style .rt-slick-slide, #review-list-content-{$sc_id} .rt-list-view-style .rt-media, #review-list-content-{$sc_id} .rt-badge-tab-style .rt-review-top { ";
                    $css .= "background:" . $value . ";";
                    $css .= "}";
                    break;
            } 
        }  

        if ( $value = $sc_meta['review_border_color'] ) { 
            switch ( $sc_meta['layout'] ) {
                case 'grid-five':
                    $css  .= "#review-list-content-{$sc_id} .rt-grid-view-style-5 .rt-media { ";
                    $css .= "border-color:" . $value . ";";
                    $css .= "}";

                    $css  .= "#review-list-content-{$sc_id} .rt-grid-view-style-5 .rt-media:after { ";
                    $css .= "border-bottom-color:" . $value . ";";
                    $css .= "}";
                    break;

                case 'grid-six':
                case 'grid-seven':
                    break;
                
                case 'list-two':
                    $css  .= "#review-list-content-{$sc_id} .rt-list-view-style-2 .rt-media { ";
                    $css .= "border-bottom-color:" . $value . ";";
                    $css .= "}";
                    break;

                case 'badge-one':
                    $css  .= "#review-list-content-{$sc_id} .rt-badge-style .rt-media { ";
                    $css .= "border-bottom-color:" . $value . ";";
                    $css .= "}";
                    break;

                case 'badge-two':
                    $css  .= "#review-list-content-{$sc_id} .rt-badge-style-2, #review-list-content-{$sc_id} .rt-badge-style .rt-media { ";
                    $css .= "border-color:" . $value . ";";
                    $css .= "}";
                    break;

                default:
                    $css  .= "#review-list-content-{$sc_id} .rt-grid-view-style { ";
                    $css .= "border-color:" . $value . ";";
                    $css .= "}";
                    break;
            }
        }
        
        // pagination button color
        if ( $value = $sc_meta['btn_text_color'] ) {
            $css  .= "#review-list-content-{$sc_id} .rtbr-pagination.rt-loadmore-btn a { ";
            $css .= "color:" . $value . ";";
            $css .= "}";
        }  
        if ( $value = $sc_meta['btn_text_hover_color'] ) {
            $css  .= "#review-list-content-{$sc_id} .rtbr-pagination.rt-loadmore-btn a:hover { ";
            $css .= "color:" . $value . ";";
            $css .= "}";
        }  
        if ( $value = $sc_meta['btn_bg_color'] ) {
            $css  .= "#review-list-content-{$sc_id} .rtbr-pagination.rt-loadmore-btn a { ";
            $css .= "background:" . $value . ";";
            $css .= "}";
        }  
        if ( $value = $sc_meta['btn_bg_hover_color'] ) {
            $css  .= "#review-list-content-{$sc_id} .rtbr-pagination.rt-loadmore-btn a:hover { ";
            $css .= "background:" . $value . ";";
            $css .= "}";
        }   
        if ( $value = $sc_meta['btn_border_radius'] ) {
            $css  .= "#review-list-content-{$sc_id} .rtbr-pagination.rt-loadmore-btn a { ";
            $css .= "border-radius:" . $value . ";";
            $css .= "}";
        }  

        // slider two
        if ( $value = $sc_meta['btn_bg_color'] ) {
            $css  .= "#review-list-content-{$sc_id} .rt-slick-next:before, .rt-slick-prev:before { ";
            $css .= "color:" . $value . " !important;";
            $css .= "}";
        }  

        //isotope tab color
        if ( $value = $sc_meta['btn_text_color'] ) {
            $css  .= "#review-list-content-{$sc_id} .rt-isotope-classes-tab .nav-item { ";
            $css .= "color:" . $value . ";";
            $css .= "}";
        }  
        if ( $value = $sc_meta['btn_text_hover_color'] ) {
            $css  .= "#review-list-content-{$sc_id} .rt-isotope-classes-tab .nav-item:hover, #review-list-content-{$sc_id} .rt-isotope-classes-tab .nav-item.current { ";
            $css .= "color:" . $value . ";";
            $css .= "}";
        }  
        if ( $value = $sc_meta['btn_bg_color'] ) {
            $css  .= "#review-list-content-{$sc_id} .slick-dots li button { ";
            $css .= "background:" . $value . ";";
            $css .= "}";
        }  
        if ( $value = $sc_meta['btn_bg_hover_color'] ) {
            $css  .= "#review-list-content-{$sc_id} .rt-isotope-classes-tab .nav-item:hover, #review-list-content-{$sc_id} .rt-isotope-classes-tab .nav-item.current { ";
            $css .= "background-color:" . $value . ";";
            $css .= "border-color:" . $value . ";";
            $css .= "}";
        }   
        if ( $value = $sc_meta['btn_border_radius'] ) {
            $css  .= "#review-list-content-{$sc_id} .rt-isotope-classes-tab .nav-item { ";
            $css .= "border-radius:" . $value . ";";
            $css .= "}";
        }  

        // slider button color
        if ( $value = $sc_meta['btn_bg_color'] ) {
            $css  .= "#review-list-content-{$sc_id} .slick-dots li button { ";
            $css .= "background-color:" . $value . " !important;";
            $css .= "}";
        }  
        if ( $value = $sc_meta['btn_bg_hover_color'] ) {
            $css  .= "#review-list-content-{$sc_id} .slick-dots li.slick-active button { "; 
            $css .= "background-color:" . $value . " !important;";
            $css .= "}";
        }  
        

        // badge floating
        if ( $sc_meta['layout'] == 'badge-floating' ) {
            $css  .= "#review-list-content-{$sc_id} .rt-badge-floating { ";

                switch( $sc_meta['floating_badge_pos'] ) {
                    case 'top-right':
                        $css .= "top: 10%; right: 0;";
                        break;

                    case 'middle-right':
                        $css .= "top: 40%; right: 0;";
                        break;

                    case 'bottom-right':
                        $css .= "bottom: 10%; right: 0;";
                        break;

                    case 'top-left':
                        $css .= "top: 10%; left: 0;";
                        break;

                    case 'middle-left':
                        $css .= "top: 40%; left: 0;";
                        break;

                    case 'bottom-left':
                        $css .= "bottom: 10%; left: 0;";
                        break;
                }
            
            $css .= "}";
        } 

        if ( $sc_meta['layout'] == 'badge-floating' ) {
            $css  .= "#review-list-content-{$sc_id} .rt-badge-sidebar {"; 
                switch( $sc_meta['floating_badge_pos'] ) {  
                    case 'top-left':
                    case 'middle-left':
                    case 'bottom-left':
                        $css .= "left: 0;";
                        break; 
                } 
            $css .= "}";
        } 

        if ( $style_tag ) {
            $css .= "</style>";
        }
        return $css;
    }

}
