<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Registers a dynamic "Breadcrumbs" block with Schema.org markup.
 */
class MyBlockTheme_DynamicBreadcrumbs {

    /**
     * Returns the primary category for the given post if available.
     * Falls back to the first assigned category.
     *
     * @param int $post_id Post ID.
     * @return WP_Term|null Category term object or null when not found.
     */
    private function get_primary_category( $post_id ) {
        // Use Yoast SEO primary term when the plugin is active.
        if ( class_exists( 'WPSEO_Primary_Term' ) ) {
            $primary = new WPSEO_Primary_Term( 'category', $post_id );
            $term_id = $primary->get_primary_term();
            if ( $term_id && ! is_wp_error( $term_id ) ) {
                $term = get_term( $term_id, 'category' );
                if ( $term && ! is_wp_error( $term ) ) {
                    return $term;
                }
            }
        }

        // Fallback to the first category.
        $categories = get_the_category( $post_id );
        return ! empty( $categories ) ? $categories[0] : null;
    }

    /**
     * Registers the breadcrumbs block.
     */
    public function register_dynamic_breadcrumbs() {
        register_block_type( 'myblocktheme/breadcrumbs', array(
            'apiVersion'      => 2,
            'title'           => __( 'Хлібні крихти', 'myblocktheme' ),
            'description'     => __( 'Динамічний блок для виводу хлібних крихт.', 'myblocktheme' ),
            'category'        => 'widgets',
            'icon'            => 'admin-links',
            'render_callback' => array( $this, 'render_breadcrumbs_block' ),
            'supports'        => array(
                'html' => false,
            ),
        ) );
    }

    /**
     * Callback for rendering breadcrumbs with SEO markup.
     */
    public function render_breadcrumbs_block( $attributes ) {
        // If this is the front page, output a single "Home" item
        if ( is_front_page() ) {
            return '';
        }

        $breadcrumbs = array();
        // First item: link to the home page
        $breadcrumbs[] = array(
            'title' => __( 'Головна', 'myblocktheme' ),
            'url'   => home_url( '/' )
        );

        // Add items depending on the current page type
        if ( is_category() ) {
            $breadcrumbs[] = array(
                'title' => single_cat_title( '', false )
            );
        } elseif ( is_single() ) {
            $category = $this->get_primary_category( get_the_ID() );
            if ( $category ) {
                $breadcrumbs[] = array(
                    'title' => esc_html( $category->name ),
                    'url'   => get_category_link( $category->term_id ),
                );
            }
            $breadcrumbs[] = array(
                'title' => get_the_title(),
            );
        } elseif ( is_page() ) {
            $breadcrumbs[] = array(
                'title' => get_the_title()
            );
        } elseif ( is_search() ) {
            $breadcrumbs[] = array(
                'title' => __( 'Результати пошуку', 'myblocktheme' )
            );
        }

        // Формуємо HTML з SEO-розміткою Schema.org BreadcrumbList
        $output = '<nav class="breadcrumb" aria-label="' . esc_attr__( 'хлібні крихти', 'myblocktheme' ) . '" itemscope itemtype="https://schema.org/BreadcrumbList"><ol>';
        $position = 1;
        foreach ( $breadcrumbs as $crumb ) {
            $output .= '<li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            if ( isset( $crumb['url'] ) ) {
                // If an URL is present, output a link
                $output .= '<a href="' . esc_url( $crumb['url'] ) . '" itemprop="item"><span itemprop="name">' . esc_html( $crumb['title'] ) . '</span></a>';
            } else {
                // Otherwise output the active item
                $output .= '<span itemprop="name" class="is-active">' . esc_html( $crumb['title'] ) . '</span>';
            }
            // Meta position for SEO
            $output .= '<meta itemprop="position" content="' . $position . '" />';
            $output .= '</li>';
            $position++;
        }
        $output .= '</ol></nav>';

        return $output;
    }
}