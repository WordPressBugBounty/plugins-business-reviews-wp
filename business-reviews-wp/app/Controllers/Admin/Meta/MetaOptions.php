<?php
namespace Rtbr\Controllers\Admin\Meta;

class MetaOptions { 

    /**
     * Marge all meta field
     *
     * @return array
     */
    function allMetaFields() { 
        $fields = array();
        $fieldsA = array_merge( 
            $this->sectionLayoutFields(),
            $this->sectionSettingFields(),
            $this->sectionSelectionFields(),
            $this->sectionStyleFields()
        );
        foreach ($fieldsA as $field) {
            $fields[] = $field;
        } 

        return $fields;
    }

    /**
     * Get all post meta in array
     *
     * @return array
     */
    public static function metaValue( $sc_id ) { 
        
        $sc_meta = [];
        // layout tab   
        $sc_meta['business_type'] = isset( $_REQUEST['business_type'] ) ? sanitize_text_field( $_REQUEST['business_type'] ) : get_post_meta( $sc_id, 'business_type', true ); 
        $sc_meta['multi_business'] = isset( $_REQUEST['multi_business'] ) ? array_map( 'sanitize_text_field', $_REQUEST['multi_business'] ) : get_post_meta( $sc_id, 'multi_business', false );  
		$sc_meta['layout'] = isset( $_REQUEST['layout'] ) ? sanitize_text_field( $_REQUEST['layout'] ) : get_post_meta( $sc_id, 'layout', true );  
		$sc_meta['grid_column'] = isset( $_REQUEST['grid_column'] ) ? absint( $_REQUEST['grid_column'] ) : get_post_meta( $sc_id, 'grid_column', true );  
		 
		$sc_meta['pagination'] = isset( $_REQUEST['pagination'] ) ? absint( $_REQUEST['pagination'] ) : get_post_meta( $sc_id, 'pagination', true ); 

		$sc_meta['reviews_per_page'] = isset( $_REQUEST['reviews_per_page'] ) ? absint( $_REQUEST['reviews_per_page'] ) : get_post_meta( $sc_id, 'reviews_per_page', true ); 
        
        $sc_meta['review_display_limit'] = isset( $_REQUEST['review_display_limit'] ) ? absint( $_REQUEST['review_display_limit'] ) : get_post_meta( $sc_id, 'review_display_limit', true ); 

		// field selection tab
		$sc_meta['business_info'] = isset( $_REQUEST['business_info'] ) ? absint( $_REQUEST['business_info'] ) : get_post_meta( $sc_id, 'business_info', true );  
		$sc_meta['business_info_fields'] = isset( $_REQUEST['business_info_fields'] ) ? array_map( 'sanitize_text_field', $_REQUEST['business_info_fields'] ) : get_post_meta( $sc_id, 'business_info_fields', false );  
		$sc_meta['review_fields'] = isset( $_REQUEST['review_fields'] ) ? array_map( 'sanitize_text_field', $_REQUEST['review_fields'] ) : get_post_meta( $sc_id, 'review_fields', false );  
		$sc_meta['see_all_reviews'] = isset( $_REQUEST['see_all_reviews'] ) ? absint( $_REQUEST['see_all_reviews'] ) : get_post_meta( $sc_id, 'see_all_reviews', true );  
		$sc_meta['direct_review_link'] = isset( $_REQUEST['direct_review_link'] ) ? absint( $_REQUEST['direct_review_link'] ) : get_post_meta( $sc_id, 'direct_review_link', true );  

		// settings tab
		$open_link_blank = isset( $_REQUEST['open_link_blank'] ) ? absint( $_REQUEST['open_link_blank'] ) : get_post_meta( $sc_id, 'open_link_blank', true );
		$sc_meta['open_link_blank'] = ( !$open_link_blank ) ? " target='_blank' " : "";
		$no_follow_link = isset( $_REQUEST['no_follow_link'] ) ? absint( $_REQUEST['no_follow_link'] ) : get_post_meta( $sc_id, 'no_follow_link', true );
		$sc_meta['no_follow_link'] = ( !$no_follow_link ) ? " rel='nofollow' " : "";

		$sc_meta['google_rich_snippet'] = isset( $_REQUEST['google_rich_snippet'] ) ? absint( $_REQUEST['google_rich_snippet'] ) : get_post_meta( $sc_id, 'google_rich_snippet', true ); 

		// style tab
		$sc_meta['width'] = isset( $_REQUEST['width'] ) ? sanitize_text_field( $_REQUEST['width'] ) : get_post_meta( $sc_id, 'width', true );
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
        return $sc_meta;
    }

