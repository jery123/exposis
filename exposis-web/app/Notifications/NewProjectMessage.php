<?php
namespace App\Notifications;

use App\Models\Project;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewProjectMessage extends Notification
{
    public function __construct(public Project $project, public bool $toAdmin) {}
    public function via($n): array { return ['mail']; }
    public function toMail($n): MailMessage
    {
        $mail = (new MailMessage)->subject("Nouveau message sur le projet {$this->project->reference}")
            ->line('Un nouveau message a été publié sur le projet « ' . $this->project->title . ' ».');
        if ($this->toAdmin) {
            return $mail->action('Lire le message', route('admin.projects.show', $this->project));
        }

        return $this->project->user
            ? $mail->action('Lire le message', route('projects.show', $this->project))
            : $mail->line("Référence du projet : {$this->project->reference}.");
    }
}
