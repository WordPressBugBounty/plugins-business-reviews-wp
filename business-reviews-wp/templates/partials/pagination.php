<?php  
/**
 * Review pagination template
 * @author      RadiusTheme
 * @package     business-reviews-wp/templates
 * @version     1.0.0
 * 
 * @var Rtbr\Models\BusinessInfo $business_info hold all business info
 * @var array $review_data[] [name, url, img, business_logo, time, human_read_time, rating, rating_star, desc, desc_text, business_type]
 * @var array $sc_meta [business_type, layout, grid_column, pagination, reviews_per_page, business_info, business_info_fields, review_fields, see_all_reviews, open_link_blank, no_follow_link, width, author_name, author_name_hover, google_star_color, facebook_star_color, yelp_star_color]
 * 
 */  
?> 
<div class="rtbr-pagination rt-loadmore-btn">
    <?php 
        do_action('rtbr_before_pagination');
            if ( $sc_meta['pagination'] && $sc_meta['layout'] != 'isotope-one' ) :
                $pagi_num = $sc_meta['reviews_per_page']; 
                if ( count( $review_data ) <= $pagi_num ) return;
                if ( !$pagi_num ) {
                    $pagi_num = 10;
                } 
            ?>
            <a href="javascript:void(0)" rel="nofollow" class="rtbr-load-more" data-pagi-num="<?php echo esc_attr( $pagi_num ); ?>" data-id="review-list-content-<?php echo esc_attr( $sc_meta['id'] ); ?>" data-total="<?php echo esc_attr( count( $review_data ) ); ?>"><?php esc_html_e( 'Load More', 'business-reviews-wp' ); ?></a>
            <?php   
            endif; //pagination 

            if ( $sc_meta['see_all_reviews'] == "" && $business_info->getBusinessType() != "multiple" ) {
                echo '<a rel="nofollow" class="rtbr-see-all-review"' . $sc_meta['open_link_blank'] . ' href="'. esc_url( $business_info->getAllReviewUrl() ) .'">' . esc_html__( 'See All Reviews', 'business-reviews-wp' ) . '</a>'; 
            } 

            if ( $sc_meta['direct_review_link'] == "" && function_exists('rtbrp') && $business_info->getBusinessType() != "multiple" ) { 
                echo $business_info->getDirectReview();
            } 
        do_action('rtbr_after_pagination');
    ?>
</div><!-- .rtbr-pagination  --> 