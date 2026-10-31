/**
 * Teacher login page (نبذة)
 * - Password show/hide toggle
 * - Caps Lock warning on the password field
 * Vanilla JS. Uses event delegation so it keeps working after Livewire re-renders the form.
 */
(function () {
    'use strict';

    var PASSWORD_SELECTOR = '[data-nb-password]';
    var TOGGLE_SELECTOR = '[data-nb-password-toggle]';
    var CAPSLOCK_SELECTOR = '[data-nb-capslock]';

    var LABEL_SHOW = 'إظهار كلمة المرور';
    var LABEL_HIDE = 'إخفاء كلمة المرور';

    var isPasswordVisible = false;

    function getPasswordInput() {
        return document.querySelector(PASSWORD_SELECTOR);
    }

    /* ---------- Show / hide password ---------- */
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
                /* some input types don't support selection ranges */
            }
        }
    });

    /* ---------- Caps Lock warning ---------- */
    function setCapsLockWarning(isOn) {
        var hint = document.querySelector(CAPSLOCK_SELECTOR);
        var input = getPasswordInput();
        if (!hint) {
            return;
        }

        hint.hidden = !isOn;

        if (input) {
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

    /* ---------- Keep state after Livewire updates the DOM ---------- */
    var hooksRegistered = false;

    function registerLivewireHooks() {
        if (hooksRegistered || !window.Livewire || typeof window.Livewire.hook !== 'function') {
            return;
        }
        hooksRegistered = true;

        // Livewire morphing resets the password input's "type" to the server value.
        window.Livewire.hook('morph.updated', function (payload) {
            var el = payload && payload.el;
            if (el && el.matches && el.matches(PASSWORD_SELECTOR)) {
                applyPasswordVisibility();
            }
        });
    }

    registerLivewireHooks();
    document.addEventListener('livewire:init', registerLivewireHooks);
})();
