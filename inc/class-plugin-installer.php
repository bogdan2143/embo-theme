<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Installs and activates required plugins on theme activation.
 */
class MyBlockTheme_PluginInstaller {

    /**
     * List of plugins to install.
     *
     * @var array[]
     */
    private $plugins = [
        [
            'slug'        => 'embosettings',
            'plugin_file' => 'embosettings/embosettings.php',
            'source'      => 'https://github.com/bogdan2143/EmboSettings/archive/refs/heads/main.zip',
        ],
        [
            'slug'        => 'git-updater',
            'plugin_file' => 'git-updater/git-updater.php',
            'source'      => 'https://github.com/afragen/git-updater/archive/refs/heads/master.zip',
        ],
    ];

    /**
     * Hooks installer into theme activation.
     */
    public function __construct() {
        add_action( 'after_switch_theme', [ $this, 'install_and_activate' ] );
    }

    /**
     * Install and activate plugins if needed.
     */
    public function install_and_activate() {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        include_once ABSPATH . 'wp-admin/includes/file.php';
        include_once ABSPATH . 'wp-admin/includes/misc.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

        $upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );

        foreach ( $this->plugins as $plugin ) {
            $plugin_path = WP_PLUGIN_DIR . '/' . $plugin['plugin_file'];

            if ( ! file_exists( dirname( $plugin_path ) ) ) {
                $upgrader->install( $plugin['source'] );
            }

            if ( file_exists( $plugin_path ) && ! is_plugin_active( $plugin['plugin_file'] ) ) {
                activate_plugin( $plugin['plugin_file'] );
            }
        }
    }
}