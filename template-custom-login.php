<?php
/*
Template Name: Custom Login Page
*/

// Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Check if the user is already logged in, redirect if they are
if ( is_user_logged_in() ) {
    wp_redirect( home_url() ); // Redirect to homepage or another page
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
                // Display any content added in the WordPress page editor
                the_content();

                // Display the WordPress login form
                wp_login_form( array(
                    'redirect' => home_url(), // URL to redirect to after successful login
                    'form_id' => 'custom-loginform', // Custom ID for the form
                    'label_username' => __( 'Username or Email Address' ),
                    'label_password' => __( 'Password' ),
                    'label_remember' => __( 'Remember Me' ),
                    'label_log_in' => __( 'Log In' ),
                    'id_username' => 'user_login',
                    'id_password' => 'user_pass',
                    'id_remember' => 'rememberme',
                    'id_submit' => 'wp-submit',
                    'remember' => true, // Show the "Remember Me" checkbox
                    'value_username' => '', // Set a default username
                    'value_remember' => false // Set the default state of the "Remember Me" checkbox
                ) );

                // Add links for Lost Password and Register (if registration is enabled)
                ?>
                <p id="nav">
                    <?php if ( get_option( 'users_can_register' ) ) : ?>
                        <a href="<?php echo wp_registration_url(); ?>"><?php _e( 'Register' ); ?></a> |
                    <?php endif; ?>
                    <a href="<?php echo wp_lostpassword_url(); ?>"><?php _e( 'Lost your password?' ); ?></a>
                </p>

            </div></article></main></div><?php
get_footer(); // Include your theme's footer
?>
