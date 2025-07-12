/**
 * Utility class for observing screen width changes.
 *
 * Executes provided callbacks when the viewport crosses the given breakpoint.
 */
class ScreenObserver {
    /**
     * @param {number} breakpointPx Width in pixels from which the screen is
     *   considered "desktop".
     */
    constructor(breakpointPx) {
        this.mediaQuery = window.matchMedia(`(min-width:${breakpointPx}px)`);
        this.onEnterCbs = [];
        this.onLeaveCbs = [];
        this._handler = this._handler.bind(this);
        this.mediaQuery.addEventListener('change', this._handler);
    }

    /**
     * Adds a callback that fires whenever the width becomes ≥ breakpoint.
     * @param {Function} cb
     */
    onEnter(cb) {
        if (this.mediaQuery.matches) cb();
        this.onEnterCbs.push(cb);
    }

    /**
     * Adds a callback that fires whenever the width becomes < breakpoint.
     * @param {Function} cb
     */
    onLeave(cb) {
        if (!this.mediaQuery.matches) cb();
        this.onLeaveCbs.push(cb);
    }

    /**
     * Internal handler for the mediaQuery change event.
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
     * Removes event listeners and clears callbacks.
     */
    destroy() {
        this.mediaQuery.removeEventListener('change', this._handler);
        this.onEnterCbs = [];
        this.onLeaveCbs = [];
    }
}

// Make globally accessible
window.ScreenObserver = ScreenObserver;