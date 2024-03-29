<?php

wbp_define_constants();
wbp_include_plugins();

//require_once( __DIR__ . '/includes/classes/class_shortcode_staff_advanced.php');
require_once(__DIR__ . '/includes/classes/class_t5_richtext_excerpt.php');
require_once(__DIR__ . '/includes/duplicate_content.php');
require_once(__DIR__ . '/includes/sender_email.php');
//require_once( __DIR__ . '/framework.php' );

//add_action('init', 'add_shortcodes_staff_advanced');
function add_shortcodes_staff_advanced()
{
  add_shortcode('staff_advanced', 'staff');
}

function staff($atts)
{
  return SP_Shortcodes::shortcode_wrapper('Shortcode_Staff_Advanced::output', $atts);
}
add_filter('site_url', function ($url) {

  $url_option = get_option('siteurl');
  if (!isset($_SERVER["HTTPS"]) || 'on' != $_SERVER["HTTPS"]) {
    $url = str_replace('http:', 'https:', $url);
  }
  return $url;
});

// Declare SportsPress support.
add_theme_support('sportspress');

// Declare Mega Slider support.
add_theme_support('mega-slider');

// Declare Social Sidebar support.
add_theme_support('social-sidebar');

// Declare News Widget support.
add_theme_support('news-widget');

function wbp_add_styles()
{

  $version = mesmerize_get_version();

  wp_enqueue_style('mesmerize-pro-style', get_template_directory_uri() . '/style.css');

  if (apply_filters('mesmerize_load_bundled_version', true)) {
    $deps = array('mesmerize-theme');
  } else {
    $deps = array('jquery-fancybox');
    wp_enqueue_style('fancybox', mesmerize_pro_uri('/assets/css/jquery.fancybox.min.css'), array(), $version);
    wp_enqueue_script('jquery-fancybox', mesmerize_pro_uri() . '/assets/js/jquery.fancybox.min.js', array('jquery'), $version, true);
  }
  wp_enqueue_script('fancybox-helper', get_stylesheet_directory_uri() . '/js/fancybox-helper.js', $deps, '1.0', true);
  wp_enqueue_style('mobile', get_stylesheet_directory_uri() . '/css/main.css', array(), '1.0');
  wp_enqueue_script('mobile', get_stylesheet_directory_uri() . '/js/main.js', array('jquery'), '1.0', true);

  if (!IS_DEV_MODE) {
    wp_enqueue_script('google-analytics', get_stylesheet_directory_uri() . '/js/analyticstracking.js', false, '1.0', true);
    // make the current user available to analytics
    $current_user = wp_get_current_user();
    $user_id = (0 !== $current_user->ID ? $current_user->ID : '');
    // hand over the userID to the analytics script
    wp_localize_script('google-analytics', 'atts', array('user_id' => $user_id, 'ga_id' => GA_ID));
  }
}
add_action('wp_enqueue_scripts', 'wbp_add_styles');

function wbp_add_mobile_scripts()
{

  if (wp_is_mobile()) {
    //
  }
}
add_action('wp_enqueue_scripts', 'wbp_add_mobile_scripts');

function wbp_allow_svg_upload($m)
{
  $m['svg'] = 'image/svg+xml';
  $m['svgz'] = 'image/svg+xml';
  return $m;
}
add_filter('upload_mimes', 'wbp_allow_svg_upload');

add_filter('login_headerurl', function () {
  return site_url();
});
add_filter('login_headertitle', function () {
  return get_option('blogname');
});

function wbp_do_before_single_player($arg)
{
}
add_action('sportspress_before_single_player', 'wbp_do_before_single_player');

function wbp_do_after_single_player($arg)
{
}
add_action('sportspress_after_single_player', 'wbp_do_after_single_player');

function wbp_staff_content()
{
}
add_action('sportspress_single_staff_content', 'wbp_staff_content');

function add_register_script()
{

  wp_enqueue_script('register-helper', get_stylesheet_directory_uri() . '/js/register-helper.js', array('jquery'), '1.0', true);
}
add_action('register_form', function () {

  $first_name = (!empty($_POST['first_name'])) ? trim($_POST['first_name']) : '';
  $last_name = (!empty($_POST['last_name'])) ? trim($_POST['last_name']) : '';
  $sp_staff = (!empty($_POST['sp_staff']) ? $_POST['sp_staff'] : '');
  $sp_team = (!empty($_POST['sp_team']) ? $_POST['sp_team'] : '');
  $ssv_user = (!empty($_POST['ssv_user']) ? $_POST['ssv_user'] : '');
  $privacy_policy = (!empty($_POST['privacy_policy']) ? $_POST['privacy_policy'] : '');
?>
  <p>
    <label for="first_name"><?php _e('First Name', 'ultimate-member') ?><br />
      <input type="text" name="first_name" id="first_name" class="input" value="<?php echo esc_attr(wp_unslash($first_name)); ?>" size="25" /></label>
  </p>

  <p>
    <label for="last_name"><?php _e('Last Name', 'ultimate-member') ?><br />
      <input type="text" name="last_name" id="last_name" class="input" value="<?php echo esc_attr(wp_unslash($last_name)); ?>" size="25" /></label>
  </p><br>
  <p>
    <label for="ssv_user"><strong><?php echo 'Ich bin Mitglied des SSV'; ?></strong><br />
      <input type="checkbox" name="ssv_user" id="ssv_user" class="checkbox opt-ssv_user" value="1" <?php echo $ssv_user ? "checked" : ''; ?> /></label>
  </p><br>
  <p>
    <label for="sp_team"><?php _e('Team', 'sportspress') ?><br />
      <?php
      $args = array(
        'post_type' => 'sp_team',
        'name' => 'sp_team',
        'values' => 'ID',
        'selected' => $sp_team,
        'show_option_none' => sprintf(__('Select %s', 'sportspress'), __('Team', 'sportspress')),
        'property' => 'style="width:100%;height:36px;margin-bottom:16px"',
      );
      sp_dropdown_pages($args);
      ?>
  </p>
  <p>
    <label for="sp_staff"><?php echo 'Ich bin ' . __('Staff', 'sportspress'); ?><br />
      <input type="checkbox" name="sp_staff" id="sp_staff" class="checkbox" value="1" <?php echo $sp_staff ? "checked" : ''; ?> /></label>
  </p><br>
  <p>
    <label for="privacy_policy"><?php echo 'Ich habe die <a href="' . home_url('datenschutzbestimmungen') . '" target="_blank">Datenschutzbestimmungen</a> gelesen und akzeptiere sie'; ?><br />
      <input type="checkbox" name="privacy_policy" id="privacy_policy" class="checkbox" value="1" <?php echo $privacy_policy ? "checked" : ''; ?> required="" /></label>
  </p><br>
<?php
  add_action('login_footer', 'add_register_script');
}, 10);

