<?php
/**
 * Embo Theme – Основные функции и определения
 */

if ( ! function_exists('embo_theme_setup') ) {
    function embo_theme_setup() {
        load_theme_textdomain('embo-theme', get_template_directory() . '/languages');
        add_theme_support('automatic-feed-links');
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_image_size('embo-auto-crop', 1200, 800, true);
        add_theme_support('custom-logo', array(
            'height'      => 100,
            'width'       => 400,
            'flex-height' => true,
            'flex-width'  => true,
        ));
        add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
        register_nav_menus(array(
            'top_menu'  => __('Головне меню', 'embo-theme'),
            'side_menu' => __('Бічне меню', 'embo-theme'),
        ));
    }
    add_action('after_setup_theme', 'embo_theme_setup');
}

/**
 * Подключение стилей и скриптов для фронтенда.
 */
function embo_theme_enqueue_scripts() {
    // Подключаем Bulma через functions.php (для фронтенда)
    wp_enqueue_style('bulma', 'https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css', array(), '0.9.4');
    wp_enqueue_style('tema-styl', get_stylesheet_uri(), array(), wp_get_theme()->get('Version'));
    
    // Подключаем динамические стили (шрифты, цвета)
    embo_theme_enqueue_dynamic_styles();
}
add_action('wp_enqueue_scripts', 'embo_theme_enqueue_scripts');

function embo_theme_enqueue_dynamic_styles() {
    $font_family   = get_option('mytheme_font_family', 'Roboto');
    $primary_color = get_option('mytheme_primary_color', '#3273dc');
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=' . urlencode($font_family) . ':wght@400;700&display=swap', false);
    $custom_css = "body { font-family: '{$font_family}', sans-serif; }
                   .primary-color { color: {$primary_color}; }
                   .primary-bg { background-color: {$primary_color}; }";
    wp_add_inline_style('tema-styl', $custom_css);
}

/**
 * Очистка head от лишних тегов.
 */
function embo_theme_cleanup_head() {
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
    remove_action('wp_head', 'wp_shortlink_wp_head', 10);
}
add_action('wp_head', 'embo_theme_cleanup_head');

/**
 * Добавление классов к body.
 */
function embo_theme_body_class($classes) {
    $classes[] = 'schema-webpage';
    return $classes;
}
add_filter('body_class', 'embo_theme_body_class');

/**
 * Регистрация динамических блоков.
 */
function embo_theme_register_blocks() {
    register_block_type('embo/theme-layout', array(
        'render_callback' => 'embo_theme_render_layout',
    ));
    register_block_type('embo/breadcrumbs', array(
        'render_callback' => 'embo_theme_render_breadcrumbs',
    ));
}
add_action('init', 'embo_theme_register_blocks');

function embo_theme_render_layout($attributes, $content) {
    $settings = get_option('embo_theme_settings');
    $block_layout = isset($settings['block_layout']) ? trim($settings['block_layout']) : '';
    if (empty($block_layout)) {
        return '';
    }
    $blocks = json_decode($block_layout, true);
    if (!is_array($blocks)) {
        return __('Помилка даних блоку.', 'embo-theme');
    }
    $output = '';
    foreach ($blocks as $block) {
        switch ($block['type']) {
            case 'header':
                $output .= '<!-- wp:template-part {"slug":"header"} /-->';
                break;
            case 'content':
                $output .= '<!-- wp:template-part {"slug":"content"} /-->';
                break;
            case 'sidebar':
                $output .= '<!-- wp:template-part {"slug":"sidebar"} /-->';
                break;
            case 'footer':
                $output .= '<!-- wp:template-part {"slug":"footer"} /-->';
                break;
            case 'column':
                $output .= '<!-- wp:template-part {"slug":"column"} /-->';
                break;
            case 'custom':
                $custom = isset($block['content']) ? $block['content'] : '';
                $output .= '<div class="custom-block">' . wp_kses_post($custom) . '</div>';
                break;
            default:
                break;
        }
    }
    return do_blocks($output);
}

function embo_theme_render_breadcrumbs($attributes, $content) {
    ob_start();
    if (function_exists('yoast_breadcrumb')) {
        yoast_breadcrumb('<p id="breadcrumbs">', '</p>');
    } else {
        echo '<a href="' . esc_url(home_url('/')) . '">' . __('Головна', 'embo-theme') . '</a> &raquo; ';
        if (is_singular()) {
            echo '<span>' . get_the_title() . '</span>';
        }
    }
    return ob_get_clean();
}

/**
 * Вывод инлайн-стилей с заменой CSS-переменных.
 */
function embo_theme_custom_css() {
    $options = get_option('embo_theme_settings');
    $primary    = isset($options['primary_color'])    ? $options['primary_color']    : '#3273dc';
    $secondary  = isset($options['secondary_color'])  ? $options['secondary_color']  : '#23d160';
    $body_bg    = isset($options['body_bg_color'])    ? $options['body_bg_color']    : '#ffffff';
    $header_bg  = isset($options['header_bg_color'])  ? $options['header_bg_color']  : '#3273dc';
    $footer_bg  = isset($options['footer_bg_color'])  ? $options['footer_bg_color']  : '#f5f5f5';
    $text_color = isset($options['text_color'])       ? $options['text_color']       : '#333333';
    echo "<style>
    :root {
        --wp--preset--color--primary: {$primary};
        --wp--preset--color--secondary: {$secondary};
        --wp--preset--color--background: {$body_bg};
        --wp--preset--color--header: {$header_bg};
        --wp--preset--color--footer: {$footer_bg};
        --wp--preset--color--text: {$text_color};
    }
    body {
        background-color: var(--wp--preset--color--background) !important;
        color: var(--wp--preset--color--text) !important;
    }
    </style>";
}
add_action('wp_head', 'embo_theme_custom_css', 100);