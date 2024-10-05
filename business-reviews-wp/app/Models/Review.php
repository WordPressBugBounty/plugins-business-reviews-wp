<?php 

namespace Rtbr\Models; 
use Rtbr\Helpers\Functions; 
use Rtbr\Models\BusinessInfo;

class Review {
 
    private $shortcode_id;  
    private $description;
    private $created_at;
    private $rating;
    private $author_name;
    private $review_url;
    private $author_img; 
    private $business_type_logo; 
    private $business_type;   

    function setReview( $shortcode_id, $review, $business_type ) {  
 
        $this->shortcode_id = $shortcode_id;   
        $this->business_type = $business_type;

        switch ( $this->business_type ) {
            case "google":  
                $this->author_name = $review->author_name; 
                $this->description = $review->text; 
                $this->created_at  = $review->time;  
                $this->author_img  = $review->profile_photo_url; 
                $this->rating      = $review->rating;   
                $this->review_url  = $review->author_url;   

                break;

            case "facebook": 
                if ( isset( $review->reviewer ) ) {
                    $this->author_name = $review->reviewer->name;
                    $this->author_img  = Functions::get_default_placeholder_url(); 
                }
                
                if ( ! $this->author_img ) {
                    $this->author_img  = Functions::get_default_placeholder_url(); 
                }

                $this->description = isset( $review->review_text ) ? $review->review_text : ''; 
                $this->created_at  = $review->created_time;  
                 
                if ( isset( $review->rating ) ) { 
                    $this->rating  = $review->rating;
				} elseif ( isset( $review->recommendation_type ) ) {
					$review_rating = ($review->recommendation_type == 'negative' ? 1 : 5);
					$this->rating  = $review_rating;
                } 
                $this->review_url  = "https://facebook.com/" . $review->open_graph_story->id; 

                break;

            case "yelp":
                $this->id = $review->id; 
                $this->author_name = $review->user->name; 
                $this->description = $review->text; 
                $this->created_at  = $review->time_created;  
                $this->author_img  = $review->user->image_url; 
                $this->rating      = $review->rating;  
                $this->review_url  = $review->url; 
                
                break; 
        }
    } 

    /**
     * decorate review from object to array
     * @return array
     */
    public static function decorate_review( $shortcode_id, $review_data, $business_type ) {
		$review_obj = new Review();
        $new_array = [];
        if ( $review_data ) {
            foreach( $review_data as $key => $single_data ) {
                $review_obj->setReview( $shortcode_id, $single_data, $business_type );
                $new_array[$key]['name'] = $review_obj->getAuthorName();
                $new_array[$key]['url'] = $review_obj->getReviewUrl();
                $new_array[$key]['img'] = $review_obj->getAuthorImg();
                $new_array[$key]['business_logo'] = $review_obj->getBusinessTypeLogo();
                $new_array[$key]['time'] = $review_obj->getCreatedAt();
                $new_array[$key]['human_read_time'] = $review_obj->getHumanReadTime();
                $new_array[$key]['rating'] = $review_obj->getRating(); 
                $new_array[$key]['rating_star'] = $review_obj->getRatingStar(); 
                $new_array[$key]['desc'] = $review_obj->getDescription();
                $new_array[$key]['desc_text'] = $review_obj->getDescriptionText();
                $new_array[$key]['business_type'] = $business_type;
            } 
        } 
		return $new_array;
    }
    
