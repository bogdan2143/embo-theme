// File: /src/js/header-ui.js

;(function(){
  // === Клас для ініціалізації UI-хедера ===
  class HeaderUI {
    constructor() {
      // спочатку чекаємо DOMContentLoaded
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
     * Ініціалізує «дублюючий» список меню за принципом overflow → More…
     */
    _initOverflow() {
      const wrapper    = document.querySelector('nav > .container');
      const navbarEnd  = wrapper?.querySelector('.navbar-end');
      const moreToggle = navbarEnd?.querySelector('.more-toggle');
      const dropdown   = moreToggle?.querySelector('.navbar-dropdown');

      if (!wrapper || !navbarEnd || !moreToggle || !dropdown) {
        console.warn('DynamicMenu: не знайдено елементи');
        return;
      }

      // сховаємо кнопку
      moreToggle.style.display = 'none';
      moreToggle.setAttribute('aria-expanded','false');

      // Виносимо логику redisribute в метод
      const redistribute = () => {
        // повернути всі елементи з dropdown назад
        Array.from(dropdown.children).forEach(el => navbarEnd.insertBefore(el, moreToggle));
        dropdown.innerHTML = '';
        navbarEnd.querySelectorAll('a.navbar-item').forEach(el => el.style.display = 'inline-block');
        moreToggle.classList.remove('is-active');
        moreToggle.setAttribute('aria-expanded','false');

        const totalW  = wrapper.clientWidth;
        const brandW  = wrapper.querySelector('.navbar-brand').offsetWidth;
        const toggleW = moreToggle.offsetWidth || 0;
        const freeW   = totalW - brandW - toggleW - 20;

        let used = 0;
        navbarEnd.querySelectorAll('a.navbar-item:not(.more-toggle)').forEach(item => {
          const style = getComputedStyle(item);
          const w = item.offsetWidth + parseFloat(style.marginLeft) + parseFloat(style.marginRight);
          used += w;
          if (used > freeW) dropdown.appendChild(item);
        });

        moreToggle.style.display = dropdown.children.length ? '' : 'none';
      };

      // Викликаємо відразу
      redistribute();

      // Відловлюємо зміни ширини через ScreenObserver (breakpoint 1025px)
      this._overflowObserver = new ScreenObserver(1025);
      this._overflowObserver.onEnter(() => redistribute());
      this._overflowObserver.onLeave(() => redistribute());

      // При кліку More…
      moreToggle.addEventListener('click', e => {
        e.preventDefault();
        e.stopPropagation();
        const open = moreToggle.classList.toggle('is-active');
        moreToggle.setAttribute('aria-expanded', String(open));
      });

      // Клік поза — закрити
      document.addEventListener('click', () => {
        if (moreToggle.classList.contains('is-active')) {
          moreToggle.classList.remove('is-active');
          moreToggle.setAttribute('aria-expanded','false');
        }
      });

      // Поки змінюємо шрифти
      if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(redistribute);
      }
    }

    /**
     * Ініціалізує поведінку мобільної кнопки пошуку
     */
    _initSearchToggle() {
      const forms = document.querySelectorAll('.wp-block-search.wp-block-search__text-button');
      if (!forms.length) return;

      forms.forEach(form => {
        const btn   = form.querySelector('.wp-block-search__button');
        const input = form.querySelector('.wp-block-search__input');
        if (!btn || !input) return;

        // Тоггл по кліку
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

        // Клік поза — згорнути
        document.addEventListener('click', e => {
          if (form.classList.contains('expanded') && !form.contains(e.target)) {
            form.classList.remove('expanded');
            input.value = '';
            input.blur();
          }
        });

        // При зміні ширини через ScreenObserver
        const searchObserver = new ScreenObserver(481);
        searchObserver.onEnter(() => {
          // якщо >480, згортаємо
          if (form.classList.contains('expanded')) {
            form.classList.remove('expanded');
            input.value = '';
            input.blur();
          }
        });
      });
    }
  }

  // Старт
  new HeaderUI();
})();