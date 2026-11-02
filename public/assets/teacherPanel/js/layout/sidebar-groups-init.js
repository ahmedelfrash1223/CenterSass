/**
 * Runs synchronously right after the sidebar markup (included at the end of sidebar.blade.php):
 * restores each parent card's saved open/closed state before the first paint.
 * Parents are closed by default; the one containing the active page is always open
 * (the server already renders it open). Behaviour lives in sidebar.js.
 */
(function () {
    'use strict';

    var state = {};
    try {
        state = JSON.parse(window.localStorage.getItem('nb-sidebar-groups') || '{}') || {};
    } catch (error) {
        state = {};
    }

    var items = document.querySelectorAll('[data-nb-accordion][data-nb-group]');
    for (var i = 0; i < items.length; i++) {
        var item = items[i];
        var toggle = item.querySelector('[data-nb-accordion-toggle]');
        var panel = item.querySelector('[data-nb-accordion-panel]');
        if (!toggle || !panel) {
            continue;
        }
        var open = item.hasAttribute('data-nb-group-active') || state[item.getAttribute('data-nb-group')] === true;
        item.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        panel.hidden = !open;
    }
})();
