<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Registers and renders the Search Heading block.
 */
class MyBlockTheme_SearchHeadingBlock {

    /**
     * Registers the dynamic Search Heading block.
     */
    public function register_block() {
        register_block_type(
            'myblocktheme/search-heading',
            array(
                'apiVersion'      => 2,
                'title'           => __( 'Search Heading', 'myblocktheme' ),
                'description'     => __( 'Displays the search query as a heading.', 'myblocktheme' ),
                'category'        => 'widgets',
                'icon'            => 'search',
                'render_callback' => array( $this, 'render_search_heading' ),
                'supports'        => array(
                    'html' => false,
                ),
            )
        );
    }

    /**
     * Renders the search heading markup.
     *
     * @return string HTML output for the heading element.
     */
    public function render_search_heading() {
        $query = get_search_query();
        if ( empty( $query ) ) {
            return '';
        }

        $title = sprintf(
            /* translators: %s: search query. */
            esc_html__( 'Search results for: %s', 'myblocktheme' ),
            esc_html( $query )
        );

        return '<h1 class="search-title">' . $title . '</h1>';
    }
}
