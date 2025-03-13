<?php
function mytheme_add_dynamic_styles() {
    // Получаем сохранённые настройки или используем значения по умолчанию.
    $font_family   = get_option( 'mytheme_font_family', 'Roboto' );
    $primary_color = get_option( 'mytheme_primary_color', '#3273dc' );

    // Подключаем Google Fonts для выбранного шрифта.
    wp_enqueue_style(
        'google-fonts',
        'https://fonts.googleapis.com/css2?family=' . urlencode( $font_family ) . ':wght@400;700&display=swap',
        false
    );

    // Генерируем динамический CSS.
    $custom_css = "
        body {
            font-family: '{$font_family}', sans-serif;
        }
        .primary-color {
            color: {$primary_color};
        }
        .primary-bg {
            background-color: {$primary_color};
        }
    ";
    wp_add_inline_style( 'theme-style', $custom_css );
}
add_action( 'wp_enqueue_scripts', 'mytheme_add_dynamic_styles' );