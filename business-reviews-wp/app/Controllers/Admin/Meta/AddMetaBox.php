<?php 
namespace Rtbr\Controllers\Admin\Meta; 

use Rtbr\Helpers\Functions;
use Rtbr\Controllers\Admin\Meta\MetaOptions;

class AddMetaBox {
    function __construct() {
        // actions
        add_action('admin_head', array($this, 'admin_head')); 
        add_action('edit_form_after_title', array($this, 'rtbr_sc_after_title'));
        add_action('save_post', array($this, 'save_post'), 10, 2);
        add_filter('manage_edit-rtbr_columns', array($this, 'arrange_rtbr_columns'));
        add_action('manage_rtbr_posts_custom_column', array($this, 'manage_rtbr_columns'), 10, 2);
    }

    public function manage_rtbr_columns($column) {
        switch ($column) {
            case 'shortcode':
                echo '<input type="text" onfocus="this.select();" readonly="readonly" value="[rt-business-review id=&quot;' . get_the_ID() . '&quot; title=&quot;' . get_the_title() . '&quot;]" class="large-text code rt-code-sc">';
                break;
            default:
                break;
        }
    }

    function arrange_rtbr_columns($columns) {
        $shortcode = array('shortcode' => esc_html__('Shortcode', 'business-reviews-wp')); 
        return array_slice($columns, 0, 2, true) + $shortcode + array_slice($columns, 1, null, true);
    } 

    function admin_head() {
        add_meta_box(
            'rtbr_meta',
            esc_html__('Short Code Generator', 'business-reviews-wp'),
            array($this, 'rtbr_meta_settings_selection'),
            rtbr()->getPostType(),
            'normal',
            'high');
        add_meta_box(
            rtbr()->getPostType() . '_sc_preview_meta',
            esc_html__('Layout Preview', 'business-reviews-wp'),
            array($this, 'sc_preview_selection'),
            rtbr()->getPostType(),
            'normal',
            'high');

        add_meta_box(
            'rt_plugin_sc_pro_information',
            esc_html__('Documentation', 'business-reviews-wp'),
            array($this, 'rt_plugin_sc_pro_information'),
            rtbr()->getPostType(),
            'side',
            'low'
        );
    }

    function rt_plugin_sc_pro_information($post) {
        $html = '';
       
        if ( ! function_exists('rtbrp') ) {
            $html .= sprintf('<div class="rt-document-box">
                            <div class="rt-box-icon"><i class="dashicons dashicons-awards"></i></div>
                            <div class="rt-box-content">
                                <h3 class="rt-box-title">Pro Features</h3> 
                                    <ol>
                                        <li>Support Additional Layouts</li>
                                        <li>Multiple Review Type</li>
                                        <li>Minimum Rating Filter</li>
                                        <li>Word Filter</li>
                                        <li>Review Sorting</li>
                                        <li>Google Rich Snippet</li>
                                        <li>Advance Layout Styling</li>
                                    </ol>
                                    <a href="https://www.radiustheme.com/downloads/business-review/?utm_source=WordPress&utm_medium=business-review&utm_campaign=pro_click" target="_blank" class="rt-admin-btn">Get Pro Version</a>
                            </div>
                        </div>', 
            );
        }

        $html .= sprintf('<div class="rt-document-box">
                        <div class="rt-box-icon"><i class="dashicons dashicons-media-document"></i></div>
                        <div class="rt-box-content">
                            <h3 class="rt-box-title">%1$s</h3>
                                <p>%2$s</p>
                                <a href="https://www.radiustheme.com/docs/business-reviews/business-reviews/" target="_blank" class="rt-admin-btn">%1$s</a>
                        </div>
                    </div>',
            esc_html__("Documentation", 'business-reviews-wp'),
            esc_html__("Get started by spending some time with the documentation we included step by step process with screenshots with video.", 'business-reviews-wp')
        );

        $html .= '<div class="rt-document-box">
                        <div class="rt-box-icon"><i class="dashicons dashicons-sos"></i></div>
                        <div class="rt-box-content">
                            <h3 class="rt-box-title">' . esc_html__( 'Need Help?', 'business-reviews-wp' ) . '</h3>
                                <p>' . esc_html__( 'Stuck with something? Please create a', 'business-reviews-wp' ) . ' 
                    <a href="https://www.radiustheme.com/contact/">' . esc_html__( 'ticket here', 'business-reviews-wp' ) . '</a> ' . esc_html__( 'or post on ', 'business-reviews-wp' ) . '<a href="https://www.facebook.com/groups/234799147426640/">' . esc_html__( 'facebook group', 'business-reviews-wp' ) . '</a>. ' . esc_html__( 'For emergency case join our', 'business-reviews-wp' ) . ' <a href="https://www.radiustheme.com/">' . esc_html__( 'live chat', 'business-reviews-wp' ) . '</a>.</p>
                                <a href="https://www.radiustheme.com/contact/" target="_blank" class="rt-admin-btn">' . esc_html__("Get Support", "business-reviews-wp") . '</a>
                        </div>
                    </div>';

        echo $html;
    }


    /**
     *  Preview section
     */
    function sc_preview_selection() {
        $html = null;
        $html .= "<div class='rt-response'></div>";
        $html .= "<div id='rtbr-preview-container'></div>";
        echo $html;

    }

