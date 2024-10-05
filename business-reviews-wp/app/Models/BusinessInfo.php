<?php

namespace Rtbr\Models; 

use Rtbr\Helpers\Functions; 

class BusinessInfo {

    private $id; 
    private $shortcode_id;  
    private $name;  
    private $total_rating; 
    private $rating;  
    private $business_url;
    private $business_img; 
    private $phone; 
    private $business_type; 
    private $business_type_logo; 
    private $layout; 
    private $all_review_url;  
    private $direct_review_link;  
    private $direct_review_tag;  

    function __construct( $shortcode_id, $business_info, $business_type ) {   
        $this->shortcode_id = $shortcode_id; 
        $this->business_type = $business_type; 

        $this->layout = ( isset( $_POST['layout'] ) ) ? sanitize_text_field( $_POST['layout'] ) : get_post_meta( $shortcode_id, 'layout', true ); 

        switch ( $this->business_type ) {
            case "google":  
                
                if ( isset( $business_info->name ) ) {
                    $this->name = $business_info->name; 
                    $img_url = "";
                    if ( $attachement_id = rtbr()->get_options('rtbr_google_settings', 'place_img') ) { 
                        if ( $img_url = wp_get_attachment_image_src($attachement_id, 'thumbnail') ){
                            $img_url = $img_url[0];
                        } 
                    } else {
                        $img_url = $business_info->icon;
                    }
                    $this->business_img  =  $img_url; 
                    $this->phone         = isset( $business_info->international_phone_number ) ? $business_info->international_phone_number : '';   
                    $this->total_rating  = $business_info->user_ratings_total;  
                    $this->rating        = $business_info->rating;   
                    $this->business_url  = $business_info->url; 
                    $this->direct_review_link  = "https://search.google.com/local/writereview?placeid=" . rtbr()->get_options('rtbr_google_settings', 'place_id'); 
                } 

                break;

            case "facebook": 
                $this->name = rtbr()->get_options('rtbr_facebook_settings', 'page_name'); 
                $page_id = rtbr()->get_options('rtbr_facebook_settings', 'page_id');
                
                if ( isset( $business_info->ratings ) ) {
                    $img_url = "";
                    if ( $attachement_id = rtbr()->get_options('rtbr_facebook_settings', 'page_img') ) {
                        if ( $img_url = wp_get_attachment_image_src($attachement_id, 'thumbnail') ){
                            $img_url = $img_url[0];
                        }
                    } else {
                        $img_url = 'https://graph.facebook.com/' . $page_id . '/picture';
                    }

                    $this->business_img = $img_url;

                    if ( isset( $business_info->overall_star_rating ) ) {
                        $facebook_rating = number_format((float)$business_info->overall_star_rating, 1, '.', '');
                    }
                    if ( isset( $business_info->rating_count ) && $business_info->rating_count > 0 ) {
                        $facebook_count = $business_info->rating_count;
                    }

                    $reviews = $business_info->ratings->data;
                    $review_count = isset($facebook_count) ? $facebook_count : count($reviews);
                    if (isset($facebook_rating) ) {
                        $rating = $facebook_rating;
                    } else {
                        $rating = 0;
                        $review_count = count($reviews);
                        foreach ($reviews as $review) {
                            if (isset($review->rating)) {
                                $rating = $rating + $review->rating;
                            } elseif (isset($review->recommendation_type)) {
                                $rating = $rating + ($review->recommendation_type == 'negative' ? 1 : 5);
                            } else {
                                continue;
                            }
                        }
                        $rating = round($rating / $review_count, 1);
                        $rating = number_format((float)$rating, 1, '.', '');
                    }

                    $this->total_rating  = $review_count;  
                    $this->rating        = $rating;

                    $this->business_url  = "https://facebook.com/" . $business_info->id;
                    $this->direct_review_link  = "https://facebook.com/" . $business_info->id . "/reviews";
                } 

                break;

            case "yelp":
                
                if ( isset( $business_info->name ) ) {
                    $this->name = $business_info->name; 
                    $img_url = "";
                    if ( $attachement_id = rtbr()->get_options('rtbr_yelp_settings', 'business_img') ) { 
                        if ( $img_url = wp_get_attachment_image_src($attachement_id, 'thumbnail') ){
                            $img_url = $img_url[0];
                        } 
                    } else {
                        $img_url = $business_info->image_url;
                    }
                    $this->business_img  = $img_url; 
                    $this->phone         = $business_info->display_phone;  
                    $this->total_rating  = $business_info->review_count;  
                    $this->rating        = $business_info->rating;  
                    $this->business_url  = $business_info->url;  
                    $this->direct_review_link  = "https://www.yelp.com/writeareview/biz/" . $business_info->id; 
                }
                
                break; 
        } // end switch
		if( $this->direct_review_link ){
			$this->direct_review_tag = '<a href="javascript:void(0)" class="rtbr-direct-review" onclick="rtbr_popup(\''. esc_url( $this->direct_review_link ) .'\', 800, 600)">'. esc_html__( 'Write A Review', 'business-reviews-wp' ) .'</a>';
		} else {
			$this->direct_review_tag = null;
		}

    } 
 
    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getBusinessType() {
        return $this->business_type;
    }

