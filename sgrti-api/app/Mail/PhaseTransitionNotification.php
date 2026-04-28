<?php

namespace App\Mail;

use App\Models\Core\Requirement;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PhaseTransitionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $requirement;

    public function __construct(Requirement $requirement)
    {
        $this->requirement = $requirement;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Notificación de Avance: Requerimiento ' . $this->requirement->numero_rrti,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.requirements.phase_changed', // Crearemos esta vista ahora
        );
    }
}