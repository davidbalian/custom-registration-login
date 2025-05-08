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
  * if the user is not logged in, while allowing necessary actions on wp-login.php.
  */
 function custom_login_redirect_default() {
     // If user is logged in, this function has nothing to do here.
     // (Another function likely handles logged-in users visiting /login, as per user's problem description)
     if ( is_user_logged_in() ) {
         return;
     }

     $request_uri = $_SERVER['REQUEST_URI'];
     // Define your custom login page slug. Make this consistent across your code.
     $login_page_slug = 'login';
     $custom_login_url_path = '/' . $login_page_slug;

     // Prevent redirect loop: if already on the custom login page, do nothing.
     if ( strpos( $request_uri, $custom_login_url_path ) !== false ) {
         return;
     }

     $is_wplogin_url = stripos( $request_uri, 'wp-login.php' ) !== false;
     // For wp-admin, is_admin() is a more robust check, but REQUEST_URI is used here for consistency with original.
     $is_wpadmin_url = stripos( $request_uri, 'wp-admin' ) !== false;

     $should_redirect_to_custom_login = false;

     if ( $is_wplogin_url ) {
         // For wp-login.php:
         // Redirect if it's a GET request for the 'login' action (i.e., trying to view the default login form).
         // Allow POST requests (e.g., login form submissions) and other GET actions (e.g., register, lostpassword, rp, resetpass).
         $current_action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'login'; // Default action on wp-login.php is 'login'.

         if ( $_SERVER['REQUEST_METHOD'] === 'GET' && $current_action === 'login' ) {
             $should_redirect_to_custom_login = true;
         }
         // If it's a POST (e.g. submitting login form) or a GET with actions like 'register', 'lostpassword', etc.,
         // $should_redirect_to_custom_login remains false, so wp-login.php can process them.

     } elseif ( $is_wpadmin_url ) {
         // For wp-admin: redirect if the user is not logged in and it's not an AJAX request.
         if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
             $should_redirect_to_custom_login = true;
         }
     }

     if ( $should_redirect_to_custom_login ) {
         wp_safe_redirect( home_url( $custom_login_url_path ) );
         exit; // Always exit after a redirect
     }
 }
// Hook the function to the 'init' action, which runs early.
// A priority might be needed if conflicts occur, but default 10 is usually fine.
add_action( 'init', 'custom_login_redirect_default' );