    /**
     * load google rich snippet
     * @return object
     */
    public static function rich_snippet( $shortcode_id, $review_data, $business_info_data, $business_type ) {
        if ( !function_exists('rtbrp') ) return [];
		$business_info = new BusinessInfo( $shortcode_id, $business_info_data, $business_type ); 
		$snippet = [];
		$reviews = [];
		foreach( $review_data as $single ) {
			$reviews[] = [
				"@type" => "Review",
				"reviewRating" => [
					"@type" => "Rating",
					"ratingValue" => "{$single['rating']}"
				], 
				"author" => [
					"@type" => "Person",
					"name" => $single['name']
				], 
				"datePublished" => date("Y-m-d", $single['time']),
				"reviewBody" => $single['desc_text']  
			];
		} 

		$snippet = [
			"@context" => "http://schema.org/",
			"@type" => "Organization",
			"image" => $business_info->getBusinessImg(),
			"name" => $business_info->getName(),
			"telephone" => $business_info->getPhone(),
			"aggregateRating" => [
				"@type" => "AggregateRating",
				"ratingValue" => "{$business_info->getAvgRating()}",
				"reviewCount" => "{$business_info->getTotalRating()}" 
			],
			"review" => $reviews
        ]; 
        
		return $snippet; 
	}
 
    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    } 

    /**
     * @return mixed
     */
    public function getDescriptionText() {
        return $this->description;
    }   
    /**
     * @return mixed
     */
    public function getDescription() {
         
        $this->description = apply_filters('rtbr_review_text', $this->description, $this->shortcode_id);  

        $text_limit = ( isset( $_POST['review_text_limit'] ) ) ? absint( $_POST['review_text_limit'] ) : get_post_meta( $this->shortcode_id, 'review_text_limit', true ); 
        if ( $text_limit ) { 
            $read_more_text = ( isset( $_POST['read_more_text'] ) ) ? sanitize_text_field( $_POST['read_more_text'] ) : get_post_meta( $this->shortcode_id, 'read_more_text', true );
            
            $limit_type = ( isset( $_POST['review_text_limit_type'] ) ) ? sanitize_text_field( $_POST['review_text_limit_type'] ) : get_post_meta( $this->shortcode_id, 'review_text_limit_type', true );
            if ( $limit_type == 'word' ) {

                $display_text = wp_trim_words( $this->description, $text_limit, '' );

                $text = $this->description;
                $words = explode(' ', $text);
                $text_array_to_keep = array_slice( $words, $text_limit );
                $hidden_text = implode(' ',$text_array_to_keep ); 

            } else { 
                if ( strlen( $this->description ) <= $text_limit ) {
                    $display_text = $this->description;
                    $hidden_text = "";
                } else {
                    $display_text = mb_strimwidth($this->description, 0, $text_limit, '');
                    $hidden_text = mb_strimwidth($this->description, $text_limit, 999, '');
                } 
            }

            if ( strlen( $this->description ) <= strlen( $display_text ) ) {
                return $this->description;
            } else {
                $extra_space = ( $limit_type == 'word' ) ? ' ' : '';
                return $display_text . " <a href='#' class='rtbr-read-more'>" . $read_more_text . "</a><span class='rtbr-full-review'>" . $extra_space . $hidden_text . "</span>";
            } 
        } else {
            return $this->description;
        }  
    } 

    /**
     * @return mixed
     */
    public function getHumanReadTime() {  
        $time = ( $this->business_type == "google" ) ? $this->created_at : strtotime( $this->created_at ) ;
        return human_time_diff( $time, current_time('timestamp') ) . esc_html__(' ago', 'business-reviews-wp');
    }

    /**
     * @return mixed
     */
    public function getCreatedAt() {  
        return ( $this->business_type == "google" ) ? $this->created_at : strtotime( $this->created_at ); 
    }

    /**
     * @return mixed
     */
    public function getRating() {
        return $this->rating; 
    }

    /**
     * @return mixed
     */
    public function getRatingStar() {
        return Functions::review_stars( $this->rating ); 
    }

    /**
     * @return string
     */
    public function getAuthorName() {
        return $this->author_name;
    }

    /**
     * @return string
     */
    public function getReviewUrl() {
        return $this->review_url;
    }

    /**
     * @return string
     */
    public function getBusinessTypeLogo() {
        switch( $this->business_type ) {
            case "google": 
                $img_url = 'google-mini.png'; 
                $this->business_type_logo = RTBR_URL . '/assets/imgs/' . $img_url;
                break;

            case "facebook": 
                $img_url = 'facebook-mini.png'; 
                $this->business_type_logo = RTBR_URL . '/assets/imgs/' . $img_url;
                break;

            case "yelp": 
                $img_url = 'yelp-mini.png'; 
                $this->business_type_logo = RTBR_URL . '/assets/imgs/' . $img_url;
                break; 
        } 
        return $this->business_type_logo;
    }

    /**
     * @return string
     */
    public function getAuthorImg() { 
        if ( $this->business_type == "yelp" ) {
            if (strlen( $this->author_img ) > 0) {
                return str_replace('o.jpg', 'ms.jpg', $this->author_img);
            } 
        } else {
            return $this->author_img;
        } 
    }  
}