<?php

namespace Rtbr\Controllers; 
 
use Rtbr\Shortcodes\BusinessReview;  

class Shortcodes {

    public static function init_short_code() {
        $shortcodes = array( 
            'rt-business-review'   => __CLASS__ . '::business_review', 
        );

        foreach ($shortcodes as $shortcode => $function) {
            add_shortcode(apply_filters("{$shortcode}_shortcode_tag", $shortcode), $function);
        } 
    }

    public static function shortcode_wrapper(
        $function,
        $atts = array(),
        $wrapper = array(
            'class'  => 'rtbr',
            'before' => null,
            'after'  => null,
        )
    ) {
        ob_start();

        // @codingStandardsIgnoreStart
        echo empty($wrapper['before']) ? '<div class="' . esc_attr($wrapper['class']) . '">' : wp_kses_post( $wrapper['before'] );
        call_user_func($function, $atts);
        echo empty($wrapper['after']) ? '</div>' : wp_kses_post( $wrapper['after'] );

        // @codingStandardsIgnoreEnd

        return ob_get_clean();
    }

    /**
     * All shortcode.
     *
     * @param array $atts Attributes.
     *
     * @return string
     */  
    public static function business_review($atts) {
        return self::shortcode_wrapper(array(BusinessReview::class, 'output'), $atts);
    } 

}