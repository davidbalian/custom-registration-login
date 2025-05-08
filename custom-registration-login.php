<?php
/**
 * Plugin Name: Custom Registration & Login
 * Description: Adds custom registration and login functionalities.
 * Version: 1.0
 * Author: Your Name
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Include the custom login form shortcode.
require_once plugin_dir_path( __FILE__ ) . 'login.php';

// ... existing code ...
// Add other plugin functionalities below, if any.

// Example: If you have a registration shortcode in another file, you might include it like this:
// require_once plugin_dir_path( __FILE__ ) . 'registration.php';

// Example: Enqueue scripts or styles
/*
function crl_enqueue_scripts() {
    wp_enqueue_style( 'crl-styles', plugin_dir_url( __FILE__ ) . 'css/style.css' );
    wp_enqueue_script( 'crl-scripts', plugin_dir_url( __FILE__ ) . 'js/main.js', array( 'jquery' ), '1.0', true );
}
add_action( 'wp_enqueue_scripts', 'crl_enqueue_scripts' );
*/

// Example: Activation/Deactivation hooks
/*
function crl_activate() {
    // Code to run on plugin activation
}
register_activation_hook( __FILE__, 'crl_activate' );

function crl_deactivate() {
    // Code to run on plugin deactivation
}
register_deactivation_hook( __FILE__, 'crl_deactivate' );
*/

?>
