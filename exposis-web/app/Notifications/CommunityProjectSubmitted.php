<?php

namespace App\Notifications;

use App\Models\PublicProject;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommunityProjectSubmitted extends Notification
{
    public function __construct(public PublicProject $project) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("[EXPOSIS] Projet communautaire à valider : {$this->project->title}")
            ->line("Un membre a proposé « {$this->project->title} » pour publication.")
            ->line("Domaine : {$this->project->pole} · Avancement : {$this->project->status}")
            ->action('Examiner la proposition', route('admin.public-projects.edit', $this->project));
    }
}
