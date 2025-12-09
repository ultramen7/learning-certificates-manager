<?php
/**
 * Plugin Name:       Learning Certificates Manager
 * Plugin URI:        https://github.com/ultramen7/learning-certificates-manager
 * Description:       Manage and display learning certificates grouped by institution with modern cards, tooltips, and image previews.
 * Version:           1.0.0
 * Author:            hazman
 * Author URI:        https://zman.my
 * Text Domain:       learning-certificates-manager
 * Domain Path:       /languages
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Plugin constants.
define( 'LCM_VERSION', '1.0.0' );
define( 'LCM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'LCM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Load plugin textdomain (for translations).
 */
function lcm_load_textdomain() {
    load_plugin_textdomain(
        'learning-certificates-manager',
        false,
        dirname( plugin_basename( __FILE__ ) ) . '/languages'
    );
}
add_action( 'plugins_loaded', 'lcm_load_textdomain' );

// Includes.
require_once LCM_PLUGIN_DIR . 'includes/class-lcm-cpt-taxonomy.php';
require_once LCM_PLUGIN_DIR . 'includes/class-lcm-meta-boxes.php';
require_once LCM_PLUGIN_DIR . 'includes/class-lcm-shortcode.php';

/**
 * Frontend assets (CSS).
 */
function lcm_enqueue_frontend_assets() {
    if ( is_admin() ) {
        return;
    }

    wp_enqueue_style(
        'lcm-frontend',
        LCM_PLUGIN_URL . 'assets/css/frontend.css',
        array(),
        LCM_VERSION
    );
}
add_action( 'wp_enqueue_scripts', 'lcm_enqueue_frontend_assets' );

/**
 * Admin assets (JS for media picker).
 */
function lcm_enqueue_admin_assets( $hook ) {
    global $post_type;

    if ( 'learning_certificate' !== $post_type ) {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_script(
        'lcm-admin-media',
        LCM_PLUGIN_URL . 'assets/js/admin-media.js',
        array( 'jquery' ),
        LCM_VERSION,
        true
    );
}
add_action( 'admin_enqueue_scripts', 'lcm_enqueue_admin_assets' );

