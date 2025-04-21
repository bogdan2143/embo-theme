/**
 * Простое управление бургер‑кнопкой и выпадающими пунктами
 */
document.addEventListener('DOMContentLoaded', () => {
  /* бургер */
  const burger = document.querySelector('.navbar-burger');
  const menu   = document.getElementById( burger?.dataset.target || 'navbarMain' );

  if (burger && menu){
    burger.addEventListener('click', () => {
      burger.classList.toggle('is-active');
      menu.classList.toggle('is-active');
    });
  }

  /* раскрываем / сворачиваем подпункты в мобильном меню */
  menu?.querySelectorAll('.has-dropdown > .navbar-link').forEach(link => {
    link.addEventListener('click', e => {
      /* только когда меню в мобильном режиме */
      if (window.innerWidth <= 1200){
        e.preventDefault();                    // предотвращаем переход
        link.parentElement.classList.toggle('is-active');
      }
    });
  });
});