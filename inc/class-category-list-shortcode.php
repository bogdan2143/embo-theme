<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Implements the [category_list] shortcode which outputs all or specified
 * categories as pills styled the same way as tags.
 */
class MyBlockTheme_CategoryListShortcode {

    public function __construct() {
        // Register the [category_list] shortcode
        add_shortcode( 'category_list', [ $this, 'render_category_list' ] );
    }

    /**
     * Shortcode handler.
     *
     * Supported attributes:
     *  - include     (comma-separated category IDs)
     *  - exclude     (comma-separated category IDs)
     *  - orderby     (name, count, slug etc.)
     *  - order       (ASC|DESC)
     *  - hide_empty  (true|false)
     *  - prefix      (string before the category name, default "#")
     *  - class       (container CSS class, default "tags-area section")
     */
    public function render_category_list( $atts ) {
        // Merge provided attributes with defaults
        $atts = shortcode_atts( [
            'include'    => '',
            'exclude'    => '',
            'orderby'    => 'name',
            'order'      => 'ASC',
            'hide_empty' => 'true',
            'prefix'     => '#',
            'class'      => 'tags-area informer-block',
        ], $atts, 'category_list' );

        // Prepare arguments for category query
        $args = [
            'orderby'    => $atts['orderby'],
            'order'      => $atts['order'],
            'hide_empty' => filter_var( $atts['hide_empty'], FILTER_VALIDATE_BOOLEAN ),
        ];

        if ( $atts['include'] ) {
            // Include only specified categories
            $args['include'] = array_map( 'intval', explode( ',', $atts['include'] ) );
        }
        if ( $atts['exclude'] ) {
            // Exclude specified categories
            $args['exclude'] = array_map( 'intval', explode( ',', $atts['exclude'] ) );
        }

        // Get category list
        $categories = get_categories( $args );
        if ( empty( $categories ) ) {
            return '';
        }

        // Start output buffering
        ob_start();
        ?>
        <div class="<?php echo esc_attr( $atts['class'] ); ?>">
            <h2 class="title is-4"><?php echo esc_html__( 'Популярні теми', 'myblocktheme' ); ?></h2>
            <div class="tags is-flex is-flex-wrap-wrap">
                <?php foreach ( $categories as $cat ) :
                    $link = get_category_link( $cat->term_id );
                    $name = esc_html( $cat->name );
                ?>
                    <a href="<?php echo esc_url( $link ); ?>"
                       class="tag is-light has-text-weight-medium mr-2 mb-2">
                        <?php echo esc_html( $atts['prefix'] . $name ); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        // Return the generated HTML
        return ob_get_clean();
    }
}

new MyBlockTheme_CategoryListShortcode();