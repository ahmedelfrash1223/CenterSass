'use strict';

/*
|--------------------------------------------------------------------------
| Admin Panel - Livewire Navigate Support
|--------------------------------------------------------------------------
*/

function initAdminPanel() {

    // =========================================================
    // Feather Icons
    // =========================================================

    if (typeof feather !== 'undefined') {
        feather.replace();
    }

    // =========================================================
    // Bootstrap Components
    // =========================================================

    initBootstrapComponents();

    // =========================================================
    // Sidebar Menu
    // =========================================================

    initSidebarMenu();

    // =========================================================
    // SimpleBar
    // =========================================================

    initSimpleBars();

    // =========================================================
    // Active Menu
    // =========================================================

    initActiveMenu();
}


/*
|--------------------------------------------------------------------------
| Bootstrap Components
|--------------------------------------------------------------------------
*/

function initBootstrapComponents() {

    if (typeof bootstrap === 'undefined') {
        return;
    }

    // Dropdown
    document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function (element) {

        bootstrap.Dropdown.getOrCreateInstance(element);

    });

    // Tooltip
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (element) {

        bootstrap.Tooltip.getOrCreateInstance(element);

    });

    // Popover
    document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (element) {

        bootstrap.Popover.getOrCreateInstance(element);

    });

    // Toast
    document.querySelectorAll('.toast').forEach(function (element) {

        bootstrap.Toast.getOrCreateInstance(element);

    });
}


/*
|--------------------------------------------------------------------------
| Sidebar Menu
|--------------------------------------------------------------------------
*/

