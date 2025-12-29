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

/**
 * Add order field to certificate group add form
 */
function lcm_add_certificate_group_order_field() {
    ?>
    <div class="form-field">
        <label for="certificate_group_order">Order</label>
        <input type="number" name="certificate_group_order" id="certificate_group_order" value="" min="0" step="1" />
        <p>Enter a number to set custom sort order. Lower numbers appear first.</p>
    </div>
    <?php
}
add_action( 'certificate_group_add_form_fields', 'lcm_add_certificate_group_order_field' );

/**
 * Add order field to certificate group edit form
 */
function lcm_edit_certificate_group_order_field( $term ) {
    $order = get_term_meta( $term->term_id, 'certificate_group_order', true );
    ?>
    <tr class="form-field">
        <th scope="row"><label for="certificate_group_order">Order</label></th>
        <td>
            <input type="number" name="certificate_group_order" id="certificate_group_order" 
                   value="<?php echo esc_attr( $order ); ?>" min="0" step="1" />
            <p class="description">Enter a number to set custom sort order. Lower numbers appear first.</p>
        </td>
    </tr>
    <?php
}
add_action( 'certificate_group_edit_form_fields', 'lcm_edit_certificate_group_order_field' );

/**
 * Save certificate group order when term is created
 */
function lcm_save_certificate_group_order_create( $term_id ) {
    if ( isset( $_POST['certificate_group_order'] ) && '' !== $_POST['certificate_group_order'] ) {
        $order = absint( $_POST['certificate_group_order'] );
        update_term_meta( $term_id, 'certificate_group_order', $order );
    }
}
add_action( 'created_certificate_group', 'lcm_save_certificate_group_order_create' );

/**
 * Save certificate group order when term is edited
 */
function lcm_save_certificate_group_order_edit( $term_id ) {
    if ( isset( $_POST['certificate_group_order'] ) ) {
        if ( '' !== $_POST['certificate_group_order'] ) {
            $order = absint( $_POST['certificate_group_order'] );
            update_term_meta( $term_id, 'certificate_group_order', $order );
        } else {
            delete_term_meta( $term_id, 'certificate_group_order' );
        }
    }
}
add_action( 'edited_certificate_group', 'lcm_save_certificate_group_order_edit' );

/**
 * Add order column to certificate groups admin table
 */
function lcm_certificate_group_columns( $columns ) {
    // Insert order column after name
    $new_columns = array();
    foreach ( $columns as $key => $value ) {
        $new_columns[ $key ] = $value;
        if ( $key === 'name' ) {
            $new_columns['order'] = 'Order';
        }
    }
    return $new_columns;
}
add_filter( 'manage_edit-certificate_group_columns', 'lcm_certificate_group_columns' );

/**
 * Display order column content for certificate groups
 */
function lcm_certificate_group_column_content( $content, $column_name, $term_id ) {
    if ( $column_name === 'order' ) {
        $order = get_term_meta( $term_id, 'certificate_group_order', true );
        $content = $order ? esc_html( $order ) : '—';
    }
    return $content;
}
add_filter( 'manage_certificate_group_custom_column', 'lcm_certificate_group_column_content', 10, 3 );

/**
 * Make order column sortable for certificate groups
 */
function lcm_certificate_group_sortable_columns( $columns ) {
    $columns['order'] = 'order';
    return $columns;
}
add_filter( 'manage_edit-certificate_group_sortable_columns', 'lcm_certificate_group_sortable_columns' );
