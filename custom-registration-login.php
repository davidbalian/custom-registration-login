<?php
/**
 * Plugin Name: Custom Registration and Login
 * Description: Custom login, registration, and redirection functionality for MomentMoi.
 * Version: 1.0
 * Author: David Balian
 * License: GPL-2.0-or-later
 * Text Domain: momentmoi-custom-login
 *
 * @package MomentMoi
 */

// Ensure this file is not accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Your custom code will go below this line

// require_once plugin_dir_path( __FILE__ ) . 'redirect-default-login.php';
require_once plugin_dir_path( __FILE__ ) . 'login.php';
require_once plugin_dir_path( __FILE__ ) . 'couple-registration.php';
require_once plugin_dir_path( __FILE__ ) . 'vendor-registration.php';