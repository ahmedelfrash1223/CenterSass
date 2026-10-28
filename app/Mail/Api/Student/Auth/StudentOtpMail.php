<?php

namespace App\Mail\Api\Student\Auth;

use App\Models\StudentOtp;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public StudentOtp $otp,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'كود التحقق - نبذة',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.api.student.auth.otp',
            with: [
                'otp' => $this->otp,
                'code' => $this->otp->code,
                'expiresInMinutes' => StudentOtp::class::OTP_TTL_MINUTES ?? 5,
                'purposeLabel' => $this->otp->purpose?->label() ?? '',
            ],
        );
    }
}