/*
 *  Add styles to login/register form
 */
add_action('login_header', function () {
?>
  <style type="text/css">
    .hide {
      display: none;
    }

    #login {
      width: 400px;
      padding: 4% 0 0;
    }

    .login .password-input-wrapper {
      width: 100%;
    }

    label input[type="checkbox"] {
      margin-bottom: 0px;
      margin-top: 5px;
    }
  </style>
<?php
});
add_filter('user_register', function ($user_id) {

  $post_type = (isset($_POST['ssv_user']) ? (isset($_POST['sp_staff']) ? 'sp_staff' : 'sp_player') : '');

  if ($post_type) {
    if (!empty($_POST['sp_team'])) {
      $team = trim($_POST['sp_team']);
      if ($team <= 0) $team = 0;
      update_user_meta($user_id, 'sp_team', $team);
    }

    $parts = array();
    if (!empty($_POST['first_name']) && !empty($_POST['last_name'])) {
      $meta = trim($_POST['first_name']);
      update_user_meta($user_id, 'first_name', $meta);
      $parts[] = $meta;
      $meta = trim($_POST['last_name']);
      update_user_meta($user_id, 'last_name', $meta);
      $parts[] = $meta;
    }

    if (sizeof($parts)) {
      $name = implode(' ', $parts);
    } else {
      $name = $_POST['user_login'];
    }

    /*
     *  Add player or staff
     * 
    */
    $post['post_type'] = $post_type;
    $post['post_title'] = trim($name);
    $post['post_author'] = $user_id;
    $post['post_status'] = 'draft';
    $id = wp_insert_post($post);

    if (isset($team) && $team) {
      update_post_meta($id, 'sp_team', $team);
      update_post_meta($id, 'sp_current_team', $team);
    }

    // make shure to set the correct user role instead default role set in wp-admin
    wp_update_user(array('ID' => $user_id, 'role' => $post_type));
  }
}, 11);

/*
 * Add registration errors
 */
function wbp_errors_on_register($errors, $sanitized_user_login, $user_email)
{

  if (empty($_POST['first_name'])) :
    $errors->add('first_name_error', '<strong>' . __('FEHLER', 'wordpress') . "</strong>: Bitte gib deinen Vornamen ein.");
  endif;

  if (empty($_POST['last_name'])) :
    $errors->add('last_name_error', '<strong>' . __('FEHLER', 'wordpress') . "</strong>: Bitte gib deinen Nachnamen ein.");
  endif;

  if (!isset($_POST['privacy_policy'])) :
    $errors->add('policy_error', '<strong>' . __('FEHLER', 'wordpress') . "</strong>: Bitte lese und akzeptiere unsere Datenschutzbestimmungen.");
  endif;

  return $errors;
}
add_filter('registration_errors', 'wbp_errors_on_register', 10, 3);

function wbp_shake_errors($err)
{

  $errors = array('policy_error', 'first_name_error', 'last_name_error', 'policy_error');
  return array_merge($err, $errors);
}
add_filter('shake_error_codes', 'wbp_shake_errors', 11);
/*
 * FEATURED IMAGE 2
 * 
 * Using additional Featured Image (besides Team Logo) for Team Pages as Hero Image
 */
add_filter('wbp_featured_image_2_supported_post_types', function () {

  return array('sp_team');
});

add_filter('kdmfi_featured_images', function ($featured_images) {

  $args = array(
    'id' => 'featured-image-2',
    'desc' => 'Team Bild das im Header angezeigt wird',
    'label_name' => 'Team Bild',
    'label_set' => 'Teambild festlegen',
    'label_remove' => 'Team Bild entfernen',
    'label_use' => 'Teambild festlegen',
    'post_type' => apply_filters('wbp_featured_image_2_supported_post_types', array()) //array( 'sp_team' )
  );

  $featured_images[] = $args;

  return $featured_images;
});

function wbp_override_with_thumbnail_image()
{

  global $post;

  if (!isset($post)) {
    return;
  }
  $post_type = $post->post_type;
  $post_types = array('page', 'post');
  $supported_post_types = array_merge($post_types, apply_filters('wbp_featured_image_2_supported_post_types', array()));

  if (isset($post) && in_array($post_type, $supported_post_types)) {
    return TRUE;
  }
  return FALSE;
}
add_filter('mesmerize_override_with_thumbnail_image', 'wbp_override_with_thumbnail_image');

function wbp_overriden_thumbnail_image($thumbnail)
{

  global $post;

  $post_type = $post->post_type;
  $id = $post->ID;
  if (!empty($src = kdmfi_get_featured_image_src('featured-image-2', 'full', $id)))
    $thumbnail = $src;

  return $thumbnail;
}
add_filter('mesmerize_overriden_thumbnail_image', 'wbp_overriden_thumbnail_image');

function wbp_remove_mesmerize_header_background_mobile_image()
{

  remove_action('wp_head', 'mesmerize_header_background_mobile_image');
}
add_action('wp_head', 'wbp_remove_mesmerize_header_background_mobile_image', 0);

function wbp_handle_um_profile_changes($content, $player_id, $post = NULL)
{

  $args = array();
  $team_posts = wbp_get_posts_of_type_by_user(array('sp_player', 'sp_staff'), $player_id);


  if (!empty($content) && isset($content['description']) && !empty($team_posts)) {

    $team_post = array_shift($team_posts);
    $status = '';

    $description_array = get_user_meta($player_id, 'description');

    $new_description = trim($content['description']);
    $old_description = trim($description_array[0]);

    /*
     * Check for changes in description field for user role 'sp_player'
     * 
    */
    wbp_um_fetch_current_user();
    $current_role = UM()->user()->get_role();

    switch ($current_role) {
      case 'sp_player':
        if ($new_description !== $old_description) {
          /*
           * Notify about the users profiles change
           * 
          */
          wbp_notify_pending($player_id, $team_post);

          /**
           * Disable the users player profile
           */
          $status = 'draft';
        }



        break;
      case 'sp_staff':
      case 'administrator':

        /*
         * Enable the users player profile and notify if user is confirmed
        */
        um_fetch_user($player_id);
        $account_status = um_user('account_status'); // possible statuses are: "awaiting_email_confirmation", "awaiting_admin_review", "active", "inactive", "approved"

        if (in_array($account_status, array('active'))) {
          if (!is_admin()) {
            // approve and notify user when changes are made from UM profile page
            wbp_notify_approved($player_id);
          }
          $status = 'publish';
        } else {
          // don't touch status of a player from nonconfirmed or inactive users
          $status = 'draft';
        }

        break;
      default:
    }
    $args['post_status'] = $status;
    $args['post_excerpt'] = $new_description;
  }
  return $args;
}

