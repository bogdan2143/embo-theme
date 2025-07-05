<?php
/**
 * Клас MyBlockTheme_DynamicMenus
 *
 * Реєструє динамічні блоки для меню шапки (header) та меню футера (footer).
 * Меню керуються через стандартний редактор меню WordPress.
 * Волкер для адаптації меню під Bulma інкапсульовано всередині цього класу.
 */

class MyBlockTheme_DynamicMenus {

    /**
     * Ініціалізація: реєстрація динамічних блоків.
     */
    public function __construct() {
        add_action( 'init', array( $this, 'register_dynamic_menus' ) );
        add_filter( 'render_block_core/search', array( $this, 'replace_search_button_with_svg' ), 10, 2 );
    }

    /**
     * Реєструє динамічні блоки для меню шапки та футера.
     */
    public function register_dynamic_menus() {
        // Динамічний блок для меню шапки
        register_block_type( 'myblocktheme/header-menu', array(
            'render_callback' => array( $this, 'render_header_menu' ),
            'attributes'      => array(),
        ) );

        // Динамічний блок для меню футера
        register_block_type( 'myblocktheme/footer-menu', array(
            'render_callback' => array( $this, 'render_footer_menu' ),
            'attributes'      => array(),
        ) );
    }

    /**
     * Вивід меню шапки (theme_location = 'primary') з використанням кастомного Walker.
     *
     * @param array  $attributes Атрибути блоку.
     * @param string $content    Зміст блоку.
     * @return string HTML розмітка меню.
     */
    public function render_header_menu( $attributes, $content ) {
        if ( ! has_nav_menu( 'primary' ) ) {
            return '';
        }
        $menu_html = wp_nav_menu( array(
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'header-menu',
            'items_wrap'     => '%3$s',  // Не обгортаємо у <ul>
            'depth'          => 2,       // Підтримка вкладених пунктів
            'echo'           => false,
            'walker'         => self::get_bulma_walker(),
        ) );
        return $menu_html;
    }

    /**
     * Вивід меню футера (theme_location = 'footer') як простого flex‑рядка.
     *
     * @param array  $attributes Атрибути блоку.
     * @param string $content    Зміст блоку.
     * @return string HTML розмітка меню.
     */
    public function render_footer_menu( $attributes, $content ) {
        if ( ! has_nav_menu( 'footer' ) ) {
            return '';
        }

        $items = wp_nav_menu( array(
            'theme_location' => 'footer',
            'container'      => false,
            'items_wrap'     => '%3$s',
            'depth'          => 1,
            'echo'           => false,
            'walker'         => self::get_bulma_walker(),
        ) );

        return '<div class="footer-menu">' . $items . '</div>';
    }

    /**
     * Повертає екземпляр кастомного Walker для меню в стилі Bulma.
     *
     * Використовуємо анонімний клас, щоб не потребувати окремого файлу.
     *
     * @return Walker_Nav_Menu
     */
    private static function get_bulma_walker() {
        return new class extends Walker_Nav_Menu {

            /**
             * Початок рівня підменю.
             *
             * @param string $output HTML-розмітка.
             * @param int    $depth  Рівень вкладеності.
             * @param array  $args   Аргументи меню.
             */
            public function start_lvl( &$output, $depth = 0, $args = array() ) {
                $indent = str_repeat("\t", $depth);
                $output .= "\n$indent<div class=\"navbar-dropdown\">\n";
            }

            /**
             * Кінець рівня підменю.
             *
             * @param string $output HTML-розмітка.
             * @param int    $depth  Рівень вкладеності.
             * @param array  $args   Аргументи меню.
             */
            public function end_lvl( &$output, $depth = 0, $args = array() ) {
                $indent = str_repeat("\t", $depth);
                $output .= "$indent</div>\n";
            }

            /**
             * Початок елемента меню.
             *
             * @param string $output HTML-розмітка.
             * @param object $item   Об'єкт пункту меню.
             * @param int    $depth  Рівень вкладеності.
             * @param array  $args   Аргументи меню.
             * @param int    $id     Ідентифікатор пункту.
             */
            public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
                $indent = ($depth) ? str_repeat("\t", $depth) : '';

                $classes   = empty( $item->classes ) ? array() : (array)$item->classes;
                $has_child = in_array( 'menu-item-has-children', $classes, true );

                $attributes = '';
                if ( ! empty( $item->url ) ) {
                    $attributes .= ' href="' . esc_attr( $item->url ) . '"';
                }

                $title = apply_filters( 'the_title', $item->title, $item->ID );

                if ( $depth === 0 && $has_child ) {
                    // Верхній рівень з підменю
                    $output .= $indent . '<div class="navbar-item has-dropdown is-hoverable">';
                    $output .= '<a class="navbar-link"' . $attributes . '>';
                    $output .= $title;
                    $output .= '</a>';
                } elseif ( $depth === 0 ) {
                    // Верхній рівень без підменю
                    $output .= $indent . '<a class="navbar-item"' . $attributes . '>';
                    $output .= $title;
                    $output .= '</a>';
                } else {
                    // Пункти підменю
                    $output .= $indent . '<a class="navbar-item"' . $attributes . '>';
                    $output .= $title;
                    $output .= '</a>';
                }
            }

            /**
             * Кінець елемента меню.
             *
             * @param string $output HTML-розмітка.
             * @param object $item   Об'єкт пункту меню.
             * @param int    $depth  Рівень вкладеності.
             * @param array  $args   Аргументи меню.
             */
            public function end_el( &$output, $item, $depth = 0, $args = array() ) {
                $has_child = in_array( 'menu-item-has-children', (array)$item->classes, true );
                if ( $depth === 0 && $has_child ) {
                    $output .= "</div>\n";
                } else {
                    $output .= "\n";
                }
            }
        };
    }

    /**
     * Замінює текст кнопки пошуку на SVG-іконку.
     *
     * @param string $block_content Згенерований HTML вміст блоку.
     * @param array  $block         Дані блоку.
     * @return string Модифікований HTML.
     */
    public function replace_search_button_with_svg( $block_content, $block ) {
        // SVG-іконка лупи
        $svg = '<svg width="96" height="96" viewBox="0 0 96 96" xmlns="http://www.w3.org/2000/svg">
            <circle cx="40" cy="40" r="28" fill="none" stroke="#ffffff" stroke-width="9" />
            <line x1="59.8" y1="59.8" x2="79.6" y2="79.6" stroke="#ffffff" stroke-width="9" stroke-linecap="round" />
        </svg>';

        // Заміна тексту всередині кнопки на SVG
        $block_content = preg_replace('/(<button[^>]*>).*?(<\/button>)/', '$1' . $svg . '$2', $block_content);

        return $block_content;
    }
}
