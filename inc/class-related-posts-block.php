<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Dynamic block for displaying similar posts by tags
 * in cards based on Figma layout with placeholder support
 * without unnecessary overlay on the image.
 */
class MyBlockTheme_RelatedPostsBlock {

    public function __construct() {
        add_action( 'init', [ $this, 'register_block' ] );
    }

    /**
     * Block registration
     */
    public function register_block() {
        register_block_type( 'myblocktheme/related-posts', [
            'apiVersion'      => 2,
            'render_callback' => [ $this, 'render_related_posts' ],
            'attributes'      => [
                'title' => [
                    'type'    => 'string',
                    'default' => __( 'Схожі публікації', 'myblocktheme' ),
                ],
                'postsPerPage' => [
                    'type'    => 'number',
                    'default' => 4,
                ],
            ],
        ] );
    }

    /**
     * Rendering similar posts with placeholder
     *
     * @param array $attributes
     * @return string
     */
    public function render_related_posts( $attributes ) {
        $tag_ids = wp_get_post_tags( get_the_ID(), [ 'fields' => 'ids' ] );
        if ( empty( $tag_ids ) ) {
            return '';
        }

        $query = new WP_Query( [
            'post_type'      => 'post',
            'posts_per_page' => intval( $attributes['postsPerPage'] ),
            'post__not_in'   => [ get_the_ID() ],
            'tag__in'        => $tag_ids,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ] );
        if ( ! $query->have_posts() ) {
            return '';
        }

        // URL to your filler image
        $placeholder = get_template_directory_uri() . '/assets/images/placeholder.jpg';

        ob_start();
        ?>
        <div class="related-posts section">
            <h3 class="title is-4"><?php echo esc_html( $attributes['title'] ); ?></h3>
            <div class="columns is-multiline is-variable is-6">
                <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                    <div class="column is-12-mobile is-6-tablet is-6-desktop">
                        <div class="related-item">
                            <figure class="image is-4by3">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <?php echo get_the_post_thumbnail(
                                        get_the_ID(),
                                        'medium_large',
                                        [ 'alt' => get_the_title() ]
                                    ); ?>
                                <?php else : ?>
                                    <img
                                        src="<?php echo esc_url( $placeholder ); ?>"
                                        alt="<?php echo esc_attr( get_the_title() ); ?>"
                                    />
                                <?php endif; ?>
                            </figure>

                            <h4 class="related-title title is-6 mb-2">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h4>

                            <p class="related-date subtitle is-7 has-text-grey">
                                <?php echo get_the_date( 'd.m.Y' ); ?>
                            </p>
                        </div>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

new MyBlockTheme_RelatedPostsBlock();