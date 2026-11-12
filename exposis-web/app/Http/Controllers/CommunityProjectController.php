<?php

namespace App\Http\Controllers;

use App\Models\PublicProject;
use App\Models\PublicProjectImage;
use App\Models\PublicProjectReport;
use App\Notifications\CommunityProjectSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CommunityProjectController extends Controller
{
    public function index(Request $request)
    {
        return view('community.projects.index', [
            'projects' => $request->user()->publicProjects()->with(['report'])->withCount('images')->latest()->paginate(15),
        ]);
    }

    public function create()
    {
        return view('community.projects.create');
    }

    public function show(Request $request, PublicProject $project)
    {
        abort_unless($project->user_id === $request->user()->id, 403);

        return view('community.projects.show', [
            'project' => $project->load(['images', 'report']),
        ]);
    }

    public function image(Request $request, PublicProject $project, PublicProjectImage $image)
    {
        abort_unless($project->user_id === $request->user()->id, 403);
        abort_unless($image->public_project_id === $project->id, 404);
        abort_unless(Storage::disk('local')->exists($image->path), 404);

        return Storage::disk('local')->response($image->path, headers: [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:150'],
            'pole' => ['required', Rule::in(PublicProject::POLES)],
            'status' => ['required', Rule::in(['Recherche', 'Prototype', 'MVP', 'Terminé'])],
            'summary' => ['required', 'string', 'max:400'],
            'body' => ['required', 'string', 'max:50000'],
            'repo_url' => ['nullable', 'url', 'max:255'],
            'team_members' => ['nullable', 'string', 'max:2000'],
            'participant_consent' => ['accepted'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'report' => ['nullable', 'file', 'mimes:pdf,docx', 'max:20480'],
        ]);

        $imageFiles = $request->file('images', []);
        $reportFile = $request->file('report');
        $teamMembers = collect(preg_split('/\R/u', $data['team_members'] ?? ''))
            ->map(fn (string $name) => trim($name))
            ->filter()
            ->unique()
            ->take(20)
            ->values()
            ->all();
        unset($data['team_members'], $data['participant_consent'], $data['images'], $data['report']);

        $baseSlug = Str::slug($data['title']) ?: 'projet';
        do {
            $slug = $baseSlug.'-'.Str::lower(Str::random(6));
        } while (PublicProject::where('slug', $slug)->exists());

        $storedPaths = [];
        try {
            $project = DB::transaction(function () use ($request, $data, $slug, $teamMembers, $imageFiles, $reportFile, &$storedPaths) {
                $project = $request->user()->publicProjects()->create($data + [
                    'slug' => $slug,
                    'team_members' => $teamMembers,
                    'is_sample' => false,
                    'published_at' => null,
                ]);

                foreach ($imageFiles as $index => $image) {
                    $path = $image->store("community-projects/{$project->id}", 'local');
                    if ($path === false) {
                        throw new \RuntimeException('The project image could not be stored.');
                    }

                    $storedPaths[] = $path;
                    $project->images()->create([
                        'path' => $path,
                        'original_name' => mb_substr($image->getClientOriginalName(), 0, 200),
                        'sort_order' => $index,
                    ]);
                }

                if ($reportFile) {
                    $path = $reportFile->store("community-projects/{$project->id}/reports", 'local');
                    if ($path === false) {
                        throw new \RuntimeException('The project report could not be stored.');
                    }

                    $storedPaths[] = $path;
                    $project->report()->create([
                        'path' => $path,
                        'original_name' => mb_substr($reportFile->getClientOriginalName(), 0, 200),
                    ]);
                }

                return $project;
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($storedPaths);
            throw $exception;
        }

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

    public function storeReport(Request $request, PublicProject $project)
    {
        abort_unless($project->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'report' => ['required', 'file', 'mimes:pdf,docx', 'max:20480'],
        ]);
        $reportFile = $data['report'];
        $path = $reportFile->store("community-projects/{$project->id}/reports", 'local');
        if ($path === false) {
            throw new \RuntimeException('The project report could not be stored.');
        }

        $existing = $project->report;
        try {
            DB::transaction(function () use ($project, $path, $reportFile) {
                $report = $project->report()->firstOrNew();
                $report->fill([
                    'path' => $path,
                    'original_name' => mb_substr($reportFile->getClientOriginalName(), 0, 200),
                    'approved_at' => null,
                ]);
                $report->save();
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($path);
            throw $exception;
        }

        if ($existing && ! Storage::disk('local')->delete($existing->path)) {
            Log::warning('A replaced community project report could not be removed from private storage.', [
                'project_id' => $project->id,
                'report_id' => $existing->id,
            ]);
        }

        return redirect()->route('community.projects.index')
            ->with('status', 'Votre rapport a été transmis à l’équipe EXPOSIS. Il sera téléchargeable par tous après validation.');
    }
}
