<?php
// /**
//  * Custom Redirects
//  *
//  * Handles redirects for wp-login.php and wp-admin.
//  *
//  * @package YourSiteCustomFunctions
//  */

// // Ensure this file is not accessed directly
if ( ! defined( 'ABSPATH' ) ) {
     exit;
 }

 /**
  * Redirects wp-login.php and wp-admin to the custom login page slug
  * if the user is not logged in.
  */
 function custom_login_redirect_default() {
     $current_uri = $_SERVER['REQUEST_URI'];
     // Define your custom login page slug. Make this consistent across your code.
     $login_page_slug = 'login';

     // Check if the user is NOT logged in and is trying to access wp-login.php or wp-admin
    // The DOING_AJAX check prevents redirecting AJAX requests to wp-admin
     if ( ! is_user_logged_in() && ( stripos( $current_uri, 'wp-login.php' ) !== false || ( stripos( $current_uri, 'wp-admin' ) !== false && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) ) ) {
         // Perform a safe redirect to the custom login page URL
         wp_safe_redirect( home_url( '/' . $login_page_slug ) );
         exit; // Always exit after a redirect
     }
}
// Hook the function to the 'init' action, which runs early.
// A priority might be needed if conflicts occur, but default 10 is usually fine.
add_action( 'init', 'custom_login_redirect_default' );