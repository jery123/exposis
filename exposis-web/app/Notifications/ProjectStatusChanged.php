<?php
namespace App\Notifications;

use App\Models\Project;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectStatusChanged extends Notification
{
    public function __construct(public Project $project) {}
    public function via($n): array { return ['mail']; }
    public function toMail($n): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("Votre projet {$this->project->reference} : {$this->project->statusLabel()}")
            ->line("Le statut de votre projet « {$this->project->title} » est maintenant : {$this->project->statusLabel()}.")
            ->line("Conservez cette référence pour tout échange avec EXPOSIS : {$this->project->reference}.");

        return $this->project->user
            ? $mail->action('Voir mon projet', route('projects.show', $this->project))
            : $mail;
    }
}
