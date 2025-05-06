<?php
/**
 * Клас MyBlockTheme_AjaxLoadMore
 *
 * Реєструє AJAX-обробку для завантаження постів.
 */

class MyBlockTheme_AjaxLoadMore {

    /**
     * Підключає скрипт для AJAX-завантаження постів.
     */
    public function enqueue_load_more_script() {
        $opts = get_option( 'embo_custom_css_options', [] );
        if ( ($opts['load_type'] ?? 'ajax') !== 'ajax' ) {
            return;
        }
        wp_enqueue_script( 'myblocktheme-load-more', get_template_directory_uri() . '/src/js/load-more.js', array( 'jquery' ), '1.0', true );
        // Визначаємо поточну категорію, якщо ми на архівній сторінці
        $current_category = 'news';
        if ( is_category() ) {
            $cat_obj = get_queried_object();
            if ( isset( $cat_obj->slug ) ) {
                $current_category = $cat_obj->slug;
            }
        }
        wp_localize_script( 'myblocktheme-load-more', 'myblockthemeLoadMore', array(
            'ajax_url'       => admin_url( 'admin-ajax.php' ),
            'category'       => $current_category,
            'posts_per_page' => 4,
        ));
    }

    /**
     * Обробляє AJAX-запит для завантаження постів.
     */
    public function load_more_posts() {
        $paged = isset( $_POST['paged'] ) ? intval( $_POST['paged'] ) : 2;
        $category = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : 'news';
        $posts_per_page = isset( $_POST['posts_per_page'] ) ? intval( $_POST['posts_per_page'] ) : 4;

        $args = array(
            'post_type'      => 'post',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'paged'          => $paged,
            'posts_per_page' => $posts_per_page,
            'tax_query'      => array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => $category,
                ),
            ),
        );
        $query = new WP_Query( $args );

        if ( $query->have_posts() ) {
            ob_start();
            while ( $query->have_posts() ) : $query->the_post();
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
                        <div><?php the_excerpt(); ?></div>
                        <a href="<?php the_permalink(); ?>" class="button is-link is-small"><?php _e( 'Читати далі', 'myblocktheme' ); ?></a>
                    </div>
                </article>
                <?php
            endwhile;
            wp_reset_postdata();
            $content = ob_get_clean();
            wp_send_json_success( $content );
        } else {
            wp_send_json_error( __( 'No more posts', 'myblocktheme' ) );
        }
        wp_die();
    }
}