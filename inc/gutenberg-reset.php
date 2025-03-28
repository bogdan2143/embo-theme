<?php
/**
 * Функции для сброса настроек Гутенберга и очистки шаблонных данных.
 *
 * При активации темы выводится уведомление с предложением сбросить настройки.
 * При нажатии на ссылку выполняется сброс:
 *   - удаляется опция настроек редактора блоков,
 *   - удаляются все записи шаблонов и шаблонных частей (wp_template, wp_template_part),
 *   - сбрасываются theme_mods.
 */

/**
 * Вывод уведомления в админке, если установлен флаг сброса.
 */
function myblocktheme_maybe_show_gutenberg_reset_notice() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Если флаг не установлен, не показываем уведомление.
    $flag = get_option( 'myblocktheme_show_reset_notice' );
    if ( ! $flag ) {
        return;
    }

    // Удаляем флаг, чтобы уведомление показывалось только один раз.
    delete_option( 'myblocktheme_show_reset_notice' );

    // Формируем URL для сброса с nonce-проверкой.
    $reset_url = add_query_arg(
        array(
            'gutenberg_reset' => '1',
            '_wpnonce'        => wp_create_nonce( 'gutenberg_reset_nonce' ),
        ),
        admin_url()
    );
    ?>
    <div class="notice notice-warning is-dismissible">
        <p>
            <?php
            _e( 'Тема My Block Theme требует сброса настроек Гутенберга. Нажмите ', 'myblocktheme' );
            printf( '<a href="%s">%s</a>', esc_url( $reset_url ), __( 'здесь', 'myblocktheme' ) );
            _e( ' чтобы выполнить сброс.', 'myblocktheme' );
            ?>
        </p>
    </div>
    <?php
}
add_action( 'admin_notices', 'myblocktheme_maybe_show_gutenberg_reset_notice' );

/**
 * Обработка запроса на сброс настроек Гутенберга и очистку шаблонных данных.
 */
function myblocktheme_handle_gutenberg_reset() {
    if ( isset( $_GET['gutenberg_reset'] ) && '1' === $_GET['gutenberg_reset'] && check_admin_referer( 'gutenberg_reset_nonce' ) ) {

        // Удаляем опцию настроек редактора блоков.
        delete_option( 'wp_block_editor_settings' );

        global $wpdb;
        // Удаляем записи шаблонов.
        $deleted_templates = $wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'wp_template'" );
        // Удаляем записи шаблонных частей.
        $deleted_template_parts = $wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'wp_template_part'" );
        
        // Удаляем связанные метаданные.
        $wpdb->query( "DELETE pm FROM {$wpdb->postmeta} pm
            JOIN {$wpdb->posts} p ON p.ID = pm.post_id
            WHERE p.post_type IN ('wp_template', 'wp_template_part')" );
        
        // Сбрасываем theme_mods для текущей темы.
        $theme = get_stylesheet();
        delete_option( "theme_mods_{$theme}" );

        add_action( 'admin_notices', function() use ( $deleted_templates, $deleted_template_parts ) {
            ?>
            <div class="notice notice-success is-dismissible">
                <p>
                    <?php
                    printf( __( 'Настройки Гутенберга и шаблонные данные успешно сброшены. Удалено шаблонов: %d, шаблонных частей: %d.', 'myblocktheme' ), $deleted_templates, $deleted_template_parts );
                    ?>
                </p>
            </div>
            <?php
        } );
    }
}
add_action( 'admin_init', 'myblocktheme_handle_gutenberg_reset' );

/**
 * При активации темы устанавливаем флаг для показа уведомления.
 */
function myblocktheme_set_reset_notice_flag() {
    update_option( 'myblocktheme_show_reset_notice', true );
}
add_action( 'after_switch_theme', 'myblocktheme_set_reset_notice_flag' );