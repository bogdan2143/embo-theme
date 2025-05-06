jQuery(document).ready(function($) {
    var paged = 2; // начинаем со второй страницы
    var loading = false;

    $('#loadMoreButton').on('click', function(e) {
        e.preventDefault();
        if (loading) {
            return;
        }
        loading = true;

        $.ajax({
            url: myblockthemeLoadMore.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'myblocktheme_load_more',
                paged: paged,
                category: myblockthemeLoadMore.category,
                posts_per_page: myblockthemeLoadMore.posts_per_page
            },
            success: function(response) {
                if (response.success) {
                    // Добавляем полученные посты в контейнер новостной ленты
                    $('.news-feed .wp-block-query').append(response.data);
                    paged++;
                } else {
                    // Если постов больше нет, отключаем кнопку и выводим сообщение
                    $('#loadMoreButton').text('Немає більше постів').prop('disabled', true);
                }
                loading = false;
            },
            error: function() {
                loading = false;
            }
        });
    });
});