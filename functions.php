<?php
/**
 * Функціональна частина теми My Block Theme – ООП версія.
 *
 * Головний файл, що централізовано ініціалізує всі модулі.
 */

// Підключаємо модулі з каталогу inc
require_once get_template_directory() . '/inc/class-theme-setup.php';
require_once get_template_directory() . '/inc/class-block-patterns.php';
require_once get_template_directory() . '/inc/class-informer-shortcode.php';
require_once get_template_directory() . '/inc/class-dynamic-breadcrumbs.php';
require_once get_template_directory() . '/inc/class-ajax-load-more.php';
require_once get_template_directory() . '/inc/class-gutenberg-reset.php';

// Ініціалізуємо модулі
$theme_setup          = new MyBlockTheme_Setup();
$block_patterns       = new MyBlockTheme_BlockPatterns();
$informer_shortcode   = new MyBlockTheme_InformerShortcode();
$dynamic_breadcrumbs  = new MyBlockTheme_DynamicBreadcrumbs();
$ajax_load_more       = new MyBlockTheme_AjaxLoadMore();
$gutenberg_reset      = new MyBlockTheme_GutenbergReset();

// Централізована реєстрація хуків для модуля налаштування теми
add_action( 'after_setup_theme', array( $theme_setup, 'switch_to_standard_editor' ), 1 );
add_action( 'after_setup_theme', array( $theme_setup, 'setup' ) );
add_action( 'after_switch_theme', array( $theme_setup, 'create_and_assign_home_page' ) );
add_action( 'wp_enqueue_scripts', array( $theme_setup, 'enqueue_styles' ) );

// Реєстрація блокових патернів
add_action( 'init', array( $block_patterns, 'register_block_patterns' ) );

// Реєстрація шорткоду інформера
add_shortcode( 'informer', array( $informer_shortcode, 'informer_shortcode' ) );

// Реєстрація динамічного блоку "Breadcrumbs"
add_action( 'init', array( $dynamic_breadcrumbs, 'register_dynamic_breadcrumbs' ) );

// Реєстрація AJAX-завантаження постів
add_action( 'wp_enqueue_scripts', array( $ajax_load_more, 'enqueue_load_more_script' ) );
add_action( 'wp_ajax_myblocktheme_load_more', array( $ajax_load_more, 'load_more_posts' ) );
add_action( 'wp_ajax_nopriv_myblocktheme_load_more', array( $ajax_load_more, 'load_more_posts' ) );

// Реєстрація функцій скидання налаштувань Gutenberg
add_action( 'admin_notices', array( $gutenberg_reset, 'maybe_show_gutenberg_reset_notice' ) );
add_action( 'admin_init', array( $gutenberg_reset, 'handle_gutenberg_reset' ) );
add_action( 'after_switch_theme', array( $gutenberg_reset, 'set_reset_notice_flag' ) );