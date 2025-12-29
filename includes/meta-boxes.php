<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Meta Box: Certificate File (with Media Library picker)
 */
function lcm_add_certificate_meta_box() {
    add_meta_box(
        'lcm_certificate_file',
        'Certificate File',
        'lcm_certificate_file_meta_box_callback',
        'learning_certificate',
        'normal',
        'default'
    );
}
add_action( 'add_meta_boxes', 'lcm_add_certificate_meta_box' );

/**
 * Move "Certificate Groups" meta box under the Certificate File meta box.
 */
function lcm_move_certificate_groups_metabox() {

    // 1) Remove the default taxonomy box from the right sidebar
    //    ID pattern is <taxonomy> . 'div'
    remove_meta_box(
        'certificate_groupdiv',      // meta box ID
        'learning_certificate',      // post type
        'side'                       // current context
    );

    // 2) Re-add it in the main column, *below* other boxes (priority 'low')
    add_meta_box(
        'certificate_groupdiv',                                     // same ID
        __('Certificate Groups', 'learning-certificates-manager'),  // title
        'post_categories_meta_box',                                // callback for hierarchical taxonomies
        'learning_certificate',                                    // post type
        'normal',                                                  // context: main column
        'low',                                                     // priority: lower than your file box
        array( 'taxonomy' => 'certificate_group' )                 // pass taxonomy
    );
}
add_action( 'add_meta_boxes', 'lcm_move_certificate_groups_metabox', 20 );


function lcm_certificate_file_meta_box_callback( $post ) {
    // Needed so we can use wp.media (Media Library popup)
    wp_enqueue_media();

    wp_nonce_field( 'lcm_save_certificate_file', 'lcm_certificate_file_nonce' );

    $file_url = get_post_meta( $post->ID, '_lcm_certificate_file_url', true );
    $certificate_order = get_post_meta( $post->ID, '_lcm_certificate_order', true );
    ?>
<p>
    <label for="lcm_certificate_file_url"><strong>Certificate File</strong></label><br>
    <input type="text" id="lcm_certificate_file_url" name="lcm_certificate_file_url"
        value="<?php echo esc_attr( $file_url ); ?>" style="width: 70%; max-width: 500px; margin-right: 6px;">
    <button type="button" class="button" id="lcm_select_certificate_button">
        Select from Media Library
    </button>
</p>
<p style="font-size: 12px; color: #666;">
    Click <strong>"Select from Media Library"</strong> to choose a file, or paste a file URL manually.
</p>

<p>
    <label for="lcm_certificate_order"><strong>Sort Order</strong></label><br>
    <input type="number" id="lcm_certificate_order" name="lcm_certificate_order"
        value="<?php echo esc_attr( $certificate_order ); ?>" style="width: 100px;" min="0" step="1">
</p>
<p style="font-size: 12px; color: #666;">
    Enter a number to set custom sort order. Lower numbers appear first. Leave empty for alphabetical sorting.
</p>

<script>
jQuery(document).ready(function($) {
    var lcm_frame;

    $('#lcm_select_certificate_button').on('click', function(e) {
        e.preventDefault();

        // If the media frame already exists, reopen it.
        if (lcm_frame) {
            lcm_frame.open();
            return;
        }

        // Create the media frame.
        lcm_frame = wp.media({
            title: 'Select or Upload Certificate',
            button: {
                text: 'Use this file'
            },
            multiple: false // Only one file
        });

        // When a file is selected, run a callback.
        lcm_frame.on('select', function() {
            var attachment = lcm_frame.state().get('selection').first().toJSON();
            $('#lcm_certificate_file_url').val(attachment.url);
        });

        // Finally, open the modal
        lcm_frame.open();
    });
});
</script>
<?php
}

function lcm_save_certificate_file_meta( $post_id ) {
    // Security: Check nonce
    if ( ! isset( $_POST['lcm_certificate_file_nonce'] ) ) {
        return;
    }

    if ( ! wp_verify_nonce( $_POST['lcm_certificate_file_nonce'], 'lcm_save_certificate_file' ) ) {
        return;
    }

    // Stop during autosaves
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Permission check
    if ( isset( $_POST['post_type'] ) && 'learning_certificate' === $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    // Save URL
    if ( isset( $_POST['lcm_certificate_file_url'] ) ) {
        $url = sanitize_text_field( $_POST['lcm_certificate_file_url'] );
        update_post_meta( $post_id, '_lcm_certificate_file_url', $url );
    }

    // Save order
    if ( isset( $_POST['lcm_certificate_order'] ) ) {
        $order = absint( $_POST['lcm_certificate_order'] );
        update_post_meta( $post_id, '_lcm_certificate_order', $order );
    } else {
        delete_post_meta( $post_id, '_lcm_certificate_order' );
    }
}
add_action( 'save_post', 'lcm_save_certificate_file_meta' );

/**
 * Add custom columns to certificates admin list
 */
function lcm_certificate_custom_columns( $columns ) {
    // Insert order column after title
    $new_columns = array();
    foreach ( $columns as $key => $value ) {
        $new_columns[ $key ] = $value;
        if ( $key === 'title' ) {
            $new_columns['lcm_order'] = 'Order';
        }
    }
    return $new_columns;
}
add_filter( 'manage_learning_certificate_posts_columns', 'lcm_certificate_custom_columns' );

/**
 * Display order column content
 */
function lcm_certificate_custom_column_content( $column, $post_id ) {
    if ( $column === 'lcm_order' ) {
        $order = get_post_meta( $post_id, '_lcm_certificate_order', true );
        echo $order ? esc_html( $order ) : '—';
    }
}
add_action( 'manage_learning_certificate_posts_custom_column', 'lcm_certificate_custom_column_content', 10, 2 );

/**
 * Make order column sortable
 */
function lcm_certificate_sortable_columns( $columns ) {
    $columns['lcm_order'] = 'lcm_order';
    return $columns;
}
add_filter( 'manage_edit-learning_certificate_sortable_columns', 'lcm_certificate_sortable_columns' );

/**
 * Custom orderby for order column
 */
function lcm_certificate_orderby( $query ) {
    if ( ! is_admin() || ! $query->is_main_query() ) {
        return;
    }

    $orderby = $query->get( 'orderby' );
    if ( 'lcm_order' === $orderby ) {
        $query->set( 'meta_key', '_lcm_certificate_order' );
        $query->set( 'orderby', 'meta_value_num' );
    }
}
add_action( 'pre_get_posts', 'lcm_certificate_orderby' );