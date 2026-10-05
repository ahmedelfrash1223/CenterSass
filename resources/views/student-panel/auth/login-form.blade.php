<form wire:submit.prevent="submit" class="student-login-form">
    <div class="mb-4">
        <label for="student-login" class="form-label">اسم المستخدم أو البريد الإلكتروني أو الهاتف</label>
        <input id="student-login" type="text" class="form-control @error('login') is-invalid @enderror"
               wire:model="login" autocomplete="username" required maxlength="255"
               placeholder="أدخل بيانات حسابك" aria-describedby="student-login-error" aria-invalid="{{ $errors->has('login') ? 'true' : 'false' }}">
        <div id="student-login-error" aria-live="polite">@error('login')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror</div>
    </div>
    <div class="mb-4" x-data="{ visible: false }">
        <label for="student-password" class="form-label">كلمة المرور</label>
        <div class="student-password-input">
            <input id="student-password" type="password" x-bind:type="visible ? 'text' : 'password'"
                   class="form-control @error('password') is-invalid @enderror" wire:model="password"
                   autocomplete="current-password" required maxlength="255" placeholder="أدخل كلمة المرور"
                   aria-describedby="student-password-error" aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}">
            <button type="button" class="student-password-toggle" x-on:click="visible = !visible"
                    x-bind:aria-label="visible ? 'إخفاء كلمة المرور' : 'إظهار كلمة المرور'" x-bind:aria-pressed="visible">
                <i class="ti" x-bind:class="visible ? 'ti-eye-off' : 'ti-eye'" aria-hidden="true"></i>
            </button>
        </div>
        <div id="student-password-error" aria-live="polite">@error('password')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror</div>
    </div>
    <button type="submit" class="btn btn-primary student-login-button w-100" wire:loading.attr="disabled" wire:target="submit">
        <span wire:loading.remove wire:target="submit">تسجيل الدخول <i class="ti ti-arrow-left ms-2" aria-hidden="true"></i></span>
        <span wire:loading wire:target="submit"><span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span> جاري تسجيل الدخول...</span>
    </button>
</form>
