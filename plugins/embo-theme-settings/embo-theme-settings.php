<?php
/*
Plugin Name: Embo Налаштування Теми з Візуальним Режимом та Керуванням Блоками
Plugin URI:  https://example.com
Description: Плагін для розширеного налаштування теми Embo Theme з режимом live preview, який дозволяє змінювати базові параметри та порядок шаблонних блоків (Header, Content, Sidebar, Footer, Column, Custom) збереженням через AJAX.
Version:     1.6.3
Author:      ChatGPT
Author URI:  https://example.com
License:     GPL2
*/

if ( ! defined('ABSPATH') ) {
    exit;
}

if ( ! class_exists('Embo_Tema_Nalashchuvannya') ) {

    class Embo_Tema_Nalashchuvannya {

        private $option_name = 'embo_theme_settings';
        private $nonce_action = 'embo_save_blocks';

        public function __construct() {
            add_action('admin_menu', array($this, 'dodaty_administrativne_menu'));
            add_action('admin_init', array($this, 'reistryuvaty_nalashchuvannya'));
            add_filter('theme_action_links_' . get_stylesheet(), array($this, 'zaminyty_customizer_link'));
            add_action('admin_enqueue_scripts', array($this, 'pidklyuchyty_administrativni_scripti'));
            add_action('wp_ajax_embo_save_blocks', array($this, 'ajax_zberezhennya_blokiv'));
        }

        /**
         * Добавление страницы настроек в меню темы.
         */
        public function dodaty_administrativne_menu() {
            add_theme_page(
                __('Embo Налаштування Теми', 'embo-theme'),
                __('Embo Налаштування Теми', 'embo-theme'),
                'edit_theme_options',
                'embo-theme-settings',
                array($this, 'vidobrazyty_administrativnu_storinku')
            );
        }

        /**
         * Регистрация настроек.
         *
         * Здесь регистрируются только общие настройки – блок-макет не выводится в общей форме,
         * чтобы избежать конфликтов с визуальным режимом.
         */
        public function reistryuvaty_nalashchuvannya() {
            register_setting($this->option_name, $this->option_name, array($this, 'sanityzuvaty_nalashchuvannya'));

            add_settings_section(
                'general_settings',
                __('Загальні налаштування', 'embo-theme'),
                null,
                $this->option_name
            );

            add_settings_field(
                'favicon',
                __('Favicon', 'embo-theme'),
                array($this, 'pole_favicon'),
                $this->option_name,
                'general_settings'
            );
            add_settings_field(
                'logo',
                __('Логотип', 'embo-theme'),
                array($this, 'pole_logo'),
                $this->option_name,
                'general_settings'
            );
            add_settings_field(
                'ga4',
                __('GA4', 'embo-theme'),
                array($this, 'pole_ga4'),
                $this->option_name,
                'general_settings'
            );
            add_settings_field(
                'gtm',
                __('Google Tag Manager', 'embo-theme'),
                array($this, 'pole_gtm'),
                $this->option_name,
                'general_settings'
            );
            add_settings_field(
                'comments_toggle',
                __('Увімкнення коментарів', 'embo-theme'),
                array($this, 'pole_comments_toggle'),
                $this->option_name,
                'general_settings'
            );
            add_settings_field(
                'html_comments_disable',
                __('Вимкнути HTML-коментарі для незалогінених', 'embo-theme'),
                array($this, 'pole_html_comments_disable'),
                $this->option_name,
                'general_settings'
            );
            add_settings_field(
                'lazyload_toggle',
                __('Lazyload для зображень', 'embo-theme'),
                array($this, 'pole_lazyload_toggle'),
                $this->option_name,
                'general_settings'
            );
            add_settings_field(
                'remove_head_links',
                __('Видалити зайві посилання у <head>', 'embo-theme'),
                array($this, 'pole_remove_head_links'),
                $this->option_name,
                'general_settings'
            );
            add_settings_field(
                'remove_style_version',
                __('Видалити версії зі стилів', 'embo-theme'),
                array($this, 'pole_remove_style_version'),
                $this->option_name,
                'general_settings'
            );
            add_settings_field(
                'cookie_warning',
                __('Попередження про куки', 'embo-theme'),
                array($this, 'pole_cookie_warning'),
                $this->option_name,
                'general_settings'
            );
        }

        /**
         * Санитизация настроек.
         *
         * Для поля block_layout (JSON-макет) значение сохраняется «как есть»,
         * если оно передано из визуального режима; иначе – оставляется текущее.
         */
        public function sanityzuvaty_nalashchuvannya($input) {
            $existing = get_option($this->option_name);
            $new_input = array();
            $new_input['favicon']              = isset($input['favicon']) ? esc_url_raw($input['favicon']) : '';
            $new_input['logo']                 = isset($input['logo']) ? esc_url_raw($input['logo']) : '';
            $new_input['ga4']                  = isset($input['ga4']) ? sanitize_text_field($input['ga4']) : '';
            $new_input['gtm']                  = isset($input['gtm']) ? sanitize_text_field($input['gtm']) : '';
            $new_input['comments_toggle']      = isset($input['comments_toggle']) ? '1' : '0';
            $new_input['html_comments_disable']= isset($input['html_comments_disable']) ? '1' : '0';
            $new_input['lazyload_toggle']      = isset($input['lazyload_toggle']) ? '1' : '0';
            $new_input['remove_head_links']    = isset($input['remove_head_links']) ? '1' : '0';
            $new_input['remove_style_version'] = isset($input['remove_style_version']) ? '1' : '0';
            $new_input['cookie_warning']       = isset($input['cookie_warning']) ? sanitize_text_field($input['cookie_warning']) : '';

            // Если поле block_layout передано (из визуального режима) – сохраняем его без изменений.
            if (isset($input['block_layout']) && trim($input['block_layout']) !== '') {
                $new_input['block_layout'] = $input['block_layout'];
            } else {
                $new_input['block_layout'] = isset($existing['block_layout']) ? $existing['block_layout'] : '[]';
            }
            return $new_input;
        }

        /**
         * Вывод страницы настроек.
         *
         * Два таба: "Налаштування" для общих настроек и "Візуальний режим" для управления макетом блоков.
         * Поле с JSON-макетом выводится только во вкладке "Візуальний режим".
         * Добавлены стили, чтобы фон был однородным.
         */
        public function vidobrazyty_administrativnu_storinku() {
            if ( ! current_user_can('edit_theme_options') ) {
                return;
            }
            $options = get_option($this->option_name);
            $initialBlocks = isset($options['block_layout']) && trim($options['block_layout']) !== '' ? $options['block_layout'] : '[]';
            $nonce = wp_create_nonce($this->nonce_action);
            ?>
            <div class="wrap" style="background: #fff; padding: 20px;">
                <h1><?php _e('Embo Налаштування Теми', 'embo-theme'); ?></h1>
                <div id="embo-settings-tabs">
                    <ul>
                        <li><a href="#tab-settings"><?php _e('Налаштування', 'embo-theme'); ?></a></li>
                        <li><a href="#tab-visual"><?php _e('Візуальний режим', 'embo-theme'); ?></a></li>
                    </ul>
                    <div id="tab-settings">
                        <form method="post" action="options.php" id="embo-settings-form">
                            <?php
                                settings_fields($this->option_name);
                                do_settings_sections($this->option_name);
                                submit_button(__('Зберегти налаштування', 'embo-theme'));
                            ?>
                        </form>
                    </div>
                    <div id="tab-visual">
                        <h2><?php _e('Редактор макету', 'embo-theme'); ?></h2>
                        <p><?php _e('Перетягуйте блоки для зміни їх порядку. Для відновлення дефолтного макету натисніть «Скинути макет». Зміни зберігаються через AJAX і негайно відображаються на фронті.', 'embo-theme'); ?></p>
                        <div style="margin-bottom:10px;">
                            <button id="add-new-block" class="button"><?php _e('Додати блок', 'embo-theme'); ?></button>
                            <button id="reset-blocks" class="button"><?php _e('Скинути макет', 'embo-theme'); ?></button>
                            <button id="save-blocks" class="button button-primary"><?php _e('Зберегти макет', 'embo-theme'); ?></button>
                        </div>
                        <div id="template-preview" style="border:1px solid #ccc; padding:20px; min-height:450px; background: #fff;">
                            <div id="no-blocks-placeholder" style="text-align: center; color: #999; padding: 50px;"><?php _e('Немає блоків. Додайте блок або натисніть «Скинути макет».', 'embo-theme'); ?></div>
                        </div>
                        <!-- Скрытое поле для сохранения JSON-массива макету (управляется через визуальный режим) -->
                        <input type="hidden" id="block-editor-content" name="<?php echo $this->option_name; ?>[block_layout]" value="<?php echo esc_attr($initialBlocks); ?>" />
                    </div>
                </div>
            </div>
            <script>
            jQuery(document).ready(function($){
                $("#embo-settings-tabs").tabs();

                var defaultBlockLayout = [
                    { id: 1, type: "header", backgroundColor: "#8000ff", content: "Header" },
                    { id: 2, type: "content", backgroundColor: "#ffffff", content: "Content" },
                    { id: 3, type: "sidebar", backgroundColor: "#eeeeee", content: "Sidebar" },
                    { id: 4, type: "footer", backgroundColor: "#23d160", content: "Footer" },
                    { id: 5, type: "column", backgroundColor: "#3273dc", content: "Новий блок" }
                ];

                var blockData = [];
                try {
                    var saved = $("#block-editor-content").val();
                    blockData = saved ? JSON.parse(saved) : [];
                } catch(e) {
                    blockData = [];
                }

                function updateHiddenField() {
                    $("#block-editor-content").val(JSON.stringify(blockData));
                }

                function renderBlock(block) {
                    var typeLabels = {
                        "header": "Стандартний: Header",
                        "content": "Стандартний: Content",
                        "sidebar": "Стандартний: Sidebar",
                        "footer": "Стандартний: Footer",
                        "column": "Колонка (Bulma)",
                        "custom": "Custom"
                    };
                    var label = typeLabels[block.type] ? typeLabels[block.type] : block.type;
                    var $block = $('<div class="template-block" style="border:1px solid #ddd; padding:10px; margin-bottom:10px; position:relative; background:' + block.backgroundColor + '"></div>');
                    $block.attr('data-id', block.id);
                    var $header = $('<div class="block-header" style="font-weight:bold; cursor:pointer;">' + label + ( block.content ? ': ' + block.content : '' ) + '</div>');
                    var $dragHandle = $('<span class="drag-handle" style="cursor: move; margin-right: 10px;">&#9776;</span>');
                    $header.prepend($dragHandle);
                    var $editBtn = $('<button class="edit-block button" style="position:absolute; top:5px; right:70px;"><?php _e("Редагувати", "embo-theme"); ?></button>');
                    var $deleteBtn = $('<button class="delete-block button" style="position:absolute; top:5px; right:5px;"><?php _e("Видалити", "embo-theme"); ?></button>');
                    $block.append($header).append($editBtn).append($deleteBtn);
                    return $block;
                }

                function renderBlockList() {
                    var $container = $("#template-preview");
                    $container.empty();
                    if (blockData.length === 0) {
                        $container.html('<div id="no-blocks-placeholder" style="text-align: center; color: #999; padding: 50px;"><?php _e("Немає блоків. Додайте блок або натисніть «Скинути макет».", "embo-theme"); ?></div>');
                    } else {
                        $.each(blockData, function(index, block){
                            var $el = renderBlock(block);
                            $container.append($el);
                        });
                    }
                    updateHiddenField();
                }

                function editBlock(index) {
                    var block = blockData[index];
                    var $blockEl = $("#template-preview").children(".template-block").eq(index);
                    $blockEl.find(".edit-form").remove();
                    var $form = $('<div class="edit-form" style="position:absolute; top:0; left:0; right:0; background:#fff; border:1px solid #ccc; padding:10px; z-index:10;"></div>');
                    var typesOptions = '<select class="edit-type">' +
                        '<option value="header">Стандартний: Header</option>' +
                        '<option value="content">Стандартний: Content</option>' +
                        '<option value="sidebar">Стандартний: Sidebar</option>' +
                        '<option value="footer">Стандартний: Footer</option>' +
                        '<option value="column">Колонка (Bulma)</option>' +
                        '<option value="custom">Custom</option>' +
                        '</select>';
                    $form.append('<?php _e("Тип блоку:", "embo-theme"); ?> ' + typesOptions + '<br><br>');
                    $form.append('<?php _e("Колір фону:", "embo-theme"); ?> <input type="color" class="edit-bg" value="'+ (block.backgroundColor || "#ffffff") +'"><br><br>');
                    $form.append('<?php _e("Зміст:", "embo-theme"); ?> <input type="text" class="edit-content" value="'+ (block.content || "") +'"><br><br>');
                    $form.append('<button class="save-edit button button-primary"><?php _e("Зберегти", "embo-theme"); ?></button> ');
                    $form.append('<button class="cancel-edit button"><?php _e("Відмінити", "embo-theme"); ?></button>');
                    $blockEl.append($form);
                    $form.find(".cancel-edit").on("click", function(e){
                        e.preventDefault();
                        $form.remove();
                    });
                    $form.find(".save-edit").on("click", function(e){
                        e.preventDefault();
                        blockData[index].type = $form.find(".edit-type").val();
                        blockData[index].backgroundColor = $form.find(".edit-bg").val();
                        blockData[index].content = $form.find(".edit-content").val();
                        renderBlockList();
                    });
                }

                $("#template-preview").on("click", ".block-header", function(e){
                    if($(e.target).hasClass("drag-handle")) return;
                    var index = $(this).parent().index();
                    editBlock(index);
                });

                $("#template-preview").on("click", ".delete-block", function(e){
                    e.preventDefault();
                    var index = $(this).parent().index();
                    blockData.splice(index, 1);
                    renderBlockList();
                });

                $("#template-preview").sortable({
                    handle: ".drag-handle",
                    forcePlaceholderSize: true,
                    update: function(event, ui) {
                        var newOrder = [];
                        $("#template-preview").children(".template-block").each(function(){
                            var id = $(this).attr("data-id");
                            var found = blockData.find(function(b){ return b.id == id; });
                            if(found) newOrder.push(found);
                        });
                        blockData = newOrder;
                        updateHiddenField();
                    }
                });

                $("#add-new-block").on("click", function(e){
                    e.preventDefault();
                    var newId = blockData.length ? Math.max.apply(Math, blockData.map(function(b){ return b.id; })) + 1 : 1;
                    var newBlock = { id: newId, type: "header", backgroundColor: "#3273dc", content: "<?php _e('Новий блок', 'embo-theme'); ?>" };
                    blockData.push(newBlock);
                    renderBlockList();
                });

                $("#reset-blocks").on("click", function(e){
                    e.preventDefault();
                    if(confirm("<?php _e('Скинути макет до значень за замовчуванням?', 'embo-theme'); ?>")) {
                        blockData = defaultBlockLayout;
                        renderBlockList();
                    }
                });

                $("#save-blocks").on("click", function(e){
                    e.preventDefault();
                    var data = {
                        action: "embo_save_blocks",
                        nonce: "<?php echo $nonce; ?>",
                        block_layout: JSON.stringify(blockData)
                    };
                    $.post(ajaxurl, data, function(response){
                        if(response.success) {
                            alert("<?php _e('Макет збережено. Оновіть сторінку для перегляду змін.', 'embo-theme'); ?>");
                        } else {
                            alert("<?php _e('Помилка збереження:', 'embo-theme'); ?> " + response.data);
                        }
                    });
                });

                $("#cancel-blocks").on("click", function(e){
                    e.preventDefault();
                    try {
                        var saved = $("#block-editor-content").val();
                        blockData = saved ? JSON.parse(saved) : [];
                    } catch(e) {
                        blockData = [];
                    }
                    renderBlockList();
                });

                renderBlockList();
            });
            </script>
            <style>
                /* Единый фон для всей страницы настроек */
                .wrap { background: #fff !important; }
                /* Стили для визуального редактора */
                #template-preview {
                    display: block !important;
                    background: #fff;
                }
                #template-preview .template-block {
                    display: block !important;
                    background: #f9f9f9;
                    padding: 10px;
                    margin-bottom: 10px;
                    border: 1px solid #ddd;
                    cursor: move;
                    position: relative;
                }
                .edit-form { width: 90%; }
            </style>
            <?php
        }

        /**
         * Замена ссылки на настройки темы в панели администратора.
         */
        public function zaminyty_customizer_link($links) {
            $custom_link = '<a href="' . admin_url('themes.php?page=embo-theme-settings') . '">' . __('Налаштувати тему', 'embo-theme') . '</a>';
            return array($custom_link);
        }

        /**
         * Подключение скриптов и стилей для административной части.
         * (Bulma не подключается здесь – оно теперь подключается через файл функций темы)
         */
        public function pidklyuchyty_administrativni_scripti($hook) {
            if ( 'appearance_page_embo-theme-settings' !== $hook ) {
                return;
            }
            wp_enqueue_media();
            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('jquery');
            wp_enqueue_style('wp-edit-blocks');
        }

        // Методы для отображения полей общих настроек

        public function pole_favicon() {
            $options = get_option($this->option_name);
            $favicon = isset($options['favicon']) ? esc_url($options['favicon']) : '';
            ?>
            <input type="text" name="<?php echo $this->option_name; ?>[favicon]" value="<?php echo $favicon; ?>" style="width:300px;" />
            <button class="upload-favicon button"><?php _e('Завантажити', 'embo-theme'); ?></button>
            <p class="description"><?php _e('Вкажіть URL favicon або завантажте зображення.', 'embo-theme'); ?></p>
            <script>
            jQuery(document).ready(function($){
                $('.upload-favicon').on('click', function(e){
                    e.preventDefault();
                    var image = wp.media({ title: '<?php _e("Виберіть зображення", "embo-theme"); ?>', multiple: false })
                        .open()
                        .on('select', function(){
                            var uploaded_image = image.state().get('selection').first();
                            var image_url = uploaded_image.toJSON().url;
                            $('input[name="<?php echo $this->option_name; ?>[favicon]"]').val(image_url);
                        });
                });
            });
            </script>
            <?php
        }

        public function pole_logo() {
            $options = get_option($this->option_name);
            $logo = isset($options['logo']) ? esc_url($options['logo']) : '';
            ?>
            <input type="text" name="<?php echo $this->option_name; ?>[logo]" value="<?php echo $logo; ?>" style="width:300px;" />
            <button class="upload-logo button"><?php _e('Завантажити', 'embo-theme'); ?></button>
            <p class="description"><?php _e('Вкажіть URL логотипу або завантажте зображення.', 'embo-theme'); ?></p>
            <script>
            jQuery(document).ready(function($){
                $('.upload-logo').on('click', function(e){
                    e.preventDefault();
                    var image = wp.media({ title: '<?php _e("Виберіть зображення", "embo-theme"); ?>', multiple: false })
                        .open()
                        .on('select', function(){
                            var uploaded_image = image.state().get('selection').first();
                            var image_url = uploaded_image.toJSON().url;
                            $('input[name="<?php echo $this->option_name; ?>[logo]"]').val(image_url);
                        });
                });
            });
            </script>
            <?php
        }

        public function pole_ga4() {
            $options = get_option($this->option_name);
            $ga4 = isset($options['ga4']) ? sanitize_text_field($options['ga4']) : '';
            ?>
            <input type="text" name="<?php echo $this->option_name; ?>[ga4]" value="<?php echo esc_attr($ga4); ?>" style="width:400px;" />
            <p class="description"><?php _e('Введіть ідентифікатор GA4.', 'embo-theme'); ?></p>
            <?php
        }

        public function pole_gtm() {
            $options = get_option($this->option_name);
            $gtm = isset($options['gtm']) ? sanitize_text_field($options['gtm']) : '';
            ?>
            <input type="text" name="<?php echo $this->option_name; ?>[gtm]" value="<?php echo esc_attr($gtm); ?>" style="width:400px;" />
            <p class="description"><?php _e('Введіть код Google Tag Manager.', 'embo-theme'); ?></p>
            <?php
        }

        public function pole_comments_toggle() {
            $options = get_option($this->option_name);
            $checked = isset($options['comments_toggle']) && $options['comments_toggle'] == '1' ? 'checked' : '';
            ?>
            <label>
                <input type="checkbox" name="<?php echo $this->option_name; ?>[comments_toggle]" value="1" <?php echo $checked; ?> />
                <?php _e('Увімкнути коментарі на сторінках блогу', 'embo-theme'); ?>
            </label>
            <?php
        }

        public function pole_html_comments_disable() {
            $options = get_option($this->option_name);
            $checked = isset($options['html_comments_disable']) && $options['html_comments_disable'] == '1' ? 'checked' : '';
            ?>
            <label>
                <input type="checkbox" name="<?php echo $this->option_name; ?>[html_comments_disable]" value="1" <?php echo $checked; ?> />
                <?php _e('Вимкнути HTML-коментарі для незалогінених користувачів', 'embo-theme'); ?>
            </label>
            <?php
        }

        public function pole_lazyload_toggle() {
            $options = get_option($this->option_name);
            $checked = isset($options['lazyload_toggle']) && $options['lazyload_toggle'] == '1' ? 'checked' : '';
            ?>
            <label>
                <input type="checkbox" name="<?php echo $this->option_name; ?>[lazyload_toggle]" value="1" <?php echo $checked; ?> />
                <?php _e('Увімкнути lazyload для зображень', 'embo-theme'); ?>
            </label>
            <?php
        }

        public function pole_remove_head_links() {
            $options = get_option($this->option_name);
            $checked = isset($options['remove_head_links']) && $options['remove_head_links'] == '1' ? 'checked' : '';
            ?>
            <label>
                <input type="checkbox" name="<?php echo $this->option_name; ?>[remove_head_links]" value="1" <?php echo $checked; ?> />
                <?php _e('Видалити зайві посилання у <head>', 'embo-theme'); ?>
            </label>
            <?php
        }

        public function pole_remove_style_version() {
            $options = get_option($this->option_name);
            $checked = isset($options['remove_style_version']) && $options['remove_style_version'] == '1' ? 'checked' : '';
            ?>
            <label>
                <input type="checkbox" name="<?php echo $this->option_name; ?>[remove_style_version]" value="1" <?php echo $checked; ?> />
                <?php _e('Видалити версії зі стилів', 'embo-theme'); ?>
            </label>
            <?php
        }

        public function pole_cookie_warning() {
            $options = get_option($this->option_name);
            $warning = isset($options['cookie_warning']) ? sanitize_text_field($options['cookie_warning']) : '';
            ?>
            <input type="text" name="<?php echo $this->option_name; ?>[cookie_warning]" value="<?php echo esc_attr($warning); ?>" style="width:400px;" />
            <p class="description"><?php _e('Введіть текст попередження про куки.', 'embo-theme'); ?></p>
            <?php
        }

        /**
         * AJAX-обработка сохранения макета блоков.
         * Очищение кеша выполняется для синхронизации с фронтендом.
         */
        public function ajax_zberezhennya_blokiv() {
            check_ajax_referer($this->nonce_action, 'nonce');
            if ( ! current_user_can('edit_theme_options') ) {
                wp_send_json_error(__('Недостатньо прав', 'embo-theme'));
            }
            $block_layout = isset($_POST['block_layout']) ? wp_unslash($_POST['block_layout']) : '';
            $options = get_option($this->option_name);
            $options['block_layout'] = $block_layout;
            update_option($this->option_name, $options);
            wp_cache_flush();
            wp_send_json_success();
        }
    }

    new Embo_Tema_Nalashchuvannya();
}