/*
 * Check for users update (UM) and copy its biography to players excerpt
 */
function wbp_before_update_um_profile($content, $player_id)
{
  $id = wbp_get_player_id_from_user(array('sp_player', 'sp_staff'), $player_id);

  if ($id) {
    $args = wbp_handle_um_profile_changes($content, $player_id);
    wbp_update_player($id, $args);
  }

  return $content;
};
add_filter('um_before_update_profile', 'wbp_before_update_um_profile', 10, 2);

/*
 * listen to profile status changes and apply status also to player
 */
function wbp_um_after_user_status_changed($status)
{

  if (isset($_REQUEST['uid']) && !empty($_REQUEST['uid'])) {

    $user_id = $_REQUEST['uid'];
    $args = array(
      'post_status' => $status == 'approved' ? 'publish' : 'draft'
    );

    $id = wbp_get_player_id_from_user(array('sp_player', 'sp_staff'), $user_id);
    wbp_update_player($id, $args);
  }
};
add_action('um_after_user_status_is_changed', 'wbp_um_after_user_status_changed', 10, 2);

/*
 * before WP profile update
 */
function wbp_before_update_wp_profile($user_id)
{

  $new_description = $_POST['description'];
  $changes = array('description' => wp_strip_all_tags($new_description));

  $id = wbp_get_player_id_from_user(array('sp_player', 'sp_staff'), $user_id);

  // copy teams from wp profile to player
  if ($_POST['sp_team'] > 0) {
    sp_update_post_meta_recursive($id, 'sp_team', array(sp_array_value($_POST, 'sp_team', array())));
    sp_update_post_meta_recursive($id, 'sp_current_team', array(sp_array_value($_POST, 'sp_team', array())));
  }

  apply_filters('um_before_update_profile', $changes, $user_id);
}
add_action('personal_options_update', 'wbp_before_update_wp_profile', 10, 2);
add_action('edit_user_profile_update', 'wbp_before_update_wp_profile', 10, 3);


/*
 * Create/delete post(s) of sp post_type (sp_staff, sp_player) when transitioning within or outside these roles
 * 
 */
function wbp_after_update_wp_profile($user_id, $old_profile)
{

  $user = new WP_User($user_id);
  $roles = $user->roles;
  $role_found = false;
  $sp_roles = array('sp_staff', 'sp_player');

  // extract the current post_type from sp_roles array
  foreach ($roles as $role) {
    if (($key = array_search($role, $sp_roles)) !== false) {
      unset($sp_roles[$key]);
      $role_found = true;
      break;
    }
  }
  // make sure we haven't saved w/o changing role
  if ($role_found && !in_array($role, $old_profile->roles)) {

    // check for exisiting sp role and create post if necessary
    if (empty(wbp_get_posts_of_type_by_user($role, $user_id))) {

      $parts = array();
      if (!empty($_POST['first_name']) && !empty($_POST['last_name'])) {
        $meta = trim($_POST['first_name']);
        $parts[] = $meta;
        $meta = trim($_POST['last_name']);
        $parts[] = $meta;
      }

      if (sizeof($parts)) {
        $name = implode(' ', $parts);
      } else {
        $name = $_POST['user_login'];
      }


      $post['post_type'] = $role;
      $post['post_title'] = $name;
      $post['post_author'] = $user_id;
      $post['post_excerpt'] = $user->description;
      $post['post_status'] = 'draft';
      wp_insert_post($post);
    }
    // delete all other post of type roles if present
    if (!empty($posts = wbp_get_posts_of_type_by_user($sp_roles, $user_id))) {
      delete_posts($posts);
    }
    // post not within sp_roles, so delete posts of type sp_roles if present
  } else {
    $posts = wbp_get_posts_of_type_by_user($sp_roles, $user_id);
    if (!empty($posts)) {
      delete_posts($posts);
    }
  }
  // make sure we copy team metas to the post, regardless of it is a new created post or saved existing profil (no new post)
  if (($id = wbp_get_player_id_from_user($role, $user_id)) && (!empty($_POST['sp_team']) && ($_POST['sp_team'] > 0))) {
    sp_update_post_meta_recursive($id, 'sp_team', array(sp_array_value($_POST, 'sp_team', array())));
    sp_update_post_meta_recursive($id, 'sp_current_team', array(sp_array_value($_POST, 'sp_team', array())));
  }
}
function delete_posts($posts = array())
{
  foreach ($posts as $post) {
    wp_delete_post($post->ID);
  }
}
add_action('profile_update', 'wbp_after_update_wp_profile', 10, 2);

/*
 * After user state changes to "approved" reactivate its player profile
 * 
 */
function wbp_after_user_is_approved($user_id)
{

  $args = array(
    'post_status' => 'publish'
  );

  $player_id = wbp_get_player_id_from_user('sp_player', $user_id);
  wbp_update_player($player_id, $args);
}
add_action('um_after_user_is_approved', 'wbp_after_user_is_approved', 10, 1); // updates are handled by wbp_before_update_um_profile


/* 
 * we must intercept server requests at a very early stage to prevent destroying the hashed key of a user that is about to register
 * since that we must now welcome the user manually
 */
function wbp_get_request()
{

  if (isset($_REQUEST['hash']) && (isset($_REQUEST['act']) && $_REQUEST['act'] == 'activate_via_email') && ($_REQUEST['user_id'] && !empty($_REQUEST['user_id']))) {

    $request = $_REQUEST;
    $user_id = absint($_REQUEST['user_id']);
    um_fetch_user($user_id);

    // rebuild $_REQUEST and welcome user when we find the wp register hash
    if (array_key_exists('key', $_REQUEST)) {

      unset($_REQUEST);

      $allowed_keys = array('key', 'action', 'login');

      foreach ($request as $key => $value) {

        if (in_array($key, $allowed_keys)) {
          $_REQUEST[$key] = $value; // rebuild the keys
        }
      }
    }
  }

  return $_REQUEST;
}
add_action('init', 'wbp_get_request', 0);

