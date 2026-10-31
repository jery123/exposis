<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublicProject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PublicProjectController extends Controller
{
    public function index() { return view('admin.public-projects.index', ['items' => PublicProject::with('user')->latest()->paginate(20)]); }
    public function create() { return view('admin.public-projects.form', ['item' => new PublicProject(['status' => 'Prototype'])]); }
    public function edit(PublicProject $public_project) { return view('admin.public-projects.form', ['item' => $public_project]); }

    public function store(Request $r) { PublicProject::create($this->data($r)); return redirect()->route('admin.public-projects.index')->with('status', 'Projet créé.'); }
    public function update(Request $r, PublicProject $public_project) { $public_project->update($this->data($r, $public_project)); return redirect()->route('admin.public-projects.index')->with('status', 'Projet mis à jour.'); }
    public function destroy(PublicProject $public_project) { $public_project->delete(); return back()->with('status', 'Projet supprimé.'); }

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
