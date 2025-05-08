<?php
/**
 * Plugin Name: Custom Registration and Login
 * Description: Adds a custom login page template and other custom registration/login functionalities.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: Your Website
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: custom-reg-login
 */

// Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Adds the custom login page template to the list of available page templates.
 *
 * This function is hooked into 'theme_page_templates' for WordPress 4.7+
 * to make the template selectable in the Page Attributes meta box.
 *
 * @param array $templates Array of page templates. Keys are filenames, values are display names.
 * @return array Modified array of page templates.
 */
function crl_add_custom_login_template( $templates ) {
    $templates['templates/template-custom-login.php'] = __( 'Simple Custom Login Page', 'custom-reg-login' );
    return $templates;
}
// For WordPress 4.7+ (Preferred method)
add_filter( 'theme_page_templates', 'crl_add_custom_login_template' );
// Fallback for older versions (though less common to need now)
// add_filter( 'page_attributes_dropdown_pages_args', 'crl_register_project_templates' ); // This hook is slightly different
// add_filter( 'wp_insert_post_data', 'crl_register_project_templates' ); // This hook is slightly different


/**
 * Loads the custom login page template from the plugin directory.
 *
 * This function is hooked into 'template_include'. If a page is using
 * our custom template, it ensures the template file is loaded from the plugin's
 * 'templates' directory rather than the theme's directory.
 *
 * @param string $template The path of the template to include.
 * @return string The path of the template to include.
 */
function crl_load_custom_login_template( $template ) {
    // Get the current post data (if available)
    global $post;

    // If $post is not set (e.g., on some admin screens or non-singular views), bail.
    if ( ! isset( $post ) ) {
        return $template;
    }

    // Get the page template meta for the current post.
    $page_template = get_post_meta( $post->ID, '_wp_page_template', true );

    // Check if our custom template is selected for the current page.
    if ( 'templates/template-custom-login.php' === $page_template ) {
        $plugin_template_path = plugin_dir_path( __FILE__ ) . 'templates/template-custom-login.php';

        // Check if the template file exists in the plugin directory.
        if ( file_exists( $plugin_template_path ) ) {
            return $plugin_template_path; // Load our template
        }
    }

    return $template; // Otherwise, return the original template
}
add_filter( 'template_include', 'crl_load_custom_login_template' );

?> 