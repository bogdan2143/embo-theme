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
     * Constructor hooks into WordPress filters.
     */
    public function __construct() {
        add_filter( 'excerpt_length', array( $this, 'reduce_excerpt_length' ), 20 );
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
            return 20; // Display only a small snippet for search results.
        }

        return $length;
    }

}
