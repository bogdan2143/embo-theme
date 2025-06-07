// File: /src/js/header-ui.js

;(function(){
  // =======================================================
  // ЧАСТИНА 1: Абсолютно оригінальний код initOverflow (жодних правок!)
  // =======================================================
  // проста «тротлінг» при зміні розміру
  function throttle(fn, ms){
    let last = 0;
    return function(...args){
      const now = Date.now();
      if (now - last >= ms){
        last = now;
        fn.apply(this, args);
      }
    };
  }

  function initOverflow(){
    // знаходимо контейнер navbar
    const wrapper    = document.querySelector('nav > .container'),
          navbarEnd  = wrapper?.querySelector('.navbar-end'),
          moreToggle = navbarEnd?.querySelector('.more-toggle'),
          dropdown   = moreToggle?.querySelector('.navbar-dropdown');

    if (!wrapper || !navbarEnd || !moreToggle || !dropdown) {
      console.warn('DynamicMenu: не знайдено елементи');
      return;
    }

    // спочатку ховаємо кнопку «Ще»
    moreToggle.style.display = 'none';
    moreToggle.setAttribute('aria-expanded','false');

    function redistribute(){
      // 1) повернути всі елементи з dropdown назад у ряд
      Array.from(dropdown.children).forEach(el => {
        navbarEnd.insertBefore(el, moreToggle);
      });
      dropdown.innerHTML = '';
      navbarEnd.querySelectorAll('a.navbar-item').forEach(el => {
        el.style.display = 'inline-block';
      });
      moreToggle.classList.remove('is-active');
      moreToggle.setAttribute('aria-expanded','false');

      // 2) рахуємо, скільки вміщується
      const totalW    = wrapper.clientWidth,
            brandW    = wrapper.querySelector('.navbar-brand').offsetWidth,
            toggleW   = moreToggle.offsetWidth || 0,
            freeW     = totalW - brandW - toggleW - 20; // 20px на відступи

      let used = 0;
      navbarEnd.querySelectorAll('a.navbar-item:not(.more-toggle)').forEach(item => {
        const style = getComputedStyle(item),
              w = item.offsetWidth
                + parseFloat(style.marginLeft)
                + parseFloat(style.marginRight);
        used += w;
        if (used > freeW) {
          dropdown.appendChild(item);
        }
      });

      // 3) показуємо кнопку «Ще» тільки якщо в dropdown щось є
      moreToggle.style.display = dropdown.children.length ? '' : 'none';
    }

    // перший розрахунок та додамо слухача resize
    redistribute();
    window.addEventListener('resize', throttle(redistribute, 100));

    // додатковий «тайм-аут» перерахунку після першого рендеру
    requestAnimationFrame(() => {
      redistribute();
      // на всякий випадок — ще раз у наступному кадрі
      requestAnimationFrame(redistribute);
    });

    // якщо є Font Loading API — дочекаємось завантаження шрифтів і перерахунку
    if (document.fonts && document.fonts.ready) {
      document.fonts.ready.then(redistribute);
    }

    // 4) клік по «Ще» — переключаємо is-active
    moreToggle.addEventListener('click', e => {
      e.preventDefault();
      e.stopPropagation();                // перешкоджає негайному document-кліку
      const open = moreToggle.classList.toggle('is-active');
      moreToggle.setAttribute('aria-expanded', String(open));
    });

    // 5) клік поза елементом — закриваємо
    document.addEventListener('click', () => {
      if (moreToggle.classList.contains('is-active')) {
        moreToggle.classList.remove('is-active');
        moreToggle.setAttribute('aria-expanded','false');
      }
    });
  }


  // ================================================
  // ЧАСТИНА 2: Новий код initSearchToggle (жодним чином не змінює initOverflow)
  // ================================================
  function initSearchToggle(){
    // знаходимо всі форми пошуку типу:
    // <form class="wp-block-search__button-outside wp-block-search__text-button navbar-item wp-block-search">
    const searchForms = document.querySelectorAll('.wp-block-search.wp-block-search__text-button');

    if (!searchForms.length) {
      return;
    }

    searchForms.forEach(function(form) {
      const button = form.querySelector('.wp-block-search__button');
      const input  = form.querySelector('.wp-block-search__input');

      if (!button || !input) {
        return;
      }

      // при кліку на кнопку-«лупу»
      button.addEventListener('click', function(e) {
        // лише якщо ширина екрана ≤ 480px → згортаємо/розгортаємо поле
        if (window.innerWidth <= 480) {
          e.preventDefault();
          e.stopPropagation();

          const isExpanded = form.classList.contains('expanded');
          if (isExpanded) {
            // згорнути поле, очистити, зняти фокус
            form.classList.remove('expanded');
            input.value = '';
            input.blur();
          } else {
            // розгорнути поле та сфокусуватися
            form.classList.add('expanded');
            input.focus();
          }
        }
        // якщо >480px — форма відправиться як зазвичай (жодних перешкод)
      });

      // клік поза формою — якщо було розгорнуто, згорнути
      document.addEventListener('click', function(ev) {
        if (form.classList.contains('expanded') && !form.contains(ev.target)) {
          form.classList.remove('expanded');
          input.value = '';
          input.blur();
        }
      });

      // при зміні розміру вікна: якщо екран став ширше за 480px, прибрати .expanded
      window.addEventListener('resize', function() {
        if (window.innerWidth > 480 && form.classList.contains('expanded')) {
          form.classList.remove('expanded');
        }
      });
    });
  }


  // ================================================
  // ЧАСТИНА 3: Запуск обох init (після DOMContentLoaded)
  // ================================================
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function(){
      initOverflow();
      initSearchToggle();
    });
  } else {
    initOverflow();
    initSearchToggle();
  }

})();