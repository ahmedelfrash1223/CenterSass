/**
 * Teacher dashboard sidebar (نبذة)
 * - Desktop: collapse / expand (remembered in localStorage)
 * - < 1024px: off-canvas drawer with overlay, Esc, focus trap, scroll lock
 * - Parent cards with sub-cards: accordion when expanded (open/closed state
 *   remembered in localStorage, the parent of the active page is always open),
 *   flyout card when collapsed
 * - Tooltips for icon-only items
 * - Sidebar logout button -> existing Livewire logout (TeacherProfileCard)
 *
 * Works with Livewire wire:navigate: listeners are delegated on document and
 * state is re-synced after every navigation (the body is swapped, <html> is kept).
 */
(function () {
    'use strict';

    var STORAGE_KEY = 'nb-sidebar-collapsed';
    var SCROLL_KEY = 'nb-sidebar-scroll';
    var GROUPS_KEY = 'nb-sidebar-groups';
    var COLLAPSED_CLASS = 'nb-sidebar-collapsed';
    var DRAWER_CLASS = 'nb-drawer-open';

    var root = document.documentElement;
    var desktopQuery = window.matchMedia('(min-width: 1024px)');
    var reducedMotionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');

    var tooltipEl = null;
    var flyoutEl = null;
    var flyoutOwner = null;

    /* ---------------------------------------------------------------- utils */
    function $(selector) {
        return document.querySelector(selector);
    }

    function isDesktop() {
        return desktopQuery.matches;
    }

    function isCollapsed() {
        return root.classList.contains(COLLAPSED_CLASS);
    }

    function isDrawerOpen() {
        return root.classList.contains(DRAWER_CLASS);
    }

    function prefersReducedMotion() {
        return reducedMotionQuery.matches;
    }

    function storageGet(key, storage) {
        try {
            return (storage || window.localStorage).getItem(key);
        } catch (error) {
            return null;
        }
    }

    function storageSet(key, value, storage) {
        try {
            (storage || window.localStorage).setItem(key, value);
        } catch (error) {
            /* storage unavailable: state just won't persist */
        }
    }

    function focusableIn(container) {
        return Array.prototype.filter.call(
            container.querySelectorAll('a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])'),
            function (el) {
                return el.offsetParent !== null || el === document.activeElement;
            }
        );
    }

    /* ------------------------------------------------------- toggle button */
    function syncToggleButton() {
        var toggle = $('[data-nb-sidebar-toggle]');
        if (!toggle) {
            return;
        }

        var expanded = isDesktop() ? !isCollapsed() : isDrawerOpen();
        toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');

        if (isDesktop()) {
            toggle.setAttribute('aria-label', isCollapsed() ? 'توسيع القائمة الجانبية' : 'تصغير القائمة الجانبية');
        } else {
            toggle.setAttribute('aria-label', isDrawerOpen() ? 'إغلاق القائمة' : 'فتح القائمة');
        }
    }

    /* ----------------------------------------------------------- collapse */
    function setCollapsed(collapsed) {
        root.classList.toggle(COLLAPSED_CLASS, collapsed);
        storageSet(STORAGE_KEY, collapsed ? '1' : '0');
        hideTooltip();
        closeFlyout();
        syncToggleButton();
    }

    /* -------------------------------------------------------------- drawer */
    function openDrawer() {
        var sidebar = $('[data-nb-sidebar]');
        var overlay = $('[data-nb-drawer-overlay]');
        if (!sidebar) {
            return;
        }

        root.classList.add(DRAWER_CLASS);
        if (overlay) {
            overlay.hidden = false;
        }
        sidebar.setAttribute('role', 'dialog');
        sidebar.setAttribute('aria-modal', 'true');
        syncToggleButton();

        var target = sidebar.querySelector('.nb-nav__link.is-active, .nb-nav__sublink.is-active') || sidebar.querySelector('[data-nb-drawer-close]');
        if (target) {
            window.setTimeout(function () { target.focus(); }, 50);
        }
    }

    function closeDrawer(options) {
        var sidebar = $('[data-nb-sidebar]');
        var overlay = $('[data-nb-drawer-overlay]');
        var wasOpen = isDrawerOpen();

        root.classList.remove(DRAWER_CLASS);
        if (overlay) {
            overlay.hidden = true;
        }
        if (sidebar) {
            sidebar.removeAttribute('role');
            sidebar.removeAttribute('aria-modal');
        }
        syncToggleButton();

        if (wasOpen && !(options && options.skipFocus)) {
            var toggle = $('[data-nb-sidebar-toggle]');
            if (toggle) {
                toggle.focus();
            }
        }
    }

    function trapFocus(event) {
        if (!isDrawerOpen() || event.key !== 'Tab') {
            return;
        }
        var sidebar = $('[data-nb-sidebar]');
        if (!sidebar) {
            return;
        }
        var items = focusableIn(sidebar);
        if (!items.length) {
            return;
        }
        var first = items[0];
        var last = items[items.length - 1];

        if (event.shiftKey && (document.activeElement === first || !sidebar.contains(document.activeElement))) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && (document.activeElement === last || !sidebar.contains(document.activeElement))) {
            event.preventDefault();
            first.focus();
        }
    }

    /* ------------------------------------------- parent cards (accordion) */
    // Open/closed state per parent card, keyed by data-nb-group (closed by default)
    function readGroupState() {
        try {
            return JSON.parse(storageGet(GROUPS_KEY) || '{}') || {};
        } catch (error) {
            return {};
        }
    }

    function saveGroupState(id, open) {
        if (!id) {
            return;
        }
        var state = readGroupState();
        state[id] = open;
        storageSet(GROUPS_KEY, JSON.stringify(state));
    }

    function setAccordion(item, open, animate) {
        var toggle = item.querySelector('[data-nb-accordion-toggle]');
        var panel = item.querySelector('[data-nb-accordion-panel]');
        if (!toggle || !panel) {
            return;
        }

        item.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');

        // No animation when reduced motion is on or the sidebar is icon-only (panel hidden by CSS)
        if (!animate || prefersReducedMotion() || (isDesktop() && isCollapsed())) {
            panel.hidden = !open;
            panel.style.height = '';
            return;
        }

        var done = false;
        var finish = function (event) {
            if (done || (event && (event.target !== panel || event.propertyName !== 'height'))) {
                return;
            }
            done = true;
            panel.removeEventListener('transitionend', finish);
            // Only apply the end state if it wasn't toggled again meanwhile
            if (item.classList.contains('is-open') === open) {
                if (!open) {
                    panel.hidden = true;
                }
                panel.style.height = '';
            }
        };
        panel.addEventListener('transitionend', finish);
        // Fallback when transitionend never fires (background tab, interrupted transition)
        window.setTimeout(function () { finish(); }, 320);

        if (open) {
            panel.hidden = false;
            var target = panel.scrollHeight;
            panel.style.height = '0px';
            void panel.offsetHeight; // reflow so the transition starts from 0
            panel.style.height = target + 'px';
        } else {
            panel.style.height = panel.scrollHeight + 'px';
            void panel.offsetHeight;
            panel.style.height = '0px';
        }
    }

    function restoreAccordions() {
        var state = readGroupState();
        Array.prototype.forEach.call(document.querySelectorAll('[data-nb-accordion]'), function (item) {
            var id = item.getAttribute('data-nb-group');
            var open = item.hasAttribute('data-nb-group-active') || (!!id && state[id] === true);
            setAccordion(item, open, false);
        });
    }

    /* -------------------------------------------------------------- flyout */
    function closeFlyout() {
        if (flyoutEl) {
            flyoutEl.remove();
            flyoutEl = null;
        }
        if (flyoutOwner) {
            flyoutOwner.setAttribute('aria-expanded', 'false');
            flyoutOwner = null;
        }
    }

    function openFlyout(item, toggle) {
        closeFlyout();
        hideTooltip();

        var panel = item.querySelector('[data-nb-accordion-panel]');
        if (!panel) {
            return;
        }

        flyoutEl = document.createElement('div');
        flyoutEl.className = 'nb-flyout';
        flyoutEl.setAttribute('role', 'group');
        flyoutEl.setAttribute('aria-label', toggle.getAttribute('data-nb-tooltip') || '');

        var title = document.createElement('p');
        title.className = 'nb-flyout__title';
        title.textContent = toggle.getAttribute('data-nb-tooltip') || '';
        flyoutEl.appendChild(title);

        var list = panel.querySelector('.nb-nav__sublist');
        if (list) {
            flyoutEl.appendChild(list.cloneNode(true));
        }

        document.body.appendChild(flyoutEl);

        var rect = toggle.getBoundingClientRect();
        var top = Math.min(rect.top, window.innerHeight - flyoutEl.offsetHeight - 8);
        flyoutEl.style.top = Math.max(8, top) + 'px';
        flyoutEl.style.left = Math.max(8, rect.left - flyoutEl.offsetWidth - 10) + 'px';

        flyoutOwner = toggle;
        toggle.setAttribute('aria-expanded', 'true');

        var firstLink = flyoutEl.querySelector('a');
        if (firstLink) {
            firstLink.focus();
        }
    }

    /* ------------------------------------------------------------- tooltip */
    function shouldShowTooltip(el) {
        if (el.hasAttribute('data-nb-tooltip-always')) {
            return true;
        }
        return isDesktop() && isCollapsed() && !!el.closest('[data-nb-sidebar]');
    }

    function showTooltip(el) {
        var text = el.getAttribute('data-nb-tooltip');
        if (!text || !shouldShowTooltip(el) || flyoutOwner === el) {
            return;
        }

        if (!tooltipEl) {
            tooltipEl = document.createElement('div');
            tooltipEl.className = 'nb-tooltip';
            tooltipEl.id = 'nb-tooltip';
            tooltipEl.setAttribute('role', 'tooltip');
        }
        tooltipEl.textContent = text;
        document.body.appendChild(tooltipEl);

        var rect = el.getBoundingClientRect();
        var tipWidth = tooltipEl.offsetWidth;
        var tipHeight = tooltipEl.offsetHeight;
        var left = rect.left - tipWidth - 10;
        if (left < 8) {
            left = Math.min(rect.right + 10, window.innerWidth - tipWidth - 8);
        }
        tooltipEl.style.left = left + 'px';
        tooltipEl.style.top = Math.max(8, rect.top + rect.height / 2 - tipHeight / 2) + 'px';
    }

    function hideTooltip() {
        if (tooltipEl && tooltipEl.parentNode) {
            tooltipEl.parentNode.removeChild(tooltipEl);
        }
    }

    /* ----------------------------------------------------- scroll restore */
    function saveNavScroll() {
        var nav = $('[data-nb-nav]');
        if (nav) {
            storageSet(SCROLL_KEY, String(nav.scrollTop), window.sessionStorage);
        }
    }

    function restoreNavScroll() {
        var nav = $('[data-nb-nav]');
        if (!nav) {
            return;
        }
        var saved = parseInt(storageGet(SCROLL_KEY, window.sessionStorage), 10);
        if (!isNaN(saved)) {
            nav.scrollTop = saved;
        }

        var active = nav.querySelector('.nb-nav__link.is-active, .nb-nav__sublink.is-active');
        if (active) {
            var navRect = nav.getBoundingClientRect();
            var itemRect = active.getBoundingClientRect();
            if (itemRect.top < navRect.top || itemRect.bottom > navRect.bottom) {
                active.scrollIntoView({ block: 'nearest' });
            }
        }
    }

    /* ------------------------------------------------------------- events */
    document.addEventListener('click', function (event) {
        var target = event.target;

        // Header toggle: collapse on desktop, drawer on mobile
        if (target.closest('[data-nb-sidebar-toggle]')) {
            event.preventDefault();
            if (isDesktop()) {
                setCollapsed(!isCollapsed());
            } else if (isDrawerOpen()) {
                closeDrawer();
            } else {
                openDrawer();
            }
            return;
        }

        if (target.closest('[data-nb-drawer-close]') || target.closest('[data-nb-drawer-overlay]')) {
            closeDrawer();
            return;
        }

        // Sidebar logout -> existing Livewire logout action
        if (target.closest('[data-nb-logout]')) {
            event.preventDefault();
            if (window.Livewire && typeof window.Livewire.dispatch === 'function') {
                window.Livewire.dispatch('nb-teacher-logout');
            }
            return;
        }

        // Parent card: accordion (expanded sidebar / drawer) or flyout (collapsed)
        var accordionToggle = target.closest('[data-nb-accordion-toggle]');
        if (accordionToggle) {
            event.preventDefault();
            var item = accordionToggle.closest('[data-nb-accordion]');
            if (!item) {
                return;
            }
            if (isDesktop() && isCollapsed()) {
                if (flyoutOwner === accordionToggle) {
                    closeFlyout();
                } else {
                    openFlyout(item, accordionToggle);
                }
            } else {
                var willOpen = !item.classList.contains('is-open');
                setAccordion(item, willOpen, true);
                saveGroupState(item.getAttribute('data-nb-group'), willOpen);
            }
            return;
        }

        // Navigation link inside the drawer / flyout closes it
        if (target.closest('[data-nb-sidebar] a[href]') && isDrawerOpen()) {
            closeDrawer({ skipFocus: true });
        }
        if (flyoutEl && (target.closest('.nb-flyout a[href]') || !target.closest('.nb-flyout'))) {
            closeFlyout();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            if (flyoutEl) {
                var owner = flyoutOwner;
                closeFlyout();
                if (owner) {
                    owner.focus();
                }
                return;
            }
            if (isDrawerOpen()) {
                closeDrawer();
                return;
            }
            hideTooltip();
        }
        trapFocus(event);
    });

    document.addEventListener('mouseover', function (event) {
        var el = event.target.closest && event.target.closest('[data-nb-tooltip]');
        if (el) {
            showTooltip(el);
        }
    });

    document.addEventListener('mouseout', function (event) {
        var el = event.target.closest && event.target.closest('[data-nb-tooltip]');
        if (el && !el.contains(event.relatedTarget)) {
            hideTooltip();
        }
    });

    document.addEventListener('focusin', function (event) {
        var el = event.target.closest && event.target.closest('[data-nb-tooltip]');
        if (el && el.matches(':focus-visible')) {
            showTooltip(el);
        } else {
            hideTooltip();
        }
    });

    document.addEventListener('focusout', function (event) {
        if (event.target.closest && event.target.closest('[data-nb-tooltip]')) {
            hideTooltip();
        }
    });

    document.addEventListener('scroll', function () {
        hideTooltip();
        closeFlyout();
    }, true);

    function onViewportChange() {
        if (isDesktop() && isDrawerOpen()) {
            closeDrawer({ skipFocus: true });
        }
        hideTooltip();
        closeFlyout();
        syncToggleButton();
    }

    if (typeof desktopQuery.addEventListener === 'function') {
        desktopQuery.addEventListener('change', onViewportChange);
    } else if (typeof desktopQuery.addListener === 'function') {
        desktopQuery.addListener(onViewportChange);
    }

    /* ---------------------------------------------- Livewire wire:navigate */
    document.addEventListener('livewire:navigate', function () {
        saveNavScroll();
        hideTooltip();
        closeFlyout();
        if (isDrawerOpen()) {
            closeDrawer({ skipFocus: true });
        }
    });

    function init() {
        // <html> survives navigations, so re-apply the stored state defensively
        root.classList.toggle(COLLAPSED_CLASS, storageGet(STORAGE_KEY) === '1');
        closeDrawer({ skipFocus: true });
        restoreAccordions();
        syncToggleButton();
        restoreNavScroll();
    }

    document.addEventListener('livewire:navigated', init);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
