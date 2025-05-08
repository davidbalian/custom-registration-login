<?php
/**
 * Vendor Registration Functionality
 *
 * Handles the display and submission of the custom vendor registration form.
 * Registers users with the 'vendor' role and uses URL parameters for messages.
 * Includes optional AJAX implementation.
 *
 * @package CustomRegistrationLogin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Shortcode to display a custom registration form for vendors.
 * Messages (errors/success) are read from URL parameters.
 *
 * @param array $atts Shortcode attributes (not used in this implementation).
 * @return string HTML output of the vendor registration form.
 */
function vendor_registration_form_shortcode( $atts ) {
    // Optional: Check if user is already logged in. If so, redirect.
    /*
    if ( is_user_logged_in() ) {
        // Redirect to the vendor dashboard or profile page.
        $current_user = wp_get_current_user();
        // You might use your custom_get_redirect_url_for_user function here
        // if you want the vendor dashboard redirect to match your login redirect logic.
        $redirect_url = home_url( '/vendor-dashboard' ); // Default vendor dashboard slug
        wp_safe_redirect( esc_url_raw( $redirect_url ) );
        exit;
    }
    */

    // Start output buffering to capture HTML output.
    ob_start();

    // Check for registration status and messages in URL parameters after redirect
    $registration_status = isset( $_GET['registration_status'] ) ? sanitize_key( $_GET['registration_status'] ) : '';
    // Decode the message string from URL and sanitize; use rawurldecode for potential '+' signs
    $message_string      = isset( $_GET['messages'] ) ? sanitize_text_field( rawurldecode( $_GET['messages'] ) ) : '';

    // Display introductory text and any messages.
    ?>
    <p><?php esc_html_e( 'Please complete the form below to register as a vendor, or', 'custom-registration-login' ); ?> <a href="<?php echo esc_url( wp_login_url() ); ?>"><?php esc_html_e( 'login as a user', 'custom-registration-login' ); ?></a> <?php esc_html_e( 'or', 'custom-registration-login' ); ?> <a href="<?php echo esc_url( home_url( '/couple-login' ) ); ?>"><?php esc_html_e( 'couple', 'custom-registration-login' ); ?></a>.</p>

    <?php if ( ! empty( $registration_status ) && ! empty( $message_string ) ) : ?>
        <div class="<?php echo esc_attr( $registration_status === 'success' ? 'success' : 'error' ); ?>">
            <?php
            // If multiple error messages were joined by '||', split them
            if ( $registration_status === 'error' && strpos( $message_string, '||' ) !== false ) {
                $messages = explode( '||', $message_string );
                echo '<ul class="registration-messages">';
                foreach ( $messages as $msg ) {
                    echo '<li>' . wp_kses_post( $msg ) . '</li>'; // Sanitize each message part
                }
                echo '</ul>';
            } else {
                // Single message (success or error)
                echo wp_kses_post( $message_string ); // Sanitize the single message
            }
            ?>
        </div>
    <?php endif; ?>

    <form id="vendor-registration-form" method="post" action=""> <?php // Leave action empty for same-page submission ?>
        <?php
        // Optionally repopulate fields on error by reading from URL parameters
        $default_username = isset( $_GET['username'] ) ? esc_attr( rawurldecode( sanitize_user( $_GET['username'] ) ) ) : '';
        $default_first    = isset( $_GET['first_name'] ) ? esc_attr( rawurldecode( sanitize_text_field( $_GET['first_name'] ) ) ) : '';
        $default_last     = isset( $_GET['last_name'] ) ? esc_attr( rawurldecode( sanitize_text_field( $_GET['last_name'] ) ) ) : '';
        $default_email    = isset( $_GET['email'] ) ? esc_attr( rawurldecode( sanitize_email( $_GET['email'] ) ) ) : '';
        // Assuming business_name might be added to the form later
        // $default_business = isset( $_GET['business_name'] ) ? esc_attr( rawurldecode( sanitize_text_field( $_GET['business_name'] ) ) ) : '';
        ?>
        <input class="text-input default_field_username" required name="username" maxlength="70" type="text" id="username" value="<?php echo $default_username; ?>" placeholder="<?php esc_attr_e( 'Username*', 'custom-registration-login' ); ?>">
        <input class="text-input default_field_firstname" required name="first_name" maxlength="70" type="text" id="first_name" value="<?php echo $default_first; ?>" placeholder="<?php esc_attr_e( 'First Name*', 'custom-registration-login' ); ?>">
        <input class="text-input default_field_lastname" required name="last_name" maxlength="70" type="text" id="last_name" value="<?php echo $default_last; ?>" placeholder="<?php esc_attr_e( 'Last Name*', 'custom-registration-login' ); ?>">
        <input class="text-input default_field_email" required name="email" maxlength="70" type="email" id="email" value="<?php echo $default_email; ?>" placeholder="<?php esc_attr_e( 'E-mail*', 'custom-registration-login' ); ?>">
        <input class="text-input default_field_password" required name="password" type="password" id="password" placeholder="<?php esc_attr_e( 'Password*', 'custom-registration-login' ); ?>">
        <?php /* If adding business_name field: ?>
        <input class="text-input default_field_businessname" name="business_name" maxlength="70" type="text" id="business_name" value="<?php echo $default_business; ?>" placeholder="<?php esc_attr_e( 'Business Name', 'custom-registration-login' ); ?>">
        <?php */ ?>
        <label class="user_consent_gdbr_wrapper" for="user_consent_gdpr">
            <input required value="agree" name="user_consent_gdpr" id="user_consent_gdpr" type="checkbox">
            <span><?php printf( esc_html__( 'I allow the website to collect and store the data I submit through this form.%s', 'custom-registration-login' ), '<span title="' . esc_attr__( 'This field is required', 'custom-registration-login' ) . '">*</span>' ); ?></span>
        </label>
        <?php wp_nonce_field( 'vendor_registration_nonce', 'vendor_registration_nonce_field' ); ?>
        <input name="register_vendor" type="submit" id="register_vendor" class="submit button" value="<?php esc_attr_e( 'Register as Vendor', 'custom-registration-login' ); ?>">
    </form>
     <p><?php printf( esc_html__( 'For couple registration, please visit the %s page.', 'custom-registration-login' ), '<a href="' . esc_url( home_url( '/register' ) ) . '">' . esc_html__( 'Couple Registration', 'custom-registration-login' ) . '</a>' ); ?></p>

    <?php // Container for AJAX messages - keep if you plan to use AJAX ?>
    <div id="vendor-registration-message-container" style="display:none;"></div>
    <?php
    // Get the captured HTML output.
    $output = ob_get_clean();
    return $output;
}
// Register the shortcode.
add_shortcode( 'vendor_registration_form', 'vendor_registration_form_shortcode' );

