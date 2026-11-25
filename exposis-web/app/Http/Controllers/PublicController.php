<?php
namespace App\Http\Controllers;

use App\Models\PublicProject;
use App\Models\PublicProjectImage;
use App\Models\PublicProjectReport;
use App\Support\ReportDocumentRenderer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PublicController extends Controller
{
    public function home()
    {
        $featuredProjects = PublicProject::published()
            ->with('images')
            ->latest('published_at')
            ->limit(2)
            ->get();

        return view('public.home', compact('featuredProjects'));
    }
    public function about() { return view('public.about'); }
    public function services() { return view('public.services'); }
    public function contact() { return view('public.contact'); }
    public function legal() { return view('public.legal'); }

    public function projects(Request $r)
    {
        $pole = in_array($r->query('pole'), PublicProject::POLES, true) ? $r->query('pole') : null;
        $query = $r->query('q', '');
        $search = is_string($query) ? mb_substr(trim($query), 0, 100) : '';
        $driver = DB::connection()->getDriverName();
        $projects = PublicProject::published()
            ->with(['report', 'images'])
            ->when($pole, fn ($q) => $q->where('pole', $pole))
            ->when($search !== '', function ($query) use ($search, $driver) {
                $like = '%'.$search.'%';
                $query->where(function ($query) use ($like, $driver) {
                    $query->where('title', 'like', $like)
                        ->orWhere('summary', 'like', $like)
                        ->orWhere('body', 'like', $like)
                        ->orWhere('pole', 'like', $like);

                    if ($driver === 'sqlite') {
                        $query->orWhereExists(fn ($members) => $members
                            ->selectRaw('1')
                            ->fromRaw('json_each(public_projects.team_members) as team_member')
                            ->whereRaw('team_member.value LIKE ?', [$like]));
                    } elseif ($driver === 'mysql') {
                        $query->orWhereRaw("JSON_SEARCH(team_members, 'one', ?) IS NOT NULL", [$like]);
                    } elseif ($driver === 'pgsql') {
                        $query->orWhereExists(fn ($members) => $members
                            ->selectRaw('1')
                            ->fromRaw('json_array_elements_text(public_projects.team_members) as team_member(name)')
                            ->whereRaw('team_member.name ILIKE ?', [$like]));
                    }
                });
            })
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('public.projects', compact('projects', 'pole', 'search'));
    }

    public function apiProjects(Request $request)
    {
        $pole = in_array($request->query('pole'), PublicProject::POLES, true) ? $request->query('pole') : null;
        $search = mb_substr(trim((string) $request->query('q', '')), 0, 100);
        $driver = DB::connection()->getDriverName();
        $projects = PublicProject::published()
            ->when($pole, fn ($query) => $query->where('pole', $pole))
            ->when($search !== '', function ($query) use ($search, $driver) {
                $like = '%'.$search.'%';
                $query->where(function ($query) use ($like, $driver) {
                    $query->where('title', 'like', $like)
                        ->orWhere('summary', 'like', $like)
                        ->orWhere('body', 'like', $like)
                        ->orWhere('pole', 'like', $like);

                    if ($driver === 'sqlite') {
                        $query->orWhereExists(fn ($members) => $members
                            ->selectRaw('1')
                            ->fromRaw('json_each(public_projects.team_members) as team_member')
                            ->whereRaw('team_member.value LIKE ?', [$like]));
                    } elseif ($driver === 'mysql') {
                        $query->orWhereRaw("JSON_SEARCH(team_members, 'one', ?) IS NOT NULL", [$like]);
                    } elseif ($driver === 'pgsql') {
                        $query->orWhereExists(fn ($members) => $members
                            ->selectRaw('1')
                            ->fromRaw('json_array_elements_text(public_projects.team_members) as team_member(name)')
                            ->whereRaw('team_member.name ILIKE ?', [$like]));
                    }
                });
            })
            ->latest('published_at')
            ->with('report')
            ->get()
            ->map(fn (PublicProject $project) => $this->projectData($project))
            ->values();

        return response()->json(['projects' => $projects]);
    }

    public function apiProject(PublicProject $project)
    {
        abort_unless($project->published_at && $project->published_at->isPast(), 404);

        return response()->json(['project' => $this->projectData($project->load('images', 'report'), true)]);
    }

    public function projectImage(PublicProject $project, PublicProjectImage $image)
    {
        abort_unless($project->published_at && $project->published_at->isPast(), 404);
        abort_unless($image->public_project_id === $project->id, 404);
        abort_unless(Storage::disk('local')->exists($image->path), 404);

        return Storage::disk('local')->response($image->path, headers: [
            'Cache-Control' => 'public, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function projectReport(Request $request, PublicProject $project)
    {
        abort_unless($request->user(), 403);

        $report = $this->approvedPublishedReport($project);

        return Storage::disk('local')->download($report->path, $report->original_name, [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function reportPreview(Request $request, PublicProject $project, ReportDocumentRenderer $renderer)
    {
        $report = $this->approvedPublishedReport($project);
        $fullAccess = $request->user() !== null;
        $previewPages = $fullAccess ? null : $renderer->previewPageCount($report);

        return view('public.report-preview', compact('project', 'report', 'fullAccess', 'previewPages'));
    }

    public function reportPreviewPage(Request $request, PublicProject $project, ReportDocumentRenderer $renderer)
    {
        $report = $this->approvedPublishedReport($project);
        $page = (int) $request->validate([
            'page' => ['required', 'integer', 'min:1', 'max:1000'],
        ])['page'];

        return response($renderer->previewImage($report, $page), headers: [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
        ]);
    }

    public function reportViewer(Request $request, PublicProject $project, ReportDocumentRenderer $renderer)
    {
        abort_unless($request->user(), 403);

        return $renderer->inlineResponse($this->approvedPublishedReport($project));
    }

    public function project(PublicProject $project)
    {
        abort_unless($project->published_at && $project->published_at->isPast(), 404);
        $project->load(['images', 'report']);

        return view('public.project', compact('project'));
    }

    private function approvedPublishedReport(PublicProject $project): PublicProjectReport
    {
        abort_unless($project->published_at && $project->published_at->isPast(), 404);

        $report = $project->report;
        abort_unless($report?->approved_at && $report->approved_at->isPast(), 404);
        abort_unless(Storage::disk('local')->exists($report->path), 404);

        return $report;
    }

    private function projectData(PublicProject $project, bool $includeBody = false): array
    {
        return array_filter([
            'slug' => $project->slug,
            'title' => $project->title,
            'summary' => $project->summary,
            'body' => $includeBody ? $project->body : null,
            'pole' => $project->pole,
            'status' => $project->status,
            'repo_url' => $project->repo_url,
            'has_report' => (bool) ($project->report?->approved_at && $project->report->approved_at->isPast()),
            'report' => $includeBody && $project->report?->approved_at && $project->report->approved_at->isPast()
                ? [
                    'name' => $project->report->original_name,
                    'url' => "/api/public/projects/{$project->slug}/report",
                ]
                : null,
            'team_members' => $project->team_members ?? [],
            'images' => $includeBody ? $project->images->map(fn (PublicProjectImage $image) => [
                'id' => $image->id,
                'url' => "/api/public/projects/{$project->slug}/images/{$image->id}",
            ])->values() : null,
            'published_at' => $project->published_at?->toIso8601String(),
        ], fn ($value) => $value !== null);
    }
}
