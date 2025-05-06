<?php
/**
 * Клас MyBlockTheme_CustomLogoBlock
 *
 * Реєструє динамічний блок для логотипу, який в першу чергу перевіряє налаштування плагіну (embo_branding_options),
 * а потім, якщо там порожньо, fallback на JSON-конфіг (theme.json). Якщо і там немає — виводить назву сайту.
 */

class MyBlockTheme_CustomLogoBlock {

    /**
     * Конструктор класу.
     */
    public function __construct() {
        // Реєструємо блок при ініціалізації
        add_action('init', array($this, 'register_custom_logo_block'));
    }

    /**
     * Функція реєстрації динамічного блоку.
     */
    public function register_custom_logo_block() {
        if ( function_exists('register_block_type') ) {
            register_block_type( 'myblocktheme/custom-logo', array(
                'render_callback' => array( $this, 'render_custom_logo' ),
            ) );
        }
    }
    
    /**
     * Функція рендерингу блоку логотипу.
     *
     * Послідовність:
     * 1) Якщо в опціях плагіна embo_branding_options['logo'] є URL - використовуємо його.
     * 2) Інакше перевіряємо theme.json -> logo
     * 3) Якщо обидва порожні, виводимо назву сайту.
     *
     * @param array  $attributes Атрибути блоку.
     * @param string $content    Вміст блоку.
     * @return string HTML розмітка для логотипу.
     */
    public function render_custom_logo( $attributes, $content ) {
        // Отримуємо URL головної сторінки
        $home_url = esc_url( home_url( '/' ) );

        // Спершу перевіряємо налаштування плагіна
        $branding_options = get_option( 'embo_branding_options', array( 'logo' => '' ) );
        $plugin_logo      = ! empty( $branding_options['logo'] ) ? esc_url( $branding_options['logo'] ) : '';

        if ( $plugin_logo ) {
            // Якщо є свій логотип у плагіні, виводимо його в посиланні на головну сторінку
            return sprintf(
                '<a class="custom-logo navbar-item" href="%s"><img src="%s" alt="%s"></a>',
                $home_url,
                $plugin_logo,
                esc_attr( get_bloginfo( 'name' ) )
            );
        }

        // Якщо у плагіні логотип порожній, fallback на theme.json
        $config_path = get_template_directory() . '/theme.json';
        $logo_url    = '';

        if ( file_exists( $config_path ) ) {
            // Зчитуємо дані конфігурації
            $config_data = json_decode( file_get_contents( $config_path ), true );
            // Перевіряємо, чи заданий ключ 'logo'
            if ( isset( $config_data['logo'] ) && ! empty( $config_data['logo'] ) ) {
                $logo_url = esc_url( $config_data['logo'] );
            }
        }
        
        if ( $logo_url ) {
            // Виводимо логотип із theme.json в посиланні на головну сторінку
            return sprintf(
                '<a class="custom-logo navbar-item" href="%s"><img src="%s" alt="%s"></a>',
                $home_url,
                $logo_url,
                esc_attr( get_bloginfo( 'name' ) )
            );
        } else {
            // Якщо нічого немає, виводимо назву сайту в посиланні на головну
            return sprintf(
                '<a class="site-title navbar-item" href="%s">%s</a>',
                $home_url,
                esc_html( get_bloginfo( 'name' ) )
            );
        }
    }
}

new MyBlockTheme_CustomLogoBlock();