/**
 * Function to handle the vendor registration form submission via standard POST.
 * Uses URL parameters to pass messages after redirect.
 */
function handle_vendor_registration_form() {
    // Check if the form was submitted via POST.
    // Ensure this only runs on pages where the form is expected.
    if ( isset( $_POST['register_vendor'] ) ) {

        // Verify the nonce.
        if ( ! isset( $_POST['vendor_registration_nonce_field'] ) || ! wp_verify_nonce( $_POST['vendor_registration_nonce_field'], 'vendor_registration_nonce' ) ) {
            // Log the nonce failure attempt for security
            error_log( 'Vendor registration nonce verification failed for IP: ' . $_SERVER['REMOTE_ADDR'] );
            wp_die( esc_html__( 'Invalid security token. Please try again.', 'custom-registration-login' ), '', array( 'response' => 403 ) ); // Provide a user-friendly error
        }

        // Sanitize and validate user input.
        $username       = sanitize_user( $_POST['username'] );
        $first_name     = sanitize_text_field( $_POST['first_name'] );
        $last_name      = sanitize_text_field( $_POST['last_name'] );
        $email          = sanitize_email( $_POST['email'] );
        $password       = $_POST['password']; // Do not sanitize password here, it will be hashed by wp_insert_user
        $gdpr_consent   = isset( $_POST['user_consent_gdpr'] ) ? sanitize_text_field( $_POST['user_consent_gdpr'] ) : '';
        // Assuming business_name might be added to the form later
        $business_name = isset( $_POST['business_name'] ) ? sanitize_text_field( $_POST['business_name'] ) : '';


        $errors = new WP_Error();

        // Perform input validation.
        if ( empty( $username ) ) {
            $errors->add( 'empty_username', esc_html__( 'Username is required.', 'custom-registration-login' ) );
        } elseif ( username_exists( $username ) ) {
            $errors->add( 'username_exists', esc_html__( 'Username already exists.', 'custom-registration-login' ) );
        }
        if ( empty( $first_name ) ) {
            $errors->add( 'empty_firstname', esc_html__( 'First name is required.', 'custom-registration-login' ) );
        }
        if ( empty( $last_name ) ) {
            $errors->add( 'empty_lastname', esc_html__( 'Last name is required.', 'custom-registration-login' ) );
        }
        if ( empty( $email ) ) {
            $errors->add( 'empty_email', esc_html__( 'Email is required.', 'custom-registration-login' ) );
        } elseif ( ! is_email( $email ) ) {
            $errors->add( 'invalid_email', esc_html__( 'Invalid email address.', 'custom-registration-login' ) );
        } elseif ( email_exists( $email ) ) {
            $errors->add( 'email_exists', esc_html__( 'Email address is already in use.', 'custom-registration-login' ) );
        }
        if ( empty( $password ) ) {
            $errors->add( 'empty_password', esc_html__( 'Password is required.', 'custom-registration-login' ) );
        }
        if ( $gdpr_consent != 'agree' ) {
            $errors->add( 'empty_gdpr', esc_html__( 'You must consent to the storage of your data.', 'custom-registration-login' ) );
        }

        // If there are any validation errors, redirect back with errors in URL.
        if ( $errors->has_errors() ) {
            $error_messages = $errors->get_error_messages();
            // Implode error messages into a string for the URL parameter
            $error_string = implode( '||', $error_messages ); // Use a delimiter not expected in messages

            // Build the redirect URL with parameters
            $redirect_url = add_query_arg(
                array(
                    'registration_status' => 'error',
                    'messages'            => urlencode( $error_string ), // Encode the message string for URL
                    'username'            => urlencode( $username ), // Optionally pass back valid fields to repopulate
                    'first_name'          => urlencode( $first_name ),
                    'last_name'           => urlencode( $last_name ),
                    'email'               => urlencode( $email ),
                    // Do NOT pass back the password!
                     'business_name'      => urlencode( $business_name ), // Pass back business name
                ),
                 // Redirect back to the same page the form was submitted from
                 esc_url_raw( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( $_SERVER['HTTP_REFERER'] ) : esc_url_raw( home_url( '/vendor-registration-page-slug' ) ) // Fallback if HTTP_REFERER is empty
            );

            wp_safe_redirect( $redirect_url );
            exit;
        }

        // If no validation errors, proceed to create the user.
        $user_data = array(
            'user_login'    => $username,
            'user_pass'     => $password,
            'user_email'    => $email,
            'first_name'    => $first_name,
            'last_name'     => $last_name,
            'role'          => 'vendor', // Set the user role to 'vendor'.
        );

        $user_id = wp_insert_user( $user_data );

        // Handle user creation errors from wp_insert_user.
        if ( is_wp_error( $user_id ) ) {
            $error_message = $user_id->get_error_message();
             // Build the redirect URL with creation error message
             $redirect_url = add_query_arg(
                array(
                    'registration_status' => 'error',
                    'messages'            => urlencode( $error_message ),
                    // Might pass back original inputs, except password
                     'username'            => urlencode( $username ),
                    'first_name'          => urlencode( $first_name ),
                    'last_name'           => urlencode( $last_name ),
                    'email'               => urlencode( $email ),
                     'business_name'      => urlencode( $business_name ), // Pass back business name
                ),
                 // Redirect back to the same page or a fallback
                 esc_url_raw( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( $_SERVER['HTTP_REFERER'] ) : esc_url_raw( home_url( '/vendor-registration-page-slug' ) ) // Fallback
            );
            wp_safe_redirect( $redirect_url );
            exit;
        }

        // Store GDPR consent and business name as user meta.
        update_user_meta( $user_id, 'gdpr_consent', $gdpr_consent );
        update_user_meta( $user_id, 'gdpr_consent_timestamp', current_time( 'timestamp' ) );
         if ( ! empty( $business_name ) ) { // Only update if business_name was provided (assuming it's optional)
            update_user_meta( $user_id, 'business_name', $business_name );
        }


        // Send email notification to the new user.
        // The third argument 'both' sends email to site admin and new user.
        wp_new_user_notification( $user_id, null, 'both' );


        // Redirect on successful registration.
        $success_message = esc_html__( 'Registration successful! Please check your email for login details.', 'custom-registration-login' );
        $success_redirect_url = add_query_arg(
             array(
                 'registration_status' => 'success',
                 'messages' => urlencode($success_message) // Pass success message in URL too
            ),
             home_url( '/vendor-dashboard' ) // Redirect to the vendor dashboard slug
         );

        wp_safe_redirect( esc_url_raw( $success_redirect_url ) );
        exit;
    }
}
// Hook the form handling function early in the WordPress load process.
add_action( 'init', 'handle_vendor_registration_form' );


// --- START AJAX Implementation for Vendor Registration ---
// Keep this section if you plan to use AJAX for form submission.
// Note: The AJAX handler below does NOT use $_SESSION.

add_action( 'wp_enqueue_scripts', 'enqueue_vendor_registration_scripts' );
/**
 * Enqueue scripts for vendor registration AJAX form.
 */
function enqueue_vendor_registration_scripts() {
    // Assuming your JS file is in your child theme's js directory
    if ( file_exists( get_stylesheet_directory() . '/js/vendor-registration.js' ) ) {
         wp_enqueue_script( 'vendor-registration-ajax', get_stylesheet_directory_uri() . '/js/vendor-registration.js', array( 'jquery' ), '1.0', true );
         wp_localize_script( 'vendor-registration-ajax', 'vendorRegistrationAjax', array(
             'ajaxurl' => admin_url( 'admin-ajax.php' ),
             'nonce'   => wp_create_nonce( 'vendor_registration_ajax_nonce' ),
         ) );
    }
}

add_action( 'wp_ajax_vendor_register', 'handle_vendor_registration_ajax' );
add_action( 'wp_ajax_nopriv_vendor_register', 'handle_vendor_registration_ajax' ); // Allows non-logged-in users to submit AJAX

/**
 * Handle the vendor registration form submission via AJAX.
 */
function handle_vendor_registration_ajax() {
    // Verify nonce
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'vendor_registration_ajax_nonce' ) ) {
        wp_send_json_error( esc_html__( 'Invalid security token. Please try again.', 'custom-registration-login' ) );
        wp_die(); // Always include wp_die() in AJAX handlers
    }

    // Sanitize and validate data
    $username       = sanitize_user( $_POST['username'] );
    $first_name     = sanitize_text_field( $_POST['first_name'] );
    $last_name      = sanitize_text_field( $_POST['last_name'] );
    $email          = sanitize_email( $_POST['email'] );
    $password       = $_POST['password']; // Do not sanitize password here
    $gdpr_consent   = isset( $_POST['user_consent_gdpr'] ) ? sanitize_text_field( $_POST['user_consent_gdpr'] ) : '';
    $business_name  = isset( $_POST['business_name'] ) ? sanitize_text_field( $_POST['business_name'] ) : ''; // Assuming you might add this later

    $errors = new WP_Error();
    if ( empty( $username ) ) {
        $errors->add( 'empty_username', esc_html__( 'Username is required.', 'custom-registration-login' ) );
    } elseif ( username_exists( $username ) ) {
        $errors->add( 'username_exists', esc_html__( 'Username already exists.', 'custom-registration-login' ) );
    }
    if ( empty( $first_name ) ) {
        $errors->add( 'empty_firstname', esc_html__( 'First name is required.', 'custom-registration-login' ) );
    }
    if ( empty( $last_name ) ) {
        $errors->add( 'empty_lastname', esc_html__( 'Last name is required.', 'custom-registration-login' ) );
    }
    if ( empty( $email ) ) {
        $errors->add( 'empty_email', esc_html__( 'Email is required.', 'custom-registration-login' ) );
    } elseif ( ! is_email( $email ) ) {
        $errors->add( 'invalid_email', esc_html__( 'Invalid email address.', 'custom-registration-login' ) );
    } elseif ( email_exists( $email ) ) {
        $errors->add( 'email_exists', esc_html__( 'Email address is already in use.', 'custom-registration-login' ) );
    }
    if ( empty( $password ) ) {
        $errors->add( 'empty_password', esc_html__( 'Password is required.', 'custom-registration-login' ) );
    }
     if ( $gdpr_consent != 'agree' ) {
        $errors->add( 'empty_gdpr', esc_html__( 'You must consent to the storage of your data.', 'custom-registration-login' ) );
    }


    if ( $errors->has_errors() ) {
        // Send error messages back as JSON
        wp_send_json_error( $errors->get_error_messages() );
        wp_die();
    }

    // Create user
    $user_data = array(
        'user_login'    => $username,
        'user_pass'     => $password,
        'user_email'    => $email,
        'first_name'    => $first_name,
        'last_name'     => $last_name,
        'role'          => 'vendor', // Set the user role to 'vendor'.
    );

    $user_id = wp_insert_user( $user_data );

    if ( is_wp_error( $user_id ) ) {
        wp_send_json_error( $user_id->get_error_message() );
        wp_die();
    }

    // Store GDPR consent and business name
    update_user_meta( $user_id, 'gdpr_consent', $gdpr_consent );
    update_user_meta( $user_id, 'gdpr_consent_timestamp', current_time( 'timestamp' ) );
    if ( ! empty( $business_name ) ) { // Only update if business_name was provided (assuming it's optional)
        update_user_meta( $user_id, 'business_name', $business_name );
    }


    // Send email notification
    wp_new_user_notification( $user_id, null, 'both' );

    // Prepare success message and redirect URL for AJAX
    $success_message = esc_html__( 'Registration successful! Redirecting to vendor dashboard', 'custom-registration-login' );
    $redirect_url = home_url( '/vendor-dashboard' ); // URL to redirect to after successful AJAX registration slug


    // Send success response as JSON
    wp_send_json_success( array(
        'message' => $success_message,
        'redirect_url' => $redirect_url
    ) );
    wp_die();
}
// --- END AJAX Implementation ---