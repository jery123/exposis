<?php
namespace App\Notifications;

use App\Models\Project;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectSubmitted extends Notification
{
    public function __construct(public Project $project) {}
    public function via($n): array { return ['mail']; }
    public function toMail($n): MailMessage
    {
        $p = $this->project->loadMissing('user');
        $email = $p->user?->email ?? $p->submitter_email;
        $name = $p->user?->organization_name ?: $p->user?->name ?: $p->submitter_organization ?: $p->submitter_name;
        return (new MailMessage)
            ->subject("[EXPOSIS] Nouveau projet {$p->reference} : {$p->title}")
            ->replyTo($email, $name)
            ->line("Contact : {$name} ({$email})")
            ->line("Type : {$p->typeLabel()}" . ($p->confidential ? ' (confidentiel)' : ''))
            ->action('Examiner le projet', route('admin.projects.show', $p));
    }
}
