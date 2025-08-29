<?php
/**
 * Server-side rendering for the Search Heading block.
 *
 * @package MyBlockTheme
 */

return function( $attributes, $content ) {
    return MyBlockTheme_SearchEnhancements::get_search_heading();
};
