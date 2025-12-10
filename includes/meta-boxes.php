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

function lcm_certificate_file_meta_box_callback( $post ) {
    // Needed so we can use wp.media (Media Library popup)
    wp_enqueue_media();

    wp_nonce_field( 'lcm_save_certificate_file', 'lcm_certificate_file_nonce' );

    $file_url = get_post_meta( $post->ID, '_lcm_certificate_file_url', true );
    ?>
    <p>
        <label for="lcm_certificate_file_url"><strong>Certificate File</strong></label><br>
        <input type="text"
               id="lcm_certificate_file_url"
               name="lcm_certificate_file_url"
               value="<?php echo esc_attr( $file_url ); ?>"
               style="width: 70%; max-width: 500px; margin-right: 6px;">
        <button type="button" class="button" id="lcm_select_certificate_button">
            Select from Media Library
        </button>
    </p>
    <p style="font-size: 12px; color: #666;">
        Click <strong>"Select from Media Library"</strong> to choose a file, or paste a file URL manually.
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
}
add_action( 'save_post', 'lcm_save_certificate_file_meta' );
