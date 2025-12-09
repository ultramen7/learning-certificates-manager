<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LCM_Meta_Boxes {

    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_certificate_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_certificate_file_meta' ) );
    }

    public function add_certificate_meta_box() {
        add_meta_box(
            'lcm_certificate_file',
            __( 'Certificate File', 'learning-certificates-manager' ),
            array( $this, 'certificate_file_meta_box_callback' ),
            'learning_certificate',
            'normal',
            'default'
        );
    }

    public function certificate_file_meta_box_callback( $post ) {
        wp_nonce_field( 'lcm_save_certificate_file', 'lcm_certificate_file_nonce' );

        $file_url = get_post_meta( $post->ID, '_lcm_certificate_file_url', true );
        ?>
        <p>
            <label for="lcm_certificate_file_url">
                <strong><?php esc_html_e( 'Certificate File URL', 'learning-certificates-manager' ); ?></strong>
            </label><br />
            <input type="text"
                   id="lcm_certificate_file_url"
                   name="lcm_certificate_file_url"
                   value="<?php echo esc_attr( $file_url ); ?>"
                   style="width: 70%; max-width: 500px; margin-right: 6px;" />
            <button type="button" class="button" id="lcm_select_certificate_button">
                <?php esc_html_e( 'Select from Media Library', 'learning-certificates-manager' ); ?>
            </button>
        </p>
        <p style="font-size: 12px; color: #666;">
            <?php esc_html_e( 'Click "Select from Media Library" to choose a file, or paste a file URL manually.', 'learning-certificates-manager' ); ?>
        </p>
        <?php
    }

    public function save_certificate_file_meta( $post_id ) {
        if ( ! isset( $_POST['lcm_certificate_file_nonce'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( $_POST['lcm_certificate_file_nonce'], 'lcm_save_certificate_file' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( isset( $_POST['post_type'] ) && 'learning_certificate' === $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }

        if ( isset( $_POST['lcm_certificate_file_url'] ) ) {
            $url = esc_url_raw( wp_unslash( $_POST['lcm_certificate_file_url'] ) );
            update_post_meta( $post_id, '_lcm_certificate_file_url', $url );
        }
    }
}

new LCM_Meta_Boxes();

