<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PublicProject extends Model
{
    public const POLES = ['Cybersécurité', 'Intelligence Artificielle', 'Logiciel & Open Source', 'Réseaux Cloud & Edge', 'Formation'];
    public const STATUSES = ['Recherche', 'Prototype', 'MVP', 'Terminé', 'Publié'];
    protected $fillable = ['slug', 'title', 'summary', 'body', 'pole', 'status', 'repo_url', 'team_members', 'is_sample', 'published_at'];
    protected $casts = ['is_sample' => 'boolean', 'published_at' => 'datetime', 'team_members' => 'array'];

    public function scopePublished(Builder $q): Builder { return $q->whereNotNull('published_at')->where('published_at', '<=', now()); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function images(): HasMany { return $this->hasMany(PublicProjectImage::class)->orderBy('sort_order'); }
    public function report(): HasOne { return $this->hasOne(PublicProjectReport::class); }
    public function getRouteKeyName(): string { return 'slug'; }
}
