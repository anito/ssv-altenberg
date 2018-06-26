<?php

define_constants();
include_plugins();

//require_once( __DIR__ . '/includes/classes/class_shortcode_staff_advanced.php');
require_once( __DIR__ . '/includes/classes/class_t5_richtext_excerpt.php');
require_once( __DIR__ . '/includes/duplicate_content.php');
require_once( __DIR__ . '/includes/sender_email.php');
//require_once( __DIR__ . '/framework.php' );

//add_action('init', 'add_shortcodes_staff_advanced');
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
add_action('wp_enqueue_scripts', 'add_styles');

function allow_svg_upload( $m ) {
    $m['svg'] = 'image/svg+xml';
    $m['svgz'] = 'image/svg+xml';
    return $m;
}
add_filter( 'upload_mimes', 'allow_svg_upload' );

add_filter( 'login_headerurl', function() {
    return site_url();
});
add_filter( 'login_headertitle', function() {
    return get_option('blogname');
});

function do_before_single_player($arg) {
}
add_action( 'sportspress_before_single_player', 'do_before_single_player' );

function do_after_single_player($arg) {
}
add_action( 'sportspress_after_single_player', 'do_after_single_player' );

function staff_content() {
}
add_action( 'sportspress_single_staff_content', 'staff_content' );

function add_register_script() {
    
    wp_enqueue_script( 'register-helper', get_stylesheet_directory_uri() . '/js/register-helper.js', array('jquery'), '1.0', true );
    
}
add_filter('register_form', function() {
    $sp_staff = ( !empty( $_POST['sp_staff'] ) ? $_POST['sp_staff'] : '' );
    $user = ( !empty( $_POST['user'] ) ? $_POST['user'] : '' );
    $privacy_policy = ( !empty( $_POST['privacy_policy'] ) ? $_POST['privacy_policy'] : '' );
    ?>
    <p>
        <label for="user"><strong><?php echo 'ich bin Mitglied des SSV'; ?></strong><br />
            <input type="checkbox" name="user" id="user" class="checkbox opt-user" value="1" <?php echo $user ? "checked" : '' ; ?>/></label>
    </p><br>
    <p>
        <label for="sp_staff"><?php echo 'ich bin ' . __( 'Staff', 'sportspress' ); ?><br />
            <input type="checkbox" name="sp_staff" id="sp_staff" class="checkbox" value="1" <?php echo $sp_staff ? "checked" : '' ; ?>/></label>
    </p><br>
    <p>
        <label for="privacy_policy"><?php echo 'ich habe die <a href="' . home_url('datenschutzbestimmungen') . '" target="_blank">Datenschutzbestimmungen</a> gelesen und akzeptiere sie'; ?><br />
            <input type="checkbox" name="privacy_policy" id="policy" class="checkbox" value="1" <?php echo $privacy_policy ? "checked" : '' ; ?> required=""/></label>
    </p><br>
    <?php
    add_action('login_footer', 'add_register_script' );
}, 11 );
add_action('login_header', function() {
    ?>
    <style type="text/css">.hide{display: none;}#login {width: 400px;padding: 4% 0 0;}</style>
    <?php
});
add_filter('user_register', function( $user_id ) {
    
    if ( ! empty( $_POST['sp_team'] ) ) {
        $team = trim( $_POST['sp_team'] );
        if ( $team <= 0 ) $team = 0;
        update_user_meta( $user_id, 'sp_team', $team );
    }
    
    // Add player or staff
    $parts = array();
    if ( ! empty( $_POST['first_name'] ) && ! empty( $_POST['last_name'] ) ) {
        $meta = trim( $_POST['first_name'] );
        $parts[] = $meta;
        $meta = trim( $_POST['last_name'] );
        $parts[] = $meta;
    }
    
    if ( sizeof( $parts ) ) {
        $name = implode( ' ', $parts );
    } else {
        $name = $_POST['user_login'];
    }
    
    $post_type = ( isset( $_POST['sp_staff'] ) ? 'sp_staff' : !isset( $_POST['user'] ) ? 'sp_player' : '' );
    // make shure to set the correct user role instead default role set in wp-admin
    if( $post_type ) {
        $post['post_type'] = $post_type;
        $post['post_title'] = trim( $name );
        $post['post_author'] = $user_id;
        $post['post_status'] = 'draft';
        $id = wp_insert_post( $post );

        if ( isset( $team ) && $team ) {
            update_post_meta( $id, 'sp_team', $team );
            update_post_meta( $id, 'sp_current_team', $team );
        }
        wp_update_user( array( 'ID' => $user_id, 'role' => $post_type ) );
    }
    
});

