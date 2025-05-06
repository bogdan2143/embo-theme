<?php
/**
 * Клас MyBlockTheme_DynamicBreadcrumbs
 *
 * Реєструє динамічний блок "Хлібні крихти" для виводу хлібних крихт із SEO-розміткою.
 */
class MyBlockTheme_DynamicBreadcrumbs {

    /**
     * Реєструє блок "Хлібні крихти".
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
     * Функція-колбек для виводу хлібних крихт із SEO-розміткою.
     */
    public function render_breadcrumbs_block( $attributes ) {
        // Якщо це головна сторінка, виводимо лише один елемент "Головна"
        if ( is_front_page() ) {
            return '';
            /*return '<nav class="breadcrumb" aria-label="хлібні крихти" itemscope itemtype="https://schema.org/BreadcrumbList">
                        <ol>
                            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="breadcrumb-item">
                                <span itemprop="name" class="is-active">Головна</span>
                                <meta itemprop="position" content="1" />
                            </li>
                        </ol>
                    </nav>';*/
        }

        $breadcrumbs = array();
        // Перший елемент: посилання на головну
        $breadcrumbs[] = array(
            'title' => 'Головна',
            'url'   => home_url( '/' )
        );

        // Додаємо пункти в залежності від типу сторінки
        if ( is_category() ) {
            $breadcrumbs[] = array(
                'title' => single_cat_title( '', false )
            );
        } elseif ( is_single() ) {
            $categories = get_the_category();
            if ( ! empty( $categories ) ) {
                $breadcrumbs[] = array(
                    'title' => esc_html( $categories[0]->name ),
                    'url'   => get_category_link( $categories[0]->term_id )
                );
            }
            $breadcrumbs[] = array(
                'title' => get_the_title()
            );
        } elseif ( is_page() ) {
            $breadcrumbs[] = array(
                'title' => get_the_title()
            );
        } elseif ( is_search() ) {
            $breadcrumbs[] = array(
                'title' => __( 'Результати пошуку', 'myblocktheme' )
            );
        }

        // Формуємо HTML з SEO-розміткою Schema.org BreadcrumbList
        $output = '<nav class="breadcrumb" aria-label="хлібні крихти" itemscope itemtype="https://schema.org/BreadcrumbList"><ol>';
        $position = 1;
        foreach ( $breadcrumbs as $crumb ) {
            $output .= '<li class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
            if ( isset( $crumb['url'] ) ) {
                // Якщо в масиві є 'url', робимо посилання
                $output .= '<a href="' . esc_url( $crumb['url'] ) . '" itemprop="item"><span itemprop="name">' . esc_html( $crumb['title'] ) . '</span></a>';
            } else {
                // Якщо 'url' немає, виводимо активний пункт
                $output .= '<span itemprop="name" class="is-active">' . esc_html( $crumb['title'] ) . '</span>';
            }
            // Meta-позиція для SEO
            $output .= '<meta itemprop="position" content="' . $position . '" />';
            $output .= '</li>';
            $position++;
        }
        $output .= '</ol></nav>';

        return $output;
    }
}