<?php
/**
 * Примусове створення/відновлення дефолтних шаблонних частин (header, aside і footer)
 * як записів типу wp_template_part.
 */

/**
 * Функція створює або оновлює шаблонну частину (wp_template_part) з вказаним slug, title і вмістом.
 *
 * @param string $slug    Слаг (наприклад, 'header', 'aside' або 'footer').
 * @param string $title   Заголовок (наприклад, 'Header', 'Aside', 'Footer').
 * @param string $content Вміст (HTML з коментарями блоків).
 */
function myblocktheme_create_or_update_template_part( $slug, $title, $content ) {
    $post_name = $slug;
    
    // Шукаємо існуючий шаблонний блок для поточної теми (тип wp_template_part).
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
        // Якщо знайдено, оновлюємо його.
        $post_id = $existing[0]->ID;
        wp_update_post( array(
            'ID'           => $post_id,
            'post_title'   => $title,
            'post_content' => $content,
            'post_status'  => 'publish',
        ) );
    } else {
        // Якщо не знайдено, створюємо новий запис.
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
 * Функція, що створює/оновлює дефолтні шаблонні частини Header, Aside і Footer.
 */
function myblocktheme_recreate_default_header_footer() {
    // Актуалізований вміст для HEADER:
    // Використовуємо оновлений шаблон з формою пошуку без напису "Search", з кнопкою "Q" та коректним бургер-меню
    $header_content = <<<HTML
<!-- wp:group {"className":"navbar is-primary","layout":{"type":"constrained"}} -->
<div class="wp-block-group navbar is-primary">
  <!-- wp:html -->
  <nav role="navigation" aria-label="головна навігація">
  <!-- /wp:html -->

  <!-- wp:group {"className":"container is-flex is-align-items-center is-justify-content-space-between","layout":{"type":"constrained"}} -->
  <div class="wp-block-group container is-flex is-align-items-center is-justify-content-space-between">

    <!-- wp:group {"className":"navbar-brand is-flex is-align-items-center","layout":{"type":"constrained"}} -->
    <div class="wp-block-group navbar-brand is-flex is-align-items-center">
      
      <!-- wp:myblocktheme/custom-logo {"className":"navbar-item"} /-->
      
      <!-- wp:search {"className":"navbar-item wp-block-search","label":"","placeholder":"Пошук...","buttonText":"Q"} /-->
      
      <!-- wp:group {"layout":{"type":"constrained"}} -->
      <div class="wp-block-group">
        <!-- wp:html -->
        <a class="navbar-burger" role="button" aria-label="меню" aria-expanded="false" data-target="navbarMain">
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
        </a>
        <!-- /wp:html -->
      </div>
      <!-- /wp:group -->
      
    </div>
    <!-- /wp:group -->

    <!-- wp:group {"className":"navbar-menu","layout":{"type":"constrained"},"id":"navbarMain"} -->
    <div id="navbarMain" class="wp-block-group navbar-menu">
      <!-- wp:group {"className":"navbar-end","layout":{"type":"constrained"}} -->
      <div class="wp-block-group navbar-end alignright">
        <!-- wp:myblocktheme/header-menu /-->
        <!-- Динамічний блок, який виводить меню з theme_location="primary" -->
      </div>
      <!-- /wp:group -->
    </div>
    <!-- /wp:group -->

  </div>
  <!-- /wp:group -->

  <!-- wp:html -->
  </nav>
  <!-- /wp:html -->
</div>
<!-- /wp:group -->
HTML;

    // Вміст для ASIDE залишається без змін.
    $aside_content = <<<HTML
<!-- wp:group {"className":"menu","layout":{"type":"constrained"}} -->
<div class="wp-block-group">
  <!-- wp:html -->
  <aside>
  <!-- /wp:html -->
    <!-- wp:paragraph {"className":"menu-label"} -->
    <p class="menu-label">Хронологія</p>
    <!-- /wp:paragraph -->
    <!-- wp:query {"query":{"perPage":5,"postType":"post","order":"desc","orderBy":"date"},"displayLayout":{"type":"list"}} -->
    <div class="wp-block-query">
      <!-- wp:post-template -->
        <!-- wp:post-title {"isLink":true} /-->
        <!-- wp:post-date /-->
      <!-- /wp:post-template -->
      <!-- wp:query-pagination /-->
    </div>
    <!-- /wp:query -->
  <!-- wp:html -->
  </aside>
  <!-- /wp:html -->
</div>
<!-- /wp:group -->
HTML;

    // Актуалізований вміст для FOOTER:
    // Використовуємо один тег <footer> із динамічним блоком меню футера
    $footer_content = <<<HTML
<!-- wp:group {"tagName":"footer","className":"footer","layout":{"type":"constrained"}} -->
<footer class="footer">
    <div class="container">
      <!-- wp:myblocktheme/footer-menu /-->
      <div class="wp-block-group content">
        <!-- wp:paragraph -->
        <p>&copy; 2025 My Block Theme</p>
        <!-- /wp:paragraph -->
      </div>
    </div>
</footer>
<!-- /wp:group -->
HTML;

    // Створюємо або оновлюємо шаблонні частини Header, Aside і Footer.
    myblocktheme_create_or_update_template_part( 'header', 'Header', $header_content );
    myblocktheme_create_or_update_template_part( 'aside', 'Aside', $aside_content );
    myblocktheme_create_or_update_template_part( 'footer', 'Footer', $footer_content );
}

/**
 * При активації теми видаляємо існуючі записи Header, Aside та Footer для поточної теми
 * і пересоздаємо дефолтні шаблонні частини.
 */
function myblocktheme_on_theme_activation() {
    global $wpdb;
    $theme = get_stylesheet();

    // Видаляємо записи шаблонних частин з іменами 'header', 'aside' та 'footer' для поточної теми.
    $wpdb->query( $wpdb->prepare(
        "DELETE FROM {$wpdb->posts} WHERE post_type = 'wp_template_part'
         AND post_name IN ('header', 'aside', 'footer')
         AND ID IN (
            SELECT post_id FROM {$wpdb->postmeta} 
            WHERE meta_key = '_wp_template_part_theme' 
              AND meta_value = %s
         )",
        $theme
    ) );

    // Створюємо дефолтні шаблонні частини Header, Aside і Footer.
    myblocktheme_recreate_default_header_footer();
}
add_action( 'after_switch_theme', 'myblocktheme_on_theme_activation' );

/**
 * Вивід повідомлення в адмінці з кнопкою для ручного скидання Header, Aside та Footer.
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
            <?php _e( 'Необхідно скинути дефолтні Header, Aside та Footer?', 'myblocktheme' ); ?>
            <a href="<?php echo esc_url( $url ); ?>" class="button button-primary">
                <?php _e( 'Скинути зараз', 'myblocktheme' ); ?>
            </a>
        </p>
    </div>
    <?php
}
add_action( 'admin_notices', 'myblocktheme_admin_notice_recreate_header_footer' );

/**
 * Обробник запиту на ручне скидання Header, Aside та Footer.
 */
function myblocktheme_handle_reset_header_footer() {
    if ( isset( $_GET['myblocktheme_reset_header_footer'] ) && '1' === $_GET['myblocktheme_reset_header_footer'] ) {
        check_admin_referer( 'myblocktheme_reset_header_footer_nonce' );
        myblocktheme_on_theme_activation();
        add_action( 'admin_notices', function() {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e( 'Header, Aside та Footer успішно пересоздані!', 'myblocktheme' ); ?></p>
            </div>
            <?php
        } );
    }
}
add_action( 'admin_init', 'myblocktheme_handle_reset_header_footer' );