/*
 * see wp-login.php retrieve_password()
*/
function wbp_create_activate_url($url)
{
  global $wpdb;

  /*
     * wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login)
     * https://ssv-altenberg.webpremiere.dev/wp-login.php?action=rp&key=jQoGBn40xnQHx5u7EXYD&login=AAA
     * https://ssv-altenberg.webpremiere.dev/wp-login.php?action=rp&key=pnrzV4zCK4daxfhMwoKW&login=AAA&act=activate_via_email&hash=2BMIYtoP6OMaikVIY7eBHeBKrmVyf0xheUjYKVaZ&user_id=245
     * https://ssv-altenberg.webpremiere.dev/wp-login.php?action=rp&key=bqekQgrdEJ9jjM0rFYIr&login=AAA&act=activate_via_email&hash=dZe2UQxkZUeCqQVQIepyrkfbxsE294O9kyl4hA9w&user_id=84
     */

  if (isset($_POST['user_login'])) {
    $login = trim($_POST['user_login']);
    $user_data = get_user_by('login', $login);
  } else {
    $user_id = um_user('ID');
    $user_data = get_user_by('ID', $user_id);
  }
  $user_login = $user_data->user_login;
  $user_email = $user_data->user_email;
  $key = get_password_reset_key($user_data);

  $url .= '/wp-login.php';
  $url =  add_query_arg('action', 'rp', $url);
  $url =  add_query_arg('key', $key, $url);
  $url =  add_query_arg('login', rawurlencode($user_login), $url);

  return $url;
}

/*
 * Send UM Activation E-Mail and get activation url + key
 */
function wbp_after_user_registered($user_id)
{

  // Send notification about registration
  um_send_registration_notification($user_id, array());

  add_filter('um_activate_url', 'wbp_create_activate_url');
  do_action('um_post_registration_checkmail_hook', $user_id, array());
}
add_action('register_new_user', 'wbp_after_user_registered');

/*
 * TO DO: update user meta "submitted" in order to get registraion form details which will included in um_email_registration
 * 
 * Kick manually add_filter um_email_registration_data since we don't use UM Registration
 * 
 * Fill in user registration data
 * 
 */
function wbp_after_user_created($meta)
{

  // UM()->user()->set_registration_details( $meta );
  return $meta;
}
add_filter('insert_user_meta', 'wbp_after_user_created');

// remove original resend activation email field and replace with custom field/action
function wbp_user_actions_hook($actions)
{

  if (um_user('account_status') == 'awaiting_email_confirmation') {

    $actions['um_resend_activation'] = NULL;
    $actions['wbp_resend_activation'] = array('label' => __('Resend Activation E-mail', 'ultimate-member'));
  }

  return $actions;
}
// add_filter('um_admin_user_actions_hook', 'wbp_user_actions_hook');

function wbp_resend_activation($action)
{

  add_filter('um_activate_url', 'wbp_create_activate_url');
  $user_id = $_REQUEST['uid'];

  do_action('um_post_registration_checkmail_hook', $user_id, array());

  // redirect to login page (may be for later use)
  $redirect = home_url('/wp-login.php?checkemail=registered');

  // redirect to current page
  exit(wp_redirect(UM()->permalinks()->get_current_url(true)));
}
add_action('um_action_user_request_hook', 'wbp_resend_activation');

/*
 * after email confirmation for new users is sent exit execution
 * 
 * 
 */
function wbp_after_email_confirmation($user_id)
{

  // Send account needs validation email to user and admin
  wbp_notify_pending($user_id);

  // define redirect after the validation email has been sent
  $user_data = get_user_by('ID', $user_id);
  $key = get_password_reset_key($user_data);
  $login = $user_data->user_login;
  $redirect = home_url('/wp-login.php?action=rp&key=' . $key . '&login=' . $login);

  exit(wp_redirect($redirect));
}
add_action('um_after_email_confirmation', 'wbp_after_email_confirmation', 10);

// SSV profile tab
function wbp_custom_profile_tabs($tabs)
{

  $ssv_tab = array(
    'ssv' => array(
      'name' => __('SSV Profil', 'ultimate-member'),
      'icon' => 'um-icon-ios-people'
    ),
  );

  $tabs = array_merge($tabs, $ssv_tab);
  return $tabs;
}
add_filter('um_profile_tabs', 'wbp_custom_profile_tabs', 10, 1);

// fill in ssv content
function wbp_profile_content_ssv($args)
{

  $user_id = um_user('ID');
  $posts = wbp_get_posts_of_type_by_user(array('sp_staff', 'sp_player'), $user_id);
  if (empty($posts)) {
    echo sprintf('<div class="um-item-link"><i class="um-icon-ios-people"></i>Der Benutzer ist kein Mitglied des SSV</div>', '');
  } else {
    $post = array_shift($posts);

    $logged_in_user = wp_get_current_user();
    $author_is_loggedin_user = $logged_in_user->ID === (int) $post->post_author;

    if ($author_is_loggedin_user) {
      $permalink = '<a href="' . get_post_permalink($post->ID) . '">Mein SSV Profil (' . $post->post_title . ')</a>';
      $output = sprintf('<div class="um-item-link"><i class="um-icon-ios-people"></i>%s</div>', $permalink);
    } else {
      $permalink = '<a href="' . get_post_permalink($post->ID) . '">' . $post->post_title . '</a>';
      $output = sprintf('<div class="um-item-link"><i class="um-icon-ios-people"></i>SSV Profil von %s</div>', $permalink);
    }

    echo $output;
  }
}
add_action('um_profile_content_ssv', 'wbp_profile_content_ssv');


/*
 * Set login url after user has successfully changes his passwort
 */
function wbp_login_url_after_password_change($login_url, $redirect = '', $force_reauth = '')
{

  return home_url('login');
}
add_filter('login_url', 'wbp_login_url_after_password_change');

/*
 * Login Redirect
 * 
 */
function wbp_admin_default_page()
{
  return '/members';
}
add_filter('login_redirect', 'wbp_admin_default_page');

/*
 * checks for changes in players excerpt and updates corresponding user profile
 * 
 */
function wbp_before_save_post($post)
{

  $type = $post['post_type'];

  switch ($type) {
    case 'sp_staff':
    case 'sp_player':

      $user_id = (int) $post['post_author'];
      $excerpt = $post['post_excerpt'];
      $content = array('description' =>  wp_strip_all_tags($excerpt));

      // send notifications
      wbp_handle_um_profile_changes($content, $user_id);

      break;
    default:
      break;
  }
  return $post;
}
add_filter('wp_insert_post_data', 'wbp_before_save_post', 10, 1);

