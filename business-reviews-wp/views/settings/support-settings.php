<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} 
/**
 * Support Settings
 */ 
?>
<style>
.rtbr-support {
	display: flex; 
	gap: 30px;
	margin-top: 30px;
}
.rtbr-support-box {
	display: table;
}
.rt-document-box + .rt-document-box {
  margin-top: 0;
}
.rt-document-box .rt-box-content p {
	margin: 0 0 15px;
}
.rtbr-support-subhead {
	font-weight: 500;
    color: #444 !important;
    margin-bottom: -5px !important;
}
</style>
<div class="wrap rtbr-support" >  
	<div class="rt-document-box">
		<div class="rt-box-icon"><i class="dashicons dashicons-media-document"></i></div>
		<div class="rt-box-content">
			<h3 class="rt-box-title">How to use Widget for Google Reviews?</h3>
			<iframe width="560" height="315" src="https://www.youtube.com/embed/DfJ1z3bXxuk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			
			<h3 class="rt-box-title" style="margin-top: 20px;"><?php esc_html_e( "Online Documentation", "business-reviews-wp" ); ?></h3>
			<p>
				<?php _e( "From our online documentation, you will know how to use our pluign. <br> If you face any issue please create a ticket. We will provide you solution as soon as possible.", "business-reviews-wp" ); ?> 
			</p> 
			<a class="rt-admin-btn" target="_blank" href="https://www.radiustheme.com/docs/business-reviews/business-reviews/" target="_blank"><?php esc_html_e( "Online documentation", "business-reviews-wp" ); ?></a>
			<a class="rt-admin-btn" target="_blank" href="https://www.radiustheme.com/contact/" target="_blank"><?php esc_html_e( "Get Support", "testimonial-slider-showcase" ); ?></a>
		</div>
	</div> 
	
	<?php if ( ! function_exists( 'rtbrp' ) ) { ?>
	<div class="rt-document-box">
		<div class="rt-box-icon"><i class="dashicons dashicons-awards"></i></div>
		<div class="rt-box-content">
			<h3 class="rt-box-title">Pro Features</h3> 
			<ol style="margin-left: 13px;">
				<li>Support Additional Layouts</li>
				<li>Multiple Review Type</li>
				<li>Minimum Rating Filter</li>
				<li>Word Filter</li>
				<li>Review Sorting</li>
				<li>Google Rich Snippet</li>
				<li>Advance Layout Styling</li>
			</ol> 
			<a href="https://www.radiustheme.com/downloads/business-review/?utm_source=WordPress&utm_medium=business-review&utm_campaign=pro_click" class="rt-admin-btn" target="_blank">Get Pro Version</a>
		</div>
	</div>  
	<?php } ?>
</div><!-- /.wrap rtbr-support -->
<?php
/**
 * Support Settings
 */
$options = array();

return apply_filters( 'rtbr_support_settings_options', $options );