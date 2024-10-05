<?php  
/**
 * Review grid one template
 * @author      RadiusTheme
 * @package     business-reviews-wp/templates
 * @version     1.0.0
 * 
 * @var Rtbr\Models\BusinessInfo $business_info hold all business info
 * @var array $review_data[] [name, url, img, business_logo, time, human_read_time, rating, rating_star, desc, desc_text, business_type]
 * @var array $sc_meta [business_type, layout, grid_column, pagination, reviews_per_page, business_info, business_info_fields, review_fields, see_all_reviews, open_link_blank, no_follow_link, width, author_name, author_name_hover, google_star_color, facebook_star_color, yelp_star_color]
 * 
 */  

if ( $review_data ): ?>  
<div class="rt-row <?php echo esc_attr( get_post_meta( $sc_meta['id'], 'parent_class', true ) ); ?> rt-<?php echo esc_attr( $business_info->getBusinessType() ); ?>-review" id="review-list-content-<?php echo esc_attr( $sc_meta['id'] ); ?>">
    <?php   
        do_action('rtbr_before_review_list');
        foreach( $review_data as $key => $single ):    
    ?>
    <div class="<?php echo esc_attr( $business_info->gridColumn() ); ?> rt-grid-layout rtbr-single-review <?php echo esc_attr( $business_info->paginationClass( $key ) ); ?>">
        <div class="rt-grid-view-style rt-grid-view-style-1">
            <div class="rt-media"> 
                <?php if ( in_array('img', $sc_meta['review_fields']) && $single['img'] ) { ?>
                <div class="rt-author-img">
                    <img src="<?php echo esc_attr( $single['img'] ); ?>" alt="<?php echo esc_attr( $single['name'] ); ?>">
                </div>
                <?php } ?> 

                <div class="rt-media-body">
                    <?php if ( in_array('name', $sc_meta['review_fields']) ) { ?>
                        <h3 class="rt-author-title"><a href="<?php echo esc_url( $single['url'] ); ?>" <?php echo wp_kses_post( $sc_meta['open_link_blank'] . $sc_meta['no_follow_link'] ); ?>><?php echo esc_html( $single['name'] ); ?></a></h3>
                    <?php } ?> 

                    <?php if ( in_array('time', $sc_meta['review_fields']) ) { ?>
                        <div class="rt-time-ago"><?php echo esc_html( $single['human_read_time'] ); ?></div>
                    <?php } ?>

                    <div class="rt-right-item">

                        <?php if ( in_array('powered_by', $sc_meta['business_info_fields']) ) { ?>
                        <div class="rt-social-icon">
                            <img src="<?php echo esc_attr( $single['business_logo'] ); ?>" alt="<?php echo esc_attr( $single['business_type'] ); ?>">
                        </div>
                        <?php } ?> 
                        
                        <?php if ( in_array('rating_star', $sc_meta['review_fields']) ) { ?> 
                            <div class="rt-item-rating <?php echo esc_attr( $single['business_type'] ); ?>-rating">
                            <?php  
                                $allowed_html = [
                                    'svg'   => array(
                                        'class'           => true,
                                        'aria-hidden'     => true,
                                        'aria-labelledby' => true,
                                        'role'            => true,
                                        'xmlns'           => true,
                                        'width'           => true,
                                        'height'          => true,
                                        'viewbox'         => true,
                                    ),
                                    'g'     => array( 'fill' => true ),
                                    'title' => array( 'title' => true ),
                                    'path'  => array(
                                        'd'    => true,
                                        'fill' => true,
                                    ),
                                ];
                                echo wp_kses( $single['rating_star'], $allowed_html );
                            ?>
                            </div> 
                        <?php } ?> 
                    </div>
                </div>
            </div>

            <?php if ( in_array('review', $sc_meta['review_fields']) ) { ?>
                <p><?php echo wp_kses_post( $single['desc'] ); ?></p> 
            <?php } ?>  
        </div>
    </div>
    <?php 
        endforeach; 
        do_action('rtbr_after_review_list'); 
    ?>

    <?php rtbr()->get_partial_path('pagination', $args); ?>
</div>
<?php      
endif; //review_data   