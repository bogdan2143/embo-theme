<?php
/**
 * Функции для сброса настроек Гутенберга.
 *
 * При активации темы выводится уведомление с предложением сбросить настройки.
 * При нажатии на ссылку выполняется сброс (например, удаление опции block editor).
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
 * Обработка запроса на сброс настроек Гутенберга.
 */
function myblocktheme_handle_gutenberg_reset() {
    if ( isset( $_GET['gutenberg_reset'] ) && '1' === $_GET['gutenberg_reset'] && check_admin_referer( 'gutenberg_reset_nonce' ) ) {
        // Пример сброса: удаляем опцию, отвечающую за настройки редактора блоков.
        delete_option( 'wp_block_editor_settings' );
        add_action( 'admin_notices', function() {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e( 'Настройки Гутенберга успешно сброшены.', 'myblocktheme' ); ?></p>
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