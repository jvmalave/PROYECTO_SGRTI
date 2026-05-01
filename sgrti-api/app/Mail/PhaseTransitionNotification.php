<?php

namespace App\Mail;

use App\Models\Core\Requirement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Clase PhaseTransitionNotification optimizada con tipado estricto.
 * Implementa ShouldQueue para el procesamiento asíncrono vía Redis[cite: 1].
 */
class PhaseTransitionNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Instancia del requerimiento técnico.
     * Se define el tipo explícito para resolver la advertencia de información de tipo[cite: 11].
     * 
     * @var Requirement
     */
    public Requirement $requirement;

    /**
     * Número de reintentos permitidos ante fallos de red o servidor de correo.
     * Cumple con los estándares de resiliencia del proyecto[cite: 1].
     * 
     * @var int
     */
    public int $tries = 3;

    /**
     * Tiempo de espera (en segundos) antes de reintentar un envío fallido.
     * Optimiza la gestión de carga en el worker de Redis[cite: 1].
     * 
     * @var int
     */
    public int $backoff = 60;

    /**
     * Constructor de la clase con inyección de dependencias tipada.
     */
    public function __construct(Requirement $requirement)
    {
        $this->requirement = $requirement;
    }

    /**
     * Define los metadatos del sobre del correo electrónico.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'SGRTI: Notificación de Avance - Requerimiento ' . $this->requirement->numero_rrti,
        );
    }

    /**
     * Especifica la ruta de la vista Blade y el contenido del mensaje.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.requirements.phase_changed',
        );
    }
}