<?php
/**
 * Динамічний блок «Коментарі»:
 * виводить стандартний шаблон comments.php тільки якщо
 * в налаштуваннях посту ввімкнені коментарі.
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
      // Якщо коментарі закриті і їх нема — нічого не виводимо
      if ( ! comments_open() && get_comments_number() === 0 ) {
          return '';
      }

      // Підтягуємо стандартний шаблон коментарів у буфер
      ob_start();
      comments_template();
      $html = ob_get_clean();

      // Обрізаємо посилання з <time>…</time>, залишаючи тільки дату «ДД.ММ.РРРР»
      $html = preg_replace(
          '/<a[^>]*><time[^>]*>((?:\d{1,2}\.){2}\d{4})(?:[^<]*)<\/time><\/a>/u',
          '$1',
          $html
      );

      return $html;
  }
}