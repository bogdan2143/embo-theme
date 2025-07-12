<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Registers dynamic blocks for header and footer menus. The menus are managed
 * through the standard WordPress menu editor. The Bulma walker is encapsulated
 * inside this class.
 */
class MyBlockTheme_DynamicMenus {

    /**
     * Initialization: register dynamic blocks.
     */
    public function __construct() {
        add_action( 'init', array( $this, 'register_dynamic_menus' ) );
        add_filter( 'render_block_core/search', array( $this, 'replace_search_button_with_svg' ), 10, 2 );
    }

    /**
     * Registers dynamic blocks for header and footer menus.
     */
    public function register_dynamic_menus() {
        // Dynamic block for header menu
        register_block_type( 'myblocktheme/header-menu', array(
            'render_callback' => array( $this, 'render_header_menu' ),
            'attributes'      => array(),
        ) );

        // Dynamic block for footer menu
        register_block_type( 'myblocktheme/footer-menu', array(
            'render_callback' => array( $this, 'render_footer_menu' ),
            'attributes'      => array(),
        ) );
    }

    /**
     * Output header menu (theme_location = 'primary') using a custom Walker.
     *
     * @param array  $attributes Block attributes.
     * @param string $content    Block content.
     * @return string Menu HTML.
     */
    public function render_header_menu( $attributes, $content ) {
        if ( ! has_nav_menu( 'primary' ) ) {
            return '';
        }
        $menu_html = wp_nav_menu( array(
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'header-menu',
            'items_wrap'     => '%3$s',  // Do not wrap in <ul>
            'depth'          => 2,       // Support submenu items
            'echo'           => false,
            'walker'         => self::get_bulma_walker(),
        ) );
        return $menu_html;
    }

    /**
     * Output footer menu (theme_location = 'footer') as a simple flex row.
     *
     * @param array  $attributes Block attributes.
     * @param string $content    Block content.
     * @return string Menu HTML.
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
     * Returns an instance of a custom Bulma-style Walker.
     *
     * Uses an anonymous class to avoid a separate file.
     *
     * @return Walker_Nav_Menu
     */
    private static function get_bulma_walker() {
        return new class extends Walker_Nav_Menu {

            /**
             * Start of submenu level.
             *
             * @param string $output Menu HTML output.
             * @param int    $depth  Depth level.
             * @param array  $args   Menu args.
             */
            public function start_lvl( &$output, $depth = 0, $args = array() ) {
                $indent = str_repeat("\t", $depth);
                $output .= "\n$indent<div class=\"navbar-dropdown\">\n";
            }

            /**
             * End of submenu level.
             *
             * @param string $output Menu HTML output.
             * @param int    $depth  Depth level.
             * @param array  $args   Menu args.
             */
            public function end_lvl( &$output, $depth = 0, $args = array() ) {
                $indent = str_repeat("\t", $depth);
                $output .= "$indent</div>\n";
            }

            /**
             * Start of menu item.
             *
             * @param string $output Menu HTML output.
             * @param object $item   Menu item object.
             * @param int    $depth  Depth level.
             * @param array  $args   Menu args.
             * @param int    $id     Item ID.
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
                    // Top level with submenu
                    $output .= $indent . '<div class="navbar-item has-dropdown is-hoverable">';
                    $output .= '<a class="navbar-link"' . $attributes . '>';
                    $output .= $title;
                    $output .= '</a>';
                } elseif ( $depth === 0 ) {
                    // Top level without submenu
                    $output .= $indent . '<a class="navbar-item"' . $attributes . '>';
                    $output .= $title;
                    $output .= '</a>';
                } else {
                    // Submenu items
                    $output .= $indent . '<a class="navbar-item"' . $attributes . '>';
                    $output .= $title;
                    $output .= '</a>';
                }
            }

            /**
             * End of menu item.
             *
             * @param string $output Menu HTML output.
             * @param object $item   Menu item object.
             * @param int    $depth  Depth level.
             * @param array  $args   Menu args.
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
     * Replaces the search button text with an SVG icon.
     *
     * @param string $block_content Generated block HTML.
     * @param array  $block         Block data.
     * @return string Modified HTML.
     */
    public function replace_search_button_with_svg( $block_content, $block ) {
        // SVG icon for the search button
        $svg = '<svg width="96" height="96" viewBox="0 0 96 96" xmlns="http://www.w3.org/2000/svg">
            <circle cx="40" cy="40" r="28" fill="none" stroke="#ffffff" stroke-width="9" />
            <line x1="59.8" y1="59.8" x2="79.6" y2="79.6" stroke="#ffffff" stroke-width="9" stroke-linecap="round" />
        </svg>';

        // Replace button text with SVG
        $block_content = preg_replace('/(<button[^>]*>).*?(<\/button>)/', '$1' . $svg . '$2', $block_content);

        return $block_content;
    }
}