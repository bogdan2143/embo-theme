<?php
/**
 * Динамічний блок для перемикання між AJAX-Load More і посторінковою пагінацією
 *
 * @package MyBlockTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MyBlockTheme_LoadToggle {

    public function __construct() {
        // Реєструємо сам блок
        add_action( 'init', [ $this, 'register_block' ] );
        // Реєструємо фільтр, що ховає core/query з inherit:true в режимі pagination
        add_filter( 'render_block_core/query', [ $this, 'filter_core_query' ], 10, 2 );
    }

    public function register_block() {
        register_block_type( 'embo/load-toggle', [
            'render_callback' => [ $this, 'render_toggle' ],
            'supports'        => [ 'html' => false ],
        ] );
    }

    /**
     * Сховаємо лише core/query з inherit:true у режимі pagination
     *
     * Через баг у Gutenberg (до версії 6.7.2) блок Query Loop із inherit:true
     * не наслідує параметр paged із pretty URL (/page/X/),
     * тому в режимі посторінкової навігації замість першої сторінки
     * нам потрібно приховати цей блок і рендерити власний WP_Query.
     *
     * Докладніше: https://github.com/WordPress/gutenberg/issues/67252 :contentReference[oaicite:0]{index=0}
     */
    public function filter_core_query( $output, $block ) {
        // Не чіпаємо адмінку
        if ( is_admin() ) {
            return $output;
        }

        // Дізнаємося режим (ajax або pagination)
        $opts = get_option( 'embo_custom_css_options', [] );
        $mode = $opts['load_type'] ?? 'ajax';

        // Якщо посторінкова навігація і є inherit:true — ховаємо core/query
        if ( $mode === 'pagination' && ! empty( $block['attrs']['inherit'] ) ) {
            return '';
        }

        return $output;
    }

    public function render_toggle() {
        $opts = get_option( 'embo_custom_css_options', [] );
        $type = $opts['load_type'] ?? 'ajax';

        // Режим пагінації
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
                'posts_per_page' => 4,
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
                                the_post_thumbnail( 'medium' );
                            } else {
                                echo '<img src="https://via.placeholder.com/300x200" alt="' . esc_attr__( 'Новина', 'myblocktheme' ) . '">';
                            }
                            ?>
                        </figure>
                        <div class="news-content">
                            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <p class="subtitle is-6"><?php echo get_the_date(); ?></p>
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

            $links = paginate_links( [
                'base'      => esc_url( $base_url ),
                'format'    => '',           // format empty because page param is already in base
                'current'   => $paged,
                'total'     => $q->max_num_pages,
                'type'      => 'list',
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
            ] );

            echo '<div class="fallback-pagination">' . $links . '</div>';

            return ob_get_clean();
        }

        // Режим AJAX
        $label = __( 'Завантажити старі пости', 'myblocktheme' );
        return sprintf(
            '<div class="load-more"><button id="loadMoreButton" class="button is-primary">%s</button></div>',
            esc_html( $label )
        );
    }
}