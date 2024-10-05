<?php

namespace Rtbr\Widgets; 

class BusinessReview extends \WP_Widget {

    protected $widget_slug;

    public function __construct() {

        $this->widget_slug = 'rtbr-widget-review';

        parent::__construct(
            $this->widget_slug,
            esc_html__('Business Review', 'business-reviews-wp'),
            array(
                'classname'   => 'rtbr ' . $this->widget_slug . '-class',
                'description' => esc_html__('A list of Business Review.', 'business-reviews-wp') 
            )
        );  
    }

    public function widget($args, $instance) {  
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        if ( $instance['shortcode_id'] ) {
            echo do_shortcode( '[rt-business-review id="'. absint($instance['shortcode_id']) .'"]' );  
        } 

        echo $args['after_widget']; 
    }

    public function update($new_instance, $old_instance) {

        $instance = $old_instance;

        $instance['title'] = !empty($new_instance['title']) ? esc_html( $new_instance['title'] ) : '';
        $instance['shortcode_id'] = isset($new_instance['shortcode_id']) ? (int)$new_instance['shortcode_id'] : ''; 

        return $instance;

    }

    public function form($instance) {

        // Define the array of defaults
        $defaults = array(
            'title'        => esc_html__('Business Review', 'business-reviews-wp'),
            'shortcode_id' => '', 
        );

        // Parse incoming $instance into an array and merge it with $defaults
        $instance = wp_parse_args(
            (array)$instance,
            $defaults
        );

        // Display the admin form
        include(RTBR_PATH . "views/widgets/business-review.php"); 
    }   
}