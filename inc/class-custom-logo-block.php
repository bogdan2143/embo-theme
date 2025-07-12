<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Registers a dynamic logo block. It first checks plugin settings (embo_branding_options),
 * then falls back to the theme.json configuration. If nothing is found it outputs the site title.
 */
class MyBlockTheme_CustomLogoBlock {

    /**
     * Class constructor.
     */
    public function __construct() {
        // Register block on init
        add_action('init', array($this, 'register_custom_logo_block'));
    }

    /**
     * Registers the dynamic block.
     */
    public function register_custom_logo_block() {
        if ( function_exists('register_block_type') ) {
            register_block_type( 'myblocktheme/custom-logo', array(
                'render_callback' => array( $this, 'render_custom_logo' ),
            ) );
        }
    }
    
    /**
     * Renders the logo block.
     *
     * Steps:
     * 1) Use embo_branding_options['logo'] if present.
     * 2) Otherwise check theme.json -> logo.
     * 3) If both are empty output the site title.
     *
     * @param array  $attributes Block attributes.
     * @param string $content    Block content.
     * @return string HTML markup for the logo.
    */
    public function render_custom_logo( $attributes, $content ) {
        // Get home page URL
        $home_url = esc_url( home_url( '/' ) );

        // Check plugin settings first
        $branding_options = get_option( 'embo_branding_options', array( 'logo' => '' ) );
        $plugin_logo      = ! empty( $branding_options['logo'] ) ? esc_url( $branding_options['logo'] ) : '';

        if ( $plugin_logo ) {
            // If plugin logo exists output it linked to the home page
            return sprintf(
                '<a class="custom-logo navbar-item" href="%s"><img src="%s" alt="%s"></a>',
                $home_url,
                $plugin_logo,
                esc_attr( get_bloginfo( 'name' ) )
            );
        }

        // Fallback to theme.json when the plugin logo is empty
        $config_path = get_template_directory() . '/theme.json';
        $logo_url    = '';

        if ( file_exists( $config_path ) ) {
            // Read configuration data
            $config_data = json_decode( file_get_contents( $config_path ), true );
            // Check if 'logo' key is set
            if ( isset( $config_data['logo'] ) && ! empty( $config_data['logo'] ) ) {
                $logo_url = esc_url( $config_data['logo'] );
            }
        }
        
        if ( $logo_url ) {
            // Output the theme.json logo linked to the home page
            return sprintf(
                '<a class="custom-logo navbar-item" href="%s"><img src="%s" alt="%s"></a>',
                $home_url,
                $logo_url,
                esc_attr( get_bloginfo( 'name' ) )
            );
        } else {
            // If nothing found output the site title linked to the home page
            return sprintf(
                '<a class="site-title navbar-item" href="%s">%s</a>',
                $home_url,
                esc_html( get_bloginfo( 'name' ) )
            );
        }
    }
}