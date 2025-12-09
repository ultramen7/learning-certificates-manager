<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class LCM_Shortcode {

    public function __construct() {
        add_shortcode( 'learning_certificates', array( $this, 'render_learning_certificates' ) );
    }

    public function render_learning_certificates( $atts ) {
        $atts = shortcode_atts(
            array(
                'order'   => 'ASC',
                'orderby' => 'name',
            ),
            $atts,
            'learning_certificates'
        );

        $groups = get_terms(
            array(
                'taxonomy'   => 'certificate_group',
                'hide_empty' => false,
                'orderby'    => $atts['orderby'],
                'order'      => $atts['order'],
            )
        );

        if ( empty( $groups ) || is_wp_error( $groups ) ) {
            return '<p>' . esc_html__( 'No certificate groups found.', 'learning-certificates-manager' ) . '</p>';
        }

        ob_start();

        echo '<div class="lcm-cert-grid">';

        foreach ( $groups as $group ) {
            $cert_query = new WP_Query(
                array(
                    'post_type'      => 'learning_certificate',
                    'posts_per_page' => -1,
                    'tax_query'      => array(
                        array(
                            'taxonomy' => 'certificate_group',
                            'field'    => 'term_id',
                            'terms'    => $group->term_id,
                        ),
                    ),
                    'orderby'        => 'title',
                    'order'          => 'ASC',
                )
            );

            if ( $cert_query->have_posts() ) {
                echo '<div class="lcm-cert-group-card">';
                echo '<div class="lcm-cert-group-header">';
                echo '<h3 class="lcm-cert-group-title">' . esc_html( $group->name ) . '</h3>';
                echo '</div>';

                echo '<ul class="lcm-cert-list">';

                while ( $cert_query->have_posts() ) {
                    $cert_query->the_post();

                    $file_url    = get_post_meta( get_the_ID(), '_lcm_certificate_file_url', true );
                    $title       = get_the_title();
                    $raw_content = get_post_field( 'post_content', get_the_ID() );
                    $description = trim( wp_strip_all_tags( $raw_content ) );

                    if ( strlen( $description ) > 220 ) {
                        $description = mb_substr( $description, 0, 217 ) . '...';
                    }

                    $is_image = false;
                    if ( $file_url ) {
                        $path = parse_url( $file_url, PHP_URL_PATH );
                        if ( $path && preg_match( '/\.(jpe?g|png|gif|webp)$/i', $path ) ) {
                            $is_image = true;
                        }
                    }

                    $tooltip_attr = '';
                    if ( ! $is_image && $description ) {
                        $tooltip_attr = ' data-lcm-tooltip="' . esc_attr( $description ) . '"';
                    }

                    echo '<li class="lcm-cert-item">';

                    if ( $file_url ) {
                        echo '<a class="lcm-cert-link" href="' . esc_url( $file_url ) . '" target="_blank" rel="noopener noreferrer"' . $tooltip_attr . '>';
                        echo '<span class="lcm-cert-title">' . esc_html( $title ) . '</span>';
                        echo '<span class="lcm-cert-badge">' . esc_html__( 'View', 'learning-certificates-manager' ) . '</span>';

                        if ( $is_image ) {
                            echo '<span class="lcm-cert-thumb">';
                            echo '<img src="' . esc_url( $file_url ) . '" alt="' . esc_attr( $title ) . '" />';
                            if ( $description ) {
                                echo '<span class="lcm-cert-thumb-caption">' . esc_html( $description ) . '</span>';
                            }
                            echo '</span>';
                        }

                        echo '</a>';
                    } else {
                        echo '<span class="lcm-cert-link lcm-cert-link--no-file"' . $tooltip_attr . '>';
                        echo '<span class="lcm-cert-title">' . esc_html( $title ) . '</span>';
                        echo '</span>';
                    }

                    echo '</li>';
                }

                echo '</ul>';
                echo '</div>';
            }

            wp_reset_postdata();
        }

        echo '</div>';

        return ob_get_clean();
    }
}

new LCM_Shortcode();