    function rtbr_sc_after_title($post) {
        if (rtbr()->getPostType() !== $post->post_type) {
            return;
        }
        $html = null;
        $html .= '<div class="postbox rt-after-title" style="margin-bottom: 0;"><div class="inside">';
        $html .= '<p><input type="text" onfocus="this.select();" readonly="readonly" value="[rt-business-review id=&quot;' . $post->ID . '&quot; title=&quot;' . $post->post_title . '&quot;]" class="large-text code rt-code-sc">
        <input type="text" onfocus="this.select();" readonly="readonly" value="&#60;&#63;php echo do_shortcode( &#39;[rt-business-review id=&quot;' . $post->ID . '&quot; title=&quot;' . $post->post_title . '&quot;]&#39; ); &#63;&#62;" class="large-text code rt-code-sc">
        </p>';
        $html .= '</div></div>';

        echo $html;
    }

    function rtbr_meta_settings_selection($post) {
        $post = array(
            'post' => $post
        );
        wp_nonce_field(rtbr()->getNonceText(), rtbr()->getNonceId());
        $html = null;
        $html .= '<div id="sc-tabs" class="rt-tab-container">';
        $html .= '<ul class="rt-tab-nav">
                <li class="active"><a href="#sc-post-layout"><i class="dashicons dashicons-layout"></i>' . esc_html__('Layout', 'business-reviews-wp') . '</a></li>  
                <li><a href="#sc-field-selection"><i class="dashicons dashicons-editor-table"></i>' . esc_html__('Field Selection', 'business-reviews-wp') . '</a></li>
                <li><a href="#sc-settings"><i class="dashicons dashicons-admin-tools"></i>' . esc_html__('Settings', 'business-reviews-wp') . '</a></li>
                <li><a href="#sc-style"><i class="dashicons dashicons-admin-customizer"></i>' . esc_html__('Style', 'business-reviews-wp') . '</a></li>
                </ul>'; 

        $html .= '<div id="sc-post-layout" class="rt-tab-content" style="display: block">';
        $html .= rtbr()->render('metas.layout', $post, true); 
        $html .= '</div>'; 

        $html .= '<div id="sc-field-selection" class="rt-tab-content">';
        $html .= rtbr()->render('metas.field-selection', $post, true);
        $html .= '</div>';

        $html .= '<div id="sc-settings" class="rt-tab-content">';
        $html .= rtbr()->render('metas.settings', $post, true);
        $html .= '</div>';
        
        $html .= '<div id="sc-style" class="rt-tab-content">';
        $html .= rtbr()->render('metas.style', $post, true);
        $html .= '</div>'; 

        $html .= '</div>';
        echo $html;
    } 

    function save_post($post_id, $post) {

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        if (!Functions::verify_nonce()) {
            return $post_id;
        }

        if (rtbr()->getPostType() != $post->post_type) {
            return $post_id;
        }

        $meta_options = new MetaOptions();

        foreach ( $meta_options->allMetaFields() as $field ) { 
            if (isset($field['multiple'])) {
                if ($field['multiple']) {
                    delete_post_meta($post_id, $field['name']);
                    $mValueA = isset($_REQUEST[$field['name']]) ? array_map( 'sanitize_text_field', $_REQUEST[$field['name']] )  : array();
                    if (is_array($mValueA) && !empty($mValueA)) {
                        foreach ($mValueA as $item) {
                            add_post_meta($post_id, $field['name'], trim($item));
                        }
                    }
                }
            } else {   

                switch ( $field['name'] ) {
                    case 'business_type':
                    case 'layout':
                    case 'width':
                    case 'img_border_radius':
                    case 'review_text_limit_type':
                    case 'read_more_text':
                        $fValue = isset( $_REQUEST[$field['name']] ) ? sanitize_text_field( $_REQUEST[$field['name']] ) : null; 
                        break;
                   
                    case 'multi_business':
                    case 'business_info_fields':
                    case 'review_fields':
                    case 'business_title':
                    case 'business_title_hover':
                    case 'author_name':
                    case 'author_name_hover':
                    case 'review_text':
                    case 'time_ago_text':
                    case 'total_review_text':
                    case 'powered_by_text':
                        $fValue = isset( $_REQUEST[$field['name']] ) ? array_map( 'sanitize_text_field', $_REQUEST[$field['name']] ) : null;  
                        break; 

                    case 'grid_column':
                    case 'reviews_per_page':
                    case 'review_text_limit':
                        $fValue = isset( $_REQUEST[$field['name']] ) ? absint( $_REQUEST[$field['name']] ) : null;
                        break;

                    case 'pagination':
                    case 'business_info':
                    case 'see_all_reviews':
                    case 'direct_review_link':
                    case 'open_link_blank':
                    case 'no_follow_link':
                    case 'google_rich_snippet':
                        $fValue = isset( $_REQUEST[$field['name']] ) ? absint( $_REQUEST[$field['name']] ) : null;
                        break;  

                    case 'google_star_color':
                    case 'facebook_star_color':
                    case 'yelp_star_color':
                    case 'review_border_color':
                    case 'review_bg_color':
                        $fValue = isset( $_REQUEST[$field['name']] ) ? sanitize_hex_color( $_REQUEST[$field['name']] ) : null; 
                        break;  
                    
                    default: 
                        $fValue = isset( $_REQUEST[$field['name']] ) ? sanitize_text_field( $_REQUEST[$field['name']] ) : null;
                        break;
                }

                update_post_meta($post_id, $field['name'], $fValue); 
            }
        } 

        Functions::generatorShortCodeCss($post_id);

    } // end function 
}
 