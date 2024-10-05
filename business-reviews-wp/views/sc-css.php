<?php
 
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

		case 'badg

	e-two':
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

if ( $css ) {
    echo esc_html( $css );
} 