/*
 * Add registration errors
 */
function errors_on_register( $errors, $sanitized_user_login, $user_email ) {
 
    if ( ! isset( $_POST['privacy_policy'] ) ) :
        $errors->add( 'policy_error', '<strong>' . __('FEHLER', 'wordpress') . "</strong>: Bitte lese und akzeptiere unsere Datenschutzbestimmungen." );
    endif;
    
    if ( empty( $_POST['first_name'] ) ) :
        $errors->add( 'first_name_error', '<strong>' . __('FEHLER', 'wordpress') . "</strong>: Bitte gebe Deinen Vornamen an." );
    endif;
    
    if ( empty( $_POST['last_name'] ) ) :
        $errors->add( 'last_name_error', '<strong>' . __('FEHLER', 'wordpress') . "</strong>: Bitte gebe Deinen Nachnamen an." );
    endif;
    
    return $errors;
}
add_filter( 'registration_errors', 'errors_on_register', 10, 3 );
function shake_errors( $shake_errors ) {
    
    $errors = array( 'policy_error', 'first_name_error', 'last_name_error', 'policy_error' );
    return array_merge( $shake_errors, $errors );
    
    
}
add_filter( 'shake_error_codes', 'shake_errors', 11 );
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
    
    if( !isset($post )) {
        return;
    }
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

function handle_profile_changes( $content, $user_id, $post = NULL ) {
    
    $args = array();
    $posts = get_posts_of_type_by_user( array( 'sp_player', 'sp_staff' ), $user_id );
    
    
    if( !empty( $content ) && isset( $content['description'] ) && !empty( $posts )) {
        
        $post = array_shift($posts);
        $player_id = $post->ID;
        
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
            case 'sp_staff':
            case 'administrator':
                
                /*
                 * Enable the users player profile and notify if user is confirmed
                 * 
                 * 
                 */
                um_fetch_user( $user_id );
                if( um_user('account_status') != 'awaiting_email_confirmation' ) {
                    

                    if( !is_admin() ) {
                        // approve and notify user when changes are made from UM profile page
                        notify_approved( $user_id );
                        $args['post_status'] = 'publish';
                    }
                    
                } else {
                        $args['post_status'] = 'draft'; // don't touch status of a player from nonconfirmed users
                }
                
                break;
            default:
                
        }
        $args['post_excerpt'] = $new_description;
        
    }
    return $args;
    
}

/*
 * Check for users update (UM) and copy its biography to players excerpt
 */
function before_update_um_profile( $content, $user_id ) {
    
    
    $id = get_post_id_from_user( array('sp_player', 'sp_staff'), $user_id );
    $args = handle_profile_changes( $content, $user_id );
    
    
    if( $id ) update_player( $id, $args );
    
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
        
        $id = get_post_id_from_user( array( 'sp_player', 'sp_staff' ), $uiser_id );
        
        update_player( $id, $args );
    }
    
};
add_action( 'um_after_user_status_is_changed', 'after_user_status_changed', 10, 2 );

/*
 * before WP profile update
 */
function before_update_wp_profile( $user_id ) {
    
    $new_description = $_POST['description'];
    $changes = array( 'description' => wp_strip_all_tags($new_description) );
    
    $id = get_post_id_from_user( array( 'sp_player', 'sp_staff' ), $user_id );
    
    // copy also the teams from wp-profile to the player
    if( $_POST[ 'sp_team' ] > 0 ) {
        sp_update_post_meta_recursive( $id, 'sp_team', array( sp_array_value( $_POST, 'sp_team', array() ) ) );
        sp_update_post_meta_recursive( $id, 'sp_current_team', array( sp_array_value( $_POST, 'sp_team', array() ) ) );
    }
    
    apply_filters( 'um_before_update_profile', $changes, $user_id );
    
}
add_action( 'personal_options_update', 'before_update_wp_profile', 10, 2 );
add_action( 'edit_user_profile_update', 'before_update_wp_profile', 10, 3 );


