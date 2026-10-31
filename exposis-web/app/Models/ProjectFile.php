<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectFile extends Model
{
    protected $fillable = ['project_id', 'user_id', 'original_name', 'path', 'size'];
    
    public function project() { return $this->belongsTo(Project::class); }
    public function user() { return $this->belongsTo(User::class); }
}
