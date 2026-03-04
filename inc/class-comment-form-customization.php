<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Customizes default comment form markup.
 */
class MyBlockTheme_CommentFormCustomization {

    /**
     * Replace heading tags for the reply title with semantic non-heading wrappers.
     *
     * @param array<string, mixed> $defaults Comment form defaults.
     *
     * @return array<string, mixed>
     */
    public function customize_reply_title_wrapper( $defaults ) {
        if ( is_admin() ) {
            return $defaults;
        }

        $defaults['title_reply_before'] = '<p id="reply-title" class="comment-reply-title">';
        $defaults['title_reply_after']  = '</p>';

        return $defaults;
    }
}
