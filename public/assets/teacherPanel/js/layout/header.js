/**
 * Teacher dashboard header (نبذة)
 * - Accessible dropdowns ([data-nb-dropdown]): click/Enter/Space to open,
 *   Esc + outside click to close, Arrow keys between items, Tab out closes.
 *   The Livewire components keep their actions (wire:click); open/close is client-side
 *   and the toggle/menu carry wire:ignore.self so re-renders don't reset them.
 * - Livewire theme events: light / dark / system via window.NbTheme (theme-mode.js).
 */
(function () {
    'use strict';

    var ITEM_SELECTOR = 'a[href], button:not([disabled])';

    function getParts(dropdown) {
        return {
            toggle: dropdown.querySelector('[data-nb-dropdown-toggle]'),
            menu: dropdown.querySelector('[data-nb-dropdown-menu]')
        };
    }

    function menuItems(menu) {
        return Array.prototype.slice.call(menu.querySelectorAll(ITEM_SELECTOR));
    }

    function isOpen(dropdown) {
        var parts = getParts(dropdown);
        return !!parts.menu && !parts.menu.hidden;
    }

    function closeDropdown(dropdown, options) {
        var parts = getParts(dropdown);
        if (!parts.menu || parts.menu.hidden) {
            return;
        }
        parts.menu.hidden = true;
        parts.menu.classList.remove('is-open');
        if (parts.toggle) {
            parts.toggle.setAttribute('aria-expanded', 'false');
            if (options && options.returnFocus) {
                parts.toggle.focus();
            }
        }
    }

    function closeAll(except) {
        Array.prototype.forEach.call(document.querySelectorAll('[data-nb-dropdown]'), function (dropdown) {
            if (dropdown !== except) {
                closeDropdown(dropdown);
            }
        });
    }

    function openDropdown(dropdown, options) {
        var parts = getParts(dropdown);
        if (!parts.menu) {
            return;
        }
        closeAll(dropdown);
        parts.menu.hidden = false;
        parts.menu.classList.add('is-open');
        if (parts.toggle) {
            parts.toggle.setAttribute('aria-expanded', 'true');
        }
        if (options && options.focusFirst) {
            var items = menuItems(parts.menu);
            if (items.length) {
                items[0].focus();
            }
        }
    }

    /* ---------------------------------------------------------------- click */
    document.addEventListener('click', function (event) {
        var toggle = event.target.closest('[data-nb-dropdown-toggle]');
        if (toggle) {
            var dropdown = toggle.closest('[data-nb-dropdown]');
            event.preventDefault();
            if (isOpen(dropdown)) {
                closeDropdown(dropdown);
            } else {
                // event.detail === 0 means keyboard activation (Enter / Space)
                openDropdown(dropdown, { focusFirst: event.detail === 0 });
            }
            return;
        }

        var insideMenu = event.target.closest('[data-nb-dropdown-menu]');
        if (insideMenu) {
            // Navigation links close the menu; in-menu buttons (theme mode, colors) keep it open
            if (event.target.closest('a[href]')) {
                closeAll();
            }
            return;
        }

        closeAll();
    });

    /* ------------------------------------------------------------- keyboard */
    document.addEventListener('keydown', function (event) {
        var dropdown = event.target.closest && event.target.closest('[data-nb-dropdown]');

        if (event.key === 'Escape') {
            var open = document.querySelector('[data-nb-dropdown-menu]:not([hidden])');
            if (open) {
                closeDropdown(open.closest('[data-nb-dropdown]'), { returnFocus: true });
                event.stopPropagation();
            }
            return;
        }

        if (!dropdown) {
            return;
        }

        var parts = getParts(dropdown);

        if (event.target === parts.toggle && (event.key === 'ArrowDown' || event.key === 'ArrowUp')) {
            event.preventDefault();
            openDropdown(dropdown, { focusFirst: true });
            return;
        }

        if (!parts.menu || parts.menu.hidden || !parts.menu.contains(event.target)) {
            return;
        }

        var items = menuItems(parts.menu);
        var index = items.indexOf(event.target);

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            items[(index + 1) % items.length].focus();
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            items[(index - 1 + items.length) % items.length].focus();
        } else if (event.key === 'Home') {
            event.preventDefault();
            items[0].focus();
        } else if (event.key === 'End') {
            event.preventDefault();
            items[items.length - 1].focus();
        }
    });

    // Tabbing out of a dropdown closes it
    document.addEventListener('focusout', function (event) {
        var dropdown = event.target.closest && event.target.closest('[data-nb-dropdown]');
        if (dropdown && event.relatedTarget && !dropdown.contains(event.relatedTarget)) {
            closeDropdown(dropdown);
        }
    });

    document.addEventListener('livewire:navigate', function () {
        closeAll();
    });

    /* ------------------------------------------------- Livewire theme events */
    var themeEventsRegistered = false;

    function registerThemeEvents() {
        if (themeEventsRegistered || !window.Livewire || typeof window.Livewire.on !== 'function') {
            return;
        }
        themeEventsRegistered = true;

        // Light / dark / system switch -> theme-mode.js (sets <html data-pc-theme>, animates, refreshes charts)
        window.Livewire.on('apply-theme', function (event) {
            if (window.NbTheme) {
                window.NbTheme.apply(event.mode);
            }
        });

        window.Livewire.on('theme-settings-reset', function () {
            if (window.NbTheme) {
                window.NbTheme.apply('light');
            }
            if (typeof window.change_box_container === 'function') {
                window.change_box_container('false');
            }
        });
    }

    registerThemeEvents();
    document.addEventListener('livewire:init', registerThemeEvents);
    document.addEventListener('DOMContentLoaded', registerThemeEvents);

    document.addEventListener('livewire:navigated', function () {
        if (typeof window.feather !== 'undefined' && typeof window.feather.replace === 'function') {
            window.feather.replace();
        }
    });
})();
