<?php
/**
 * Plugin Name: Custom Login Form Shortcode
 * Description: Adds a [custom_login_form] shortcode to display a login form.
 * Version: 1.0
 * Author: AI Assistant
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! function_exists( 'custom_login_form_shortcode_handler' ) ) {
    /**
     * Renders the custom login form.
     *
     * @param array $atts Shortcode attributes.
     * @return string The login form HTML or logged-in message.
     */
    function custom_login_form_shortcode_handler( $atts ) {
        // Default attributes
        $atts = shortcode_atts(
            array(
                'redirect' => '', // Default redirect URL after login (empty means current page or home_url('/') if not specified further down)
                'show_lost_password' => 'yes',
                'show_register' => 'yes',
                'show_remember_me' => 'yes',
                'show_logged_in_message' => 'yes',
                'login_button_text' => esc_html__( 'Log In', 'custom-login' ),
                'username_label' => esc_html__( 'Email or username', 'custom-login' ),
                'password_label' => esc_html__( 'Password', 'custom-login' ),
            ),
            $atts,
            'custom_login_form'
        );

        if ( is_user_logged_in() ) {
            if ( 'yes' === $atts['show_logged_in_message'] ) {
                $current_user = wp_get_current_user();
                $logout_redirect_url = ! empty( $atts['redirect'] ) ? esc_url( $atts['redirect'] ) : home_url( '/' );
                return sprintf(
                    /* translators: 1: User display name, 2: Link opening tag, 3: Link closing tag. */
                    esc_html__( 'You are logged in as %1$s (%2$sLogout%3$s)', 'custom-login' ),
                    esc_html( $current_user->display_name ),
                    sprintf( '<a href="%s">', esc_url( wp_logout_url( $logout_redirect_url ) ) ),
                    '</a>'
                );
            }
            return '';
        }

        // Determine redirect URL, defaulting to home_url('/') if not specified, like custom-login.php
        $redirect_url = ! empty( $atts['redirect'] ) ? esc_url( $atts['redirect'] ) : esc_url( home_url( '/' ) );

        ob_start();
        ?>
        <div class="custom-login-form-container">
            <form id="custom-login-form" class="custom-login-form" method="post" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>">

                <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_url ); ?>" />

                <p class="login-username">
                    <label for="user_login"><?php echo esc_html( $atts['username_label'] ); ?><br />
                    <input type="text" name="log" id="user_login" class="form-control input-text username-field" value="" size="20" autocapitalize="off" /></label>
                </p>

                <p class="login-password">
                    <label for="user_pass"><?php echo esc_html( $atts['password_label'] ); ?><br />
                    <input type="password" name="pwd" id="user_pass" class="form-control input-text password-field" value="" size="20" /></label>
                </p>

                <?php if ( 'yes' === $atts['show_remember_me'] ) : ?>
                <p class="forgetmenot">
                    <label for="rememberme">
                        <input name="rememberme" type="checkbox" id="rememberme" class="form-check-input" value="forever" /> <?php esc_html_e( 'Remember Me', 'custom-login' ); ?>
                    </label>
                </p>
                <?php endif; ?>

                <p class="login-submit">
                    <input type="submit" name="wp-submit" id="wp-submit" class="btn btn-primary login-button" value="<?php echo esc_attr( $atts['login_button_text'] ); ?>" />
                    <input type="hidden" name="testcookie" value="1" />
                </p>

                <?php
                $show_lost_password_link = ( 'yes' === $atts['show_lost_password'] );
                $users_can_register = get_option( 'users_can_register' );
                $show_register_link = ( $users_can_register && 'yes' === $atts['show_register'] );
                ?>

                <?php if ( $show_lost_password_link || $show_register_link ) : ?>
                    <p class="register-links">
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
        <?php
        return ob_get_clean();
    }
}

if ( ! shortcode_exists( 'custom_login_form' ) ) {
    add_shortcode( 'custom_login_form', 'custom_login_form_shortcode_handler' );
}