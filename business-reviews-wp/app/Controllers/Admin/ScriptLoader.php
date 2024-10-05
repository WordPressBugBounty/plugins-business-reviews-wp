<?php

namespace Rtbr\Controllers\Admin; 
use Rtbr\Helpers\Functions;

class ScriptLoader {

	private $suffix;
	private $version;
	private $ajaxurl;
	private static $wp_localize_scripts = [];

	function __construct() { 
		$this->suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';  
		$this->version = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? time() : rtbr()->version();

		$this->ajaxurl = admin_url( 'admin-ajax.php' ); 

		add_action( 'wp_enqueue_scripts', array( $this, 'register_script' ), 1 );
		add_action( 'admin_init', array( $this, 'register_admin_script' ), 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_script_setting_page' ) );  
		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_script_page_custom_fields' ) );  

		// add_action( 'wp_head', array( $this, 'shortcode_style' ) ); 
 
	}    

	function register_script_both_end() { 
 
		wp_register_style( 'rtbr-app', rtbr()->get_assets_uri( "css/app{$this->suffix}.css" ), array(), $this->version );  
		wp_register_script( 'rtbr-app', rtbr()->get_assets_uri( "js/app{$this->suffix}.js" ), array('jquery'), $this->version, true ); 
		 
	}

	function register_script() {
		$this->register_script_both_end();  
		 
		wp_enqueue_style( 'rtbr-app' );   
		wp_enqueue_script( 'rtbr-app' ); 
		
		$version = $this->version;
		$upload_dir = wp_upload_dir(); 
		$cssFile = $upload_dir['basedir'] . '/business-reviews/sc.css'; 
		if( file_exists( $cssFile ) ) { 
			$version = filemtime( $cssFile ) ;
			wp_enqueue_style( 'rtbr-sc', set_url_scheme( $upload_dir['baseurl'] ) . '/business-reviews/sc.css', array('rtbr-app') , $version );
		}
	}

	function register_admin_script() {
		$this->register_script_both_end(); 
		
		wp_register_style( 'select2', rtbr()->get_assets_uri( "vendor/select2/select2.min.css" ), array(), $this->version ); 
		wp_register_style( 'rtbr-admin', rtbr()->get_assets_uri( "css/admin{$this->suffix}.css" ), array(), $this->version );

		wp_register_script( 'select2', rtbr()->get_assets_uri( "vendor/select2/select2.min.js" ), array( 'jquery' ), $this->version, true );  
		wp_register_script( 'rtbr-admin', rtbr()->get_assets_uri( "js/admin{$this->suffix}.js" ), array( 'jquery', 'wp-color-picker' ), $this->version, true ); 
 
	}

	function load_admin_script_page_custom_fields() {
		global $pagenow, $post_type;
		if ( ! in_array( $pagenow, array( 'post.php', 'post-new.php', 'edit.php' ) ) ) {
			return;
		}
		if ( rtbr()->getPostType() != $post_type ) {
			return;
		}  

		wp_enqueue_style( 'wp-color-picker' ); 
		wp_enqueue_style( 'select2' ); 
		wp_enqueue_style( 'slick' );  
		wp_enqueue_style( 'slick-theme' );  
		wp_enqueue_style( 'rtbr-admin' ); 
		 
		wp_enqueue_script( 'select2' ); 
		wp_enqueue_script( 'slick' ); 
		wp_enqueue_script( 'rtbr-admin' ); 

		wp_enqueue_style( 'rtbr-app' );  
		wp_enqueue_script( 'rtbr-app' ); 
		

		wp_localize_script('rtbr-admin', 'rtbr',
			array(
				'nonceID' => rtbr()->getNonceId(),
				'nonce'   => rtbr()->getNonceText(),
				'ajaxurl' => admin_url('admin-ajax.php')
			)
		); 

	}

	function load_admin_script_setting_page() {
		if ( ! empty( $_GET['post_type'] ) && $_GET['post_type'] == rtbr()->getPostType() && ! empty( $_GET['page'] ) && $_GET['page'] == 'rtbr-settings' ) {
			wp_enqueue_media(); 
			wp_enqueue_style( 'rtbr-admin' );
			wp_enqueue_script( 'rtbr-admin' ); 

			wp_localize_script('rtbr-admin', 'rtbr',
				array( 
					'ajaxurl' => admin_url('admin-ajax.php')
				)
			); 

			wp_enqueue_script( 'select2' ); 
		}
	}   

	/* function shortcode_style() {
		$css = null;  
		$css .= "<style>";
		$shortcode_args = new \WP_Query(array(
			'post_type' => rtbr()->getPostType(),
			'posts_per_page' => -1
		));

		while( $shortcode_args->have_posts() ): $shortcode_args->the_post();   
			$css .= Functions::shortcode_style( get_the_ID() );
		endwhile; 
		wp_reset_postdata();
		$css .= "</style>";

		$allowed_html = [ 
			'style'  => [], 
		];
		echo wp_kses( $css, $allowed_html ); 
	}   */

}
