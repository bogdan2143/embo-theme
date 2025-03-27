<?php
/**
 * Функциональная часть темы My Block Theme
 */

function myblocktheme_setup() {
    // Поддержка блоковых шаблонов и FSE
    add_theme_support( 'block-templates' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'editor-styles' );
    
    // Поддержка кастомного логотипа (логотип – ссылка на главную страницу)
    add_theme_support( 'custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    
    // Поддержка миниатюр записей
    add_theme_support( 'post-thumbnails' );
    
    // Регистрация меню – оно будет настраиваться через стандартный функционал WordPress
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'myblocktheme' ),
    ));

    // Стартовый контент для автоматического наполнения при активации темы
    $starter_content = array(
        'posts' => array(
            'home' => array(
                'post_type'    => 'page',
                'post_title'   => __( 'Home', 'myblocktheme' ),
                'post_content' => '<!-- wp:paragraph --><p>Добро пожаловать на наш сайт!</p><!-- /wp:paragraph -->',
            ),
        ),
        'nav_menus' => array(
            'primary' => array(
                'name'  => __( 'Primary Menu', 'myblocktheme' ),
                'items' => array(
                    'link_home',
                    'page_about',
                    'page_contact',
                ),
            ),
        ),
        'options' => array(
            'show_on_front' => 'page',
            'page_on_front' => '{{home}}',
        ),
    );
    add_theme_support( 'starter-content', $starter_content );
}
add_action( 'after_setup_theme', 'myblocktheme_setup' );

/**
 * Подключение стилей: Bulma (из CDN) и основного файла стилей темы.
 */
function myblocktheme_enqueue_styles() {
    wp_enqueue_style( 'bulma', 'https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css', array(), '0.9.4' );
    wp_enqueue_style( 'myblocktheme-style', get_stylesheet_uri(), array('bulma'), '1.0' );
}
add_action( 'wp_enqueue_scripts', 'myblocktheme_enqueue_styles' );

/**
 * Регистрация блоковых паттернов (информеров)
 */
