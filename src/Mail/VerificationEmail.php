<?php

namespace BagistoPackages\Shop\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var array
     */
    public $verificationData;

    /**
     * Create a new mailable instance.
     *
     * @param array $verificationData
     * @return void
     */
    public function __construct($verificationData)
    {
        $this->verificationData = $verificationData;
    }

    /**
     * Build the message.
     *
     * @return VerificationEmail
     */
    public function build()
    {
        return $this->from(core()->getSenderEmailDetails()['email'], core()->getSenderEmailDetails()['name'])
            ->to($this->verificationData['email'])
            ->subject(trans('shop::app.mail.customer.verification.subject'))
            ->view('shop::emails.customer.verification-email')
            ->with('data', [
                    'email' => $this->verificationData['email'],
                    'token' => $this->verificationData['token'],
                ]
            );
    }
}
