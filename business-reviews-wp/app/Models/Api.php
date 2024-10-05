<?php

namespace Rtbr\Models; 

class Api { 

    /**
     * The unique instance of the plugin.
     *
     * @var  
     */
    private static $google_data;
    private static $fb_data;
    public $error_text; 

    function __construct() { 
    } 

    /**
     * @return mixed
     */
    private function googleApi() {  
        $place_id = rtbr()->get_options('rtbr_google_settings', 'place_id');
        $api_key = rtbr()->get_options('rtbr_google_settings', 'api_key');
        $api_url = "https://maps.googleapis.com/maps/api/place/details/json?place_id=" . $place_id . "&reviews_no_translations=true&key=" . $api_key;
       // error_log( print_r(  $api_url , true ) . "\n\n" , 3, __DIR__ . '/log.txt' );
        $google_reviews = wp_remote_get( $api_url ); 
        if ( is_wp_error( $google_reviews ) ) {
            return [];
        }
        $google_reviews = wp_remote_retrieve_body( $google_reviews ); 
        return json_decode( $google_reviews ); 
    }

    /**
     * @return mixed
     */
    private function facebookApi() {  
        $page_id = rtbr()->get_options('rtbr_facebook_settings', 'page_id');
        $page_access_token = rtbr()->get_options('rtbr_facebook_settings', 'page_access_token');

        $limit = 500; 
        $api_url = 'https://graph.facebook.com/v12.0/' . $page_id . "?access_token=" . $page_access_token . "&fields=ratings.fields(reviewer{id,name,picture.width(120).height(120)},created_time,rating,recommendation_type,review_text,open_graph_story{id}).limit(" . $limit . "),overall_star_rating,rating_count"; 
        $fb_reviews = wp_remote_get( $api_url ); 
        if ( is_wp_error( $fb_reviews ) ) {
            return [];
        }
        $fb_reviews = wp_remote_retrieve_body( $fb_reviews ); 
        return json_decode( $fb_reviews ); 
    }

    /**
     * @return mixed
     */
    private function yelpApi( $type = null ) {  
        $business_url = rtbr()->get_options('rtbr_yelp_settings', 'business_url');
	    if ( empty( $business_url ) ) {
		    return [];
	    }
        $segments = explode('/', trim(parse_url($business_url, PHP_URL_PATH), '/'));  
        $review_path = ( $type == 'business_info' ) ? '' : '/reviews';
        $api_url = "https://api.yelp.com/v3/businesses/" . $segments[1] . $review_path;  
        $api_key = rtbr()->get_options('rtbr_yelp_settings', 'api_key');
 
        $header_args = array(
            'headers' => array(
                'user-agent' => 'business-reviews-wp',
                'Authorization' => "Bearer " . $api_key
            )
        );
        $yelp_reviews = wp_remote_get( $api_url, $header_args ); 
        if ( is_wp_error( $yelp_reviews ) ) {
            return [];
        }
        $yelp_reviews = wp_remote_retrieve_body( $yelp_reviews ); 
        return json_decode( $yelp_reviews );  
    }

    /**
     * @return mixed
     */
    public function getGoogleReview( $rtbr_shortcode_id ) {   

        if ( null === self::$google_data ) { 
              
            if ( rtbr()->get_options('rtbr_general_settings', 'save_into_database') ) {
                if ( get_transient( 'rtbr_google_reviews') ) {
                    $google_reviews = get_transient( 'rtbr_google_reviews');
                } else {
                    $google_reviews = $this->googleApi(); 
                    
                    // show error message
                    if ( isset( $google_reviews->error_message ) && $google_reviews->error_message ) { 
                        $this->error_text = $google_reviews->error_message;
                        return;
                    } 
                    
                    // save data to database
                    set_transient( 'rtbr_google_reviews', $google_reviews, rtbr()->get_options('rtbr_general_settings', 'refresh_review') * HOUR_IN_SECONDS );
                }  
            } else {  
                $google_reviews = $this->googleApi(); 
            }  

            if ( isset( $google_reviews->result ) ) {
                self::$google_data = $google_reviews->result;
            }
            
        }

        return self::$google_data; 
    }

