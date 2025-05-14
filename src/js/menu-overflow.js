;(function(){
  function throttle(fn, ms) {
    let last = 0;
    return function(...args){
      const now = Date.now();
      if (now - last >= ms) {
        last = now;
        fn.apply(this, args);
      }
    };
  }

  // ширина + margin, даже если el.hidden
  function outerW(el){
    const cs = getComputedStyle(el);
    let prev;
    if (cs.display === 'none') {
      prev = el.style.display;
      el.style.display = 'inline-block';
    }
    const w = el.getBoundingClientRect().width
            + (parseFloat(cs.marginLeft)||0)
            + (parseFloat(cs.marginRight)||0);
    if (prev !== undefined) el.style.display = prev;
    return w;
  }

  function initOverflow(){
    const wrapper   = document.querySelector('.container.is-flex'),
          navbarEnd = wrapper?.querySelector('.navbar-end'),
          moreWrap  = navbarEnd?.querySelector('.more-toggle'),
          dropdown  = moreWrap?.querySelector('.navbar-dropdown');

    if (!wrapper || !navbarEnd || !moreWrap || !dropdown) {
      console.warn('DynamicMenu: не найдены элементы');
      return;
    }

    // прячем кнопку до первого перерасчёта
    moreWrap.style.display = 'none';
    moreWrap.setAttribute('aria-expanded','false');

    function redistribute(){
      const totalW = wrapper.clientWidth,
            brandW = outerW(wrapper.querySelector('.navbar-brand')),
            btnW   = outerW(moreWrap),
            freeW  = totalW - brandW - btnW - 20,
            endItems = navbarEnd.querySelectorAll('a.navbar-item:not(.more-toggle)');
      let used = 0;

      // 1) сброс: вернуть всё из dropdown в ряд
      Array.from(dropdown.children).forEach(el => navbarEnd.insertBefore(el, moreWrap));
      navbarEnd.querySelectorAll('a.navbar-item').forEach(el => el.style.display = 'inline-block');
      dropdown.innerHTML = '';
      // убираем старый active, если был
      moreWrap.classList.remove('active');
      moreWrap.setAttribute('aria-expanded','false');

      // 2) «запихать» лишние в dropdown
      endItems.forEach(item => {
        used += outerW(item);
        if (used > freeW) {
          dropdown.appendChild(item);
        }
      });

      // 3) показать / скрыть кнопку
      moreWrap.style.display = dropdown.children.length ? 'inline-block' : 'none';
    }

    // клик — toggle класса active
    moreWrap.addEventListener('click', e => {
      e.preventDefault();
      // переключаем Bulma-класс is-active
      const isOpen = moreWrap.classList.toggle('is-active');
      moreWrap.setAttribute('aria-expanded', String(isOpen));
    });

    redistribute();
    window.addEventListener('resize', throttle(redistribute, 100));
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initOverflow);
  } else {
    initOverflow();
  }
  window.addEventListener('load', initOverflow);
})();