<?php
/**
 * Клас MyBlockTheme_CategoryListShortcode
 *
 * Реалізує шорткод [category_list],
 * який виводить всі (або вказані) категорії у вигляді «таблеток»,
 * стилізованих так само, як теги.
 */
class MyBlockTheme_CategoryListShortcode {

    public function __construct() {
        // Реєструємо шорткод [category_list]
        add_shortcode( 'category_list', [ $this, 'render_category_list' ] );
    }

    /**
     * Обробник шорткоду.
     *
     * Підтримувані атрибути:
     *  - include     (список ID категорій через кому)
     *  - exclude     (список ID категорій через кому)
     *  - orderby     (name, count, slug тощо)
     *  - order       (ASC|DESC)
     *  - hide_empty  (true|false)
     *  - prefix      (рядок перед ім’ям категорії; за замовчуванням "#")
     *  - class       (CSS-клас контейнера; за замовчуванням "tags-area section")
     */
    public function render_category_list( $atts ) {
        // Зливаємо передані атрибути з дефолтними
        $atts = shortcode_atts( [
            'include'    => '',
            'exclude'    => '',
            'orderby'    => 'name',
            'order'      => 'ASC',
            'hide_empty' => 'true',
            'prefix'     => '#',
            'class'      => 'tags-area informer-block',
        ], $atts, 'category_list' );

        // Підготовка аргументів для запиту категорій
        $args = [
            'orderby'    => $atts['orderby'],
            'order'      => $atts['order'],
            'hide_empty' => filter_var( $atts['hide_empty'], FILTER_VALIDATE_BOOLEAN ),
        ];

        if ( $atts['include'] ) {
            // Включаємо лише вказані категорії
            $args['include'] = array_map( 'intval', explode( ',', $atts['include'] ) );
        }
        if ( $atts['exclude'] ) {
            // Виключаємо вказані категорії
            $args['exclude'] = array_map( 'intval', explode( ',', $atts['exclude'] ) );
        }

        // Отримуємо список категорій
        $categories = get_categories( $args );
        if ( empty( $categories ) ) {
            return '';
        }

        // Починаємо буферизацію виводу
        ob_start();
        ?>
        <div class="<?php echo esc_attr( $atts['class'] ); ?>">
            <h2 class="title is-4">Популярні теми</h2>
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
        // Повертаємо сформований HTML
        return ob_get_clean();
    }
}

new MyBlockTheme_CategoryListShortcode();