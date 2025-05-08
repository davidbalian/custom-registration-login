<?php
/*
Template Name: Simple Custom Login Page
*/

// Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * IMPORTANT: By default, WordPress will redirect logged-in users trying
 * to access wp-login.php. If you redirect wp-login.php to this page
 * (as shown in a previous step), you might get a redirect loop
 * if you don't redirect logged-in users *from* this page as well.
 *
 * The check below will redirect logged-in users away from this page.
 * You can change the redirect URL (home_url() is used here).
 */
if ( is_user_logged_in() ) {
    wp_redirect( home_url( '/' ) ); // Redirect logged-in users to homepage
    exit();
}

get_header(); // Include your theme's header

?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

            <header class="entry-header">
                <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
            </header><div class="entry-content">

                <?php
                // Display any content added in the WordPress page editor above the form
                the_content();
                ?>

                <p><?php esc_html_e( 'Please log in.', 'textdomain' ); ?></p> <form id="custom-login-form" class="custom-login-form" method="post" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>">

                    <?php
                    // Important: Add a hidden redirect_to field. This tells wp-login.php where
                    // to send the user after a successful login attempt. Defaulting to homepage.
                    // You can customize this redirect URL here if needed.
                    ?>
                     <input type="hidden" name="redirect_to" value="<?php echo esc_url( home_url( '/' ) ); ?>" />


                    <label for="user_login"><?php esc_html_e( 'Email or username', 'textdomain' ); ?><br /> <input type="text" name="log" id="user_login" class="form-control input-text username-field" value="<?php echo esc_attr( isset( $_POST['log'] ) ? wp_unslash( $_POST['log'] ) : '' ); ?>" size="20" autocapitalize="off" /></label>

                    <label for="user_pass"><?php esc_html_e( 'Password', 'textdomain' ); ?><br /> <input type="password" name="pwd" id="user_pass" class="form-control input-text password-field" value="" size="20" /></label>

                    <p class="forgetmenot">
                        <label for="rememberme">
                            <input name="rememberme" type="checkbox" id="rememberme" class="form-check-input" value="forever" /> <?php esc_html_e( 'Remember Me', 'textdomain' ); ?> </label>
                    </p>

                    <input type="submit" name="wp-submit" id="wp-submit" class="btn btn-primary login-button" value="<?php esc_attr_e( 'Log In', 'textdomain' ); ?>" /> <?php // Include a hidden field for the test cookie - standard for wp-login.php ?>
                     <input type="hidden" name="testcookie" value="1" />

                     <?php
                        // Add login error messages if any
                        // Note: login_errors filter is often added in functions.php or a plugin.
                        // If errors don't appear, you may need to hook into the login_errors filter
                        // elsewhere to capture and set the error messages.
                        $errors = new WP_Error();
                        if ( isset( $_GET['login'] ) && $_GET['login'] === 'failed' ) {
                             $errors->add( 'authentication_failed', __( '<strong>ERROR</strong>: Invalid username or incorrect password.', 'textdomain' ) ); // Added text domain
                        } elseif ( isset( $_GET['login'] ) && $_GET['login'] === 'inactive' ) {
                             $errors->add( 'inactive_user', __( '<strong>ERROR</strong>: Your account is inactive.', 'textdomain' ) ); // Added text domain
                        }
                        // You can add more custom error handling here based on $_GET parameters or other logic

                        // Display accumulated errors
                        if ( $errors->get_error_codes() ) {
                            echo '<div class="login-error-message">';
                            foreach ( $errors->get_error_messages() as $message ) {
                                echo '<p>' . $message . '</p>';
                            }
                            echo '</div>';
                        }

                        // Basic failure message if no specific error object output (less common now with filters)
                        // This might overlap with the WP_Error handling above, keep only if needed.
                         // elseif ( isset( $_GET['login'] ) && 'failed' === $_GET['login'] ) {
                         //     echo '<div class="login-error-message">' . esc_html__( 'Login failed. Please try again.', 'textdomain' ) . '</div>'; // Added text domain
                         // }
                     ?>

                </form>
                <p class="register-links">
                    <?php
                    // Prepare URLs and translatable text segments for the registration links
                    $register_user_url = home_url( '/register' ); // Use home_url() for site-relative links
                    $register_vendor_url = home_url( '/register-vendor' ); // Use home_url() for site-relative links
                    $lost_password_url = wp_lostpassword_url(); // Use WP function for lost password

                    $no_account_text = esc_html__( 'No account?', 'textdomain' ); // Added text domain
                    $register_user_text = esc_html__( 'Register as a user', 'textdomain' ); // Added text domain
                    $or_text = esc_html__( 'or', 'textdomain' ); // Added text domain
                    $register_vendor_text = esc_html__( 'register as a vendor', 'textdomain' ); // Added text domain
                    $lost_password_text = esc_html__( 'Lost your password?', 'textdomain' ); // Added text domain

                    // Output the HTML structure with translated text and escaped URLs
                    // This maintains the exact structure: "Text <a...>text</a>, text <a...>text</a>"
                    echo $no_account_text . ' <a href="' . esc_url( $register_user_url ) . '">' . $register_user_text . '</a>, ' . $or_text . ' <a href="' . esc_url( $register_vendor_url ) . '">' . $register_vendor_text . '</a>';

                    // Add the lost password link
                    echo '<br /><a href="' . esc_url( $lost_password_url ) . '">' . $lost_password_text . '</a>';
                    ?>
                </p>

            </div></article></main></div><?php
get_footer(); // Include your theme's footer
?>