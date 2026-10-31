/**
 * Nabza theme mode (light / dark / system) for the teacher dashboard.
 *
 * Loaded synchronously in <head> so the right mode is applied before the first paint.
 * <html data-pc-theme="light|dark"> is the single source of truth for the CSS
 * ([data-pc-theme=dark] selectors in the template and in nabza-*.css).
 * <html data-nb-theme-pref="light|dark|system"> is the teacher's saved preference (rendered by the server from the DB).
 *
 * Public API: window.NbTheme.apply('light'|'dark'|'system'), window.NbTheme.mode()
 * Emits: document 'nb:theme-change' (detail: { mode, preference })
 */
(function () {
    'use strict';

    var root = document.documentElement;
    var darkQuery = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;
    var SWITCH_CLASS = 'nb-theme-switching';
    var switchTimer = null;

    function preference() {
        var pref = root.getAttribute('data-nb-theme-pref');
        return pref === 'dark' || pref === 'system' ? pref : 'light';
    }

    function resolve(pref) {
        if (pref === 'system') {
            return darkQuery && darkQuery.matches ? 'dark' : 'light';
        }
        return pref === 'dark' ? 'dark' : 'light';
    }

    function currentMode() {
        return root.getAttribute('data-pc-theme') === 'dark' ? 'dark' : 'light';
    }

    /* ------------------------------------------------------------ charts */
    function cssVar(name, fallback) {
        var value = window.getComputedStyle(root).getPropertyValue(name);
        return value ? value.trim() : fallback;
    }

    function chartPalette() {
        var dark = currentMode() === 'dark';
        return {
            mode: dark ? 'dark' : 'light',
            text: cssVar('--nb-muted', dark ? '#9DAB9F' : '#5E6B60'),
            grid: cssVar('--nb-border', dark ? '#2A372E' : '#E1E6DA'),
            colors: dark
                ? ['#8CB98A', '#A4B388', '#5F8F64', '#E0A526']
                : ['#4A7350', '#A4B388', '#6A9268', '#E0A526']
        };
    }

    function apexOptions() {
        var p = chartPalette();
        return {
            colors: p.colors,
            chart: { foreColor: p.text, background: 'transparent', fontFamily: '"IBM Plex Sans Arabic", system-ui, sans-serif' },
            grid: { borderColor: p.grid },
            theme: { mode: p.mode },
            tooltip: { theme: p.mode },
            xaxis: { axisBorder: { color: p.grid }, axisTicks: { color: p.grid } }
        };
    }

    function applyChartTheme() {
        // Global defaults for every ApexCharts instance created from now on
        window.Apex = Object.assign({}, window.Apex || {}, apexOptions());

        // Update charts that already exist (ApexCharts keeps them in Apex._chartInstances)
        var instances = window.Apex && window.Apex._chartInstances;
        if (Array.isArray(instances)) {
            instances.forEach(function (entry) {
                if (entry && entry.chart && typeof entry.chart.updateOptions === 'function') {
                    try {
                        entry.chart.updateOptions(apexOptions(), false, false);
                    } catch (error) {
                        /* a chart that can't be updated keeps its colors */
                    }
                }
            });
        }
    }

    /* ------------------------------------------------------------- apply */
    function setMode(mode, animate) {
        if (animate) {
            root.classList.add(SWITCH_CLASS);
            window.clearTimeout(switchTimer);
            switchTimer = window.setTimeout(function () {
                root.classList.remove(SWITCH_CLASS);
            }, 260);
        }

        root.setAttribute('data-pc-theme', mode);
        root.style.colorScheme = mode;
    }

    function apply(pref) {
        pref = pref === 'dark' || pref === 'system' ? pref : 'light';
        var previous = currentMode();
        root.setAttribute('data-nb-theme-pref', pref);

        var mode = resolve(pref);
        setMode(mode, mode !== previous);

        applyChartTheme();
        document.dispatchEvent(new CustomEvent('nb:theme-change', { detail: { mode: mode, preference: pref } }));
    }

    /* Initial paint: no transition */
    setMode(resolve(preference()), false);

    /* Follow the OS when the preference is "system" */
    function onSystemChange() {
        if (preference() === 'system') {
            apply('system');
        }
    }

    if (darkQuery) {
        if (typeof darkQuery.addEventListener === 'function') {
            darkQuery.addEventListener('change', onSystemChange);
        } else if (typeof darkQuery.addListener === 'function') {
            darkQuery.addListener(onSystemChange);
        }
    }

    /* After wire:navigate the <html> attributes may be refreshed from the server: re-resolve */
    document.addEventListener('livewire:navigated', function () {
        setMode(resolve(preference()), false);
        applyChartTheme();
    });

    document.addEventListener('DOMContentLoaded', applyChartTheme);

    window.NbTheme = {
        apply: apply,
        mode: currentMode,
        preference: preference
    };
})();
