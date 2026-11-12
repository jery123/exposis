<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'organization_name', 'phone', 'role', 'role_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function publicProjects(): HasMany
    {
        return $this->hasMany(PublicProject::class);
    }

    public function accessRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function hasPermissionTo(string $permission): bool
    {
        $role = $this->accessRole;

        if ($role) {
            $permissions = $role->permissions;

            if ($permissions->contains('slug', $permission)) {
                return true;
            }

            return $permission === 'admin.access'
                && $permissions->contains(fn (Permission $assigned) => $assigned->slug !== 'admin.access');
        }

        return $this->role === 'admin';
    }

    public function roleLabel(): string
    {
        return $this->accessRole?->name ?? ($this->role === 'admin' ? 'Administrateur' : 'Membre');
    }
}
