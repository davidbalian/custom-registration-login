<?php
/**
 * Simple Custom Login Form and Homepage Redirect
 *
 * Provides a [custom_login_form] shortcode.
 * - Redirects already logged-in users on this page to the homepage.
 * - Redirects users to the homepage after successful login.
 *
 * @package CustomRegistrationLogin
 */

// Ensure this file is not accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handles the redirection after a successful login.
 * Uses the 'login_redirect' filter to always redirect to the homepage.
 *
 * @param string  $redirect_to            The redirect destination URL WordPress determined.
 * @param string  $requested_redirect_to  The URL the user was attempting to reach (if any).
 * @param WP_User $user                   The WP_User object of the logged-in user.
 * @return string The new redirect URL (always the homepage URL).
 */
function custom_simple_login_redirect_handler( $redirect_to, $requested_redirect_to, $user ) {
    // Ensure the user object is valid, though this filter should fire on success.
    if ( is_wp_error( $user ) || ! ( $user instanceof WP_User ) ) {
        return $redirect_to; // Return default if user object is invalid
    }

    // Always redirect to the site's homepage after successful login.
    return home_url( '/' );
}
// Hook into the 'login_redirect' filter. Priority 10 (default) is fine, or use 99 to run late.
// Accepts 3 arguments: $redirect_to, $requested_redirect_to, $user.
add_filter( 'login_redirect', 'custom_simple_login_redirect_handler', 10, 3 );


/**
 * Renders a simple custom login form using the [custom_login_form] shortcode.
 *
 * If a user is already logged in when viewing a page with this shortcode,
 * they will be redirected to the homepage.
 *
 * @param array $atts Shortcode attributes (not used in this implementation).
 * @return string HTML for the login form, or triggers a redirect.
 */
function custom_render_simple_login_form_shortcode( $atts ) {
    // If the user is already logged in, redirect them to the homepage.
    if ( is_user_logged_in() ) {
        wp_safe_redirect( home_url( '/' ) );
        exit; // Always exit after a redirect
    }

    // User is not logged in, display the login form.
    // Start output buffering to capture the HTML.
    ob_start();
    ?>
    <p><?php esc_html_e( 'Fill out your details to log in.', 'customregistrationlogin' ); ?></p>
    <form id="custom-login-form" class="custom-login-form" method="post" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>">

        <?php
        // Important: Add a hidden redirect_to field. This tells wp-login.php where
        // to send the user after a successful login attempt. We want the homepage.
        ?>
         <input type="hidden" name="redirect_to" value="<?php echo esc_url( home_url( '/' ) ); ?>" />


        <label for="user_login"><?php esc_html_e( 'Email or username', 'customregistrationlogin' ); ?><br />
        <input type="text" name="log" id="user_login" class="form-control input-text username-field" value="<?php echo esc_attr( isset( $_POST['log'] ) ? wp_unslash( $_POST['log'] ) : '' ); ?>" size="20" autocapitalize="off" /></label>

        <label for="user_pass"><?php esc_html_e( 'Password', 'customregistrationlogin' ); ?><br />
        <input type="password" name="pwd" id="user_pass" class="form-control input-text password-field" value="" size="20" /></label>

        <p class="forgetmenot">
            <label for="rememberme">
                <input name="rememberme" type="checkbox" id="rememberme" class="form-check-input" value="forever" /> <?php esc_html_e( 'Remember Me', 'customregistrationlogin' ); ?>
            </label>
        </p>

        <input type="submit" name="wp-submit" id="wp-submit" class="btn btn-primary login-button" value="<?php esc_attr_e( 'Log In', 'customregistrationlogin' ); ?>" />
         <?php // Include a hidden field for the test cookie - standard for wp-login.php ?>
         <input type="hidden" name="testcookie" value="1" />

         <?php
            // Add login error messages if any
            $errors = apply_filters( 'login_errors', '' ); // Filter login errors
            if ( ! empty( $errors ) ) {
                echo '<div class="login-error-message">' . $errors . '</div>';
            } elseif ( isset( $_GET['login'] ) && 'failed' === $_GET['login'] ) {
                 // Basic failure message if no specific error filter output (less common now with filters)
                 echo '<div class="login-error-message">' . esc_html__( 'Login failed. Please try again.', 'customregistrationlogin' ) . '</div>';
            }
         ?>

    </form>
    <p class="register-links">
        <?php
        // Prepare URLs and translatable text segments for the registration links
        // Using site_url() for wp-login.php?action=register is another option for WP's built-in reg page
        $register_user_url = home_url( '/register' ); // Use home_url() for site-relative links
        $register_vendor_url = home_url( '/register-vendor' ); // Use home_url() for site-relative links

        $no_account_text = esc_html__( 'No account?', 'customregistrationlogin' );
        $register_user_text = esc_html__( 'Register as a user', 'customregistrationlogin' );
        $or_text = esc_html__( 'or', 'customregistrationlogin' ); // Comma is structural, "or" is translatable
        $register_vendor_text = esc_html__( 'register as a vendor', 'customregistrationlogin' );

        // Output the HTML structure with translated text and escaped URLs
        // This maintains the exact structure: "Text <a...>text</a>, text <a...>text</a>"
        echo $no_account_text . ' <a href="' . esc_url( $register_user_url ) . '">' . $register_user_text . '</a>, ' . $or_text . ' <a href="' . esc_url( $register_vendor_url ) . '">' . $register_vendor_text . '</a>';
        ?>
    </p>
    <?php
    // Return the buffered content.
    $output = ob_get_clean();
    return $output;
}

// Register the simple custom login form shortcode.
add_shortcode( 'custom_login_form', 'custom_render_simple_login_form_shortcode' );