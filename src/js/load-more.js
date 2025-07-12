/**
 * Load More controller.
 *
 * Handles the AJAX request for loading additional posts. Designed to work with
 * the MyBlockTheme WordPress theme.
 */
class LoadMore {
    constructor(options) {
        this.button = document.getElementById(options.buttonId);
        this.container = document.querySelector(options.container);
        this.ajaxUrl = options.ajaxUrl;
        this.category = options.category;
        this.postsPerPage = options.postsPerPage;
        this.paged = 2;
        this.loading = false;
        if (this.button) {
            this.button.addEventListener('click', e => this._onClick(e));
        }
    }

    _onClick(e) {
        e.preventDefault();
        if (this.loading) {
            return;
        }
        this.loading = true;

        const data = new URLSearchParams({
            action: 'myblocktheme_load_more',
            paged: this.paged,
            category: this.category,
            posts_per_page: this.postsPerPage
        });

        fetch(this.ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: data.toString()
        })
            .then(response => response.json())
            .then(json => {
                if (json.success) {
                    this.container.insertAdjacentHTML('beforeend', json.data);
                    this.paged++;
                } else {
                    this.button.textContent = myblockthemeLoadMore.no_more_posts;
                    this.button.disabled = true;
                }
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
            });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new LoadMore({
        buttonId: 'loadMoreButton',
        container: '.news-feed .wp-block-query',
        ajaxUrl: myblockthemeLoadMore.ajax_url,
        category: myblockthemeLoadMore.category,
        postsPerPage: myblockthemeLoadMore.posts_per_page
    });
});