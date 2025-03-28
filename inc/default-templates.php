<?php
/**
 * Принудительное создание/восстановление дефолтных шаблонных частей (header и footer)
 * как записей типа wp_template_part.
 */

/**
 * Создаёт или обновляет шаблонную часть (wp_template_part) с указанным slug, title и содержимым.
 *
 * @param string $slug    Слаг (например, 'header' или 'footer').
 * @param string $title   Заголовок (например, 'Header').
 * @param string $content Содержимое (HTML с комментариями блоков).
 */
function myblocktheme_create_or_update_template_part( $slug, $title, $content ) {
    $post_name = $slug;
    
    // Ищем существующий шаблонный блок для текущей темы (используем тип wp_template_part).
    $existing = get_posts( array(
        'post_type'      => 'wp_template_part',
        'name'           => $post_name,
        'posts_per_page' => 1,
        'post_status'    => 'any',
        'meta_query'     => array(
            array(
                'key'   => '_wp_template_part_theme',
                'value' => get_stylesheet(),
            ),
        ),
    ) );

    if ( ! empty( $existing ) ) {
        // Если найден, обновляем его.
        $post_id = $existing[0]->ID;
        wp_update_post( array(
            'ID'           => $post_id,
            'post_title'   => $title,
            'post_content' => $content,
            'post_status'  => 'publish',
        ) );
    } else {
        // Если не найден, создаём новую запись.
        $post_id = wp_insert_post( array(
            'post_type'    => 'wp_template_part',
            'post_name'    => $post_name,
            'post_title'   => $title,
            'post_content' => $content,
            'post_status'  => 'publish',
        ) );
        if ( $post_id && ! is_wp_error( $post_id ) ) {
            add_post_meta( $post_id, '_wp_template_part_theme', get_stylesheet() );
        }
    }
}

/**
 * Функция, создающая/обновляющая дефолтные header и footer.
 */
function myblocktheme_recreate_default_header_footer() {
    // Пример содержимого для HEADER (упрощённый).
    $header_content = <<<HTML
<!-- wp:group {"tagName":"nav","className":"navbar is-primary","layout":{"type":"constrained"}} -->
<nav class="navbar is-primary" role="navigation" aria-label="головна навігація">
  <!-- wp:group {"className":"container","layout":{"type":"constrained"}} -->
  <div class="container">
    <!-- wp:site-title {"tagName":"p","className":"navbar-item"} /-->
    <!-- wp:search {"label":"Search","placeholder":"Пошук...","className":"navbar-item"} /-->
    <!-- wp:group {"tagName":"a","className":"navbar-burger","layout":{"type":"constrained"}} -->
    <a class="navbar-burger" role="button" aria-label="меню" aria-expanded="false" data-target="navbarMain">
      <!-- wp:html -->
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
      <!-- /wp:html -->
    </a>
    <!-- /wp:group -->
  </div>
  <!-- /wp:group -->
</nav>
<!-- /wp:group -->
HTML;

    // Пример содержимого для FOOTER (упрощённый).
    $footer_content = <<<HTML
<!-- wp:group {"tagName":"footer","className":"footer","layout":{"type":"constrained"}} -->
<footer class="footer">
  <!-- wp:paragraph {"className":"has-text-centered"} -->
  <p class="has-text-centered">© 2025 My Block Theme</p>
  <!-- /wp:paragraph -->
</footer>
<!-- /wp:group -->
HTML;

    myblocktheme_create_or_update_template_part( 'header', 'Header', $header_content );
    myblocktheme_create_or_update_template_part( 'footer', 'Footer', $footer_content );
}

/**
 * При активации темы удаляем существующие записи header и footer для текущей темы
 * и пересоздаём дефолтные шаблонные части.
 */
function myblocktheme_on_theme_activation() {
    global $wpdb;
    $theme = get_stylesheet();

    // Удаляем записи шаблонных частей с именами 'header' и 'footer' для текущей темы.
    $wpdb->query( $wpdb->prepare(
        "DELETE FROM {$wpdb->posts} WHERE post_type = 'wp_template_part'
         AND (post_name = 'header' OR post_name = 'footer')
         AND ID IN (
            SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_template_part_theme' AND meta_value = %s
         )",
        $theme
    ) );

    // Создаём дефолтные header и footer.
    myblocktheme_recreate_default_header_footer();
}
add_action( 'after_switch_theme', 'myblocktheme_on_theme_activation' );

/**
 * Вывод уведомления в админке с кнопкой для ручного сброса header и footer.
 */
function myblocktheme_admin_notice_recreate_header_footer() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $url = add_query_arg(
        array(
            'myblocktheme_reset_header_footer' => '1',
            '_wpnonce' => wp_create_nonce( 'myblocktheme_reset_header_footer_nonce' ),
        ),
        admin_url()
    );
    ?>
    <div class="notice notice-info is-dismissible">
        <p>
            <?php _e( 'Нужно сбросить дефолтный Header и Footer?', 'myblocktheme' ); ?>
            <a href="<?php echo esc_url( $url ); ?>" class="button button-primary">
                <?php _e( 'Сбросить сейчас', 'myblocktheme' ); ?>
            </a>
        </p>
    </div>
    <?php
}
add_action( 'admin_notices', 'myblocktheme_admin_notice_recreate_header_footer' );

/**
 * Обработчик запроса на ручной сброс header и footer.
 */
function myblocktheme_handle_reset_header_footer() {
    if ( isset( $_GET['myblocktheme_reset_header_footer'] ) && '1' === $_GET['myblocktheme_reset_header_footer'] ) {
        check_admin_referer( 'myblocktheme_reset_header_footer_nonce' );
        myblocktheme_on_theme_activation();
        add_action( 'admin_notices', function() {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e( 'Header и Footer успешно пересозданы!', 'myblocktheme' ); ?></p>
            </div>
            <?php
        } );
    }
}
add_action( 'admin_init', 'myblocktheme_handle_reset_header_footer' );