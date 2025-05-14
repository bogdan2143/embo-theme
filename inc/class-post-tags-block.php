<?php
/**
 * Клас MyBlockTheme_PostTagsBlock
 *
 * Динамічний блок для виводу тегів поточного посту,
 * стилізованих під дизайн із Figma.
 */
class MyBlockTheme_PostTagsBlock {

    public function __construct() {
        add_action( 'init', [ $this, 'register_block' ] );
    }

    /**
     * Реєстрація блоку.
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
     * Рендеринг списку тегів поточного посту.
     *
     * @param array $attributes
     * @return string
     */
    public function render_post_tags( $attributes ) {
        $tags = get_the_tags();
        if ( empty( $tags ) ) {
            return '';
        }

        // Виводимо контейнер з відступами
        $output  = '<div class="' . esc_attr( $attributes['className'] ) . '">';
        // flex-wrap, щоб переносити теги на новий рядок
        $output .= '<div class="tags is-flex is-flex-wrap-wrap">';
        foreach ( $tags as $tag ) {
            $link = get_tag_link( $tag->term_id );
            $name = esc_html( $tag->name );
            // Пігулка з легким фоном і відступами
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