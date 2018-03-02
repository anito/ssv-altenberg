<?php

require_once( __DIR__ . '/includes/rookie-header-area.php');
require_once( __DIR__ . '/includes/classes/class_shortcode_staff_advanced.php');

function woocommerce_styles() {
		return array(
				'woocommerce-layout' => array(
				'src'     => get_stylesheet_directory_uri() . '/css/woocommerce-layout.css' ,
				'deps'    => '',
				'version' => WC_VERSION,
				'media'   => 'all',
				'has_rtl' => true,
				),
				'woocommerce-general' => array(
				'src'     => get_stylesheet_directory_uri() . '/css/woocommerce.css' ,
				'deps'    => '',
				'version' => WC_VERSION,
				'media'   => 'all',
				'has_rtl' => true,
				),
				'woocommerce-smallscreen' => array(
				'src'     => get_stylesheet_directory_uri() . '/css/woocommerce-smallscreen.css' ,
				'deps'    => 'woocommerce-layout',
				'version' => WC_VERSION,
				'media'   => 'only screen and (max-width: ' . apply_filters( 'woocommerce_style_smallscreen_breakpoint', $breakpoint = '600px' ) . ')',
				'has_rtl' => true,
				),
		);
};

function add_scripts() {
		
		function wpb_add_google_fonts() {
		}

		
//	Load Google Fonts if mobile
		if ( wp_is_mobile() ) {
				
//				wp_dequeue_style( 'rookie-style');
//				wp_dequeue_style( 'rookie-framework-style');
//				wp_dequeue_style( 'rookie-framework-rtl-style');
				
				// Remove standard ThemeBoy Font Family
				wp_dequeue_style( 'emblem-titillium-web');
				//	Load Google Fonts
				wp_enqueue_style( 'wpb-google-fonts', add_query_arg( 'family', 'Lato:100,100i,300,300i,400,400i,700,700i,900,900i|Oswald:200,300,400,500,600,700', "//fonts.googleapis.com/css", array(), null ) );

		}
		
		wp_enqueue_script( 'navigation', get_stylesheet_directory_uri() . '/js/navigation.js', array('jquery'), '1.0', true );
		if ( !IS_DEV_MODE ) {
				// Register analyticstracking.js file (Google Analytics)
				wp_enqueue_script( 'google-analytics', get_stylesheet_directory_uri() . '/js/analyticstracking.js', false, '1.0', true );
			}
		
		wp_enqueue_script( 'fancybox-helper', get_stylesheet_directory_uri() . '/js/fancybox-helper.js', array('jquery'), '1.0', true );
		wp_enqueue_script( 'fancybox-', get_stylesheet_directory_uri() . '/js/jquery.fancybox.min.js' );
		
		wp_enqueue_style( 'fancybox-css', get_stylesheet_directory_uri() . '/css/jquery.fancybox.min.css' );
		
//		wp_enqueue_style( 'woocommerce-css', get_stylesheet_directory_uri() . '/css/woocommerce.css' );
		wp_enqueue_style( 'sportspress-css', get_stylesheet_directory_uri() . '/css/sportspress.css' );
		wp_enqueue_style( 'framework-css', get_stylesheet_directory_uri() . '/css/framework.css' );
		wp_enqueue_style( 'wpcf7-css', get_stylesheet_directory_uri() . '/css/wpcf7.css' );
		wp_enqueue_style( 'document-gallery-widget-css', get_stylesheet_directory_uri() . '/css/document-gallery.css' );
		wp_enqueue_style( 'forms-css', get_stylesheet_directory_uri() . '/css/forms.css' );
		wp_enqueue_style( 'menu-css', get_stylesheet_directory_uri() . '/css/menu.css' );
		
}

