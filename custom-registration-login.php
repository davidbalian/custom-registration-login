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
require_once plugin_dir_path( __FILE__ ) . 'couple-registration.php';
require_once plugin_dir_path( __FILE__ ) . 'vendor-registration.php';