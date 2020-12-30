<?php

$texts['title']     = $title;
$texts['header']    = $heading;
$texts['subheader'] = $text;

$days               = $this->plugin_settings['modules']['countdown_details']['days'];
$hours              = $this->plugin_settings['modules']['countdown_details']['hours'];
$minutes            = $this->plugin_settings['modules']['countdown_details']['minutes'];

$assetsUrl = get_stylesheet_directory_uri() . '/maintenance/assets/';

$allowed_socials = array('facebook', 'twitter', 'instagram', 'google+');
$social_prefix = 'social_';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="author" content="<?php echo esc_attr($author); ?>" />
    <meta name="description" content="<?php echo esc_attr($description); ?>" />
    <meta name="keywords" content="<?php echo esc_attr($keywords); ?>" />
    <meta name="robots" content="<?php echo esc_attr($robots); ?>" />
    <?php
    if (!empty($styles) && is_array($styles)) {
        foreach ($styles as $src) {
            ?>
            <link rel="stylesheet" href="<?php echo $src; ?>">
            <?php
        }
    }
    if (!empty($custom_css) && is_array($custom_css)) {
        echo '<style>' . implode(array_map('stripslashes', $custom_css)) . '</style>';
    }
    
    ?>
    <link rel="icon" href="/favicon.ico">
    <link rel="stylesheet" href="<?php echo $assetsUrl; ?>styles.css">
    <script src="<?php echo $assetsUrl; ?>timer.js"></script>
    <title><?php echo $texts['title']; ?></title>
</head>

