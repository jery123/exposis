<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublicProject;
use App\Models\PublicProjectImage;
use App\Models\PublicProjectReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PublicProjectController extends Controller
{
    public function index() { return view('admin.public-projects.index', ['items' => PublicProject::with(['user', 'report'])->withCount('images')->latest()->paginate(20)]); }
    public function create() { return view('admin.public-projects.form', ['item' => new PublicProject(['status' => 'Prototype'])]); }
    public function edit(PublicProject $public_project) { return view('admin.public-projects.form', ['item' => $public_project->load('images', 'report')]); }

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
}
