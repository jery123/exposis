<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function __invoke(Request $r)
    {
        // Prefix risky leading characters so spreadsheets never run user text as a formula.
        $safe = fn ($v) => is_string($v) && preg_match('/^[=+\-@\t\r]/', $v) ? "'" . $v : $v;
        $query = Project::with('user', 'evaluation')->when($r->status, fn ($q, $s) => $q->where('status', $s))->latest();
        return response()->streamDownload(function () use ($query, $safe) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel
            fputcsv($out, ['Référence', 'Date', 'Organisation', 'E-mail', 'Titre', 'Type', 'Statut', 'NDA', 'Confidentiel', 'Score', 'Recommandation']);
            $query->chunk(200, function ($rows) use ($out, $safe) {
                foreach ($rows as $p) {
                    fputcsv($out, array_map($safe, [
                        $p->reference,
                        $p->created_at->toDateString(),
                        $p->user?->organization_name ?? $p->submitter_organization ?? $p->submitter_name,
                        $p->user?->email ?? $p->submitter_email,
                        $p->title,
                        $p->typeLabel(), $p->statusLabel(), Project::NDA[$p->nda_status], $p->confidential ? 'oui' : 'non',
                        $p->evaluation?->score(), $p->evaluation ? \App\Models\Evaluation::RECOMMENDATIONS[$p->evaluation->recommendation] : '']));
                }
            });
            fclose($out);
        }, 'exposis-projets-' . now()->format('Ymd') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
