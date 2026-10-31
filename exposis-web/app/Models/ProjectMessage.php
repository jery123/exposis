<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMessage extends Model
{
    protected $fillable = ['project_id', 'user_id', 'body', 'is_internal'];
    protected $casts = ['is_internal' => 'boolean'];
    public function project() { return $this->belongsTo(Project::class); }
    public function user() { return $this->belongsTo(User::class); }
}
