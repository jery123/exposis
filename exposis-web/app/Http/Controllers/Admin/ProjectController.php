<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Project;
use App\Notifications\ProjectStatusChanged;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $r): View|JsonResponse
    {
        $projects = Project::with('user', 'evaluation')
            ->when($r->status, fn ($q, $s) => $q->where('status', $s))
            ->when($r->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('title', 'like', "%$s%")->orWhere('reference', 'like', "%$s%")))
            ->latest()->paginate(20)->withQueryString();

        if ($r->expectsJson()) {
            return response()->json([
                'projects' => $projects->getCollection()->map(fn (Project $project) => [
                    'id' => $project->id,
                    'reference' => $project->reference,
                    'title' => $project->title,
                    'organization' => $project->user?->organization_name ?? $project->user?->name ?? $project->submitter_organization ?? $project->submitter_name,
                    'status' => $project->status,
                    'status_label' => $project->statusLabel(),
                    'confidential' => $project->confidential,
                    'score' => $project->evaluation?->score(),
                ])->values(),
                'pagination' => [
                    'current_page' => $projects->currentPage(),
                    'last_page' => $projects->lastPage(),
                    'total' => $projects->total(),
                ],
            ]);
        }

        return view('admin.projects.index', compact('projects'));
    }

    public function show(Request $request, Project $project): View|JsonResponse
    {
        $project->load('user', 'evaluation.evaluator');

        if ($request->expectsJson()) {
            return response()->json([
                'project' => [
                    'id' => $project->id,
                    'reference' => $project->reference,
                    'title' => $project->title,
                    'organization' => $project->user?->organization_name ?? $project->submitter_organization ?? 'Porteur de projet',
                    'submitter_name' => $project->user?->name ?? $project->submitter_name,
                    'email' => $project->user?->email ?? $project->submitter_email,
                    'phone' => $project->user?->phone ?? $project->submitter_phone,
                    'type' => $project->typeLabel(),
                    'confidential' => $project->confidential,
                    'timeline' => $project->timeline,
                    'budget' => $project->budget,
                    'status' => $project->status,
                    'status_label' => $project->statusLabel(),
                    'nda_status' => $project->nda_status,
                    'nda_label' => Project::NDA[$project->nda_status],
                    'problem' => $project->problem,
                    'description' => $project->description,
                    'evaluation' => $project->evaluation ? [
                        'technical' => $project->evaluation->technical,
                        'resources' => $project->evaluation->resources,
                        'impact' => $project->evaluation->impact,
                        'risk' => $project->evaluation->risk,
                        'recommendation' => $project->evaluation->recommendation,
                        'summary' => $project->evaluation->summary,
                        'shared' => $project->evaluation->shared,
                        'score' => $project->evaluation->score(),
                    ] : null,
                ],
                'statuses' => Project::STATUSES,
                'nda_options' => Project::NDA,
                'recommendations' => \App\Models\Evaluation::RECOMMENDATIONS,
            ]);
        }

        return view('admin.projects.show', compact('project'));
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