    function get_image_sizes() {
        global $_wp_additional_image_sizes;

        $sizes = array();

        foreach (get_intermediate_image_sizes() as $_size) {
            if (in_array($_size, array('thumbnail', 'medium', 'large'))) {
                $sizes[$_size]['width'] = get_option("{$_size}_size_w");
                $sizes[$_size]['height'] = get_option("{$_size}_size_h");
                $sizes[$_size]['crop'] = (bool)get_option("{$_size}_crop");
            } elseif (isset($_wp_additional_image_sizes[$_size])) {
                $sizes[$_size] = array(
                    'width'  => $_wp_additional_image_sizes[$_size]['width'],
                    'height' => $_wp_additional_image_sizes[$_size]['height'],
                    'crop'   => $_wp_additional_image_sizes[$_size]['crop'],
                );
            }
        }

        $imgSize = array();
        foreach ($sizes as $key => $img) {
            $imgSize[$key] = ucfirst($key) . " ({$img['width']}*{$img['height']})";
        }

        return $imgSize;
    }  

    function get_limit_type() {
        $types = array(
            'character' => esc_html__("Character", "business-reviews-wp"),
            'word'      => esc_html__("Word", "business-reviews-wp")
        );  
        return apply_filters('rtbr_limit_type', $types);
    }

    function businessInfoFields() {
        $business_info_field = array(
            'logo'            => esc_html__('Logo', 'business-reviews-wp'),
            'name'            => esc_html__('Name', 'business-reviews-wp'),
            'average_rating'  => esc_html__('Average Rating', 'business-reviews-wp'), 
            'rating_star'     => esc_html__('Rating Star', 'business-reviews-wp'), 
            'total_review'    => esc_html__('Total Review', 'business-reviews-wp'), 
            'powered_by'      => esc_html__('Powered By Logo', 'business-reviews-wp'), 
        );
        return apply_filters('rtbr_business_info_field', $business_info_field);
    }

    function reviewFields() {
        $review_field = array(
            'img'          => esc_html__("Author Image", 'business-reviews-wp'),
            'name'         => esc_html__("Author Name", 'business-reviews-wp'), 
            'rating_star'  => esc_html__("Rating Star", 'business-reviews-wp'), 
            'time'         => esc_html__("Time", 'business-reviews-wp'), 
            'review'       => esc_html__("Review", 'business-reviews-wp'), 
        );
        return apply_filters('rtbr_review_field', $review_field);
    }

