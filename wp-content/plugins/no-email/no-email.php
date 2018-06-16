<?php

/*
Plugin Name: Block Email Notification
Plugin URI: http://www.barattalo.it/
Description: Blocks email notification
Author: Better Days
Version: 0.1.0
*/
 
// disable all new user notification email
if ( ! function_exists( 'wp_new_user_notification' ) ) :
    function wp_new_user_notification( $user_id, $deprecated = null, $notify = '' ) {
    
        return;
    }
endif;

