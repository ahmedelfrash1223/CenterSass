/**
 * Teacher login page (نبذة)
 * - Password show/hide toggle
 * - Caps Lock warning
 * - Brand panel text slider
 * - Open book: scales the 1280x860 book to the viewport (body.nb-login--book / --card)
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
       Open book: scale the 1280x860 book to the viewport
       Sets --nb-book-scale on [data-nb-book-fit] and body.nb-login--book /
       body.nb-login--card (too small to read -> single page card).
       ====================================================================== */
    var BOOK_W = 1280;
    var BOOK_H = 860;
    var BOOK_MAX_SCALE = 1.1;
    var BOOK_MIN_SCALE = 0.6;
    var fitFrame = null;

    function fitBook() {
        fitFrame = null;
        var fit = document.querySelector('[data-nb-book-fit]');
        if (!fit || !document.body) {
            return;
        }

        var scale = Math.min(
            BOOK_MAX_SCALE,
            (window.innerWidth - 48) / BOOK_W,
            (window.innerHeight - 40) / BOOK_H
        );
        var isBook = scale >= BOOK_MIN_SCALE;

        fit.style.setProperty('--nb-book-scale', scale.toFixed(4));
        document.body.classList.toggle('nb-login--book', isBook);
        document.body.classList.toggle('nb-login--card', !isBook);
    }

    function requestFit() {
        if (fitFrame === null) {
            fitFrame = window.requestAnimationFrame(fitBook);
        }
    }

    fitBook();
    window.addEventListener('resize', requestFit);
    window.addEventListener('orientationchange', requestFit);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSliders);
    } else {
        initSliders();
    }
})();
