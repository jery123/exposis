<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Project;
use App\Models\PublicProject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View|JsonResponse
    {
        $counts = Project::selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status');
        $evaluations = Evaluation::all();
        $data = [
            'counts' => $counts,
            'total' => $counts->sum(),
            'avgScore' => $evaluations->isEmpty() ? null : (int) round($evaluations->avg(fn ($e) => $e->score())),
            'waiting' => Project::with('user')->whereIn('status', ['submitted', 'needs_info'])->oldest()->limit(8)->get(),
            'pendingPublicProjects' => PublicProject::whereNull('published_at')->count(),
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'counts' => $data['counts'],
                'total' => $data['total'],
                'average_score' => $data['avgScore'],
                'pending_public_projects' => $data['pendingPublicProjects'],
                'waiting' => $data['waiting']->map(fn (Project $project) => [
                    'id' => $project->id,
                    'reference' => $project->reference,
                    'title' => $project->title,
                    'organization' => $project->user?->organization_name ?? $project->submitter_organization ?? $project->submitter_name,
                    'status' => $project->statusLabel(),
                ])->values(),
            ]);
        }

        return view('admin.dashboard', $data);
    }
}
