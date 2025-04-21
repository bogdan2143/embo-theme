/**
 * Navbar overflow → прячем лишние пункты в «Ще…»
 * (Bulma / flex).  Без jQuery.
 */
(() => {
  const wrap = document.querySelector('.navbar-end');
  if (!wrap) return;

  /* ───── создаём пункт «Ще…» ───── */
  const toggle = document.createElement('a');
  toggle.className = 'navbar-item more-toggle';
  toggle.href = '#';
  toggle.innerHTML = 'Ще…<div class="more-dropdown"></div>';
  const dropdown = toggle.querySelector('.more-dropdown');
  wrap.appendChild(toggle);

  toggle.addEventListener('click', e => {
    e.preventDefault();
    toggle.classList.toggle('is-active');
  });

  /* ───── перераспределение ───── */
  function redistribute() {
    // возвращаем всё назад
    [...dropdown.children].forEach(el => wrap.insertBefore(el, toggle));
    toggle.style.display = 'none';

    // пока последний пункт выходит за границу  ‑ переносим
    const wrapRight = wrap.getBoundingClientRect().right - 4; // ‑4 px запас
    let last = toggle.previousElementSibling;

    while (last && last.getBoundingClientRect().right > wrapRight) {
      dropdown.prepend(last);
      toggle.style.display = 'block';
      last = toggle.previousElementSibling;
    }
  }

  window.addEventListener('load', redistribute, { once: true });
  window.addEventListener('resize', redistribute);
})();