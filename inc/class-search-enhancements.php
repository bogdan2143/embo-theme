<?php
/**
 * Adjustments for search results display.
 *
 * Adds filters to improve the appearance of search results.
 *
 * @package MyBlockTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Customize search result behaviour.
 */
class MyBlockTheme_SearchEnhancements {

    /**
     * Maximum number of words to display in excerpts on search pages.
     *
     * @var int
     */
    private int $excerpt_length = 20;

    /**
     * Constructor hooks into WordPress filters.
     */
    public function __construct() {
        add_filter( 'excerpt_length', array( $this, 'reduce_excerpt_length' ), 20 );
        add_filter( 'get_the_excerpt', array( $this, 'trim_manual_excerpt' ), 20, 2 );
    }

    /**
     * Shorten excerpts specifically on search result pages.
     *
     * @param int $length Default excerpt length.
     *
     * @return int Modified length for search pages.
     */
    public function reduce_excerpt_length( $length ) {
        if ( is_search() ) {
            return $this->excerpt_length; // Display only a small snippet for search results.
        }

        return $length;
    }

    /**
     * Trim manual excerpts on search pages to the predefined length.
     *
     * @param string $excerpt The existing excerpt.
     * @param WP_Post $post   The post object.
     *
     * @return string Possibly trimmed excerpt.
     */
    public function trim_manual_excerpt( string $excerpt, $post ): string {
        if ( is_search() ) {
            return wp_trim_words( wp_strip_all_tags( $excerpt ), $this->excerpt_length );
        }

        return $excerpt;
    }

}
