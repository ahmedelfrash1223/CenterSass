/**
 * Teacher login page (نبذة)
 * - Password show/hide toggle
 * - Caps Lock warning
 * - Brand panel text slider
 * - Open book: fits the book to the viewport (scale by height, width stretches)
 * Submit loading/disabled state is handled by Livewire (wire:loading) on the button.
 * Vanilla JS, event delegation so it survives Livewire re-renders.
 */
(function () {
    'use strict';

    /* ======================================================================
       Password show / hide
       ====================================================================== */
    var PASSWORD_SELECTOR = '[data-nb-password]';
    var TOGGLE_SELECTOR = '[data-nb-password-toggle]';
    var CAPSLOCK_SELECTOR = '[data-nb-capslock]';

    var LABEL_SHOW = 'إظهار كلمة المرور';
    var LABEL_HIDE = 'إخفاء كلمة المرور';

    var isPasswordVisible = false;

    function getPasswordInput() {
        return document.querySelector(PASSWORD_SELECTOR);
    }

    function applyPasswordVisibility() {
        var input = getPasswordInput();
        var toggle = document.querySelector(TOGGLE_SELECTOR);

        if (input) {
            input.type = isPasswordVisible ? 'text' : 'password';
        }

        if (toggle) {
            toggle.setAttribute('aria-pressed', isPasswordVisible ? 'true' : 'false');
            toggle.setAttribute('aria-label', isPasswordVisible ? LABEL_HIDE : LABEL_SHOW);
        }
    }

    document.addEventListener('click', function (event) {
        var toggle = event.target.closest(TOGGLE_SELECTOR);
        if (!toggle) {
            return;
        }

        isPasswordVisible = !isPasswordVisible;
        applyPasswordVisibility();

        var input = getPasswordInput();
        if (input) {
            input.focus();
            try {
                var end = input.value.length;
                input.setSelectionRange(end, end);
            } catch (error) {
                /* selection not supported for this input type */
            }
        }
    });

    /* ======================================================================
       Caps Lock warning
       ====================================================================== */
    function setCapsLockWarning(isOn) {
        var hint = document.querySelector(CAPSLOCK_SELECTOR);
        var input = getPasswordInput();
        if (!hint) {
            return;
        }

        hint.hidden = !isOn;

        if (!input) {
            return;
        }

        var describedBy = (input.getAttribute('aria-describedby') || '')
            .split(' ')
            .filter(function (id) { return id && id !== hint.id; });

        if (isOn) {
            describedBy.push(hint.id);
        }

        if (describedBy.length) {
            input.setAttribute('aria-describedby', describedBy.join(' '));
        } else {
            input.removeAttribute('aria-describedby');
        }
    }

    function handleCapsLockKey(event) {
        if (!event.target.matches || !event.target.matches(PASSWORD_SELECTOR)) {
            return;
        }
        if (typeof event.getModifierState !== 'function') {
            return;
        }
        setCapsLockWarning(event.getModifierState('CapsLock'));
    }

    document.addEventListener('keydown', handleCapsLockKey);
    document.addEventListener('keyup', handleCapsLockKey);

    document.addEventListener('focusout', function (event) {
        if (event.target.matches && event.target.matches(PASSWORD_SELECTOR)) {
            setCapsLockWarning(false);
        }
    });

    /* Livewire morphing resets the password input's "type"; re-apply after each update */
    var hooksRegistered = false;

    function registerLivewireHooks() {
        if (hooksRegistered || !window.Livewire || typeof window.Livewire.hook !== 'function') {
            return;
        }
        hooksRegistered = true;

        window.Livewire.hook('morph.updated', function (payload) {
            var el = payload && payload.el;
            if (el && el.matches && el.matches(PASSWORD_SELECTOR)) {
                applyPasswordVisibility();
            }
        });
    }

    registerLivewireHooks();
    document.addEventListener('livewire:init', registerLivewireHooks);

    /* ======================================================================
       Brand panel slider
       ====================================================================== */
    var SLIDE_INTERVAL_MS = 5000;

    function initSlider(slider) {
        var slides = Array.prototype.slice.call(slider.querySelectorAll('[data-nb-slide]'));
        var dots = Array.prototype.slice.call(slider.querySelectorAll('[data-nb-dot]'));
        if (slides.length < 2) {
            return;
        }

        var reducedMotionQuery = window.matchMedia
            ? window.matchMedia('(prefers-reduced-motion: reduce)')
            : null;

        var currentIndex = 0;
        var timerId = null;
        var isHovered = false;
        var hasFocus = false;

        function showSlide(index) {
            currentIndex = (index + slides.length) % slides.length;

            slides.forEach(function (slide, i) {
                var isActive = i === currentIndex;
                slide.classList.toggle('is-active', isActive);
                if (isActive) {
                    slide.removeAttribute('aria-hidden');
                } else {
                    slide.setAttribute('aria-hidden', 'true');
                }
            });

            dots.forEach(function (dot, i) {
                var isActive = i === currentIndex;
                dot.classList.toggle('is-active', isActive);
                if (isActive) {
                    dot.setAttribute('aria-current', 'true');
                } else {
                    dot.removeAttribute('aria-current');
                }
            });
        }

        function canAutoplay() {
            var prefersReduced = reducedMotionQuery && reducedMotionQuery.matches;
            return !prefersReduced && !isHovered && !hasFocus && !document.hidden;
        }

        function stop() {
            if (timerId !== null) {
                window.clearInterval(timerId);
                timerId = null;
            }
        }

        function start() {
            stop();
            if (canAutoplay()) {
                timerId = window.setInterval(function () {
                    showSlide(currentIndex + 1);
                }, SLIDE_INTERVAL_MS);
            }
        }

        dots.forEach(function (dot) {
            dot.addEventListener('click', function () {
                showSlide(parseInt(dot.getAttribute('data-nb-dot'), 10) || 0);
                start(); // restart the timer so the chosen slide gets a full interval
            });
        });

        slider.addEventListener('mouseenter', function () { isHovered = true; stop(); });
        slider.addEventListener('mouseleave', function () { isHovered = false; start(); });
        slider.addEventListener('focusin', function () { hasFocus = true; stop(); });
        slider.addEventListener('focusout', function (event) {
            if (!slider.contains(event.relatedTarget)) {
                hasFocus = false;
                start();
            }
        });

        document.addEventListener('visibilitychange', start);

        if (reducedMotionQuery) {
            var onMotionChange = function () { start(); };
            if (typeof reducedMotionQuery.addEventListener === 'function') {
                reducedMotionQuery.addEventListener('change', onMotionChange);
            } else if (typeof reducedMotionQuery.addListener === 'function') {
                reducedMotionQuery.addListener(onMotionChange);
            }
        }

        showSlide(0);
        start();
    }

    function initSliders() {
        Array.prototype.forEach.call(document.querySelectorAll('[data-nb-slider]'), initSlider);
    }

    /* ======================================================================
       Open book: fit the book to the viewport
       - Scale from the height: (innerHeight - 32) / 860, clamped 0.6–1.25.
       - Design width stretches to fill: (innerWidth - 32) / scale, clamped
         1280–2000px. If even 1280 design px don't fit the width (narrow
         screens), the scale is lowered so the book still fits.
       Sets --nb-book-scale / --nb-book-w on [data-nb-book-fit] and
       body.nb-login--book / body.nb-login--card (single page card).
       ====================================================================== */
    var BOOK_H = 860;
    var BOOK_MIN_W = 1280;
    var BOOK_MAX_W = 2000;
    var BOOK_MIN_SCALE = 0.6;
    var BOOK_MAX_SCALE = 1.25;
    var BOOK_GUTTER = 32; // 16px around the book
    var BOOK_MIN_VIEWPORT = 900;
    var fitFrame = null;

    function clamp(value, min, max) {
        return Math.min(max, Math.max(min, value));
    }

    function fitBook() {
        fitFrame = null;
        var fit = document.querySelector('[data-nb-book-fit]');
        if (!fit || !document.body) {
            return;
        }

        var availW = window.innerWidth - BOOK_GUTTER;
        var availH = window.innerHeight - BOOK_GUTTER;

        // height decides the scale; never wider than the screen allows
        var scale = Math.min(availH / BOOK_H, availW / BOOK_MIN_W, BOOK_MAX_SCALE);
        var isBook = scale >= BOOK_MIN_SCALE && window.innerWidth >= BOOK_MIN_VIEWPORT;

        scale = clamp(scale, BOOK_MIN_SCALE, BOOK_MAX_SCALE);
        var width = clamp(availW / scale, BOOK_MIN_W, BOOK_MAX_W);

        fit.style.setProperty('--nb-book-scale', scale.toFixed(4));
        fit.style.setProperty('--nb-book-w', Math.floor(width) + 'px');
        document.body.classList.toggle('nb-login--book', isBook);
        document.body.classList.toggle('nb-login--card', !isBook);

        if (isBook) {
            fitContent(fit, width);
        } else {
            fit.style.removeProperty('--nb-content-scale');
        }
    }

    /* ----------------------------------------------------------------------
       Content scale: the page content grows with the book width,
       clamp(width / 1280, 1, 1.15), but never more than the page height
       allows (validation errors, Caps Lock hint...). Never below 0.9; past
       that the form page scrolls inside itself as a last resort.
       Heights are layout values (offsetHeight), so the transforms don't
       affect the measurement.
       ---------------------------------------------------------------------- */
    var CONTENT_MIN_SCALE = 0.9;
    var CONTENT_MAX_SCALE = 1.15;

    function px(style, prop) {
        return parseFloat(style[prop]) || 0;
    }

    function outerHeight(el) {
        if (!el) {
            return 0;
        }
        var style = window.getComputedStyle(el);
        return el.offsetHeight + px(style, 'marginTop') + px(style, 'marginBottom');
    }

    function paddingY(el) {
        var style = window.getComputedStyle(el);
        return px(style, 'paddingTop') + px(style, 'paddingBottom');
    }

    // unscaled height the page content needs
    function contentHeight(page) {
        var inner = page.querySelector('[data-nb-page-inner]');
        if (!inner) {
            return 0;
        }
        var center = inner.querySelector('.nb-login__center');
        var total = paddingY(inner);

        Array.prototype.forEach.call(inner.children, function (child) {
            if (child === center) {
                // the middle row stretches (1fr): count the form block, not the row
                total += paddingY(center) + outerHeight(center.firstElementChild);
            } else {
                total += outerHeight(child);
            }
        });
        return total;
    }

    function fitContent(fit, bookWidth) {
        var pages = fit.querySelectorAll('.nb-login__main, .nb-brand');
        var wanted = clamp(bookWidth / BOOK_MIN_W, 1, CONTENT_MAX_SCALE);
        var current = wanted;

        // two passes: a new scale changes the line wrapping, so measure again
        for (var pass = 0; pass < 2; pass++) {
            fit.style.setProperty('--nb-content-scale', current.toFixed(4));
            var next = wanted;
            Array.prototype.forEach.call(pages, function (page) {
                var need = contentHeight(page);
                if (need > 0 && page.clientHeight > 0) {
                    next = Math.min(next, page.clientHeight / need);
                }
            });
            next = clamp(next, CONTENT_MIN_SCALE, CONTENT_MAX_SCALE);
            if (Math.abs(next - current) < 0.002) {
                break;
            }
            current = next;
        }
        fit.style.setProperty('--nb-content-scale', current.toFixed(4));
    }

    function requestFit() {
        if (fitFrame === null) {
            fitFrame = window.requestAnimationFrame(fitBook);
        }
    }

    fitBook();
    window.addEventListener('resize', requestFit);
    window.addEventListener('orientationchange', requestFit);

    // Re-fit when the content changes height: validation errors, the Caps Lock
    // hint, web fonts finishing loading. The scale it sets settles in a frame.
    if (typeof window.ResizeObserver === 'function') {
        var contentObserver = new window.ResizeObserver(requestFit);
        Array.prototype.forEach.call(
            document.querySelectorAll('.nb-login__form-block, .nb-slider'),
            function (el) { contentObserver.observe(el); }
        );
    }
    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(requestFit);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSliders);
    } else {
        initSliders();
    }
})();
