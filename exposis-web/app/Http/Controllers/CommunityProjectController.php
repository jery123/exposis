<?php

namespace App\Http\Controllers;

use App\Models\PublicProject;
use App\Notifications\CommunityProjectSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CommunityProjectController extends Controller
{
    public function index(Request $request)
    {
        return view('community.projects.index', [
            'projects' => $request->user()->publicProjects()->latest()->paginate(15),
        ]);
    }

    public function create()
    {
        return view('community.projects.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:150'],
            'pole' => ['required', Rule::in(PublicProject::POLES)],
            'status' => ['required', Rule::in(['Recherche', 'Prototype', 'MVP'])],
            'summary' => ['required', 'string', 'max:400'],
            'body' => ['required', 'string', 'max:50000'],
            'repo_url' => ['nullable', 'url', 'max:255'],
            'team_members' => ['nullable', 'string', 'max:2000'],
            'participant_consent' => ['accepted'],
        ]);

        $teamMembers = collect(preg_split('/\R/u', $data['team_members'] ?? ''))
            ->map(fn (string $name) => trim($name))
            ->filter()
            ->unique()
            ->take(20)
            ->values()
            ->all();
        unset($data['team_members'], $data['participant_consent']);

        $baseSlug = Str::slug($data['title']) ?: 'projet';
        do {
            $slug = $baseSlug.'-'.Str::lower(Str::random(6));
        } while (PublicProject::where('slug', $slug)->exists());

        $project = $request->user()->publicProjects()->create($data + [
            'slug' => $slug,
            'team_members' => $teamMembers,
            'is_sample' => false,
            'published_at' => null,
        ]);

        try {
            Notification::route('mail', config('services.exposis.inbox'))
                ->notify(new CommunityProjectSubmitted($project));
        } catch (\Throwable $exception) {
            report($exception);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'slug' => $project->slug,
                'status' => 'pending_review',
                'message' => 'Votre projet a été transmis à l’équipe EXPOSIS pour validation avant publication.',
            ], 201);
        }

        return redirect()->route('community.projects.index')
            ->with('status', 'Votre projet a été transmis à l’équipe EXPOSIS pour validation avant publication.');
    }
}
