<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Dynamic block that toggles between AJAX "Load More" and page-based pagination.
 *
 * In pagination mode this block hides the inherited Query Loop (inherit:true)
 * and renders its own WP_Query with controlled navigation.
 *
 * @package MyBlockTheme
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MyBlockTheme_LoadToggle {

    public function __construct() {
        // Register the block
        add_action( 'init', [ $this, 'register_block' ] );
        // Hide inherited core/query in pagination mode
        add_filter( 'render_block', [ $this, 'filter_core_query' ], 10, 2 );

    }

    public function register_block() {
        register_block_type( 'embo/load-toggle', [
            'render_callback' => [ $this, 'render_toggle' ],
            'supports'        => [ 'html' => false ],
        ] );
    }

    /**
     * Hide the inherited core/query Loop when pagination is active.
     *
     * Due to a server-side rendering issue in Query Loop (Gutenberg before PR #69698)
     * the block does not respect the paged parameter from pretty URLs (/page/X/).
     * In pagination mode we hide this block and render our own WP_Query.
     *
     * @see https://github.com/WordPress/gutenberg/pull/69698
     */
    public function filter_core_query( $output, $block ) {
        // Skip admin area
        if ( is_admin() ) {
            return $output;
        }

        // Determine mode (ajax or pagination)
        $opts = get_option( 'embo_custom_css_options', [] );
        $mode = $opts['load_type'] ?? 'ajax';

        // Hide core/query if pagination mode with inherit:true
        if ( $mode === 'pagination'
             && isset( $block['blockName'], $block['attrs']['inherit'] )
             && $block['blockName'] === 'core/query'
             && ! empty( $block['attrs']['inherit'] )
        ) {
            return '';
        }

        return $output;
    }

    public function render_toggle() {
        $opts = get_option( 'embo_custom_css_options', [] );
        $type = $opts['load_type'] ?? 'ajax';

        // Pagination mode
        if ( $type === 'pagination' ) {
            // Determine current pagination page (supports both 'paged' and 'page' query vars)
            $paged_query = absint( get_query_var( 'paged' ) );
            $page_query  = absint( get_query_var( 'page' ) );
            $paged       = max( 1, $paged_query > 1 ? $paged_query : $page_query );
            error_log( sprintf(
                'MyBlockTheme_LoadToggle::render_toggle – paged_query=%d, page_query=%d, using paged=%d',
                $paged_query,
                $page_query,
                $paged
            ) );

            $category_slug = '';
            if ( is_category() ) {
                $cat = get_queried_object();
                $category_slug = $cat->slug ?? '';
            }

            $args = [
                'post_type'      => 'post',
                'orderby'        => 'date',
                'order'          => 'DESC',
                'posts_per_page' => absint( get_option( 'posts_per_page' ) ),
                'paged'          => $paged,
            ];
            if ( $category_slug ) {
                $args['tax_query'] = [
                    [
                        'taxonomy' => 'category',
                        'field'    => 'slug',
                        'terms'    => $category_slug,
                    ],
                ];
            }

            $q = new \WP_Query( $args );
            ob_start();
            if ( $q->have_posts() ) {
                while ( $q->have_posts() ) {
                    $q->the_post();
                    ?>
                    <article class="news-item">
                        <figure class="news-image">
                            <?php
                            if ( has_post_thumbnail() ) {
                                the_post_thumbnail( 'informer_featured' );
                            } else {
                                echo '<img src="https://via.placeholder.com/300x200" alt="' . esc_attr__( 'Новина', 'myblocktheme' ) . '">';
                            }
                            ?>
                        </figure>
                        <div class="news-content">
                            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <p class="subtitle is-6">
                                <time datetime="<?php echo get_the_date( 'c' ); ?>"><?php echo get_the_date(); ?></time>
                            </p>
                        </div>
                    </article>
                    <?php
                }
                wp_reset_postdata();
            }

            // Build pagination links using query var ?page=
            $current_url = get_pagenum_link(); // base URL for current context
            $base_url    = remove_query_arg( 'page', $current_url );
            $base_url    = add_query_arg( 'page', '%#%', $base_url );

            // Build pagination links using pretty URL (/page/X/)
            $big  = 999999999; // placeholder number to be replaced
            $links = paginate_links( [
                'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                'format'    => 'page/%#%/',
                'current'   => $paged,
                'total'     => $q->max_num_pages,
                'type'      => 'list',
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
            ] );

            echo '<div class="fallback-pagination">' . $links . '</div>';

            return ob_get_clean();
        }

        // AJAX mode
        $label = __( 'Завантажити старі пости', 'myblocktheme' );
        return sprintf(
            '<div class="load-more"><button id="loadMoreButton" class="button is-primary">%s</button></div>',
            esc_html( $label )
        );
    }
}