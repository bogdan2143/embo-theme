// File: /src/js/screen-utils.js

/**
 * Клас для спостереження за зміною ширини екрану.
 * Викликає колбеки при вході/виході за заданий брейкпоінт.
 */
class ScreenObserver {
  /**
   * @param {number} breakpointPx — ширина в px, починаючи з якої вважаємо «desktop».
   */
  constructor(breakpointPx) {
    this.mediaQuery = window.matchMedia(`(min-width:${breakpointPx}px)`);
    this.onEnterCbs = [];
    this.onLeaveCbs = [];
    // Прив’язуємо обробку події
    this._handler = this._handler.bind(this);
    this.mediaQuery.addEventListener('change', this._handler);
  }

  /**
   * Додає колбек, який виконається щоразу, коли ширина стане ≥ breakpoint.
   * @param {Function} cb
   */
  onEnter(cb) {
    if (this.mediaQuery.matches) cb();
    this.onEnterCbs.push(cb);
  }

  /**
   * Додає колбек, який виконається щоразу, коли ширина стане < breakpoint.
   * @param {Function} cb
   */
  onLeave(cb) {
    if (!this.mediaQuery.matches) cb();
    this.onLeaveCbs.push(cb);
  }

  /**
   * Внутрішній обробник зміни mediaQuery.
   * @param {MediaQueryListEvent} e
   */
  _handler(e) {
    if (e.matches) {
      this.onEnterCbs.forEach(cb => cb());
    } else {
      this.onLeaveCbs.forEach(cb => cb());
    }
  }

  /**
   * Відв’язує всі обробники і чистить список колбеків.
   */
  destroy() {
    this.mediaQuery.removeEventListener('change', this._handler);
    this.onEnterCbs = [];
    this.onLeaveCbs = [];
  }
}

// Робимо глобально доступним (завантажується першим)
window.ScreenObserver = ScreenObserver;