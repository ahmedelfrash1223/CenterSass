<div>
    <li class="pc-h-item pc-sidebar-collapse">
        <button type="button" class="pc-head-link ms-0 border-0 bg-transparent" wire:click="toggle">
            <i class="ti ti-menu-2"></i>
        </button>
    </li>

    <script>
        document.addEventListener('livewire:init', function () {
            Livewire.on('toggle-sidebar', function () {
                const sidebar = document.querySelector('.pc-sidebar');
                const overlay = document.querySelector('.pc-sidebar-overlay');

                if (window.innerWidth >= 1025) {
                    document.body.classList.toggle('pc-sidebar-hide');
                } else {
                    sidebar?.classList.toggle('mob-sidebar-active');
                    overlay?.classList.toggle('active');
                }
            });
        });
    </script>
</div>