function wbp_on_post_status_change($new_status, $old_status, $post)
{

  if ($new_status === $old_status) {
    return;
  }

  $type = $post->post_type;
  $user_id = $post->post_author;

  um_fetch_user($user_id);
  $status = get_user_meta($user_id, 'account_status', true);

  if ($status == 'awaiting_email_confirmation' || $status == '') {
    return;
  }

  switch ($type) {
    case 'sp_staff':
    case 'sp_player':


      if ($new_status != 'publish') {
        wbp_notify_pending($user_id);
      } else {
        wbp_notify_approved($user_id);
      }
      break;
    default:
      break;
  }
}
add_action('transition_post_status',  'wbp_on_post_status_change', 10, 3);

function wbp_set_teams_on_save_post($post_id)
{

  $post = get_post($post_id);
  $meta = get_metadata('post', $post_id);
  $user_id = $post->post_author;
  if (isset($meta['sp_team'])) {
    $teams = $meta['sp_team'];
    foreach ($teams as $team) {
      add_user_meta($user_id, 'sp_team', $team);
    }
  }
}
add_action('save_post', 'wbp_set_teams_on_save_post');

/*
 * action for sportspress header in single sportspress pages
 * 
 */
function wbp_sportpress_page_header($id)
{

  $post = get_post($id);
  $title = $post->post_title;
  $teams = array();
  if (is_singular($post_type = $post->post_type)) {
    switch ($post_type) {

      case 'sp_staff':
        $roles = get_the_terms($post, 'sp_role');
        if ($roles && !is_wp_error($roles)) {
          foreach ($roles as $role) {
            $staff_role[] = $role->name;
          }
        }
        $staff_roles = implode(', ', $staff_role);
        $caption = '<span class="roles">' . $staff_roles . '</span>';
        $part = __('Staff', 'sportspress');
        $title .= ' (' . $caption . ')';

        break;
      case 'sp_player':
        $part = __('Player', 'sportspress');

        break;
      case 'sp_directory':
        $part = __('Verzeichnis', 'sportspress');

        break;
      case 'sp_event':
        $part = __('Event', 'sportspress');

        break;
      default:
        $part = __('Not found', 'sportspress');
    }

    $teams = implode(', ', $teams);
    echo sprintf('<h2 class="sp-header-title">%s</h2><h4>%s</h4>%s', $part, $title, $teams);
  }
}
add_action('sportspress_header', 'wbp_sportpress_page_header', 10);

/*
 * Add content after sp_team content
 */
function wbp_the_content($content)
{
  global $post;

  if ($post->post_type == 'sp_team') {
    ob_start();
    get_template_part('template-parts/elements/team', 'single-header');
    $header = ob_get_clean();

    $content = $header . $content;

    ob_start();
    wbp_news_widget();
    $widget = ob_get_clean();

    $content .= $widget;
  }
  return $content;
}
add_filter('the_content', 'wbp_the_content', 9);

function wbp_news_widget()
{
  global $post;

  $slug = $post->post_name;
  $category = get_category_by_slug($slug);

  if ($category && is_object($category)) {
    $cat_ID = $category->cat_ID;
  } else {
    $cat_ID = 0;
  }

  $permalink = home_url(SSV_CATEGORY_BASE . '/' .  $slug);
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

  $news_widget = new News_Widget();
  $news_widget->widget($args);
}

function wbp_update_player($id, $args = array())
{

  $post = get_post($id);

  foreach ($args as $key => $value) {
    $post->$key = $value;
  }

  wp_update_post($post, true);
}

/*
 * Disable the user and notify admins
 * 
 */
function wbp_notify_pending($user_id)
{

  um_fetch_user($user_id);
  if (UM()->user()->get_role() === 'administrator') {
    return;
  }

  $emails = um_multi_admin_email();
  if (!empty($emails)) {
    if (UM()->user()->is_approved($user_id)) {
      foreach ($emails as $email) {
        UM()->mail()->send($email, 'notification_review', array('admin' => true));
      }
      UM()->user()->pending();
    }
  }
}
function wbp_notify_approved($user_id)
{


  if (!UM()->user()->is_approved($user_id)) {
    um_fetch_user($user_id);
    UM()->user()->approve();
  }
}

function wbp_um_fetch_current_user()
{
  um_fetch_user(get_current_user_id());
}

function wbp_get_player_id_from_user($post_type = '', $user_id = '')
{

  $posts = wbp_get_posts_of_type_by_user($post_type, $user_id);

  if (!empty($posts)) {

    $post = array_shift($posts);

    return $post->ID;
  }

  return FALSE;
}

function wbp_get_user_id_by_author($author_id)
{

  $post = get_post($author_id);
  if (isset($post->post_author)) {
    return $post->post_author;
  }
  return false;
}

function wbp_get_posts_of_type_by_user($post_type, $user_id = '')
{

  $args = array(
    'author' => (int) $user_id,
    'post_type' => $post_type,
    'post_status' => array('any'),
  );

  $the_query = new WP_Query($args);
  $posts = $the_query->posts;

  return $posts;
}

function wbp_is_player($post_id)
{

  $post = get_post($post_id);

  if ($post->post_type == 'sp_player') return TRUE;
  return FALSE;
}
function is_staff($post_id)
{

  $post = get_post($post_id);

  if ($post->post_type == 'sp_staff') return TRUE;
  return FALSE;
}
/*
  * Add team to UM Profile grid or Cover
  * 
  * 
  * 
  */
function print_user_data($user_id = null)
{

  // overloaded
  if (is_array($user_id))
    $user_id = um_user('ID');

  $user = get_userdata($user_id);
  $roles = $user->roles;

  foreach ($roles as $role) {

    switch ($role) {

      case 'sp_staff':

        $user = get_userdata($user_id);
        $staff = new SP_Staff($user_id);
        $label = array(__('Staff', 'sportspress'));

        $staff_id = wbp_get_player_id_from_user('sp_staff', $user_id);
        $sp_roles = get_the_terms($staff_id, 'sp_role');
        $sp_teams = get_post_meta($staff_id, 'sp_team');
        if ($sp_roles) {
          foreach ($sp_roles as $sp_role) :
            $text[] = $sp_role->name;
          endforeach;
          if ($sp_teams) {
            $label = $text;
            $text = array();
            foreach ($sp_teams as $sp_team) :
              $team_name = sp_team_short_name($sp_team);
              $text[] = '<a href="' . get_post_permalink($sp_team) . '">' . $team_name . '</a>';
            endforeach;
          }
        } else {
          $text[] = __('<span style="opacity: 0.5;">(ohne Funktion)</span>', 'sportspress');
        }
        $label = implode(', ', $label);
        $text = implode(', ', $text);

        break;
      case 'sp_player':

        $player_id = wbp_get_player_id_from_user('sp_player', $user_id);
        $player = new SP_Player($player_id);
        $current_teams = $player->current_teams();
        $sp_teams = get_post_meta($player_id, 'sp_team');
        if ($current_teams) :
          $teams = array();
          foreach ($current_teams as $team) :
            $team_name = sp_team_short_name($team);
            $teams[] = '<a href="' . get_post_permalink($team) . '">' . $team_name . '</a>';
          endforeach;
        else :
          // is player without team
          $teams = array('<span style="color: #f00;">ohne Team</<span>');
        endif;

        $label = __('Current Team', 'sportspress');
        $text = implode(', ', $teams);

        break;
      default:
        $text = __('<span style="opacity: 0.5;">(ohne Funktion)</span>', 'sportspress');
        $label = 'Extern';

        break;
    }
  }

  echo sprintf('<div class="member-of-team"><span>%s</span><span>&nbsp;</span><span>%s</span></div>', $label, $text);
}
add_action('um_members_just_after_name', 'print_user_data', 10);
add_action('um_before_profile_main_meta', 'print_user_data', 10);

