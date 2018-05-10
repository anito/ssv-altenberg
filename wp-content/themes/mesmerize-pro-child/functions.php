<?php

define_constants();
include_plugins();

require_once( __DIR__ . '/includes/rookie-header-area.php');
require_once( __DIR__ . '/includes/classes/class_shortcode_staff_advanced.php');
require_once( __DIR__ . '/includes/classes/class_t5_richtext_excerpt.php');
//require_once( __DIR__ . '/framework.php' );

add_action('init', 'add_shortcodes_staff_advanced');
function add_shortcodes_staff_advanced(  ) {
    add_shortcode( 'staff_advanced', 'staff' );
}

function staff( $atts ) {
    return SP_Shortcodes::shortcode_wrapper( 'Shortcode_Staff_Advanced::output', $atts );
}

// Declare SportsPress support.
add_theme_support( 'sportspress' );

// Declare Mega Slider support.
add_theme_support( 'mega-slider' );

// Declare Social Sidebar support.
add_theme_support( 'social-sidebar' );

// Declare News Widget support.
add_theme_support( 'news-widget' );

add_action('wp_enqueue_scripts', 'add_styles');
function add_styles() {
    
    wp_enqueue_style('mesmerize-pro-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_script( 'fancybox-helper', get_stylesheet_directory_uri() . '/js/fancybox-helper.js', array('jquery-fancybox'), '1.0', true );
//    wp_enqueue_script('utilities', get_stylesheet_directory_uri() . '/js/utils.js', array( 'mesmerize-theme' ), '1.0', true);
    
    if ( !IS_DEV_MODE ) {
        /*
         * Google Analytics
         */
        wp_enqueue_script( 'google-analytics', get_stylesheet_directory_uri() . '/js/analyticstracking.js', false, '1.0', true );
        // make the current user available to analytics
        $current_user = wp_get_current_user();
        if (0 == $current_user->ID) {
            // Not logged in.
            $id = '';
        } else {
            // Logged in.
            $id = $current_user->ID;
        }
        // hand over the userID to the analytics script
        wp_localize_script('google-analytics', 'user', array('id' => $id));
        /*
         * End Google Analytics
         */
    }
		
}

add_filter( 'upload_mimes', 'allow_svg_upload' );
function allow_svg_upload( $m ) {
    $m['svg'] = 'image/svg+xml';
    $m['svgz'] = 'image/svg+xml';
    return $m;
}

add_filter( 'login_headerurl', function() {
    return site_url();
});
add_filter( 'login_headertitle', function() {
    return get_option('blogname');
});

add_action( 'sportspress_before_single_player', 'do_before_single_player' );
function do_before_single_player($arg) {
}

add_action( 'sportspress_after_single_player', 'do_after_single_player' );
function do_after_single_player($arg) {
}

add_action( 'sportspress_single_staff_content', 'staff_content' );
function staff_content() {
}

/*
 * FEATURED IMAGE 2
 * 
 * Using additional Featured Image (besides Team Logo) for Team Pages as Hero Image
 */
add_filter('featured_image_2_supported_post_types', function() {
        
    return array( 'sp_team' );

});
add_filter('kdmfi_featured_images', function( $featured_images ) {
    
    $supported_post_types = apply_filters( 'featured_image_2_supported_post_types', array() );
    
    $args = array(
        'id' => 'featured-image-2',
        'desc' => 'Team Bild das im Header angezeigt wird',
        'label_name' => 'Team Bild',
        'label_set' => 'Teambild festlegen',
        'label_remove' => 'Team Bild entfernen',
        'label_use' => 'Teambild festlegen',
        'post_type' => apply_filters('featured_image_2_supported_post_types', array() )//array( 'sp_team' )
    );

    $featured_images[] = $args;

    return $featured_images;
});
add_filter('mesmerize_override_with_thumbnail_image', 'override_with_thumbnail_image');
function override_with_thumbnail_image() {
    
    global $post;
    
    $post_type = $post->post_type;
    $post_types = array( 'page', 'post' );
    $supported_post_types = array_merge( $post_types, apply_filters('featured_image_2_supported_post_types', array() ) );

    if (isset($post) && in_array($post_type, $supported_post_types)) {
        return TRUE;
    }
    return FALSE;
}
add_filter('mesmerize_overriden_thumbnail_image', 'overriden_thumbnail_image');
function overriden_thumbnail_image( $thumbnail ) {
    
    global $post;
    
    $post_type = $post->post_type;
    $id = $post->ID;
    if( !empty( $src = kdmfi_get_featured_image_src('featured-image-2','full', $id) ) )
        $thumbnail = $src;
    
    return $thumbnail;
}

add_action('wp_head', 'remove_mesmerize_header_background_mobile_image', 0);
function remove_mesmerize_header_background_mobile_image() {
    
    remove_action('wp_head', 'mesmerize_header_background_mobile_image');
    
}

/*
 * Copy Users Biography to Players Excerpt
 */
add_filter('um_before_update_profile', 'before_update_profile', 10, 2 );
function before_update_profile( $content, $user_id ) {
    
    $args = array(
        'author' => (int) $user_id,
        'post_type' => 'sp_player',
        'post_status' => array( 'any' ),
    );              
    
    $the_query = new WP_Query( $args );
    $request = $the_query->request;
    $posts = $the_query->posts; 
    
    if( !empty( $content ) && isset( $content['description'] ) && !empty( $posts )) {
        
        $new_description = $content['description'];
        $user_description = get_user_meta( $user_id, 'description' );
        $old_description = $user_description[0];
        $excerpt = '<h4>Kurzbiografie:</h4><p>' . $new_description . '</p>';
        $post = array_shift($posts);
        $player_id = $post->ID;
        
        $id = wp_update_post(
            array(
                'ID' => $post->ID,
                'post_excerpt' => $excerpt
        ));
        
        um_fetch_user( $user_id );
        if ( UM()->user()->get_role() == 'sp_player' && $new_description !== $old_description) {
            UM()->user()->pending();
            /*
             * Notify all Admins
             */
            send_pending_notification( $user_id );
		}
        
    }
    return $content;
};
function send_pending_notification( $user_id ) {
    
    um_fetch_user( $user_id );
    
    $allowed_status = array( 'pending', 'checkmail' );
    $emails = um_multi_admin_email();
	if ( ! empty( $emails ) ) {
		foreach ( $emails as $email ) {
            $status = um_user( 'status' );
			if (in_array( $status, $allowed_status ) ) {
				UM()->mail()->send( $email, 'notification_review', array( 'admin' => true ) );
			}
		}
	}
}

/*
 * Filter Slideshow Category and stay within
 * 
 */
add_filter( 'is_slideshow', 'check_for_slideshow_categories' );
function check_for_slideshow_categories() {
    global $post;
    
    $terms = get_the_category($post->ID);
    return has_category_name($terms, 'slideshow');
    
}
function has_category_name( $terms = array(), $name ) {
    
    if(!empty($terms)) {

        foreach ($terms as $term) {
            if(term_has_name($term, $name) )
                return true;
        }
    };
    return false;
}
function term_has_name( $term, $name ) {
    if( is_object($term) && isset( $term->term_id ) && term_exists($term->term_id, 'category') && strpos($term->slug, $name) === 0) {
        return true;
    }
}
add_filter( 'exclude_other_categories', 'exclude_other' );
function exclude_other( $slideshow_ids ) {
    $terms = get_terms('category', array(
        'exclude' => $slideshow_ids
    ));
    foreach( $terms as $term) {
        
        $id = absint( $term->term_id );
        $ids[] = $id;
        
    }
    return $ids;
}
/*
 * Add T5 Functionality to Excerpts
 */
add_action( 'add_meta_boxes', array ( 'T5_Richtext_Excerpt', 'switch_boxes' ) );

/**
 * Define ThemeBoy Constants.
 */
function define_constants() {
    define( 'THEMEBOY_FILE', __FILE__ );
    if ( !defined( 'MEGA_SLIDER_URL' ) )
        define( 'MEGA_SLIDER_URL', get_stylesheet_directory_uri() . '/plugins/mega-slider/' );
    if ( !defined( 'MEGA_SLIDER_DIR' ) )
        define( 'MEGA_SLIDER_DIR', get_stylesheet_directory() . '/plugins/mega-slider/' );
    if ( !defined( 'NEWS_WIDGET_URL' ) )
        define( 'NEWS_WIDGET_URL', get_stylesheet_directory_uri() . '/plugins/news-widget/');
    if ( !defined( 'NEWS_WIDGET_DIR' ) )
        define( 'NEWS_WIDGET_DIR', get_stylesheet_directory() . '/plugins/news-widget/');
    if ( !defined( 'SOCIAL_SIDEBAR_URL' ) )
        define( 'SOCIAL_SIDEBAR_URL', get_stylesheet_directory_uri() . '/plugins/social-sidebar/' );
    if ( !defined( 'SOCIAL_SIDEBAR_DIR' ) )
        define( 'SOCIAL_SIDEBAR_DIR', get_stylesheet_directory() . '/plugins/social-sidebar/' );
}

/**
 * get generic players photo for players gender
 */
function get_players_gender_photo_filename($id) {
    
    if ( ! isset( $player ) )
        $player = new SP_Player( $id );
    
    $photo_filename = 'team-5.jpg';
    
    $metrics = array_map('strtolower', $player->metrics( false ));
    if(isset($metrics['Geschlecht']) )
        if($metrics['Geschlecht'] === 'm')
            $photo_filename = 'team-2.jpg';
        elseif ($metrics['Geschlecht'] === 'w')
            $photo_filename = 'team-8.jpg';
    return $photo_filename;
}
/**
 * Include plugins.
 */
function include_plugins() {
    require_once( __DIR__ . '/plugins/mega-slider/mega-slider.php');
    require_once( __DIR__ . '/plugins/news-widget/news-widget.php');
    require_once( __DIR__ . '/plugins/social-sidebar/social-sidebar.php');
}

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_separate', trailingslashit( get_stylesheet_directory_uri() ) . 'ctc-style.css', array( 'mesmerize-pro-style','mesmerize-style','companion-pro-page-css','mesmerize-font-awesome','animate','mesmerize-webgradients','jquery-fancybox','kirki-styles-mesmerize' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css' );

// END ENQUEUE PARENT ACTION
