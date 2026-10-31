<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Notifications\NewProjectMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class ProjectMessageController extends Controller
{
    public function store(Request $r, Project $project)
    {
        $this->authorize('view', $project);
        $admin = $r->user()->role === 'admin';
        $data = $r->validate(['body' => ['required', 'string', 'min:2', 'max:3000']]);
        $internal = $admin && $r->boolean('is_internal'); // internal notes: admins only, never shown to organizations
        $project->messages()->create(['user_id' => $r->user()->id, 'body' => $data['body'], 'is_internal' => $internal]);
        if (! $internal) {
            try {
                if ($admin && $project->user) {
                    $project->user->notify(new NewProjectMessage($project, false));
                } elseif ($admin && $project->submitter_email) {
                    Notification::route('mail', $project->submitter_email)->notify(new NewProjectMessage($project, false));
                } elseif (! $admin) {
                    Notification::route('mail', config('services.exposis.inbox'))->notify(new NewProjectMessage($project, true));
                }
            } catch (\Throwable $e) { report($e); }
        }
        return back()->with('status', $internal ? 'Note interne ajoutée.' : 'Message envoyé.');
    }
}
