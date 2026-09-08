<form wire:submit.prevent="submit">

    <!------- Username / Email ------->
    <div class="mb-3">
        <input
            type="text"
            class="form-control"
            placeholder="اسم المستخدم أو البريد الإلكتروني أو رقم هاتفك"
            wire:model="login"
        >

        @error('login')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <!------- Password ------->
    <div class="mb-3">
        <input
            type="password"
            class="form-control"
            placeholder="كلمة المرور"
            wire:model="password"
        >

        @error('password')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <!------- Remember Me ------->
    <div class="d-flex mt-1 justify-content-between align-items-center">
        <div class="form-check">
            <input
                class="form-check-input input-primary"
                type="checkbox"
                id="rememberMe"
                wire:model="remember"
            >

            <label
                class="form-check-label text-muted"
                for="rememberMe"
            >
                تذكرني
            </label>
        </div>
    </div>

    <!------- Submit Button ------->
    <div class="d-grid mt-4">
        <button
            type="submit"
            class="btn btn-primary"
            wire:loading.attr="disabled"
            wire:target="submit"
        >

            <span
                wire:loading.remove
                wire:target="submit"
            >
                تسجيل الدخول
            </span>

            <div
                wire:loading
                wire:target="submit"
            >
                <span class="d-inline-flex align-items-center gap-2">
                    <span
                        class="spinner-border spinner-border-sm"
                        role="status"
                        aria-hidden="true"
                    ></span>

                    جاري تسجيل الدخول...
                </span>
            </div>

        </button>
    </div>

</form>
