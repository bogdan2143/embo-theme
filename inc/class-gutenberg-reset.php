<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handles resetting Gutenberg settings and cleaning template data.
 * When the theme is activated a notice is shown asking to perform the reset.
 * Clicking the link removes block editor options, templates and template parts
 * and resets theme mods.
 */
class MyBlockTheme_GutenbergReset {

    /**
     * Displays an admin notice if the reset flag is set.
     */
    public function maybe_show_gutenberg_reset_notice() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $flag = get_option( 'myblocktheme_show_reset_notice' );
        if ( ! $flag ) {
            return;
        }

        // Remove the flag so the notice appears only once
        delete_option( 'myblocktheme_show_reset_notice' );

        // Build reset URL with nonce
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
                _e( 'My Block Theme requires a Gutenberg reset. Click ', 'myblocktheme' );
                printf( '<a href="%s">%s</a>', esc_url( $reset_url ), __( 'here', 'myblocktheme' ) );
                _e( ' to perform the reset.', 'myblocktheme' );
                ?>
            </p>
        </div>
        <?php
    }

    /**
     * Handles the request to reset Gutenberg settings and purge template data.
     */
    public function handle_gutenberg_reset() {
        if ( isset( $_GET['gutenberg_reset'] ) && '1' === $_GET['gutenberg_reset'] && check_admin_referer( 'gutenberg_reset_nonce' ) ) {

            // Remove block editor settings option
            delete_option( 'wp_block_editor_settings' );

            global $wpdb;
            // Delete template posts
            $deleted_templates = $wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'wp_template'" );
            // Delete template part posts
            $deleted_template_parts = $wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type = 'wp_template_part'" );
            
            // Remove related post meta
            $wpdb->query( "DELETE pm FROM {$wpdb->postmeta} pm
                JOIN {$wpdb->posts} p ON p.ID = pm.post_id
                WHERE p.post_type IN ('wp_template', 'wp_template_part')" );
            
            // Reset theme mods for the current theme
            $theme = get_stylesheet();
            delete_option( "theme_mods_{$theme}" );

            add_action( 'admin_notices', function() use ( $deleted_templates, $deleted_template_parts ) {
                ?>
                <div class="notice notice-success is-dismissible">
                    <p>
                        <?php
                        printf( __( 'Gutenberg settings and template data reset. Removed templates: %d, template parts: %d.', 'myblocktheme' ), $deleted_templates, $deleted_template_parts );
                        ?>
                    </p>
                </div>
                <?php
            } );
        }
    }

    /**
     * Sets a flag on theme activation to display the notice.
     */
    public function set_reset_notice_flag() {
        update_option( 'myblocktheme_show_reset_notice', true );
    }
}