    /**
     * @return string
     */
    public function getDirectReview() {
        if ( function_exists('rtbrp') ) {
            return $this->direct_review_tag;
        } else {
            return null;
        } 
    }

    /**
     * @return string
     */
    public function getBusinessTypeLogo() {
        switch( $this->business_type ) {
            case "google": 
                $img_url = ( $this->layout == 'grid' ) ? 'google-mini.png' : 'google-medium.png';
                $this->business_type_logo = RTBR_URL . '/assets/imgs/' . $img_url;
                break;

            case "facebook": 
                $img_url = ( $this->layout == 'grid' ) ? 'facebook-mini.png' : 'facebook-medium.png';
                $this->business_type_logo = RTBR_URL . '/assets/imgs/' . $img_url;
                break;

            case "yelp": 
                $img_url = ( $this->layout == 'grid' ) ? 'yelp-mini.png' : 'yelp-medium.png';
                $this->business_type_logo = RTBR_URL . '/assets/imgs/' . $img_url;
                break; 
        } 
        return $this->business_type_logo;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }  

    /**
     * @return int
     */
    public function getTotalRating() {
        return $this->total_rating;
    } 

    /**
     * @return float
     */
    public function getAvgRating() {
        return $this->rating; 
    } 

    /**
     * @return mixed
     */
    public function getRating() {
        return Functions::review_stars( $this->rating ); 
    } 

    /**
     * @return string
     */
    public function getBusinessUrl() {
        return $this->business_url;
    }

    /**
     * @return string
     */
    public function getBusinessImg() { 
        if ( $this->business_type == "yelp" && is_string( $this->business_img ) ) {
            if (strlen( $this->business_img ) > 0) {
                return str_replace('o.jpg', 'ms.jpg', $this->business_img);
            } 
        } else {
            return $this->business_img;
        } 
    } 

    /**
     * @return string
     */
    public function getPhone() { 
        return $this->phone;
    } 

    /**
     * @return mixed
     */
    public function getAllReviewUrl() {
        switch( $this->business_type ) {
            case "google":  
                $place_id = rtbr()->get_options('rtbr_google_settings', 'place_id');
                if ( $place_id ) {
                    $this->all_review_url = 'https://search.google.com/local/reviews?placeid=' . $place_id;
                }
                
                break;

            case "facebook":  
                $page_id = rtbr()->get_options('rtbr_facebook_settings', 'page_id');
                if ( $page_id ) {
                    $this->all_review_url = 'https://fb.com/' . $page_id . '/reviews';
                }
                break;

            case "yelp":  
                if ( $business_url = rtbr()->get_options('rtbr_yelp_settings', 'business_url') ) { 
                    $this->all_review_url = $business_url;
                } 
                break; 
        } 
        return $this->all_review_url; 
    } 

    /**
     * @return mixed
     */
    public function gridColumn() {
        $class = "";   
        $col = ( isset( $_POST['grid_column'] ) ) ? absint( $_POST['grid_column'] ) : get_post_meta( $this->shortcode_id, 'grid_column', true );  
         
        switch ( $col ) {
            case 1:
                $class = "rt-col-xl-12 rt-col-md-12";
                break;

            case 2:
                $class = "rt-col-xl-6 rt-col-md-6";
                break;

            case 3:
                $class = "rt-col-xl-4 rt-col-md-6";
                break;

            case 4:
                $class = "rt-col-xl-3 rt-col-md-6";
                break;
        }
        return $class;
    } 

    /**
     * @return mixed
     */
    public function paginationClass( $key ) {
        $class = "";   
        $pagination = ( isset( $_POST['pagination'] ) ) ? absint( $_POST['pagination'] ) : get_post_meta( $this->shortcode_id, 'pagination', true ); 

        $reviews_per_page = ( isset( $_POST['reviews_per_page'] ) ) ? absint( $_POST['reviews_per_page'] ) : get_post_meta( $this->shortcode_id, 'reviews_per_page', true ); 

        $review_display_limit = ( isset( $_POST['review_display_limit'] ) ) ? absint( $_POST['review_display_limit'] ) : get_post_meta( $this->shortcode_id, 'review_display_limit', true ); 
  
        $reviews_per_page = ( $reviews_per_page ) ? $reviews_per_page : 10;  
        if ( $pagination && $key >= $reviews_per_page ) {
            $class = "rtbr-review-hide";
        }
        $review_display_limit = ( $review_display_limit ) ? $review_display_limit : 5; 
        if ( ! $pagination && $key >= $review_display_limit ) {
            $class = "rtbr-review-hide";
        }
        return $class;
    }  

}