/*
 * Header title
 */
function add_title($title)
{
  global $post;

  $post_type = '';

  if (is_object($post)) {

    $post_type = $post->post_type;

    switch ($post_type) {
      case 'sp_team':
        $part = __('Team', 'sportspress');
        break;
      case 'sp_player':
        $part = __('Player', 'sportspress');
        break;
      case 'sp_staff':
        $part = __('Staff', 'sportspress');
        break;
      case 'sp_event':
        $part = __('Event', 'sportspress');
        break;
      case 'sp_list':
        $part = __('Sportlerliste', 'sportspress');
        break;
      case 'sp_directory':
        $part = __('Verzeichnis', 'sportspress');
        break;
      case 'sp_calendar':
        $part = __('Calendar', 'sportspress');
        break;
      case 'sp_tournament':
        $part = __('Tournament', 'sportspress');
        break;
      case 'sp_sponsor':
        $part = __('Sponsor', 'sportspress');
        break;
      case 'post':
        if (is_archive()) {
          $part = __('Beiträge');
        } else {
          $part = __('Post');
        }
        if (is_category()) {
          $title = sprintf(__('Category: %s'), single_cat_title('', false));
        } elseif (is_tag()) {
          $title = sprintf(__('Thema: %s'), single_tag_title('', false));
        }
        break;
      case 'page':
        $part = __('Page');
        break;
      case 'attachment':
        $part = __('Attachment', 'wordpress');
        break;
      default:
    }
  } else {
    $part = __('', 'wordpress');
  }

  return sprintf('<div class="sp_type-header %s-header">%s</div><span class="hero-inner-title">%s</span>', $post_type, $part, $title);
}
add_filter('single_post_title', 'add_title');
add_filter('get_the_archive_title', 'add_title');

function header_title($title)
{

  if (is_404()) {
    $title = sprintf(__('Seite nicht gefunden', 'mesmerize'));
  } elseif (is_search()) {
    $title = sprintf(__('Suchergebisse für &#8220;%s&#8221;', 'mesmerize'), get_search_query());
  }
  return $title;
};
add_filter('mesmerize_header_title', 'header_title');

/*
 * Register team-specific Sportspress Sidebar
 * 
 */
function register_team_widget_areas()
{

  $teams = wbp_get_teams();
  foreach ($teams as $team) {

    $name = $team->post_name;
    $title = $team->post_title;
    $id = $team->ID;

    register_sidebar(array(
      'name'          => "Sportspress Pages Sidebar $title",
      'id'            => "sportspress_pages_sidebar_$id",
      'title'         => "Sportspress Pages Sidebar $title",
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget'  => '</div>',
      'before_title'  => '<h4 class="widgettitle">',
      'after_title'   => '</h4>',
    ));
  }

  register_sidebar(array(
    'name'          => "Sportspress Pages Sidebar",
    'id'            => "sportspress_pages_sidebar",
    'title'         => "Sportspress Pages Sidebar",
    'before_widget' => '<div id="%1$s" class="widget %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h4 class="widgettitle">',
    'after_title'   => '</h4>',
  ));
}
add_action('widgets_init', 'register_team_widget_areas');

/*
 * Add Logo to hero on team post
 * 
 */
function wbp_add_hero_team_logo()
{
  global $post;

  $sp_type = $post->post_type;

  $logo = '';
  if ('sp_team' == $sp_type) :
    $id = get_the_ID();
    if (has_post_thumbnail($id)) :
      echo sprintf('<div class="hero-team-logo"><div class="sp-template sp-template-team-logo sp-template-logo sp-team-logo">%s</div></div>', get_the_post_thumbnail($id, 'sportspress-fit-icon'));
    endif;
  endif;
}
//add_action( 'mesmerize_after_inner_page_header_content', 'wbp_add_hero_team_logo' );
/*
 * Override method to also display bloginfo on Sportspress posts
 */
function wbp_mesmerize_post_type_is($type)
{
  global $wp_query;

  $sp_post_types = array('sp_team', 'sp_staff', 'sp_player', 'sp_event', 'sp_calendar', 'sp_table', 'sp_list', 'sp_directory', 'sp_tournament', 'sp_sponsor');

  $post_type = $wp_query->query_vars['post_type'] ? $wp_query->query_vars['post_type'] : 'post';

  if (!is_array($type)) {

    $type = array($type);
  }

  $type = array_merge($sp_post_types, $type);

  return in_array($post_type, $type);
}
/*
 * don't send account deletion email to unconfirmed users
 * let only admins receive account deletion email
 */
function on_delete_user($user_id)
{

  um_fetch_user($user_id);

  if (um_user('account_status') == 'awaiting_email_confirmation') {

    UM()->user()->send_mail_on_delete = FALSE;

    $emails = um_multi_admin_email();
    if (!empty($emails)) {
      foreach ($emails as $email) {
        UM()->mail()->send($email, 'notification_deletion', array('admin' => true));
      }
    }
  }
  // action added twice by UM, so remove this action now
  remove_action('um_delete_user', 'on_delete_user');
}
add_action('um_delete_user', 'on_delete_user');

/*
 * Filter Slideshow Category and stay within
 * 
 */
function check_for_slideshow_categories()
{
  global $post;

  $terms = get_the_category($post->ID);
  return wbp_has_category_name('slideshow', $terms);
}
add_filter('is_slideshow', 'check_for_slideshow_categories');

