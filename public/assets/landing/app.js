/* =========================================================
   نبذة | Theme + Landing Interactions
   ========================================================= */
(function () {
    "use strict";

    // ---------- Theme ----------
    const root = document.documentElement;
    const storageKey = "app-theme";

    function getSystemTheme() {
        return window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
    }

    function getSavedTheme() {
        const theme = localStorage.getItem(storageKey);
        return theme === "dark" || theme === "light" ? theme : getSystemTheme();
    }

    function updateThemeIcons() {
        const isDark = root.getAttribute("data-bs-theme") === "dark";
        const darkIcon = document.querySelector(".theme-icon-dark");
        const lightIcon = document.querySelector(".theme-icon-light");

        if (darkIcon) {
            darkIcon.classList.toggle("d-none", isDark);
        }

        if (lightIcon) {
            lightIcon.classList.toggle("d-none", !isDark);
        }
    }

    function applyTheme(theme) {
        const nextTheme = theme === "dark" ? "dark" : "light";

        root.setAttribute("data-bs-theme", nextTheme);
        document.body.setAttribute("data-bs-theme", nextTheme);
        localStorage.setItem(storageKey, nextTheme);

        updateThemeIcons();
    }

    function toggleTheme() {
        const currentTheme = root.getAttribute("data-bs-theme") || "light";
        const nextTheme = currentTheme === "dark" ? "light" : "dark";

        applyTheme(nextTheme);
    }

    // ---------- Initial Theme ----------
    applyTheme(getSavedTheme());

    // ---------- Theme Button ----------
    function bindThemeButton() {
        const button = document.getElementById("appThemeToggle");

        if (!button || button.dataset.themeBound === "true") {
            updateThemeIcons();
            return;
        }

        button.dataset.themeBound = "true";

        button.addEventListener("click", function (event) {
            event.preventDefault();
            event.stopPropagation();
            toggleTheme();
        });

        updateThemeIcons();
    }

    bindThemeButton();

    // ---------- Livewire Navigation ----------
    document.addEventListener("livewire:navigated", function () {
        applyTheme(localStorage.getItem(storageKey) || getSystemTheme());
        bindThemeButton();
    });

    // ---------- Portal / CTA click feedback ----------
    document.addEventListener("click", function (event) {
        const button = event.target.closest("[data-portal-btn]");

        if (!button) {
            return;
        }

        const label = button.getAttribute("data-portal-btn");

        const toast = document.createElement("div");
        toast.className = "position-fixed bottom-0 start-50 translate-middle-x mb-4 px-4 py-2 rounded-pill fw-semibold";
        toast.style.background = "var(--inverse-surface)";
        toast.style.color = "var(--inverse-on-surface)";
        toast.style.zIndex = "2000";
        toast.style.boxShadow = "var(--shadow-lg)";
        toast.innerHTML = `
            <span class="material-symbols-outlined align-middle me-1" style="font-size:18px;color:var(--primary-fixed);">lock_open</span>
            جاري تحويلك بأمان إلى ${label}...
        `;

        document.body.appendChild(toast);

        setTimeout(function () {
            toast.style.transition = "opacity .4s ease, transform .4s ease";
            toast.style.opacity = "0";
            toast.style.transform = "translate(-50%, 10px)";

            setTimeout(function () {
                toast.remove();
            }, 400);
        }, 2200);
    });
})();
