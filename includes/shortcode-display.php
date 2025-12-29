<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Shortcode: [learning_certificates]
 * Modern card layout + inline CSS + tooltip on hover + entrance animation.
 * If the file is an image, show an image thumbnail tooltip on hover.
 */
function lcm_learning_certificates_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'order'      => 'ASC',
        'orderby'    => 'name',
    ), $atts, 'learning_certificates' );

    // Get all groups
    $groups = get_terms( array(
        'taxonomy'   => 'certificate_group',
        'hide_empty' => false,
    ) );

    if ( empty( $groups ) || is_wp_error( $groups ) ) {
        return '<p>No certificate groups found.</p>';
    }

    // Check if any group has custom order set
    $has_custom_order = false;
    foreach ( $groups as $group ) {
        $order = get_term_meta( $group->term_id, 'certificate_group_order', true );
        if ( $order !== '' ) {
            $has_custom_order = true;
            break;
        }
    }

    // Sort groups by custom order if available, otherwise by name
    if ( $has_custom_order ) {
        usort( $groups, function( $a, $b ) {
            $order_a = get_term_meta( $a->term_id, 'certificate_group_order', true );
            $order_b = get_term_meta( $b->term_id, 'certificate_group_order', true );
            
            // If both have no order, sort by name
            if ( $order_a === '' && $order_b === '' ) {
                return strcmp( $a->name, $b->name );
            }
            // If only one has no order, it goes after
            if ( $order_a === '' ) {
                return 1;
            }
            if ( $order_b === '' ) {
                return -1;
            }
            // Both have order, compare numerically
            if ( $order_a == $order_b ) {
                return strcmp( $a->name, $b->name );
            }
            return $order_a - $order_b;
        } );
    } else {
        // Use default WordPress ordering
        usort( $groups, function( $a, $b ) use ( $atts ) {
            if ( $atts['orderby'] === 'name' ) {
                $result = strcmp( $a->name, $b->name );
                return $atts['order'] === 'DESC' ? -$result : $result;
            }
            return 0;
        } );
    }

    ob_start();

    /**
     * Output inline CSS once (first time shortcode is used).
     */
    static $lcm_styles_printed = false;
    if ( ! $lcm_styles_printed ) {
        $lcm_styles_printed = true;
        ?>
        <style>
        /* Container for all groups */
        .lcm-cert-grid {
            margin: 2rem 0;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.5rem;
        }

        /* Entrance animation */
        @keyframes lcm-fade-up {
            from {
                opacity: 0;
                transform: translateY(18px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Group Card */
.lcm-cert-group-card {
    background: #ffffff;
    border-radius: 1rem;
    padding: 1.5rem 1.5rem 1.25rem;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
    border: 1px solid rgba(148, 163, 184, 0.25);
    position: relative;
    overflow: visible;         /* allow tooltip to extend outside the card */

    opacity: 0;
    transform: translateY(18px) scale(0.98);
    animation: lcm-fade-up 0.6s ease-out forwards;
}


        /* Stagger the cards a bit */
        .lcm-cert-grid .lcm-cert-group-card:nth-child(1) { animation-delay: 0.05s; }
        .lcm-cert-grid .lcm-cert-group-card:nth-child(2) { animation-delay: 0.15s; }
        .lcm-cert-grid .lcm-cert-group-card:nth-child(3) { animation-delay: 0.25s; }
        .lcm-cert-grid .lcm-cert-group-card:nth-child(4) { animation-delay: 0.35s; }
        .lcm-cert-grid .lcm-cert-group-card:nth-child(5) { animation-delay: 0.45s; }
        .lcm-cert-grid .lcm-cert-group-card:nth-child(6) { animation-delay: 0.55s; }

        @media (prefers-reduced-motion: reduce) {
            .lcm-cert-group-card {
                animation: none;
                opacity: 1;
                transform: none;
            }
        }

        .lcm-cert-group-card::before {
            content: "";
            position: absolute;
            inset: 0;
            height: 4px;
            background: linear-gradient(90deg, #0ea5e9, #22c55e, #a855f7);
            opacity: 0.85;
        }

        .lcm-cert-group-header {
            margin-top: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .lcm-cert-group-title {
            font-size: 1.05rem;
            font-weight: 600;
            color: #0f172a;
            margin: 0;
        }

        .lcm-cert-list {
            list-style: none;
            margin: 0.75rem 0 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .lcm-cert-item {
            margin: 0;
        }

        .lcm-cert-link {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            width: 100%;
            padding: 0.4rem 0.55rem;
            border-radius: 0.6rem;
            text-decoration: none;
            background: rgba(248, 250, 252, 0.9);
            border: 1px solid transparent;
            position: relative;
            transition:
                background 0.18s ease,
                border-color 0.18s ease,
                box-shadow 0.18s ease,
                transform 0.12s ease;
        }

        .lcm-cert-link--no-file {
            cursor: default;
        }

        .lcm-cert-link:hover {
            background: #0f172a;
            border-color: #0ea5e9;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.25);
            transform: translateY(-1px);
        }

        .lcm-cert-title {
            font-size: 0.9rem;
            font-weight: 500;
            color: #0f172a;
        }

        .lcm-cert-link:hover .lcm-cert-title {
            color: #e5f2ff;
        }

        .lcm-cert-badge {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.2rem 0.45rem;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.06);
            color: #334155;
            flex-shrink: 0;
        }

        .lcm-cert-link:hover .lcm-cert-badge {
            background: rgba(56, 189, 248, 0.16);
            color: #e0f2fe;
        }

        /* Text tooltip using data-lcm-tooltip (for non-image files) */
        .lcm-cert-link[data-lcm-tooltip]:hover::after,
        .lcm-cert-link--no-file[data-lcm-tooltip]:hover::after {
            opacity: 1;
            visibility: visible;
            transform: translate(-50%, -10px);
        }

        .lcm-cert-link[data-lcm-tooltip]::after,
        .lcm-cert-link--no-file[data-lcm-tooltip]::after {
            content: attr(data-lcm-tooltip);
            position: absolute;
            left: 50%;
            bottom: 110%;
            transform: translate(-50%, -4px);
            background: #0f172a;
            color: #f9fafb;
            padding: 0.55rem 0.75rem;
            border-radius: 0.6rem;
            font-size: 0.75rem;
            line-height: 1.3;
            white-space: normal;
            min-width: 180px;
            max-width: 260px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.45);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            z-index: 20;
        }

        .lcm-cert-link[data-lcm-tooltip]::before,
        .lcm-cert-link--no-file[data-lcm-tooltip]::before {
            content: "";
            position: absolute;
            left: 50%;
            bottom: 104%;
            transform: translateX(-50%);
            border-width: 6px;
            border-style: solid;
            border-color: #0f172a transparent transparent transparent;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.18s ease;
        }

        .lcm-cert-link[data-lcm-tooltip]:hover::before,
        .lcm-cert-link--no-file[data-lcm-tooltip]:hover::before {
            opacity: 1;
            visibility: visible;
        }

        /* Image thumbnail tooltip (for image cert files) */
        .lcm-cert-thumb {
            position: absolute;
            left: 50%;
            bottom: 110%;
            transform: translate(-50%, -4px);
            background: #0f172a;
            padding: 0.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.5);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            z-index: 25;
            max-width: 240px;
        }

        .lcm-cert-thumb img {
            display: block;
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
        }

        .lcm-cert-thumb-caption {
            display: block;
            margin-top: 0.4rem;
            font-size: 0.72rem;
            color: #e5e7eb;
            line-height: 1.3;
        }

        .lcm-cert-link:hover .lcm-cert-thumb {
            opacity: 1;
            visibility: visible;
            transform: translate(-50%, -10px);
        }

        @media (max-width: 640px) {
            .lcm-cert-group-card {
                padding: 1.25rem 1.1rem 1rem;
            }

            .lcm-cert-group-title {
                font-size: 1rem;
            }

            .lcm-cert-link {
                padding: 0.35rem 0.5rem;
            }
        }
        </style>
        <?php
    }

    echo '<div class="lcm-cert-grid">';

    foreach ( $groups as $group ) {
        // First check if any certificate in this group has custom order
        $has_custom_cert_order = false;
        $temp_query = new WP_Query( array(
            'post_type'      => 'learning_certificate',
            'posts_per_page' => -1,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'certificate_group',
                    'field'    => 'term_id',
                    'terms'    => $group->term_id,
                ),
            ),
            'fields'         => 'ids',
        ) );

        if ( $temp_query->have_posts() ) {
            foreach ( $temp_query->posts as $cert_id ) {
                $order = get_post_meta( $cert_id, '_lcm_certificate_order', true );
                if ( $order !== '' ) {
                    $has_custom_cert_order = true;
                    break;
                }
            }
        }
        wp_reset_postdata();

        // Query certificates with appropriate ordering
        $query_args = array(
            'post_type'      => 'learning_certificate',
            'posts_per_page' => -1,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'certificate_group',
                    'field'    => 'term_id',
                    'terms'    => $group->term_id,
                ),
            ),
        );

        if ( $has_custom_cert_order ) {
            $query_args['meta_key'] = '_lcm_certificate_order';
            $query_args['orderby'] = array(
                'meta_value_num' => 'ASC',
                'title' => 'ASC',
            );
        } else {
            $query_args['orderby'] = 'title';
            $query_args['order'] = 'ASC';
        }

        $cert_query = new WP_Query( $query_args );

        if ( $cert_query->have_posts() ) {
            echo '<div class="lcm-cert-group-card">';
            echo '<div class="lcm-cert-group-header">';
            echo '<h3 class="lcm-cert-group-title">' . esc_html( $group->name ) . '</h3>';
            echo '</div>';

            echo '<ul class="lcm-cert-list">';

            while ( $cert_query->have_posts() ) {
                $cert_query->the_post();
                $file_url   = get_post_meta( get_the_ID(), '_lcm_certificate_file_url', true );
                $title      = get_the_title();

                // Description from post content (for text or caption)
                $raw_content = get_post_field( 'post_content', get_the_ID() );
                $description = wp_strip_all_tags( $raw_content );
                $description = trim( $description );
                if ( strlen( $description ) > 220 ) {
                    $description = mb_substr( $description, 0, 217 ) . '...';
                }

                // Detect if file is an image (by extension)
                $is_image = false;
                if ( $file_url ) {
                    $path = parse_url( $file_url, PHP_URL_PATH );
                    if ( $path && preg_match( '/\.(jpe?g|png|gif|webp)$/i', $path ) ) {
                        $is_image = true;
                    }
                }

                // For non-image files, we keep text tooltip; for image we show image preview instead
                $tooltip_attr = '';
                if ( ! $is_image && $description ) {
                    $tooltip_attr = ' data-lcm-tooltip="' . esc_attr( $description ) . '"';
                }

                echo '<li class="lcm-cert-item">';

                if ( $file_url ) {
                    echo '<a class="lcm-cert-link" href="' . esc_url( $file_url ) . '" target="_blank" rel="noopener noreferrer"' . $tooltip_attr . '>';
                    echo '<span class="lcm-cert-title">' . esc_html( $title ) . '</span>';
                    echo '<span class="lcm-cert-badge">View</span>';

                    // If image, add thumbnail tooltip block
                    if ( $is_image ) {
                        echo '<span class="lcm-cert-thumb">';
                        echo '<img src="' . esc_url( $file_url ) . '" alt="' . esc_attr( $title ) . '">';
                        if ( $description ) {
                            echo '<span class="lcm-cert-thumb-caption">' . esc_html( $description ) . '</span>';
                        }
                        echo '</span>';
                    }

                    echo '</a>';
                } else {
                    // No file URL – keep as plain text (with possible text tooltip)
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
add_shortcode( 'learning_certificates', 'lcm_learning_certificates_shortcode' );
