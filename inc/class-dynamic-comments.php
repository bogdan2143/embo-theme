<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Dynamic "Comments" block: outputs the default comments template only when
 * comments are enabled for the current post.
 */
class MyBlockTheme_DynamicComments {

  public function __construct() {
    add_action( 'init', [ $this, 'register_dynamic_comments_block' ] );
  }

  public function register_dynamic_comments_block() {
    register_block_type( 'myblocktheme/comments', [
      'apiVersion'      => 2,
      'render_callback' => [ $this, 'render_comments_block' ],
      'supports'        => [
        'html' => false,
      ],
    ] );
  }

  public function render_comments_block( $attributes ) {
      // If comments are closed and there are none, output nothing
      if ( ! comments_open() && get_comments_number() === 0 ) {
          return '';
      }

      // Capture the default comments template into a buffer
      ob_start();
      comments_template();
      $html = ob_get_clean();

      // Strip links from <time>…</time>, leaving only the date "DD.MM.YYYY"
      $html = preg_replace(
          '/<a[^>]*><time[^>]*>((?:\d{1,2}\.){2}\d{4})(?:[^<]*)<\/time><\/a>/u',
          '$1',
          $html
      );

      return $html;
  }
}