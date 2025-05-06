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
    // Если комментарии закрыты и нет ни одного — ничего не выводим
    if ( ! comments_open() && get_comments_number() === 0 ) {
      return '';
    }

    // Иначе — подтягиваем стандартный шаблон WP
    ob_start();
    comments_template();
    return ob_get_clean();
  }
}

// Инициализируем
new MyBlockTheme_DynamicComments();