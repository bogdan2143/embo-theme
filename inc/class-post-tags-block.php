<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Dynamic block that outputs post tags styled according to the Figma design.
 */
class MyBlockTheme_PostTagsBlock {

    public function __construct() {
        add_action( 'init', [ $this, 'register_block' ] );
    }

    /**
     * Block registration.
     */
    public function register_block() {
        register_block_type( 'myblocktheme/post-tags', [
            'apiVersion'      => 2,
            'render_callback' => [ $this, 'render_post_tags' ],
            'attributes'      => [
                'className' => [
                    'type'    => 'string',
                    'default' => 'tags-area section',
                ],
            ],
        ] );
    }

    /**
     * Render the tag list for the current post.
     *
     * @param array $attributes
     * @return string
     */
    public function render_post_tags( $attributes ) {
        $tags = get_the_tags();
        if ( empty( $tags ) ) {
            return '';
        }

        // Output container with padding
        $output  = '<div class="' . esc_attr( $attributes['className'] ) . '">';
        // flex-wrap to place tags on new lines
        $output .= '<div class="tags is-flex is-flex-wrap-wrap">';
        foreach ( $tags as $tag ) {
            $link = get_tag_link( $tag->term_id );
            $name = esc_html( $tag->name );
            // Pill with light background and padding
            $output .= sprintf(
                '<a href="%1$s" class="tag is-light has-text-weight-medium mr-2 mb-2">#%2$s</a>',
                esc_url( $link ),
                $name
            );
        }
        $output .= '</div>';
        $output .= '</div>';

        return $output;
    }
}

new MyBlockTheme_PostTagsBlock();