<?php

define_constants();
include_plugins();

//require_once( __DIR__ . '/includes/classes/class_shortcode_staff_advanced.php');
require_once( __DIR__ . '/includes/classes/class_t5_richtext_excerpt.php');
require_once( __DIR__ . '/includes/duplicate_content.php');
require_once( __DIR__ . '/includes/sender_email.php');
//require_once( __DIR__ . '/framework.php' );

//add_action('init', 'add_shortcodes_staff_advanced');
//function add_shortcodes_staff_advanced(  ) {
//    add_shortcode( 'staff_advanced', 'staff' );
//}
//
//function staff( $atts ) {
//    return SP_Shortcodes::shortcode_wrapper( 'Shortcode_Staff_Advanced::output', $atts );
//}

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
    
    if ( !IS_DEV_MODE && IS_PRODUCTION ) {
        wp_enqueue_script( 'google-analytics', get_stylesheet_directory_uri() . '/js/analyticstracking.js', false, '1.0', true );
        // make the current user available to analytics
        $current_user = wp_get_current_user();
        $user_id = (0 !== $current_user->ID ? $current_user->ID : '' );
        // hand over the userID to the analytics script
        wp_localize_script('google-analytics', 'atts', array('user_id' => $user_id, 'ga_id' => GA_ID ));
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
add_filter('mesmerize_override_with_thumbnail_image', 'override_with_thumbnail_image');

function overriden_thumbnail_image( $thumbnail ) {
    
    global $post;
    
    $post_type = $post->post_type;
    $id = $post->ID;
    if( !empty( $src = kdmfi_get_featured_image_src('featured-image-2','full', $id) ) )
        $thumbnail = $src;
    
    return $thumbnail;
}
add_filter('mesmerize_overriden_thumbnail_image', 'overriden_thumbnail_image');

function remove_mesmerize_header_background_mobile_image() {
    
    remove_action('wp_head', 'mesmerize_header_background_mobile_image');
    
}
add_action('wp_head', 'remove_mesmerize_header_background_mobile_image', 0);

function handle_profile_changes( $content, $user_id ) {
    
    $args = array();
    $posts = get_player_posts_by_user($user_id);
    
    
    if( !empty( $content ) && isset( $content['description'] ) && !empty( $posts )) {
        
        
        if( !empty( $posts ) ) {
            $post = array_shift($posts);
            $player_id = $post->ID;
        } else {
            return;
        }
        
        $array_description = get_user_meta( $user_id, 'description' );
        
        $new_description = trim($content['description']);
        $old_description = trim($array_description[0]);
        
        /*
         * Check for changes in description field and for user role 'sp_player'
         * 
         * 
         */
        fetch_current_user();
        $role = UM()->user()->get_role();
        
        switch ($role) {
            case 'sp_player':
                if( $new_description === $old_description ) {
                    return;
                }
                
                notify_pending( $user_id, $post );

                /*
                 * Disable the users player profile and notify
                 * 
                 * 
                 */
                $args['post_status'] = 'draft';

                break;
            case 'administrator':
                
                /*
                 * Enable the users player profile and notify
                 * 
                 * 
                 */
                notify_approved( $user_id );
                
                if( !is_admin() ) {
                    $args['post_status'] = 'publish';
                }
                
                break;
            default:
                
        }
        $args['post_excerpt'] = sprintf( HEADER_PLAYER_EXCERPT . '%s', $new_description );
        
    }
    return $args;
    
}

/*
 * Check for users update (UM) and copy its biography to players excerpt
 */
function before_update_um_profile( $content, $user_id ) {
    
    $player_id = get_player_id_by_user( $user_id );
    $args = handle_profile_changes( $content, $user_id );
    
    update_player( $player_id, $args );
    
    return $content;
};
add_filter( 'um_before_update_profile', 'before_update_um_profile', 10, 2 );

/*
 * listen to profile status changes and apply status also to player
 */
function after_user_status_changed( $status ) {
    
    if( isset( $_REQUEST['uid'] ) && !empty( $_REQUEST['uid'] ) ) {
        
        $uiser_id = $_REQUEST['uid'];
        $args = array(
            'post_status' => $status == 'approved' ? 'publish' : 'draft'
        );
        
        $player_id = get_player_id_by_user( $uiser_id );
        
        update_player( $player_id, $args );
    }
    
};
add_action( 'um_after_user_status_is_changed', 'after_user_status_changed', 10, 2 );

/*
 * before WP profile update
 */
function before_update_wp_profile( $user_id ) {
    
    $new_description = $_POST['description'];
    $changes = array( 'description' => wp_strip_all_tags($new_description) );
    
    $player_id = get_player_id_by_user( $user_id );
    
    // copy also the teams from wp-profile to the player
    sp_update_post_meta_recursive( $player_id, 'sp_team', array( sp_array_value( $_POST, 'sp_team', array() ) ) );
    sp_update_post_meta_recursive( $player_id, 'sp_current_team', array( sp_array_value( $_POST, 'sp_team', array() ) ) );
    
    apply_filters( 'um_before_update_profile', $changes, $user_id );
    
}
add_action( 'personal_options_update', 'before_update_wp_profile', 10, 2 );
add_action( 'edit_user_profile_update', 'before_update_wp_profile', 10, 3 );

/*
 * After user state changes to "approved" reactivate its player profile
 * 
 */
function after_user_is_approved( $user_id ) {
    
    $args = array(
        'post_status' => 'publish'
    );              
    
    $player_id = get_player_id_by_user( $user_id );
    update_player($player_id, $args);
    
}
//add_action( 'um_after_user_is_approved', 'after_user_is_approved', 10, 1 ); // updates are handled by before_update_um_profile

/*
 * Send UM Activation E-Mail and get activation url + key
 */
function after_user_registered( $user_id ) {
    
    add_filter('um_activate_url', 'create_activate_url');
    do_action('um_post_registration_checkmail_hook', $user_id, array() );
    
}

/* 
 * we must intercept server requests at a very early stage to prevent destroying the hashed key of a user that is about to register
 * since that we must now welcome the user manually
 */
function listen_to_server_requests() {
    
    if ( isset( $_REQUEST['hash'] ) && ( isset( $_REQUEST['act'] ) && $_REQUEST['act'] == 'activate_via_email' ) && ( $_REQUEST['user_id'] && !empty( $_REQUEST['user_id'] ) ) ) {

        $request = $_REQUEST;
        $user_id = absint( $_REQUEST['user_id'] );
        um_fetch_user( $user_id );
        
        // welcome user when we find the wp register hash
        if( array_key_exists( 'key', $_REQUEST ) ) {
            
            UM()->user()->approve(); // welcome email
            UM()->user()->pending(); // account needs validation email
            
        }

        unset( $_REQUEST );

        $allowed_keys = array( 'key', 'action', 'login' );
        
        foreach ($request as $key => $value) {

            if( in_array( $key, $allowed_keys) ) {
                
                $_REQUEST[$key] = $value; // rebuild it
                
            }

        }
        
    }
        
    return $_REQUEST;
}
add_action( 'init', 'listen_to_server_requests', 0 );
/*
 * see wp-login.php retrieve_password()
 */
function create_activate_url( $url ) {
    global $wpdb;
    
    $user_id = um_user('ID');
    $login = trim($_POST['user_login']);
    
//    $user = get_userdata( $user_id );
    
    
    $user_data = get_user_by('login', $login);
    
    $user_login = $user_data->user_login;
	$user_email = $user_data->user_email;
	$key = get_password_reset_key( $user_data );
    
    /*
     * wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login)
     * https://ssv-altenberg.webpremiere.dev/wp-login.php?action=rp&key=jQoGBn40xnQHx5u7EXYD&login=AAA
     * https://ssv-altenberg.webpremiere.dev/wp-login.php?action=rp&key=bqekQgrdEJ9jjM0rFYIr&login=AAA&act=activate_via_email&hash=dZe2UQxkZUeCqQVQIepyrkfbxsE294O9kyl4hA9w&user_id=84
     */
    
    $user_ = $wpdb->get_row( "SELECT * FROM $wpdb->users WHERE ID = $user_id" );
    
    
    $url .= '/wp-login.php';
    $url =  add_query_arg( 'action', 'rp', $url );
    $url =  add_query_arg( 'key', $key, $url );
    $url =  add_query_arg( 'login', rawurlencode($user_login), $url );
    
    return $url;
    
}
add_action( 'register_new_user', 'after_user_registered' );

/*
 * after email confirmation for new users is sent exit execution
 * 
 * 
 */
function after_email_confirmation() {
    
    return;
    
}
add_action( 'um_after_email_confirmation', 'after_email_confirmation', 10 );

/*
 * Set login url after user has successfully changes his passwort
 */
function login_url_after_password_change ( $login_url, $redirect='', $force_reauth='' ) {
    
    return home_url('login');
    
}
add_filter('login_url', 'login_url_after_password_change' );

/*
 * Login Redirect
 * 
 */
function admin_default_page() {
  return '/members';
}
add_filter('login_redirect', 'admin_default_page');

/*
 * checks for changes in players excerpt and updates the corresponding user profile
 * 
 */
function before_save_post(  $post ) {
    
    $type = $post['post_type'];
    
    switch ($type) {
        case 'sp_player':

            $user_id = (int) $post['post_author'];
            $excerpt = str_replace( HEADER_PLAYER_EXCERPT, '', $post['post_excerpt'] );

            $changes = array( 'description' =>  wp_strip_all_tags($excerpt) );
            
            um_fetch_user( $user_id );
            
            remove_action('wp_insert_post_data', 'before_save_post', 10 );
            UM()->user()->update_profile( $changes );
            add_action( 'wp_insert_post_data', 'before_save_post', 10, 1 );


            $changes = handle_profile_changes( $changes, $user_id );

            unset( $changes['description']);
            $changes['post_excerpt'] =  sprintf( HEADER_PLAYER_EXCERPT . '%s', $excerpt );

            $post = array_merge( $post, $changes );

            break;

        case 'x':
            
            
            
            
            break;
        default:
            break;
    }
    
    return $post;
}
add_action( 'wp_insert_post_data', 'before_save_post', 10, 1 );

function update_player( $player_id, $args = array() ) {
    
    $post = get_post( $player_id );
    
    $post_meta = get_post_meta($post->ID);
    
    foreach ($args as $key => $value) {
        $post->$key = $value;
    }
    
    remove_action('wp_insert_post_data', 'before_save_post', 10 ); // prevent infinite loop
    
    // update post
    $post_id = wp_update_post( $post, true );
    // update postmeta e.g. sp_team
//    sp_update_post_meta_recursive( $player_id, 'sp_team', array( sp_array_value( $post_meta, 'sp_team', array() ) ) );
//    sp_update_post_meta_recursive( $player_id, 'sp_current_team', array( sp_array_value( $post_meta, 'sp_team', array() ) ) );
    
    add_action( 'wp_insert_post_data', 'before_save_post', 10, 1 );
    
}

/*
 * Disable the user and notify admins
 * 
 * 
 */
function notify_pending( $user_id ) {
    
    um_fetch_user( $user_id );
    
    $emails = um_multi_admin_email();
	if ( ! empty( $emails ) ) {
		foreach ( $emails as $email ) {
            UM()->mail()->send( $email, 'notification_review', array( 'admin' => true ) );
		}
        if( UM()->user()->is_approved( $user_id )) {
            UM()->user()->pending();
        }
	}
    
}
function notify_approved( $user_id ) {
    
    um_fetch_user( $user_id );
    
    if( !UM()->user()->is_approved( $user_id )) {
        UM()->user()->approve();
    }
    
}
function fetch_current_user() {
    
    um_fetch_user( get_current_user_id() );
    
}
function get_player_id_by_user( $user_id ) {
    
    $posts = get_player_posts_by_user($user_id);
    
    if( !empty( $posts ) ) {
        
        $post = array_shift($posts);
        $player_id = $post->ID;
        
        return $player_id;
    }
    
    return FALSE;
    
}
function get_user_id_by_author( $author_id ) {
    
    $post = get_post( $author_id );
    if( isset( $post->post_author ) ) {
        return $post->post_author;
    }
    return false;
    
}
function get_player_posts_by_user( $user_id ) {
    
    $args = array(
        'author' => (int) $user_id,
        'post_type' => 'sp_player',
        'post_status' => array( 'any' ),
    );              
    
    $the_query = new WP_Query( $args );
    $posts = $the_query->posts;
    
    return $posts;
    
}

 /*
  * Add team to profile grid or cover
  * 
  * 
  * 
  */
function print_teams( $user_id = null ) {
    
    if( is_array($user_id) ) {
        $user_id = um_user('ID');
        $player_id = get_player_id_by_user( $user_id );
        
    } else {
        $player_id = get_player_id_by_user( $user_id );
    }
    
    if( $player_id ) {
        
        $team = get_post_meta( $player_id, 'sp_current_team', true );
        $team_name = sp_team_short_name( $team );
        $team_name = '<a href="' . get_post_permalink( $team ) . '">' . $team_name . '</a>';
        
    } else {
        
        $team_name = 'Mitarbeiter';
        
    }
    
    echo sprintf('<div class="member-of-team"><p>%s</p></div>', $team_name );
    
}
add_action('um_members_just_after_name', 'print_teams', 10 );
add_action( 'um_profile_header_cover_area', 'print_teams', 10 );

/*
 * Filter Slideshow Category and stay within
 * 
 */
function check_for_slideshow_categories() {
    global $post;
    
    $terms = get_the_category($post->ID);
    return has_category_name($terms, 'slideshow');
    
}
add_filter( 'is_slideshow', 'check_for_slideshow_categories' );

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
add_filter( 'exclude_other_categories', 'exclude_other' );
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
    if( !defined('HEADER_PLAYER_EXCERPT') ) {
        define( 'HEADER_PLAYER_EXCERPT', '<h4>Kurzbiografie:</h4>' );
    } 
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

// remove the redirect UM plugin provides for new users (UM -> core -> um-actions-register.php)
remove_action('login_form_register', 'um_form_register_redirect', 10);

function get_the_teams( $args ) {
    $defaults = array(
        'id' => null,
        'numberposts' => -1
    );
    $args = array_merge( $defaults, $args );

    $posts = get_posts( $args );
    
    return $posts;
}
function get_teams() {
    $args = array(
        'post_type' => 'sp_team',
        'values' => 'ID',
    );
    return get_the_teams( $args );
}
//add_filter( 'init', 'get_teams' );
function get_players( $team ) {
    $args = array(
        'post_type' => 'sp_player',
        'numberposts' => -1,
        'posts_per_page' => -1,
        'meta_key' => 'sp_number',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
        'tax_query' => array(
            'relation' => 'AND',
        ),
        'meta_query' => array(
            array(
                'key' => 'sp_team',
                'value' => '138'
            ),
        )
    );
    
    return get_posts($args);
}
//add_filter( 'init', 'get_players' );

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_separate', trailingslashit( get_stylesheet_directory_uri() ) . 'ctc-style.css', array( 'mesmerize-pro-style','mesmerize-style','companion-pro-page-css','mesmerize-font-awesome','animate','mesmerize-webgradients','jquery-fancybox','kirki-styles-mesmerize' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css' );

// END ENQUEUE PARENT ACTION