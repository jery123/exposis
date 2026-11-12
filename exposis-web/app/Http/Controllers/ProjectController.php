<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use App\Notifications\ProjectSubmitted;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $activeStatuses = ['submitted', 'in_review', 'needs_info'];
        $resolvedStatuses = ['evaluated', 'accepted', 'declined'];

        return view('projects.index', [
            'projects' => $user->projects()->latest()->paginate(12),
            'stats' => [
                'total' => $user->projects()->count(),
                'active' => $user->projects()->whereIn('status', $activeStatuses)->count(),
                'resolved' => $user->projects()->whereIn('status', $resolvedStatuses)->count(),
            ],
        ]);
    }

    public function create() { return view('projects.create'); }

    public function publicCreate() { return view('public.submit-project'); }

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
        } catch (\Throwable $e) { Log::warning('Project mail failed: ' . $e->getMessage()); }
        if ($request->expectsJson()) {
            return response()->json(['reference' => $project->reference], 201);
        }
        if (! $request->user()) {
            return redirect()->route('public.submissions.received')
                ->with('submission_reference', $project->reference);
        }
        return redirect()->route('projects.show', $project)->with('status', 'Merci ! Votre projet a bien été reçu. Aucun droit n’est transféré ; un NDA précède tout travail approfondi.');
    }

    public function show(Project $project)
    {
        $this->authorize('view', $project);
        return view('projects.show', ['project' => $project->load('evaluation', 'messages.user')]);
    }
}
