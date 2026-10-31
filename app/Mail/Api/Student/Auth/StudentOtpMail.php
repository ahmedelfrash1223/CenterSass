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
        public int $expiresInMinutes = 5,
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
                'otp'              => $this->otp,
                'code'             => $this->otp->code,
                'expiresInMinutes' => $this->expiresInMinutes,
                'purposeLabel'     => $this->otp->purpose?->label() ?? '',
            ],
        );
    }
}
