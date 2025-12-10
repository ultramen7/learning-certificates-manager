<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Custom Post Type (Certificates)
 * We use the post content as the "description" for tooltip.
 */
function lcm_register_certificate_cpt() {
    $labels = array(
        'name'               => 'Certificates',
        'singular_name'      => 'Certificate',
        'add_new'            => 'Add New Certificate',
        'add_new_item'       => 'Add New Certificate',
        'edit_item'          => 'Edit Certificate',
        'new_item'           => 'New Certificate',
        'view_item'          => 'View Certificate',
        'search_items'       => 'Search Certificates',
        'not_found'          => 'No certificates found',
        'not_found_in_trash' => 'No certificates found in Trash',
        'menu_name'          => 'Certificates',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'supports'           => array( 'title', 'editor' ), // editor = description
        'has_archive'        => false,
        'menu_position'      => 20,
        'menu_icon'          => 'dashicons-awards',
    );

    register_post_type( 'learning_certificate', $args );
}
add_action( 'init', 'lcm_register_certificate_cpt' );

/**
 * Register Taxonomy (Certificate Group)
 */
function lcm_register_certificate_group_taxonomy() {
    $labels = array(
        'name'              => 'Certificate Groups',
        'singular_name'     => 'Certificate Group',
        'search_items'      => 'Search Groups',
        'all_items'         => 'All Groups',
        'edit_item'         => 'Edit Group',
        'update_item'       => 'Update Group',
        'add_new_item'      => 'Add New Group',
        'new_item_name'     => 'New Group Name',
        'menu_name'         => 'Certificate Groups',
    );

    $args = array(
        'hierarchical'      => true, // behaves like categories
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
    );

    register_taxonomy( 'certificate_group', array( 'learning_certificate' ), $args );
}
add_action( 'init', 'lcm_register_certificate_group_taxonomy' );
