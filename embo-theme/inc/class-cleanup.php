<?php
/**
 * Клас MyBlockTheme_Cleanup
 *
 * Обробляє очищення head, видалення зайвого виводу та коригування inline стилів.
 */
class MyBlockTheme_Cleanup {

    public function __construct() {
        // Видаляємо непотрібні дії в head
        add_action( 'init', array( $this, 'remove_head_extras' ) );
        // Видаляємо параметри версії з посилань на стилі
        add_filter( 'style_loader_tag', array( $this, 'remove_style_version' ), 10, 2 );
        // Видаляємо клас .hentry для незалогінених користувачів
        add_filter( 'post_class', array( $this, 'remove_hentry_class' ), 10, 3 );
        // Запускаємо буферизацію виводу для незалогінених користувачів
        add_action( 'template_redirect', array( $this, 'start_buffer' ) );
    }

    /**
     * Видаляє зайві теги з head.
     */
    public function remove_head_extras() {
        remove_action( 'wp_head', 'rsd_link' );                   // Видаляємо RSD link
        remove_action( 'wp_head', 'wlwmanifest_link' );             // Видаляємо Windows Live Writer manifest
        remove_action( 'wp_head', 'wp_generator' );                 // Видаляємо версію WordPress
        remove_action( 'wp_head', 'feed_links', 2 );                // Видаляємо загальні посилання RSS
        remove_action( 'wp_head', 'feed_links_extra', 3 );          // Видаляємо додаткові RSS (коментарі тощо)
        // Можна додати й інші видалення (наприклад, короткі посилання, adjacent posts links тощо)
    }

    /**
     * Видаляє параметр версії з тегів стилів.
     *
     * @param string $tag    HTML тег для підключеного стилю.
     * @param string $handle Ідентифікатор стилю.
     * @return string Модифікований HTML тег.
     */
    public function remove_style_version( $tag, $handle ) {
        // Видаляємо параметр версії (наприклад, ?ver=6.7.2) за допомогою регулярного виразу
        $tag = preg_replace( "/ver=\d+(\.\d+){1,2}/", '', $tag );
        return $tag;
    }

    /**
     * Видаляє клас .hentry для незалогінених користувачів.
     *
     * @param array  $classes Масив класів поста.
     * @param string $class   Додаткові класи.
     * @param int    $post_id Ідентифікатор поста.
     * @return array Модифікований масив класів.
     */
    public function remove_hentry_class( $classes, $class, $post_id ) {
        if ( ! is_user_logged_in() ) {
            $classes = array_diff( $classes, array( 'hentry' ) );
        }
        return $classes;
    }

    /**
     * Запускає буферизацію виводу для незалогінених користувачів.
     */
    public function start_buffer() {
        if ( ! is_user_logged_in() ) {
            // Використовуємо callback, що спочатку об’єднує inline стилі, а потім видаляє HTML-коментарі
            ob_start( array( $this, 'combine_and_strip' ) );
        }
    }

    /**
     * Callback для буферизації виводу.
     *
     * Спочатку об’єднує inline стилі, а потім видаляє HTML-коментарі (окрім умовних).
     *
     * @param string $buffer Повний вивід сторінки.
     * @return string Модифікований вивід.
     */
    public function combine_and_strip( $buffer ) {
        // Об’єднуємо inline стилі з id, що закінчуються на "-inline-css"
        $buffer = $this->combine_inline_styles( $buffer );
        // Видаляємо HTML-коментарі, за винятком умовних (для IE)
        $buffer = preg_replace( '/<!--(?!\s*\[if).*?-->/', '', $buffer );
        return $buffer;
    }

    /**
     * Об’єднує всі inline <style> блоки з id, що закінчуються на "-inline-css",
     * додає над кожним фрагментом CSS коментар із зазначенням id та видаляє їх з основного виводу.
     * Отриманий єдиний блок вставляється перед </head>.
     *
     * @param string $buffer Повний HTML вивід.
     * @return string Модифікований HTML вивід.
     */
    public function combine_inline_styles( $buffer ) {
        // Регулярний вираз шукає <style> блоки з id, що закінчуються на "-inline-css"
        $pattern = '/<style\b[^>]*\bid=[\'"](?P<id>[^\'"]+-inline-css)[\'"][^>]*>(?P<css>.*?)<\/style>/is';
        $combined_css = '';

        // Знаходимо всі відповідні <style> блоки
        if ( preg_match_all( $pattern, $buffer, $matches, PREG_SET_ORDER ) ) {
            foreach ( $matches as $match ) {
                // Додаємо коментар з id та відповідний CSS код
                $combined_css .= "\n/* " . $match['id'] . " */\n" . trim( $match['css'] ) . "\n";
            }
            // Видаляємо знайдені <style> блоки з HTML виводу
            $buffer = preg_replace( $pattern, '', $buffer );
        }

        // Якщо є об’єднаний CSS, створюємо єдиний блок стилів
        if ( ! empty( $combined_css ) ) {
            $combined_block = "<style>\n" . trim( $combined_css ) . "\n</style>";
            // Вставляємо блок перед закриваючим тегом </head>
            $buffer = preg_replace( '/<\/head>/i', $combined_block . "\n</head>", $buffer, 1 );
        }

        return $buffer;
    }
}

// Створюємо екземпляр класу очищення
new MyBlockTheme_Cleanup();