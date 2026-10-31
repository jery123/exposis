<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectEvent extends Model
{
    protected $fillable = ['user_id', 'type', 'from', 'to'];
    
    public function project() { return $this->belongsTo(Project::class); }
    public function user() { return $this->belongsTo(User::class); }
}
