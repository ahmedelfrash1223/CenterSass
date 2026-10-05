<header class="pc-header">
    <div class="header-wrapper">
        <div class="me-auto pc-mob-drp">
            <ul class="list-unstyled">
                <li class="pc-h-item pc-sidebar-collapse">
                    <button type="button" class="pc-head-link ms-0 border-0 bg-transparent" id="sidebar-hide"
                            x-on:click="sidebarHidden = !sidebarHidden" x-bind:aria-expanded="!sidebarHidden"
                            aria-controls="student-sidebar" aria-label="طي أو إظهار القائمة">
                        <i class="ti ti-menu-2" aria-hidden="true"></i>
                    </button>
                </li>
                <li class="pc-h-item pc-sidebar-popup">
                    <button type="button" class="pc-head-link ms-0 border-0 bg-transparent" id="mobile-collapse"
                            x-on:click="sidebarOpen = !sidebarOpen" x-bind:aria-expanded="sidebarOpen"
                            aria-controls="student-sidebar" aria-label="فتح أو إغلاق القائمة">
                        <i class="ti ti-menu-2" aria-hidden="true"></i>
                    </button>
                </li>
            </ul>
        </div>
        <div class="ms-auto">
            <ul class="list-unstyled">
                @livewire('student-panel.parts.student-profile-card')
            </ul>
        </div>
    </div>
</header>
