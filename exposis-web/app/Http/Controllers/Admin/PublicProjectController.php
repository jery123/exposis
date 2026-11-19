<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublicProject;
use App\Models\PublicProjectImage;
use App\Models\PublicProjectReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PublicProjectController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $items = PublicProject::with(['user', 'report'])->withCount('images')->latest()->paginate(20);

        if ($request->expectsJson()) {
            return response()->json([
                'items' => $items->getCollection()->map(fn (PublicProject $item) => [
                    'id' => $item->id,
                    'title' => $item->title,
                    'pole' => $item->pole,
                    'status' => $item->status,
                    'published_at' => $item->published_at?->toIso8601String(),
                    'images_count' => $item->images_count,
                    'submitter' => $item->user?->name,
                    'report' => $item->report ? [
                        'name' => $item->report->original_name,
                        'approved' => (bool) $item->report->approved_at,
                    ] : null,
                ])->values(),
                'pagination' => [
                    'current_page' => $items->currentPage(),
                    'last_page' => $items->lastPage(),
                    'total' => $items->total(),
                ],
                'poles' => PublicProject::POLES,
                'statuses' => PublicProject::STATUSES,
            ]);
        }

        return view('admin.public-projects.index', compact('items'));
    }

    public function create(Request $request): View|JsonResponse
    {
        $item = new PublicProject(['status' => 'Prototype']);

        if ($request->expectsJson()) {
            return response()->json([
                'item' => $this->itemData($item),
                'poles' => PublicProject::POLES,
                'statuses' => PublicProject::STATUSES,
            ]);
        }

        return view('admin.public-projects.form', compact('item'));
    }

    public function edit(Request $request, PublicProject $public_project): View|JsonResponse
    {
        $item = $public_project->load('images', 'report');

        if ($request->expectsJson()) {
            return response()->json([
                'item' => $this->itemData($item),
                'poles' => PublicProject::POLES,
                'statuses' => PublicProject::STATUSES,
            ]);
        }

        return view('admin.public-projects.form', compact('item'));
    }

    public function store(Request $r) { PublicProject::create($this->data($r)); return redirect()->route('admin.public-projects.index')->with('status', 'Projet créé.'); }
    public function update(Request $r, PublicProject $public_project)
    {
        $public_project->update($this->data($r, $public_project));

        if ($public_project->report()->exists()) {
            $report = $public_project->report;
            $report->update([
                'approved_at' => $r->boolean('approve_report')
                    ? ($report->approved_at ?? now())
                    : null,
            ]);
        }

        return redirect()->route('admin.public-projects.index')->with('status', 'Projet mis à jour.');
    }
    public function destroy(PublicProject $public_project)
    {
        Storage::disk('local')->delete($public_project->images()->pluck('path')->all());
        $report = $public_project->report;
        if ($report && Storage::disk('local')->exists($report->path) && ! Storage::disk('local')->delete($report->path)) {
            Log::warning('A deleted community project report could not be removed from private storage.', [
                'project_id' => $public_project->id,
                'report_id' => $report->id,
            ]);
        }
        $public_project->delete();

        return back()->with('status', 'Projet supprimé.');
    }

    public function image(PublicProject $public_project, PublicProjectImage $image)
    {
        abort_unless($image->public_project_id === $public_project->id, 404);
        abort_unless(Storage::disk('local')->exists($image->path), 404);

        return Storage::disk('local')->response($image->path, headers: [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function report(PublicProject $public_project, PublicProjectReport $report)
    {
        abort_unless($report->public_project_id === $public_project->id, 404);
        abort_unless(Storage::disk('local')->exists($report->path), 404);

        return Storage::disk('local')->download($report->path, $report->original_name, [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function data(Request $r, ?PublicProject $current = null): array
    {
        $r->merge(['slug' => Str::slug($r->input('slug') ?: $r->input('title', ''))]);
        $d = $r->validate([
            'title' => ['required', 'string', 'max:150'], 'slug' => ['required', 'max:150', Rule::unique('public_projects', 'slug')->ignore($current?->id)],
            'pole' => ['required', Rule::in(PublicProject::POLES)], 'status' => ['required', Rule::in(PublicProject::STATUSES)],
            'summary' => ['required', 'string', 'max:400'], 'body' => ['required', 'string', 'max:50000'], 'repo_url' => ['nullable', 'url', 'max:255'],
            'team_members' => ['nullable', 'string', 'max:2000'],
        ]);
        $d['team_members'] = collect(preg_split('/\R/u', $d['team_members'] ?? ''))
            ->map(fn (string $name) => trim($name))
            ->filter()
            ->unique()
            ->take(20)
            ->values()
            ->all();
        $d['is_sample'] = $r->boolean('is_sample');
        $d['published_at'] = $r->boolean('publish') ? ($current?->published_at ?? now()) : null;
        return $d;
    }

    private function itemData(PublicProject $item): array
    {
        return [
            'id' => $item->id,
            'title' => $item->title,
            'slug' => $item->slug,
            'pole' => $item->pole,
            'status' => $item->status,
            'summary' => $item->summary,
            'body' => $item->body,
            'repo_url' => $item->repo_url,
            'team_members' => $item->team_members ?? [],
            'is_sample' => (bool) $item->is_sample,
            'published' => (bool) $item->published_at,
            'images' => $item->images->map(fn (PublicProjectImage $image) => [
                'id' => $image->id,
                'name' => $image->original_name,
                'url' => route('admin.public-projects.images.show', [$item, $image], false),
            ])->values(),
            'report' => $item->report ? [
                'id' => $item->report->id,
                'name' => $item->report->original_name,
                'approved' => (bool) $item->report->approved_at,
                'url' => route('admin.public-projects.reports.show', [$item, $item->report], false),
            ] : null,
        ];
    }
}
