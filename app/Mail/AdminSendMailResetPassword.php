<?php

namespace App\Mail;

use App\Models\Admin;
use App\Models\AdminResetPasswordToken;
use App\Models\ResetPasswordToken;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminSendMailResetPassword extends Mailable
{
    use Queueable, SerializesModels;
    public $admin, $token;

    /**
     * Create a new message instance.
     */
    public function __construct(Admin $admin, AdminResetPasswordToken $token)
    {
        $this->admin = $admin;
        $this->token = $token;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Password LPG-Route',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'admin_template_email',
            with: [
                'token' => $this->token,
                'admin' => $this->admin
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
