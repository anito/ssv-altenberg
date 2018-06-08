<?php
// Function to change email address
 
function wpb_sender_email( $original_email_address ) {
    $url = home_url();
    $pattern = '/(https?):\/\/(.*)/';
    preg_match( $pattern, $url, $matches );
    return 'ssv' . $matches[2];
}
 
// Function to change sender name
function wpb_sender_name( $original_email_from ) {
    return 'SSV';
}
 
// Hooking up our functions to WordPress filters 
add_filter( 'wp_mail_from', 'wpb_sender_email' );
add_filter( 'wp_mail_from_name', 'wpb_sender_name' );