    function sectionLayoutFields() {  
        $section_layout =  array(
            'business_type' => array(
                "type"    => "select",
                "name"    => "business_type",
                "label"   => esc_html__("Review type", 'business-reviews-wp'),
                'default' => 'google',
                "id"      => "rtbr-business-type",
                "class"   => "rt-select2",
                "options" => $this->businessType()
            ), 
            'layout'     => array(
                "type"    => "select",
                "name"    => "layout",
                "label"   => esc_html__("Layout", 'business-reviews-wp'),
                "id"      => "rtbr-sc-layout",
                "class"   => "rt-select2",
                "options" => $this->layouts()
            ),  
            'grid_column'                   => array(
                "type"    => "select",
                "name"    => "grid_column",
                "label"   => esc_html__("Grid column", 'business-reviews-wp'),
                "id"      => "rtbr-sc-grid-column",
                "class"   => "rt-select2",
                "default" => 2,
                "options" => $this->columns()
            ), 
            'pagination'      => array(
                "type"        => "switch",
                "name"        => "pagination",
                "label"       => esc_html__("Pagination", 'business-reviews-wp'),
                'holderClass' => "pagination",
                "id"          => "rt-rtbr-pagination",
                "option"      => esc_html__("Enable", 'business-reviews-wp')
            ),
            'reviews_per_page'  => array(
                "type"        => "number",
                "name"        => "reviews_per_page",
                "label"       => esc_html__("Review per pagination", 'business-reviews-wp'),
                'holderClass' => "reviews-per-page rtbr-hidden",
                "id"          => "reviews-per-page",
                "default"     => 5,
                "description" => esc_html__("If value of Limit setting is not blank (empty), this value should be smaller than Limit value.",
                    'business-reviews-wp')
            ), 
            'review_display_limit'  => array(
                "type"        => "number",
                "name"        => "review_display_limit",
                "label"       => esc_html__("Review display limit", 'business-reviews-wp'),
                'holderClass' => "review-display-limit",
                "id"          => "review-dispaly-limit",
                "default"     => 5,
                "description" => esc_html__("Maxmum Limit", 'business-reviews-wp')
            ),    
            'review_text_limit' => array(
                "name"        => "review_text_limit",
                "id"          => "rtbr-review-text-limit",
                "type"        => "number",
                "label"       => esc_html__("Review text limit", 'business-reviews-wp'),
                "description" => esc_html__("Review text limit only integer number is allowed, Leave it blank for full review.", 'business-reviews-wp')
            ),
            'review_text_limit_type' => array(
                "name"      => "review_text_limit_type",
                "id"        => "rtbr-review-text-limit-type",
                "type"      => "radio",
                "label"     => esc_html__("Review text limit type", 'business-reviews-wp'),
                "alignment" => "vertical",
                "default"   => 'character',
                "options"   => $this->get_limit_type(),
            ),  
            'read_more_text' => array(
                "type"  => "text",
                "label" => esc_html__("Read more text", 'business-reviews-wp'),
                "name"  => "read_more_text",
                "id"    => "read_more_text",
                'default' => 'Read More',
            ),  
        );
        return apply_filters('rtbr_section_layout_fields', $section_layout);
    }

    function sectionSelectionFields() {  
        $section_selection =  array( 
            'business_info'   => array(
                "type"        => "switch",
                "name"        => "business_info",
                "label"       => esc_html__("Hide business info?", 'business-reviews-wp'),
                "description" => esc_html__("Show only reviews", 'business-reviews-wp'), 
                "id"          => "rtbr-business-info",
                "option"      => esc_html__("Hide", 'business-reviews-wp')
            ),  
            'business_info_fields' => array(
                "type"      => "checkbox",
                "name"      => "business_info_fields",
                "label"     => esc_html__("Business info field", 'business-reviews-wp'), 
                "id"        => "rtbr-business-info-field",
                "multiple"  => true,
                "alignment" => "vertical",
                "default"   => array_keys($this->businessInfoFields()),
                "options"   => $this->businessInfoFields()
            ), 
            'review_fields' => array(
                "type"      => "checkbox",
                "name"      => "review_fields",
                "label"     => esc_html__("Review field", 'business-reviews-wp'), 
                "id"        => "rtbr-review-field",
                "multiple"  => true,
                "alignment" => "vertical",
                "default"   => array_keys($this->reviewFields()),
                "options"   => $this->reviewFields()
            ), 
            'see_all_reviews'   => array(
                "type"        => "switch",
                "name"        => "see_all_reviews",
                "label"       => esc_html__("Hide see all review link?", 'business-reviews-wp'),  
                "id"          => "rtbr-see-all-reviews",
                "option"      => esc_html__("Hide", 'business-reviews-wp')
            ),  
        );
        return apply_filters('rtbr_section_selection_fields', $section_selection);
    }