/*
 * Create/delete post(s) of sp post_type (sp_staff, sp_player) when transitioning within or outside these roles
 * 
 */
function after_update_wp_profile( $user_id, $old_profile ) {
    
    $user = new WP_User( $user_id );
    $roles = $user->roles;
    $role_found = false;
    $sp_roles = array( 'sp_staff', 'sp_player' );
    
    // extract the current post_type from sp_roles array
    foreach ($roles as $role) {
        if ( ( $key = array_search( $role, $sp_roles ) ) !== false) {
            unset($sp_roles[$key]);
            $role_found = true;
            break;
        }
    }
    // make sure we haven't saved w/o changing role
    if( $role_found && !in_array( $role, $old_profile->roles ) ) {
        
        // check for exisiting sp role and create post if necessary
        if( empty( get_posts_of_type_by_user( $role, $user_id ) ) ) {

            $post['post_type'] = $role;
            $post['post_title'] = $user->display_name;
            $post['post_author'] = $user_id;
            $post['post_excerpt'] = $user->description;
            $post['post_status'] = 'draft';
            wp_insert_post( $post );
            
        }
        // delete all other post of type roles if present
        if( !empty( $posts = get_posts_of_type_by_user( $sp_roles, $user_id ) ) ) {
            
            delete_posts( $posts );
            
        }
    // post not within sp_roles, so delete posts of type sp_roles if present
    } elseif( !empty( $posts = get_posts_of_type_by_user( $sp_roles, $user_id ) ) ) {
        
        delete_posts( $posts );

    }
    // make sure we copy team metas to the post, regardless of it is a new created post or saved existing profil (no new post)
    if( ( $id = get_post_id_from_user( $role, $user_id ) ) && $_POST[ 'sp_team' ] > 0 ) {
        sp_update_post_meta_recursive( $id, 'sp_team', array( sp_array_value( $_POST, 'sp_team', array() ) ) );
        sp_update_post_meta_recursive( $id, 'sp_current_team', array( sp_array_value( $_POST, 'sp_team', array() ) ) );
    }
}
function delete_posts( $posts = array() ) {
    foreach ( $posts as $post ) {
        wp_delete_post( $post->ID );
    }
}
add_action( 'profile_update', 'after_update_wp_profile', 10, 2 );

/*
 * After user state changes to "approved" reactivate its player profile
 * 
 */
function after_user_is_approved( $user_id ) {
    
    $args = array(
        'post_status' => 'publish'
    );              
    
    $player_id = get_post_id_from_user( 'sp_player', $user_id );
    update_player($player_id, $args);
    
}
//add_action( 'um_after_user_is_approved', 'after_user_is_approved', 10, 1 ); // updates are handled by before_update_um_profile


/* 
 * we must intercept server requests at a very early stage to prevent destroying the hashed key of a user that is about to register
 * since that we must now welcome the user manually
 */
