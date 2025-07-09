<?php
/**
 * Class MyBlockTheme_TemplateTranslations
 *
 * Replaces i18n placeholders like {{__( 'Text', 'domain' )}} in block templates
 * with their translated values. WordPress normally handles this automatically,
 * but some environments may skip parsing, leaving placeholders visible. This
 * filter ensures translations are applied at render time.
 */
class MyBlockTheme_TemplateTranslations {

    /**
     * Register the render filter on construction.
     */
    public function __construct() {
        add_filter( 'render_block', array( $this, 'replace_placeholders' ), 10, 2 );
    }

    /**
     * Replaces all {{__( 'Text', 'domain' )}} occurrences with translated strings.
     *
     * @param string $block_content Rendered block HTML.
     * @param array  $block        Block data.
     * @return string Filtered HTML with translations.
     */
    public function replace_placeholders( $block_content, $block ) {
        if ( strpos( $block_content, '{{__' ) === false ) {
            return $block_content;
        }

        // Match both raw and HTML entity encoded quotes around the strings.
        $quote   = '(?:\\"|\\\'|&quot;|&#039;)';
        $pattern = '/\{\{__\(\s*' . $quote . '(.+?)' . $quote . '\s*,\s*' . $quote . '(.+?)' . $quote . '\s*\)\}\}/u';

        return preg_replace_callback(
            $pattern,
            function ( $matches ) {
                return __( $matches[1], $matches[2] );
            },
            $block_content
        );
    }
}
