<?php
/**
 * Клас MyBlockTheme_InformerShortcode
 *
 * Шорткод [informer] виводить один «великий» та кілька «малих» постів,
 * а також заголовок із назвою переданої категорії.
 * Під заголовками виводиться дата у форматі «07 Травня 2025, 15:34».
 *
 * @package myblocktheme
 */

class MyBlockTheme_InformerShortcode {

    /**
     * Обробка шорткода.
     *
     * @param array $atts Атрибути шорткода.
     * @return string HTML-розмітка інформера.
     */
    public function informer_shortcode( $atts ) {

        // Витягуємо атрибути шорткоду з дефолтами
        $atts = shortcode_atts( array(
            'category'    => 'news',
            'per_page'    => 4,
            'button_text' => __( 'Читати далі', 'myblocktheme' ),
        ), $atts, 'informer' );

        // Отримуємо об’єкт терміну категорії за slug
        $term = get_term_by( 'slug', $atts['category'], 'category' );
        $category_name = $term && ! is_wp_error( $term )
            ? $term->name
            : __( 'Новини', 'myblocktheme' );

        /* ---------- 1. «Великий» пост ---------- */
        $query_featured = new WP_Query( array(
            'posts_per_page' => 1,
            'post_type'      => 'post',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'tax_query'      => array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => $atts['category'],
                ),
            ),
        ) );

        /* ---------- 2. Маленькі пости ---------- */
        $query_small = new WP_Query( array(
            'posts_per_page' => max( intval( $atts['per_page'] ) - 1, 1 ),
            'offset'         => 1,
            'post_type'      => 'post',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'tax_query'      => array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => $atts['category'],
                ),
            ),
        ) );

        ob_start(); ?>
        <div class="informer-block">
            <!-- Динамічний заголовок із назвою категорії -->
            <h2 class="title is-4"><?php echo esc_html( $category_name ); ?></h2>

            <div class="columns">
                <!-- ---------- Featured ---------- -->
                <div class="column is-two-thirds featured-item">
                    <?php if ( $query_featured->have_posts() ) : $query_featured->the_post(); ?>
                        <figure class="informer-img">
                            <a href="<?php the_permalink(); ?>">
                                <?php
                                if ( has_post_thumbnail() ) {
                                    // Використовуємо кастомний розмір informer_featured (326×242)
                                    the_post_thumbnail( 'informer_featured' );
                                } else {
                                    echo '<img src="https://via.placeholder.com/326x242" alt="' . esc_attr__( 'Новина', 'myblocktheme' ) . '">';
                                }
                                ?>
                            </a>
                        </figure>

                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

                        <?php
                        // Форматуємо дату: 07 Травня 2025, 15:34
                        $day   = get_the_date( 'd' );
                        $month = get_the_date( 'F' );
                        // Перша буква місяця — велика
                        if ( function_exists( 'mb_convert_case' ) ) {
                            $month = mb_convert_case( $month, MB_CASE_TITLE, 'UTF-8' );
                        } else {
                            $month = ucfirst( $month );
                        }
                        $year  = get_the_date( 'Y' );
                        $time  = get_the_time( 'H:i' );
                        $date_string = sprintf( '%s %s %s, %s', $day, $month, $year, $time );
                        ?>
                        <div class="informer-date"><?php echo esc_html( $date_string ); ?></div>

                    <?php endif; wp_reset_postdata(); ?>
                </div>

                <!-- ---------- Small ---------- -->
                <div class="column is-one-third">
                    <?php if ( $query_small->have_posts() ) : ?>
                        <?php while ( $query_small->have_posts() ) : $query_small->the_post(); ?>
                            <div class="small-item">
                                <figure class="informer-img">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php
                                        if ( has_post_thumbnail() ) {
                                            // Використовуємо кастомний розмір informer_small (120×90)
                                            the_post_thumbnail( 'informer_small' );
                                        } else {
                                            echo '<img src="https://via.placeholder.com/120x90" alt="' . esc_attr__( 'Новина', 'myblocktheme' ) . '">';
                                        }
                                        ?>
                                    </a>
                                </figure>

                                <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>

                                <?php
                                // Дата для маленьких постів
                                $day   = get_the_date( 'd' );
                                $month = get_the_date( 'F' );
                                if ( function_exists( 'mb_convert_case' ) ) {
                                    $month = mb_convert_case( $month, MB_CASE_TITLE, 'UTF-8' );
                                } else {
                                    $month = ucfirst( $month );
                                }
                                $year  = get_the_date( 'Y' );
                                $time  = get_the_time( 'H:i' );
                                $date_string = sprintf( '%s %s %s, %s', $day, $month, $year, $time );
                                ?>
                                <div class="informer-date small-date"><?php echo esc_html( $date_string ); ?></div>

                            </div>
                        <?php endwhile; wp_reset_postdata(); ?>
                    <?php endif; ?>
                </div>
            </div>

            <?php
            // ---------- кнопка «Читати далі» ----------
            if ( $term && ! is_wp_error( $term ) ) {
                $cat_link = get_category_link( $term->term_id );
                // Формуємо перекладену рядок з плейсхолдером
                $read_all = sprintf(
                    /* translators: %s — назва категорії */
                    esc_html__( 'Всі статті %s →', 'myblocktheme' ),
                    esc_html( $category_name )
                );

                printf(
                    '<p class="informer-read-all"><a href="%s" class="has-text-link">%s</a></p>',
                    esc_url( $cat_link ),
                    $read_all
                );
            }
            ?>
        </div>
        <?php
        return ob_get_clean();
    }
}