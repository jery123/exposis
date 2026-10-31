<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Project extends Model
{
    public const TYPES = ['feasibility' => 'Étude de faisabilité technique', 'prototype' => 'Prototype / MVP', 'research' => 'Collaboration de recherche'];
    public const STATUSES = ['submitted' => 'Reçu', 'in_review' => 'En cours d’examen', 'needs_info' => 'Informations demandées', 'evaluated' => 'Évalué', 'accepted' => 'Accepté', 'declined' => 'Non retenu'];
    public const NDA = ['none' => 'Aucun', 'requested' => 'Demandé', 'signed' => 'Signé'];

    protected $fillable = [
        'title', 'request_type', 'problem', 'description', 'timeline', 'budget', 'confidential',
        'submitter_name', 'submitter_email', 'submitter_organization', 'submitter_phone',
    ];
    protected $casts = ['confidential' => 'boolean'];

    protected static function booted(): void
    {
        static::creating(function (Project $p) {
            do { $ref = 'EXP-' . strtoupper(Str::random(6)); } while (static::where('reference', $ref)->exists());
            $p->reference = $ref;
        });
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function evaluation(): HasOne { return $this->hasOne(Evaluation::class); }
    public function messages() { return $this->hasMany(ProjectMessage::class)->latest(); }
    public function files() { return $this->hasMany(ProjectFile::class); }
    public function events() { return $this->hasMany(ProjectEvent::class); }
    public function record(string $type, ?string $from = null, ?string $to = null): void
    {
        $this->events()->create(['user_id' => auth()->id(), 'type' => $type, 'from' => $from, 'to' => $to]);
    }
    public function statusLabel(): string { return self::STATUSES[$this->status] ?? $this->status; }
    public function typeLabel(): string { return self::TYPES[$this->request_type] ?? $this->request_type; }
}
