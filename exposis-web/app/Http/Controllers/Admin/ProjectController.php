<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Project;
use App\Notifications\ProjectStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    public function index(Request $r)
    {
        $projects = Project::with('user', 'evaluation')
            ->when($r->status, fn ($q, $s) => $q->where('status', $s))
            ->when($r->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('title', 'like', "%$s%")->orWhere('reference', 'like', "%$s%")))
            ->latest()->paginate(20)->withQueryString();
        return view('admin.projects.index', compact('projects'));
    }

    public function show(Project $project)
    {
        return view('admin.projects.show', ['project' => $project->load('user', 'evaluation.evaluator')]);
    }

    public function update(Request $r, Project $project)
    {
        $data = $r->validate(['status' => ['required', Rule::in(array_keys(Project::STATUSES))], 'nda_status' => ['required', Rule::in(array_keys(Project::NDA))]]);
        $changed = $project->status !== $data['status'];
        $project->update($data);
        if ($changed) {
            try {
                if ($project->user) {
                    $project->user->notify(new ProjectStatusChanged($project));
                } elseif ($project->submitter_email) {
                    Notification::route('mail', $project->submitter_email)->notify(new ProjectStatusChanged($project));
                }
            } catch (\Throwable $e) {
                report($e);
            }
        }
        return back()->with('status', 'Projet mis à jour.');
    }

    public function evaluate(Request $r, Project $project)
    {
        $data = $r->validate([
            'technical' => ['required', 'integer', 'between:1,5'], 'resources' => ['required', 'integer', 'between:1,5'],
            'impact' => ['required', 'integer', 'between:1,5'], 'risk' => ['required', 'integer', 'between:1,5'],
            'recommendation' => ['required', Rule::in(array_keys(Evaluation::RECOMMENDATIONS))],
            'summary' => ['required', 'string', 'min:20', 'max:5000'], 'shared' => ['boolean'],
        ]);
        $data['shared'] = $r->boolean('shared');
        $project->evaluation()->updateOrCreate(['project_id' => $project->id], $data + ['evaluator_id' => $r->user()->id]);
        if ($project->status !== 'accepted' && $project->status !== 'declined') $project->update(['status' => 'evaluated']);
        return back()->with('status', 'Évaluation enregistrée.');
    }
}
