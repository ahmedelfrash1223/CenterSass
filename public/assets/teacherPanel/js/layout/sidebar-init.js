/**
 * Runs synchronously in <head>: applies the saved collapsed-sidebar state
 * before the first paint so the layout doesn't jump. Behaviour lives in sidebar.js.
 */
(function () {
    try {
        if (window.localStorage.getItem('nb-sidebar-collapsed') === '1') {
            document.documentElement.classList.add('nb-sidebar-collapsed');
        }
    } catch (error) {
        /* storage unavailable (private mode, blocked): keep the default expanded sidebar */
    }
})();
