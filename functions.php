<?php
/**
 * Core functionality of the My Block Theme (OOP version).
 *
 * This is the main file that loads and initializes all modules.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Load modules from the inc directory
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
require_once get_template_directory() . '/inc/class-template-translations.php';
require_once get_template_directory() . '/inc/class-plugin-installer.php';

// Instantiate modules
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
$template_translations = new MyBlockTheme_TemplateTranslations();
$plugin_installer     = new MyBlockTheme_PluginInstaller();

// Centralized hook registration for theme setup
add_action( 'after_setup_theme', array( $theme_setup, 'switch_to_standard_editor' ), 1 );
add_action( 'after_setup_theme', array( $theme_setup, 'load_textdomain' ), 5 );
add_action( 'after_setup_theme', array( $theme_setup, 'setup' ), 10 );
add_action( 'after_switch_theme', array( $theme_setup, 'create_and_assign_home_page' ), 10 );
add_action( 'after_switch_theme', array( $theme_setup, 'update_template_part_areas' ), 10 );

// Enqueue styles and scripts in the proper order
// Bulma first, then style.css (enqueue_styles() in MyBlockTheme_Setup)
// Inline styles added via wp_add_inline_style('myblocktheme-style', ...) are appended automatically
add_action( 'wp_enqueue_scripts', array( $theme_setup, 'enqueue_styles' ), 10 );
add_action( 'wp_enqueue_scripts', array( $theme_setup, 'enqueue_scripts' ), 10 );

// Register block patterns
add_action( 'init', array( $block_patterns, 'register_block_patterns' ), 10 );

// Register informer shortcode
add_shortcode( 'informer', array( $informer_shortcode, 'informer_shortcode' ) );

// Register the dynamic "Breadcrumbs" block
add_action( 'init', array( $dynamic_breadcrumbs, 'register_dynamic_breadcrumbs' ), 10 );

// Register AJAX loading of posts
add_action( 'wp_enqueue_scripts', array( $ajax_load_more, 'enqueue_load_more_script' ), 10 );
add_action( 'wp_ajax_myblocktheme_load_more', array( $ajax_load_more, 'load_more_posts' ), 10 );
add_action( 'wp_ajax_nopriv_myblocktheme_load_more', array( $ajax_load_more, 'load_more_posts' ), 10 );

// Register Gutenberg reset functionality
add_action( 'admin_notices', array( $gutenberg_reset, 'maybe_show_gutenberg_reset_notice' ), 10 );
add_action( 'admin_init', array( $gutenberg_reset, 'handle_gutenberg_reset' ), 10 );add_action( 'after_switch_theme', array( $gutenberg_reset, 'set_reset_notice_flag' ), 10 );
