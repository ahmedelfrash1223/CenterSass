/**
 * Runs synchronously right after the sidebar markup (included at the end of sidebar.blade.php):
 * restores each collapsible group's saved open/closed state before the first paint.
 * The group containing the active page is always open. Behaviour lives in sidebar.js.
 */
(function () {
    'use strict';

    var state = {};
    try {
        state = JSON.parse(window.localStorage.getItem('nb-sidebar-groups') || '{}') || {};
    } catch (error) {
        state = {};
    }

    var groups = document.querySelectorAll('[data-nb-group]');
    for (var i = 0; i < groups.length; i++) {
        var group = groups[i];
        var toggle = group.querySelector('[data-nb-group-toggle]');
        if (!toggle) {
            continue;
        }
        var open = group.hasAttribute('data-nb-group-active') || state[group.getAttribute('data-nb-group')] !== false;
        group.classList.toggle('is-collapsed', !open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
})();
