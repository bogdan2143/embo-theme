<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handles head cleanup, removes unnecessary output and adjusts inline styles.
 */
class MyBlockTheme_Cleanup {

    public function __construct() {
        // Remove unnecessary actions in the head
        add_action( 'init', array( $this, 'remove_head_extras' ) );
        // Remove version parameters from style links
        add_filter( 'style_loader_tag', array( $this, 'remove_style_version' ), 10, 2 );
        // Remove .hentry class for logged-out users
        add_filter( 'post_class', array( $this, 'remove_hentry_class' ), 10, 3 );
        // Start output buffering for logged-out users
        add_action( 'template_redirect', array( $this, 'start_buffer' ) );
    }

    /**
     * Removes extra tags from the head.
     */
    public function remove_head_extras() {
        remove_action( 'wp_head', 'rsd_link' );                   // Remove RSD link
        remove_action( 'wp_head', 'wlwmanifest_link' );             // Remove Windows Live Writer manifest
        remove_action( 'wp_head', 'wp_generator' );                 // Remove WordPress version
        remove_action( 'wp_head', 'feed_links', 2 );                // Remove general RSS links
        remove_action( 'wp_head', 'feed_links_extra', 3 );          // Remove additional RSS links (comments etc.)
        // You can add other removals such as shortlinks or adjacent posts links
    }

    /**
     * Removes the version parameter from style tags.
     *
     * @param string $tag    HTML tag for the enqueued style.
     * @param string $handle Style handle.
     * @return string Modified HTML tag.
     */
    public function remove_style_version( $tag, $handle ) {
        // Strip version parameter (e.g., ?ver=6.7.2) using a regex
        $tag = preg_replace( "/ver=\d+(\.\d+){1,2}/", '', $tag );
        return $tag;
    }

    /**
     * Removes the .hentry class for logged-out users.
     *
     * @param array  $classes Post classes.
     * @param string $class   Additional classes.
     * @param int    $post_id Post ID.
     * @return array Modified class list.
     */
    public function remove_hentry_class( $classes, $class, $post_id ) {
            $classes = array_diff( $classes, array( 'hentry' ) );
        return $classes;
    }

    /**
     * Starts output buffering for logged-out users.
     */
    public function start_buffer() {
        // Use callback that combines inline styles and removes HTML comments
            ob_start( array( $this, 'combine_and_strip' ) );
    }

    /**
     * Callback for output buffering.
     * Combines inline styles and removes HTML comments (except conditional ones).
     *
     * @param string $buffer Full page output.
     * @return string Modified output.
     */
    public function combine_and_strip( $buffer ) {
        $buffer = $this->combine_inline_styles( $buffer );
        $buffer = preg_replace( '/<!--(?!\s*\[if).*?-->/', '', $buffer );
        $buffer = preg_replace( '/(\r?\n){2,}/', "\n", $buffer );
        return $buffer;
    }

    /**
     * Combines all inline <style> blocks into a single <style> without extra breaks.
     *
     * @param string $buffer Full HTML output.
     * @return string Modified HTML output.
     */
    public function combine_inline_styles( $buffer ) {
        // Regex searches for <style> blocks with ids ending in "-inline-css"
        $pattern = '/<style\b[^>]*\bid=[\'"](?P<id>[^\'"]+-inline-css)[\'"][^>]*>(?P<css>.*?)<\/style>/is';
        $combined_css = '';

        // Find all matching <style> blocks
        if ( preg_match_all( $pattern, $buffer, $matches, PREG_SET_ORDER ) ) {
            foreach ( $matches as $match ) {
                // Extract CSS trimming whitespace and line breaks
                $css_piece = trim( $match['css'] );
                // Append a comment with the identifier (no line breaks)
                $combined_css .= "/* {$match['id']} */ {$css_piece} ";
            }
            // Remove all found <style> blocks
            $buffer = preg_replace( $pattern, '', $buffer );
        }

        // If any CSS collected insert single <style> before </head>
        if ( $combined_css !== '' ) {
            // Remove excess whitespace
            $clean_css = preg_replace( '/\s+/', ' ', trim( $combined_css ) );
            // Build block without newlines or extra spaces
            $combined_block = "<style>{$clean_css}</style>";
            // Insert right before </head> without a new line
            $buffer = preg_replace( '/<\/head>/i', $combined_block . '</head>', $buffer, 1 );
        }

        return $buffer;
    }
}