function initSidebarMenu() {

    const navbar = document.querySelector('.pc-navbar');

    if (!navbar) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Prevent duplicate event listener
    |--------------------------------------------------------------------------
    */

    if (navbar.dataset.livewireInitialized === 'true') {
        return;
    }

    navbar.dataset.livewireInitialized = 'true';


    /*
    |--------------------------------------------------------------------------
    | Hide closed submenus
    |--------------------------------------------------------------------------
    */

    navbar.querySelectorAll('.pc-submenu').forEach(function (submenu) {

        const parent = submenu.parentElement;

        if (!parent.classList.contains('pc-trigger')) {
            submenu.style.display = 'none';
        }

    });


    /*
    |--------------------------------------------------------------------------
    | Main + Nested Menu Click
    |--------------------------------------------------------------------------
    */

    navbar.addEventListener('click', function (event) {

        const link = event.target.closest('a');

        if (!link || !navbar.contains(link)) {
            return;
        }

        const parent = link.parentElement;

        if (!parent || !parent.classList.contains('pc-hasmenu')) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | If the link has wire:navigate
        |--------------------------------------------------------------------------
        */

        if (link.hasAttribute('wire:navigate')) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();


        /*
        |--------------------------------------------------------------------------
        | Nested Menu
        |--------------------------------------------------------------------------
        */

        if (parent.parentElement.classList.contains('pc-submenu')) {

            if (parent.classList.contains('pc-trigger')) {

                parent.classList.remove('pc-trigger');

                const submenu = parent.children[1];

                if (submenu) {
                    slideUp(submenu, 200);
                }

            } else {

                const siblings = parent.parentElement.children;

                for (let i = 0; i < siblings.length; i++) {

                    const sibling = siblings[i];

                    if (sibling === parent) {
                        continue;
                    }

                    sibling.classList.remove('pc-trigger');

                    const submenu = sibling.children[1];

                    if (submenu) {
                        slideUp(submenu, 200);
                    }

                }

                parent.classList.add('pc-trigger');

                const submenu = parent.children[1];

                if (submenu) {
                    slideDown(submenu, 200);
                }

            }

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Main Menu
        |--------------------------------------------------------------------------
        */

        if (parent.classList.contains('pc-trigger')) {

            parent.classList.remove('pc-trigger');

            const submenu = parent.children[1];

            if (submenu) {

                slideUp(submenu, 200);

                setTimeout(function () {

                    submenu.removeAttribute('style');
                    submenu.style.display = 'none';

                }, 200);

            }

        } else {

            navbar.querySelectorAll(':scope > li.pc-trigger').forEach(function (item) {

                if (item === parent) {
                    return;
                }

                item.classList.remove('pc-trigger');

                const submenu = item.children[1];

                if (submenu) {

                    slideUp(submenu, 200);

                    setTimeout(function () {

                        submenu.removeAttribute('style');
                        submenu.style.display = 'none';

                    }, 200);

                }

            });


            parent.classList.add('pc-trigger');

            const submenu = parent.children[1];

            if (submenu) {
                slideDown(submenu, 200);
            }

        }

    });

}


/*
|--------------------------------------------------------------------------
| SimpleBar
|--------------------------------------------------------------------------
*/

function initSimpleBars() {

    if (typeof SimpleBar === 'undefined') {
        return;
    }

    const navbarContent = document.querySelector('.navbar-content');

    if (navbarContent && !navbarContent.dataset.simplebarInitialized) {

        navbarContent.dataset.simplebarInitialized = 'true';

        new SimpleBar(navbarContent);

    }


    const notificationScroll = document.querySelector('.header-notification-scroll');

    if (notificationScroll && !notificationScroll.dataset.simplebarInitialized) {

        notificationScroll.dataset.simplebarInitialized = 'true';

        new SimpleBar(notificationScroll);

    }


    const profileScroll = document.querySelector('.profile-notification-scroll');

    if (profileScroll && !profileScroll.dataset.simplebarInitialized) {

        profileScroll.dataset.simplebarInitialized = 'true';

        new SimpleBar(profileScroll);

    }


    const componentList = document.querySelector('.component-list-card .card-body');

    if (componentList && !componentList.dataset.simplebarInitialized) {

        componentList.dataset.simplebarInitialized = 'true';

        new SimpleBar(componentList);

    }

}


/*
|--------------------------------------------------------------------------
| Sidebar Hide
|--------------------------------------------------------------------------
*/

function toggleSidebar() {

    const sidebar = document.querySelector('.pc-sidebar');

    if (!sidebar) {
        return;
    }

    sidebar.classList.toggle('pc-sidebar-hide');

}


/*
|--------------------------------------------------------------------------
| Mobile Sidebar
|--------------------------------------------------------------------------
*/

function toggleMobileSidebar() {

    const sidebar = document.querySelector('.pc-sidebar');

    if (!sidebar) {
        return;
    }

    if (sidebar.classList.contains('mob-sidebar-active')) {

        rm_menu();

        return;

    }

    sidebar.classList.add('mob-sidebar-active');


    if (!sidebar.querySelector('.pc-menu-overlay')) {

        const overlay = document.createElement('div');

        overlay.className = 'pc-menu-overlay';

        sidebar.appendChild(overlay);

        overlay.addEventListener('click', function () {

            rm_menu();

        });

    }

}


/*
|--------------------------------------------------------------------------
| Remove Mobile Menu
|--------------------------------------------------------------------------
*/

function rm_menu() {

    const sidebar = document.querySelector('.pc-sidebar');

    const topbar = document.querySelector('.topbar');


    if (sidebar) {

        sidebar.classList.remove('mob-sidebar-active');

        const overlay = sidebar.querySelector('.pc-menu-overlay');

        if (overlay) {
            overlay.remove();
        }

    }


    if (topbar) {

        topbar.classList.remove('mob-sidebar-active');

        const overlay = topbar.querySelector('.pc-menu-overlay');

        if (overlay) {
            overlay.remove();
        }

    }

}


/*
|--------------------------------------------------------------------------
| Remove Overlay Menu
|--------------------------------------------------------------------------
*/

function remove_overlay_menu() {

    const sidebar = document.querySelector('.pc-sidebar');

    const topbar = document.querySelector('.topbar');


    if (sidebar) {

        sidebar.classList.remove('pc-over-menu-active');

        const overlay = sidebar.querySelector('.pc-menu-overlay');

        if (overlay) {
            overlay.remove();
        }

    }


    if (topbar) {

        topbar.classList.remove('mob-sidebar-active');

        const overlay = topbar.querySelector('.pc-menu-overlay');

        if (overlay) {
            overlay.remove();
        }

    }

}


/*
|--------------------------------------------------------------------------
| Active Menu
|--------------------------------------------------------------------------
*/

function initActiveMenu() {

    const links = document.querySelectorAll('.pc-sidebar .pc-navbar a');

    if (!links.length) {
        return;
    }

    const currentUrl = window.location.href.split(/[?#]/)[0];

    links.forEach(function (link) {

        if (
            link.href === currentUrl &&
            link.getAttribute('href') !== ''
        ) {

            const parent = link.parentElement;

            if (parent) {
                parent.classList.add('active');
            }

            const parentMenu = parent?.parentElement?.parentElement;

            if (parentMenu) {
                parentMenu.classList.add('pc-trigger');
                parentMenu.classList.add('active');
            }

        }

    });

}


/*
|--------------------------------------------------------------------------
| Sidebar Buttons
|--------------------------------------------------------------------------
*/

document.addEventListener('click', function (event) {

    /*
    |--------------------------------------------------------------------------
    | Sidebar Hide
    |--------------------------------------------------------------------------
    */

    const sidebarHide = event.target.closest('#sidebar-hide');

    if (sidebarHide) {

        event.preventDefault();

        toggleSidebar();

        return;

    }


    /*
    |--------------------------------------------------------------------------
    | Mobile Sidebar
    |--------------------------------------------------------------------------
    */

    const mobileCollapse = event.target.closest('#mobile-collapse');

    if (mobileCollapse) {

        event.preventDefault();

        toggleMobileSidebar();

        return;

    }


    /*
    |--------------------------------------------------------------------------
    | Overlay
    |--------------------------------------------------------------------------
    */

    const overlay = event.target.closest('.pc-menu-overlay');

    if (overlay) {

        rm_menu();

        return;

    }

});


/*
|--------------------------------------------------------------------------
| Keyboard Shortcut
|--------------------------------------------------------------------------
*/

document.addEventListener('keydown', function (event) {

    if (event.ctrlKey && event.keyCode === 75) {

        const input = document.querySelector('.form-search input');

        if (input) {
            input.focus();
        }

        event.preventDefault();

    }

});


/*
|--------------------------------------------------------------------------
| Livewire Initial Load
|--------------------------------------------------------------------------
*/

document.addEventListener('DOMContentLoaded', function () {

    initAdminPanel();

});


/*
|--------------------------------------------------------------------------
| Livewire Navigate
|--------------------------------------------------------------------------
*/

document.addEventListener('livewire:navigated', function () {

    initAdminPanel();

});


/*
|--------------------------------------------------------------------------
| Slide Up
|--------------------------------------------------------------------------
*/

function slideUp(target, duration = 0) {

    if (!target) {
        return;
    }

    target.style.transitionProperty = 'height, margin, padding';
    target.style.transitionDuration = duration + 'ms';
    target.style.boxSizing = 'border-box';
    target.style.height = target.offsetHeight + 'px';

    target.offsetHeight;

    target.style.overflow = 'hidden';
    target.style.height = 0;
    target.style.paddingTop = 0;
    target.style.paddingBottom = 0;
    target.style.marginTop = 0;
    target.style.marginBottom = 0;

}


/*
|--------------------------------------------------------------------------
| Slide Down
|--------------------------------------------------------------------------
*/

function slideDown(target, duration = 0) {

    if (!target) {
        return;
    }

    target.style.removeProperty('display');

    let display = window.getComputedStyle(target).display;

    if (display === 'none') {
        display = 'block';
    }

    target.style.display = display;

    const height = target.offsetHeight;

    target.style.overflow = 'hidden';
    target.style.height = 0;
    target.style.paddingTop = 0;
    target.style.paddingBottom = 0;
    target.style.marginTop = 0;
    target.style.marginBottom = 0;

    target.offsetHeight;

    target.style.boxSizing = 'border-box';
    target.style.transitionProperty = 'height, margin, padding';
    target.style.transitionDuration = duration + 'ms';
    target.style.height = height + 'px';

    target.style.removeProperty('padding-top');
    target.style.removeProperty('padding-bottom');
    target.style.removeProperty('margin-top');
    target.style.removeProperty('margin-bottom');


    window.setTimeout(function () {

        target.style.removeProperty('height');
        target.style.removeProperty('overflow');
        target.style.removeProperty('transition-duration');
        target.style.removeProperty('transition-property');

    }, duration);

}

(function () {
    'use strict';

    let initialized = false;

    function initFeather() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    function initBootstrap() {
        if (typeof bootstrap === 'undefined') {
            return;
        }

        document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function (element) {
            bootstrap.Dropdown.getOrCreateInstance(element);
        });

        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (element) {
            bootstrap.Tooltip.getOrCreateInstance(element);
        });

        document.querySelectorAll('[data-bs-toggle="popover"]').forEach(function (element) {
            bootstrap.Popover.getOrCreateInstance(element);
        });
    }

    function initSimpleBar() {
        if (typeof SimpleBar === 'undefined') {
            return;
        }

        document.querySelectorAll('.navbar-content').forEach(function (element) {
            if (!element.dataset.simplebarInitialized) {
                new SimpleBar(element);
                element.dataset.simplebarInitialized = 'true';
            }
        });

        document.querySelectorAll('.header-notification-scroll').forEach(function (element) {
            if (!element.dataset.simplebarInitialized) {
                new SimpleBar(element);
                element.dataset.simplebarInitialized = 'true';
            }
        });

        document.querySelectorAll('.profile-notification-scroll').forEach(function (element) {
            if (!element.dataset.simplebarInitialized) {
                new SimpleBar(element);
                element.dataset.simplebarInitialized = 'true';
            }
        });
    }

    function closeMobileSidebar() {
        const sidebar = document.querySelector('.pc-sidebar');
        const topbar = document.querySelector('.topbar');

        if (sidebar) {
            sidebar.classList.remove('mob-sidebar-active');
        }

        if (topbar) {
            topbar.classList.remove('mob-sidebar-active');
        }

        document.querySelectorAll('.pc-menu-overlay').forEach(function (overlay) {
            overlay.remove();
        });
    }

    function openMobileSidebar() {
        const sidebar = document.querySelector('.pc-sidebar');

        if (!sidebar) {
            return;
        }

        sidebar.classList.add('mob-sidebar-active');

        if (!sidebar.querySelector('.pc-menu-overlay')) {
            const overlay = document.createElement('div');

            overlay.className = 'pc-menu-overlay';

            overlay.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();

                closeMobileSidebar();
            });

            sidebar.appendChild(overlay);
        }
    }

    function toggleSidebar() {
        const sidebar = document.querySelector('.pc-sidebar');

        if (!sidebar) {
            return;
        }

        sidebar.classList.toggle('pc-sidebar-hide');
    }

    function toggleMobileSidebar() {
        const sidebar = document.querySelector('.pc-sidebar');

        if (!sidebar) {
            return;
        }

        if (sidebar.classList.contains('mob-sidebar-active')) {
            closeMobileSidebar();
        } else {
            openMobileSidebar();
        }
    }

    function initSidebarState() {
        const sidebar = document.querySelector('.pc-sidebar');

        if (!sidebar) {
            return;
        }

        const navbar = sidebar.querySelector('.pc-navbar');

        if (!navbar) {
            return;
        }

        navbar.querySelectorAll('.pc-submenu').forEach(function (submenu) {
            const parent = submenu.parentElement;

            if (!parent || !parent.classList.contains('pc-trigger')) {
                submenu.style.display = 'none';
            }
        });
    }

    function toggleMenuItem(item) {
        const submenu = item.querySelector(':scope > .pc-submenu');

        if (!submenu) {
            return;
        }

        const isOpen = item.classList.contains('pc-trigger');

        document.querySelectorAll('.pc-navbar li.pc-trigger').forEach(function (openItem) {
            if (openItem !== item) {
                openItem.classList.remove('pc-trigger');

                const openSubmenu = openItem.querySelector(':scope > .pc-submenu');

                if (openSubmenu) {
                    openSubmenu.style.display = 'none';
                }
            }
        });

        if (isOpen) {
            item.classList.remove('pc-trigger');
            submenu.style.display = 'none';
        } else {
            item.classList.add('pc-trigger');
            submenu.style.display = 'block';
        }
    }

    function initActiveMenu() {
        const currentUrl = window.location.href.split(/[?#]/)[0];

        document.querySelectorAll('.pc-sidebar .pc-navbar a').forEach(function (link) {
            const href = link.href;

            if (!href || href === '#' || link.getAttribute('href') === '') {
                return;
            }

            if (href === currentUrl) {
                const li = link.closest('li');

                if (li) {
                    li.classList.add('active');

                    const parentMenu = li.closest('.pc-hasmenu');

                    if (parentMenu) {
                        parentMenu.classList.add('pc-trigger');
                    }
                }
            }
        });
    }

    function initAdminPanel() {
        initFeather();
        initBootstrap();
        initSimpleBar();
        initSidebarState();
        initActiveMenu();
    }

    /*
    |--------------------------------------------------------------------------
    | Global Click Handler
    |--------------------------------------------------------------------------
    */

    document.addEventListener('click', function (event) {

        /*
        |--------------------------------------------------------------------------
        | Sidebar Desktop Toggle
        |--------------------------------------------------------------------------
        */

        const sidebarHide = event.target.closest('#sidebar-hide');

        if (sidebarHide) {
            event.preventDefault();
            event.stopPropagation();

            toggleSidebar();

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Mobile Sidebar Toggle
        |--------------------------------------------------------------------------
        */

        const mobileCollapse = event.target.closest('#mobile-collapse');

        if (mobileCollapse) {
            event.preventDefault();
            event.stopPropagation();

            toggleMobileSidebar();

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Sidebar Menu
        |--------------------------------------------------------------------------
        */

        const menuLink = event.target.closest('.pc-navbar li.pc-hasmenu > .pc-link');

        if (menuLink) {
            const li = menuLink.parentElement;

            if (!li) {
                return;
            }

            const submenu = li.querySelector(':scope > .pc-submenu');

            if (submenu) {
                event.preventDefault();
                event.stopPropagation();

                toggleMenuItem(li);
            }
        }
    });

    /*
    |--------------------------------------------------------------------------
    | Close Mobile Sidebar When Clicking Outside
    |--------------------------------------------------------------------------
    */

    document.addEventListener('click', function (event) {
        const sidebar = document.querySelector('.pc-sidebar');

        if (!sidebar) {
            return;
        }

        if (!sidebar.classList.contains('mob-sidebar-active')) {
            return;
        }

        const clickedInsideSidebar = event.target.closest('.pc-sidebar');
        const clickedMobileButton = event.target.closest('#mobile-collapse');

        if (!clickedInsideSidebar && !clickedMobileButton) {
            closeMobileSidebar();
        }
    });

    /*
    |--------------------------------------------------------------------------
    | Keyboard Shortcut
    |--------------------------------------------------------------------------
    */

    document.addEventListener('keydown', function (event) {
        if (event.ctrlKey && event.key.toLowerCase() === 'k') {
            const input = document.querySelector('.form-search input');

            if (input) {
                event.preventDefault();
                input.focus();
            }
        }
    });

    /*
    |--------------------------------------------------------------------------
    | Initial Page
    |--------------------------------------------------------------------------
    */

    document.addEventListener('DOMContentLoaded', function () {
        if (initialized) {
            return;
        }

        initialized = true;

        initAdminPanel();
    });

    /*
    |--------------------------------------------------------------------------
    | Livewire Navigate
    |--------------------------------------------------------------------------
    */

    document.addEventListener('livewire:navigated', function () {
        initAdminPanel();
    });

})();
