<?php
/**
 * Клас MyBlockTheme_BlockPatterns
 *
 * Реєструє блокові патерни для теми.
 */

class MyBlockTheme_BlockPatterns {

    /**
     * Реєструє блокові патерни.
     */
    public function register_block_patterns() {
        if ( function_exists( 'register_block_pattern' ) ) {
            // Головний інформер: вивід 4 останніх постів з виділенням першого поста
            register_block_pattern(
                'myblocktheme/main-informer',
                array(
                    'title'       => __( 'Головний інформер', 'myblocktheme' ),
                    'description' => __( 'Інформер для головної сторінки: вивід 4 останніх постів з виділенням першого поста.', 'myblocktheme' ),
                    'content'     => '
<!-- wp:group {"className":"informer-block"} -->
<div class="informer-block">
  <!-- wp:heading {"level":2,"className":"title is-4"} -->
  <h2 class="title is-4">Новини</h2>
  <!-- /wp:heading -->
  <!-- wp:columns -->
  <div class="wp-block-columns">
    <!-- wp:column {"width":"66.66%","className":"featured-item"} -->
    <div class="wp-block-column featured-item" style="flex-basis:66.66%">
      <!-- wp:query {"query":{"perPage":1,"postType":"post","order":"desc","orderBy":"date","taxQuery":[{"taxonomy":"category","field":"slug","terms":["news"]}]},"displayLayout":{"type":"list"}} -->
      <div class="wp-block-query">
        <!-- wp:post-template -->
          <!-- wp:post-featured-image {"isLink":true} /-->
          <!-- wp:post-title {"isLink":true} /-->
          <!-- wp:post-excerpt /-->
        <!-- /wp:post-template -->
      </div>
      <!-- /wp:query -->
    </div>
    <!-- /wp:column -->
    <!-- wp:column {"width":"33.33%"} -->
    <div class="wp-block-column" style="flex-basis:33.33%">
      <!-- wp:query {"query":{"offset":1,"perPage":3,"postType":"post","order":"desc","orderBy":"date","taxQuery":[{"taxonomy":"category","field":"slug","terms":["news"]}]},"displayLayout":{"type":"list"}} -->
      <div class="wp-block-query">
        <!-- wp:post-template -->
          <!-- wp:post-featured-image {"isLink":true} /-->
          <!-- wp:post-title {"isLink":true} /-->
          <!-- wp:post-excerpt {"className":"truncate"} /-->
        <!-- /wp:post-template -->
      </div>
      <!-- /wp:query -->
    </div>
    <!-- /wp:column -->
  </div>
  <!-- /wp:columns -->
  <!-- wp:group {"className":"has-text-left"} -->
  <div class="has-text-left">
    <!-- wp:button {"className":"is-link"} -->
    <div class="wp-block-button is-link">
      <a class="wp-block-button__link">Читати далі</a>
    </div>
    <!-- /wp:button -->
  </div>
  <!-- /wp:group -->
</div>
<!-- /wp:group -->
',
                )
            );

            // Боковий інформер (таймлайн)
            register_block_pattern(
                'myblocktheme/aside-informer',
                array(
                    'title'       => __( 'Боковий інформер', 'myblocktheme' ),
                    'description' => __( 'Інформер для бокової області: вивід постів у хронологічному порядку.', 'myblocktheme' ),
                    'content'     => '
<!-- wp:query {"query":{"perPage":5,"postType":"post","order":"desc","orderBy":"date"},"displayLayout":{"type":"list"}} -->
<div class="wp-block-query">
    <!-- wp:post-template -->
      <!-- wp:post-title {"isLink":true} /-->
      <!-- wp:post-date {"format":"F j, Y"} /-->
    <!-- /wp:post-template -->
    <!-- wp:query-pagination /-->
</div>
<!-- /wp:query -->
',
                )
            );

            // Інформер списку тегів
            register_block_pattern(
                'myblocktheme/tag-list-informer',
                array(
                    'title'       => __( 'Інформер списку тегів', 'myblocktheme' ),
                    'description' => __( 'Інформер для виводу списку тегів, який можна редагувати вручну.', 'myblocktheme' ),
                    'content'     => '
<!-- wp:group {"className":"tags-area"} -->
<div class="wp-block-group tags-area">
    <div class="tags">
        <span class="tag is-info">Тег 1</span>
        <span class="tag is-info">Тег 2</span>
        <span class="tag is-info">Тег 3</span>
    </div>
</div>
<!-- /wp:group -->
',
                )
            );
        }
    }
}