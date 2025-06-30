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
    public $saleData;
    public $saleType;
    public $totalAmount;
    public $settings;

    /**
     * Create a new message instance.
     */
    public function __construct( $saleData, $businessOwnerName = null)
    {
        // $this->saleType = $saleType;
        $this->saleData = $saleData;
        // $this->totalAmount = $totalAmount;
        $this->settings = GeneralSetting::first();
        $this->businessOwnerName = $businessOwnerName ?? ($this->settings->site_name ?? 'Admin');
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