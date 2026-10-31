<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Project;
use App\Models\PublicProject;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $counts = Project::selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status');
        $evaluations = Evaluation::all();
        return view('admin.dashboard', [
            'counts' => $counts, 'total' => $counts->sum(),
            'avgScore' => $evaluations->isEmpty() ? null : (int) round($evaluations->avg(fn ($e) => $e->score())),
            'waiting' => Project::with('user')->whereIn('status', ['submitted', 'needs_info'])->oldest()->limit(8)->get(),
            'pendingPublicProjects' => PublicProject::whereNull('published_at')->count(),
        ]);
    }
}
