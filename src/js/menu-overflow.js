;(function(){
  // простая «троттлизация» ресайза
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
    // находим контейнер навбара
    const wrapper    = document.querySelector('nav > .container'),
          navbarEnd  = wrapper?.querySelector('.navbar-end'),
          moreToggle = navbarEnd?.querySelector('.more-toggle'),
          dropdown   = moreToggle?.querySelector('.navbar-dropdown');

    if (!wrapper || !navbarEnd || !moreToggle || !dropdown) {
      console.warn('DynamicMenu: не найдены элементы');
      return;
    }

    // сначала прячем кнопку «Еще»
    moreToggle.style.display = 'none';
    moreToggle.setAttribute('aria-expanded','false');

    function redistribute(){
      // 1) вернуть всё из dropdown обратно в ряд
      Array.from(dropdown.children).forEach(el => {
        navbarEnd.insertBefore(el, moreToggle);
      });
      dropdown.innerHTML = '';
      navbarEnd.querySelectorAll('a.navbar-item').forEach(el => {
        el.style.display = 'inline-block';
      });
      moreToggle.classList.remove('is-active');
      moreToggle.setAttribute('aria-expanded','false');

      // 2) считаем, сколько помещается
      const totalW    = wrapper.clientWidth,
            brandW    = wrapper.querySelector('.navbar-brand').offsetWidth,
            toggleW   = moreToggle.offsetWidth || 0,
            freeW     = totalW - brandW - toggleW - 20; // 20px на отступы

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

      // 3) показываем кнопку «Еще» только если в dropdown что-то есть
      moreToggle.style.display = dropdown.children.length ? '' : 'none';
    }

    // первый расчёт и повесим ресайз
    redistribute();
    window.addEventListener('resize', throttle(redistribute, 100));

    // Дополнительный «тайм-аут» перерасчёта после первого рендера
    requestAnimationFrame(() => {
      redistribute();
      // на всякий случай — ещё раз в следующем кадре
      requestAnimationFrame(redistribute);
    });

    // Если есть Font Loading API — подождать загрузки шрифтов и пересчитать
    if (document.fonts && document.fonts.ready) {
      document.fonts.ready.then(redistribute);
    }

    // 4) клик по «Еще» — toggle is-active
    moreToggle.addEventListener('click', e => {
      e.preventDefault();
      e.stopPropagation();                // препятствует сразу же document-клику
      const open = moreToggle.classList.toggle('is-active');
      moreToggle.setAttribute('aria-expanded', String(open));
    });

    // 5) клик вне — закрываем
    document.addEventListener('click', () => {
      if (moreToggle.classList.contains('is-active')) {
        moreToggle.classList.remove('is-active');
        moreToggle.setAttribute('aria-expanded','false');
      }
    });
  }

  // Запускаем один раз при готовности DOM
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initOverflow);
  } else {
    initOverflow();
  }
})();