function wbp_has_category_name($name, $terms = array())
{

  if (!empty($terms)) {

    foreach ($terms as $term) {
      if (wbp_term_has_name($term, $name))
        return true;
    }
  };
  return false;
}
function wbp_term_has_name($term, $name)
{
  if (is_object($term) && isset($term->term_id) && term_exists($term->term_id, 'category') && strpos($term->slug, $name) === 0) {
    return true;
  }
}
function wbp_exclude_other($slideshow_ids)
{
  $terms = get_terms('category', array(
    'exclude' => $slideshow_ids
  ));
  foreach ($terms as $term) {

    $id = absint($term->term_id);
    $ids[] = $id;
  }
  return $ids;
}
add_filter('exclude_other_categories', 'wbp_exclude_other');
/*
 * Add T5 Functionality to Excerpts
 */
add_action('add_meta_boxes', array('T5_Richtext_Excerpt', 'switch_boxes'));

/**
 * Define ThemeBoy Constants.
 */
function wbp_define_constants()
{
  define('THEMEBOY_FILE', __FILE__);
  if (!defined('MEGA_SLIDER_URL'))
    define('MEGA_SLIDER_URL', get_stylesheet_directory_uri() . '/plugins/mega-slider/');
  if (!defined('MEGA_SLIDER_DIR'))
    define('MEGA_SLIDER_DIR', get_stylesheet_directory() . '/plugins/mega-slider/');
  if (!defined('NEWS_WIDGET_URL'))
    define('NEWS_WIDGET_URL', get_stylesheet_directory_uri() . '/plugins/news-widget/');
  if (!defined('NEWS_WIDGET_DIR'))
    define('NEWS_WIDGET_DIR', get_stylesheet_directory() . '/plugins/news-widget/');
  if (!defined('SOCIAL_SIDEBAR_URL'))
    define('SOCIAL_SIDEBAR_URL', get_stylesheet_directory_uri() . '/plugins/social-sidebar/');
  if (!defined('SOCIAL_SIDEBAR_DIR'))
    define('SOCIAL_SIDEBAR_DIR', get_stylesheet_directory() . '/plugins/social-sidebar/');
  if (!defined('HEADER_PLAYER_EXCERPT'))
    define('HEADER_PLAYER_EXCERPT', '<h4 class="player-excerpt-header excerpt-header">Biografische Angaben:</h4>');
  if (!defined('HEADER_STAFF_EXCERPT'))
    define('HEADER_STAFF_EXCERPT', '<h4 class="staff-excerpt-header excerpt-header">Über mich:</h4>');
  if (!defined('SSV_CATEGORY_BASE'))
    define('SSV_CATEGORY_BASE', 'ssv-category');
}

/**
 * get generic players photo for players gender
 */
function wbp_get_players_gender_photo_filename($id)
{

  if (!isset($player))
    $player = new SP_Player($id);

  $metrics = array_map('strtolower', $player->metrics(false));

  if (isset($metrics['Geschlecht']))
    return $metrics['Geschlecht'] === 'm' ? 'team-2.jpg' : 'team-8.jpg';

  return 'team-5.jpg';
}

/**
 * Include plugins.
 */
function wbp_include_plugins()
{
  require_once(__DIR__ . '/plugins/mega-slider/mega-slider.php');
  require_once(__DIR__ . '/plugins/news-widget/news-widget.php');
  require_once(__DIR__ . '/plugins/social-sidebar/social-sidebar.php');
  require_once(__DIR__ . '/plugins/team-links-widget/team-links-widget.php');
}

// remove the redirect UM plugin provides for new users (UM -> core -> um-actions-register.php)
remove_action('login_form_register', 'um_form_register_redirect', 10);

// Undefer scripts
function wbp_mesmerize_undefer_js_scripts($tag)
{

  $matches = array(
    includes_url('/js/masonry.min.js'),
  );

  foreach ($matches as $match) {
    if (strpos($tag, $match) !== false) {
      return str_replace('defer="defer" src', ' src', $tag);
    }
  }

  return $tag;
}
add_filter('script_loader_tag', 'wbp_mesmerize_undefer_js_scripts', 12, 1);

function wbp_get_the_posts_of_type($args)
{
  $defaults = array(
    'id' => null,
    'numberposts' => -1
  );
  $args = array_merge($defaults, $args);

  $posts = get_posts($args);

  return $posts;
}
function wbp_get_teams()
{
  $args = array(
    'post_type' => 'sp_team',
    'values' => 'ID',
  );
  return wbp_get_the_posts_of_type($args);
}

