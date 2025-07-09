<?php
/**
 * Class MyBlockTheme_Setup
 *
 * Registers the basic theme initialization:
 * - switches to the classic editor
 * - sets up theme supports (custom logo, thumbnails, menus, starter content)
 * - creates and assigns the front page
 * - enqueues styles and scripts
 */

class MyBlockTheme_Setup {

    /**
     * Switches to the classic editor while preserving theme settings.
     */
    public function switch_to_standard_editor() {
        // Disable block templates support
        remove_theme_support( 'block-templates' );
        // Disable block editor for individual posts
        add_filter( 'use_block_editor_for_post', '__return_false' );
        // Disable block editor for post types
        add_filter( 'use_block_editor_for_post_type', '__return_false' );
        // Enqueue styles for the classic editor (editor-style.css in theme root)
        add_editor_style( 'editor-style.css' );
    }

    /**
     * Registers custom informer image sizes: 326×242 and 120×90.
     */
    public function register_image_sizes() {
        // Large informer image with hard crop centered
        add_image_size( 'informer_featured', 326, 242, true );
        // Small informer image with hard crop centered
        add_image_size( 'informer_small',    120,  90,  true );
    }

    /**
     * Sets up theme supports: block templates, custom logo, thumbnails, menus and starter content.
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

        // Register custom sizes for informer thumbnails
        $this->register_image_sizes();

        // Register menus: primary and footer
        register_nav_menus( array(
            'primary' => __( 'Primary Menu', 'myblocktheme' ),
            'footer'  => __( 'Footer Menu', 'myblocktheme' ),
        ) );

        $starter_content = array(
            'posts' => array(
                'home' => array(
                    'post_type'    => 'page',
                    'post_title'   => __( 'Home', 'myblocktheme' ),
                    'post_content' => sprintf(
                        '<!-- wp:paragraph --><p>%s</p><!-- /wp:paragraph -->',
                        __( 'Ласкаво просимо на наш сайт!', 'myblocktheme' )
                    ),
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
     * Creates a page with the slug "home" and assigns it as the front page.
     * Runs once on theme activation.
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
     * Enqueues theme styles.
     *
     * Loading order:
     * 1. Bulma (via CDN)
     * 2. Main theme stylesheet (style.css)
     * Inline styles added via wp_add_inline_style('myblocktheme-style', ...) are appended after style.css.
     */
    public function enqueue_styles() {
        wp_enqueue_style( 'bulma', 'https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css', array(), '0.9.4' );
        wp_enqueue_style( 'myblocktheme-style', get_stylesheet_uri(), array( 'bulma' ), '1.0' );
    }

    /**
     * Enqueues theme scripts placing jQuery and jquery-migrate in the footer.
     * Adds JS code for burger menu toggling (is-active classes).
     */
    public function enqueue_scripts() {
        if ( ! is_admin() ) {
            // Re-register jQuery to load in the footer
            wp_deregister_script( 'jquery' );
            wp_register_script( 'jquery', includes_url( '/js/jquery/jquery.js' ), array(), null, true );
            wp_enqueue_script( 'jquery' );

            // Re-register jquery-migrate to load in the footer
            wp_deregister_script( 'jquery-migrate' );
            wp_register_script( 'jquery-migrate', includes_url( '/js/jquery/jquery-migrate.js' ), array( 'jquery' ), null, true );
            wp_enqueue_script( 'jquery-migrate' );

            // Enqueue our breakpoint utility
            wp_enqueue_script(
                'screen-utils',
                get_template_directory_uri() . '/src/js/screen-utils.js',
                [],               // no dependencies
                '1.0',            // utility version
                true              // in the footer
            );

            // Enqueue script to toggle the search field
            wp_enqueue_script(
              'myblocktheme-search-toggle',
              get_template_directory_uri() . '/src/js/header-ui.js',
              [ 'screen-utils' ],
              '1.0',
              true
            );
        }

        // Add inline script for burger menu
        add_action( 'wp_footer', function() {
            ?>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Locate the burger element
                var burger = document.querySelector('.navbar-burger');
                if (burger) {
                    burger.addEventListener('click', function() {
                        // Get the data-target value (e.g., "navbarMain")
                        var targetId = burger.getAttribute('data-target');
                        var menu = document.getElementById(targetId);
                        // Toggle is-active class on burger and target menu
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