    function sectionSettingFields() {
        $settings_fields = array(
            'open_link_blank'   => array(
                "type"        => "switch",
                "name"        => "open_link_blank",
                "id"          => "rtbr-open-link-blank",
                "label"       => esc_html__("Open links in new window", 'business-reviews-wp'), 
                "option"      => esc_html__("Disable", 'business-reviews-wp')
            ), 
            'no_follow_link'  => array(
                "type"        => "switch",
                "name"        => "no_follow_link",
                "id"          => "rtbr-no-follow-link",
                "label"       => esc_html__("Use no follow links", 'business-reviews-wp'),  
                "option"      => esc_html__("Disable", 'business-reviews-wp')
            ),   
        );
        return apply_filters('rtbr_section_setting_fields', $settings_fields);
    }

    function sectionStyleFields() { 
        $style_fields = array(
            'parent_class' => array(
                "name"        => "parent_class",
                "type"        => "text",
                "label"       => "Parent class",
                "id"          => "rtbr-parent-class",
                "class"       => "medium-text", 
                "description" => esc_html__("Parent class for adding custom css", 'business-reviews-wp')
            ), 
            'width' => array(
                "name"        => "width",
                "id"          => "rtbr-width",
                "type"        => "text",
                "class"       => "small-width",
                "label"       => esc_html__("Width", 'business-reviews-wp'),
                "description" => esc_html__("Layout width, pass value like (400px, 50%)", 'business-reviews-wp')
            ),  
            'author_name'  => array(
                "name"        => "author_name",
                'type'        => 'style',
                'label'       => esc_html__( 'Author name', 'business-reviews-wp' ),
            ), 
            'author_name_hover'  => array(
                "name"        => "author_name_hover",
                'type'        => 'style',
                'label'       => esc_html__( 'Author name hover', 'business-reviews-wp' ),
            ), 
            'google_star_color' => array(
                "type"  => "text",
                "name"  => "google_star_color",
                "id"    => "rtbr-google-star-color",
                "default"  => "#e7711b",
                "label" => esc_html__("Google star color", "business-reviews-wp"), 
                "class" => "rt-color"
            ), 
            'facebook_star_color' => array(
                "type"  => "text",
                "name"  => "facebook_star_color",
                "id"    => "rtbr-facebook-star-color",
                "default"  => "#3c5b9b",
                "label" => esc_html__("Facebook star color", "business-reviews-wp"), 
                "class" => "rt-color"
            ), 
            'yelp_star_color' => array(
                "type"  => "text",
                "name"  => "yelp_star_color",
                "id"    => "rtbr-yelp-star-color",
                "default"  => "#d32323",
                "label" => esc_html__("Yelp star color", "business-reviews-wp"), 
                "class" => "rt-color"
            ), 
        );
        return apply_filters('rtbr_section_style_fields', $style_fields);
    }
 
    function layouts() {
        $layouts = array(
            'grid-one' => esc_html__('Grid One', 'business-reviews-wp'),  
            'list-one' => esc_html__('List One', 'business-reviews-wp'), 
        ); 
        return apply_filters('rtbr_layouts', $layouts);
    }

    function columns() {
        $columns = array(
            1 => esc_html__("Column 1", 'business-reviews-wp'),
            2 => esc_html__("Column 2", 'business-reviews-wp'),
            3 => esc_html__("Column 3", 'business-reviews-wp'),
            4 => esc_html__("Column 4", 'business-reviews-wp'), 
        );
        return apply_filters('rtbr_columns', $columns);
    }

    function businessType() {
        $business_type = array( 
            'google'  => esc_html__("Google", 'business-reviews-wp'),
            'facebook'  => esc_html__("Facebook", 'business-reviews-wp'),
            'yelp'  => esc_html__("Yelp", 'business-reviews-wp'), 
        ); 
        return apply_filters('rtbr_business_type', $business_type);
    }   
}