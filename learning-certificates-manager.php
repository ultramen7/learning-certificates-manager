<?php
/**
 * Plugin Name:       Learning Certificates Manager
 * Plugin URI:        https://github.com/ultramen7/learning-certificates-manager
 * Description:       Manage and display learning certificates grouped by institution with modern cards, tooltips, and image previews. use [learning_certificates] in page or post
 * Version:           1.0.0
 * Author:            hazman
 * Author URI:        https://zman.my
 * Text Domain:       learning-certificates-manager
 * Domain Path:       /languages
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define plugin directory & URL constants
if ( ! defined( 'LCM_PLUGIN_DIR' ) ) {
    define( 'LCM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'LCM_PLUGIN_URL' ) ) {
    define( 'LCM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

// Include separate files
require_once LCM_PLUGIN_DIR . 'includes/cpt-taxonomy.php';
require_once LCM_PLUGIN_DIR . 'includes/meta-boxes.php';
require_once LCM_PLUGIN_DIR . 'includes/shortcode-display.php';
require_once LCM_PLUGIN_DIR . 'includes/assets.php';
