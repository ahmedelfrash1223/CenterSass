<div>
    {{-- ✅ Success Message --}}
    @if($success)
        <div class="alert alert-success d-flex align-items-center gap-2 mb-4"
             wire:key="flash-success-{{ md5($success) }}">
            <i class="ph-duotone ph-check-circle fs-5"></i>
            <span class="flex-grow-1">{{ $success }}</span>
            <button type="button"
                    class="btn-close"
                    wire:click="clear('success')"
                    title="إغلاق"></button>
        </div>

        <script>
            setTimeout(() => {
                @this.call('clear', 'success');
            }, 4000);
        </script>
    @endif

    {{-- ✅ Error Message --}}
    @if($error)
        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4"
             wire:key="flash-error-{{ md5($error) }}">
            <i class="ph-duotone ph-x-circle fs-5"></i>
            <span class="flex-grow-1">{{ $error }}</span>
            <button type="button"
                    class="btn-close"
                    wire:click="clear('error')"
                    title="إغلاق"></button>
        </div>

        <script>
            setTimeout(() => {
                @this.call('clear', 'error');
            }, 6000);
        </script>
    @endif

    {{-- ✅ Warning Message --}}
    @if($warning)
        <div class="alert alert-warning d-flex align-items-center gap-2 mb-4"
             wire:key="flash-warning-{{ md5($warning) }}">
            <i class="ph-duotone ph-warning-circle fs-5"></i>
            <span class="flex-grow-1">{{ $warning }}</span>
            <button type="button"
                    class="btn-close"
                    wire:click="clear('warning')"
                    title="إغلاق"></button>
        </div>

        <script>
            setTimeout(() => {
                @this.call('clear', 'warning');
            }, 5000);
        </script>
    @endif
</div>