<body class="<?php echo $body_classes ? $body_classes : ''; ?>">
    <div class="container">

    <header class="header">
        <h1><?php echo $texts['header']; ?></h1>
        <h2><?php echo $texts['subheader']; ?></h2>
    </header>

    <!--START_TIMER_BLOCK-->
    <?php if ( $this->plugin_settings['modules']['countdown_status'] ): ?>
        <section class="timer">
            <div class="timer__item">
                <div class="timer__data" id="timerResultDays"></div>
                <div class="timer__type"><?php _e('Tage', 'wp'); ?></div>
            </div>:
            <div class="timer__item">
                <div class="timer__data" id="timerResultHours"></div>
                <div class="timer__type"><?php _e('Stunden', 'wp'); ?></div>
            </div>:
            <div class="timer__item">
                <div class="timer__data" id="timerResultMinutes"></div>
                <div class="timer__type"><?php _e('Minuten', 'wp'); ?></div>
            </div>:
            <div class="timer__item">
                <div class="timer__data" id="timerResultSeconds"></div>
                <div class="timer__type"><?php _e('Sekunden', 'wp'); ?></div>
            </div>
        </section>
        <script type="application/javascript">
            startTimer(<?php echo $days . ',' . $hours . ',' . $minutes . ',' . strtotime($countdown_start) . ',' . time(); ?> - Math.floor(Date.now() / 1000));
        </script>
    <?php endif; ?>
    <!--END_TIMER_BLOCK-->

    <!--START_SOCIAL_LINKS_BLOCK-->
    <section class="social-links">
        <?php foreach($this->plugin_settings['modules'] as $network => $url): ?>
            <?php $social = (($test = preg_replace('/'.$social_prefix.'(\w+)/', '$1', $network)) === $network) ?
                FALSE : $test ?>
            <?php if( $social &&  in_array($social, $allowed_socials) && !empty($url) ) { ?>
            <a class="social-links__link" href="<?php echo $url;?>" target="_blank" title="<?php echo ucfirst($social);?>">
                <span class="icon"><img src="<?php echo $assetsUrl; ?>images/<?php echo $social;?>.svg" alt="<?php echo ucfirst($social);?>"></span>
            </a>
            <?php  } ?>
        <?php endforeach; ?>
    </section>
    <!--END_SOCIAL_LINKS_BLOCK-->
    
    <?php if (!empty($this->plugin_settings['modules']['contact_status']) && $this->plugin_settings['modules']['contact_status'] == 1) { ?>
        <div class="contact">
            <?php list($open, $close) = !empty($this->plugin_settings['modules']['contact_effects']) && strstr($this->plugin_settings['modules']['contact_effects'], '|') ? explode('|', $this->plugin_settings['modules']['contact_effects']) : explode('|', 'move_top|move_bottom'); ?>
            <div class="form <?php echo esc_attr($open); ?>">
                <span class="close-contact_form">
                    <img src="<?php echo WPMM_URL ?>assets/images/close.svg" alt="">
                </span>

                <form class="contact_form">
                    <?php do_action('wpmm_contact_form_start'); ?>

                    <p class="col"><input type="text" placeholder="<?php _e('Name', $this->plugin_slug); ?>" data-rule-required="true" data-msg-required="<?php esc_attr_e('This field is required.', $this->plugin_slug); ?>" name="name" class="name_input" /></p>
                    <p class="col last"><input type="text" placeholder="<?php _e('E-mail', $this->plugin_slug); ?>" data-rule-required="true" data-rule-email="true" data-msg-required="<?php esc_attr_e('This field is required.', $this->plugin_slug); ?>" data-msg-email="<?php esc_attr_e('Please enter a valid email address.', $this->plugin_slug); ?>" name="email" class="email_input" /></p>
                    <br clear="all" />

                    <?php do_action('wpmm_contact_form_before_message'); ?>

                    <p><textarea placeholder="<?php _e('Your message', $this->plugin_slug); ?>" data-rule-required="true" data-msg-required="<?php esc_attr_e('This field is required.', $this->plugin_slug); ?>" name="content" class="content_textarea"></textarea></p>

                    <?php do_action('wpmm_contact_form_after_message'); ?>

                    <?php if (!empty($this->plugin_settings['gdpr']['status']) && $this->plugin_settings['gdpr']['status'] == 1) { ?>
                        <div class="privacy_checkbox"><input type="checkbox" name="acceptance" value="YES" data-rule-required="true" data-msg-required="<?php esc_attr_e('This field is required.', $this->plugin_slug); ?>"><label for="acceptance"><?php _e("I've read and agree with the site's privacy policy", $this->plugin_slug); ?></label></div>
                        <?php if(!empty($this->plugin_settings['gdpr']['contact_form_tail'])) { ?>
                            <p class="privacy_tail"><?php echo $this->plugin_settings['gdpr']['contact_form_tail']; ?></p>
                        <?php }} ?>
                    <p class="submit"><input type="submit" value="<?php _e('Send', $this->plugin_slug); ?>"></p>

                    <?php do_action('wpmm_contact_form_end'); ?>
                </form>
            </div>
        </div>

        <a class="contact_us" href="javascript:void(0);" data-open="<?php echo esc_attr($open); ?>" data-close="<?php echo esc_attr($close); ?>"><?php _e('Webmaster kontaktieren', $this->plugin_slug); ?></a>
    <?php } ?>

    <?php if ((!empty($this->plugin_settings['general']['admin_link']) && $this->plugin_settings['general']['admin_link'] == 1) ||
              (!empty($this->plugin_settings['gdpr']['status']) && $this->plugin_settings['gdpr']['status'] == 1)) { ?>
        <div class="author_link">
            <?php if($this->plugin_settings['general']['admin_link'] == 1) { ?>
                <a href="<?php echo admin_url(); ?>"><?php _e('Dashboard', $this->plugin_slug); ?></a> 
            <?php } ?>
            <?php if ($this->plugin_settings['gdpr']['status'] == 1) { ?>
                <a href="<?php echo $this->plugin_settings['gdpr']['policy_page_link']; ?>"><?php echo $this->plugin_settings['gdpr']['policy_page_label']; ?></a>
            <?php } ?>
        </div>
    <?php } ?>
    </div>

    <script type='text/javascript'>
    var wpmm_vars = {"ajax_url": "<?php echo admin_url('admin-ajax.php'); ?>"};
    </script>

    <?php

    // Hook before scripts, mostly for internationalization
    do_action('wpmm_before_scripts');

    if (!empty($scripts) && is_array($scripts)) {
    foreach ($scripts as $src) {
        ?>
        <script src="<?php echo $src; ?>"></script>
        <?php
    }
    }
    // Do some actions
    do_action('wm_footer'); // this hook will be removed in the next versions
    do_action('wpmm_footer');
    ?>
    <?php if (!empty($this->plugin_settings['bot']['status']) && $this->plugin_settings['bot']['status'] === 1) { ?>
    <script type='text/javascript'>
        jQuery(function($) {
            startConversation('homepage', 1);
        });
    </script>
    <?php } ?>
    
</div>

<footer class="footer">
    <div class="footer__bg_copyright"><?php echo $credits; ?></div>
    <div class="footer__content">
        <?php printf( __( '%1$s is proudly powered by %2$s' ), get_bloginfo('name'), '<a href="https://webpremiere.de/" target="_blank"><img class="logo" src="https://files.webpremiere.de/f/a4015838ca4941d3a90f/?dl=1" alt="WebPremiere"></a><sup>&reg;</sup>' ); ?>
    </div>
</footer>

</body>
</html>