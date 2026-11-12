<?php
namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function before(User $u): ?bool { return $u->hasPermissionTo('projects.manage') ? true : null; }
    public function view(User $u, Project $p): bool { return $p->user_id === $u->id; }
}
