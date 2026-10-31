<?php

namespace App\Services\Api\Student\Auth;

use App\Enums\Student\OtpChannelEnum;
use App\Enums\Student\OtpPurposeEnum;
use App\Mail\Api\Student\Auth\StudentOtpMail;
use App\Models\StudentAccount;
use App\Models\StudentOtp;
use App\Support\Api\Student\Auth\TokenHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class OtpService
{
    public const OTP_LENGTH = 6;
    public const OTP_TTL_MINUTES = 5;
    public const MAX_ATTEMPTS = 5;
    public const RESEND_COOLDOWN_SECONDS = 60;

    public function __construct(
        private readonly TokenHelper $tokenHelper,
    ) {}

    // =============================================
    // Send
    // =============================================

    public function send(string $identifier, OtpPurposeEnum $purpose): StudentOtp
    {
        return DB::transaction(function () use ($identifier, $purpose) {
            StudentOtp::where('identifier', $identifier)
                ->where('purpose', $purpose->value)
                ->delete();

            $otp = StudentOtp::create([
                'identifier' => $identifier,
                'channel'    => $this->resolveChannel($identifier)->value,
                'purpose'    => $purpose->value,
                'code'       => $this->generateCode(),
                'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
            ]);

            $this->dispatch($otp);

            return $otp;
        });
    }

    // =============================================
    // Verify
    // =============================================

    /**
     * @return array{token:string,account:StudentAccount}
     *
     * @throws ValidationException
     */
    public function verify(
        string $identifier,
        string $code,
        string $purpose,
        string $deviceName,
    ): array {
        $purposeEnum = OtpPurposeEnum::from($purpose);

        $otp = StudentOtp::where('identifier', $identifier)
            ->where('purpose', $purposeEnum->value)
            ->whereNull('verified_at')
            ->where('expires_at', '>', now())
            ->latest('created_at')
            ->first();

        if (! $otp) {
            throw ValidationException::withMessages([
                'code' => ['كود التحقق غير صالح أو منتهي الصلاحية.'],
            ]);
        }

        if ($otp->attempts >= self::MAX_ATTEMPTS) {
            throw ValidationException::withMessages([
                'code' => ['تم تجاوز عدد المحاولات. يرجى طلب كود جديد.'],
            ]);
        }

        if (! hash_equals($otp->code, $code)) {
            $otp->incrementAttempts();

            throw ValidationException::withMessages([
                'code' => ['كود التحقق غير صحيح.'],
            ]);
        }

        $otp->markAsVerified();

        $account = $this->resolveAccount($identifier);
        $this->markVerified($account, $identifier, $purposeEnum);

        return [
            'token'   => $this->tokenHelper->createToken($account, $deviceName),
            'account' => $account,
        ];
    }

    // =============================================
    // Resend
    // =============================================

    public function resend(string $identifier, string $purpose): void
    {
        $purposeEnum = OtpPurposeEnum::from($purpose);

        $recent = StudentOtp::where('identifier', $identifier)
            ->where('purpose', $purposeEnum->value)
            ->latest('created_at')
            ->first();

        if ($recent && $recent->created_at->diffInSeconds(now()) < self::RESEND_COOLDOWN_SECONDS) {
            throw ValidationException::withMessages([
                'identifier' => ['يرجى الانتظار قبل طلب كود جديد.'],
            ]);
        }

        $this->send($identifier, $purposeEnum);
    }

    // =============================================
    // Helpers
    // =============================================

    protected function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), self::OTP_LENGTH, '0', STR_PAD_LEFT);
    }

    protected function resolveChannel(string $identifier): OtpChannelEnum
    {
        return filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? OtpChannelEnum::EMAIL
            : OtpChannelEnum::SMS;
    }

    protected function resolveAccount(string $identifier): StudentAccount
    {
        $field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $account = StudentAccount::where($field, $identifier)->first();

        if (! $account) {
            throw ValidationException::withMessages([
                'identifier' => ['الحساب غير موجود.'],
            ]);
        }

        return $account;
    }

    protected function markVerified(StudentAccount $account, string $identifier, OtpPurposeEnum $purpose): void
    {
        if (in_array($purpose, [OtpPurposeEnum::REGISTER, OtpPurposeEnum::VERIFY_EMAIL], true)) {
            $account->markEmailAsVerified();
        }

        if ($purpose === OtpPurposeEnum::VERIFY_PHONE) {
            $account->markPhoneAsVerified();
        }
    }

    // =============================================
    // Dispatch (SMTP only)
    // =============================================

    protected function dispatch(StudentOtp $otp): void
    {
        if ($otp->channel === OtpChannelEnum::EMAIL) {
            try {
                Mail::to($otp->identifier)->send(
                    new StudentOtpMail($otp, self::OTP_TTL_MINUTES)
                );

                Log::info('OTP Email sent via SMTP', ['identifier' => $otp->identifier]);
            } catch (\Throwable $e) {
                Log::error('Failed to send OTP Email', [
                    'identifier' => $otp->identifier,
                    'error'      => $e->getMessage(),
                ]);

                throw ValidationException::withMessages([
                    'identifier' => ['تعذر إرسال كود التحقق. حاول لاحقًا.'],
                ]);
            }

            return;
        }

        // SMS dispatch — implement later
        Log::info('SMS OTP not implemented yet', ['identifier' => $otp->identifier]);
    }
}
