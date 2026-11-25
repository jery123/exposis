<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Models\Evaluation;
use App\Models\Project;
use App\Models\PublicProject;
use App\Notifications\ProjectSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $activeStatuses = ['submitted', 'in_review', 'needs_info'];
        $resolvedStatuses = ['evaluated', 'accepted', 'declined'];

        $projects = $user->projects()->latest()->paginate(12);
        $communityProjects = $user->publicProjects()
            ->withCount('images')
            ->latest()
            ->limit(6)
            ->get();
        $communityProjectCount = $user->publicProjects()->count();
        $stats = [
            'total' => $user->projects()->count(),
            'active' => $user->projects()->whereIn('status', $activeStatuses)->count(),
            'resolved' => $user->projects()->whereIn('status', $resolvedStatuses)->count(),
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'projects' => $projects->getCollection()->map(fn (Project $project) => [
                    'id' => $project->id,
                    'reference' => $project->reference,
                    'title' => $project->title,
                    'type' => $project->typeLabel(),
                    'status' => $project->status,
                    'status_label' => $project->statusLabel(),
                    'created_at' => $project->created_at?->toIso8601String(),
                ])->values(),
                'pagination' => [
                    'current_page' => $projects->currentPage(),
                    'last_page' => $projects->lastPage(),
                    'total' => $projects->total(),
                ],
                'stats' => $stats,
                'community_projects' => $communityProjects->map(fn (PublicProject $project) => [
                    'slug' => $project->slug,
                    'title' => $project->title,
                    'pole' => $project->pole,
                    'status' => $project->status,
                    'images_count' => $project->images_count,
                    'published_at' => $project->published_at?->toIso8601String(),
                ])->values(),
                'community_projects_count' => $communityProjectCount,
                'status_message' => session('status'),
            ]);
        }

        return view('projects.index', compact('projects', 'stats', 'communityProjects', 'communityProjectCount'));
    }

    public function create(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'types' => Project::TYPES,
                'errors' => session('errors')?->getBag('default')->messages() ?? [],
                'old' => [
                    'title' => old('title'),
                    'request_type' => old('request_type'),
                    'problem' => old('problem'),
                    'description' => old('description'),
                    'timeline' => old('timeline'),
                    'budget' => old('budget'),
                    'confidential' => old('confidential'),
                    'consent' => old('consent'),
                ],
            ]);
        }

        return view('projects.create');
    }

    public function publicCreate()
    {
        return view('public.submit-project');
    }

    public function store(StoreProjectRequest $request)
    {
        $data = $request->safe()->except([
            'consent', 'website', 'contact_name', 'contact_email', 'contact_organization', 'contact_phone',
        ]);
        if ($request->user()) {
            $project = $request->user()->projects()->create($data);
        } else {
            $project = Project::create($data + [
                'submitter_name' => $request->validated('contact_name'),
                'submitter_email' => $request->validated('contact_email'),
                'submitter_organization' => $request->validated('contact_organization'),
                'submitter_phone' => $request->validated('contact_phone'),
            ]);
        }
        try { // a failed email must not fail the submission
            Notification::route('mail', config('services.exposis.inbox'))->notify(new ProjectSubmitted($project));
        } catch (\Throwable $e) {
            Log::warning('Project mail failed: '.$e->getMessage());
        }
        if ($request->expectsJson()) {
            return response()->json(['reference' => $project->reference], 201);
        }
        if (! $request->user()) {
            return redirect()->route('public.submissions.received')
                ->with('submission_reference', $project->reference);
        }

        return redirect()->route('projects.show', $project)->with('status', 'Merci ! Votre projet a bien été reçu. Aucun droit n’est transféré ; un NDA précède tout travail approfondi.');
    }

    public function show(Request $request, Project $project)
    {
        $this->authorize('view', $project);

        if ($request->expectsJson()) {
            $project->load('evaluation', 'messages.user');

            return response()->json([
                'project' => [
                    'id' => $project->id,
                    'reference' => $project->reference,
                    'title' => $project->title,
                    'type' => $project->typeLabel(),
                    'status' => $project->statusLabel(),
                    'nda_status' => Project::NDA[$project->nda_status] ?? $project->nda_status,
                    'problem' => $project->problem,
                    'description' => $project->description,
                    'messages' => $project->messages
                        ->where('is_internal', false)
                        ->map(fn ($message) => [
                            'author' => $message->user?->name ?? 'Équipe EXPOSIS',
                            'body' => $message->body,
                            'created_at' => $message->created_at?->toIso8601String(),
                        ])->values(),
                    'evaluation' => $project->evaluation?->shared ? [
                        'score' => $project->evaluation->score(),
                        'recommendation' => Evaluation::RECOMMENDATIONS[$project->evaluation->recommendation] ?? $project->evaluation->recommendation,
                        'summary' => $project->evaluation->summary,
                    ] : null,
                ],
                'status_message' => session('status'),
                'errors' => session('errors')?->getBag('default')->messages() ?? [],
            ]);
        }

        return view('projects.show', ['project' => $project->load('evaluation', 'messages.user')]);
    }
}