function listen_to_server_requests() {
    
    if ( isset( $_REQUEST['hash'] ) && ( isset( $_REQUEST['act'] ) && $_REQUEST['act'] == 'activate_via_email' ) && ( $_REQUEST['user_id'] && !empty( $_REQUEST['user_id'] ) ) ) {

        $request = $_REQUEST;
        $user_id = absint( $_REQUEST['user_id'] );
        um_fetch_user( $user_id );
        
        // rebuild $_REQUEST and welcome user when we find the wp register hash
        if( array_key_exists( 'key', $_REQUEST ) ) {
            
            unset( $_REQUEST );
            
            $allowed_keys = array( 'key', 'action', 'login' );

            foreach ($request as $key => $value) {

                if( in_array( $key, $allowed_keys) ) {

                    $_REQUEST[$key] = $value; // rebuild it

                }

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
    
    /*
     * wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login)
     * https://ssv-altenberg.webpremiere.dev/wp-login.php?action=rp&key=jQoGBn40xnQHx5u7EXYD&login=AAA
     * https://ssv-altenberg.webpremiere.dev/wp-login.php?action=rp&key=pnrzV4zCK4daxfhMwoKW&login=AAA&act=activate_via_email&hash=2BMIYtoP6OMaikVIY7eBHeBKrmVyf0xheUjYKVaZ&user_id=245
     * https://ssv-altenberg.webpremiere.dev/wp-login.php?action=rp&key=bqekQgrdEJ9jjM0rFYIr&login=AAA&act=activate_via_email&hash=dZe2UQxkZUeCqQVQIepyrkfbxsE294O9kyl4hA9w&user_id=84
     */
    
    if( isset( $_POST['user_login'] ) ) {
        $login = trim($_POST['user_login']);
        $user_data = get_user_by('login', $login);
    } else {
        $user_id = um_user('ID');
        $user_data = get_user_by('ID', $user_id);
        
    }
    $user_login = $user_data->user_login;
    $user_email = $user_data->user_email;
    $key = get_password_reset_key( $user_data );
    
    $url .= '/wp-login.php';
    $url =  add_query_arg( 'action', 'rp', $url );
    $url =  add_query_arg( 'key', $key, $url );
    $url =  add_query_arg( 'login', rawurlencode($user_login), $url );
    
    return $url;
    
}
/*
 * Send UM Activation E-Mail and get activation url + key
 */
function after_user_registered( $user_id ) {
    
    add_filter('um_activate_url', 'create_activate_url');
    do_action('um_post_registration_checkmail_hook', $user_id, array() );
    
}
add_action( 'register_new_user', 'after_user_registered' );

// remove original resend activation email field and replace with custom field/action
function user_actions_hook( $actions ) {
    
    if ( um_user('account_status') == 'awaiting_email_confirmation' ) {
        
        $actions['um_resend_activation'] = NULL;
        $actions['resend_activation'] = array( 'label' => __('Resend Activation E-mail','ultimate-member') );
        
    }
    
    return $actions;
    
}
add_filter('um_admin_user_actions_hook', 'user_actions_hook');

function resend_activation( $action ) {
    
    add_filter('um_activate_url', 'create_activate_url');
    $user_id = $_REQUEST['uid'];
    
    do_action('um_post_registration_checkmail_hook', $user_id, array() );
    
    // redirect to login page (may be for later use)
    $redirect = home_url( '/wp-login.php?checkemail=registered' );
    
    // redirect to current page
    exit( wp_redirect( UM()->permalinks()->get_current_url( true ) ) );
    
}
add_action( 'um_action_user_request_hook', 'resend_activation' );

/*
 * after email confirmation for new users is sent exit execution
 * 
 * 
 */
function after_email_confirmation( $user_id ) {
    
    // account needs validation email to user and admin
    notify_pending($user_id);
    
    // define redirect after the validation email has been sent
    $user_data = get_user_by('ID', $user_id);
    $key = get_password_reset_key( $user_data );
    $login = $user_data->user_login;
    $redirect = home_url( '/wp-login.php?action=rp&key=' . $key . '&login=' . $login );
    
    exit( wp_redirect( $redirect ) );
    
}
add_action( 'um_after_email_confirmation', 'after_email_confirmation', 10 );

// SSV profile tab
function custom_profile_tabs( $tabs ) {
    
    $ssv_tab = array(
        'ssv' => array(
            'name' => __( 'SSV Profil', 'ultimate-member' ),
            'icon' => 'um-faicon-user'
        ),
    );
    
    $tabs = array_merge( $tabs, $ssv_tab );
    return $tabs;
}
add_filter( 'um_profile_tabs', 'custom_profile_tabs', 10, 1 );

// fill in ssv content
function profile_content_ssv( $args ) {
    
    $user_id = um_user('ID');
    $posts = get_posts_of_type_by_user( array('sp_staff', 'sp_player'), $user_id );
    if( empty( $posts ) ) {
        echo sprintf( '<div class="um-item-link"><i class="um-icon-ios-people"></i>Der Benutzer ist kein Mitglied des SSV</div>', '' );
    } else {
        $post = array_shift( $posts );
        
        $logged_in_user = wp_get_current_user();
        $author_is_loggedin_user = $logged_in_user->ID === (int) $post->post_author;
        
        if( $author_is_loggedin_user ) {
            $permalink = '<a href="' . get_post_permalink( $post->ID ) . '">Mein SSV Profil (' . $post->post_title . ')</a>';
            $output = sprintf( '<div class="um-item-link"><i class="um-icon-ios-people"></i>%s</div>', $permalink);
        } else {
            $permalink = '<a href="' . get_post_permalink( $post->ID ) . '">' . $post->post_title . '</a>';
            $output = sprintf( '<div class="um-item-link"><i class="um-icon-ios-people"></i>SSV Profil von %s</div>', $permalink);
        }
        
        echo $output;
        
    }
    
}
add_action( 'um_profile_content_ssv', 'profile_content_ssv' );


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
        case 'sp_staff':
        case 'sp_player':

            $user_id = (int) $post['post_author'];
            $excerpt = $post['post_excerpt'];
            $changes = array( 'description' =>  wp_strip_all_tags( $excerpt ) );
            
            um_fetch_user( $user_id );
            
            remove_action('wp_insert_post_data', 'before_save_post', 10 );
            UM()->user()->update_profile( $changes );
            add_action( 'wp_insert_post_data', 'before_save_post', 10, 1 );

            $changes = handle_profile_changes( $changes, $user_id );

            unset( $changes['description']);
            $changes['post_excerpt'] =  $excerpt;

            $post = array_merge( $post, $changes );

            break;
        default:
            break;
    }
    
    return $post;
}
add_action( 'wp_insert_post_data', 'before_save_post', 10, 1 );

function on_post_status_change( $new_status, $old_status, $post ) {
    
    if( $new_status === $old_status )
        return;
    
    
    $type = $post->post_type;
    $user_id = $post->post_author;
    
    um_fetch_user( $user_id );
    $status = get_user_meta( $user_id, 'account_status', true );
    
    if( $status == 'awaiting_email_confirmation' || $status == '' )
        return;
    
    switch ($type) {
        case 'sp_staff':
        case 'sp_player':
            
            
            if( $new_status != 'publish' ) {
                notify_pending($user_id);
            } else {
                notify_approved($user_id);
            }
            break;
        default:
            break;
    }
}
add_action( 'transition_post_status',  'on_post_status_change', 10, 3 );
        
function set_teams_on_save_post( $post_id ) {
    
    $post = get_post( $post_id );
    $meta = get_metadata( 'post', $post_id );
    $user_id = $post->post_author;
    if( isset( $meta['sp_team'] ) ) {
        $teams = $meta['sp_team'];
        foreach ($teams as $team) {
            add_user_meta( $user_id, 'sp_team', $team );
        }
    }
    
}
add_action( 'save_post', 'set_teams_on_save_post' );
/*
 * action for sportspress header in single sportspress pages
 * 
 */
function sportspress_header( $id ) {
    
    $post = get_post( $id );
    $title = $post->post_title;
    if( is_singular( $post_type = $post->post_type ) ) {
        switch ( $post_type ) {
            
            case 'sp_staff':
                $part = __( 'Staff', 'sportspress' );
                
                break;
            case 'sp_player':
                $part = __( 'Player', 'sportspress' );
                
                break;
            case 'sp_directory':
                $part = __( 'Verzeichnis', 'sportspress' );
                
                break;
            default:
                $part = __( 'Not found', 'sportspress' );
            
        }
        
        echo $part . ' <h4>' . $title . '</h4>';
    }
}
add_action( 'sportspress_header', 'sportspress_header', 10 );

/*
 * add content after sp_team content
 * 
 */
function sportspress_after_single_team_content( $content ) {
    global $post;
    
    if( ! $post || ! isset( $post ))
        return;
    
    
    if ( $post->post_type == 'sp_team' )
       return apply_filters ( 'add_team_posts_permalink', $content );
    return $content;
}
function add_team_posts_permalink( $content ) {
    global $post;
    
    $category_base = ( $cb = get_option( 'category_base' ) ) ? $cb : 'category';
    $title = $post->post_title;
    $slug = $post->post_name;
    $category = get_category_by_slug( $slug );
    $cat_ID = $category->cat_ID;
    
    $permalink = home_url( $category_base . '/' .  $slug );
    $readmore  = '<div class="read-all-team-posts"><a class="button big color1 y-move" href="' . $permalink . '">alle Sektionsbeiträge lesen</a></div>';
    $widget_before = '<hr class="sp-header-rule"/>'
        . '<div class="sp-header-wrapper">'
        . '<div class="sp-header-icon">'
        . '<i class="fa icon bordered round fa-paper-plane color1"></i>'
        . '</div>'
        . '<div class="sp-header-text">'
        . '<h5>Die letzten Beiträge</h5>'
        . '</div>' . $readmore
        . '</div>';
    $args = array(
        'number' => 2,
        'columns' => 2,
        'offset' => 0,
        'before_widget' =>  $widget_before,
        'after_widget' => '<hr/>',
        'show_date' => 1,
        'show_excerpt' => 0,
        'category' => $cat_ID,
    );
    ob_start();
    $news_widget = new News_Widget();
    $news_widget->widget($args);
    $widget = ob_get_clean();
    
    $content = '<h3>Herzlich Willkommen beim Team '. $title . '!</h3>' . $content . $widget;
    return $content;
    
}
add_filter( 'the_content', 'sportspress_after_single_team_content', 9 );
add_filter( 'add_team_posts_permalink', 'add_team_posts_permalink', 10 );

function update_player( $id, $args = array() ) {
    
    $post = get_post( $id );
    
    $post_meta = get_post_meta($post->ID);
    
    foreach ($args as $key => $value) {
        $post->$key = $value;
    }
    
    remove_action('wp_insert_post_data', 'before_save_post', 10 ); // prevent infinite loop
    
    // update post
    $post_id = wp_update_post( $post, true );
    
//     update postmeta e.g. sp_team -not needed
//    sp_update_post_meta_recursive( $player_id, 'sp_team', array( sp_array_value( $post_meta, 'sp_team', array() ) ) );
//    sp_update_post_meta_recursive( $player_id, 'sp_current_team', array( sp_array_value( $post_meta, 'sp_team', array() ) ) );
    
    add_action( 'wp_insert_post_data', 'before_save_post', 10, 1 ); // re-adding after removal
    
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
        if( UM()->user()->is_approved( $user_id )) {
            foreach ( $emails as $email ) {
                UM()->mail()->send( $email, 'notification_review', array( 'admin' => true ) );
            }
            UM()->user()->pending();
        }
	}
    
}
function notify_approved( $user_id ) {
    
    
    if( !UM()->user()->is_approved( $user_id )) {
        um_fetch_user( $user_id );
        UM()->user()->approve();
    }
    
}
function fetch_current_user() {
    
    um_fetch_user( get_current_user_id() );
    
}
function get_post_id_from_user( $post_type = '', $user_id = '' ) {
    
    $posts = get_posts_of_type_by_user( $post_type, $user_id );
    
    if( !empty( $posts ) ) {
        
        $post = array_shift($posts);
        
        return $post->ID;
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
function get_posts_of_type_by_user( $post_type, $user_id = '' ) {
    
    $args = array(
        'author' => (int) $user_id,
        'post_type' => $post_type,
        'post_status' => array( 'any' ),
    );              
    
    $the_query = new WP_Query( $args );
    $posts = $the_query->posts;
    
    return $posts;
    
}
function is_player( $post_id ) {
    
    $post = get_post( $post_id );
    
    if( $post->post_type == 'sp_player' ) return TRUE;
    return FALSE;
    
}
function is_staff( $post_id ) {
    
    $post = get_post( $post_id );
    
    if( $post->post_type == 'sp_staff' ) return TRUE;
    return FALSE;
    
}
 /*
  * Add team to UM Profile grid or Cover
  * 
  * 
  * 
  */
function print_user_data( $user_id = null ) {
    
    // overloaded
    if( is_array($user_id) )
        $user_id = um_user('ID');
    
    $user = get_userdata($user_id);
    $roles = $user->roles;
    
    foreach ($roles as $role) {
        
        switch( $role ) {

            case 'sp_staff':

                $user = get_userdata( $user_id );
                $staff = new SP_Staff( $user_id );
                $label = array( __( 'Staff', 'sportspress' ) );

                $staff_id = get_post_id_from_user('sp_staff', $user_id );
                $sp_roles = get_the_terms( $staff_id, 'sp_role' );
                $sp_teams = get_post_meta( $staff_id, 'sp_team' );
                if( $sp_roles ) {
                    foreach ( $sp_roles as $sp_role ):
                        $text[] = $sp_role->name;
                    endforeach;
                    if( $sp_teams ) {
                        $label = $text;
                        $text = array();
                        foreach ( $sp_teams as $sp_team ):
                            $team_name = sp_team_short_name( $sp_team );
                            $text[] = '<a href="' . get_post_permalink( $sp_team ) . '">' . $team_name . '</a>';
                        endforeach;
                    }
                } else {
                    $text[] = __( '<span style="opacity: 0.5;">(ohne Funktion)</span>', 'sportspress' );
                }
                $label = implode(', ', $label);
                $text = implode( ', ', $text );
                
                break;
            case 'sp_player':

                $player_id = get_post_id_from_user( 'sp_player', $user_id );
                $player = new SP_Player( $player_id );
                $current_teams = $player->current_teams();
                $sp_teams = get_post_meta( $player_id, 'sp_team' );
                if ( $current_teams ):
                    $teams = array();
                    foreach ( $current_teams as $team ):
                        $team_name = sp_team_short_name( $team );
                        $teams[] = '<a href="' . get_post_permalink( $team ) . '">' . $team_name . '</a>';
                    endforeach;
                else:
                    // is player without team
                    $teams = array( '<span style="color: #f00;">ohne Team</<span>' );
                endif;
                
                $label = __( 'Current Team', 'sportspress' );
                $text = implode( ', ', $teams );
                
                break;
            default:
                $text = __( '<span style="opacity: 0.5;">(ohne Funktion)</span>', 'sportspress' );
                $label = 'Extern';
                
                break;

        }
        
    }
    
    echo sprintf('<div class="member-of-team"><span>%s</span><span>&nbsp;</span><span>%s</span></div>', $label, $text );
    
}
add_action('um_members_just_after_name', 'print_user_data', 10 );
add_action( 'um_before_profile_main_meta', 'print_user_data', 10 );

/*
 * don't send account deletion email to unconfirmed users
 * let only admins receive account deletion email
 */
function on_delete_user( $user_id ) {
    
    um_fetch_user( $user_id );
    
    if( um_user('account_status') == 'awaiting_email_confirmation' ) {
        
        UM()->user()->send_mail_on_delete = FALSE;
        
        $emails = um_multi_admin_email();
        if ( ! empty( $emails ) ) {
            foreach ( $emails as $email ) {
                UM()->mail()->send( $email, 'notification_deletion', array( 'admin' => true ) );
            }
        }
        
    }
    // action added twice by UM, so remove this action now
    remove_action('um_delete_user', 'on_delete_user');
    
}
add_action( 'um_delete_user', 'on_delete_user' );
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
    if( !defined('HEADER_PLAYER_EXCERPT') )
        define( 'HEADER_PLAYER_EXCERPT', '<h4 class="player-excerpt-header">Biografische Angaben:</h4>' );
    if( !defined('HEADER_STAFF_EXCERPT') )
        define( 'HEADER_STAFF_EXCERPT', '<h4 class="staff-excerpt-header">Sonstige Informationen:</h4>' );
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