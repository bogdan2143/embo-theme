<?php
/**
 * Клас MyBlockTheme_GutenbergReset
 *
 * Функції для скидання налаштувань Gutenberg та очищення шаблонних даних.
 * При активації теми виводиться повідомлення з пропозицією скинути налаштування.
 * При натисканні на посилання виконується скидання:
 *  - видаляється опція налаштувань редактора блоків,
 *  - видаляються всі записи шаблонів та шаблонних частин (wp_template, wp_template_part),
 *  - скидаються theme_mods.
 */

class MyBlockTheme_GutenbergReset {

    /**
     * Виводить повідомлення в адмінці, якщо встановлено прапорець скидання.
     */
    public function maybe_show_gutenberg_reset_notice() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $flag = get_option( 'myblocktheme_show_reset_notice' );
        if ( ! $flag ) {
            return;
        }

        // Видаляємо прапорець, щоб повідомлення з'явилося лише один раз.
        delete_option( 'myblocktheme_show_reset_notice' );

        // Формуємо URL для скидання з nonce-перевіркою.
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
                _e( 'Тема My Block Theme потребує скидання налаштувань Gutenberg. Натисніть ', 'myblocktheme' );
                printf( '<a href="%s">%s</a>', esc_url( $reset_url ), __( 'тут', 'myblocktheme' ) );
                _e( ' щоб виконати скидання.', 'myblocktheme' );
                ?>
            </p>
        </div>
        <?php
    }

    /**
     * Обробляє запит на скидання налаштувань Gutenberg та очищення шаблонних даних.
     */
    public function handle_gutenberg_reset() {
        if ( isset( $_GET['gutenberg_reset'] ) && '1' === $_GET['gutenberg_reset'] && check_admin_referer( 'gutenberg_reset_nonce' ) ) {

            // Видаляємо опцію налаштувань редактора блоків.
            delete_option( 'wp_block_editor_settings' );

            global $wpdb;
            // Видаляємо записи шаблонів.
            $deleted_templates = $wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'wp_template'" );
            // Видаляємо записи шаблонних частин.
            $deleted_template_parts = $wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'wp_template_part'" );
            
            // Видаляємо пов'язані метадані.
            $wpdb->query( "DELETE pm FROM {$wpdb->postmeta} pm
                JOIN {$wpdb->posts} p ON p.ID = pm.post_id
                WHERE p.post_type IN ('wp_template', 'wp_template_part')" );
            
            // Скидаємо theme_mods для поточної теми.
            $theme = get_stylesheet();
            delete_option( "theme_mods_{$theme}" );

            add_action( 'admin_notices', function() use ( $deleted_templates, $deleted_template_parts ) {
                ?>
                <div class="notice notice-success is-dismissible">
                    <p>
                        <?php
                        printf( __( 'Налаштування Gutenberg та шаблонні дані успішно скинуто. Видалено шаблонів: %d, шаблонних частин: %d.', 'myblocktheme' ), $deleted_templates, $deleted_template_parts );
                        ?>
                    </p>
                </div>
                <?php
            } );
        }
    }

    /**
     * При активації теми встановлює прапорець для показу повідомлення.
     */
    public function set_reset_notice_flag() {
        update_option( 'myblocktheme_show_reset_notice', true );
    }
}

new MyBlockTheme_GutenbergReset();