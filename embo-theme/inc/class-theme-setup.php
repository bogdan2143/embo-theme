<?php
/**
 * Клас MyBlockTheme_Setup
 *
 * Реєструє базову ініціалізацію теми:
 * - перемикання редактора на класичний,
 * - налаштування підтримки теми (custom logo, мініатюри, меню, стартовий контент),
 * - створення та призначення домашньої сторінки,
 * - підключення стилів та скриптів.
 */

class MyBlockTheme_Setup {

    /**
     * Перемикає на класичний редактор з збереженням налаштувань теми.
     */
    public function switch_to_standard_editor() {
        // Вимикаємо підтримку блокових шаблонів
        remove_theme_support( 'block-templates' );
        // Вимикаємо блоковий редактор для окремих постів
        add_filter( 'use_block_editor_for_post', '__return_false' );
        // Вимикаємо блоковий редактор для типів записів
        add_filter( 'use_block_editor_for_post_type', '__return_false' );
        // Підключаємо стилі для класичного редактора (файл editor-style.css має бути у кореневій папці теми)
        add_editor_style( 'editor-style.css' );
    }

    /**
     * Налаштовує тему: підтримка блокових шаблонів, кастомного логотипу, мініатюр, меню, стартового контенту.
     */
    public function setup() {
        add_theme_support( 'block-templates' );
        add_theme_support( 'wp-block-styles' );
        add_theme_support( 'align-wide' );
        add_theme_support( 'responsive-embeds' );
        add_theme_support( 'editor-styles' );

        add_theme_support( 'custom-logo', array(
            'height'      => 100,
            'width'       => 400,
            'flex-height' => true,
            'flex-width'  => true,
        ) );
        add_theme_support( 'post-thumbnails' );

        // Реєструємо меню: primary та footer
        register_nav_menus( array(
            'primary' => __( 'Primary Menu', 'myblocktheme' ),
            'footer'  => __( 'Footer Menu', 'myblocktheme' ),
        ) );

        $starter_content = array(
            'posts' => array(
                'home' => array(
                    'post_type'    => 'page',
                    'post_title'   => __( 'Home', 'myblocktheme' ),
                    'post_content' => '<!-- wp:paragraph --><p>Ласкаво просимо на наш сайт!</p><!-- /wp:paragraph -->',
                ),
            ),
            'nav_menus' => array(
                'primary' => array(
                    'name'  => __( 'Primary Menu', 'myblocktheme' ),
                    'items' => array(
                        'link_home',
                        'page_about',
                        'page_contact',
                    ),
                ),
            ),
            'options' => array(
                'show_on_front' => 'page',
                'page_on_front' => '{{home}}',
            ),
        );
        add_theme_support( 'starter-content', $starter_content );
    }

    /**
     * Створює сторінку зі slug "home" та призначає її як головну (show_on_front = page).
     * Виконується один раз при активації теми.
     */
    public function create_and_assign_home_page() {
        $existing_front_page_id = get_option( 'page_on_front' );
        if ( $existing_front_page_id ) {
            return;
        }

        $home_page = get_page_by_path( 'home' );
        if ( ! $home_page ) {
            $page_id = wp_insert_post( array(
                'post_type'    => 'page',
                'post_name'    => 'home',
                'post_title'   => __( 'Home', 'myblocktheme' ),
                'post_content' => __( 'Ласкаво просимо на наш сайт!', 'myblocktheme' ),
                'post_status'  => 'publish',
            ) );
        } else {
            $page_id = $home_page->ID;
        }
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $page_id );
    }

    /**
     * Підключає стилі теми.
     *
     * Порядок підключення:
     * 1. Bulma (через CDN)
     * 2. Основний файл стилів теми (style.css)
     * 
     * Inline-стилі, що додаються плагіном через wp_add_inline_style('myblocktheme-style', ...),
     * автоматично додаються після style.css.
     */
    public function enqueue_styles() {
        wp_enqueue_style( 'bulma', 'https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css', array(), '0.9.4' );
        wp_enqueue_style( 'myblocktheme-style', get_stylesheet_uri(), array( 'bulma' ), '1.0' );
    }

    /**
     * Підключає скрипти теми з правильним розташуванням jQuery та jquery-migrate у футері.
     * Додає JS-код для роботи бургер-меню (перемикання класів is-active).
     */
    public function enqueue_scripts() {
        if ( ! is_admin() ) {
            // Перепідключаємо jQuery з параметром завантаження у футері.
            wp_deregister_script( 'jquery' );
            wp_register_script( 'jquery', includes_url( '/js/jquery/jquery.js' ), array(), null, true );
            wp_enqueue_script( 'jquery' );

            // Перепідключаємо jquery-migrate з параметром завантаження у футері.
            wp_deregister_script( 'jquery-migrate' );
            wp_register_script( 'jquery-migrate', includes_url( '/js/jquery/jquery-migrate.js' ), array( 'jquery' ), null, true );
            wp_enqueue_script( 'jquery-migrate' );
        }
        
        // Додаємо inline-скрипт для роботи бургер-меню
        add_action( 'wp_footer', function() {
            ?>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Знаходимо елемент бургер-меню
                var burger = document.querySelector('.navbar-burger');
                if (burger) {
                    burger.addEventListener('click', function() {
                        // Отримуємо значення data-target (наприклад, "navbarMain")
                        var targetId = burger.getAttribute('data-target');
                        var menu = document.getElementById(targetId);
                        // Тогл класу is-active для бургер-меню та цільового меню
                        burger.classList.toggle('is-active');
                        if (menu) {
                            menu.classList.toggle('is-active');
                        }
                    });
                }
            });
            </script>
            <?php
        });
    }
}

new MyBlockTheme_Setup();