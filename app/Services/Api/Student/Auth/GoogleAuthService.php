<?php

namespace App\Services\Api\Student\Auth;

use App\Enums\Student\AuthProviderEnum;
use App\Models\StudentAccount;
use App\Support\Api\Student\Auth\TokenHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class GoogleAuthService
{
    public function __construct(
        private readonly TokenHelper $tokenHelper,
    ) {}

    /**
     * @return array{token:string,account:StudentAccount,is_new:bool}
     *
     * @throws ValidationException
     */
    public function authenticateWithIdToken(string $idToken, string $deviceName): array
    {
        $payload = $this->verifyGoogleIdToken($idToken);

        if (! $payload) {
            throw ValidationException::withMessages([
                'id_token' => ['Google ID token غير صالح.'],
            ]);
        }

        return DB::transaction(function () use ($payload, $deviceName) {
            $googleId = $payload['sub'];
            $email = $payload['email'] ?? null;
            $name = $payload['name'] ?? 'مستخدم Google';
            $avatar = $payload['picture'] ?? null;
            $emailVerified = (bool) ($payload['email_verified'] ?? false);

            // 1) Match by google_id
            $account = StudentAccount::where('google_id', $googleId)->first();
            $isNew = false;

            // 2) Fallback: match by email, link google_id
            if (! $account && $email) {
                $account = StudentAccount::where('email', $email)->first();

                if ($account) {
                    $account->update([
                        'google_id' => $googleId,
                        'provider' => AuthProviderEnum::GOOGLE->value,
                        'avatar' => $account->avatar ?? $avatar,
                        'email_verified_at' => $account->email_verified_at ?? ($emailVerified ? now() : null),
                    ]);
                }
            }

            // 3) Create new account
            if (! $account) {
                $account = StudentAccount::create([
                    'name' => $name,
                    'email' => $email,
                    'google_id' => $googleId,
                    'avatar' => $avatar,
                    'provider' => AuthProviderEnum::GOOGLE->value,
                    'email_verified_at' => $emailVerified ? now() : null,
                ]);
                $isNew = true;
            }

            if (! $account->canLogin()) {
                throw ValidationException::withMessages([
                    'id_token' => ['هذا الحساب غير نشط.'],
                ]);
            }

            return [
                'token' => $this->tokenHelper->createToken($account, $deviceName),
                'account' => $account,
                'is_new' => $isNew,
            ];
        });
    }

    // =============================================
    // Google ID Token Verification (via tokeninfo + proxy)
    // =============================================

    protected function verifyGoogleIdToken(string $idToken): ?array
    {
        try {
            // نستخدم نفس البروكسي بتاع Mailtrap عشان نعدي من الشبكة
            $proxy = config('services.mailtrap.proxy');

            $response = Http::withOptions(array_filter([
                'proxy'   => $proxy ?: null,
                'timeout' => 30,
            ]))
                ->get('https://oauth2.googleapis.com/tokeninfo', [
                    'id_token' => $idToken,
                ]);

            if ($response->failed()) {
                Log::error('Google tokeninfo failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                return null;
            }

            $payload = $response->json();

            // تأكد إن التوكن ده معمول للـ Client ID بتاعنا
            $expectedAudience = config('services.google.client_id');

            if (! $expectedAudience || ($payload['aud'] ?? null) !== $expectedAudience) {
                Log::warning('Google token audience mismatch', [
                    'expected' => $expectedAudience,
                    'got'      => $payload['aud'] ?? null,
                ]);

                return null;
            }

            return $payload;
        } catch (\Throwable $e) {
            Log::error('Google tokeninfo exception', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