function is_mobile() {
		
		if ( wp_is_mobile() ) {

				$localize_params = 	array(
					'ajaxurl' => get_bloginfo( 'wpurl' ) . '/wp-admin/admin-ajax.php',
					'siteurl' => str_replace( array( 'http://' . $_SERVER['SERVER_NAME'] . '','https://' . $_SERVER['SERVER_NAME'] . '' ), '', get_bloginfo( 'url' ) . '/' ),
					'security_nonce' => wp_create_nonce( 'wptouch-ajax' ),
					'current_shortcode_url' => add_query_arg( array( 'wptouch_shortcode' => '1' ), esc_url_raw( $_SERVER[ 'REQUEST_URI' ] ) ),
					'query_vars' => $query_vars
				);
				
				wp_enqueue_script( 'wptouch-front-ajax', WPTOUCH_URL . '/include/js/wptouch.min.js', array( 'jquery' ), md5( WPTOUCH_VERSION ), true );
				wp_localize_script( 'wptouch-front-ajax', 'wptouchMain', apply_filters( 'wptouch_localize_scripts', $localize_params  ) );


				apply_filters( 'wptouch_page_menu_walker' );
				apply_filters( 'foundation_menu_inline_style' );
				apply_filters( 'foundation_module_init_mobile' );

				wptouch_load_framework();
		}
}

function add_shortcodes_staff_advanced(  ) {
		
		add_shortcode( 'staff_advanced', 'staff' );
		
}
function staff( $atts ) {
		
		return SP_Shortcodes::shortcode_wrapper( 'Shortcode_Staff_Advanced::output', $atts );

}

function add_login_link( $items, $args ){
		
		$html = '<li class="menu-item">';
		
		if ( !is_user_logged_in() ) { 
				$html .=			'<a class="login-toggle tappable" href="' . wp_login_url( esc_url_raw( $_SERVER['REQUEST_URI'] ) ) .'">
								<i class="wptouch-icon-key"></i> Login
							</a>';
		} else {
				$html .=			'<a href="' . wp_logout_url( esc_url_raw( $_SERVER['REQUEST_URI'] ) ) . '" class="tappable" title="' . __( 'Logout' ) . '">
								<i class="wptouch-icon-user"></i>' . __( 'Logout' )  .
							'</a>';
		}
		$html .=	'</li>';
		
    if( $args->theme_location == 'primary' ){
        return $items . $html;
    }
    return $items;
}

function hasSearch() {

		$options = get_option('themeboy', array());

		if (!array_key_exists('nav_menu_search', $options) || $options['nav_menu_search']) {
				$has_search = true;
		} else {
				$has_search = false;
		}
		
		return $has_search;
}

function logo() {
		
		$options = get_option( 'themeboy', array() );
		if ( array_key_exists( 'logo_url', $options ) && ! empty( $options['logo_url'] ) ) {
				$logo = $options['logo_url'];
				$logo = esc_url( set_url_scheme( $logo ) );
				
				return $logo;
		}
		
		return false;
		
}

function prevent_metaslider_slideshow_output( $items ) {
		return $items;
//		return false;
}

function test( $image ) {
		
		return $image;
		
}

add_shortcodes_staff_advanced();

//add_filter( 'woocommerce_enqueue_styles', 'woocommerce_styles' );
//add_filter( 'wp_nav_menu_items', 'add_login_link', 10, 2 );
//add_action( 'init', 'is_mobile' );
//add_action( 'wp_enqueue_scripts', 'add_scripts', 20 );
remove_filter('the_title', 'sportspress_the_title'); //prevents double role in staff (photo) shortcode
//add_filter( 'metaslider_slideshow_output', 'prevent_metaslider_slideshow_output', 10 ); //no meta slideshow container
//add_filter( "metaslider_get_image_slide", 'test', 11); //no meta slideshow container

add_action( 'login_enqueue_scripts', 'login_logo'  );

//add_filter( 'woocommerce_breadcrumb_defaults', 'jk_woocommerce_breadcrumbs' );
function jk_woocommerce_breadcrumbs() {
    return array(
            'delimiter'   => ' &raquo; ',
            'wrap_before' => '<nav class="woocommerce-breadcrumb" itemprop="breadcrumb">',
            'wrap_after'  => '</nav>',
            'before'      => '',
            'after'       => '',
            'home'        => _x( 'Home', 'breadcrumb', 'woocommerce' ),
        );
}

//add_filter( 'woocommerce_breadcrumb_home_url', 'woo_custom_breadrumb_home_url' );
function woo_custom_breadrumb_home_url() {
    return home_url('shop');
}

//add_filter( 'wptouch_menu_start_html', 'example_callback' , 1 );
add_filter( 'upload_mimes', 'allow_svg_upload' );
function allow_svg_upload( $m ) {
    $m['svg'] = 'image/svg+xml';
    $m['svgz'] = 'image/svg+xml';
    return $m;
}