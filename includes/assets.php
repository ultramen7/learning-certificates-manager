<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue frontend styles for the certificates grid & tooltips.
 */
function lcm_enqueue_frontend_assets() {
    if ( ! is_admin() ) {
        wp_enqueue_style(
            'lcm-frontend',
            LCM_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            '1.0'
        );
    }
}
add_action( 'wp_enqueue_scripts', 'lcm_enqueue_frontend_assets' );
