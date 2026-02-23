<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Replaces i18n placeholders like {{__( 'Text', 'domain' )}} in block templates
 * with their translated values. WordPress normally handles this automatically,
 * but some environments may skip parsing, leaving placeholders visible. This
 * filter ensures translations are applied at render time.
 */
class MyBlockTheme_TemplateTranslations {

    /**
     * Regex for i18n placeholders inside template HTML/attributes.
     * Supports plain quotes and HTML encoded quote entities.
     */
    private const PLACEHOLDER_PATTERN = '/\{\{(__|esc_html__|esc_attr__)\(\s*(?:"|\'|&quot;|&#039;)(.+?)(?:"|\'|&quot;|&#039;)\s*,\s*(?:"|\'|&quot;|&#039;)(.+?)(?:"|\'|&quot;|&#039;)\s*\)\}\}/u';

    /**
     * Register the render filter on construction.
     */
    public function __construct() {
        add_filter( 'render_block_data', array( $this, 'translate_block_data' ), 10, 1 );
        add_filter( 'render_block', array( $this, 'replace_placeholders' ), 10, 2 );
    }

    /**
     * Translates placeholders inside block attributes before block rendering.
     *
     * @param array $parsed_block Parsed block data.
     * @return array
     */
    public function translate_block_data( $parsed_block ) {
        if ( ! is_array( $parsed_block ) ) {
            return $parsed_block;
        }

        if ( isset( $parsed_block['attrs'] ) && is_array( $parsed_block['attrs'] ) ) {
            $parsed_block['attrs'] = $this->translate_recursive( $parsed_block['attrs'] );
        }

        return $parsed_block;
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

        return $this->translate_string( $block_content );
    }

    /**
     * Recursively translates placeholders in nested arrays/strings.
     *
     * @param mixed $value Value to translate.
     * @return mixed
     */
    private function translate_recursive( $value ) {
        if ( is_array( $value ) ) {
            foreach ( $value as $key => $item ) {
                $value[ $key ] = $this->translate_recursive( $item );
            }

            return $value;
        }

        if ( ! is_string( $value ) || strpos( $value, '{{__' ) === false ) {
            return $value;
        }

        return $this->translate_string( $value );
    }

    /**
     * Replaces all supported i18n placeholders in a string.
     *
     * @param string $content Source string.
     * @return string
     */
    private function translate_string( $content ) {
        return preg_replace_callback(
            self::PLACEHOLDER_PATTERN,
            function ( $matches ) {
                if ( $matches[1] === 'esc_html__' ) {
                    return esc_html__( $matches[2], $matches[3] );
                }

                if ( $matches[1] === 'esc_attr__' ) {
                    return esc_attr__( $matches[2], $matches[3] );
                }

                return __( $matches[2], $matches[3] );
            },
            $content
        );
    }
}
