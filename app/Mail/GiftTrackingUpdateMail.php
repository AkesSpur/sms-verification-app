<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GiftTrackingUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $order;
    public $trackingInfo;
 
    public function __construct($user, $order, $trackingInfo)
    {
        $this->user = $user;
        $this->order = $order;
        $this->trackingInfo = $trackingInfo;
    }

    public function build()
    {
        return $this->view('mail.gift-tracking-update')
                    ->subject('Your Gift Tracking Info has been Updated');
    }


    // /**
    //  * Get the message envelope.
    //  */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Gift Tracking Update Mail',
    //     );
    // }

    // /**
    //  * Get the message content definition.
    //  */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

    // /**
    //  * Get the attachments for the message.
    //  *
    //  * @return array<int, \Illuminate\Mail\Mailables\Attachment>
    //  */
    // public function attachments(): array
    // {
    //     return [];
    // }
}

