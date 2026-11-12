<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicProjectReport extends Model
{
    protected $fillable = ['path', 'original_name', 'approved_at'];

    protected $casts = ['approved_at' => 'datetime'];

    public function publicProject(): BelongsTo
    {
        return $this->belongsTo(PublicProject::class);
    }
}
