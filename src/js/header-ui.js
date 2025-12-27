/**
 * Header UI controller.
 *
 * Part of the EmboTheme WordPress FSE theme. Handles menu overflow logic and
 * the mobile search field toggle. All logic is wrapped in the HeaderUI class
 * and executed once the DOM is ready.
 */
;(function() {
    class HeaderUI {
        constructor() {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => this.init());
            } else {
                this.init();
            }
        }

        init() {
            this._initOverflow();
            this._initSearchToggle();
        }

        /**
         * Initializes the duplicate menu list using overflow → More… logic.
         */
        _initOverflow() {
            const wrapper = document.querySelector('nav > .container');
            const navbarEnd = wrapper?.querySelector('.navbar-end');
            const moreToggle = navbarEnd?.querySelector('.more-toggle');
            const dropdown = moreToggle?.querySelector('.navbar-dropdown');

            if (!wrapper || !navbarEnd || !moreToggle || !dropdown) {
                console.warn('DynamicMenu: elements not found');
                return;
            }

            moreToggle.style.display = 'none';
            moreToggle.setAttribute('aria-expanded', 'false');

            const redistribute = () => {
                Array.from(dropdown.children).forEach(el => navbarEnd.insertBefore(el, moreToggle));
                dropdown.innerHTML = '';
                navbarEnd.querySelectorAll('a.navbar-item').forEach(el => el.style.display = 'inline-block');
                moreToggle.classList.remove('is-active');
                moreToggle.setAttribute('aria-expanded', 'false');

                const totalW = wrapper.clientWidth;
                const brandW = wrapper.querySelector('.navbar-brand').offsetWidth;
                const toggleW = moreToggle.offsetWidth || 0;
                const freeW = totalW - brandW - toggleW - 20;

                let used = 0;
                navbarEnd.querySelectorAll('a.navbar-item:not(.more-toggle)').forEach(item => {
                    const style = getComputedStyle(item);
                    const w = item.offsetWidth + parseFloat(style.marginLeft) + parseFloat(style.marginRight);
                    used += w;
                    if (used > freeW) dropdown.appendChild(item);
                });

                moreToggle.style.display = dropdown.children.length ? '' : 'none';
            };

            redistribute();

            this._attachResizeObserver(wrapper, redistribute);

            this._overflowObserver = new ScreenObserver(1025);
            this._overflowObserver.onEnter(() => redistribute());
            this._overflowObserver.onLeave(() => redistribute());

            moreToggle.addEventListener('click', e => {
                e.preventDefault();
                e.stopPropagation();
                const open = moreToggle.classList.toggle('is-active');
                moreToggle.setAttribute('aria-expanded', String(open));
            });

            document.addEventListener('click', () => {
                if (moreToggle.classList.contains('is-active')) {
                    moreToggle.classList.remove('is-active');
                    moreToggle.setAttribute('aria-expanded', 'false');
                }
            });

            if (document.fonts && document.fonts.ready) {
                document.fonts.ready.then(redistribute);
            }
        }

        _attachResizeObserver(element, callback) {
            if (!element || typeof ResizeObserver === 'undefined') return;

            let resizeHandle = null;
            const debouncedCallback = () => {
                if (resizeHandle) cancelAnimationFrame(resizeHandle);
                resizeHandle = requestAnimationFrame(() => {
                    resizeHandle = null;
                    callback();
                });
            };

            this._resizeObserver = new ResizeObserver(debouncedCallback);
            this._resizeObserver.observe(element);
        }

        /**
         * Initializes behaviour of the mobile search button.
         */
        _initSearchToggle() {
            const forms = document.querySelectorAll('.wp-block-search.wp-block-search__text-button');
            if (!forms.length) return;

            forms.forEach(form => {
                const btn = form.querySelector('.wp-block-search__button');
                const input = form.querySelector('.wp-block-search__input');
                if (!btn || !input) return;

                btn.addEventListener('click', e => {
                    if (window.innerWidth <= 480) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (form.classList.toggle('expanded')) {
                            input.focus();
                        } else {
                            input.value = '';
                            input.blur();
                        }
                    }
                });

                document.addEventListener('click', e => {
                    if (form.classList.contains('expanded') && !form.contains(e.target)) {
                        form.classList.remove('expanded');
                        input.value = '';
                        input.blur();
                    }
                });

                const searchObserver = new ScreenObserver(481);
                searchObserver.onEnter(() => {
                    if (form.classList.contains('expanded')) {
                        form.classList.remove('expanded');
                        input.value = '';
                        input.blur();
                    }
                });
            });
        }
    }

    new HeaderUI();
})();
