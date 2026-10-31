<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Model
{
    public const RECOMMENDATIONS = ['go' => 'Faisable', 'conditional' => 'Faisable sous conditions', 'no_go' => 'Non faisable en l’état'];
    protected $fillable = ['technical', 'resources', 'impact', 'risk', 'recommendation', 'summary', 'shared'];
    protected $casts = ['shared' => 'boolean'];

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function evaluator(): BelongsTo { return $this->belongsTo(User::class, 'evaluator_id'); }

    /** Weighted feasibility score, 0-100. Risk is inverted (5 = worst). */
    public function score(): int
    {
        $raw = $this->technical * 0.35 + $this->resources * 0.25 + $this->impact * 0.20 + (6 - $this->risk) * 0.20;
        return (int) round($raw / 5 * 100);
    }
}