function wbp_get_players($team)
{
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
//add_filter( 'init', 'wbp_get_players' );

/*
 * CREATE CUSTOM CATEGORIES
 * hook into the init action and call create_book_taxonomies when it fires
 */

// Change build-in post_tag labels
function wbp_post_tag_labels($labels)
{

  $labels = array(
    'name' => 'Themen',
    'menu_name' => 'Themen',
    'singular_name' => _x('Thema', 'taxonomy singular name'),
    'search_items' => 'Themen suchen',
    'popular_items' => 'Meisst genutzt',
    'all_items' => 'Alle Themen',
    'parent_item' => null, // Tags aren't hierarchical
    'parent_item_colon' => null,
    'edit_item' => 'Thema bearbeiten',
    'update_item' => 'Thema aktualisieren',
    'add_new_item' => 'Neues Thema erstellen',
    'new_item_name' => 'Neuer Thema Name',
    'separate_items_with_commas' => 'Themen durch Kommas trennen.',
    'add_or_remove_items' => 'Themen hinzufügen oder entfernen',
    'choose_from_most_used' => 'Wähle aus den meisst genutzten Themen'
  );

  return $labels;
}
add_filter('taxonomy_labels_post_tag', 'wbp_post_tag_labels');

/*
 * Add new taxonomy, make it hierarchical like categories
 * 
 */
function wbp_create_ssv_hierarchical_taxonomy()
{

  /*
     * do the translations part for GUI
     */
  $labels = array(
    'name' => _x('SSV Kategorien', 'taxonomy general name', 'astra-child'),
    'singular_name' => _x('SSV Sektion', 'taxonomy singular name', 'astra-child'),
    'search_items' => __('SSV Kategorie suchen', 'astra-child'),
    'all_items' => __('Alle SSV Kategorien', 'astra-child'),
    'parent_item' => __('Übergeordnete SSV Kategorie', 'astra-child'),
    'parent_item_colon' => __('Übergeordnete SSV Kategorie:', 'ssv-altenberg', 'astra-child'),
    'edit_item' => __('SSV Category bearbeiten', 'astra-child'),
    'update_item' => __('SSV Kategorie aktualisieren', 'astra-child'),
    'add_new_item' => __('Neue SSV Kategorie erstellen', 'astra-child'),
    'new_item_name' => __('Neue SSV Kategorie', 'astra-child'),
    'menu_name' => __('SSV Kategorien', 'astra-child'),
  );

  // register taxonomy
  register_taxonomy(SSV_CATEGORY_BASE, array('post', 'mega-slider'), array(
    'hierarchical' => true,
    'public' => true,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'show_in_rest' => true,
    'show_in_quick_edit' => true,
    'query_var' => true,
    'rewrite' => array('slug' => SSV_CATEGORY_BASE),
  ));
}
add_action('init', 'wbp_create_ssv_hierarchical_taxonomy', 1);

/*
 * Create SSV Category for each team
 */
function wbp_insert_ssv_category($slug, $title)
{

  $parent_slug = 'ssv-all-teams';
  if (!term_exists($parent_slug, SSV_CATEGORY_BASE)) {
    $parent_title = 'Alle Sektionen';

    $parent_term = wp_insert_term(
      $parent_title,
      SSV_CATEGORY_BASE,
      array(
        'description'  => 'SSV speziefische Eltern-Kategorie für alle Teams',
        'slug'     => $parent_slug
      )
    );
    $parent_term_id = $parent_term['term_id'];
  } else {
    $parent_term = get_term_by('slug', $parent_slug, SSV_CATEGORY_BASE);
    $parent_term_id = $parent_term->term_id;
  }
  wp_insert_term(
    $title,
    SSV_CATEGORY_BASE,
    array(
      'parent' => $parent_term_id,
      'description'  => 'SSV speziefische Kategorie für Team ' . $title,
      'slug'     => $slug
    )
  );
  wp_insert_term(
    $title,
    'post_tag',
    array(
      'description'  => 'SSV speziefisches Thema für Team ' . $title,
      'slug'     => $slug
    )
  );
}
function wbp_insert_ssv_categories()
{

  $teams = wbp_get_teams();
  foreach ($teams as $team) {
    $team_title = $team->post_title;
    $team_slug = $team->post_name;
    wbp_insert_ssv_category($team_slug, $team_title);
  }

  add_filter('mega-slider_register_post_type', function ($args) {
    $args = wp_parse_args($args, array(
      'taxonomies'       => array(SSV_CATEGORY_BASE, 'category')
    ));
    return $args;
  }, 100);

  register_taxonomy_for_object_type(SSV_CATEGORY_BASE, 'mega-slider');
  register_post_type(SSV_CATEGORY_BASE);
}
add_action('init', 'wbp_insert_ssv_categories', 11);

function wbp_the_ssv_category($category = array())
{
  global $post;

  $terms = get_the_terms($post, SSV_CATEGORY_BASE);
  if ($terms) {
    $category = wp_parse_args($category, $terms);
  }

  return $category;
}
add_filter('get_the_categories', 'wbp_the_ssv_category');

function wbp_ssv_posts_by_term($terms = array(), $categories = array(), $args = array())
{

  if (!$terms && !$categories) return array();

  $args = wp_parse_args($args, array(
    'post_type' => 'post',
    'tax_query' => array(
      'relation' => 'OR',
      array(
        'taxonomy' => SSV_CATEGORY_BASE,
        'terms' => $terms
      ),
      array(
        'taxonomy'  => 'category',
        'field' => 'id',
        'terms' => $categories,
      ),
    )
  ));

  $get_posts = new WP_Query($args);
  $posts = $get_posts->posts;

  return $posts;
}

// News Widget for SSV Categories
function wbp_widget_ssv_posts_args($args)
{
  global $post;

  $args = wp_parse_args($args, array(
    'tax_query' => array(
      'relation' => 'OR',
      array(
        'taxonomy' => SSV_CATEGORY_BASE,
        'field' => 'slug',
        'terms' => $post->post_name
      )
    )
  ));
  return $args;
}
add_filter('widget_posts_args', 'wbp_widget_ssv_posts_args');


// team link for ssv-category posts
function wbp_team_link($args)
{

  $teams = wbp_get_teams();
  $team_names = [];
  foreach ($teams as $team) {
    $team_names[] = $team->post_name;
  }

  $ssv_cats = wbp_the_ssv_category();
  $ssv_cat_names = [];
  foreach ($ssv_cats as $ssv_cat) {
    $ssv_cat_names[] = $ssv_cat->slug;
  }

  $slugs = array_intersect($ssv_cat_names, $team_names);

  if (!empty($slugs)) {

    $defaults = array(
      'classnames' => 'post-item',
      'title' => 'Zu den Teams in diesem Beitrag'
    );

    $args = wp_parse_args($args, $defaults);

    set_query_var('args', $args);
    get_template_part('template-parts/elements/team', 'link-start', $args);

    foreach ($slugs as $slug) {

      $args = array(
        'post_type' => 'sp_team',
        'name' => $slug
      );
      $query = new WP_Query($args);
      $team = $query->post;

      // pass variable to template
      set_query_var('the_team', $team);
      get_template_part('template-parts/elements/team', 'link');
    }

    get_template_part('template-parts/elements/team', 'link-end');
  }
}
add_action('output_team_links', 'wbp_team_link');

// Post Thumb
function wbp_post_image_preview($thumbnail_url)
{
  global $post;

  $post_types = array('sp_staff', 'sp_player');
  $post_type = $post->post_type;

  if (in_array($post_type, $post_types)) {

    $id = $post->ID;
    $user_id = wbp_get_user_id_by_author($id);
    $avatar_data = um_get_user_avatar_data($user_id);
    $avatar_url = (is_array($avatar_data) && isset($avatar_data['url'])) ? $avatar_data['url'] : FALSE;

    if (has_post_thumbnail($id)) :
      $thumbnail_url = get_the_post_thumbnail_url($id, 'sportspress-fit-medium');
    elseif ($avatar_url) :
      $thumbnail_url = $avatar_url;
    else :
      $thumbnail_url = '/wp-content/plugins/mesmerize-companion/theme-data/mesmerize/sections/images/team-2.jpg';
    endif;
  }
  return $thumbnail_url;
}
add_filter('mesmerize_post_image_preview', 'wbp_post_image_preview');

// remove cache cleaner button for non admins
function wbp_remove_cache_cleaner_button($wp_admin_bar)
{

  $user = wp_get_current_user();

  if (empty($user))
    return;

  $roles = $user->roles;

  if (!empty($roles) && 'administrator' === $roles[0])
    return;

  $wp_admin_bar->remove_menu('mesmerize_clear_theme_cache');
}
add_action('admin_bar_menu', 'wbp_remove_cache_cleaner_button', 73);

// Register Widget Team-Links
add_action('widgets_init', function () {

  // register_widget('Team_Links_Widget');
});

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

// END ENQUEUE PARENT ACTION
