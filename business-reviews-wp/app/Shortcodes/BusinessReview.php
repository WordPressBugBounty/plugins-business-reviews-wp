<?php
namespace Rtbr\Shortcodes; 

use Rtbr\Helpers\Functions;
use Rtbr\Models\Api; 
use Rtbr\Models\Review;  
use Rtbr\Models\BusinessInfo;
use Rtbr\Controllers\Admin\Meta\MetaOptions;

class BusinessReview {    
	
	public static function output( $atts ) { 

		$sc_meta = shortcode_atts( array(
			'id'  		 => '',
			'title'      => '',  
		), $atts );   
	 
		// get all meta value
		$sc_meta = array_merge( $sc_meta, MetaOptions::metaValue( $sc_meta['id'] ) ); 
		
		$review_data = $rich_snippet = $google_data = $facebook_data = $yelp_data = []; 
		$api = new Api(); 

		// check empty api
		if ( $error_data = $api->emptyApi( $sc_meta['business_type'] ) ) {
			if ( current_user_can('administrator') ) {
				echo esc_html($error_data);
			}
			return;
		}
 
		if ( $sc_meta['business_type'] == "google" || ( $sc_meta['business_type'] == "multiple" && in_array('google', $sc_meta['multi_business']) ) ) {  
			$review_data = isset($api->getGoogleReview( $sc_meta['id'] )->reviews) ? $api->getGoogleReview( $sc_meta['id'] )->reviews : ''; 
			$business_info_data = $api->getGoogleReview( $sc_meta['id'] );  
			$ggl_data = Review::decorate_review( $sc_meta['id'], $review_data, "google" );  

			// google data shorting filter
			$review_data = $google_data = apply_filters('rtbr_google_review_shorting', $ggl_data, $sc_meta['id']); 
			 
			if ( $sc_meta['google_rich_snippet'] == "" ) {
				$rich_snippet[] = Review::rich_snippet( $sc_meta['id'], $review_data, $business_info_data, "google" );
			} 
		} 
 
		if ( $sc_meta['business_type'] == "facebook" || ( $sc_meta['business_type'] == "multiple" && in_array('facebook', $sc_meta['multi_business']) ) ) {  
			$review_data = $api->getFacebookReview( $sc_meta['id'] ); 
			$business_info_data = $api->getFacebookReview( $sc_meta['id'], 'business_info' );  
			$fb_data = Review::decorate_review( $sc_meta['id'], $review_data, "facebook" ); 
			
			// facebook data shorting filter
			$review_data = $facebook_data = apply_filters('rtbr_facebook_review_shorting', $fb_data, $sc_meta['id']); 
 
			if ( $sc_meta['google_rich_snippet'] == "" ) {
				$rich_snippet[] = Review::rich_snippet( $sc_meta['id'], $review_data, $business_info_data, "facebook" );
			} 
		}  
	
		if ( $sc_meta['business_type'] == "yelp" || ( $sc_meta['business_type'] == "multiple" && in_array('yelp', $sc_meta['multi_business']) ) ) { 
			$review_data = $api->getYelpReview( $sc_meta['id'] );
			$business_info_data = $api->getYelpReview( $sc_meta['id'], 'business_info' ); 
			$yp_data = Review::decorate_review( $sc_meta['id'], $review_data, "yelp" ); 

			// yelp data shorting filter
			$review_data = $yelp_data = apply_filters('rtbr_yelp_review_shorting', $yp_data, $sc_meta['id']); 
 
			if ( $sc_meta['google_rich_snippet'] == "" ) {
				$rich_snippet[] = Review::rich_snippet( $sc_meta['id'], $review_data, $business_info_data, "yelp" );
			}  
		}  

		// merge multi business
		if ( $sc_meta['business_type'] == "multiple" ) { 
			$business_info_data = "";   
			$review_data = apply_filters('rtbr_multi_business', $google_data, $facebook_data, $yelp_data, $sc_meta['multi_business'] ); 
		}  
 
		// show error notice only for admin
		if ( $api->error_text ) {
			if ( current_user_can('administrator') ) {
				echo esc_html($api->error_text); 
			} 
			return;
		}

		Functions::get_template_part( $sc_meta['layout'], array( 
			'business_info' => new BusinessInfo( $sc_meta['id'], $business_info_data, $sc_meta['business_type'] ), 
			'review_data' => $review_data,  
			'sc_meta' => $sc_meta, 
		) );   
		 
		// custom shortcode style 
		if ( is_admin() ) { 
			echo Functions::shortcode_style( $sc_meta['id'], true );  
		} 

		// google rich snippet load
		if ( $rich_snippet ) {
			$script = "<script type='application/ld+json' id='rtbr-snippet-{$sc_meta['id']}'>".json_encode( $rich_snippet )."</script>";
			add_action( 'wp_footer', function() use( $script ) {
				$allowed_html = [
					'script' => [
						'type'  => [],
						'id' => [],
					], 
				];
				echo wp_kses( $script, $allowed_html );  
			});
		}     
	}   
}   
 