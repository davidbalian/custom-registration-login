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

                <div class="custom-login-form-container">
                    <form name="loginform" id="custom-login-form" class="custom-login-form" method="post" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>">
                        <?php
                        // Determine redirect URL - defaulting to current page or homepage if not specified
                        // For a template, you might hardcode this or use a theme option.
                        // Using home_url('/') as per original template for simplicity here.
                        $redirect_url = home_url( '/' );
                        ?>
                        <p class="login-username">
                            <label for="user_login_<?php echo esc_attr( uniqid() ); ?>"><?php echo esc_html__( 'Username or Email Address', 'custom-login' ); ?></label>
                            <input type="text" name="log" id="user_login_<?php echo esc_attr( uniqid() ); ?>" class="input" value="" size="20" />
                        </p>
                        <p class="login-password">
                            <label for="user_pass_<?php echo esc_attr( uniqid() ); ?>"><?php echo esc_html__( 'Password', 'custom-login' ); ?></label>
                            <input type="password" name="pwd" id="user_pass_<?php echo esc_attr( uniqid() ); ?>" class="input" value="" size="20" />
                        </p>

                        <p class="login-remember">
                            <label for="rememberme_<?php echo esc_attr( uniqid() ); ?>">
                                <input name="rememberme" type="checkbox" id="rememberme_<?php echo esc_attr( uniqid() ); ?>" value="forever" /> <?php esc_html_e( 'Remember Me', 'custom-login' ); ?>
                            </label>
                        </p>

                        <p class="login-submit">
                            <input type="submit" name="wp-submit" id="wp-submit_<?php echo esc_attr( uniqid() ); ?>" class="button button-primary" value="<?php esc_attr_e( 'Log In', 'custom-login' ); ?>" />
                            <input type="hidden" name="redirect_to" value="<?php echo esc_url( $redirect_url ); ?>" />
                        </p>

                        <?php
                        $show_lost_password_link = true; // Or some theme option
                        $users_can_register = get_option( 'users_can_register' );
                        $show_register_link = $users_can_register; // Or some theme option
                        ?>

                        <?php if ( $show_lost_password_link || $show_register_link ) : ?>
                            <p class="login-links">
                                <?php if ( $show_lost_password_link ) : ?>
                                    <a href="<?php echo esc_url( wp_lostpassword_url( $redirect_url ) ); ?>"><?php esc_html_e( 'Lost your password?', 'custom-login' ); ?></a>
                                <?php endif; ?>

                                <?php if ( $show_register_link ) : ?>
                                    <?php if ( $show_lost_password_link ) : ?>
                                        <span class="login-separator"> | </span>
                                    <?php endif; ?>
                                    <a href="<?php echo esc_url( wp_registration_url() ); ?>"><?php esc_html_e( 'Register', 'custom-login' ); ?></a>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                    </form>
                </div>

            </div></article></main></div><?php
get_footer(); // Include your theme's footer
?>