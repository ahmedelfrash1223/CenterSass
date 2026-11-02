/**
 * Teacher home page (نبذة)
 * - Greeting (صباح الخير / مساء الخير) by the time of day.
 * - Live session status in "حصص النهارده": جاية / شغالة دلوقتي / خلصت, re-checked
 *   every 30s in the app timezone (data-nb-tz on the page root), with the same
 *   rule as the server (start <= now <= end -> now, end < now -> past).
 *   When a session changes state, the dashboard Livewire component is refreshed
 *   so the now/next strip follows too (read-only re-render, no backend change).
 * - The weekly chart is SVG colored by CSS tokens, so it follows light / dark
 *   mode without any JS.
 *
 * Loaded on every page (defer, in <head>); does nothing unless [data-nb-home]
 * exists. Works with wire:navigate.
 */
(function () {
    'use strict';

    if (window.NbTeacherHome) {
        return;
    }

    var TICK_MS = 30000;
    var timer = null;
    var refreshQueued = false;

    var STATES = {
        now: { label: 'شغالة دلوقتي', pill: 'nb-pill--success' },
        past: { label: 'خلصت', pill: 'nb-pill--neutral' },
        upcoming: { label: 'جاية', pill: 'nb-pill--info' }
    };

    function pageRoot() {
        return document.querySelector('[data-nb-home]');
    }

    // "HH:MM" (24h) in the given IANA timezone; falls back to the browser clock
    function nowHM(timeZone) {
        try {
            var parts = new Intl.DateTimeFormat('en-GB', {
                timeZone: timeZone || undefined,
                hour: '2-digit',
                minute: '2-digit',
                hourCycle: 'h23'
            }).formatToParts(new Date());
            var hour = '';
            var minute = '';
            parts.forEach(function (part) {
                if (part.type === 'hour') { hour = part.value; }
                if (part.type === 'minute') { minute = part.value; }
            });
            if (hour && minute) {
                return hour + ':' + minute;
            }
        } catch (error) {
            /* unknown timezone: use the browser clock below */
        }
        var d = new Date();
        return String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0');
    }

    function sessionState(start, end, now) {
        if (start <= now && end >= now) {
            return 'now';
        }
        if (end < now) {
            return 'past';
        }
        return 'upcoming';
    }

    function updateGreeting(root, now) {
        var el = root.querySelector('[data-nb-greeting]');
        if (el) {
            el.textContent = parseInt(now.slice(0, 2), 10) < 12 ? 'صباح الخير' : 'مساء الخير';
        }
    }

    function updateSessions(root, now) {
        var changed = false;

        Array.prototype.forEach.call(root.querySelectorAll('[data-nb-session]'), function (item) {
            var start = item.getAttribute('data-start');
            var end = item.getAttribute('data-end');
            if (!start || !end) {
                return;
            }

            var state = sessionState(start, end, now);
            if (item.classList.contains('is-' + state)) {
                return;
            }
            changed = true;

            item.classList.remove('is-now', 'is-past', 'is-upcoming');
            item.classList.add('is-' + state);

            var pill = item.querySelector('[data-nb-session-pill]');
            if (pill) {
                pill.classList.remove('nb-pill--success', 'nb-pill--neutral', 'nb-pill--info');
                pill.classList.add(STATES[state].pill);
            }
            var label = item.querySelector('[data-nb-session-label]');
            if (label) {
                label.textContent = STATES[state].label;
            }
            var dot = item.querySelector('[data-nb-session-dot]');
            if (dot) {
                dot.hidden = state !== 'now';
            }
        });

        return changed;
    }

    // Re-render the dashboard Livewire component so the now/next strip matches
    function refreshDashboard(root) {
        if (refreshQueued || !window.Livewire || typeof window.Livewire.find !== 'function') {
            return;
        }
        var host = root.querySelector('.nb-home__body[wire\\:id]') || root.querySelector('[wire\\:id]');
        if (!host) {
            return;
        }
        var component = window.Livewire.find(host.getAttribute('wire:id'));
        if (!component || typeof component.$refresh !== 'function') {
            return;
        }
        refreshQueued = true;
        window.setTimeout(function () {
            refreshQueued = false;
            try {
                component.$refresh();
            } catch (error) {
                /* component gone (navigated away) */
            }
        }, 300);
    }

    function tick(options) {
        var root = pageRoot();
        if (!root) {
            stop();
            return;
        }
        var now = nowHM(root.getAttribute('data-nb-tz'));
        updateGreeting(root, now);
        var changed = updateSessions(root, now);
        if (changed && !(options && options.initial)) {
            refreshDashboard(root);
        }
    }

    function start() {
        stop();
        if (!pageRoot()) {
            return;
        }
        tick({ initial: true });
        timer = window.setInterval(tick, TICK_MS);
    }

    function stop() {
        if (timer) {
            window.clearInterval(timer);
            timer = null;
        }
    }

    // Re-check right after the user comes back to the tab
    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible' && pageRoot()) {
            tick();
        }
    });

    document.addEventListener('livewire:navigate', stop);
    document.addEventListener('livewire:navigated', start);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }

    window.NbTeacherHome = { refresh: tick };
})();