function myblocktheme_register_block_patterns() {
    if ( function_exists( 'register_block_pattern' ) ) {
        // Главный информер: вывод 4 последних постов с выделением первого поста
        register_block_pattern(
            'myblocktheme/main-informer',
            array(
                'title'       => __( 'Главный информер', 'myblocktheme' ),
                'description' => __( 'Информер для главной страницы: вывод 4 последних постов с выделением первого поста.', 'myblocktheme' ),
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

        // Боковой информер (таймлайн): вывод постов в хронологическом порядке
        register_block_pattern(
            'myblocktheme/aside-informer',
            array(
                'title'       => __( 'Боковой информер', 'myblocktheme' ),
                'description' => __( 'Информер для боковой области: вывод постов в хронологическом порядке.', 'myblocktheme' ),
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

        // Информер списка тегов: вывод тегов (редактируемый вручную)
        register_block_pattern(
            'myblocktheme/tag-list-informer',
            array(
                'title'       => __( 'Информер списка тегов', 'myblocktheme' ),
                'description' => __( 'Информер для вывода списка тегов, который можно редактировать вручную.', 'myblocktheme' ),
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
add_action( 'init', 'myblocktheme_register_block_patterns' );

/**
 * Регистрация шорткода для информера.
 * Используйте [informer category="news" per_page="4" button_text="Перейти в %s"] в редакторе контента.
 */
function myblocktheme_informer_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'category'    => 'news',
        'per_page'    => 4,
        'button_text' => __( 'Читати далі', 'myblocktheme' ), // Если содержит %s, заменим на название категории.
    ), $atts, 'informer' );

    // Первый запрос: выводим один пост (featured)
    $query_featured = new WP_Query( array(
        'posts_per_page' => 1,
        'post_type'      => 'post',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'tax_query'      => array(
            array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => $atts['category'],
            ),
        ),
    ) );

    // Второй запрос: выводим оставшиеся (per_page - 1) постов с offset 1
    $query_small = new WP_Query( array(
        'posts_per_page' => intval( $atts['per_page'] ) - 1,
        'offset'         => 1,
        'post_type'      => 'post',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'tax_query'      => array(
            array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => $atts['category'],
            ),
        ),
    ) );

    ob_start();
    ?>
    <div class="informer-block">
      <h2 class="title is-4"><?php _e( 'Новини', 'myblocktheme' ); ?></h2>
      <div class="columns">
        <!-- Featured пост -->
        <div class="column is-two-thirds featured-item">
          <?php if ( $query_featured->have_posts() ) : $query_featured->the_post(); ?>
            <figure class="image is-4by3">
              <?php
              if ( has_post_thumbnail() ) {
                  the_post_thumbnail( 'large' );
              } else {
                  echo '<img src="https://via.placeholder.com/600x400" alt="' . esc_attr__( 'Основна новина', 'myblocktheme' ) . '">';
              }
              ?>
            </figure>
            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <div class="excerpt"><?php the_excerpt(); ?></div>
          <?php endif; wp_reset_postdata(); ?>
        </div>
        <!-- Маленькие посты -->
        <div class="column is-one-third">
          <?php if ( $query_small->have_posts() ) : ?>
            <?php while ( $query_small->have_posts() ) : $query_small->the_post(); ?>
              <div class="small-item">
                <figure class="image is-4by3">
                  <?php
                  if ( has_post_thumbnail() ) {
                      the_post_thumbnail( 'medium' );
                  } else {
                      echo '<img src="https://via.placeholder.com/300x200" alt="' . esc_attr__( 'Новина', 'myblocktheme' ) . '">';
                  }
                  ?>
                </figure>
                <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                <p class="truncate" title="<?php echo esc_attr( get_the_excerpt() ); ?>"><?php the_excerpt(); ?></p>
              </div>
            <?php endwhile; wp_reset_postdata(); ?>
          <?php endif; ?>
        </div>
      </div>
      <div class="has-text-left">
        <?php 
        $cat_id = get_cat_ID( $atts['category'] );
        if ( $cat_id ) {
            $cat_link = get_category_link( $cat_id );
            $cat_name = get_cat_name( $cat_id );
            if ( strpos( $atts['button_text'], '%s' ) !== false ) {
                $button_text = sprintf( $atts['button_text'], $cat_name );
            } else {
                $button_text = $atts['button_text'];
            }
            printf( '<a href="%s" class="is-link">%s</a>', esc_url( $cat_link ), $button_text );
        } else {
            echo '<a href="#" class="is-link">' . __( 'Читати далі', 'myblocktheme' ) . '</a>';
        }
        ?>
      </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'informer', 'myblocktheme_informer_shortcode' );

/**
 * Регистрирует динамический блок "Breadcrumbs".
 */
function myblocktheme_register_dynamic_breadcrumbs() {
    register_block_type( 'myblocktheme/breadcrumbs', array(
        'apiVersion'      => 2,
        'title'           => __( 'Хлебные крошки', 'myblocktheme' ),
        'description'     => __( 'Динамический блок для вывода хлебных крошек.', 'myblocktheme' ),
        'category'        => 'widgets',
        'icon'            => 'admin-links',
        'render_callback' => 'myblocktheme_render_breadcrumbs_block',
        'supports'        => array(
            'html' => false,
        ),
    ) );
}
add_action( 'init', 'myblocktheme_register_dynamic_breadcrumbs' );

/**
 * Функция-колбэк для динамического блока хлебных крошек.
 */
function myblocktheme_render_breadcrumbs_block( $attributes ) {
    $output = '<nav class="breadcrumb" aria-label="хлібні крошки">';
    $output .= '<ul>';

    // Первый элемент: ссылка на главную
    $output .= '<li><a href="' . esc_url( home_url( '/' ) ) . '">Головна</a></li>';

    if ( is_category() ) {
        $output .= '<li class="is-active">' . single_cat_title( '', false ) . '</li>';
    } elseif ( is_single() ) {
        $categories = get_the_category();
        if ( ! empty( $categories ) ) {
            // Выводим первую категорию
            $output .= '<li><a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . esc_html( $categories[0]->name ) . '</a></li>';
        }
        $output .= '<li class="is-active">' . get_the_title() . '</li>';
    } elseif ( is_page() ) {
        // Для страниц – выводим заголовок
        $output .= '<li class="is-active">' . get_the_title() . '</li>';
    } elseif ( is_search() ) {
        $output .= '<li class="is-active">' . __( 'Результати пошуку', 'myblocktheme' ) . '</li>';
    }
    // Можно расширить логику для других типов архивов

    $output .= '</ul>';
    $output .= '</nav>';

    return $output;
}