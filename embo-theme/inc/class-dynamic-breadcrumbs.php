<?php
/**
 * Клас MyBlockTheme_DynamicBreadcrumbs
 *
 * Реєструє динамічний блок "Breadcrumbs" для виводу хлібних крихт.
 */

class MyBlockTheme_DynamicBreadcrumbs {

    /**
     * Реєструє блок "Breadcrumbs".
     */
    public function register_dynamic_breadcrumbs() {
        register_block_type( 'myblocktheme/breadcrumbs', array(
            'apiVersion'      => 2,
            'title'           => __( 'Хлібні крихти', 'myblocktheme' ),
            'description'     => __( 'Динамічний блок для виводу хлібних крихт.', 'myblocktheme' ),
            'category'        => 'widgets',
            'icon'            => 'admin-links',
            'render_callback' => array( $this, 'render_breadcrumbs_block' ),
            'supports'        => array(
                'html' => false,
            ),
        ) );
    }

    /**
     * Функція-колбек для виводу хлібних крихт.
     */
    public function render_breadcrumbs_block( $attributes ) {
        $output = '<nav class="breadcrumb" aria-label="хлібні крихти">';
        $output .= '<ul>';
        // Перший елемент: посилання на головну
        $output .= '<li><a href="' . esc_url( home_url( '/' ) ) . '">Головна</a></li>';

        if ( is_category() ) {
            $output .= '<li class="is-active">' . single_cat_title( '', false ) . '</li>';
        } elseif ( is_single() ) {
            $categories = get_the_category();
            if ( ! empty( $categories ) ) {
                $output .= '<li><a href="' . esc_url( get_category_link( $categories[0]->term_id ) ) . '">' . esc_html( $categories[0]->name ) . '</a></li>';
            }
            $output .= '<li class="is-active">' . get_the_title() . '</li>';
        } elseif ( is_page() ) {
            $output .= '<li class="is-active">' . get_the_title() . '</li>';
        } elseif ( is_search() ) {
            $output .= '<li class="is-active">' . __( 'Результати пошуку', 'myblocktheme' ) . '</li>';
        }
        $output .= '</ul>';
        $output .= '</nav>';

        return $output;
    }
}