/**
 * Вывод инлайн-стилей с заменой CSS-переменных WP.
 */
function embo_tema_custom_css() {
    $options = get_option('embo_theme_settings');
    $primary    = isset($options['primary_color'])    ? $options['primary_color']    : '#3273dc';
    $secondary  = isset($options['secondary_color'])  ? $options['secondary_color']  : '#23d160';
    $body_bg    = isset($options['body_bg_color'])    ? $options['body_bg_color']    : '#ffffff';
    $header_bg  = isset($options['header_bg_color'])  ? $options['header_bg_color']  : '#3273dc';
    $footer_bg  = isset($options['footer_bg_color'])  ? $options['footer_bg_color']  : '#f5f5f5';
    $text_color = isset($options['text_color'])       ? $options['text_color']       : '#333333';
    echo "<style>
    :root {
        --wp--preset--color--primary: {$primary};
        --wp--preset--color--secondary: {$secondary};
        --wp--preset--color--background: {$body_bg};
        --wp--preset--color--header: {$header_bg};
        --wp--preset--color--footer: {$footer_bg};
        --wp--preset--color--text: {$text_color};
    }
    body {
        background-color: var(--wp--preset--color--background) !important;
        color: var(--wp--preset--color--text) !important;
    }
    </style>";
}
add_action('wp_head', 'embo_tema_custom_css', 100);