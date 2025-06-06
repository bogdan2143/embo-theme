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
require_once get_template_directory() . '/inc/class-load-toggle.php';
require_once get_template_directory() . '/inc/class-ajax-load-more.php';
require_once get_template_directory() . '/inc/class-gutenberg-reset.php';
require_once get_template_directory() . '/inc/class-cleanup.php';
require_once get_template_directory() . '/inc/class-dynamic-menus.php';
require_once get_template_directory() . '/inc/class-custom-logo-block.php';
require_once get_template_directory() . '/inc/class-dynamic-comments.php';
require_once get_template_directory() . '/inc/class-category-list-shortcode.php';
require_once get_template_directory() . '/inc/class-post-tags-block.php';
require_once get_template_directory() . '/inc/class-related-posts-block.php';

// Ініціалізуємо модулі
$theme_setup          = new MyBlockTheme_Setup();
$block_patterns       = new MyBlockTheme_BlockPatterns();
$informer_shortcode   = new MyBlockTheme_InformerShortcode();
$dynamic_breadcrumbs  = new MyBlockTheme_DynamicBreadcrumbs();
$load_toggle          = new MyBlockTheme_LoadToggle();
$ajax_load_more       = new MyBlockTheme_AjaxLoadMore();
$gutenberg_reset      = new MyBlockTheme_GutenbergReset();
$cleanup              = new MyBlockTheme_Cleanup();
$dynamic_menus        = new MyBlockTheme_DynamicMenus();
$custom_logo_block    = new MyBlockTheme_CustomLogoBlock();
$dynamic_comments     = new MyBlockTheme_DynamicComments();

// Централізована реєстрація хуків для налаштування теми
add_action( 'after_setup_theme', array( $theme_setup, 'switch_to_standard_editor' ), 1 );
add_action( 'after_setup_theme', array( $theme_setup, 'setup' ), 10 );
add_action( 'after_switch_theme', array( $theme_setup, 'create_and_assign_home_page' ), 10 );

// Підключення стилів і скриптів
// Порядок: спочатку Bulma, потім style.css (enqueue_styles() MyBlockTheme_Setup)
// Inline-стилі, що додаються плагіном через wp_add_inline_style('myblocktheme-style', ...),
// автоматично підключаються після style.css.
add_action( 'wp_enqueue_scripts', array( $theme_setup, 'enqueue_styles' ), 10 );
add_action( 'wp_enqueue_scripts', array( $theme_setup, 'enqueue_scripts' ), 10 );

// Реєстрація блокових патернів
add_action( 'init', array( $block_patterns, 'register_block_patterns' ), 10 );

// Реєстрація шорткоду інформера
add_shortcode( 'informer', array( $informer_shortcode, 'informer_shortcode' ) );

// Реєстрація динамічного блоку "Breadcrumbs"
add_action( 'init', array( $dynamic_breadcrumbs, 'register_dynamic_breadcrumbs' ), 10 );

// Реєстрація AJAX‑завантаження постів
add_action( 'wp_enqueue_scripts', array( $ajax_load_more, 'enqueue_load_more_script' ), 10 );
add_action( 'wp_ajax_myblocktheme_load_more', array( $ajax_load_more, 'load_more_posts' ), 10 );
add_action( 'wp_ajax_nopriv_myblocktheme_load_more', array( $ajax_load_more, 'load_more_posts' ), 10 );

// Реєстрація функцій скидання налаштувань Gutenberg
add_action( 'admin_notices', array( $gutenberg_reset, 'maybe_show_gutenberg_reset_notice' ), 10 );
add_action( 'admin_init', array( $gutenberg_reset, 'handle_gutenberg_reset' ), 10 );
add_action( 'after_switch_theme', array( $gutenberg_reset, 'set_reset_notice_flag' ), 10 );