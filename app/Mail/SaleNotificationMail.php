<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\GeneralSetting;

class SaleNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $businessOwnerName;
    public $products;
    public $totalAmount;
    public $settings;

    /**
     * Create a new message instance.
     */
    public function __construct($businessOwnerName, $products, $totalAmount)
    {
        $this->businessOwnerName = $businessOwnerName;
        $this->products = $products;
        $this->totalAmount = $totalAmount;
        $this->settings = GeneralSetting::first();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Sale Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.sale-notification',
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