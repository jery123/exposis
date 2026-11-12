<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicProjectImage extends Model
{
    protected $fillable = ['path', 'original_name', 'sort_order'];

    public function publicProject(): BelongsTo
    {
        return $this->belongsTo(PublicProject::class);
    }
}
