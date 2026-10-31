<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectFileController extends Controller
{
    public function store(Request $r, Project $project)
    {
        $this->authorize('view', $project);
        $r->validate(['files' => ['required', 'array', 'max:5'], 'files.*' => ['file', 'max:5120', 'mimes:pdf,png,jpg,jpeg,docx,xlsx,pptx,txt,zip']]);
        foreach ($r->file('files') as $f) {
            $project->files()->create(['user_id' => $r->user()->id, 'original_name' => mb_substr($f->getClientOriginalName(), 0, 200),
                'path' => $f->store("projects/{$project->id}", 'local'), 'size' => $f->getSize()]);
        }
        $project->record('file');
        return back()->with('status', 'Fichier(s) ajouté(s).');
    }

    public function download(ProjectFile $file)
    {
        $this->authorize('view', $file->project);
        abort_unless(Storage::disk('local')->exists($file->path), 404);
        return Storage::disk('local')->download($file->path, $file->original_name);
    }
}
