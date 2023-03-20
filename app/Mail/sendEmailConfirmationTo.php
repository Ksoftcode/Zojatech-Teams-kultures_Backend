<?php

namespace App\Mail;

use App\Models\users as ModelsUsers;
use App\Users;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class sendEmailConfirmationTo extends Mailable
{
    use Queueable, SerializesModels;
    // protected $mail;
    // protected $from = 'admin@example.com';
    // protected $to;
    // protected $view;
    // protected $data = [];
    public $url;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($url)
    {
        $this->url = $url;
    
    }
    public function sendEmailConfirmationTo(ModelsUsers $user)
    {
        $this->to = $user->email;
        $this->view = 'emails.confirm';
        $this->url= compact('user');

        $this->deliver();
    }
    public function deliver()
    {
        $this->url->send($this->view, $this->url, function ($message) {
            $message->from($this->from, 'Administrator')
                    ->to($this->to);
        });
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Send Email Confirmation To',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'view.name',
            text: 'Send Email Confirmation To'
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