    /**
     * @return mixed
     */
    public function getFacebookReview( $rtbr_shortcode_id, $type = null ) {   
        if ( null === self::$fb_data ) {     
            if ( rtbr()->get_options('rtbr_general_settings', 'save_into_database') ) {
                if ( get_transient( 'rtbr_fb_reviews') ) {
                    $fb_reviews = get_transient( 'rtbr_fb_reviews');
                } else {
                    $fb_reviews = $this->facebookApi();  

                    // show error message 
                    if ( isset( $fb_reviews->error ) && $fb_reviews->error ) { 
                        $this->error_text = $fb_reviews->error->message;
                        return;
                    } 

                    // save data to database
                    set_transient( 'rtbr_fb_reviews', $fb_reviews, rtbr()->get_options('rtbr_general_settings', 'refresh_review') * HOUR_IN_SECONDS );
                }  
            } else {  
                $fb_reviews = $this->facebookApi(); 
            }  

            self::$fb_data = $fb_reviews;
        } 

        if ( $type == 'business_info' ) {
            return self::$fb_data;
        } else {
            if ( isset( self::$fb_data->ratings ) ) {
                return self::$fb_data->ratings->data;
            } else {
                return [];
            }
            
        } 
    }

    /**
     * @return mixed
     */
    public function getYelpReview( $rtbr_shortcode_id, $type = null ) {   

        if ( $type == 'business_info' ) {  
            // get business info
            if ( rtbr()->get_options('rtbr_general_settings', 'save_into_database') ) {
                if ( get_transient( 'rtbr_yelp_business_info') ) {
                    $yelp_business_info = get_transient( 'rtbr_yelp_business_info');
                } else {
                    $yelp_business_info = $this->yelpApi( $type ); 
                    
                    // show error message
                    if ( isset( $yelp_business_info->error ) && $yelp_business_info->error ) {   
                        $this->error_text = $yelp_business_info->error->description;
                        if (strpos($this->error_text, 'does not match') !== false) {
                            $this->error_text = esc_html__( 'API key does not match', 'business-reviews-wp' );
                        }
                        return;
                    }

                    // save data to database
                    set_transient( 'rtbr_yelp_business_info', $yelp_business_info, rtbr()->get_options('rtbr_general_settings', 'refresh_review') * HOUR_IN_SECONDS );
                }  
            } else {  
                $yelp_business_info = $this->yelpApi( $type ); 
            } 
            return $yelp_business_info;  
        } else { 
            // get reviews data 
            if ( rtbr()->get_options('rtbr_general_settings', 'save_into_database') ) {
                if ( get_transient( 'rtbr_yelp_reviews') ) {
                    $yelp_reviews = get_transient( 'rtbr_yelp_reviews');
                } else {
                    $yelp_reviews = $this->yelpApi( $type );  

                    // show error message
                    if ( isset( $yelp_reviews->error ) && $yelp_reviews->error ) {   
                        $this->error_text = $yelp_reviews->error->description;
                        if (strpos($this->error_text, 'does not match') !== false) {
                            $this->error_text = esc_html__( 'API key does not match', 'business-reviews-wp' );
                        }
                        return;
                    }

                    if ( isset( $yelp_reviews->reviews ) ) {
                        $yelp_reviews = $yelp_reviews->reviews;

                        // save data to database
                        set_transient( 'rtbr_yelp_reviews', $yelp_reviews, rtbr()->get_options('rtbr_general_settings', 'refresh_review') * HOUR_IN_SECONDS );
                    }
                    
                }  
            } else {  
                $yelp_reviews = $this->yelpApi( $type )->reviews; 
            } 
            return $yelp_reviews;
        }   
    }

    /**
     * @return mixed
     */
    public function emptyApi( $business_type ) {
        $error_text = null;
        // empty api check
		if ( $business_type == "google" || $business_type == "multiple" ) { 
			if ( !rtbr()->get_options('rtbr_google_settings', 'api_key') ) { 
				$error_text = esc_html__( "Please add a google api key from settings", "business-reviews-wp" );
			}
			if ( !rtbr()->get_options('rtbr_google_settings', 'place_id') ) {
				$error_text = esc_html__( "Please add a google place id from settings", "business-reviews-wp" );
			}
		} elseif ( $business_type == "facebook" || $business_type == "multiple" ) {
			if ( !rtbr()->get_options('rtbr_facebook_settings', 'page_access_token') ) {
				$error_text = esc_html__( "Please add a facebook page access token from settings", "business-reviews-wp" );
			}
			if ( !rtbr()->get_options('rtbr_facebook_settings', 'page_id') ) {
				$error_text = esc_html__( "Please add a facebook page id from settings", "business-reviews-wp" );
			}
		} elseif ( $business_type == "yelp" || $business_type == "multiple" ) { 
			if ( !rtbr()->get_options('rtbr_yelp_settings', 'api_key') ) {
				$error_text = esc_html__( "Please add a yelp api key from settings", "business-reviews-wp" );
			}
			if ( !rtbr()->get_options('rtbr_yelp_settings', 'business_url') ) {
				$error_text = esc_html__( "Please add a yelp business url from settings", "business-reviews-wp" );
			}
        }
        return $error_text;
    }
 
}